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

/**
 * English strings for customfields_exportimport
 *
 * @package   tool_customfields_exportimport
 * @copyright 2025 Serge Touvoli <serge.touvoli@eticeo.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Title
$string['plugintitle'] = 'Custom Fields Import/Export';
$string['pluginname'] = 'Custom Fields Import/Export';
$string['field'] = 'Field name';
$string['description'] = 'Description';
$string['type'] = 'Data type';
$string['required'] = 'Required';
$string['actions'] = 'Actions';
$string['export'] = 'Export';
$string['category'] = 'Category';
$string['cohortfields'] = 'Cohort fields';
$string['exportcategory'] = 'Export category';
$string['importsuccess'] = 'Import successful';
$string['profilefields'] = 'Profile fields';
$string['coursefields'] = 'Course fields';
$string['exportpage'] = 'Export custom fields';
$string['importpage'] = 'Import custom fields';
$string['settingspage'] = 'Settings page';
$string['settingsinfo'] = 'No settings available for now.';
$string['export_title'] = 'Export Custom Fields';


// CLI
$string['clihelp'] = 'Export customfields or user profile fields (JSON format).

Options:
-h, --help          {$a->help}
-t, --type          {$a->type}
-c, --categoryid    {$a->categoryid}
-f, --fieldid       {$a->fieldid}
-d, --destination   {$a->destination}

Example:
\$php export.php --type=profile --categoryid=1
\$php export.php --type=course --categoryid=2 --fieldid=5';
$string['clihelp_help'] = 'Show this help.';
$string['clihelp_type'] = 'Type of field: profile, course, cohort.';
$string['clihelp_categoryid'] = 'ID of the category to export.';
$string['clihelp_fieldid'] = '(Optional) ID of a single field to export.';
$string['clihelp_destination'] = '(Optional) Destination directory to save JSON file (default: current directory).';

$string['cli_export_success'] = 'Export successful: {$a}';
$string['cli_export_failed'] = 'Failed to write to file: {$a}';


// Cli Import
$string['clihelp_import'] = 'Import customfields or profile fields from a JSON file.

Options:
-h, --help      {$a->help}
-f, --file      {$a->file}

Example:
$php import.php --file=/path/to/fields.json';
$string['clihelp_import_help'] = 'Show this help.';
$string['clihelp_import_file'] = 'Path to the JSON file to import.';

$string['cli_import_success'] = 'Import successful for type: {$a}';
$string['cli_import_failed'] = '❌ Import failed: {$a}';
$string['cli_import_invalidfile'] = 'Error: Cannot read file at {$a}';
$string['cli_import_invalidjson'] = 'Invalid or missing "type" in JSON data.';

