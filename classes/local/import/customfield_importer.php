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

namespace tool_customfields_exportimport\local\import;

use coding_exception;
use dml_exception;
use moodle_exception;
use stdClass;

/**
 * customfield_importer class
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customfield_importer implements importer_field_interface {

    /**
     * The component name (e.g., 'core_course', 'core_cohort') for which custom fields are imported.
     *
     * @var string
     */
    private string $component;

    /**
     * The area name (e.g., 'course', 'cohort') for which custom fields are imported.
     *
     * @var string
     */
    private string $area;

    public function __construct(string $component, string $area) {
        $this->component = $component;
        $this->area = $area;
    }

    /**
     * Checks if a custom field with the given shortname exists for the current component and area.
     *
     * @param string $shortname The shortname of the custom field to check.
     * @return bool True if the custom field exists, false otherwise.
     * @throws dml_exception
     */
    private function customfield_shortname_exists(string $shortname): bool {
        global $DB;
        return $DB->record_exists('customfield_field', ['shortname' => $shortname, 'component' => $this->component, 'area' => $this->area]);
    }

    /**
     * Checks if a custom field category with the given name exists for the current component and area.
     *
     * @param string $name The name of the custom field category to check.
     * @return bool True if the category exists, false otherwise.
     * @throws dml_exception
     */
    private function customfield_category_exists(string $name): bool {
        global $DB;
        return $DB->record_exists('customfield_category', ['name' => $name, 'component' => $this->component, 'area' => $this->area]);
    }


    /**
     * Imports custom field categories and fields from an array of data.
     *
     * @param array $data The imported data, decoded from JSON. Must contain 'category' and 'fields'.
     * @return void
     * @throws moodle_exception|coding_exception If the data structure is invalid or a duplicate is found.
     */
    public function import(array $data): void {
        global $DB;

        if (!isset($data['category']) || !isset($data['fields']) || !is_array($data['fields'])) {
            throw new moodle_exception('invalidjsonstructure', 'tool_customfields_exportimport');
        }

        $category = new stdClass();
        $category->name = clean_param($data['category']['name'], PARAM_TEXT);
        $category->sortorder = clean_param($data['category']['sortorder'] ?? 0, PARAM_INT);
        $category->component = $this->component;
        $category->area = $this->area;
        $category->timecreated = time();
        $category->timemodified = time();
        $category->itemid = 0;

        if($this->customfield_category_exists($category->name)) {
            throw new moodle_exception('categoryalreadyexists','tool_customfields_exportimport');
        }

        $categoryid = $DB->insert_record('customfield_category', $category);

        foreach ($data['fields'] as $field) {

            if ($this->customfield_shortname_exists($field['shortname'])) {
                throw new moodle_exception(
                        'shortnamealreadyexists',
                        'tool_customfields_exportimport',
                        '',
                        $field['shortname']
                );
            }

            $fieldobj = new stdClass();
            $fieldobj->categoryid = clean_param($categoryid, PARAM_INT);
            $fieldobj->shortname = clean_param($field['shortname'], PARAM_ALPHANUMEXT);
            $fieldobj->name = clean_param($field['name'], PARAM_TEXT);
            $fieldobj->type = clean_param($field['type'], PARAM_ALPHANUMEXT);
            $fieldobj->description = clean_param($field['description'], PARAM_CLEANHTML);
            $fieldobj->descriptionformat = clean_param($field['descriptionformat'], PARAM_INT);
            $fieldobj->sortorder = clean_param($field['sortorder'] ?? 0, PARAM_INT);
            $fieldobj->timecreated = time();
            $fieldobj->timemodified = time();

            $DB->insert_record('customfield_field', $fieldobj);
        }
    }
}
