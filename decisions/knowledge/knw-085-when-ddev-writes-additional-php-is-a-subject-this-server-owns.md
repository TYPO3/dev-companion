---
id: D-KNW-085
title: 'When DDEV writes additional.php is a subject this server owns'
date: 2026-08-18
status: confirmed
coveredBy:
  - HintsTest::theDdevSettingsAnswerSaysWhenThatFileIsWritten
  - HintsTest::theRoutesOutOfAGeneratedAdditionalPhpAreOrdered
---

# D-KNW-085 — When DDEV writes additional.php is a subject this server owns

**When DDEV's settings management writes `config/system/additional.php` is
inside this server's boundary and absent from it. The feedback goes to the
queue.**

The corpus states what that file contains and who owns it, and the one sentence
on the moment says DDEV rewrites it on every start. A session boots a clone in
the only order a clone allows, the environment before the dependencies. It gets
no file at all and an HTTP 500 on every request. The exception names the trusted
hosts pattern rather than the file that carries it.

## Evidence

- The server delivers the subject and the moment is not in it.
  `bin/cli hints:probe "DDEV additional.php trustedHostsPattern first start fresh clone"`
  reaches `project-configuration-files` at `appliesTo(37) + text(335)` and
  `installation-boot` at `appliesTo(11) + text(506)`. The session that reported
  fetched the hint by id, so it missed nothing on the way to it.
- The four statements of `project-configuration-files` name the sections DDEV
  generates, the `#ddev-generated` marker, both ways to take the file over and
  the `config/system/.gitignore` interaction. None of them says when the file
  appears.
- `knowledge/task-intents.json` states the opposite for the case reported. The
  `installation-operations` checklist carries "While the #ddev-generated marker
  line is in that file it is rewritten on every start". On a clone with no
  `vendor/` that promises a file the start does not write.
- The failure names none of that. `installation-boot` carries exception
  1396795884 as `VerifyHostHeader` against `SYS/trustedHostsPattern`, which
  reads as a site configuration problem. Here the pattern is absent because the
  file that supplies it is.
- The install reports success either way. `typo3 setup` writes its own
  `config/system/settings.php` from the environment variables, so only the web
  requests fail. The feedback reports that the console succeeded on both
  rebuilds.
- The claim contradicts a reading already recorded here.
  [`D-KNW-049`](knw-049-what-ddev-writes-into-the-settings-is-named-in-full.md)'s
  **Confirmed on 2026-08-03** read `createTypo3SettingsFile` and
  `writeTypo3SettingsFile` at DDEV v1.25.1. It concluded that the only thing
  that stops the write at all is `disable_settings_management`. Whatever the
  session that reported met was not on the path that read covered.
- DDEV is on this machine and its source is not. `ddev version` answers v1.25.1.
  The Go package is on GitHub, so the condition is an open-web read.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a statement about DDEV's generator, and this run has read nothing but this
  repository.
- The reading comes before the wording. The checklist sentence is wrong for one
  case and right for the rest. So what replaces it depends on the condition DDEV
  actually applies. A rewrite from the report would carry the hypothesis of the
  session that reported with the corpus's authority.
- The statement goes on `project-configuration-files`, beside the sections DDEV
  writes, and the same commit corrects the `installation-operations` checklist
  item. Two places state what DDEV does with that file, and a repair of one
  leaves the other with "on every start".
- Priority `normal`, from one session. What lifts it off `low` is that the
  corpus asserts the opposite rather than stays silent. The cost is a 500
  diagnosed against TYPO3's exception while the cause is the environment's write
  order.
- Not step 5. The report satisfies no **Wrong if** of `D-KNW-049`. Those are
  about the `DB` section, a generator that reads the driver, and a session that
  never asked about `config/system/`. What it disputes is a sentence in that
  entry's confirmation, and that entry records it.
- Nobody owes `skills/typo3-development-installation` anything, which is what
  the feedback also asks for. That skill keeps routing and workflow and holds no
  environment defaults. Its proof step is what surfaced the failure, since it
  asks that the sequence run unattended from a clone. The gap is the answer the
  session looks for at that point, and the hint owns it.

## Assumed

- That the account the session that reported gave of its own failure holds. The
  file was absent rather than present and unread. It names the absence from two
  rebuilds and reports that `config/system/` came back with only `settings.php`
  and the `.gitkeep`.
- That the condition belongs to DDEV rather than to that project. One project
  reported it, at DDEV v1.25.1.

## Wrong if

- The read finds no condition beyond `disable_settings_management`, and what the
  session met was its own project's history. A `.ddev/config.yaml` written
  between the two starts, a docroot that did not exist yet. The corpus is then
  right and there is nothing to state.
- The condition turns out to be the document root rather than an installation.
  "Recognises an installation" is the phrase of the session that reported for
  what it observed, and it had already disproved one hypothesis of its own.
- A restart is not the remedy in general. The file appears at the next start
  only where the dependencies are in place by then. So a sequence that starts
  twice and installs nothing between still answers 1396795884.
- The next report arrives from a session that never asked what configures the
  installation. The statement would then belong where a session boots a clone,
  which is `installation-boot`, rather than where the file has its owner.

## Confirmed on 2026-08-18

Read in DDEV's own source, which is what the first two **Wrong if** asked for.
The condition is a detection and it belongs to DDEV rather than to the project
that reported. DDEV sets the settings paths before the generator runs, and the
lookup is for an installed core. Where it finds none both paths point at the
project root. There the generator returns on its first line, before the caveat
it would otherwise print. So the start says nothing at all, and the same test
skips the ignore file.

The account of the session that reported holds. It is the installed core rather
than the document root, which settles the third **Wrong if** too. The statement
says one thing differently from the report's own suggestion. The two triggers
are not the same kind. One is a collision every start repeats and the other an
order a second start ends.

## Since then

The order did not land in the wording, and the next session read the pair flat.
Both surfaces carry a semicolon and an "or", neither an order nor what the
second costs. It took that for the route to a single-command start and committed
the file with all four sections. A reference repository that leaves it generated
rejected the commit.

Step 4 on that half and step 2 on the other. The answer for the repository the
session was in exists here and did not reach the brief. The brief had placed an
extension path in its first paragraph and then handed over a project-scoped
checklist. Closed on the spot, because the ordering is this entry's own reading.
No gate on the intent itself — the checklist is right there in everything but
that clause.
