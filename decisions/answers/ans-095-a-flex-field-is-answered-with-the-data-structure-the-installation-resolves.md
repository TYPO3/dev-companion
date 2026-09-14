---
id: D-ANS-095
title: A flex field is answered with the data structure the installation resolves
date: 2026-08-21
status: open
coveredBy:
  - FlexFormLookupTest::theStructureIsTheOneTheInstallationResolved
---

# D-ANS-095 — A flex field is answered with the data structure the installation resolves

**A tool of its own resolves one `type=flex` column through the installation's
own `FlexFormTools`, from a record the caller supplies the values of.** Today's
answer is the bound structure a registration declares. What a writer may put
into the column is the structure behind it, and nothing here opens one.

## Evidence

- Two sessions stop at the same boundary, from two task shapes.
  `feedback/2026-08-21-074351` compared this server against another one and
  found that `typo3_schema_lookup` answers the DDL column with no call to make
  for what goes in it. `feedback/archive/2026-07-31-194510` audited
  `printworks_sitepackage` and named the two content elements the answer
  described least as exactly the two whose FlexForm it did not open. The first
  asks for the structure, the second asked for the bound reference, and the
  bound reference is what landed.
- The bound reference is where today's answer stops. `typo3_extension_describe`
  carries the data structure per content element "as the call declares it — a
  FILE:EXT: reference, or `inline`". No second tool goes past it. `src/Tool/`
  holds none about flex,
  `bin/cli hints:probe "flex field data structure identifier"` matched nothing
  on 2026-08-21, and no skill names flex at all.
- The file the bound reference names, opened, is not what the backend sees. On
  14.3 `FlexFormTools::parseDataStructureByIdentifier()` runs the found
  structure through `convertDataStructureToArray()`, `ensureDefaultSheet()`,
  `resolveFileDirectives()` and `checkMigratePrepareFlexTca()`, between
  `BeforeFlexFormDataStructureParsedEvent` and
  `AfterFlexFormDataStructureParsedEvent`. A structure with no sheet carries
  `sDEF` afterwards, and a sheet in a file of its own resolves. Every field
  migrates and prepares, and a listener may have replaced the whole of it.
- The identifier is a second answer with a second pair of events.
  `getDataStructureIdentifier()` dispatches
  `BeforeFlexFormDataStructureIdentifierInitializedEvent`, falls back to the
  default identifier and dispatches the `After` one; the default on 14.3 is
  `{"type":"tca","tableName":…,"fieldName":…,"dataStructureKey":null}`.
- Which structure it is depends on the record, which is why the caller has to
  supply values. On 12.4 and 13.4 `ds_pointerField` names up to two columns of
  the row, and their values key the `ds` array: `news_pi1,list` beside
  `default`.
- The mechanism differs across the covered majors, so a caller who reasons from
  one is wrong on another. `Breaking-107047` removed `ds_pointerField` and the
  multi-entry `ds` array in v14. `ds` names one structure, and a FlexForm per
  record type comes through `types` with `columnsOverrides`.
- The API differs too. `getDataStructureIdentifier()` and
  `parseDataStructureByIdentifier()` take `array|TcaSchema|null $schema` on 14.3
  and on `main`. The default path throws `InvalidTcaSchemaException`
  (1753182123) where it is null. 12.4 and 13.4 read `$GLOBALS['TCA']` and have
  no such parameter.
- FormEngine resolves it with those two calls and nothing else, in
  `Backend\Form\FormDataProvider\TcaFlexPrepare`. So an answer taken from them
  is what the backend form would build.
- The seam exists. `probe.php` boots the installation and asks its container,
  and `Typo3Runtime::configuration()` is the one topic that takes a parameter.
  The value goes into the payload, and the memoized read drops where it changes.
- No database is reached. The resolution reads TCA, files and events. The
  derived columns of `D-DIS-012` differ: they ask the server for a version on
  three of the four drivers.

## Decided

- Step 1b, the shape. The answer is available to this server: the same container
  the probe already asks, one boot, no credential and no second host. No
  argument to any tool reaches it. Not step 4: the wording of
  `typo3_extension_describe` is accurate about what it carries.
- Taken on. The second session and the failure mode justify it against a
  suggestion. The declared reference reads as the whole answer. A caller who
  opens the file it names gets a structure the installation would migrate and
  normalize. A listener could replace it before FormEngine ever sees it.
- A tool of its own, `typo3_flexform_lookup`, rather than a field on
  `typo3_schema_lookup`. The two answer the two halves of one column, the DDL
  side and what a writer may put into it. The second half needs arguments the
  first has no use for, and returns sheets where the first returns columns.
  `lookup` is the verb `typo3_schema_lookup` already carries for a named table
  answered from the installation.
- The tool emulates the record from values the caller passes, and nothing loads
  a row. The feedback offers a `recordUid` that reads one. The content itself is
  what `knowledge/server-scope.json` declares this server does not touch, and a
  uid is that line.
- Priority `normal`. Two sessions reported it and neither lost a task, which
  keeps it below the work that has. The extension author is one of the three
  audiences, and a plugin with a FlexForm is ordinary work for them. The
  mechanism changed inside the covered majors. That takes it off the `low` the
  card arrived at.

## Assumed

- Values a caller supplies are enough for the listeners in the wild. A listener
  that reads the row it gets has what it needs. One that queries the database by
  `uid` sees an emulated record and may answer differently from the backend.
- An extension author who asks this has an installation that boots. Where it
  does not, the answer is the reason rather than the file the reference names. A
  file read here would be the guess this tool exists to replace.

## Wrong if

- A run or a feedback reports a resolved structure that differs from what the
  backend form shows for the same record. The emulated record is the cause. Then
  caller-supplied values are the wrong boundary, and the tool owes the caller a
  way to say what it could not resolve.
- The calls that arrive are about fields with one `ds`, no listener and no
  record type. There the answer is the file the caller could have opened. Then
  this is a file read in a boot's clothes, and the reference
  `typo3_extension_describe` already carries was the whole gap.
- It reports `unavailable` more often than it answers, which is the test
  `D-FBK-027` sets for a lookup that buys its caller nothing.
