<?php
// This file is part of the tool_certificate plugin for Moodle - http://moodle.org/
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
 * process_export file for tool_customfields_exportimport
 *
 * @package   tool_customfields_exportimport
 * @copyright 2025 Serge Touvoli <serge.touvoli@eticeo.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
global $OUTPUT, $PAGE,$SITE,$CFG;
require_once($CFG->libdir.'/accesslib.php');

require_once($CFG->dirroot . '/admin/tool/customfields_exportimport/classes/export/field_exporter.php');
require_once($CFG->dirroot . '/admin/tool/customfields_exportimport/classes/export/exporter_field_interface.php');
require_once($CFG->dirroot . '/admin/tool/customfields_exportimport/classes/export/profile_field_exporter.php');
require_once($CFG->dirroot . '/admin/tool/customfields_exportimport/classes/export/customfield_exporter.php');

use tool_customfields_exportimport\export\field_exporter;

require_capability('moodle/site:config', context_system::instance());

$type = required_param('type', PARAM_TEXT);
$categoryid = required_param('categoryid', PARAM_INT);
$fieldid = optional_param('fieldid', null, PARAM_INT);

// process export based on the type (profile, course, cohort)
$exporter = field_exporter::make($type);
$export = $exporter->export($categoryid, $fieldid);

$filename = clean_filename("{$export['type']}_category_{$categoryid}_" . date('Ymd_His') . '.json');

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
