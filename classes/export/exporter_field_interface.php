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

namespace tool_customfields_exportimport\export;


/**
 * Interface for exporting custom field data.
 *
 * @package    tool_customfields_exportimport
 */
interface exporter_field_interface {

    /**
     * Exports the custom field data for a given category and optional fieldid passed
     *
     * @param int $categoryid
     * @param int|null $fieldid
     * @return array exported data in json format
     */
    public function export(int $categoryid, ?int $fieldid = null): array;
}
