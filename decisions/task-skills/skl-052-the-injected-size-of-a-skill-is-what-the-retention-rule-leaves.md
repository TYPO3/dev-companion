---
id: D-SKL-052
title: The injected size of a skill is what the retention rule leaves
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns
---

# D-SKL-052 — The injected size of a skill is what the retention rule leaves

**What holds a workflow small is the rule that it retains no fact a tool owns,
so the injected size is measured rather than budgeted.**

A session measured the two skills that carried its build and asked that the
number be watched. A number would be watched and traded against; what produced
it is a rule, and that rule is guarded on two of the four kinds it names.

## Evidence

- `feedback/2026-08-17-212600` measured the copies installed in
  `/home/benji/projects/site-demo`: 10,605 characters for
  `typo3-development-installation` and 4,645 for
  `typo3-content-element-development`, 4,235 tokens together at the 3.6
  characters to the token its own figures convert at. It asks that the size be
  treated as a number worth watching, and names the drift it fears: the two
  workflows it filed as missing being solved by growing an existing skill.
- The repository's copies at the minute it was written, `845c0c6`, were 11,096
  and 5,028 characters. The installed copies were older than the server, which
  is the state `writing-a-skill.rst` publishes into and nothing reports.
- Both grew in the day after the report, and every character of it is routing.
  Six commits — `7dab8ef8`, `d884d1e7`, `4ec22687`, `9fd46a2f`, `73cff0ab`,
  `c6cf1456` — took the same debrief's costs on, and each one names a call: the
  two hint ids at step 5 with what each alone answers, `typo3_component_lookup`
  before the preview markup, two `documentId`s at the browser step,
  `id=php-versions` at the container step, and a section saying a symptom is a
  lookup trigger. None of them states the PHP version, the variable set, the
  package or the custom property those calls answer with. `73cff0ab` removed a
  step. Today the two are 11,604 and 6,469 characters, 1,949 more than at the
  report and about 5,020 tokens.
- The sentence the report quotes is in `typo3-development-installation`
  verbatim: keep the skill as routing and workflow,
  `never retain layout keys, environment defaults, command options or package names`.
  What held it was the phrase `Keep this skill as routing`, a `TYPO3 v\d+` regex
  and a `<core:` regex, so of the four kinds it enumerates, none was asserted.
- Two of the four have a shape a file can be held to, and both already hold. No
  `SKILL.md` names an environment variable, and none names a package —
  `typo3/testing-framework` and `typo3/coding-standards` stand in
  `typo3-extension-testing`'s references, which is where `writing-a-skill.rst`
  says a package name belongs. Nothing asserted either.

## Decided

- The keep-request is answered in the only form this repository has for one, and
  it is put on the property rather than on the sentence.
  `SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns` now asserts that no
  `SKILL.md` names an environment variable or a package. It reads the body
  alone, because a reference is where either may be written once.
- The other two kinds stay the author's. A layout key and a command option are
  words with no shape, and two skills carry `--no-commit` and `--diff-filter`
  legitimately — git releases on a cycle no tool here answers for.
- The ask for a size number is refused. Every one of the six edits above would
  have been argued against a budget, and each of them answered a cost this
  server was reported for. What a budget would catch is a body growing on
  retained facts, and the retention rule catches that at the sentence that
  causes it.
- Nothing is queued. The drift the report fears is a real one and it did not
  happen here: the distribution workflow it filed became `D-SKL-050`, a skill of
  its own, rather than a section in the installation skill.

## Assumed

- That the reported figures are the installed copies rather than a different
  measurement. They are smaller than the repository's own files at that minute,
  which is what an install made earlier looks like, and this run did not read
  that project.
- That one day of edits says what the rule does. Six commits from one debrief is
  the widest window this observation has, and a workflow can grow on routing for
  a long time before anybody reads it as too long.

## Wrong if

- A skill stops being read in full while still retaining nothing. Size would
  then need a number after all, and this entry would have refused the one thing
  that could have said when.
- A routed pointer goes stale the way a retained fact does — a `documentId` or a
  hint id that no longer resolves is a fact in another coat, and the rule would
  then have moved the staleness rather than removed it.
- Either regex fires on a sentence that should stay. A skill naming the one
  variable a reader exports before anything else works would be refused by a
  guard written for the set of them.
- The drift arrives after all: a domain wide enough for its own skill folded
  into an existing one because adding a section is cheaper than publishing a
  file. `D-SKL-050` is the counter-example today and one case is not the rule.

## Since then

Measured on 2026-09-01 against `typo3-development-installation`: the three
branches its dispatch chooses between are 11,638 of its 21,280 characters, and a
task takes one. None of it is retained fact, so the rule above holds and what
the size is made of is routing that forked.

Taking a branch into a reference is blocked by a rule rather than by the
writing. `typo3_documentation_lookup` stands only in the create branch, so a
reference carrying it takes a routed call out of `SKILL.md` and
`SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder` fails — seen
by moving that branch out and running it. What would be settled first is whether
a routed call may stand in a reference.
