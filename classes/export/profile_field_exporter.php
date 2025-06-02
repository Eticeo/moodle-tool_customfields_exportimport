<?php

namespace tool_customfields_exportimport\export;

use stdClass;

class profile_field_exporter implements exporter_field_interface {

    private function get_user_info_field_id(int $fieldid){
        global $DB;

        return $DB->get_record('user_info_field', ['id' => $fieldid], '*', MUST_EXIST);
    }

    private function get_user_info_field_by_category(int $categoryid): array {
        global $DB;

        return $DB->get_records('user_info_field', ['categoryid' => $categoryid], 'sortorder');
    }

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
