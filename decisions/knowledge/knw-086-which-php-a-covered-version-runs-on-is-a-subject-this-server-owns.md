---
id: D-KNW-086
title: 'Which PHP a covered version runs on is a subject this server owns'
date: 2026-08-18
status: open
---

# D-KNW-086 — Which PHP a covered version runs on is a subject this server owns

**The corpus states, per covered TYPO3 version, the PHP floor the core requires
and the versions its test script accepts. It does so before there is an
installation to read.**

A session chooses the interpreter a project runs on when it declares the
environment, which is before there is an installation to describe. At that
moment every PHP answer this server has is unavailable, and nothing below
`knowledge/` carries a version number to put in its place.

## Evidence

- **Nothing below `knowledge/` states a PHP version.** A grep for `8.1`, `8.2`,
  `8.3` and `8.4` over every JSON and markdown file there returns nothing on
  2026-08-18. That grep keeps to the lines that name PHP at all.
  `bin/cli hints:probe "which PHP versions does TYPO3 14 support, what php_version to set in ddev config"`
  selects the `php` domain. It returns `project-configuration-files` alone,
  which states none.
- **The manual defers the numbers.** `typo3_documentation_lookup` at 14.3 for
  "system requirements supported PHP versions" returns the System Requirements
  page of `reference-coreapi` at 26% coverage. That page's own summary reads
  "For current and detailed requirements, including concrete versions, visit:".
  The versions are on a page the indexed manual does not carry.
- **The numbers are readable in this repository.**
  `.checkouts/<branch>/composer.json` and `typo3/sysext/core/composer.json`
  require `^8.1` on 12.4, `^8.2` on 13.4 and on 14.3, and `^8.5` on main.
  `config.platform.php` pins `8.1.1`, `8.2.0`, `8.2.0` and `8.5.0`. 14.3's
  `Build/Scripts/runTests.sh` takes `-p 8.2|8.3|8.4|8.5|8.6` and defaults to
  8.2, so the floor and the range the core tests itself over are both in the
  checkout.
- **The moment has no answer at all.** `skills/typo3-development-installation`
  decides the container's project type and document root under "Declare the
  container" and says nothing about its interpreter. `typo3_project_describe`
  answers "no installation" until the install has run.
  `feedback/2026-08-17-211157` wrote `php_version: 8.4` there. It reports that
  by the time the three PHP numbers were available the container had long stood
  built. The number no longer looked like a decision.

## Decided

- **Step 1a, taken on, queued.** The statement is a corpus change and its
  reading is a `.checkouts/` read, so it is not made in the run that judged it.
- **The corpus states the floor and the tested range, and names them as what
  they are.** The floor is what the core's manifest requires. The range is what
  `runTests.sh` accepts, which is the core testing itself and not a support
  statement. TYPO3's supported-version page is outside the indexed manual, and a
  range labelled "supported" from this read would claim more than the read
  covered.
- **Bound as data.** The numbers differ per major, so `since` and `until` carry
  it and no sentence names a version — `D-VER-004`, and
  `documentation/server/versions.rst`.
- **Where it lands is the todo's.** `project-configuration-files` is the hint
  the probe reaches and it is in another session's hands right now
  (`todo/progress/2026-08-17-205850`). So a second statement written into that
  file collides. Whether this is a statement there, a hint of its own, or a line
  in the installation skill waits on both reads.
- **Priority `normal`, set here.** One session reported it, so not `high`; it is
  the moment the number is cheap to change and the only one, so not `low`.

## Assumed

- **That a floor and a tested range answer what the moment asks.** What to write
  into `.ddev/config.yaml` is a choice inside that range, and the corpus states
  the range rather than makes the choice.
- **That the core maintains `runTests.sh`'s `-p` list with its branch.** This
  entry reads it as evidence of what that branch runs on. Nothing here confirms
  that a value arrives when a PHP release lands.

## Wrong if

- The statement lands and a session still writes an interpreter nobody chose,
  because the choice happens before any lookup. Then the gap was the route
  rather than the corpus, and the sentence belongs in the installation skill's
  "Declare the container" step.
- A `-p` value arrives on a maintained branch after release and the corpus
  states a ceiling one release old. `bin/cli checkouts:update` re-reads the
  branch. Nothing compares it against what the corpus states.

## Since then

It landed as a hint of its own, `php-versions`, and as a routing line in the
installation skill's "Declare the container" step. Both, because the two **Wrong
if** above are the two halves. The corpus carries the numbers and the step is
what makes anybody ask for them before the session writes the container.
`project-configuration-files` was the third option and the session refused it
twice over. Its subject is what configures the installation rather than what the
container runs. It was in another session's hands, so a statement written there
would have collided in the file. `R-KNW-072` is what holds the answer.
`D-KNW-091` is the guard the numbers needed loosened, since it refused every
`<major>.<minor>` in a hint.
