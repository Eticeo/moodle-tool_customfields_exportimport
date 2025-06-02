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
 * index file for customfields_exportimport
 *
 * @package   tool_customfields_exportimport
 * @copyright 2025 Serge Touvoli <serge.touvoli@eticeo.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
global $OUTPUT, $PAGE,$SITE,$CFG;
require_once($CFG->libdir.'/accesslib.php');

require_capability('moodle/site:config', context_system::instance());

$url = new moodle_url('/admin/tool/customfields_exportimport/index.php');

$heading = get_string('exportpage', 'tool_customfields_exportimport');

$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_title($heading);
$PAGE->set_heading($heading);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('export_title', 'tool_customfields_exportimport'));

$tabs = [];

// Define the tabs for different custom field types.
$tabs[] = new tabobject('profile', new moodle_url('/admin/tool/customfields_exportimport/index.php', ['tab' => 'profile']), get_string('profilefields', 'tool_customfields_exportimport'));
$tabs[] = new tabobject('course', new moodle_url('/admin/tool/customfields_exportimport/index.php', ['tab' => 'course']), get_string('coursefields', 'tool_customfields_exportimport'));
$tabs[] = new tabobject('cohort', new moodle_url('/admin/tool/customfields_exportimport/index.php', ['tab' => 'cohort']), get_string('cohortfields', 'tool_customfields_exportimport'));

$selectedtab = optional_param('tab', 'profile', PARAM_ALPHA);

echo $OUTPUT->tabtree($tabs, $selectedtab);

// Display the good fields based on the selected tab.
if ($selectedtab === 'profile') {
    display_profile_fields();
} else if ($selectedtab === 'course') {
    display_course_fields();
} else if ($selectedtab === 'cohort') {
    display_cohort_fields();
} else {
    echo $OUTPUT->notification(get_string('invalidtab', 'tool_customfields_exportimport'), 'error');
}

echo $OUTPUT->footer();


/**
 * Generates an export link for a specific custom field.
 *
 * @param int $categoryid The ID of the custom field category.
 * @param int $fieldid The ID of the custom field.
 * @param string $type The type of custom field (profile, course, cohort).
 * @return string The HTML link for exporting the field.
 */
function print_export_link(int $categoryid, int $fieldid, string $type): string {
    $exporturl = new moodle_url('/admin/tool/customfields_exportimport/process_export.php', ['fieldid' => $fieldid,'categoryid' => $categoryid,'type' => $type]);
    return html_writer::link($exporturl, get_string('export', 'tool_customfields_exportimport'), [
           'class' => 'btn btn-sm btn-primary'
   ]);
}

/**
 * Displays the cohort custom field categories and their fields in a table,
 * with export options for each category and field.
 *
 * @return void
 */
function display_cohort_fields(): void {

    $categories = get_cohort_customfields_categories();
    foreach ($categories as $category) {
        $categoryname = format_string($category->name);
        $exportcategoryurl = new moodle_url('/admin/tool/customfields_exportimport/process_export.php', ['categoryid' => $category->id,'type' => 'cohort']);

        echo html_writer::start_div('d-flex justify-content-between align-items-center my-3');
        echo html_writer::tag('h4', $categoryname, ['class' => 'mb-0']);
        echo html_writer::link($exportcategoryurl, get_string('exportcategory', 'tool_customfields_exportimport'), [
                'class' => 'btn btn-sm btn-success'
        ]);
        echo html_writer::end_div();

        $table = new html_table();
        $table->head = ['Nom', 'Type', 'Description', 'Actions'];
        $table->data = [];

        foreach ($category->fields as $field) {
            $actionbtn = print_export_link($category->id, $field->id, 'cohort');

            $table->data[] = [
                    format_string($field->name),
                    $field->type,
                    format_text($field->description, FORMAT_HTML),
                    $actionbtn
            ];
        }

        echo html_writer::table($table);
    }
}


/**
 * Retrieves cohort custom field categories with their fields.
 *
 * @return array List of cohort custom field categories, each with its fields.
 */
function get_cohort_customfields_categories(): array {
    global $DB;

    $categories = $DB->get_records('customfield_category', ['component' => 'core_cohort'], 'sortorder');

    foreach($categories as $category) {
        $category->fields = $DB->get_records('customfield_field', ['categoryid' => $category->id], 'sortorder');
    }

    return $categories;
}


/**
 * Displays the course custom field categories and their fields in a table,
 * with export options for each category and field.
 *
 * @return void
 */
function display_course_fields(): void {

    $categories = get_course_customfields_categories();
    foreach ($categories as $category) {
        $categoryname = format_string($category->name);
        $exportcategoryurl = new moodle_url('/admin/tool/customfields_exportimport/process_export.php', ['categoryid' => $category->id,'type' => 'course']);


        echo html_writer::start_div('d-flex justify-content-between align-items-center my-3');
        echo html_writer::tag('h4', $categoryname, ['class' => 'mb-0']);
        echo html_writer::link($exportcategoryurl, get_string('exportcategory', 'tool_customfields_exportimport'), [
                'class' => 'btn btn-sm btn-success'
        ]);
        echo html_writer::end_div();

        $table = new html_table();
        $table->head = ['Nom', 'Type', 'Description', 'Actions'];
        $table->data = [];

        foreach ($category->fields as $field) {
            $actionbtn = print_export_link($category->id, $field->id, 'course');

            $table->data[] = [
                    format_string($field->name),
                    $field->type,
                    format_text($field->description, FORMAT_HTML),
                    $actionbtn
            ];
        }

        echo html_writer::table($table);
    }
}

/**
 * Displays the user profile field categories and their fields in a table,
 * with export options for each category and field.
 *
 * @return void
 */
function display_profile_fields(): void {

    $categories = get_user_info_categories();

    foreach ($categories as $category) {
        $categoryname = format_string($category->name);
        $exportcategoryurl = new moodle_url('/admin/tool/customfields_exportimport/process_export.php', ['categoryid' => $category->id,'type' => 'profile']);

        echo html_writer::start_div('d-flex justify-content-between align-items-center my-3');
        echo html_writer::tag('h4', $categoryname, ['class' => 'mb-0']);
        echo html_writer::link($exportcategoryurl, get_string('exportcategory', 'tool_customfields_exportimport'), [
                'class' => 'btn btn-sm btn-success'
        ]);
        echo html_writer::end_div();

        $table = new html_table();
        $table->head = [
                get_string('field', 'tool_customfields_exportimport'),
                get_string('description', 'tool_customfields_exportimport'),
                get_string('type', 'tool_customfields_exportimport'),
                get_string('required', 'tool_customfields_exportimport'),
                get_string('actions', 'tool_customfields_exportimport'),
        ];

        $table->data = [];

        foreach ($category->fields as $field) {
            $type = $field->datatype;
            $required = $field->required ? get_string('yes') : get_string('no');
            $description = format_text($field->description ?? '', $field->descriptionformat ?? FORMAT_HTML);

            $actionbtn = print_export_link($category->id, $field->id, 'profile');

            $table->data[] = [
                    format_string($field->name),
                    $description,
                    $type,
                    $required,
                    $actionbtn
            ];
        }

        echo html_writer::table($table);
    }
}

/**
 * Retrieves course custom field categories with their fields.
 *
 * @return array List of course custom field categories, each with its fields.
 */
function get_course_customfields_categories(): array {
    global $DB;

    $categories = $DB->get_records('customfield_category', ['component' => 'core_course'], 'sortorder');

    foreach($categories as $category) {
        $category->fields = $DB->get_records('customfield_field', ['categoryid' => $category->id], 'sortorder');
    }

    return $categories;
}

/**
 * Retrieves user profile field categories with their fields.
 *
 * @return array List of user info categories, each with its fields.
 */
function get_user_info_categories(): array {
    global $DB;
    $categories = $DB->get_records('user_info_category',null,'sortorder');
    foreach ($categories as $category) {
        $category->fields = $DB->get_records('user_info_field', ['categoryid' => $category->id]);
    }

    return $categories;
}


echo $OUTPUT->footer();



