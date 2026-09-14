---
id: R-COD-004
title: "The versions this repository pins are checked against the day's release"
status: held
judged: 2026-08-29
---

# R-COD-004 — The versions this repository pins are checked against the day's release

**A session checks a version this repository pins against the current release on
the day it touches the file.**

That is node, a GitHub Action, a Composer library, a DDEV configuration.

Nothing here names the version a caller has to use. The current release moves.
So a target written into a rule or a check is right for a quarter and wrong
afterwards, and nothing fails on it. What decides is what the session reads on
the day.

The session raises a pin behind the current release, or writes the reason it
stays beside it. Where a runtime this package declares rules the newest release
out, the pin takes the newest version that declaration allows. That is the
reason and the target at once, never a licence to leave it where it was. Where
the constraint can carry both, it carries both and the resolver picks per
version, which is what `D-COD-008` measured on PHPUnit. The other reasons are a
runtime this repository still supports and a library whose next line needs a
change. A version the render or the suite breaks on is one too. Where nobody has
established that reason, the session asks for the raise rather than takes it. A
toolchain bump is a change the maintainer runs the risk of.

The same demand on what this server tells a caller is `R-ANS-037`. The same
demand on what a published skill does with a project it reads is `R-SKL-029`.

## From

The maintainer's instruction of 2026-08-29. At the time `documentation.yml`
pinned `actions/checkout@v4`, `actions/setup-node@v4` and node 24, and `ci.yml`
`actions/checkout@v4` beside a PHP matrix. Each of those was current on the day
somebody wrote it, and `D-DOC-019` recorded the node line as the active LTS.
Nothing here said when a session reads any of them again.

## Held by

- It is **not guarded**, and no test can hold it. An assertion that names the
  version to pin is the fixed target this forbids. One that reads the network
  fails on the day a release lands rather than on a defect.
- What stands in for a guard is the rule in `AGENTS.md` and how few files carry
  a pin at all. Those are `.github/workflows/`, `composer.json`, and a
  `.ddev/config.yaml` where a checkout has one.
