---
id: D-SKL-053
title: An absence in the extension answer names the skill that owns it
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor
---

# D-SKL-053 — An absence in the extension answer names the skill that owns it

**`typo3_extension_describe` names the skill that owns each artifact it reports
absent.**

A session read `manual: null`, `readme: null` and `tests: []` twice. It wrote
three README files by hand, shipped no test at all and handed the package over
unaudited. The closing sentence of the skill it followed named the three skills
that own those absences, and it says it read that sentence.

## Evidence

- `feedback/2026-08-17-213027`. A v14 demo site built as a sitepackage plus a
  distribution extension on 14.3.6, `/home/benji/projects/site-demo`,
  `claude-opus-5`. It followed `typo3-content-element-development` to
  completion, quotes that skill's closing sentence, and reports that none of
  `typo3-extension-testing`, `typo3-extension-documentation` and
  `typo3-extension-conformance` activated. The user then reviewed by hand and
  listed ten defects, seven of them inside the conformance skill's own stated
  scope.
- **The sentence is unchanged.** Read in this checkout on 2026-08-18, the last
  paragraph of `skills/typo3-content-element-development/SKILL.md` carries the
  quote word for word. It is an imperative, "Activate". It names three
  successors at once, it sits after the commit section, and it names no moment
  at which any of the three happens.
- **What the answer carries today.** `ExtensionDescribe::answer()` renders one
  `Ships:` line on every hit. That is
  `manual none, readme none, tests none, language files none` where an extension
  has none of them, and it names no skill. Below `src/Tool/` only `GerritLookup`
  and `FeedbackRecord` name one at all.
- **No extension-side crossing stands at its moment.** Read across `skills/` on
  2026-08-18: every crossing out of the seven extension workflows stands in the
  closing ownership paragraph. Three of them carry a trigger clause:
  `typo3-backend-module-development`'s "before editing documentation" and
  "before changing test infrastructure", `typo3-extension-cleanup`'s numbered
  step 2. None is a step at the point the crossing happens, which is the form
  the two confirmed core crossings took.
- **That is the read this report falsifies.** `D-SKL-022`'s pass of 2026-08-09
  counted `Activate typo3-extension-documentation` and
  `Activate typo3-extension-conformance` as already an act and left them out of
  the work. That pass applied `R-SKL-018` to three core crossings and to none of
  these. A session with exactly that imperative in hand that crossed nothing
  says the imperative is not the property. What the two crossings that fired
  carry beside it is the moment.
- **The answer side has decided prior art.** `D-SKL-038` puts the workflow a
  caller has begun into the answer of the tool it does call, at a moment a
  description cannot have. `D-ANS-061` is why that channel rather than the one
  nobody invokes. `SkillTest::everySkillNamedByAToolIsPublished` already holds
  every name below `src/Tool/` to what the installer publishes.
- **The corpus is one build.** `bin/cli feedback:list` on 2026-08-18 reports 8
  open, all from `/home/benji/projects/site-demo` and all from that session.
  Three siblings of the same debrief already have their judgement. `D-SKL-050`
  names this card as what failed to deliver `typo3-development-installation`.
  `D-KNW-087` is the same mechanism one layer down in a hint's closing
  neighbour. `D-SKL-049` is the terminal gate, proposed, and it waits on the
  maintainer in `todo/waiting/2026-08-17-212218`.
- **Another project missed the testing skill before.**
  `feedback/archive/2026-08-01-003533`, `/home/benji/projects/site-new`, another
  model: rendered output verified by a curl of the HTML, no browser test, a
  Playwright harness in place and unused. That session had no skill active to
  cross out of, so it is a second arrival at the absence and not at this
  sentence.

## Decided

- **Step 2 for the answer and step 4 for the crossing, and both go to the
  queue.** A rendered answer and a published skill body are contracts, and
  `documentation/records/judging.rst` puts either on the todo side of the spot.
- **The three absences name their owner: `typo3-extension-documentation` where
  `manual` or `readme` is null, `typo3-extension-testing` where `tests` is
  empty.** Only where the artifact is absent, so an extension that ships all
  three reads as it does today. The name arrives on the object the caller
  already looks at rather than in a sentence it leaves behind.
- **Not conformance.** No field of this answer reports that nobody audited the
  package, so there is no absence for it to hang on. That crossing stays with
  the skills half.
- **Not a sweep of the other tools.** `D-SKL-038`'s bullet stands. This is a
  second named moment with its own report, and the row nobody asked for is still
  what a route invented for symmetry costs.
- **The crossing half is `R-SKL-018` applied where it never was, and bounded to
  one crossing at its own moment.** What may not come from here is a list of
  everything the workflow still owes. That is `D-SKL-049`'s gate, a proposal
  rather than a decision, and a card that writes one now would answer the
  maintainer's question with a build.
- **Nothing about the descriptions or the routing.** The listing arrived and one
  of these skills was active when the crossing failed, so this is a crossing and
  not a selection. `D-SKL-033` weighed the wording and this adds no session to
  that side.
- **Priority `normal` on both cards.** The cost has a measure and it is large:
  no test written, three manuals by hand, an unaudited delivery. It is one
  session, which is what keeps it off `high`, the same read `D-KNW-087` made of
  the sibling report.
- **The feedback stays open behind both cards**, and whichever lands second
  archives it.

## Assumed

- That a session given a skill's name beside an absence loads it. Nothing here
  measures that. It is `D-SKL-038`'s first **Assumed** unchanged, and nobody has
  read the tail that entry wrote yet.
- That the moment is the property the imperative lacks. Two core crossings carry
  both and fired, this one carries the imperative alone and did not — one
  session, one model, one task.
- That a later report says which channel carried it. Two levers land against one
  report and nothing else separates them. What the corpus does have is sessions
  that quote the sentence or the field they acted on, this one included.

## Wrong if

- A session reports that it read `tests none` with `typo3-extension-testing`
  beside it and wrote no test. Then the answer side is not the lever either, and
  what remains is the gate `D-SKL-049` waits on.
- A session that asked a narrow question of `typo3_extension_describe` reports
  the names as noise on an answer about registrations. Then the condition has to
  be narrower than an absent artifact, which is `D-SKL-038`'s second **Wrong
  if** by way of this entry.
- The extension crossings are rewritten at their moments and a session crosses
  none of them anyway. Then prose does not hold a crossing at all, and
  `D-SKL-022`'s third **Wrong if**, the lever is in the tools, is what remains.
- A session activates one of the three owners off the closing paragraph as it
  stands. Then this run read one session's momentum as a property of the
  sentence.

## Since then

A second session read a closing crossing and did not act on it, which is what
the **Assumed** says a later session has to separate. The pass read the seven
extension workflows, and both halves of the guard pass on what it left in place.
The crossings map names them, and the crossing that had been prose is a step
now.
