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
 * profile_field_exporter class
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_exporter implements exporter_field_interface {


    /**
     * get a user_info_field record by id.
     *
     * @param int $fieldid The id of the user_info_field.
     * @return stdClass The user_info_field record.
     * @throws dml_exception
     */
    private function get_user_info_field_id(int $fieldid){
        global $DB;
        return $DB->get_record('user_info_field', ['id' => $fieldid], '*', MUST_EXIST);
    }

    /**
     * Get all user_info_field records for a given category, ordered by sortorder.
     *
     * @param int $categoryid The id of the user_info_category.
     * @return array The list of user_info_field records.
     * @throws dml_exception
     */
    private function get_user_info_field_by_category(int $categoryid): array {
        global $DB;
        return $DB->get_records('user_info_field', ['categoryid' => $categoryid], 'sortorder');
    }


    /**
     * Exports user profile fields for a given category or a specific field.
     *
     * @param int $categoryid The ID of the user_info_category.
     * @param int|null $fieldid Optional. The ID of a specific user_info_field to export. If null, exports all fields in the category.
     * @return array The exported profile fields data.
     * @throws dml_exception
     */
    public function export(int $categoryid, ?int $fieldid = null): array {
        global $DB;

        $category = $DB->get_record('user_info_category', ['id' => $categoryid], '*', MUST_EXIST);

        $fields = $fieldid ? [$this->get_user_info_field_id($fieldid)] : $this->get_user_info_field_by_category($categoryid);

        $export = [
                'type' => 'profile',
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
                    'datatype' => $field->datatype,
                    'description' => $field->description,
                    'descriptionformat' => $field->descriptionformat,
                    'required' => (bool) $field->required,
                    'locked' => (bool) $field->locked,
                    'visible' => (int) $field->visible,
                    'forceunique' => (bool) $field->forceunique,
                    'signup' => (bool) $field->signup,
                    'defaultdata' => $field->defaultdata,
                    'defaultdataformat' => $field->defaultdataformat,
                    'param1' => $field->param1,
                    'param2' => $field->param2,
                    'param3' => $field->param3,
                    'param4' => $field->param4,
                    'param5' => $field->param5,
            ];
        }

        return $export;
    }
}
