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
 * CLI customfields_exportimport export tool.
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Serge Touvoli <serge.touvoli@eticeo.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once("$CFG->libdir/clilib.php");

list($options, $unrecognized) = cli_get_params(
        [
                'help' => false,
                'type' => null,
                'categoryid' => null,
                'fieldid' => null,
                'destination' => null,
        ],
        [
                'h' => 'help',
                't' => 'type',
                'c' => 'categoryid',
                'f' => 'fieldid',
                'd' => 'destination',
        ]
);

if ($options['help'] || empty($options['type']) || empty($options['categoryid'])) {
    $help = get_string('clihelp', 'tool_customfields_exportimport', (object)[
            'help' => get_string('clihelp_help', 'tool_customfields_exportimport'),
            'type' => get_string('clihelp_type', 'tool_customfields_exportimport'),
            'categoryid' => get_string('clihelp_categoryid', 'tool_customfields_exportimport'),
            'fieldid' => get_string('clihelp_fieldid', 'tool_customfields_exportimport'),
            'destination' => get_string('clihelp_destination', 'tool_customfields_exportimport'),
    ]);
    cli_error($help, 2);
}

$type = $options['type'];
$categoryid = (int) $options['categoryid'];
$fieldid = isset($options['fieldid']) ? (int) $options['fieldid'] : null;
$destination = $options['destination'] ?? '.';

require_once($CFG->dirroot . '/admin/tool/customfields_exportimport/classes/export/field_exporter.php');
require_once($CFG->dirroot . '/admin/tool/customfields_exportimport/classes/export/customfield_exporter.php');

use tool_customfields_exportimport\export\field_exporter;


$exporter = field_exporter::make($type);
$data = $exporter->export($categoryid, $fieldid);

$filename = "export_{$type}_category{$categoryid}" . ($fieldid ? "_field{$fieldid}" : "") . ".json";
$filepath = rtrim($destination, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if (file_put_contents($filepath, $json) === false) {
    cli_error(get_string('cli_export_failed', 'tool_customfields_exportimport', $filepath));
}

cli_writeln(get_string('cli_export_success', 'tool_customfields_exportimport', $filepath));
