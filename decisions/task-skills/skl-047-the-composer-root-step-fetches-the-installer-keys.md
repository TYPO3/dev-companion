---
id: D-SKL-047
title: 'The Composer root step fetches the installer keys'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::anInstallationIsBuiltInDependencyOrder
---

# D-SKL-047 — The Composer root step fetches the installer keys

**Step 1 of `typo3-development-installation` routes to `typo3_hint_lookup` with
`id=extension-repository-installation` for the `extra` block. It no longer
routes to the manual and to a package nobody has installed yet.**

The step names two sources and neither answers at the moment a session reads it.
The indexed manual defers the keys, and the installed installer package is what
the manifest under construction is there to install.

## Evidence

- `feedback/2026-08-17-212152` is a v14 demo build that wrote the root
  `composer.json`, `.ddev/config.yaml` and `.gitignore` in its first fifteen
  minutes, before any install. It traces two of a reviewer's ten findings and
  one filed gap to those minutes. It asked `typo3_documentation_lookup` twice,
  read no page with `page`, and reports that neither answer changed a decision.
- **The step's second source cannot exist when the step runs.** Step 1 says to
  "read the installed installer package where the documentation is thinner than
  the question". The package that installs TYPO3 beneath the repository is what
  the manifest under construction has to pull in. Nothing gets installed until
  step 3.
- **The answer is in the corpus.** `extension-repository-installation` states
  `extra.typo3/cms.web-dir` and where the installer writes `index.php`. It
  states `config.vendor-dir` and `config.bin-dir` with the console path they
  produce. It states the message the installer accepts and then ignores
  `app-dir` and `root-dir` with, and the reset that follows a `web-dir` outside
  the Composer root. It states the `typo3/cms-cli` constraint that cannot
  resolve and names `typo3/cms-core` as the conflict.
- **It is reachable and routed from nowhere.**
  `bin/cli hints:probe "extra block the TYPO3 Composer installer reads to install TYPO3 beneath the package"`
  returns that hint alone at `text only(405)` on 2026-08-18. A search of
  `skills/` for its id returns nothing on the same day. Steps 2 and 5 of the
  same file name `php-versions`, `project-configuration-files` and
  `project-build-and-scripts`.
- **The step keeps a paraphrase where the id would go.** Its "three properties
  of that step survive any version" are three statements of that hint. A layout
  key that warns rather than errors is `app-dir`'s message. The package the core
  requires itself is `typo3/cms-cli`. The empty extension directory below the
  document root is the hint's closing statement. The skill opens with a ban on
  retained keys and package names, and this is the one step that retains both.
- **The other two facts the report names have landed.** `D-KNW-086` put the
  interpreter into `php-versions` and into step 2. `D-KNW-088` put what a
  Composer installation generates onto `project-build-and-scripts` and
  `public-assets`, which step 5 already names.
- **Convergence is not what carries this.** `bin/cli feedback:list` reports 13
  open on 2026-08-18, all from one directory and one debrief, so the evidence is
  the checkout rather than a second session.

## Decided

- **Step 3 of the ladder, routing.** The right answer exists, is reachable by
  the query the moment produces, and the step that asks the question points
  somewhere else.
- **Queued rather than closed on the spot.** A skill lands in somebody else's
  project, so its contract gets a review rather than an improvisation.
  `documentation/records/judging.rst` puts it on the todo side of that line.
- **The feedback shrinks to this half.** The interpreter and the generated paths
  have their answer, and the report says the second got a card of its own.
- **Rejected: a `no-installation` answer that says what a caller can still
  ask.** `Result\Unsupported` is one class for every tool that reads an
  installation. So a route there attaches to every unanswerable answer rather
  than to the moment before an install. `D-ANS-083` took the one pointer that
  answer carried back out. `D-ANS-061` decided on three sessions that a tool
  named in an answer is not the lever. What owns the moment is the workflow
  step, which is where `R-KNW-072` put the interpreter and where this puts the
  keys.
- **Not a new hint, and not a document.** The gap was a route to one statement,
  not a statement and not an order of steps.
- **Priority `normal`.** One session reported it, so not `high`. A session
  writes the file it informs once and never revises it, so not `low`.

## Assumed

- That the hint's `extension` scope is the shape this step has. The step makes
  the package's own manifest the Composer root, which is that hint's subject. A
  project whose root is not a package is the other branch of the skill.
- That the paraphrase can come out whole. This entry reads each of the three
  properties as a restatement of a statement in the hint. It reads that from the
  two texts and not from a run that fetched both.
- That what the session asked `typo3_documentation_lookup` is what its report
  says. Nothing here reproduces those two calls.

## Wrong if

- The routing lands and a session still writes the `extra` block from memory.
  Then the id was reachable and unread, which is delivery rather than routing,
  and the answer belongs in the step's own prose.
- The hint turns out to answer for an extension repository and not for the
  sitepackage-plus-distribution shape the report built. So the step routes to a
  statement that is true beside the question. Then the corpus owes a second hint
  and this is step 1a.
- A session follows the id and finds the manual carried something the hint does
  not. Then the step's first source did work and only its second was circular.

## Since then

The manual half came out with the circular half, because a measure rather than
an assumption settled the third **Wrong if**. Three queries about the
installer's block return six page titles whose best carries 37% of the query's
weight. The one page whose title covers the question documents the project shape
and contains none of the keys. A query for them by name returns nothing, because
that index is page titles and no page has a key as its title.

Two things turned up beside the step and stay as they are. The manifest's
extension metadata, which another hint owns, and Composer's own plugin
configuration, which nothing here covers. So the step says so rather than points
at a source that would not answer.
