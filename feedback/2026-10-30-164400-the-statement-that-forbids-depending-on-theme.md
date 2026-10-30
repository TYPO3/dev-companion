---
date: 2026-10-30T16:44:00+00:00
category: idea
status: open
model: claude-fable-5
tool: typo3_hint_lookup, typo3_extension_describe, typo3_project_describe
directory: /home/lina/trials/site12-with, /home/lina/trials/site12-without
---

# the statement that forbids depending on theme_camino arrived and changed nothing

## Observation

Two runs of 2026-10-30 built a sitepackage over an installation that renders
`typo3/theme-camino` with its shipped content. One run had this server and one
had none. Both wrote `"typo3/theme-camino": "^14.3"` into the sitepackage's own
`composer.json`. Both wrote `dependencies: - typo3/theme-camino` into its site
set.

**The corpus forbids both of those, and the statement reached the run that had
it.** `sitepackage-layout` says: "Read, not depend on: it is the theme of one
release line, announced to move out of the core into a repository of its own,
and a project sitepackage that requires it or extends its set buys that
migration for nothing." The transcript holds that answer once. The session read
it and then did both things it names.

## What the statement competed with

The session asked `typo3_hint_lookup` for eleven ids in one call. The sentence
arrives inside `sitepackage-layout`, between a sentence about a second page
layout and a sentence about how the language files are split by who reads them.
Nothing tells the reader that one of those three decides the shape of the work
and two describe a detail inside it. A constraint that vetoes an architecture
arrives at the weight of a naming convention.

## What the second source did not deliver

`knowledge/catalog/reference/entries.json` carries the same rule as a `caveat`
on the `sitepackage` entry: "Read it, do not depend on it: it is the theme of
one release line and is announced to move out of the core into a repository of
its own." The session called `typo3_extension_describe` for `theme_camino`
three times. No answer in the transcript carries that sentence. So the entry
that is about this package did not reach the session that asked about this
package.

## Why the bind is worth a veto

`theme_camino` registers its content types itself, in
`Configuration/TCA/Overrides/`, with fields of its own such as
`tx_themecamino_link`. The installation held 50 content elements, and 18 of
them stand on those types. A project that drops the theme later cannot drop it
alone: those rows lose the type that renders them and the backend cannot open
them. So the correct answer states the migration the existing content owes.
Neither run states it.

## What this measures

The run with this server spent about 2.2 times the tokens of the run without
it and reached the same architecture. The statement that would have told the
two apart was delivered and did not hold. A statement nobody acts on costs what
a missing statement costs, and it hides as coverage.

## A second pair narrows this

Two more runs of the same case ran on 2026-10-30, after the content was
rewritten so the design and the site are about one subject. The design of the
pair above is a clinic over a hiking site, and that mismatch is the first
answer to why the statement did not hold.

With the subjects matched, the statement held. The run with this server
declared no `theme_camino` in its package and none in its set. It wrote three
backend layouts of its own, moved the seven pages onto them, and said in its
`page.tsconfig` that the content element TCA still ships with the theme. The
run without this server required the theme and depended on its set, as both
runs of the first pair did.

So the report stands with a bound on it. The statement reaches a session and
holds where the task is coherent. It did not hold where the design and the
content disagreed, and no reader of the first pair alone can tell those two
cases apart. One run each is not a measurement, and `D-EVI-013` asks for
three.

## Suggestion

Judge first whether a statement can carry that it decides the shape of the work
rather than a detail inside it. Where it can, the sitepackage intent is the
place the veto belongs, because a session reaches the intent before it chooses
an architecture. Weigh that against the second pair, where the statement held on its own.

Check separately why the reference catalog's `caveat` did not reach a session
that named the package. That half is untouched by the second pair, and it looks
like a route that does not exist.
