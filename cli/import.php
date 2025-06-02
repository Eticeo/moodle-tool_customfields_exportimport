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
 * CLI customfields_exportimport import tool.
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Serge Touvoli <serge.touvoli@eticeo.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);


require(__DIR__ . '/../../../../config.php');
require_once("$CFG->libdir/clilib.php");

list($options, $unrecognized) = cli_get_params(
        [
                'help' => false,
                'file' => null,
        ],
        [
                'h' => 'help',
                'f' => 'file',
        ]
);

// Affiche l'aide si nécessaire.
if ($options['help'] || empty($options['file'])) {
    $help = get_string('clihelp_import', 'tool_customfields_exportimport', (object)[
            'help' => get_string('clihelp_import_help', 'tool_customfields_exportimport'),
            'file' => get_string('clihelp_import_file', 'tool_customfields_exportimport'),
    ]);
    cli_error($help, 2);
}

$filepath = $options['file'];

if (!file_exists($filepath) || !is_readable($filepath)) {
    cli_error(get_string('cli_import_invalidfile', 'tool_customfields_exportimport', $filepath));
}

$json = file_get_contents($filepath);
$data = json_decode($json, true);

if (!$data || !isset($data['type'])) {
    cli_error(get_string('cli_import_invalidjson', 'tool_customfields_exportimport'));
}

require_once($CFG->dirroot . '/admin/tool/customfields_exportimport/classes/import/field_importer.php');

use tool_customfields_exportimport\import\field_importer;

try {
    $importer = field_importer::make($data['type']);
    $importer->import($data);
    cli_writeln(get_string('cli_import_success', 'tool_customfields_exportimport', $data['type']));
    exit(0);
} catch (Throwable $e) {
    cli_error(get_string('cli_import_failed', 'tool_customfields_exportimport', $e->getMessage()), 1);
}
