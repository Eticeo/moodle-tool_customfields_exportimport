<?php

namespace tool_customfields_exportimport\import;

use moodle_exception;
use stdClass;

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
            throw new moodle_exception('categorynameexists', 'tool_customfields_exportimport', '', $data['category']['name']);
        }


        $category = new stdClass();
        $category->name = $data['category']['name'];
        require_once($CFG->dirroot.'/user/profile/definelib.php');
        profile_save_category($category);

        if (empty($category->id)) {
            throw new moodle_exception('insertcategoryfailed', 'tool_customfields_exportimport');
        }



        foreach ($data['fields'] as $field) {

            if($this->field_shortname_exist($field['shortname'], $category->id)) {
                throw new moodle_exception('fieldshortnameexists', 'tool_customfields_exportimport', '', $field['shortname']);
            }


            $fieldobj = new stdClass();
            $fieldobj->categoryid = $category->id;
            $fieldobj->shortname = $field['shortname'];
            $fieldobj->name = $field['name'];
            $fieldobj->datatype = $field['datatype'];
            $fieldobj->description = $field['description'];
            $fieldobj->descriptionformat = $field['descriptionformat'];
            $fieldobj->required = (int)$field['required'];
            $fieldobj->locked = (int)$field['locked'];
            $fieldobj->visible = (int)$field['visible'];
            $fieldobj->forceunique = (int)$field['forceunique'];
            $fieldobj->signup = (int)$field['signup'];
            $fieldobj->defaultdata = $field['defaultdata'];
            $fieldobj->defaultdataformat = $field['defaultdataformat'];
            $fieldobj->param1 = $field['param1'];
            $fieldobj->param2 = $field['param2'];
            $fieldobj->param3 = $field['param3'];
            $fieldobj->param4 = $field['param4'];
            $fieldobj->param5 = $field['param5'];

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
                debugging("Classe {$defineclass} introuvable", DEBUG_DEVELOPER);
            }


            profile_save_field($fieldobj,$editors);
        }

    }
}
