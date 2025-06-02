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

    private function customfield_shortname_exists(string $shortname): bool {
        global $DB;
        return $DB->record_exists('customfield_field', ['shortname' => $shortname, 'component' => $this->component, 'area' => $this->area]);
    }

    private function customfield_category_exists(string $name): bool {
        global $DB;
        return $DB->record_exists('customfield_category', ['name' => $name, 'component' => $this->component, 'area' => $this->area]);
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
        $category->timecreated = time();
        $category->timemodified = time();
        $category->itemid = 0;

        if($this->customfield_category_exists($category->name)) {
            throw new moodle_exception(
                    'categoryalreadyexists',
                    'tool_customfields_exportimport',
                    '',
                    $category->name
            );
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
            $fieldobj->categoryid = $categoryid;
            $fieldobj->shortname = $field['shortname'];
            $fieldobj->name = $field['name'];
            $fieldobj->type = $field['type'];
            $fieldobj->description = $field['description'];
            $fieldobj->descriptionformat = $field['descriptionformat'];
            $fieldobj->sortorder = $field['sortorder'] ?? 0;
            $fieldobj->timecreated = time();
            $fieldobj->timemodified = time();

            $DB->insert_record('customfield_field', $fieldobj);
        }
    }
}
