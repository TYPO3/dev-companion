---
id: D-ANS-171
title: A core issue is drafted and handed over as the filled form
date: 2026-10-05
status: open
coveredBy:
  - IssueReportTest
---

# D-ANS-171 — A core issue is drafted and handed over as the filled form

**`typo3_issue_report_guide` drafts a new TYPO3 Core issue and returns the
tracker's form with every field it can fill. Filing stays with the person.**

The same call checks the draft and searches for duplicates. Every draft carries
the tag `dev-companion`.

## Evidence

- The maintainer asked on 2026-10-05 for a simple way to file the bugs a session
  finds on the way, drafted here and opened as a filled form, for core work
  alone. The tool name, the duplicate search in the same call, the target
  version, the regression flag and the tag were the maintainer's choices.
- Read on 2026-10-05. An anonymous request for
  `/projects/typo3cms-core/issues/new?issue[...]=…` redirects to the login and
  keeps every parameter in `back_url`. The maintainer opened such a link signed
  in and got the form with tracker, subject, description and TYPO3 Version
  filled.
- The field ids come from the tracker: TYPO3 Version 4, PHP Version 5, Tags 3,
  Complexity 8 and Is Regression 15 on a filed issue, the priorities from
  `/enumerations/issue_priorities.json`, the open versions from
  `/projects/typo3cms-core/versions.json`. The custom field list itself needs an
  administrator.
- The tracker refused a request line of about 8200 characters with 414. A login
  link of 7934 characters reached the login page, and one of 8486 did not. The
  login link encodes the form link a second time, so it is the longer of the
  two.

## Decided

- `guide`, because the answer is a composed draft that always exists, as
  `typo3_commit_message_guide` is. It names `typo3_forge_lookup` and that tool
  names it back.
- The description comes as Textile or as its four parts in the order of the
  reporting page. The checks are the page's markup traps, the Bug's one required
  field, and a category or target version the project does not have. A value the
  project does not have stays out of the form.
- The duplicate search runs in the same call, because the reporting page owed it
  first anyway. An empty search is not a negative, and the answer says so.
- Where the login link would pass 7900 characters, the link fills everything but
  the description, and the answer carries the description to paste.
- Assignee, Sprint Focus, the dates and the status stay as the form leaves them.
  Whoever takes the issue on sets them.

## Assumed

- That the field ids stay. A tracker that renumbers a custom field fills the
  wrong field, and nothing here would notice.

## Wrong if

- A filled form arrives with a field empty that the link carried.
- A description below the measured length still fails at the login.
