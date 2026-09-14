---
id: D-KNW-055
title: 'The first check a standalone extension gets is a subject this server owns'
date: 2026-08-04
status: open
coveredBy:
  - HintsTest::aCodeStyleFixerTaskIsRoutedToTheSkillThatOwnsIt
  - HintsTest::theFixerHalfOfTheStaticQualityLayerIsStatedAndReachable
  - ProjectTest::anEnvironmentThatIsNotDdevIsSaidToBeUnread
  - ProjectTest::theAnswerSaysWhatRunsTheProject
  - ProjectTest::theFloorTheInstalledCoreDeclaresIsBesideTheProjectsOwn
  - ProjectTest::whatAnExtensionDoesNotShipIsAnswered
---

# D-KNW-055 — The first check a standalone extension gets is a subject this server owns

**The corpus states how a session sets up an extension's coding standards. It
stands beside the static-analysis hint that is the only half of that layer it
holds today.**

Six feedback, one session, one task: "add a code style fixer to a standalone
TYPO3 14.3 extension repository". Three of them are the corpus gap seen from
three sides. No statement on the subject, no route to the subject, and no way to
learn from a near-miss answer that a neighbour subject exists. The fourth is the
number the task ended on, which the first call of every workflow reports
everything but. The last two report what worked, and what they mark is the same
boundary the fourth one costs at.

The directory is the account no single card carries. `ext-guidedtour` holds
eight of the ten open feedback. The earlier cluster is a standalone extension
repository that gets the installation it had none of
([`D-SKL-012`](../task-skills/skl-012-bringing-a-packages-development-installation-into-existence-earns-a-task-skill.md)).
This one is the same repository that gets the first check it had none of. The
domain is a package with no infrastructure at all, and this server answers its
analyser and nothing else in that layer.

## Evidence

- **One session, six cards, four minutes.** `bin/cli feedback:list` on
  2026-08-04 reports 10 open in three directories, 8 of them in
  `/home/benji/projects/ext-guidedtour`. The six judged here are `055420`,
  `055626`, `055638`, `055658`, `055715` and `055741`. All are
  `claude-opus-5[1m]`, all name the same task, and this entry read all of them
  whole. One session is what they weigh, and it is why nothing here is `high`.
- **Nothing below `knowledge/` states the subject.**
  `bin/cli hints:probe "coding standards php-cs-fixer setup for an extension"`,
  the feedback's own query, returns `extension-repository-layout` alone. That is
  on text at 167 and no `appliesTo` needle. `"php-cs-fixer"` reaches nothing out
  of 81 candidates, `"code style fixer extension"` returns `public-assets` on
  text, and `"editorconfig"` returns six hints whose strongest match is 26. A
  grep over `knowledge/` for the packages finds them in
  `catalog/reference/entries.json` and in the core-scripts document, neither of
  which is an answer about an extension.
- **The one hint that exists is the analyser's.** This entry read
  `extension-static-analysis` in `knowledge/hints/testing.json` whole. Six
  statements, all PHPStan: the configuration's place, `tmpDir`, `bootstrapFiles`
  for `LF` and `CR`, the `ext_emconf.php` exclusion, level 5, the baseline. Its
  `appliesTo` needles are `phpstan`, `phpstan.neon`, `static analysis`,
  `analyse`, `baseline`, `phpstan-baseline`, `tmpDir`, `result cache`,
  `set up static analysis`, `level`. Not one of them is a word a caller who asks
  for a fixer would use.
- **The skill routes to a corpus that answers half of what it names.**
  `skills/typo3-extension-testing/references/static-quality.md` names
  `friendsofphp/php-cs-fixer` driven by `typo3/coding-standards` and
  `editorconfig-checker` under *Coding standards*. Under *Static analysis* it
  hands the configuration question over. "`typo3_hint_lookup` with
  `id=extension-static-analysis` answers where the file belongs … ask it rather
  than recalling a configuration from another project". There is no such
  sentence for the fixer, because there is no such id.
- **The guide recognizes the task as nothing.** Re-run through
  `bin/typo3-dev-companion` from this worktree on 2026-08-04, with the session's
  task and its paths at `targetVersion: 14.3`. `intents` is `[]`, `skills` is
  `[]`, the checks are `runTests.sh` suites, and the checklist is the core patch
  shape. `knowledge/task-intents.json` carries no entry this task's words reach.
  The nearest, `tests`, matches `test`, `coverage` and `regression`. So the
  guide cannot name the skill that owns the work, which is what `D-SKL-013` put
  it there to do.
- **The scope states one half of the layer and is silent on the other.**
  `knowledge/server-scope.json` lists "setting up static analysis for an
  extension" under `covers`, and no line of `doesNotCover` mentions coding
  standards, a fixer or repository tooling. `055715` reports that it called
  neither `typo3_server_scope` nor `typo3_task_guide` and paid a `hint_lookup`
  to find the coverage out. Had it called them, neither would have said.
- **The near-miss reproduces exactly.** Both calls of `055626` re-run through
  `bin/typo3-dev-companion` on 2026-08-04. The topical query returns
  `extension-manifest`, `extension-repository-layout` and `extension-boot-files`
  with `availableHints` empty. `id=extension-static-analysis` returns that hint
  with `availableHints` empty. `HintLookup` documents the index on the `id`
  parameter, "Every answer that returns no hint lists the ids there are".
  `MatchedHints` fills it on a miss alone. So the answer that is wrong in the
  most ordinary way is the one that withholds the means to correct itself. The
  session reached the neighbour id only because a skill file names it in prose.
- **The PHP floor claim holds, and the number is not derivable from the major.**
  `.checkouts/14.3/typo3/sysext/core/composer.json` requires `"php": "^8.2"`,
  and so does the mono-repo root; `13.4` is `^8.2`, `12.4` is `^8.1`, `main` is
  `^8.5`. The file the session actually read is the installed one, and
  `.environments/e-site-14.3/vendor/typo3/cms-core/composer.json` says `^8.2`
  there too. `Instance::typo3Version()` already locates that package directory
  to read `Classes/Information/Typo3Version.php` out of it. So the floor is one
  further read at a path the server has resolved.
- **The strength and the cost are the same boundary.** `055658` credits
  `typo3_project_describe` for two things. The environment block that names the
  container's interpreter against the caller's shell, which sent every command
  through DDEV. The explicit empty answers, `commands: []`,
  `artifacts.tests: []`, which turned "add a fixer" into "establish the first
  check". Both are already what must hold: `R-PRJ-008` holds the first with
  three `ProjectTest` methods, `R-PRJ-006` the second with one. Twelve seconds
  earlier, from the same debrief, `055638` reports the one number that same call
  does not carry. That is
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)'s
  shape: the strength says where the boundary runs and the cost says where it
  stops.
- **The skill's own gap is one sentence wide.** `static-quality.md` says "Keep a
  formatting pass in its own commit, apart from behavioural change" and says
  nothing about the order. `055741` is the case that needs it. A
  `ci:editorconfig` added onto a tree whose XLF files still hold tabs produces a
  first commit that fails the check it adds. The session inverted the split and
  verified it by a run of the checks at the new HEAD.
- **The package a writer would read the corpus from is in no checkout.** Neither
  the core mono repo nor any of the four environments below `.environments/`
  requires `typo3/coding-standards`. The mono repo carries its own
  `Build/php-cs-fixer/` and requires `friendsofphp/php-cs-fixer` directly. So
  the research this gap needs is not a `.checkouts/` read. That is the todo's
  first step rather than a copy of the feedback's account.

## Decided

- **Step 1a on the subject, taken on.** Nothing states it. The skill that routes
  here names the packages and hands the configuration question to a corpus that
  has none for them. The session that reported read the answers out of an
  installed vendor tree. What the writer writes is what the research establishes
  about `typo3/coding-standards`, not the feedback's summary of it.
- **The route is part of the same work rather than a second judgement.** The
  session's own sentence is "the vocabulary gap and the coverage gap were the
  same gap". A corpus that spells the layer "static analysis" and covers only
  PHPStan under it is out of reach of every word a fixer task uses. The intent
  in `knowledge/task-intents.json`, the `covers` line, and the hint are one
  change.
- **Step 2 for the near-miss index, queued rather than closed on the spot.** The
  ids exist, the path that already runs computes them, and the answer withholds
  them exactly where a caller needs them. It is not step 4. The parameter
  description is accurate about today's behaviour, and a reword would document
  the dead end rather than remove it. It touches `src/` and what every answer of
  a tool carries, which is the line
  [judging.md](../../documentation/records/judging.rst) draws around the spot.
- **Step 1b for the PHP floor.** The answer is available here, one file at a
  path `Instance` already resolves. There is no way to get it in the form the
  task needed. What it prevents is silent. A package that declares the
  container's `^8.4` against a core that requires `^8.2` narrows its own range
  by two minors with every check still green.
- **This change archives `055658`.** It reports no gap, so the ladder has no
  rung for it. What it carries stands above as the boundary, and both fields it
  asks to keep are already requirements tests hold. It is not a **Confirmed on**
  anywhere: an account of a run confirms nothing, which is what `D-FBK-018`
  settled.
- **Four todos for six cards, and the six cards go.** The corpus, the index, the
  field and the skill are four rungs with four reads. One card for all four
  would queue a paragraph nobody can finish. Two todos serve `055715` because
  its two halves are two of them, and `055420` and `055741` name the todo that
  took their card over.
- **Priority `normal` on three of them, and one session is what holds it
  there.** `low` is where a card arrives, and all three of these arrived with
  the cost already counted. A layer read out of a vendor tree, a corpus
  neighbour in reach only through a skill file. A number that every check passes
  while it is wrong. `high` would say several sessions, and one session reported
  these.
- **The skill card stays `low`, decided rather than defaulted.** Its workable
  half is one paragraph in a reference file, and its other half is a question
  nobody here can answer. So it does not go before the three above.
- **The question about `base.md`'s order goes up rather than to a decision
  here.** Both `055715` and `055741` ask whether a session may shorten the fixed
  five-step order where a skill has already routed the task. That is a wish
  rather than what holds, and `R-SKL-005` is what a change to it would touch.
  The skill todo carries it in the words the feedback asked it in.

## Assumed

- **That `typo3/coding-standards` is the rule set an extension uses.** This
  repository's own skill and the session that reported name it that way, and
  nothing this run read here does. The package is in no checkout and in no
  environment. If the research finds the practice has moved on, the writer
  writes the hint for what it finds.
- **That the corpus is the place and not the skill.** `static-quality.md`
  already names the packages and leaves the configuration to a lookup on
  purpose, which is the split `extension-static-analysis` stands on. A second
  copy in the skill would be the thing that page avoided for the analyser.
- **That an index of the same domains is short enough to attach to every
  answer.** `bin/cli hints:coverage` counts 127 hints over the whole corpus, and
  an answer already limits itself to the domains it matched in. So the list a
  `php` query would carry is a fraction of that. Nothing has measured what it
  costs a caller to read.
- **That the four cards this session's task shape produced are one domain rather
  than four coincidences.** This entry reads them together because one session
  hit them in four minutes on one task. A different session that hit one of them
  alone would have got a judgement as one card.

## Wrong if

- The hint lands and the next session still reaches it only because a skill file
  names its id. Then the gap was the matcher and the vocabulary rather than the
  corpus, and the statement went into a place nobody arrives at.
- The research finds `typo3/coding-standards` does not ship what the session
  describes: the two rule sets, the two setup types, the excludes as literal
  directory names. Then this entry queued a statement about a package from an
  account of it. That is the failure the ladder's steps 2 to 4 exist against.
- `corePhpConstraint` lands and a session still declares the container's PHP as
  the supported minimum. Then the absent half was never the number but where the
  workflow says to look for it.
- Every answer carries `availableHints` and callers stop their read of them, or
  the list is long enough that an answer is worse with it. Then the index was an
  affordance of the empty case and should have stayed there, and what the
  near-miss needed was a miss threshold instead.
- The answer about `base.md` comes back "the order stays fixed" and the next
  session on a tool task skips steps 3 and 5 again. Then the session skips the
  prescription rather than its wording, and `R-SKL-005` is the entry that has to
  move.

## Since then

On 2026-08-11, the fourth **Wrong if** fired. The measurement disproves the
remedy it names rather than confirms it.

## Since then

On 2026-08-18, the third **Wrong if** fired, in a variant it did not name.
`feedback/2026-08-17-211157` is a session that read `corePhpConstraint: ^8.2` in
the answer and declared `^8.3`. That is neither the core's floor nor the
container's 8.4. It is a third number above the one it could have declared and
below the only interpreter it ever ran. The failure the entry described is a
wrong floor. What it is, is a floor with no relation to anything. So the premise
holds, the number alone did not take. The remedy the **Wrong if** names, a
workflow that says where to look, is not what the session decided.
[`D-ANS-082`](../answers/ans-082-the-project-answer-states-how-its-three-php-numbers-relate.md)
carries it instead. The one place that holds all three values states how they
stand to each other. The comparison is the work, and a caller charged one
context per call should not pay a round trip for it.

## Since then

A session did the corpus research out of an installed `typo3/coding-standards`,
since the package is in no checkout here. What does not hold is the trap it
drew, which is what made it rewrite the finder. The finder matches the excluded
names at any depth and excludes the vendor directory before it reaches the
configuration. So the exclusion covers a package whose vendor sits under a dot
directory twice over. What does need a correction is a build directory that is
neither hidden nor one of those names.

A second feedback's premise holds and its conclusion does not. The hint those
six calls went around ranks seventh and the limit cut it. In the index it stands
near the end, because the index re-reads the corpus by file. The other id it
names is a different case: nothing in the query's words reaches it. What goes up
is which of the two the index becomes.
