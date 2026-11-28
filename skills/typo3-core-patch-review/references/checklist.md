# Core patch review checklist

Write the surfaces below down whole before you read the diff a second time.
Answer each one in the report: assessed, unassessed, or not applicable to this
diff. For a surface this patch does not touch, write one line. A surface you do
not name in the report looks clean to the reader, even if nobody read it.

A review ends each point in one of three ways. It reports a problem, drops a
suspicion, or calls a surface clean. The author cannot see what you read, so
each one names its source. That is the file you opened, the call site you
followed, the command you ran, or the lookup that answered.

A surface marked "assessed" with no source under it is easy to write, and nobody
can check it. Where you did not read a surface, mark it unassessed.

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
  Answer the surface with both halves: what ran, and which of the suites in the
  guide's `verifying` nobody started.

  Where the change sits in a chain, what the follow-up touches decides what a
  test is worth. One that pins behaviour the next change rewrites is churn. One
  that covers what the follow-up leaves alone lasts.
- **Documentation and changelog.** What the diff obliges: the entry, its
  directory, its file name, its cross-references. Also check whether the rules
  require an entry at all. A review that demands an entry the rules do not
  require is itself wrong.

  The manual is the surface's other half, and it sits in two places. A system
  extension's own `Documentation/` is in this checkout and changes in the patch.
  The books `typo3_documentation_lookup` searches are outside the repository. A
  page the diff makes false is a finding wherever it lives. For a page outside
  the repository, report the follow-up change it needs. Do not skip the finding
  because the page is outside.
- **Commit shape.** Subject, body, issue reference, target branch line, and the
  markers the change type requires.
- **Prose.** The comments, the docblocks, the test names and the commit message,
  read against `any/writing/the-prose-a-patch-carries`. Read them as one text,
  in the order a reviewer meets them. Compare the terms across all of them, the
  identifiers, and the core code near the change. One thing has one term, and
  the term comes from that code.

  A word finding names the rule on that page or the identifier it differs from.
  This server has no STE dictionary. So a word judged from memory of one says so
  in its **Evidence** line. The core does not require STE, so a sentence finding
  is **Worth a change**. A term finding reads like this:

  ```markdown
  ### 5. The patch says "reach" where the class says "accessible"

  - **Where:** `typo3/sysext/core/Classes/Site/WebMountSiteFinder.php:84`
  - **Observed:** `isSiteReachable()` beside `getAccessibleHosts()` in `SourceHostProvider`.
  - **Rule:** One term for one thing, across the patch — `typo3_rule_lookup` documentId=any/writing/the-prose-a-patch-carries
  ```
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

  Read it before you call one of those an oversight. Report what it explains as
  the question it is.
- **Security.** Where the diff touches authorization, user input, output
  escaping, or file paths. Or a boundary between what a role may and may not do.
  A finding here is a value and a sink. Establish both, or it is not a finding.
- **The working tree around the patch.** A modified or untracked file beside the
  commit. It ships with the patch if anybody stages it carelessly, and it is not
  part of the change.

## What a finding names

Every finding names five things, and two of them are the ones reviews skip:

1. the changed path, at its line;
2. what the patch does there;
3. the rule or the behaviour it collides with, from the lookup that returned it;
4. **the consequence** — what breaks, for whom, and when somebody would notice;
5. **whether this patch introduced it**. The line the diff wrote and the line it
   only moved past are two different requests to the author.

A finding without the fourth is only your preference. So is a finding whose rule
came from memory rather than from a lookup, even with a citation. A finding
without the fifth asks the author to repair something they did not change. The
reader cannot tell that you did not mean to ask that.

Report what the patch did not introduce in those words. It stays in the review
where it blocks submission on its own, and goes to the issue tracker otherwise.
The reading the user asked for is of a change. A list of what was already wrong
around it is a second review nobody ordered.

Tell what you verified from what you reasoned. A behaviour you traced into the
installed code, a command's output, and a reading of the diff weigh differently.
A report that does not separate them hands the reader a uniform confidence the
review did not have.

## What a dropped candidate names

While you read, you suspect problems that turn out to be none. Each such
suspicion is a candidate. The report does not show a dropped candidate unless
you list it. So list each one with the reason you dropped it, in one sentence. A
reason can be a guard that exists after all. It can be a caller that rules the
case out, or a rule that does not apply. Then the reader knows that you read a
quiet surface and did not skip it.

To drop a candidate needs more evidence than to raise one. A wrong drop loses
the author a real finding, and nothing in the report shows the loss. So drop a
candidate only where something concretely disproves it. Report one you can
neither establish nor disprove as open, with the reading that would settle it
named beside it.

Two dismissals go wrong reliably:

- Dropped because a comment, a docblock or an annotation says the code behaves
  that way. That is a sentence somebody wrote, not the behaviour. Read the
  implementation it describes. Where the two disagree, the disagreement is the
  finding.
- Dropped because it looks unlikely to happen. Unlikely is not disproved. What
  disproves a path is what makes it impossible. That is a guard nobody can pass
  or a caller that cannot exist, at a line.

Before you report a finding, argue against it as the author would. Look for the
caller they know holds the guard, the subsystem's invariant, and the choice the
commit message already states. Look also for a change stacked on this one that
the code here may prepare.

You still rate the patch on its own. Report what the follow-up explains as a
question rather than as an oversight. Where a finding still holds after those
arguments, report it with the arguments it held against. Where it does not hold,
list it as a dropped candidate, with the same evidence written down.

## Severity

- **Blocks submission** — the patch cannot go up as it is. The hook rejects the
  message, the change breaks what the rules forbid, or the diff does not match
  its message.
- **Sent back in review** — a reviewer would ask for it. That is missing
  coverage, a missing changelog entry, an unhandled case, a public API
  obligation not met.
- **Worth a change** — real, and it does not block. Say so, and do not spend the
  reader's first paragraphs on it.
- **Checked and correct** — short, and kept in. It is the only thing that
  separates a surface you read from one you skipped. It names what you read, for
  the same reason a finding names what it collides with.

Rank by what stops the patch first and by consequence second. A cosmetic finding
above a behavioural one costs the review its credibility for the rest of the
list.

What you know about who can reach the path may raise a rank. It never lowers
one. A diff alone shows very little about who can reach a path. Never rank a
real finding down because its path looked hard to reach. Where you could not
establish the path, rank on consequence and say so.
