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
 * @package    tool_customfields_exportimport
 * @copyright 2025 Eticeo https://eticeo.com
 * @author    2025 Serge Touvoli (serge.touvoli@eticeo.fr)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actions'] = 'Actions';
$string['category'] = 'Category';
$string['categoryalreadyexists'] = 'A category with the name "{$a}" already exists.';
$string['categorynameexists'] = 'A category with the name "{$a}" already exists.';
$string['cli_export_failed'] = 'Failed to write to file: {$a}';
$string['cli_export_success'] = 'Export successful: {$a}';
$string['cli_import_failed'] = 'Import failed: {$a}';
$string['cli_import_invalidfile'] = 'Error: Cannot read file at {$a}';
$string['cli_import_invalidjson'] = 'Invalid or missing "type" in JSON data.';
$string['cli_import_success'] = 'Import successful for type: {$a}';
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
$string['clihelp_categoryid'] = 'ID of the category to export.';
$string['clihelp_destination'] = '(Optional) Destination directory to save JSON file (default: current directory).';
$string['clihelp_fieldid'] = '(Optional) ID of a single field to export.';
$string['clihelp_help'] = 'Show this help.';
$string['clihelp_import'] = 'Import customfields or profile fields from a JSON file.

Options:
-h, --help      {$a->help}
-f, --file      {$a->file}

Example:
$php import.php --file=/path/to/fields.json';
$string['clihelp_import_file'] = 'Path to the JSON file to import.';
$string['clihelp_import_help'] = 'Show this help.';
$string['clihelp_type'] = 'Type of field: profile, course, cohort.';
$string['cohortfields'] = 'Cohort fields';
$string['coursefields'] = 'Course fields';
$string['description'] = 'Description';
$string['export'] = 'Export';
$string['export_title'] = 'Export Custom Fields';
$string['exportcategory'] = 'Export category';
$string['exportpage'] = 'Export custom fields';
$string['field'] = 'Field name';
$string['fieldname'] = 'Name';
$string['fieldshortnameexists'] = 'A field with the shortname "{$a}" already exists in this category.';
$string['import'] = 'Import';
$string['importpage'] = 'Import custom fields';
$string['importsuccess'] = 'Import successful';
$string['insertcategoryfailed'] = 'Failed to create the new custom field category.';
$string['invalidfiletype'] = 'The uploaded file must be a valid JSON file.';
$string['invalidjson'] = 'The uploaded JSON file is invalid or incomplete.';
$string['invalidjsonstructure'] = 'The uploaded JSON file does not have the expected structure.';
$string['invalidtab'] = 'Invalid tab selected.';
$string['invalidtype'] = 'Invalid custom field type found in the import.';
$string['pluginname'] = 'Custom Fields Import/Export';
$string['plugintitle'] = 'Custom Fields Import/Export';
$string['privacy:metadata'] = 'The Custom Fields Export/Import tool does not store any personal data.';
$string['profilefields'] = 'Profile fields';
$string['required'] = 'Required';
$string['settingsinfo'] = 'No settings available for now.';
$string['settingspage'] = 'Settings page';
$string['shortnamealreadyexists'] = 'A custom field with the shortname "{$a}" already exists.';
$string['type'] = 'Data type';
$string['invaliddatatype'] = 'Invalid data type for field "{$a}". This field type may not be installed or supported in this Moodle installation. Available types: text, textarea, checkbox, menu, date, datetime.';
$string['typefortable'] = 'Type';

// Migration-related strings
$string['migration_applied'] = 'Migration applied successfully';
$string['migration_failed'] = 'Migration failed';
$string['migration_skipped'] = 'Migration skipped (already applied)';
$string['migration_new'] = 'New migration';
$string['migration_modified'] = 'Modified migration';
$string['filenotfound'] = 'File not found: {$a}';
$string['invalidjson'] = 'Invalid JSON in file: {$a}';
$string['missingtype'] = 'Missing "type" field in JSON file: {$a}';
$string['categoryduplicate'] = 'Category "{$a}" already exists, using existing category (idempotent import)';
$string['fieldduplicate'] = 'Field "{$a}" already exists in category, skipping (idempotent import)';

