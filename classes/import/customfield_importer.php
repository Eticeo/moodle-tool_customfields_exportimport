<?php

namespace tool_customfields_exportimport\import;

use stdClass;
use moodle_exception;

class customfield_importer implements importer_field_interface {

    private string $component;
    private string $area;

    public function __construct(string $component, string $area) {
        $this->component = $component;
        $this->area = $area;
    }

    public function import(array $data): void {
        global $DB;

        if (!isset($data['category']) || !isset($data['fields']) || !is_array($data['fields'])) {
            throw new moodle_exception('invalidjsonstructure', 'tool_customfields_exportimport');
        }

        $category = new stdClass();
        $category->name = $data['category']['name'];
        $category->sortorder = $data['category']['sortorder'] ?? 0;
        $category->component = $this->component;
        $category->area = $this->area;
        $category->itemid = 0;

        $categoryid = $DB->insert_record('customfield_category', $category);

        foreach ($data['fields'] as $field) {
            $fieldobj = new stdClass();
            $fieldobj->categoryid = $categoryid;
            $fieldobj->shortname = $field['shortname'];
            $fieldobj->name = $field['name'];
            $fieldobj->type = $field['type'];
            $fieldobj->description = $field['description'];
            $fieldobj->descriptionformat = $field['descriptionformat'];
            $fieldobj->sortorder = $field['sortorder'] ?? 0;
            $fieldobj->param1 = $field['configdata'] ?? '';

            $DB->insert_record('customfield_field', $fieldobj);
        }
    }
}
