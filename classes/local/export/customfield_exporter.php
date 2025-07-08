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

/**
 * customfield_exporter class
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customfield_exporter implements exporter_field_interface {

    private string $component;

    public function __construct(string $component) {
        $this->component = $component;
    }

    private function get_customfield_field(int $fieldid, int $categoryid) {
        global $DB;
        return $DB->get_record('customfield_field', ['id' => $fieldid, 'categoryid' => $categoryid], '*', MUST_EXIST);
    }

    private function get_customfield_fields_by_category(int $categoryid): array {
        global $DB;
        return $DB->get_records('customfield_field', ['categoryid' => $categoryid], 'sortorder');
    }

    public function export(int $categoryid, ?int $fieldid = null): array {
        global $DB;

        $category = $DB->get_record('customfield_category', [
                'id' => $categoryid,
                'component' => $this->component
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
                    'configdata' => $field->param1,
            ];
        }

        return $export;
    }


}
