---
id: D-KNW-023
title: 'Which page may hold a record is a subject this server owns'
date: 2026-08-02
status: confirmed
coveredBy:
  - HintsTest::thePlacementAnswerSaysWhichPageMayHoldTheRecord
---

# D-KNW-023 — Which page may hold a record is a subject this server owns

**Where a record may live is inside this server's boundary and absent from it.
So the feedback shrinks to that half and goes to the queue.**

The corpus says where a new record is *positioned* on a page. Nothing says which
page may *hold* it, which is the question a seed session asks first and the one
this one guessed at.

## Evidence

- The position half has an answer, in the words the feedback's own query uses.
  Called over stdio with that query as `task` and `targetVersion: "14.3"`,
  `typo3_hint_lookup` returns `datahandler-persistence` and `frontend-records`.
  The first carries "A new record is placed at the TOP of its page: the pid
  field is the positioning pid". It carries the negative form `-<uid>` for
  records in order.
- That answers the sort half too. A seed run positions with the pid rather than
  writes `sorting`, so the values this session guessed at are ones it should not
  have set.
- Those statements are older than the session that missed them. They landed on
  2026-07-29 in `2d0c533`, and the feedback carries the date 2026-08-01. This is
  not a version of the server that no longer exists.
- The other half is in nothing here. "sysfolder", "doktype", "storage folder"
  and `storagePid` occur below `knowledge/` in one sentence, on the Fluid hint
  `frontend-page-rendering`, and from the opposite angle. `excludeDoktypes`
  keeps storage folders *out* of a menu.
- That sentence is also unreachable from this task.
  `bin/cli hints:probe "doktype 254 sysfolder storagePid page tree"` matches
  nothing at all. The query counts as `php`, and the one place the words occur
  is Fluid.
- The feedback's claim about TYPO3 holds, and the mechanism is one method. On
  `.checkouts/14.3`, `DataHandler::isTableAllowedForThisPage()` decides an
  insert on two things. The TCA root-level capability where the pid is 0, and
  `PageDoktypeRegistry::isRecordTypeAllowedForDoktype()` against the target
  page's doktype everywhere else.
- The storage folder is the doktype that allows any table.
  `Configuration/TCA/ pages.php` gives `DOKTYPE_SYSFOLDER` an
  `allowedRecordTypes` of `['*']`, above the comment "a general purpose storage
  folder for whatever you like. In CMS context it's NOT a viewable page."
- A standard page is the other side of it. `DOKTYPE_DEFAULT` declares no
  `allowedRecordTypes`, so `getAllowedTypesForDoktype()` falls back to `pages`,
  `sys_category`, `sys_file_reference` and `sys_file_collection`. A table of
  one's own meets a refusal there and passes in a folder. That makes the folder
  a rule in force rather than an editorial habit.
- An admin does not get past it either. `hasPageContextPermission()` returns
  early for an admin and for `bypassAccessCheckForRecords`, and
  `hasPermissionToInsert()` calls `isTableAllowedForThisPage()` after it. So the
  doktype check runs on the run a seed script makes.
- Where that list lives moved between majors. On 12.4 and 13.4 it sits hardcoded
  in `PageDoktypeRegistry` as `allowedTables => '*'`. On 14.3 it is TCA, and
  both `add()` and `addAllowedRecordTypes()` carry a deprecation for removal in
  v15. So the statement needs a range read across three checkouts, and this run
  may not write it.

## Decided

- Step 1a of the ladder on the "which page may hold this record" half, and
  queued rather than closed on the spot. What lands is a corpus statement about
  TYPO3, read across three majors.
- The feedback shrinks rather than goes to the archive. Its pid and sort half
  has an answer, and the todo is what remains.
- Not step 2 and not step 3 for the answered half. That this session called
  nothing until the user demanded it is
  `feedback/2026-08-01-003356-did-not-consult-the-mcp-knowledge-server-or.md`,
  with its judgement on its own card. A second walk here gives one gap a second
  entry.
- The DataHandler-instead-of-SQL and inline-relation halves of the same debrief
  are
  `feedback/2026-08-01-003216-lacked-datahandler-knowledge-and-worked-around.md`
  and
  [`D-KNW-018`](knw-018-what-a-datamap-does-to-a-relation-field-is-a-subject-this-server-owns.md).
  This entry adds only where a record may go.

## Assumed

- The statement belongs on `datahandler-persistence`, beside the position pid.
  The question comes up while a session writes records, not while it declares
  the TCA that describes them.
- The read side is a different question and stays where it is.
  `frontend-records` already names `pidInList`, and what an Extbase
  `persistence.storagePid` does is a question for the Extbase hint.

## Wrong if

- The restriction is not on the path a seed datamap takes. A console run without
  a real backend user meets a refusal before it, or reaches the insert by
  another route. The statement then describes a check the session never met.
- The three majors differ in more than where the allowed list lives. The hint
  then gains a statement per major where the todo planned one with a range.

## Confirmed on 2026-08-03

Both **Wrong if** had their read and neither holds. The check is on the path a
seed datamap takes, against the pid after a negative position value has
resolved. A refusal is a log entry and then a continue. So the call returns with
no error and the record is absent, which is the shape the feedback reported.

The majors differ in nothing but where the allowed list lives, so one statement
carries `since: 14` and one `until: 13`. The boundary predicted what `main` has
since done. Where the statement lands no longer describes the corpus. The hint
split, and it went to the half about which page may hold the record at all.
