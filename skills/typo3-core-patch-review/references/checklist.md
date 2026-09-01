# Core patch review checklist

The surfaces below are written down whole before the diff is read a second time,
and each one is answered in the report — assessed, unassessed, or not applicable
to this diff. A surface this patch does not touch costs one line; a surface
nobody looked at reads as clean unless it is named.

A review disposes of a thing in three ways: it reports it, it drops it, or it
declares it clean. All three are claims about a reading the author has to take
on trust, so all three carry what backs them — the file that was opened, the
call site that was followed, the command that was run, the lookup that answered.
Assessed with nothing under it is the cheapest sentence in a review to write and
the one a reader has no way to check, so where the reading did not happen the
word is unassessed.

## Review surfaces

- **Public API.** What the diff removes, renames, or changes the signature of,
  and what the contribution rules require for each of those. This is the surface
  a reading of the new code does not show, so it is enumerated from the diff's
  deletions — and from every signature line its additions touch, because a
  parameter added to a public or protected method on a class that is not final
  fatals every subclass overriding it and reads in the diff as an addition. No
  suite in the checkout can fail on that, so the surface is answered from the
  declaration rather than from a green run.
- **Behaviour.** What the patch changes for code that calls it, including the
  paths it does not touch: a guard that was unreachable before and is live
  after, a value that used to be written and now is not, a shape of output other
  code asserts on.
- **Compatibility.** Whether the change fits the branch it targets, and whether
  what it does is available on that branch at all.
- **Tests.** What exercises the changed behaviour, and whether the layer is the
  one that can fail on it. A change with no coverage is a finding; coverage that
  cannot fail on this change is a worse one, because it reads as coverage. The
  surface is answered with both halves: what ran, and which of the suites the
  guide returned nobody started. Where the change sits in a chain, what the
  follow-up touches decides what a test is worth: one pinning behaviour the next
  change rewrites is churn, one covering what the follow-up leaves alone lasts.
- **Documentation and changelog.** What the diff obliges — the entry, its
  directory, its file name, its cross-references — and equally, whether an entry
  is owed at all. Demanding one where the rules do not is a review defect. The
  manual is the surface's other half and it sits in two places: a system
  extension's own `Documentation/` is in this checkout and changes in the patch,
  and the books `typo3_documentation_lookup` searches are outside the
  repository. A page the diff makes false is a finding wherever it lives —
  outside is where the follow-up goes, not a reason none is owed.
- **Commit shape.** Subject, body, issue reference, target branch line, and the
  markers the change type requires.
- **Review readiness.** Whether the patch can be understood from the issue and
  the message alone, and whether a reviewer can reproduce what it claims to fix.
  The issue is read for that, not inferred from the message that names it.
- **The review this patch is already in.** What the issue asks for, whether the
  change is on the review server and at which patch set, whether the commit that
  patch set is the one checked out, and whether a comment from an earlier one
  went unanswered. An unanswered comment is why a change sits unmerged, and none
  of this is visible from the checkout.
- **The chain this change sits in.** The patch is rated on its own, and the
  chain says whether a shape in it is preparation. `typo3_gerrit_lookup` answers
  the chain with the change itself, and an entry stacked above is a follow-up
  whose own file list says what a namespace holding one class, a class left
  non-final or a service with no caller here is for. Read it before calling one
  of those an oversight, and report what it explains as the question it is.
- **Security.** Where the diff touches authorization, user input, output
  escaping, file paths, or a boundary between what a role may and may not do. A
  finding here is a value and a sink, and both are established or it is not a
  finding.
- **The working tree around the patch.** What is modified or untracked beside
  the commit, because it ships with the patch if anybody stages it carelessly
  and it is not part of the change.

## What a finding owes

Every finding carries five things, and two of them are the ones reviews skip:

1. the changed path, at its line;
2. what the patch does there;
3. the rule or the behaviour it collides with, from the lookup that owns it;
4. **the consequence** — what breaks, for whom, and when it would be noticed;
5. **whether this patch introduced it** — the line the diff wrote and the line
   it only moved past are two different requests to the author.

A finding without the fourth is a preference. A finding whose rule came from
recall rather than from a lookup is a preference with a citation. A finding
without the fifth sends the author to repair something they did not change, and
nothing in the report says it was not meant to.

What the patch did not introduce is reported in those words. It stays in the
review where it blocks submission on its own and goes to the issue tracker
otherwise, because the reading that was asked for is of a change, and a list of
what was already wrong around it is a second review nobody ordered.

Distinguish what was verified from what was reasoned. A behaviour traced into
the installed code, a command that was run with its output, and a reading of the
diff are three different weights, and a report that does not separate them hands
the reader a uniform confidence the review did not have.

## What a dropped candidate owes

A review drops more than it reports, and dropping is the step nothing records.
Each candidate raised while reading and then let go is named with what let it go
— the guard that turned out to be there, the caller that holds it, the rule that
turned out not to apply, the line that was actually read. One sentence each, and
it is what tells the reader that a quiet surface went quiet after the reading
rather than before it.

The two directions are not held to the same bar. Raising a candidate costs a
reading; dropping one costs the author a finding, silently, and nothing
afterwards says it happened. So a candidate is dropped only where something
concretely disproves it, and one that can be neither established nor disproved
is reported as open, with the reading that would settle it named beside it.

Two dismissals go wrong reliably:

- Dropped because a comment, a docblock or an annotation says the code behaves
  that way. That is a sentence somebody wrote, not the behaviour — read the
  implementation it describes, and where the two disagree the disagreement is
  the finding.
- Dropped because it looks unlikely to happen. Unlikely is not disproved. What
  disproves a path is what makes it impossible: a guard that cannot be passed or
  a caller that cannot exist, at a line.

Before a finding is reported, make the author's case against it — the caller
they know holds the guard, the invariant the subsystem carries, the choice the
commit message already states, the change stacked on this one that may be what a
shape here is preparing. The patch is still rated on its own; what the follow-up
explains is reported as a question rather than as an oversight. What survives
that is reported together with what it survived; what does not is a dropped
candidate, with the same evidence written down.

## Severity

- **Blocks submission** — the patch cannot go up as it is: the message will be
  rejected, the change breaks something the rules forbid breaking, or the diff
  does not do what it says.
- **Sent back in review** — a reviewer would ask for it: missing coverage, a
  missing changelog entry, an unhandled case, a public API obligation not met.
- **Worth changing** — real and not blocking. Say so, and do not spend the
  reader's first paragraphs on it.
- **Correct and checked** — kept short and kept in, because it is the only thing
  that separates a surface that was read from one that was skipped. It names
  what was read, for the same reason a finding names what it collides with.

Rank by what stops the patch first and by consequence second. A cosmetic finding
above a behavioural one costs the review its credibility for the rest of the
list.

Who can reach the path raises a rank and never lowers one. A diff is the weakest
evidence there is about reachability, and a real finding ranked down because it
looked hard to reach is the mistake this rubric cannot recover from: where the
path could not be established, rank on consequence and say so.
