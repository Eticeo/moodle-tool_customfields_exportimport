# moodle–tool_customfields_exportimport

This plugin allows Moodle administrators to **export and import** custom fields in JSON format for the following components:

- **User profile fields**
- **Course custom fields**
- **Cohort custom fields**

The tool is accessible both via **command line interface (CLI)** and a **basic web interface** for import/export.

---

##  Features

- Export profile, course or cohort custom fields to JSON
- Import previously exported fields from a JSON file
- CLI commands for automation and scripting
- Basic web interface for manual operations

---

## How it works

### Exporting custom fields

You can export custom fields by type (`profile`, `course`, `cohort`) and by category.

**Command examples**:

Export all fields from category 1 in user profile fields:

```bash
php export.php --type=profile --categoryid=1
```

Export a specific field (ID 4) from course custom fields:

```bash
php export.php --type=course --categoryid=2 --fieldid=4 --destination=/path/to/save
```

Export all cohort fields from category 3:

```bash
php export.php -t=cohort -c=3
```

The file is saved as `export_{type}_category{ID}.json` in the destination folder (current folder by default)

### Importing custom fields

You can import a previously exported JSON file:

```bash
php import.php --file=/path/to/export_profile_category1.json
```

The plugin will validate the type and insert/update the category and fields accordingly.

---

## Web Interface

You can use a basic page in the admin tool interface at:

```
/admin/tool/customfields_exportimport/index.php
```

It lets you:

- Upload a JSON file for import
- See confirmation or error messages after processing

---

## Installation

### Via ZIP

- Log in as admin
- Go to *Site administration > Plugins > Install plugins*
- Upload the ZIP of this plugin
- Complete installation steps

### Manual installation

- Unzip in:

```
/admin/tool/customfields_exportimport/
```

- Visit *Site administration > Notifications* to complete installation

Or use CLI:

```bash
php admin/cli/upgrade.php
```

---

## License

GNU GPL v3 or later 

Developed by Serge Touvoli
