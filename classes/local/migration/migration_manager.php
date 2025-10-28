<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace tool_customfields_exportimport\local\migration;

use moodle_exception;
use stdClass;

/**
 * Migration manager for custom field imports.
 *
 * Handles discovery, tracking, and execution of custom field migrations
 * inspired by Laravel's migration pattern.
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migration_manager {

    /**
     * Directory containing migration files.
     *
     * @var string
     */
    private string $migrations_dir;

    /**
     * Constructor for migration_manager.
     *
     * @param string|null $migrations_dir The directory containing migration files.
     *                                     If null, defaults to admin/tool/customfields_exportimport/import_files/
     */
    public function __construct(?string $migrations_dir = null) {
        global $CFG;

        if ($migrations_dir === null) {
            $migrations_dir = $CFG->dirroot . '/admin/tool/customfields_exportimport/import_files';
        }

        $this->migrations_dir = $migrations_dir;
    }

    /**
     * Get all migration files in the migrations directory, sorted chronologically.
     *
     * Files must be named with YYYYMMDD prefix followed by underscore (e.g., 20251027_export_profile.json)
     *
     * @return array Array of absolute file paths, sorted by filename
     */
    public function get_all_migrations(): array {
        if (!is_dir($this->migrations_dir)) {
            return [];
        }

        $files = scandir($this->migrations_dir);
        $migrations = [];

        foreach ($files as $file) {
            // Skip directories and non-JSON files
            if (is_dir($file) || substr($file, -5) !== '.json') {
                continue;
            }

            // Skip files that don't match the YYYYMMDD_* pattern
            if (!preg_match('/^\d{8}_/', $file)) {
                continue;
            }

            $migrations[] = $this->migrations_dir . '/' . $file;
        }

        // Sort chronologically by filename
        sort($migrations);

        return $migrations;
    }

    /**
     * Get pending migrations (not yet applied or content changed).
     *
     * @return array Array of migration file paths with their metadata
     */
    public function get_pending_migrations(): array {
        global $DB;

        $pending = [];
        $all_migrations = $this->get_all_migrations();

        foreach ($all_migrations as $filepath) {
            $filename = basename($filepath);
            $file_hash = md5_file($filepath);

            // Check if migration is already recorded in database
            $record = $DB->get_record('tool_customfields_migrations', ['migration_name' => $filename]);

            if ($record === false) {
                // Migration not yet applied
                $pending[] = [
                    'filepath' => $filepath,
                    'filename' => $filename,
                    'hash' => $file_hash,
                    'status' => 'new',
                ];
            } else if ($record->migration_hash !== $file_hash) {
                // Migration file has changed since last application
                $pending[] = [
                    'filepath' => $filepath,
                    'filename' => $filename,
                    'hash' => $file_hash,
                    'status' => 'modified',
                ];
            }
        }

        return $pending;
    }

    /**
     * Check if a migration has already been applied.
     *
     * @param string $migration_name The migration filename
     * @return bool True if the migration has been applied, false otherwise
     */
    public function is_migration_applied(string $migration_name): bool {
        global $DB;
        return $DB->record_exists('tool_customfields_migrations', ['migration_name' => $migration_name]);
    }

    /**
     * Mark a migration as applied in the database.
     *
     * @param string $migration_name The migration filename
     * @param string $migration_hash MD5 hash of the migration file
     * @param string $migration_type Type of fields (profile, course, cohort)
     * @param string $status Status to set (applied, failed)
     * @param string|null $error_message Error message if status is failed
     * @return int The ID of the inserted/updated record
     */
    public function mark_migration_applied(
        string $migration_name,
        string $migration_hash,
        string $migration_type,
        string $status = 'applied',
        ?string $error_message = null
    ): int {
        global $DB;

        $record = $DB->get_record('tool_customfields_migrations', ['migration_name' => $migration_name]);

        $migration_record = new stdClass();
        $migration_record->migration_name = $migration_name;
        $migration_record->migration_hash = $migration_hash;
        $migration_record->migration_type = $migration_type;
        $migration_record->status = $status;
        $migration_record->applied_at = time();
        $migration_record->error_message = $error_message;

        if ($record === false) {
            // Insert new migration record
            return $DB->insert_record('tool_customfields_migrations', $migration_record);
        } else {
            // Update existing migration record
            $migration_record->id = $record->id;
            $DB->update_record('tool_customfields_migrations', $migration_record);
            return $record->id;
        }
    }

    /**
     * Get the type of custom fields from a migration file.
     *
     * @param string $filepath Path to the migration JSON file
     * @return string The type (profile, course, cohort)
     * @throws moodle_exception If the file is invalid or doesn't contain type information
     */
    public function get_migration_type(string $filepath): string {
        if (!file_exists($filepath)) {
            throw new moodle_exception('filenotfound', 'tool_customfields_exportimport', '', $filepath);
        }

        $content = file_get_contents($filepath);
        $data = json_decode($content, true);

        if ($data === null) {
            throw new moodle_exception('invalidjson', 'tool_customfields_exportimport', '', $filepath);
        }

        if (!isset($data['type'])) {
            throw new moodle_exception('missingtype', 'tool_customfields_exportimport', '', $filepath);
        }

        return $data['type'];
    }

    /**
     * Get migration statistics for display.
     *
     * @return array Statistics including total, applied, pending counts
     */
    public function get_stats(): array {
        global $DB;

        $all_migrations = count($this->get_all_migrations());
        $pending_migrations = count($this->get_pending_migrations());
        $applied = $DB->count_records('tool_customfields_migrations');
        $failed = $DB->count_records('tool_customfields_migrations', ['status' => 'failed']);

        return [
            'total_files' => $all_migrations,
            'applied' => $applied,
            'pending' => $pending_migrations,
            'failed' => $failed,
        ];
    }

    /**
     * Get detailed migration history from the database.
     *
     * @param int $limit Maximum number of records to return
     * @param int $offset Starting offset
     * @return array Array of migration records
     */
    public function get_migration_history(int $limit = 50, int $offset = 0): array {
        global $DB;

        return $DB->get_records(
            'tool_customfields_migrations',
            [],
            'applied_at DESC',
            '*',
            $offset,
            $limit
        );
    }
}
