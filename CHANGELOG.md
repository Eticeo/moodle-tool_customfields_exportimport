# Changelog

## [2.0.0] - 2025-11-24
### Added
- **Laravel-style migration system** for safe and automated custom field deployments:
    - New database table `tool_customfields_migrations` to track applied migrations.
    - Automatic detection of pending or modified migration files via MD5 hashing.
    - `--auto` CLI flag to discover and apply migrations in chronological order.
    - `--dry-run` CLI flag to preview migration changes without applying them.
    - Idempotent import process: safely re-imports categories and fields without duplicates.
    - Support for migration naming convention `YYYYMMDD_description.json`.
    - New class `migration_manager` handling discovery, tracking and execution.
    - Added language strings for migration-related functionality.

### Changed
- Refactored custom field importer logic to be fully idempotent:
    - Reuses existing categories instead of failing.
    - Skips existing fields while maintaining consistent import state.
- Improved error messages with clearer context (field name, datatype, unsupported types).
- Bumped plugin version to **2.0.0** due to new migration infrastructure and database changes.

### Fixed
- **Privacy API compliance**: moved `privacy/provider.php` to the correct location (`classes/privacy/provider.php`)
  to comply with Moodle's privacy subsystem structure.
- Resolved multiple edge-case issues during re-import scenarios (duplicate categories/field shortnames no longer trigger errors).

## [1.0.1] - 2025-06-02
### Fixed
- Custom fields: added `timecreated` and `timemodified` properties to imported categories and fields.
- Custom fields: added validation to prevent importing a category with a name that already exists.
- Custom fields: added validation to prevent importing fields with duplicate shortnames.
