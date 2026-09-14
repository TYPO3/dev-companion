---
id: D-ANS-153
title: The route to prior art runs through the review server
date: 2026-09-09
status: open
coveredBy:
  - GerritTest::anOpenChangeNamingNoIssueSaysSo
---

# D-ANS-153 — The route to prior art runs through the review server

**`typo3_gerrit_lookup` answers "Has anybody attempted this before" by
repository path and by the issue on the change's trailers. So both descriptions
say so.**

The tracker is where a caller asks it and the tracker is where it has no answer,
because a query reaches only text somebody wrote.

## Evidence

- **The report.**
  [`feedback/2026-09-09-184148`](../../feedback/archive/2026-09-09-184148-an-issue-with-an-empty-description-is.md),
  `/home/benji/projects/typo3-cms/.worktrees/toplevel-access`,
  `claude-opus-5[1m]`. The decisive prior art was Forge #101694 "Streamline top
  frame detection". That is a rejected Task whose abandoned change had raised
  the objection the caller was about to file into. `typo3_forge_lookup` never
  found it; seven hand-written wordings against the tracker before that found it
  either.
- **Why the tracker cannot answer it.** #101694 carries `description: ''` and
  ten notes of which eight are a bot. Its only human text is one journal note.
  Its subject says "top frame detection" where every word available from the
  code is "top window" or "top level window".
- **What found it.** `typo3_gerrit_lookup` with `query='top window'` matched
  change 80556's commit message. A read of that change returned
  `{101694, resolves, "Streamline top frame detection", Task, Rejected}`, the
  tracker's own state, with no tracker call.
- **A second session names the same way in, from a different task.**
  [`feedback/2026-09-09-184254`](../../feedback/archive/2026-09-09-184254-six-answer-details-that-carried-this-session.md)
  calls `typo3_gerrit_lookup path='Build/Sources/TypeScript/core/utility'` the
  cheapest and highest-value call of its session. It says it would not have
  guessed from the tool's name that a repository path was a way in.
- **The proposed fallback would not have found it.** The report suggests a
  fallback to a subject-only match on an empty AND. Its two queries were
  `top window access JavaScript backend` and
  `unify JavaScript API backend frame`. The subject is "Streamline top frame
  detection", which carries `top` out of the first and `frame` out of the second
  and none of the rest. Both stay empty under a subject-only AND.
- **A third session reports the absent join on the other end.**
  [`feedback/2026-09-09-182704`](../../feedback/archive/2026-09-09-182704-loaded-typo3-forge-lookup-s-schema-never-called.md):
  change 93620 came back with `issues: []` and a commit message with no
  `Resolves:` trailer. The session read the empty array as nothing to do. The
  patch it finished still carries `Resolves: #XXXXXX`, committed with
  `--no-verify` past the hook that enforces the trailer.

## Decided

- **`typo3_forge_lookup`'s description says a query reaches only text somebody
  wrote**, so only its subject reaches an issue with an empty description. It
  names the review server as the way in for prior art. That is where a caller
  asks the question, and today only the other side names the cross-route.
- **The description names `typo3_gerrit_lookup`'s `path` way in as what answers
  it**, which `D-ANS-151` already puts in that description for a different
  reason. Two sessions called it decisive and one found it only through a read
  of the schema.
- **An empty `issues` on an open change stands as an absence.** It names what
  the absence costs, and the tool that says whether an issue already exists. The
  trailer is mandatory before the change can go up. Both facts are in the same
  answer today and neither points at the other.
- **Against the subject-only fallback.** It would not have answered either of
  the two queries that missed. What a caller gets instead of a miss is a second
  empty answer that looks like a wider search. The per-term counts stay what
  makes a miss actionable, which two reports of this burst name as what works.
- **Against a wider query itself.** The corpus is what somebody wrote, and a
  tracker search that reaches more than that is one this server would invent.
- Queued rather than made here, because both are tool descriptions and an
  answer's shape — `D-FBK-052`.
- The card carries `normal`. Three sessions, and one of them shipped a
  placeholder trailer past a hook because of it.

## Assumed

- That #101694 is a shape the tracker has more of than one. Read off one issue.
  What says it is not rare is that eight of its ten notes are a bot. That is the
  tracker's own habit rather than that reporter's.
- That a caller who reads a description at all reads the route sentence in it.
  `D-SKL-092` is the bound on that, and this is a sentence rather than a lever
  weighed against it.

## Wrong if

- A session reports that it found prior art through a tracker query where the
  review server had nothing. Then the route named here is one of two and the
  description states it too strongly.
- A session reports the stated absence on an open change it deliberately has no
  issue for — a work-in-progress change is exactly that. Then the sentence is
  noise on the state it fires in most, and it belongs behind the same
  work-in-progress signal the commit guide gets.
- The per-term counts turn out to be what a session rewords against for four
  more tries. Then a fallback is what stops it after all, and the shape of the
  fallback is the open question rather than whether to have one.
