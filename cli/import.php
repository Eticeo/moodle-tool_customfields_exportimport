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

/**
 * CLI customfields_exportimport import tool with migration tracking.
 *
 * Supports both manual file imports and automatic migration discovery.
 * Uses Laravel-style migration tracking for safe, idempotent deployments.
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once("$CFG->libdir/clilib.php");

use tool_customfields_exportimport\local\import\field_importer;
use tool_customfields_exportimport\local\migration\migration_manager;

list($options, $unrecognized) = cli_get_params(
        [
                'help' => false,
                'file' => null,
                'auto' => false,
                'dry-run' => false,
        ],
        [
                'h' => 'help',
                'f' => 'file',
                'a' => 'auto',
        ]
);

if ($options['help']) {
    $help = get_string('clihelp_import', 'tool_customfields_exportimport', (object)[
            'help' => get_string('clihelp_import_help', 'tool_customfields_exportimport'),
            'file' => get_string('clihelp_import_file', 'tool_customfields_exportimport'),
    ]);
    cli_writeln($help);
    cli_writeln("
Additional options for auto-discovery:
  --auto, -a          Auto-discover and apply all pending migrations
  --dry-run           Preview what will be applied without making changes
");
    exit(0);
}

$is_dryrun = $options['dry-run'];
$is_auto = $options['auto'];
$filepath = $options['file'];

// Determine mode: manual or auto
if ($is_auto) {
    // Auto-discovery mode
    cli_writeln("Migration auto-discovery mode");
    cli_writeln("================================\n");

    $migration_manager = new migration_manager();

    // Get pending migrations
    $pending = $migration_manager->get_pending_migrations();

    if (empty($pending)) {
        $stats = $migration_manager->get_stats();
        cli_writeln("No pending migrations.");
        cli_writeln("Total migrations: {$stats['total_files']}, Applied: {$stats['applied']}, Failed: {$stats['failed']}\n");
        exit(0);
    }

    cli_writeln("Found " . count($pending) . " pending migration(s):\n");

    $applied_count = 0;
    $failed_count = 0;

    foreach ($pending as $migration) {
        $filename = $migration['filename'];
        $status_label = $migration['status'] === 'new' ? '[NEW]' : '[MODIFIED]';

        if ($is_dryrun) {
            cli_writeln("  $status_label $filename");
            continue;
        }

        cli_write("  $status_label $filename ... ");

        try {
            // Read and parse JSON
            $json = file_get_contents($migration['filepath']);
            $data = json_decode($json, true);

            if (!$data || !isset($data['type'])) {
                throw new Exception('Invalid JSON structure');
            }

            // Get migration type
            $migration_type = $migration_manager->get_migration_type($migration['filepath']);

            // Apply migration
            $importer = field_importer::make($data['type']);
            $importer->import($data);

            // Record successful migration
            $migration_manager->mark_migration_applied(
                $filename,
                $migration['hash'],
                $migration_type,
                'applied'
            );

            cli_writeln("OK");
            $applied_count++;

        } catch (Throwable $e) {
            cli_writeln("FAILED");
            cli_writeln("    Error: " . $e->getMessage());

            // Record failed migration
            try {
                $migration_type = $migration_manager->get_migration_type($migration['filepath']);
                $migration_manager->mark_migration_applied(
                    $filename,
                    $migration['hash'],
                    $migration_type,
                    'failed',
                    $e->getMessage()
                );
            } catch (Exception $tracking_error) {
                cli_writeln("    Failed to record migration status: " . $tracking_error->getMessage());
            }

            $failed_count++;
        }
    }

    cli_writeln("");

    if ($is_dryrun) {
        cli_writeln("DRY-RUN: No changes were made.");
    } else {
        $stats = $migration_manager->get_stats();
        cli_writeln("Migration Summary:");
        cli_writeln("  Applied: $applied_count");
        cli_writeln("  Failed: $failed_count");
        cli_writeln("  Total migrations applied: {$stats['applied']}");
        cli_writeln("  Total failed: {$stats['failed']}\n");
    }

    exit($failed_count > 0 ? 1 : 0);

} else {
    // Manual file import mode (legacy behavior)
    if (empty($filepath)) {
        $help = get_string('clihelp_import', 'tool_customfields_exportimport', (object)[
                'help' => get_string('clihelp_import_help', 'tool_customfields_exportimport'),
                'file' => get_string('clihelp_import_file', 'tool_customfields_exportimport'),
        ]);
        cli_error($help, 2);
    }

    if (!file_exists($filepath) || !is_readable($filepath)) {
        cli_error(get_string('cli_import_invalidfile', 'tool_customfields_exportimport', $filepath));
    }

    $json = file_get_contents($filepath);
    $data = json_decode($json, true);

    if (!$data || !isset($data['type'])) {
        cli_error(get_string('cli_import_invalidjson', 'tool_customfields_exportimport'));
    }

    try {
        $importer = field_importer::make($data['type']);
        $importer->import($data);

        // Record in migration tracking if filename matches pattern
        $filename = basename($filepath);
        if (preg_match('/^\d{8}_/', $filename)) {
            $migration_manager = new migration_manager();
            $migration_type = $migration_manager->get_migration_type($filepath);
            $migration_manager->mark_migration_applied(
                $filename,
                md5_file($filepath),
                $migration_type,
                'applied'
            );
        }

        cli_writeln(get_string('cli_import_success', 'tool_customfields_exportimport', $data['type']));
        exit(0);
    } catch (Throwable $e) {
        cli_error(get_string('cli_import_failed', 'tool_customfields_exportimport', $e->getMessage()), 1);
    }
}
