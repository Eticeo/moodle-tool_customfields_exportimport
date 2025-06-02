<?php

namespace tool_customfields_exportimport\export;

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
