---
date: 2026-09-01T21:03:31+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3_record_lookup, typo3_schema_lookup
directory: /home/benji/projects/typo3-cms
---

# Record API puts enable fields under _system and unsets them from the main properties

## Observation

Migrating a local memory into the server: this is knowledge the server does not answer today.

The TYPO3 Record API (\TYPO3\CMS\Core\Domain\Record) moves enable/system fields into SystemProperties, and RecordFactory unsets them from the main properties array. A record's regular fields therefore do NOT contain hidden, starttime, endtime, fe_group, crdate or tstamp.

- $record->toArray(true) returns the regular fields plus '_system' => SystemProperties::toArray().
- _system keys: isDisabled (bool), isDeleted, isLockedForEditing, publishAt / publishUntil (\DateTimeInterface), createdAt / lastUpdatedAt (\DateTimeInterface), userGroupRestriction (int[] of uids including the special -1 / -2), sorting, description, language, version.
- Accessors: $record->getSystemProperties()?->isDisabled() and so on.

The gotcha that costs time: reading $row['hidden'] on a Record-sourced row is always empty, so it reads as "not disabled". Page module rows are Record-sourced (PageLayoutContext::getLocalizedPageRecord() / getPageRecord()). FormEngine's databaseRow in the edit view is the opposite — a flat array with the enable fields at top level (hidden as a scalar, datetimes as ISO strings, fe_group as an array).

Code consumed by both surfaces must check _system first and fall back to the raw field.

## Query

Migrated from a local project memory file (reference_record_api_system_props.md). Originally learned while making one renderer work against both Record API rows and FormEngine databaseRow.

## Suggestion

Add a hint covering the Record API's system properties, matched by paths under Core/Domain/Record and by queries like "hidden field empty", "record is disabled", "enable fields". It should state the _system key list, the two opposing row shapes (Record-sourced versus FormEngine databaseRow), and the check-_system-then-fall-back rule, because the failure is silent: the value reads as not-disabled rather than raising anything.
