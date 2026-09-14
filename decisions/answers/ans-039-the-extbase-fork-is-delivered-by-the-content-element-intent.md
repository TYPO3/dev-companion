---
id: D-ANS-039
title: 'The Extbase fork is delivered by the content-element intent'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aContentElementTaskIsOfferedTheExtbaseForkWithoutNamingIt
---

# D-ANS-039 — The Extbase fork is delivered by the content-element intent

**The `content-element` intent carries the Extbase-or-not fork, worded as
whether a controller has to answer the request rather than as a choice between
two categories.**

It has that wording because on the covered versions a plugin is a content
element. One selector holds both, and what differs is what renders inside the
element. That is the delivery step 2 of
[`D-ANS-027`](ans-027-the-extbase-fork-is-placed-where-a-caller-who-has-not-chosen-passes.md)
queued, and what
[`R-ANS-016`](../../requirements/answers/ans-016-a-content-element-task-is-offered-the-extbase-fork.md)
demands.

## Evidence

- A read of `.checkouts/` settled what the fork asks, and it is not two
  categories of thing. On 14.3 and on `main`,
  `ExtensionUtility::registerPlugin()` hands
  `ExtensionManagementUtility::addPlugin()` a `SelectItem`. That lands at the
  end of `$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items']`,
  the same column `addRecordType()` writes for any other element. So one
  selector holds both and the editor picks from one list.
- What differs is what renders inside it. `configurePlugin()` generates
  `tt_content.<signature> =< lib.contentElement` with `templateName = Generic`
  and `20 = EXTBASEPLUGIN` in 14.3, 13.4, 12.4 and `main` alike. An element
  fills that slot with its own render and its data processors; a plugin fills it
  with a controller dispatch. That is the fork, stated as what a caller chooses
  between rather than as a name for two kinds of registration.
- The category wording would have been wrong on the newest covered majors and
  ambiguous on the oldest. 12.4 defaults `configurePlugin()` to `list_type` and
  writes `tt_content.list.20.<signature>`; 13.4 defaults to `CType` and warns on
  `list_type`; 14.3 and `main` throw on anything but `CType`. Only the pre-13
  form is a category of its own, and it is the one on its way out.
- The probe contrast still holds.
  `bin/cli hints:probe "new content element for testimonials with a repeatable list of entries, TCA and Fluid rendering"`
  reaches `content-elements`, `content-element-shape` and `tca-formengine` on
  2026-08-03. It reaches neither `extbase` nor `frontend-records`.
- `skills/base.md` orders `typo3_task_guide` at step 3 and `typo3_hint_lookup`
  at step 4. So the intent's checklist reaches a task before the hints do and
  before the session reads the checkout at all. It matches on the words of the
  task, `content element`, `CType`, `tt_content`, which is what the session that
  reported it called its work.
- The intent already carried the wrong categories in the two places this
  feedback is about. Its condition read "rather than the page or the plugin
  around it", and its `typo3_extension_describe` line described that answer as
  the templates elements render through.
- The route to the two hints is in the checklist item rather than in the
  intent's `tools` list. `TaskGuide::nextTools()` keeps one entry per tool name,
  and `content-element` is not the first matched intent that names
  `typo3_hint_lookup`. On this task text the entry that survives is the
  extension testing one.

## Decided

- The `content-element` checklist gains two items, after the editor workflow and
  before the registration. They are the fork itself, and what the extension
  already does as evidence about it. A checklist is where a decision that is
  still free belongs. The intent is the one artifact a task reaches when it
  describes its work rather than names a subject.
- Not the `content-elements` hint. That hint answers how to register a record
  type, which is a question a session asks once it has chosen the shape. A fork
  filed there is the second **Wrong if** of `D-ANS-027` in wait. A reader takes
  it as a rule about plugins rather than about the element under construction.
- Not the skill's architecture section.
  `skills/typo3-content-element-development` opens with the line that it keeps
  to routing and design method and sends versioned TYPO3 facts to the tools. "A
  plugin is a `CType` like any other since v14" is exactly such a fact. It is
  also installed into other projects, where no release of this server corrects
  it.
- The condition changes with it. An intent conditioned "rather than the plugin
  around it" excludes the caller whose answer is a plugin, which is the one this
  fork exists for.
- The `typo3_extension_describe` line now says what that answer is evidence
  about, the architecture the extension already has. Where the templates live is
  the second half rather than the whole. That is what the feedback needed and
  did not name.

## Assumed

- That a task guide which reaches a session is a task guide the session reads.
  Nothing here can see whether a session acted on a checklist item. The failure
  this answers is a session that never considered an option rather than one that
  considered and dismissed it.
- That the two hint ids named in the checklist text are worth as much as a
  `tools` entry. Both are prose in the same answer; only the entry falls to the
  deduplication.

## Wrong if

- A report arrives from a content-element session that read the fork and
  answered it by category anyway — "this is an element, not a plugin". The
  wording would then still carry the two categories the checkouts say are one.
  The sentence rather than the placement is what is wrong.
- A session builds a content element against an extension whose scope answer
  said `kind: plugin` and never weighs it. That is step 4 of the ladder and a
  rewrite: delivered and not taken.
- A task guide brief comes back without these items because the caller called
  `typo3_hint_lookup` alone. The intent would then be the wrong owner for
  anything a session has to see, and the hint or the skill is where it belongs.
- The checklist grows so long that a reader takes a fork sixth in it as
  procedure. Seven items is what a content-element task now carries.
