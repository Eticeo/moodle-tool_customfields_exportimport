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
 * Upgrade script for tool_customfields_exportimport
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

/**
 * Upgrade function for tool_customfields_exportimport plugin.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_tool_customfields_exportimport_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025102800) {
        // Define table tool_customfields_migrations to be created.
        $table = new xmldb_table('tool_customfields_migrations');

        // Adding fields to table tool_customfields_migrations.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('migration_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('migration_hash', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('migration_type', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'applied');
        $table->add_field('applied_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('error_message', XMLDB_TYPE_TEXT, null, null, false, null, null);

        // Adding keys to table tool_customfields_migrations.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('migration_name', XMLDB_KEY_UNIQUE, array('migration_name'));

        // Adding indexes to table tool_customfields_migrations.
        $table->add_index('status_idx', XMLDB_INDEX_NOTUNIQUE, array('status'));
        $table->add_index('applied_at_idx', XMLDB_INDEX_NOTUNIQUE, array('applied_at'));

        // Conditionally launch create table for tool_customfields_migrations.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Customfields_exportimport savepoint reached.
        upgrade_plugin_savepoint(true, 2025102800, 'tool', 'customfields_exportimport');
    }

    return true;
}
