# Targeted contract cases

These cases name a concrete task or failure mode so a test can hold one behavior
still. The route to a backend-module workflow, a test harness that survives, a
version-bound component withheld, or verified behavior handed from one skill to
another.

They are intentionally more specific than a forward review. They answer “does
this known task shape still receive the required workflow?” They do not answer
“can an agent inspect an unfamiliar repository and decide what matters?” So they
receive no recorded forward runs and no `Status today`.

`bin/cli scenarios:contract <id>` prints a case and its criteria. Named no case
it prints the ones whose **Held by** says `not guarded`, and either form exits
nonzero where a case is one of them. That is what makes the recurring todo that
reads them due, so a case that later gets a test no longer asks for a read.
PHPUnit holds the files to the contract format; the relevant tool and skill
tests hold the behavior directly.

## Contract states

| Contract   | Meaning                                                               |
| ---------- | --------------------------------------------------------------------- |
| `held`     | The current implementation is expected to satisfy this targeted case. |
| `open`     | Accepted behavior is still missing or only partly held.               |
| `boundary` | A clean decline and correct route elsewhere is the required behavior. |

A forward review settles its mark with a run. A contract case has no run, so its
state would be a claim nobody ever answers. That is the drift the forward side
just got rid of. So every case carries a **Held by** line naming the tests that
hold it, or saying in as many words that something is not guarded. A test named
there has to exist. `ScenariosTest::everyContractCaseNamesWhatHoldsIt` checks
both halves, so a renamed or deleted test takes the case's claim down with it
rather than leaves it in place.

`held` and `not guarded` can appear together. The case names the tests that hold
most of it and the part nothing covers, the way a requirement does. A case with
nothing behind it at all is a case whose state is fiction.

## Where a case lives

One case is one file, named after its id, in the group whose behavior it holds.
Each group's `readme.md` says what that group is about; the directory listing is
the index.

| Group                                           | Cases                   |
| ----------------------------------------------- | ----------------------- |
| [core-contributor/](core-contributor/readme.md) | `CORE-01` … `CORE-07`   |
| [extension-author/](extension-author/readme.md) | `EXT-01` … `EXT-08`     |
| [site-developer/](site-developer/readme.md)     | `SITE-01` … `SITE-09`   |
| [cross-cutting/](cross-cutting/readme.md)       | `META-01` … `META-05`   |
| [task-skills/](task-skills/readme.md)           | `SKILL-01` … `SKILL-15` |

## Coverage

Rows are task shapes, columns are audiences. A cell names the targeted cases
that hold it; an empty cell is a hole in the contract suite.

| Task                                  | Core contributor      | Extension author | Site developer       |
| ------------------------------------- | --------------------- | ---------------- | -------------------- |
| Bug fix                               | `CORE-01`             | `EXT-03`         | `SITE-03`, `SITE-08` |
| New feature / new code                | `CORE-02`             | `EXT-04`         | `SITE-05`            |
| New project from scratch              | —                     | `EXT-02`         | `SITE-01`            |
| Upgrade to a new TYPO3 version        | `CORE-04`             | `EXT-01`         | `SITE-02`            |
| Testing                               | `CORE-05`             | `EXT-05`         | `SITE-06`            |
| Review, commit, submission            | `CORE-03`, `CORE-07`  | `EXT-03`         | —                    |
| Labels, icons, i18n                   | `CORE-02`             | `EXT-06`         | `EXT-06`             |
| Official API documentation            | —                     | `EXT-07`         | `SITE-07`            |
| Frontend / theming                    | —                     | —                | `SITE-04`            |
| Version and branch spread             | `CORE-06`             | `EXT-01`         | `SITE-02`            |
| Orientation, setup and degraded state | `META-01` … `META-05` |                  |                      |
