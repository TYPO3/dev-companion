---
id: D-ANS-068
title: 'A change answer carries the ref that fetches the patch set'
date: 2026-08-09
status: open
coveredBy:
  - GerritTest::aChangeWithoutARevisionSaysSo
  - GerritTest::theAnswerCarriesTheRefThatFetchesThePatchSetItNames
---

# D-ANS-068 — A change answer carries the ref that fetches the patch set

**`typo3_gerrit_lookup` hands over the ref that fetches the patch set it names,
derived from the change number and patch set it already answers with.** Two
sessions had a complete description of a patch set they could not fetch in hand.
Both wrote the sharded ref themselves out of Gerrit trivia the answer never
states.

## Evidence

- `feedback/2026-08-08-224354` is a review of change 95179. The answer carried
  number, patch set 1, commit `0b18ff0af75`, project `Packages/TYPO3.CMS` and
  the review URL. The session built `refs/changes/79/95179/1` from prior
  knowledge that Gerrit shards by the last two digits of the change number.
- `feedback/2026-08-08-224352` is a triage of Forge #82228, a different task
  shape and the same reconstruction:
  `git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/19/53819/3 && git show FETCH_HEAD`.
  That diff settled the triage: the 2017 proposal took `width` plus `height` as
  the fit-into-box the `m` modifier already does. The session names it as the
  read this server had no way to give it.
- Re-run on 2026-08-09, both against the review server. `change: "95179"` and
  `change: "53819"` answer number, subject, status, branch, patchSet, commit,
  project, updated and url. Both halves, text and data, carry them. Neither half
  names a ref or a fetch.
- Every input to the string is in that answer, and the derivation is checkable
  against a field beside it. Measured on 2026-08-09,
  `git ls-remote https://review.typo3.org/Packages/TYPO3.CMS refs/changes/79/95179/1 refs/changes/19/53819/3`
  resolves to `0b18ff0af75d3dbae5a28f92d0abf9a4a1be7870` and
  `0271db52b1d088b4b5ac33f1f7a5e15833e08cd0` — the two commits the answer
  already carries.
- The form is in the corpus and reached neither session.
  `knowledge/documents/core/contribution/gerrit-workflow.md` carries the ref,
  the shard rule and the remote asymmetry under *Fetch a Change Into This
  Checkout*. `D-SKL-021` measured on 2026-08-05 that nobody can guess the fetch
  from what the review server offers a reader.

## Decided

- This is step 2 of the ladder, delivery. The corpus lacks nothing. The document
  states the ref exactly, and the answer that reaches the moment the session has
  to fetch the patch states nothing. The lever is the answer side of the tool
  the session did call. That is what `D-ANS-061` settled for a document uri and
  `D-ANS-064` for a change reference in prose.
- The ref is a field of the change entry rather than a sentence in the text
  half. So a caller who composes a command reads it as data.
- The remote goes with it. A ref on its own repeats the failure `D-SKL-021`
  measured. `git fetch origin refs/changes/…` reports that the ref does not
  exist in the very checkout whose push would reach the change. A core clone
  fetches from the mirror. What the answer names is the review server it is
  fetchable over, which is `Gerrit::HOST` and the `project` field already in the
  entry.
- It stays a string in an answer. The tool fetches nothing, and starts nothing
  on the caller's machine as a side effect of a lookup — `R-DIS-006`.
- The ref follows the patch set the entry names, which is always the current
  one. The query asks for `CURRENT_REVISION` and no parameter reaches an older
  patch set. Asking for an earlier one is a separate capability and is not
  decided here.
- Where the server named no patch set, `patchSet` is `0` and there is no ref.
  The field is null then rather than a string with a zero in it. That is the
  shape `unavailable` and `indistinguishable` already have on this tool.

## Assumed

- The shard rule is Gerrit's own rather than this instance's configuration. Two
  changes nine years apart resolve under the last two digits, which is
  consistent with it and is not a read of Gerrit's documentation.
- The zero pad holds below ten. Both measured change numbers are five digits,
  and what a change numbered under 10 shards to was not measured.
- One field is enough. `D-ANS-061` assumed the same thing about a named
  document. Nothing yet shows a session that acts on a name this server put in
  front of it.

## Wrong if

- A session fetches the ref this answer gave and gets a revision other than the
  `commit` beside it. That would say the derivation is not the rule, or not the
  rule on this server.
- A report calls the fetch noise on an issue search that answers ten changes,
  which would say it belongs on a change lookup alone.
- A session reads the ref and fetches over `origin` anyway, which would say the
  remote named in the same answer was not enough.

## Since then

Built as a field on each change entry. It carries the ref and the remote, and
null where the server named no patch set, with the fetch command under the patch
set line.

Both assumptions stand and neither moved anything. The shard rule is Gerrit's
own documented rule, and the case a shorter number would raise cannot arise
here. The low change numbers answer nothing on that server. A measurement
settled the first **Wrong if** rather than a wait. The three refs this answer
derives resolve on the review server to the commits the same answers carry. Two
review sessions have since fetched what the answer named.
