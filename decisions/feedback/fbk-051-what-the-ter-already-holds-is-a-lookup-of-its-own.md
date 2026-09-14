---
id: D-FBK-051
title: What the TER already holds is a lookup of its own
date: 2026-08-21
status: open
---

# D-FBK-051 — What the TER already holds is a lookup of its own

**A lookup of its own reads what the TER already holds under an extension key
from its public API.**

`feedback/2026-08-19-094528` audited an extension before an official v14 release
and could not establish whether the version in `ext_emconf.php` was already
published. That is not a session that failed to look. Tailor refuses to package
unless `ext_emconf.php`'s version equals the version of the release. So the file
still names the released version afterwards and a published repository reads
exactly like an unreleased one.

## Evidence

- The session settled it outside this server, twice:
  `gh run list --workflow=publish.yml` for the publish job, then `curl` against
  the TER API. It had already reported a release blocker by then, and the
  maintainer confirmed it wrongly. Neither of them could see the registry from
  the checkout.
- The API answers in one call and needs nothing.
  `GET https://extensions.typo3.org/api/v1/extension/blog/versions` returned 200
  and 26,779 bytes on 2026-08-21. That is one array of 32 version objects, each
  with `number`, `state`, `upload_date`, the `typo3_versions` majors and the
  `dependencies.typo3` constraint the release declared. No token, no user agent
  to guess, no challenge page.
- An unknown key answers `404` with a JSON body that names the key:
  `{"status":404,"error":"not_found"}` for `this_key_does_not_exist_xyz`. That
  is the failure shape `D-FBK-027` asks for before the tool exists: a miss is an
  answer rather than a timeout.
- The same call also shows the answer in motion. The feedback recorded 31
  versions with `14.0.0` on top, which declares `>=13.4.15 <=13.4.99` and
  therefore installs on 13.4 alone. On 2026-08-21 there are 32, with `14.0.1`
  that declares `>=13.4.15 <=14.3.99` and the majors `[13, 14]`. The v14 release
  the audit was about has since happened, and nothing in the checkout says so
  either way.
- `bin/cli hints:probe` on the feedback's own question reaches
  `extension-ter-release`, `extension-repository-layout` and
  `extension-repository-dependencies`. The first is the right hint and it is
  about what a publication requires *of the extension*. None of the three says
  what the registry holds.
- Release is not new ground here. `feedback/archive/2026-07-30-174423` and its
  second run in `E-EXT` are two more sessions that answered a release request
  entirely from the checkout. `R-SKL-009` records the domain as open and due to
  come back.
- Every network source this server reads is already its own tool with its own
  `Source` and `openWorldHint`: `typo3_forge_lookup`, `typo3_gerrit_lookup`,
  `typo3_changelog_lookup`, `typo3_documentation_lookup`.

## Decided

- Step 1b, an absent tool, and it gets taken on. An extension key goes in, what
  the TER holds under it comes out, one version per entry with what that version
  declared. Finding nothing is a legitimate answer, which is the `lookup` verb.
- Not a field on `typo3_extension_describe`. That tool answers from the
  installation and from the files a package ships, and it misses where the
  installation does not have the extension. That is where a release audit
  stands, in the extension's own repository. Its miss would also carry two
  unrelated facts under one shape. `installed` says this installation has no
  such extension, and that is not the same answer as a TER that holds nothing.
- The half of the feedback that asks for the consequence in words is step 1a and
  goes into `extension-ter-release`. That the TER refuses a version it already
  holds. And that `ext_emconf.php` still names the released version afterwards,
  so the checkout looks the same in both worlds. Both are statements about the
  TER and Tailor, so they come from a source rather than from recall. That is
  why they belong to the card and not to this run.
- The third sentence the feedback suggests stays out. A route to the publish
  workflow's run history puts the answer back into git and CI state, which is
  the first `doesNotCover` entry. A green publish job is a different fact from a
  registry that holds the version.
- Priority `normal`, above the `low` a card arrives at. The question is
  unanswerable from the checkout by construction rather than by omission. The
  cost it produced was a wrong result handed to a maintainer. It is not `high`:
  nothing is broken, and one session has reported it.
- The feedback stays open until the commit that ships the tool archives it.

## Assumed

- That the release audit is a task that repeats. One session names this shape,
  and what carries it is the round trips `D-FBK-027` measures plus a checkout
  that cannot answer at all. Not a count of sessions.
- That the TER API stays public and stays at `api/v1`. Nothing here reports it
  when it moves, which `D-FBK-027` accepts as the trade for a caller that would
  otherwise rediscover the access path every session.
- That the caller has the key. A `typo3-cms-extension` package needs
  `extra.typo3/cms.extension-key` to install at all, so it is in the repository
  under audit.
- That the comparison of the published versions against the work tree is the
  caller's step. The lookup reports what the registry holds and judges no
  version free.

## Wrong if

- The lookup answers `unavailable` more often than it answers. The API starts to
  ask for a credential, rate-limits an ordinary session, or moves. That is the
  test `D-FBK-027` sets, and it is why the miss shape had its check before this
  decision rather than after.
- A session gets the published versions and still reports the version wrong,
  because what decided it was the tag rather than the registry. Then the source
  chosen answers beside the question.
- Callers reach for it while `typo3_extension_describe` is already open on the
  same extension, and pay a second call for what one answer could have carried.
  Then the separation is the cost and the field was the right shape.
- Nothing but a release audit ever calls it. Then it is a one-task convenience,
  and `D-FBK-027`'s second **Wrong if** held.

## Since then

The tool ships, and the wording settled against the sources turned one of the
two statements it was to carry. This entry had it that the TER refuses a version
it already holds. It does not. The schema declares a second success answer for
an updated version, and the upload takes that branch whenever the row exists and
replaces it. The release tool checks nothing before it posts. That gives the
lookup more weight rather than less: the registry is not a guard the release
process falls back on.

Two **Wrong if** had their measure while the tool took shape. The versions
endpoint answers without a credential and its rate limit does not move for an
anonymous read. The list arrives inside an array of one and stands in
version-number order rather than upload-date order.
