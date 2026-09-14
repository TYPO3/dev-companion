---
id: D-KNW-065
title: 'What a public method commits its author to is a subject this server owns'
date: 2026-08-09
status: confirmed
---

# D-KNW-065 — What a public method commits its author to is a subject this server owns

**That a parameter added to a public method on a non-final core class breaks
every override of it is a fact this server owns. The corpus lacks it.**

The corpus states the rule for what a change removes and narrows, in two places,
and neither names the third move. A session that widens a signature therefore
passes every convention lookup, every project check and the whole of the patch
workflow with nothing said. It meets the rule at commit-message time, after it
wrote the diff, the tests and the changelog entry.

## Evidence

- Re-run on 2026-08-09 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own query returns `fal-processing` at
  `appliesTo(10) + text(335)` and `fal-basics` at `appliesTo(3) + text(159)` and
  nothing else. That is the answer the feedback reported, unchanged. The same
  probe on the rule the session needed matches nothing at all. That is "public
  method signature change on a non-final class breaks subclasses that override
  it". 86 hints come back as the index.
- The words are absent. `non-final`, `override point`, `optional parameter` and
  `signature-incompatible` occur nowhere below `knowledge/` or `skills/`.
- The gap is on the route the session was on.
  `skills/typo3-core-patch-development/SKILL.md` puts one convention call in its
  "Make the change" step, `typo3_hint_lookup` with the concrete paths. That is
  the call the feedback made. The "Establish the blast radius" step above it
  asks how much of the pinned behaviour moves. That is about the suites and not
  about who overrides the class.
- The review route names the surface and enumerates it from the half that cannot
  carry this. `skills/typo3-core-patch-review/references/checklist.md` opens on
  "Public API. What the diff removes, renames, or changes the signature of", and
  then narrows it: "enumerated from the diff's deletions rather than from its
  additions". An added optional parameter is an addition. That is also why this
  session's functional, unit, cgl and phpstan runs were green on the draft that
  breaks.
- The two places that do state the rule name two of the three moves.
  `breaking-not-assessed` in `src/Knowledge/CommitMessage.php` says "a removed
  or narrowed public or protected member makes the change breaking".
  `## Breaking Changes` of
  `knowledge/documents/core/contribution/commit-messages.md`, what
  `typo3_rule_lookup(query "breaking change")` returns, says "A removed or
  narrowed PHP API gets an extension scanner matcher entry". A widened member is
  in neither, and the feedback reports that it read "narrowed" as a word that
  does not cover it.
- That the core calls a signature change a break because of the subclasses
  holds, read in `.checkouts/main` at `c71b2bdb2f`.
  `Changelog/14.0/Breaking-106869-RemoveStaticFunctionParameterInAuthenticationService.rst`
  is a parameter change whose affected installations are the services "extending
  or overriding" the method.
  `Changelog/14.0/Breaking-107777-UseStrictTypesInExtbaseArgument.rst` migrates
  by "Ensure all subclasses … declarations for overridden properties, method
  parameters, and return types". Both narrow. Whether the core files an *added*
  optional parameter the same way is the research the todo owes.
- The one thing that did fire fires last. `typo3_commit_message_guide` is the
  final step of the development skill's order and of the core-patch `routing`
  entry in `knowledge/server-scope.json`. So the only statement of the rule
  reaches a session after it has characterised the change.
- One report from one session. `bin/cli feedback:list` on 2026-08-09 holds 12
  open feedback, all from `/home/benji/projects/typo3-cms` and all stamped
  within three minutes of each other. Nothing in the archive reports the public
  API surface of the class under edit.

## Decided

- Step 1a on the hint half and step 4 on the two wordings, queued rather than
  closed on the spot. Both tests for the spot fail. The wording sits in `src/`
  and in a skill's contract, and what the hint states is a claim about TYPO3 a
  session has to read across `.checkouts/`.
- `normal` rather than the `low` the card arrived at. It is one session, so not
  more than that. But what the silence let through was a break with a `[BUGFIX]`
  on it, on a patch whose point was to reach two release lines. Every check the
  project has was green on it.
- Not `high`. Nothing waits on it, and the rule did arrive in the end.
- The correction is one word in each of the two wordings rather than a new
  check. Both name a closed list of moves. What decides whether the widened
  member joins it as a rule or as a "consider" is the same research the hint
  needs.
- Where the statement goes is the todo's. A hint of its own is the candidate
  rather than a domain that exists. `appliesTo` on core `Classes/` reaches
  nearly every PHP path in a core checkout. That is either the point of a
  blanket rule or the objection to one.
  [`D-KNW-038`](knw-038-a-hint-is-reached-by-the-role-of-a-file.md)
  decides a hint by the role of a file.
- Not the feedback's own wording. Its author guessed about this repository as
  much as a judging run guesses about TYPO3. Its proposed text asserts exactly
  what a session has to read first.
- The backport half is here and never joined to the diff under way.
  `## Release Targets` says a break goes to `main` and that a backport of one is
  the release managers' call. What no page states is the consequence in the
  direction a session meets it. A fix owed to two release lines cannot carry the
  signature change at all. That is a sentence for the hint rather than a second
  gap.

## Assumed

- That the hint is the lever rather than the skill. The session made the hint
  call with the changed paths, so the placement is in reach by construction.
  Nothing tests whether a session that passes no path meets the rule.
- That the silence produced the draft, rather than the model that wrote it
  without a look at the corpus. Nothing here separates the two, and the lever is
  the same either way.
- That one session wrote this feedback and the eleven beside it. They share a
  directory, a model and three minutes, and nothing in a feedback records a
  session.

## Wrong if

- A precedent in `.checkouts/` files an added optional parameter on a public
  non-final method as a plain change. The wording correction would then be
  wrong, and the hint would be teaching a rule the project does not keep.
- The hint keys on core `Classes/` and lands in the answer to every core PHP
  question. What it concerns is the class's own surface rather than the
  directory it sits in, and the placement would have to move to the role.
- A session meets the rule, hands the state over with the additive setter, and
  the reviewers reject that shape. The lever would be the API design and this
  entry would have answered the wrong question.
- A review rather than a patch reports the same surface again. The checklist's
  "from its deletions" is then the placement that has to move, and one card
  carries both.

## Confirmed on 2026-08-09

The research came out one way and the placement the other. The core files a
signature change on a public or protected member as a break on the possibility
of an override. It needs no demonstrated one. So this is a rule and not a
consideration. One entry files it against "custom extensions extending the
method", another calls the affected installations very unlikely and files it
anyway. The first **Wrong if** does not hold. The one exception is a member
taken out of the public API. That is an `Important` rather than nothing and is
the only way such a change reaches a release line.

That the parameter is optional changes nothing is PHP's rule. Nobody owes a
matcher either way.

The second **Wrong if** held and decided the placement. Keyed on paths, the hint
displaced a better one out of a brief, so the patterns came off.
