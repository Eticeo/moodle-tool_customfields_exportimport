<?php

namespace tool_customfields_exportimport\export;

class field_exporter {

    public static function make(string $type): exporter_field_interface {
        switch ($type) {
            case 'profile':
                return new profile_field_exporter();
            case 'course':
                return new customfield_exporter('core_course');
            case 'cohort':
                return new customfield_exporter('core_cohort');
            default:
                throw new \moodle_exception('invalidtype', 'tool_customfields_exportimport');
        }
    }
}
