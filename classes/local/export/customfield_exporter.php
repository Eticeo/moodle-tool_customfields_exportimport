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

namespace tool_customfields_exportimport\local\export;

use dml_exception;
use stdClass;

/**
 * customfield_exporter class
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customfield_exporter implements exporter_field_interface {


    /**
     * The component name (e\.g\., 'core_course', 'core_cohort') for which custom fields are exported\.
     *
     * @var string
     */
    private string $component;

    /**
     * Constructor.
     *
     * @param string $component The component name (e.g., 'core_course', 'core_cohort').
     */
    public function __construct(string $component) {
        $this->component = $component;
    }

    /**
     * Retrieves a single custom field by its ID and category.
     *
     * @param int $fieldid The ID of the custom field.
     * @param int $categoryid The ID of the category the field belongs to.
     * @return stdClass The custom field record.
     * @throws dml_exception If the field does not exist.
     */
    private function get_customfield_field(int $fieldid, int $categoryid): stdClass {
        global $DB;
        return $DB->get_record('customfield_field', ['id' => $fieldid, 'categoryid' => $categoryid], '*', MUST_EXIST);
    }

    /**
     * Get all custom fields for a given category.
     *
     * @param int $categoryid The ID of the category.
     * @return array List of custom field records.
     * @throws dml_exception
     */
    private function get_customfield_fields_by_category(int $categoryid): array {
        global $DB;
        return $DB->get_records('customfield_field', ['categoryid' => $categoryid], 'sortorder');
    }

    /**
     * Exports custom field data for a category or a specific field.
     *
     * @param int $categoryid The ID of the category.
     * @param int|null $fieldid Optional. The ID of a specific field to export. If null, exports all fields in the category.
     * @return array The exported data.
     * @throws dml_exception If the category or field does not exist.
     */
    public function export(int $categoryid, ?int $fieldid = null): array {
        global $DB;

        $category = $DB->get_record('customfield_category', [
                'id' => $categoryid,
                'component' => $this->component,
        ], '*', MUST_EXIST);

        $fields = $fieldid
                ? [$this->get_customfield_field($fieldid, $categoryid)]
                : $this->get_customfield_fields_by_category($categoryid);

        $export = [
                'type' => str_replace('core_', '', $this->component),
                'category' => [
                        'name' => $category->name,
                        'sortorder' => $category->sortorder,
                ],
                'fields' => [],
        ];

        foreach ($fields as $field) {
            $export['fields'][] = [
                    'shortname' => $field->shortname,
                    'name' => $field->name,
                    'type' => $field->type,
                    'description' => $field->description,
                    'descriptionformat' => $field->descriptionformat,
                    'sortorder' => (int)$field->sortorder,
                    'configdata' => $field->configdata,
            ];
        }

        return $export;
    }
}
