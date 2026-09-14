---
id: D-ANS-027
title: The Extbase fork is placed where a caller who has not chosen passes
date: 2026-08-02
status: revoked
revokedBy: D-ANS-039
---

# D-ANS-027 — The Extbase fork is placed where a caller who has not chosen passes

**The Extbase-or-not fork stands on the two hints it forks between. So it
reaches only a caller who already named the branch they did not take.**

That is step 2 of the ladder, queued as
[`R-ANS-016`](../../requirements/answers/ans-016-a-content-element-task-is-offered-the-extbase-fork.md).

`feedback/2026-08-01-003925` reports a testimonials element built on TCA and a
`DatabaseQueryProcessor` in an extension whose other plugins are Extbase, with
Extbase never considered. The knowledge it would have needed landed three days
before its date.

## Evidence

- The fork stands twice, and both landed on 2026-07-29 with `06e8727`. The
  `extbase` hint opens with it. Extbase is what a frontend needs beyond a read
  of records, and a read-only render of records needs none of it. The
  `frontend-records` hint says the same from the other side. A read of a table's
  records needs no model, no repository and no controller, and Extbase enters
  when pagination, search, validated arguments or forms do. Step 1a is out.
- The probes are the finding.
  `bin/cli hints:probe "new content element for testimonials with a repeatable list of entries, TCA and Fluid rendering"`
  reaches `content-elements` and `tca-formengine`. "content element architecture
  for a new element in a sitepackage" reaches `content-elements`,
  `sitepackage-layout` and `frontend-page-rendering`. Neither reaches `extbase`
  or `frontend-records`. The word records, as in "how to render a list of
  records in a content element", brings `frontend-records` back. The words that
  bring `extbase` back are the ones its `appliesTo` carries: extbase, plugin,
  repository, pagination, flexform.
- Neither hint is generally unreachable. `bin/cli hints:coverage` reports no
  hint its own title misses and 44 of 66 that no scenario prompt reaches.
  `extbase`, `frontend-records` and `content-elements` are in none of those
  lists. This is one query shape, not the coverage gap that command counts.
- The hint that does fire says nothing about it. `content-elements` runs to
  fifteen hints and opens on what the element owns: fields on `tt_content`, a
  child table with `type=inline`, a reference, a container. None of the fifteen
  names Extbase, a repository or a controller.
- The task guide sets the question aside by hand. The `content-element` intent
  in `knowledge/task-intents.json` carries a condition. It reads "only if the
  task adds or changes a content element rather than the page or the plugin
  around it". Its checklist repeats the same four shapes the hint carries.
- The skill that was active carries the same four and no fifth.
  `typo3-content-element-development` opens its architecture section with
  "Choose the content model first" and lists `tt_content` fields, an inline
  child table, references and a container.
- A call the base already orders already answers what the feedback asks for as a
  check. `typo3_extension_describe` reports each content element with a `kind`
  of `element` or `plugin`, where `plugin` is an Extbase plugin from
  `ExtensionUtility::registerPlugin()`
  ([`D-ANS-018`](ans-018-a-plugin-is-a-kind-of-content-element.md)).
  `skills/base.md` puts that call at step 2, before the session reads the
  checkout. Both places that name it frame the answer as where an element
  renders. The intent asks for it "for the content elements this extension
  already registers and the templates they render through".

## Decided

- Step 2, delivery. The fork exists, has the form of a rule, and only a query
  this task had no reason to write reaches it. Not 1a, because it landed on
  2026-07-29. Not 1b, because the tool that reports what the extension already
  uses exists and the base already calls it. Not 4, because nothing here shows
  that the session read the wording and it failed to take.
- The second instance of
  [`D-ANS-024`](ans-024-a-rule-reaches-only-the-task-that-already-names-its-subject.md),
  from the same debrief, and it adds one thing to it. A caller who describes its
  own subject can still reach a rule. A fork filed under one of its branches
  they cannot reach. The word that opens the path is the option the caller has
  not thought of. Naming Extbase is what a session does after it has considered
  Extbase.
- Queued rather than closed on the spot. The placement is the `content-elements`
  hint, the `content-element` checklist in `knowledge/task-intents.json`, or the
  skill's own architecture section. Those are three different claims about who a
  fork belongs to, and the last is a file installed into somebody else's
  project.
- It also needs a read of TYPO3 before its wording, which is the other thing
  [judging.md](../../documentation/records/judging.rst) keeps out of a judging
  run. A plugin is a `CType` like any other since v14. So on the covered
  versions "a content element or an Extbase plugin" is not the two categories it
  was. What the fork asks needs a read of `.checkouts/` rather than a copy of
  the sentence that exists.
- The feedback's own suggestion is not adopted as written. It asks for a rule to
  check how the extension builds its other plugins; the answer already reports
  that and the base already orders it. What it needs and does not name is that
  the answer be readable as evidence about the architecture rather than about
  where a template is.

## Assumed

- That the session named neither Extbase nor a plugin in the words it worked
  from before the architecture was chosen. Its own report is that Extbase was
  never considered, which is that in the session's words, and nothing records
  the queries it phrased. The probe contrast holds either way: it is a property
  of the corpus, not of that run.

## Wrong if

- A content-element task that names neither Extbase nor a repository gets the
  fork, and a session builds against the extension's convention anyway. The fork
  would then arrive and the session would not take it, which is step 4 and a
  rewrite.
- The placement lands on the `content-elements` hint. A report arrives from a
  session that read it as a rule about plugins rather than about the element it
  built. The hint would then be the wrong owner, and the intent or the skill is
  where the fork belongs.
- A session calls `typo3_extension_describe` at base step 2 and still does not
  read a `kind` of `plugin` as the convention the extension already has. The gap
  would then be in that answer's own words rather than in what leads to it.

## Revoked on 2026-08-03

By the delivery this entry queued. The statement was the finding, and it no
longer described this server the moment the intent started to carry the fork.
Everything above it stays. The probe contrast, the hints that name no
controller, and the reason the placement stayed open are why the successor looks
the way it does. `D-ANS-039` holds from here, with a **Wrong if** that is a
different list.
