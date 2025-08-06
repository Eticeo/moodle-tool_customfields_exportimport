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
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
global $OUTPUT, $PAGE, $SITE, $CFG;
require_once($CFG->libdir.'/accesslib.php');

require_admin();

$url = new moodle_url('/admin/tool/customfields_exportimport/index.php');

$heading = get_string('exportpage', 'tool_customfields_exportimport');

$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_title($heading);
$PAGE->set_heading($heading);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('export_title', 'tool_customfields_exportimport'));

$validstabs = ['profile', 'course', 'cohort'];

$tabs = [];

// Define the tabs for different custom field types.
foreach ($validstabs as $tab) {
    $string = get_string("{$tab}fields", 'tool_customfields_exportimport');
    $tabs[] = new tabobject($tab, tool_customfields_exportimport_make_tab_url($tab), $string);
}

$selectedtab = optional_param('tab', 'profile', PARAM_ALPHA);

echo $OUTPUT->tabtree($tabs, $selectedtab);

if (!in_array($selectedtab, $validstabs)) {
    echo $OUTPUT->notification(get_string('invalidtab', 'tool_customfields_exportimport'), 'error');
    echo $OUTPUT->footer();
    exit;
}

// Display the good fields based on the selected tab.
tool_customfields_exportimport_display_fields_list($selectedtab);

echo $OUTPUT->footer();


/**
 * Displays the list of custom field categories and their fields for a given area.
 *
 * @param string $area The area type (profile, course, cohort).
 * @return void
 */
function tool_customfields_exportimport_display_fields_list(string $area) {

    $maptype = [
            'profile' => 'core_user',
            'course' => 'core_course',
            'cohort' => 'core_cohort',
    ];

    $component = $maptype[$area];

    if ($component === "core_user") {
        $categories = tool_customfields_exportimport_get_user_info_categories();
    } else {
        $categories = tool_customfields_exportimport_get_customfields_categories($component);
    }

    foreach ($categories as $category) {
        $categoryname = format_string($category->name);
        $paramscategoryurl = ['categoryid' => $category->id, 'type' => $area];
        $exportcategoryurl = new moodle_url('/admin/tool/customfields_exportimport/process_export.php', $paramscategoryurl);

        echo html_writer::start_div('d-flex justify-content-between align-items-center my-3');
        echo html_writer::tag('h4', $categoryname, ['class' => 'mb-0']);
        echo html_writer::link($exportcategoryurl, get_string('exportcategory', 'tool_customfields_exportimport'), [
                'class' => 'btn btn-sm btn-success',
        ]);
        echo html_writer::end_div();

        $table = new html_table();
        $table->head = [
                get_string('fieldname', 'tool_customfields_exportimport'),
                get_string('typefortable', 'tool_customfields_exportimport'),
                get_string('description', 'tool_customfields_exportimport'),
                get_string('actions', 'tool_customfields_exportimport'),
        ];
        $table->data = [];

        foreach ($category->fields as $field) {

            $actionbtn = tool_customfields_exportimport_print_export_link($category->id, $field->id, $area);

            $table->data[] = [
                    format_string($field->name),
                    $field->datatype ?? $field->type,
                    format_text($field->description, FORMAT_HTML),
                    $actionbtn,
            ];
        }

        echo html_writer::table($table);
    }
}


/**
 * Generates the URL for a specific tab in the export/import tool.
 *
 * @param string $tab The tab identifier.
 * @return moodle_url The URL for the tab.
 */
function tool_customfields_exportimport_make_tab_url(string $tab): moodle_url {
    return new moodle_url('/admin/tool/customfields_exportimport/index.php', ['tab' => $tab]);
}

/**
 * Generates an export link for a specific custom field.
 *
 * @param int $categoryid The ID of the custom field category.
 * @param int $fieldid The ID of the custom field.
 * @param string $type The type of custom field (profile, course, cohort).
 * @return string The HTML link for exporting the field.
 */
function tool_customfields_exportimport_print_export_link(int $categoryid, int $fieldid, string $type): string {
    $params = [
        'fieldid' => $fieldid,
        'categoryid' => $categoryid,
        'type' => $type,
    ];
    $exporturl = new moodle_url('/admin/tool/customfields_exportimport/process_export.php', $params);
    return html_writer::link($exporturl, get_string('export', 'tool_customfields_exportimport'), [
           'class' => 'btn btn-sm btn-primary',
    ]);
}


/**
 * Retrieves custom field categories for a given component, including their fields.
 *
 * @param string $component The component name (e.g., core_course, core_cohort).
 * @return array List of custom field categories, each with its fields.
 */
function tool_customfields_exportimport_get_customfields_categories(string $component): array {
    global $DB;

    $categories = $DB->get_records('customfield_category', ['component' => $component], 'sortorder');

    foreach ($categories as $category) {
        $category->fields = $DB->get_records('customfield_field', ['categoryid' => $category->id], 'sortorder');
    }

    return $categories;
}

/**
 * Retrieves user profile field categories with their fields.
 *
 * @return array List of user info categories, each with its fields.
 */
function tool_customfields_exportimport_get_user_info_categories(): array {
    global $DB;
    $categories = $DB->get_records('user_info_category', null, 'sortorder');
    foreach ($categories as $category) {
        $category->fields = $DB->get_records('user_info_field', ['categoryid' => $category->id]);
    }

    return $categories;
}




