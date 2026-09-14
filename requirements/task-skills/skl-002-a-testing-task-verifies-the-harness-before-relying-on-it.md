---
id: R-SKL-002
title: 'A testing task verifies the harness before relying on it'
status: held
heldBy:
  - InstallerTest
  - SkillTest
---

# R-SKL-002 — A testing task verifies the harness before relying on it

**A project or extension test task verifies the harness for the behavior's
required layer before it relies on it.**

The session establishes or repairs absent or broken infrastructure when changes
are in scope. Then it adds or extends the requested coverage and replaces no
working tests and commands. Unit and functional harnesses stay with the
extension; browser harnesses stay with the runnable project. Every new layer has
a meaningful local proof before CI calls the same command. Review-only work
reports setup defects and does not change them.

Static analysis and coding standards are a layer of this workflow. The session
establishes them when the task asks for them, whether or not the project already
runs one. It reads what the project lacks off what a complete check surface
covers. Each check gets one project-owned command, and the command that reports
stays apart from the one that writes. The session fixes a new finding rather
than records it in a baseline, and automatic format changes stay inside the
first-party files. Where the check is new to a repository that does not yet pass
it, the conformance commits come first. The commit that adds the check comes
last. A run at the new HEAD verifies it, so no commit fails the check it
introduces.

## From

`EXT-05`, `SITE-06`, `SKILL-05`, `SKILL-06`, `SKILL-08`. A request for one test
skill that can add or extend PHPUnit and Playwright coverage while it checks and
repairs its setup (2026-07-30). Two recorded `REVIEW-02` runs in which an absent
static-quality workflow surfaced as an absent test workflow, which this entry
declined (2026-07-30). The commit order came from `feedback/2026-08-04-055741`.
A session established a fixer and an editorconfig check in
`/home/benji/projects/ext-guidedtour`. It found the page said to split the
format pass off and nothing about which half goes first. It worked the order out
itself, and reported that anyone who lands a first check on a non-conformant
repository has to.
