<?php

namespace tool_customfields_exportimport\import;

class field_importer {

    public static function make(string $type): importer_field_interface {
        switch ($type) {
            case 'profile':
                return new profile_field_importer();
            case 'course':
                return new customfield_importer('core_course', 'course');
            case 'cohort':
                return new customfield_importer('core_cohort', 'cohort');
            default:
                throw new \moodle_exception('invalidtype', 'tool_customfields_exportimport');
        }
    }
}
