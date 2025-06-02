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
 * import file for customfields_exportimport
 *
 * @package   tool_customfields_exportimport
 * @copyright 2025 Serge Touvoli <serge.touvoli@eticeo.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

global $OUTPUT, $PAGE,$SITE,$CFG, $USER;

require_once($CFG->libdir.'/accesslib.php');
require_once(__DIR__ . '/import_form.php');

require_once($CFG->dirroot . '/admin/tool/customfields_exportimport/classes/import/field_importer.php');
require_once($CFG->dirroot . '/admin/tool/customfields_exportimport/classes/import/importer_field_interface.php');
require_once($CFG->dirroot . '/admin/tool/customfields_exportimport/classes/import/customfield_importer.php');

use tool_customfields_exportimport\import\field_importer;

global $DB;

require_capability('moodle/site:config', context_system::instance());

$url = new moodle_url('/admin/tool/customfields_exportimport/index.php');

$heading = get_string('importpage', 'tool_customfields_exportimport');

$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_title($heading);
$PAGE->set_heading($heading);

$mform = new customfields_import_form();


// if the form is submitted and valid, process the import
if ($mform->get_data()) {

    $draftitemid = file_get_submitted_draft_itemid('import_file');
    $usercontext = context_user::instance($USER->id);
    $fs = get_file_storage();

    $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'itemid', false);

    $file = reset($files);

    // Verif if file exists and has a size
    if (!$file || !$file->get_filesize()) {
        throw new moodle_exception('nofile', 'error');
    }

    // verif json file
    if ($file->get_mimetype() !== 'application/json') {
        throw new moodle_exception('invalidfiletype', 'tool_customfields_exportimport');
    }

    $importdata = json_decode($file->get_content(), true);

    if (!$importdata || !isset($importdata['type'])) {
        throw new moodle_exception('invalidjson', 'tool_customfields_exportimport');
    }

    // process the import based on the type (cohort, course, profile)
    $importer = field_importer::make($importdata['type']);

    $importer->import($importdata);

    redirect(
            new moodle_url('/admin/tool/customfields_exportimport/import.php'),
            get_string('importsuccess', 'tool_customfields_exportimport'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
    );

} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('import', 'tool_profiling'));
    $mform->display();
    echo $OUTPUT->footer();
    die;
}
