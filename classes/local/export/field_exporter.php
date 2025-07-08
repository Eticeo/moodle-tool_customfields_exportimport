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

/**
 * field_exporter class
 *
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
