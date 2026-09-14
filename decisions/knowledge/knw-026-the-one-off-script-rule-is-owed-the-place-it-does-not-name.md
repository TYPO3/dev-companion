---
id: D-KNW-026
title: 'The one-off script rule is owed the place it does not name'
date: 2026-08-02
status: open
---

# D-KNW-026 — The one-off script rule is owed the place it does not name

**The corpus places a one-off script and names only `var/` as the wrong place.
So nothing here stood between a session and a PHP file in the webroot.**

The rule this feedback asks for is half written already. The gap is the place it
does not name, and any way to reach it from the moment of need.

## Evidence

- The feedback's own query reaches nothing.
  `bin/cli hints:probe "writing and executing a PHP script in the live webroot to introspect core classes"`
  matched no hint on 2026-08-02. It returned its 40 candidates as an index.
- The placement rule is here, on `project-repository-layout` in
  `knowledge/architecture-hints/general.json`. `Build/` is for "what runs before
  or around the site rather than in it". Its list there includes "a one-off
  script that seeds an installation".
- The same hint says where such a script may not go, and it names one place. "A
  script that exists for one run therefore does not go there because it is
  ignored; it goes into Build/ with the rest of the tooling, or it is not kept
  at all." The place is `var/`. The reason it gives is that `var/` is outside
  git.
- The document root occurs three times below `knowledge/` and never as a place a
  file may not go. They are `public-assets` on the `_assets/` publication,
  `environment-variables`, and `project-extension-tests` on the paths
  `$testExtensionsToLoad` takes.
- The rule is reachable only from a query that already names the answer. Probed
  on 2026-08-02, "where do I put a one-off script" and "one-off debug script
  placement in a TYPO3 project" match nothing at all.
- The one form that reaches it is the one that needs it least. "write a php file
  into the document root and call it in the browser" returns
  `project-repository-layout` at `text(109)` and `public-assets` at `text(67)`.
- That contrast is
  [`D-ANS-024`](../answers/ans-024-a-rule-reaches-only-the-task-that-already-names-its-subject.md)
  measured on a second rule. A caller who can phrase that query has already
  taken the decision the rule exists to change.
- The corpus endorses a throwaway boot script for a neighbour purpose and says
  nothing about where it lives. `sitepackage-initial-content` states that a seed
  "is a throwaway script that boots TYPO3 itself", down to
  `Bootstrap::init($classLoader)` and its `$failsafe` argument.
- No tool is the gap.
  [`D-ANS-003`](../answers/ans-003-retrieval-stays-lexical-and-runtime-inspection-stays-narrow.md)
  read this same script on 2026-08-02 as evidence about its runtime half, and
  recorded that the answer came from a manual page.
- The other half of the **Suggestion** belongs to a sibling.
  `feedback/2026-08-01-003933` asks for a read of the installed source instead
  of a guess, which
  [`D-FBK-021`](../feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md)
  already mapped as "what to do instead". It is in hand on another branch on
  this entry's day.
- Whether the server served the file has no ground here. The session wrote
  `/var/www/html/check_record.php`, and what a DDEV TYPO3 project serves from
  that path is a DDEV and Composer question. This run read only this repository.

## Decided

- Step 2 of the ladder. The rule exists, reads as a rule, and only a caller who
  already names the document root reaches it.
- The clause that would name the document root is absent as well. That is the
  wording half of the same sentence, it lands in the same place, and it is
  therefore one card rather than two.
- Queued rather than closed on the spot. The sentence rests on where a Composer
  installation's document root is, and on what DDEV mounts at `/var/www/html`.
  That is a lookup, and [judging.md](../../documentation/records/judging.rst)
  keeps a lookup out of a run that has read only this repository.
- Not step 1b. `D-ANS-003` refused a runtime tool for this, on the evidence of
  this very session.
- The introspection half is not restated. `003933` owns it, and a second card
  for one step is the overlap `bin/cli todo:claim` warns about. The card from
  here names that overlap, so the two sentences land together rather than twice.
- The feedback stays open. The card in `todo/open/` is what archives it when the
  clause lands.
- The debug leftovers the self-score reports are the same substitution again.
  `D-FBK-021` maps `fwrite` and `extract` in a regression test and a throwaway
  `LinkDebugTest` here. The clause's "or it is not kept at all" is where they
  land.

## Assumed

- That `project-repository-layout` is the hint that carries it. A session about
  to write a debug file decides where a file goes, whether or not it phrases the
  question that way.
- That words in `appliesTo` can make the rule reach. The moment of need is a
  decision the caller has already taken, and no path signal carries it.

## Wrong if

- `/var/www/html` turns out to be outside what a DDEV TYPO3 project serves. The
  clause would then rest on a file that outlives the run rather than on one in
  reach, which is a different sentence.
- A session gets the extended clause and writes into the document root anyway.
  That is step 4 and a rewrite, not a placement.
- The next report of this comes from a session that never asked where a file
  goes. The rule would then have to arrive from the introspection question,
  which is `003933`'s route rather than this one's.

## Since then

The lookup happened and the first **Wrong if** is what it found. The file was at
the project root and never served, because the container serves the docroot
below it. So the clause rests on both reasons rather than on the served one
alone, and `D-KNW-045` carries the sentence that landed. Two of the three
measures had also gone stale with a move between files. What still reached
nothing was the feedback's own query, which is what this entry is about.
