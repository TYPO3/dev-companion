---
id: D-KNW-027
title: 'Which caches a change invalidates is a subject this server owns'
date: 2026-08-02
status: confirmed
coveredBy:
  - HintsTest::aChangeIsToldWhichCacheGroupHoldsItsOldOutput
  - HintsTest::clearingACacheAndDeclaringOneAreDifferentQuestions
---

# D-KNW-027 — Which caches a change invalidates is a subject this server owns

**How a change leaves an installation's caches is inside this server's boundary
and absent from it. So the feedback goes to the queue rather than closes.**

The corpus names `typo3 cache:flush` once, as the last step of an upgrade,
behind a condition that excludes work on the code. A session that had just
edited a Fluid template reached none of it and deleted a cache directory
instead.

## Evidence

- The feedback's own query reaches nothing. Run in this checkout on 2026-08-02,
  `bin/cli hints:probe "clearing the fluid_template (and code) caches after template changes"`
  classifies it `fluid`. It offers 22 candidates and matches none of them.
- The nearest hint the corpus has answers a different question. "how do I clear
  the TYPO3 caches" reaches `caching` in `php.json` at
  `appliesTo(5) + text(55)`, whose statements are about a cache's declaration.
  The `cacheConfigurations` entry, which frontend a payload wants,
  `cache.<name>` injected instead of a question to `CacheManager`.
- The one place the command stands has a fence between it and this task.
  `typo3 cache:flush` is the fourth step of `installation-upgrade` in
  `general.json` and of the same entry in `task-intents.json`. Its `condition`
  reads "only if the task is about an installation rather than about the code in
  it".
- The one hint that mentions a flush from a template task assumes the reader
  knows how. `frontend-page-rendering` says of `config.sendCacheHeaders` that
  "flushing the server cache changes nothing", which is the symptom and not the
  command.
- No skill says it either. The single occurrence of "cache" below `skills/` is
  PHPStan's result cache directory, in
  `typo3-extension-testing/references/static-quality.md`.
- The commands exist and are a family. On `.checkouts/14.3` at `faf60eea22`,
  `CacheFlushCommand` takes `--group` with `system`, `pages`, `di` or `all` and
  defaults to `all`; `CacheFlushTagsCommand` takes tags; `CacheWarmupCommand`
  takes the same groups. The first one's own help is "Useful after code changes
  during development or after deployments."
- What the deletion reaches is one cache of one group.
  `DefaultConfiguration.php` declares `fluid_template` as a `FluidTemplateCache`
  over `SimpleFileBackend` in group `system`, so its directory is a real path.
  `pages`, `hash` and `rootline` are `Typo3DatabaseBackend` in group `pages`,
  which no file deletion reaches. `cache:flush` dispatches `CacheFlushEvent`
  over every group and `CacheManager::handleCacheFlushEvent()` turns that into
  `flushCachesInGroup()`.
- Whether a template change needs any of it was deliberately not established
  here. The identifier a compiled template sits under belongs to standalone
  `typo3fluid/fluid`, which no checkout vendors. That answer decides whether the
  statement is a command or a correction.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a statement about TYPO3, read across the covered majors, and this run has
  read nothing but this repository and three core checkouts.
- Not step 2, so the sentence is not moved. An explicit `condition` binds the
  one that names the command to an installation task. A move of it to a template
  task would carry the upgrade order with it. What a code change needs is a
  different statement.
- Inside the boundary rather than out of it. `doesNotCover` sends "running an
  installation: server and container setup" to the manual, and that line runs at
  the container and the webserver. `installation-upgrade` already states two of
  the core's own console commands under scope `project`.
- The category is not the answer. It arrived as `tool-gap` and needs no tool —
  `typo3_hint_lookup` would answer it from a hint that does not exist.
- The feedback's **Suggestion** is not copied down. It asks for "the one correct
  way", and whether there is one way or one per kind of change is what the
  research settles.

## Assumed

- That the answer is one statement rather than three. A template, a TypoScript
  and a TCA change may invalidate different groups, and the research may split
  it.
- That `var/cache/code/fluid_template` was a real path in that installation. It
  is where a `SimpleFileBackend` PHP cache writes, and nothing here saw the
  project the session was in.

## Wrong if

- The research shows a changed Fluid template invalidates its own entry. The gap
  is then why the change did not show rather than which command clears it, and
  the statement belongs beside the `sendCacheHeaders` one.
- Which caches a change invalidates turns out to be per installation rather than
  per major. `cacheConfigurations` is overridable in a project's settings, and a
  general statement would describe a default that installation does not run.
- The statement lands on the `caching` hint in `php.json`. That is where a
  cache's declaration lives, which is a core-development question, and the
  session that needed this asked from a template.

## Confirmed on 2026-08-03

The statement held and the gap has closed. The first **Wrong if** fired and the
entry's own instruction for it was right. The template identifier carries a hash
of the file's modification time on all three engines. So a changed template
lands under a new identifier and nobody reads the stale entry again. The
deletion the feedback reported reached the one cache that was already correct.
What answers with the old page is the page cache, whose identifier no template
file enters. So the statement is a correction and a command.

The second did not come up rather than fell. What it names is the group and the
command, both of which survive a project's override of the configuration. Two
results the entry did not anticipate are in the statement. One group comes from
synthesis rather than configuration, and on the newest major a component cache
keys on too little and keeps a stale declaration.
