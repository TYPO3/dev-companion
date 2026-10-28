---
id: D-KNW-178
title: A content element showitem is told which tabs the core adds
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::aContentElementShowitemIsToldWhichTabsTheCoreAdds
---

# D-KNW-178 — A content element showitem is told which tabs the core adds

**`content-elements` states which tabs the TCA build adds to every
`tt_content` type, and which hand-written tab it strips.**

## Evidence

- `feedback/2026-10-28-052325`. A session on 14.3 wrote a language and an access
  tab into a new CType's `showitem` and saw each twice. It got
  `content-elements` and `content-element-shape`, and neither said that the core
  adds tabs.
- `bin/cli hints:probe "addRecordType showitem tabs language access appended"`
  on 2026-10-28 reached `content-elements` first, with nothing on the tabs.
- The feedback named the wrong mechanism. `addRecordType()` appends the extended
  tab alone, and only where the list lacks it. That holds in `.checkouts/13.4`,
  `14.3` and `main`, with the label `core.form.tabs:extended` from 14 on.
- `TcaPreparation::addSystemFieldsToShowitemTypes()` adds the rest, in `13.4`,
  `14.3` and `main` alike. It strips the system fields, their palettes, and the
  general, language, access and notes tabs under the core's label, in either
  form. Then it writes the general palette first, the type's own items, the
  language, access and notes tabs, and the extended tab last. The changelog is
  `Feature-104814-AutomaticallyAddSystemFieldsToContentTypes.rst`, 13.3.
- `.checkouts/12.4` has neither method, so the statement is bound `since: 13`.
- `TabsContainer::render()` drops a tab whose content renders empty. So a
  hand-written tab that held only system fields disappears, and one that held
  anything else stands beside the core's tab.

## Decided

- Step 1a, closed on the spot. The judgement run read the checkouts and held
  the evidence, so the change lands with it (`D-FBK-052`).
- One statement in `content-elements`, after the one that introduces the
  `showitem` argument. That is where a session writes the string.
- The worked example is the core's `text` type, as the feedback asked. Its shape
  is read from `14.3`. The `general` palette the feedback listed is not in it,
  because the core adds that palette.
- The statement names the condition for a duplicate rather than the feedback's
  "duplicates". The core strips a tab under its own label, and the form drops
  an emptied one. A duplicate needs a label of its own and something left in
  the tab.

## Assumed

- That the session's two tabs carried labels of their own or other items. The
  feedback does not quote its string, and the code produces no duplicate
  otherwise.

## Wrong if

- A session reports a duplicate tab from a `showitem` that uses the core's
  labels and holds only system fields. The strip or the drop would then work
  differently from the read here.
- A covered branch extends the rebuild beyond `tt_content`. The method's own
  `@todo` names that for a later major, and the statement would then hold for
  more than content elements.
