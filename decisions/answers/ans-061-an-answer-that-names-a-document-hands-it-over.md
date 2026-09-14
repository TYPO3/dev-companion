---
id: D-ANS-061
title: 'An answer that names a document hands it over'
date: 2026-08-07
status: open
---

# D-ANS-061 — An answer that names a document hands it over

**The tool the session already called reaches a `typo3://guides` document, or
nothing reaches it at all.** Three core sessions finished long tasks with a
`uri` field, an `alsoInHints` id and a resource list their client never rendered
in hand. They read none of the three.

## Evidence

- `feedback/2026-08-07-065313` is a whole session that never called
  `typo3_server_scope`. Its client listed the tools as deferred schemas and
  showed no resource list, so no guide was ever seen. The server's own
  instructions predict exactly this, which is what makes it evidence about the
  instruction rather than about the session.
- `feedback/2026-08-07-130058` is the same session as it corrects itself in the
  debrief. `typo3_script_lookup` returns `typo3://guides/core/testing/scripts`
  inline, with its body, as an ordinary match. So the guide was reachable all
  along. What kept it out of sight is that `typo3_test_run_guide` answered every
  `runTests.sh` question first, and its description reads as a superset. The
  finding is overlap, not obscurity.
- `feedback/2026-08-07-132535` is a different session, a review rather than a
  patch. `typo3_rule_lookup` returned
  `uri: typo3://guides/core/contribution/commit-messages` on both of its calls;
  the session read the matched section twice and never fetched the document. It
  says why: nothing in the answer presents the `uri` as a next action.
- The same session names the page it wanted and never found, "what each change
  type owes". It is a section of the document whose uri the session had in hand.
  `feedback/2026-08-07-132446` is the failed query for that section, from the
  same review, and it carried `alsoInHints: documentation-changelog`, which the
  session saw twice and passed over.
- Re-run here on 2026-08-07. Both of that feedback's queries still return
  `Release Targets` with the uri and the `alsoInHints` id attached. They never
  return `Changelog Files`, which is a section of the same document.

## Decided

- A `uri` in an answer is not delivery. It is delivery to a client that renders
  MCP resources, and three sessions in a row had one that does not. That is the
  failure `bin/cli hints:coverage` already measured from the other side.
- The lever is the tool the session does call, not a tool it should have called.
  `typo3_server_scope` is correct and was never reached, twice, for the same
  stated reason: orientation felt complete. An answer reached that moment; a
  tool nobody invokes did not.
- So the work is on the answer side of the three tools that already name a
  document, rather than on the resource surface. Those are `typo3_rule_lookup`,
  `typo3_test_run_guide` and `typo3_script_lookup`.
- This is step 2 of the ladder, delivery. The corpus lacks nothing: every
  document the three sessions wanted exists, and answers the sessions read named
  two of them.

## Assumed

- The clients that render no resource list are the common case rather than these
  three. All three reports come from one model in one checkout, which is the
  weakest part of the evidence. But a client's resource support is a property of
  the client, and all three state the failure from the client side.
- Naming a document in the answer is enough. Nothing here shows a session that
  acts on such a name; what it shows is three sessions that did not act on a
  bare uri.

## Wrong if

- A session reports that it read a guide because an answer named it, and still
  reassembles the procedure by hand. That would say the handover has to carry
  the body rather than the name.
- A report calls a tool that inlines the whole document too long to read. That
  would say the section cut is right and only its label was wrong.
- A later debrief from a client that renders resources reports the same miss,
  which would say the client was never the variable.

## Since then

On 2026-08-09, the second **Assumed** did not hold on first contact.
`feedback/2026-08-08-224406` is a core patch session that had the guide ids from
`typo3_project_describe` in hand. It read the `documentId` parameter
description, got the line this entry produced at the foot of its answer, and ran
one search instead. Re-run with its own arguments, that answer is two of the
nine sections of `core/contribution/commit-messages`. It says it cut a page and
does not say how much of it it left.

Nothing decided here moves: the resource surface was never the lever, and this
session's client rendered no list either. What it adds is that the name is one
step short of the handover. Where the imperative to read a page whole stands, in
two skills, it is still an address. That is
[`D-ANS-070`](ans-070-a-document-is-handed-over-by-the-call-that-reads-it.md).

## Since then

A fourth session had the ids from the project answer in hand and did not open
one, on a build rather than a review. The name was in front of it all along.

The tool whose subject is one page names no page. A walk over the shelf cost a
session under a client that defers three calls, where the answer it already had
carried the ids.

The second **Assumed** was then met, by the first session on record to act on a
document named in an answer.
