---
id: D-ANS-165
title: The changelog is read from docs.typo3.org and the installation answers the rest
date: 2026-09-18
status: open
coveredBy:
  - CoreChangelogTest::aMajorDocsTypo3OrgDidNotAnswerForComesFromTheInstallationWhereItShipsIt
  - CoreChangelogTest::aManualTitleDoesNotShadowTheInstalledEntryAQueryIsAbout
  - CoreChangelogTest::aShownEntryIsReadAsTheMarkdownDocsTypo3OrgRenders
  - CoreChangelogTest::aTagFilterReadsNoManualEntryItDrops
  - CoreChangelogTest::aVersionDocsTypo3OrgDoesNotListComesFromTheInstallation
  - CoreChangelogTest::aVersionDocsTypo3OrgListsComesFromItWhateverTheInstallationShips
  - CoreChangelogTest::anInstalledEntryIsShownFromThePageDocsTypo3OrgRendered
  - CoreChangelogTest::docsTypo3OrgNotAnsweringLeavesTheInstallationToAnswer
  - CoreChangelogTest::docsTypo3OrgThatDidNotAnswerIsNotAskedAgainInThisProcess
  - CoreChangelogTest::oneListingPerCoveredMajorIsRead
  - CoreChangelogTest::withoutAnInstallationAndWithoutDocsTypo3OrgTheQuestionIsUnsupported
  - CoreChangelogTest::withoutAnInstallationDocsTypo3OrgAnswersAlone
---

# D-ANS-165 — The changelog is read from docs.typo3.org and the installation answers the rest

**`typo3_changelog_lookup` reads every covered major from the listing
docs.typo3.org publishes and a shown entry from the Markdown it renders. The
installation answers a version docs.typo3.org did not list.**

The installation was the source and docs.typo3.org filled in the versions above
its major (`D-ANS-067`). What an installation ships is the RST the build runs
on, includes unresolved and every role as markup. docs.typo3.org renders the
changelog after every merge, so it is ahead of a package and of a checkout
nobody pulled today.

## Evidence

- **Read on 2026-09-18**: docs.typo3.org publishes `Changelog-<major>.json` for
  every major from 7 to 15, with `type`, `issue`, `typo3-version`, `title`,
  `path` and `tags` per entry. Beside every page it publishes the page as
  Markdown at the page's own URL with `.md` for `.html`, the way `D-ANS-157`
  reads a manual page. Its front matter carries the same fields and a `rendered`
  timestamp, and its body is the rendered one. The Forge issue is a link, the
  Migration its own `##` section, and code is fenced.
- Every listing and every page carries an `ETag` and answers `If-None-Match`
  with a 304 and no body. So a process holds what it read and asks
  docs.typo3.org per call whether it moved. A render after a merge is in the
  next answer.
- What it costs against `.checkouts/13.4` with docs.typo3.org up: the four
  covered listings are ~70 KB compressed. A cold call with three shown entries
  is 275 ms. A call that revalidates the four listings and the shown pages is
  about 220 ms of round trips and no payload.
- A tag filter over the manual side reads nothing, because the tags are in the
  listing. On disk it reads one RST per entry that survived the version and the
  type, `D-ANS-093`.
- `v14.3.0` ships `14.0` to `14.3` and no `14.3.x`, while docs.typo3.org lists
  21 entries under `14.3.x`. Those were reachable only by a directory comparison
  before, and a bound by major lost them for an afternoon.

## Decided

- **docs.typo3.org answers every version it lists, whatever the installation
  ships for it.** A version on disk that docs.typo3.org lists is behind
  docs.typo3.org or equal to it, never ahead, except for a change in review. The
  entry says `manual` and links by URL, and `url` is on every entry.
- **The installation answers what docs.typo3.org did not list.** That is every
  version below what `knowledge/versions.json` covers, which a package ships
  down to 7.0. It is also a major docs.typo3.org did not answer for. Offline
  that is the whole changelog the installation ships, and the answer says that
  the versions above it are unread rather than absent.
- **A shown entry's body is the rendered page whichever side listed it.** The
  RST on disk is the source the build ran on, includes unresolved and every role
  as markup, and the page is its result. So the removal and the migration come
  from docs.typo3.org for an entry the installation listed as well, and from the
  RST only where docs.typo3.org did not answer.
- **The identifier search stays on disk.** It reads the body of every entry in
  the narrowed set, `D-ANS-042`, and the listing carries no body. The answer
  says it reaches the entries the installation ships.
- **Without an installation docs.typo3.org answers alone**, and the answer says
  that nothing comes from disk. The unsupported answer remains for the case
  where neither answers, and its reason names both.
- **The hold is per process and revalidated per call.** `D-ANS-049` keeps a
  store off the disk, and a server process lives a session. What the caller pays
  for a listing that did not move is one round trip and no payload.
- **A server that gives nothing at all is not asked again in this process.** A
  404 for one major is an answer, and the next major is asked for. The two are
  told apart by whether a body came back at all.
- **Against `toc.json` for the manual search.** `Documentation` reads its pages
  from the inventory, and `Permalink` reads the objects that are not pages from
  the same file. A second read for the pages alone would add a fetch and take
  none away.
- **Against a bound by the installed major**, which a first cut held for an
  afternoon. It lost the patch level of the installed line and a change merged
  since the last pull, and both are what a caller on a current major asks for.

## Assumed

- That the documentation team keeps publishing the listing and the Markdown
  beside every render, and that `typo3-version` stays the directory name the
  core uses.
- That every major `knowledge/versions.json` covers has a listing on
  docs.typo3.org, so a 404 on one is a major docs.typo3.org has not opened.

## Wrong if

- docs.typo3.org publishes a `Changelog-<major>.json` for a major
  `knowledge/versions.json` does not cover. Then that major is neither read from
  docs.typo3.org nor, above the installed one, on disk. The raise of that file
  is what closes it.
- A checkout on `main` carries an entry docs.typo3.org does not list yet, and
  the answer hides it. That is a change in review, and the caller who wrote it
  knows.
- An entry's `tags` in the listing differ from the `.. index::` line of its
  file. Then the tag filter disagrees with what the same entry shows from disk,
  where docs.typo3.org did not answer.
- docs.typo3.org withdraws the listing or the Markdown while it keeps the
  inventory and the sources. Then every manual read fails, and the readers this
  replaced are in the history of `src/Manual/CoreChangelog.php`.
