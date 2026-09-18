---
id: D-ANS-067
title: The changelog above the installed major comes from the manual
date: 2026-08-08
status: revoked
revokedBy: D-ANS-165
coveredBy: []
---

# D-ANS-067 — The changelog above the installed major comes from the manual

**`typo3_changelog_lookup` answers from the installation for the versions it
ships and from docs.typo3.org for every version above its own major. Each entry
says which side it came from.**

A package carries every changelog down to 7.0 and nothing above its own major.
So the entries a caller upgrades *to* were the ones the tool could not show.
`skills/typo3-extension-upgrade/SKILL.md` names that gap in its own words.

## Evidence

- Measured 2026-08-08. A 13.4 installation ships 50 version directories and 3388
  entries; the host names 55 and 3855. The difference is exactly six directories
  and 469 entries — 14.0, 14.1, 14.2, 14.3, 14.3.x and 15.0.
- The manual is `cms-core` under `/c/`, not one of the books under `/m/`. TYPO3
  Explained indexes no changelog entry at all, which is what the first read of
  this got wrong.
- **It is not versioned.** Every version in the URL redirects to `main`. `12.4`,
  `13.4`, `14.3` and `99.9` all resolve to the same `objects.inv`, and what
  answers carries the whole history. So a version applies to the entry names and
  never to the URL, and no read runs per version.
- The inventory line carries the stated title, in the form
  `Deprecation: #110148 - Experimental backend ViewHelpers`. That is the same
  title `Changelog::read()` parses out of a file. So a title costs no read.
- `_sources/<page>.rst.txt` returns the entry's RST byte for byte, `.. index::`
  included. `Changelog::parse()` runs on it unchanged and read `removal: 16.0`
  off a 15.0 deprecation on the first try.
- What it costs: 186 ms for the inventory on a cold process, and 14 ms to
  revalidate it against its entity tag afterwards. Then 14.3 ms per entry body.
  A whole answer against a 13.4 installation is 292 ms. The per-read figure is
  what `D-ANS-066` bought; before the connection stayed open, it was 90 ms.

## Decided

- The two sides never overlap. A version the installation has a directory for is
  that installation's, whatever the host says about it. An entry on disk is the
  code that runs, and the host publishes what the branch carries today.
- Each entry carries `publishedIn`, and a manual entry links by URL where an
  installed one carries its `EXT:` path. A caller who acts on an entry above its
  own major reads a target that moves, for a major with no release. An answer
  that presented both as "the changelog" would hide exactly that.
- Naming a version the installation ships reaches no host at all. That is the
  ordinary question, and it must not pay a round trip for entries the narrower
  query has already excluded. With no network that is a connect timeout.
- A host that fails once is not asked again in this process. This is the tool a
  session calls most and it used to touch nothing outside the machine. An
  offline session pays the timeout once rather than once a question.
- The search reaches a manual entry by name and by its stated title, never by
  the identifiers in its body. Those are 469 reads on a fallback that runs on a
  miss, which is six seconds. The answer says the identifier search does not
  reach them.
- The entry carries the stated title as `stated` rather than `title`. A search
  reads `title`, and an installed entry gains one only from a file read. Under
  the searched name it would let a manual entry answer in the wrong pass. That
  is the pass where the search still reaches the installed entry it should have
  found by its file name alone.
- One parser reads both sides. The host serves the same RST the package ships,
  so what differs is the delivery. `Changelog::parse()` and `Changelog::named()`
  take contents, and the two readers supply them.
- The installation is still required. Without one the tool is `unsupported` as
  before: this fills the gap above an installation rather than replaces one.

## Assumed

- That the redirect to `main` stays. If `/c/typo3/cms-core/13.4/` ever served a
  13.4-shaped changelog, this would read one manual and call it another.
- That `_sources` stays published. It is a Sphinx build artefact rather than a
  documented endpoint. A request that `llms.txt` name it is one of the five asks
  that card left in wait.
- That the entry-name grammar holds. The reader silently skips a page whose name
  does not parse as `<Type>-<issue>-<Spelling>`. That is right for the handful
  of non-entry pages and wrong if the writer ever renames the rest.

## Wrong if

- An answer carries an entry twice, once from each side. That is what a change
  to how the installation names its directories would do to the overlap rule.
- A session acts on a 15.0 entry as settled and the entry changes before the
  release. The `publishedIn` field and the sentence beside it are the whole of
  what stands between a caller and that.
- The host is slow rather than absent, and every changelog call pays up to eight
  seconds. The store holds only the failure, not a slow success.
- A caller on a current major pays for a read that adds nothing, because the
  installation's highest version and the host's are the same. Nothing withholds
  the read in that case today: the skip is by named version, not by a comparison
  of the two highest.

## Since then

`feedback/2026-08-24-140259` is a session the gate the last **Decided** keeps
refused. It asked for v14 Fluid entries before `composer install` had run and
heard there was no installation. Read on 2026-08-25 as evidence against that
line and it is not. Its query,
`registerTagAttribute registerUniversalTagAttributes`, run against
`.checkouts/13.4` with the host up, returns one entry. That is the 13.3
Deprecation "Fluid standalone methods", `matchedIn: "body"` and
`answeredBy: "packages"`. The six manual versions from 15.0 down to 14.0
contribute nothing, exactly as the decided line about identifiers in a body says
they would. An ungated manual half would have answered that session the same
nothing. What it needed instead is
[`D-ANS-105`](ans-105-the-unsupported-answer-says-what-would-make-it-answerable.md).

## Revoked on 2026-09-18

By
[`D-ANS-165`](ans-165-the-changelog-is-read-from-docs-typo3-org-and-the-installation-answers-the-rest.md).
docs.typo3.org renders the changelog after every merge and publishes every page
as Markdown. So it is the source, and the installation answers what
docs.typo3.org did not list. The two sides and the sentence that names an
unreachable docs.typo3.org stand there. Two things no longer hold. The gate by
named version, which reached no server for a version the installation ships, and
the installation as the side that answers first.
