---
id: D-KNW-047
title: 'What installs TYPO3 below an extension is a subject this server owns'
date: 2026-08-03
status: open
---

# D-KNW-047 — What installs TYPO3 below an extension is a subject this server owns

**The Composer layout that installs TYPO3 below the extension's own
`composer.json` is inside this server's boundary and absent from it. The card
goes to the queue at `normal`.**

The corpus knows the keys of that layout from the other side only. It names
`extra.typo3/cms.web-dir` to say which directory the server serves, and
therefore where a one-off script may not go. It says nothing about the keys that
put an installation there in the first place. A session that composes such a
root package gets silence on two of them. It gets a resolver failure whose
message points at the wrong package, and a key the writer accepts and the
installer ignores.

## Evidence

- Re-run on 2026-08-03 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own query reaches `extension-manifest` at
  `appliesTo(13) + text(194)` and `project-build-and-scripts` at
  `appliesTo(6) + text(238)`, and nothing else. The first is what an extension
  declares about itself, the second is where a script goes. Neither says how an
  installation comes to be below the package.
- Narrowing the query loses even that.
  `bin/cli hints:probe "TYPO3 extension composer root package app-dir web-dir typo3/cms-cli local installation"`
  matches nothing at all, and 78 hints come back as the index.
- The words are absent. `app-dir`, `vendor-dir`, `bin-dir` and `cms-cli` occur
  nowhere below `knowledge/` or `skills/`. `web-dir` occurs once, in
  `project.json`, and only to name the document root — the ground
  [`D-KNW-045`](knw-045-the-document-root-is-named-by-what-configures-and-serves-it.md)
  and
  [`D-KNW-026`](knw-026-the-one-off-script-rule-is-owed-the-place-it-does-not-name.md)
  stand on.
- The second claim holds, read in `.checkouts/14.3`.
  `typo3/sysext/core/composer.json` line 92 and the root `composer.json` line
  118 both require `typo3/cms-cli` at `^3.1.3`. So a root constraint of `^5.0`
  cannot resolve against a 14.3 core. The package's version line has nothing to
  do with the core's, which is what makes `^5.0` a plausible thing to write.
- This run cannot read the other two claims here.
  `typo3/cms-composer-installers` is below no checkout, since `.checkouts/14.3`
  carries no vendor directory. So the message the installer ignores `app-dir`
  with, and where TYPO3 loads a root-package extension from, need an
  installation rather than a grep.
- The corpus answers part of the third claim already, in reach. `public-assets`
  states that the installer publishes `Resources/Public/` of an installed
  package into the document root below `_assets/<hash>/`.
  `bin/cli hints:probe "where does a composer-installed extension land, typo3conf/ext symlink or _assets"`
  reaches it at `appliesTo(7) + text(231)`. What no hint says is the half the
  session that reported called worth a positive statement. That is that TYPO3
  loads a root-package extension from the repository root, so an empty
  `typo3conf/ext/` beside it is not a broken installation.
- The discovery side already knows the root package.
  [`D-DIS-001`](../discovery/dis-001-the-root-package-counts-as-an-installed-package.md)
  counts it among the installed packages, and a session confirmed it against two
  fixtures on 2026-08-01. What this server reads is therefore settled, and
  nothing states what TYPO3 itself does with such a package.
- The card is one of five. `bin/cli feedback:list` groups 12 open feedback under
  `/home/benji/projects/ext-guidedtour`, and `feedback/2026-08-03-162745` is the
  umbrella of five of them. It lists the five steps of the task and names four
  obstacles. It says each obstacle has its own feedback, and asks for a skill
  over the whole local environment. This is the first of the four. All five are
  in hand on their own branches today.

## Decided

- Step 1a, and queued rather than closed on the spot. What lands is a statement
  about Composer and about the TYPO3 installer, and this run could read one of
  its three claims.
- Trimmed rather than archived. The `_assets` half of the suggestion is in the
  corpus and in reach, so this entry strikes it with `public-assets` named in
  its place. The rest stays open behind the card.
- `normal` rather than the `low` the card arrived at. Two of the three findings
  are traps rather than absences. The writer accepts `app-dir` and the installer
  ignores it, and the `cms-cli` failure blames a version line that looks wrong
  and is not.
- Not `high`. Nothing waits on it, and one session reported it, five times in
  nine minutes, rather than several sessions once each.
- Not the suggestion's own wording. Its author guessed about this repository, as
  [`judging.md`](../../documentation/records/judging.rst) says every suggestion
  does, and a third of what it asks for is already here.
- The shape question stays where the feedback asked it. Whether the local
  environment earns a skill is step 1b and belongs to the umbrella card, so this
  entry decides the knowledge half only.
- The four obstacle cards are not folded into one. They share a task and name
  four subjects: the Composer layout, the `setup` command, the site base of an
  imported distribution, and DDEV's settings management. The umbrella is where a
  session would decide a fold.
- Where the statement goes is the todo's. `project-build-and-scripts` is the
  candidate it starts from, since it already names `web-dir`.

## Assumed

- That one session wrote all five. They share a directory, a model and nine
  minutes, and nothing in a feedback records a session.
- That the pair the claims are about is the one the front matter states: TYPO3
  14.3.5 with `typo3/cms-composer-installers` v5. Nothing here records that
  pair, and the message about `app-dir` is a property of the installer rather
  than of the core.
- That a session can build a root package that installs TYPO3 where this
  repository runs. `Upkeep\Fixture` writes a Composer project below `.fixtures/`
  already, but it writes the vendor tree itself, and a real `composer install`
  needs the network.

## Wrong if

- The `app-dir` message turns out to be conditional, on the installer major or
  on a `web-dir` beside it. It is then one statement per line rather than one
  statement, and the todo plans one.
- The umbrella card lands a skill that owns the local environment. The statement
  belongs in that skill's material then, and a session should fold these four
  cards into it under one `**Serves:**` line.
- A session that composes the layout reads the written statement and still
  requires `typo3/cms-cli`. The gap would be in the routing rather than in the
  corpus, and this entry would have answered a cheap rung for a step-3 problem.
- Nothing here can install a root package without the network. The statement
  then rests on a read of the installer's source rather than on a run, and it
  has to say so.

## Since then

A session worked the card the same day and disproved the last **Assumed**. The
network is in reach from here, so the session built the layout rather than
reasoned about it and read all three claims off it. The first **Wrong if** did
not hold. The installer raises the message from the presence of the key alone,
and every covered major requires the same installer. So what landed is one
statement per claim and unbound. It went into the hint about the repository that
is only the extension, rather than the one about a repository that holds an
installation.
