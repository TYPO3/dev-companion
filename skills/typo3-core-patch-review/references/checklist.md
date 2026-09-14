# Core patch review checklist

Write the surfaces below down whole before you read the diff a second time.
Answer each one in the report: assessed, unassessed, or not applicable to this
diff. A surface this patch does not touch costs one line. A surface nobody
looked at reads as clean unless you name it.

A review disposes of a thing in three ways: it reports it, it drops it, or it
declares it clean. All three are claims about a reading the author has to take
on trust. So all three carry what backs them. That is the file you opened, the
call site you followed, the command you ran, the lookup that answered.

Assessed with nothing under it is the cheapest sentence in a review, and the one
a reader cannot check. So where the reading did not happen, write unassessed.

## Review surfaces

- **Public API.** What the diff removes, renames, or changes the signature of,
  and what the contribution rules require for each of those. A read of the new
  code does not show this surface. So enumerate it from the diff's deletions,
  and from every signature line its additions touch. A parameter added to a
  public or protected method on a non-final class fatals every subclass that
  overrides it. It reads in the diff as an addition.

  No suite in the checkout can fail on that. So answer the surface from the
  declaration rather than from a green run.
- **Behaviour.** What the patch changes for code that calls it, the paths it
  does not touch included. That is a guard that was unreachable before and is
  live after. Or it is a value the code used to write and now does not. It is a
  shape of output other code asserts on.
- **Compatibility.** Whether the change fits the branch it targets, and whether
  what it does is available on that branch at all.
- **Tests.** What exercises the changed behaviour, and whether the layer is the
  one that can fail on it. A change with no coverage is a finding. Coverage that
  cannot fail on this change is a worse one, because it reads as coverage.
  Answer the surface with both halves: what ran, and which of the suites the
  guide returned nobody started.

  Where the change sits in a chain, what the follow-up touches decides what a
  test is worth. One that pins behaviour the next change rewrites is churn. One
  that covers what the follow-up leaves alone lasts.
- **Documentation and changelog.** What the diff obliges: the entry, its
  directory, its file name, its cross-references. Equally, whether the patch
  owes an entry at all. A demand for one where the rules do not is a review
  defect.

  The manual is the surface's other half, and it sits in two places. A system
  extension's own `Documentation/` is in this checkout and changes in the patch.
  The books `typo3_documentation_lookup` searches are outside the repository. A
  page the diff makes false is a finding wherever it lives. Outside is where the
  follow-up goes, not a reason the patch owes none.
- **Commit shape.** Subject, body, issue reference, target branch line, and the
  markers the change type requires.
- **Review readiness.** Whether a reader can understand the patch from the issue
  and the message alone. Whether a reviewer can reproduce what it claims to fix.
  Read the issue for that; do not infer it from the message that names it.
- **The review this patch is already in.** What the issue asks for, whether the
  change is on the review server and at which patch set. Whether the checkout
  holds the commit of that patch set. Also whether a comment from an earlier one
  went unanswered. An unanswered comment is why a change sits unmerged, and none
  of this is visible from the checkout.
- **The chain this change sits in.** You rate the patch on its own, and the
  chain says whether a shape in it is preparation. `typo3_gerrit_lookup` answers
  the chain with the change itself. An entry stacked above is a follow-up. Its
  own file list says what a one-class namespace, a non-final class or a service
  without a caller is for.

  Read it before calling one of those an oversight. Report what it explains as
  the question it is.
- **Security.** Where the diff touches authorization, user input, output
  escaping, or file paths. Or a boundary between what a role may and may not do.
  A finding here is a value and a sink. Establish both, or it is not a finding.
- **The working tree around the patch.** A modified or untracked file beside the
  commit. It ships with the patch if anybody stages it carelessly, and it is not
  part of the change.

## What a finding owes

Every finding carries five things, and two of them are the ones reviews skip:

1. the changed path, at its line;
2. what the patch does there;
3. the rule or the behaviour it collides with, from the lookup that owns it;
4. **the consequence** — what breaks, for whom, and when somebody would notice;
5. **whether this patch introduced it**. The line the diff wrote and the line it
   only moved past are two different requests to the author.

A finding without the fourth is a preference. A finding whose rule came from
recall rather than from a lookup is a preference with a citation. A finding
without the fifth sends the author to repair something they did not change.
Nothing in the report says you did not mean that.

Report what the patch did not introduce in those words. It stays in the review
where it blocks submission on its own, and goes to the issue tracker otherwise.
The reading the user asked for is of a change. A list of what was already wrong
around it is a second review nobody ordered.

Tell what you verified from what you reasoned. A behaviour you traced into the
installed code, a command's output, and a reading of the diff weigh differently.
A report that does not separate them hands the reader a uniform confidence the
review did not have.

## What a dropped candidate owes

A review drops more than it reports, and nothing records the drop. Name each
candidate you raised while you read and then let go, with what let it go. That
is the guard that was there after all, its caller, or the rule that does not
apply. One sentence each. It tells the reader that a quiet surface went quiet
after the reading rather than before it.

The two directions do not meet the same bar. To raise a candidate costs a
reading. To drop one costs the author a finding, silently, and nothing
afterwards says it happened. So drop a candidate only where something concretely
disproves it. Report one you can neither establish nor disprove as open, with
the reading that would settle it named beside it.

Two dismissals go wrong reliably:

- Dropped because a comment, a docblock or an annotation says the code behaves
  that way. That is a sentence somebody wrote, not the behaviour. Read the
  implementation it describes. Where the two disagree, the disagreement is the
  finding.
- Dropped because it looks unlikely to happen. Unlikely is not disproved. What
  disproves a path is what makes it impossible. That is a guard nobody can pass
  or a caller that cannot exist, at a line.

Before you report a finding, make the author's case against it. That is the
caller they know holds the guard, the subsystem's invariant, and the choice the
commit message already states. It is the change stacked on this one that a shape
here may prepare.

You still rate the patch on its own. Report what the follow-up explains as a
question rather than as an oversight. Report what survives that together with
what it survived. What does not survive is a dropped candidate, with the same
evidence written down.

## Severity

- **Blocks submission** — the patch cannot go up as it is. The hook rejects the
  message, the change breaks what the rules forbid, or the diff does not match
  its message.
- **Sent back in review** — a reviewer would ask for it. That is missing
  coverage, a missing changelog entry, an unhandled case, a public API
  obligation not met.
- **Worth a change** — real and not blocking. Say so, and do not spend the
  reader's first paragraphs on it.
- **Correct and checked** — short, and kept in. It is the only thing that
  separates a surface you read from one you skipped. It names what you read, for
  the same reason a finding names what it collides with.

Rank by what stops the patch first and by consequence second. A cosmetic finding
above a behavioural one costs the review its credibility for the rest of the
list.

Who can reach the path raises a rank and never lowers one. A diff is the weakest
evidence there is about reachability. A real finding ranked down because it
looked hard to reach is the mistake this rubric cannot recover from. Where you
could not establish the path, rank on consequence and say so.
