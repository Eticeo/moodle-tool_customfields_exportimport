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

use moodle_exception;
use stdClass;

/**
 * Importer for user profile fields.
 *
 * Implements the import of custom user profile fields (user_info_category and user_info_field)
 * from a decoded JSON array structure.
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_importer implements importer_field_interface {

    /**
     * Checks if a user_info_category with the given name already exists.
     *
     * @param string $name The category name to check.
     * @return int|false The category ID if it exists, false otherwise.
     */
    private function get_category_by_name(string $name) {
        global $DB;
        $record = $DB->get_record('user_info_category', ['name' => $name]);
        return $record ? $record->id : false;
    }

    /**
     * Checks if a user_info_field with the given shortname exists in the specified category.
     *
     * @param string $shortname The field shortname to check.
     * @param int $categoryid The category ID to check within.
     * @return bool True if the field exists, false otherwise.
     */
    private function field_shortname_exist(string $shortname, int $categoryid): bool {
        global $DB;
        return $DB->record_exists('user_info_field', ['shortname' => $shortname, 'categoryid' => $categoryid]);
    }


    /**
     * Imports profile fields from an array of data.
     *
     * This import is idempotent: running it multiple times is safe.
     * - If a category already exists, it will be reused (not an error)
     * - If a field already exists, it will be skipped (not an error)
     *
     * @param array $data The imported data, decoded from JSON. Must contain 'category' and 'fields'.
     * @return void
     */
    public function import(array $data): void {

        global $CFG;

        if (!isset($data['category']) || !isset($data['fields']) || !is_array($data['fields'])) {
            throw new moodle_exception('invalidjsonstructure', 'tool_customfields_exportimport');
        }

        // Check if category already exists; if not, create it
        $category_id = $this->get_category_by_name($data['category']['name']);

        if ($category_id === false) {
            // Category doesn't exist, create it
            $category = new stdClass();
            $category->name = clean_param($data['category']['name'], PARAM_TEXT);
            require_once($CFG->dirroot.'/user/profile/definelib.php');
            profile_save_category($category);

            if (empty($category->id)) {
                throw new moodle_exception('insertcategoryfailed', 'tool_customfields_exportimport');
            }

            $category_id = $category->id;
        } else {
            // Category already exists, reuse it
            require_once($CFG->dirroot.'/user/profile/definelib.php');
        }

        foreach ($data['fields'] as $field) {

            // Skip field if it already exists in this category (idempotent behavior)
            if ($this->field_shortname_exist($field['shortname'], $category_id)) {
                continue;
            }

            $fieldobj = new stdClass();
            $fieldobj->categoryid = $category_id;
            $fieldobj->shortname = clean_param($field['shortname'], PARAM_ALPHANUMEXT);
            $fieldobj->name = clean_param($field['name'], PARAM_TEXT);
            $fieldobj->datatype = clean_param($field['datatype'], PARAM_ALPHANUMEXT);
            $fieldobj->description = clean_param($field['description'] ?? '', PARAM_CLEANHTML);
            $fieldobj->descriptionformat = clean_param($field['descriptionformat'], PARAM_INT);
            $fieldobj->required = clean_param($field['required'], PARAM_INT);
            $fieldobj->locked = clean_param($field['locked'], PARAM_INT);
            $fieldobj->visible = clean_param($field['visible'], PARAM_INT);
            $fieldobj->forceunique = clean_param($field['forceunique'], PARAM_INT);
            $fieldobj->signup = clean_param($field['signup'], PARAM_INT);
            $fieldobj->defaultdata = clean_param($field['defaultdata'] ?? '', PARAM_TEXT);
            $fieldobj->defaultdataformat = clean_param($field['defaultdataformat'], PARAM_INT);
            $fieldobj->param1 = clean_param($field['param1'] ?? '', PARAM_TEXT);
            $fieldobj->param2 = clean_param($field['param2'] ?? '', PARAM_TEXT);
            $fieldobj->param3 = clean_param($field['param3'] ?? '', PARAM_TEXT);
            $fieldobj->param4 = clean_param($field['param4'] ?? '', PARAM_TEXT);
            $fieldobj->param5 = clean_param($field['param5'] ?? '', PARAM_TEXT);

            $editors = [];

            // We always wrap the description as an editor array, even if it's empty,
            // because profile_save_field() expects ['text' => ..., 'format' => ...] format.
            // If we leave it as a plain string, it will break with a type error.
            $fieldobj->description = [
                    'text' => $fieldobj->description ?? '',
                    'format' => $fieldobj->descriptionformat ?? FORMAT_HTML,
            ];
            $editors[] = 'description';

            // Same logic here for defaultdata: profile_save_field() handles it as an editor
            // in case of textarea or similar fields. So we ensure the format is respected.
            $fieldobj->defaultdata = [
                    'text' => $fieldobj->defaultdata,
                    'format' => $fieldobj->defaultdataformat ?? FORMAT_HTML,
            ];
            $editors[] = 'defaultdata';

            $definefile = $CFG->dirroot . '/user/profile/field/' . $fieldobj->datatype . '/define.class.php';

            if (file_exists($definefile)) {
                require_once($definefile);
            }

            $defineclass = 'profile_define_' . $fieldobj->datatype;
            if (!class_exists($defineclass)) {
                $error_detail = $fieldobj->name . ' (' . $fieldobj->datatype . ')';
                throw new moodle_exception('invaliddatatype', 'tool_customfields_exportimport', '', $error_detail);
            }

            profile_save_field($fieldobj, $editors);
        }

    }
}
