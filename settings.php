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
 *  tool_customfield_exportimport settings
 *
 * @package    tool_customfields_exportimport
 * @copyright 2024 Eticeo https://eticeo.com
 * @author    2024 mars Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $ADMIN, $DB, $USER,$CFG;


$ADMIN->add('tools', new admin_category('tool_customfields_exportimport',
        get_string('pluginname', 'tool_customfields_exportimport')));

$ADMIN->add('tool_customfields_exportimport',
        new admin_externalpage(
                'tool_customfields_exportimport_home_page',
                get_string('exportpage', 'tool_customfields_exportimport'),
                "$CFG->wwwroot/admin/tool/customfields_exportimport/index.php"
        )
);

$ADMIN->add('tool_customfields_exportimport',
        new admin_externalpage(
                'tool_customfields_exportimport_import_page',
                get_string('importpage', 'tool_customfields_exportimport'),
                "$CFG->wwwroot/admin/tool/customfields_exportimport/import.php"
        )
);
