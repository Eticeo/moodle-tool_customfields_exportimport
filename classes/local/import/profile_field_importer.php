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
 * Interface for importing custom field data.
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_importer implements importer_field_interface {

    private function category_name_exist(string $name): bool {
        global $DB;
        return $DB->record_exists('user_info_category', ['name' => $name]);
    }

    private function field_shortname_exist(string $shortname, int $categoryid): bool {
        global $DB;
        return $DB->record_exists('user_info_field', ['shortname' => $shortname, 'categoryid' => $categoryid]);
    }


    public function import(array $data): void {

        global $CFG;

        if (!isset($data['category']) || !isset($data['fields']) || !is_array($data['fields'])) {
            throw new moodle_exception('invalidjsonstructure', 'tool_customfields_exportimport');
        }

        if($this->category_name_exist($data['category']['name'])) {
            throw new moodle_exception('categorynameexists', 'tool_customfields_exportimport');
        }


        $category = new stdClass();
        $category->name = clean_param($data['category']['name'], PARAM_TEXT);
        require_once($CFG->dirroot.'/user/profile/definelib.php');
        profile_save_category($category);

        if (empty($category->id)) {
            throw new moodle_exception('insertcategoryfailed', 'tool_customfields_exportimport');
        }



        foreach ($data['fields'] as $field) {

            if($this->field_shortname_exist($field['shortname'], $category->id)) {
                throw new moodle_exception('fieldshortnameexists', 'tool_customfields_exportimport');
            }


            $fieldobj = new stdClass();
            $fieldobj->categoryid = $category->id;
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


            $defineclass = 'profile_define_' . $fieldobj->datatype;
            if (!class_exists($defineclass)) {
                throw new moodle_exception('invalidtype', 'tool_customfields_exportimport');
            }

            profile_save_field($fieldobj,$editors);
        }

    }
}
