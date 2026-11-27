# The verdicts, and the evidence each one needs

One issue gets one verdict. The verdicts are not degrees of confidence in one
finding. Each one names a different thing that is true, and each one needs
different evidence. Pick the verdict first, and you see which evidence is
missing.

Ask the seventh before the other six, of every finding rather than of the ones
that look alarming. It decides where the answer goes rather than what it says:
is what you established a security defect?

## Still happens

The behaviour the report describes is what the branch does today.

Needs: the code path, at its file and line, or the steps that reproduced it and
what you saw. A failing test, where a test layer can hold it. The branch the
reproduction ran on.

Not enough on its own: that the code looks like it would still do this. A read
of a method is evidence about the method. The report is usually about how two
methods interact.

## Gone

The behaviour cannot happen any more, and the reason is in the checkout.

Needs: what changed, by name. It is a removed method, a code path nothing
reaches, or the changelog entry about a rework. A reproduction that came out
clean is not this verdict. It is the next one.

Fixed by accident is as final as fixed on purpose, but only where you name the
mechanism. "It works for me now" is not a mechanism.

It ends the issue, so it also needs the comment that closes it.

## Not reproducible as written

The steps in the report do not produce what it describes. Nothing says whether
the report or the branch is the reason.

Needs: what you tried, what happened instead, and which of three it is. The
steps were incomplete, the environment differs, or the report never had enough
to try. Say which parts of the report you could verify and which you did not
test at all.

People often write this verdict as "gone". The two are opposite outcomes. One
closes the issue, the other asks the reporter a question.

## Superseded

Something else already covers this. That is a patch under review, a merged
change, a duplicate, or a rework that made the request pointless.

Needs: the change or issue number, its state, and whether it does the same work.
An alternative closes an issue only where it drops nothing the reporter wanted.
Name the arguments and the behaviour the original had and the replacement does
not.

It ends the issue, so it also needs the comment that closes it.

## Not a defect

The branch behaves as the project intends. What the report wants is a change of
intent.

Needs: where the intent is written down. That is the documentation, the
docblock, the test that pins the behaviour, or the changelog entry that
introduced it. "Works as designed" without a source is an opinion in a
maintainer's voice.

The need behind the report stays. Say what it would take as a feature, and that
the argument for a feature is a different one. A bugfix needs an inconsistency
inside one version. Find the place where the system already does the right
thing. That turns a wish into a defect.

It ends the issue, so it also needs the comment that closes it.

## Cannot be settled here

The question is real, and this checkout cannot answer it.

Needs: exactly what is missing. That can be an installation that runs, a
database that behaves differently, or a browser. It can be the reporter's
configuration, or a version nobody supports any more. Also say what would settle
it, so the next person starts where you stopped.

This verdict is legitimate and underused. It is the honest end of a triage whose
reading ran out before the evidence did.

## A security defect

What the report describes, or what your reading found beside it, is something an
attacker can use. Examples are access to a record the user may not read, or
output that is not escaped. A check somebody can bypass is another.

Needs: nothing for the tracker. This verdict is about where the answer goes. It
needs the report the security team receives. Step 5 of the skill says what that
report is and which lookup gives the address.

Whichever of the other six is also true stays true. You write it for that report
rather than for the issue. A defect that still happens and is exploitable is not
a "still happens" with a note attached. The note changes who may read the
answer.

Do not wait until you are sure. A finding that might be exploitable is one the
team rates. A question to them costs an email. A wrong decision here costs a
public exploit against installations with no fix available.

# Before you write any of them

- **Which of the report's three claims you verified.** Those are what the
  reporter saw, what they believed caused it, and what they wanted. A checkout
  settles only the first. The typical failure of this work is to disprove the
  cause and report the issue invalid.

- **Which branch the verification ran on**, said in the answer. Nobody can
  repeat a verdict without a branch.

- **Where the suites ran.** Inside the checkout's DDEV project and in your own
  shell are two different PHP versions and two different databases. An old
  report can depend on either. Where you leave this out, it stays open.

- **Whether a suite that reported success inspected anything.** A green over no
  files turns "not reproducible as written" into "gone", and nobody notices.

- **Whether you asked the review server.** The cheapest result is there, and it
  costs one call.

- **What you did not establish.** The part you skipped is the next person's
  whole task. A verdict that reads as complete hides it.

- **Whether the verdict ends the issue.** Three of them do, and each comes with
  the comment that closes it, in the next section. To close, reassign and reopen
  are the maintainer's acts. The text is part of the triage.

# The comment that closes it

**Gone**, **Superseded** and **Not a defect** end the issue, and each comes with
the comment that closes it. A verdict that leaves the reason to somebody else
stops one step short of what it established.

The other three come with no comment. **Not reproducible as written** and
**Cannot be settled here** ask the reporter a question instead. To write either
one up as a closure is the trap the Gone verdict names. **A security defect**
gives the tracker nothing. A closing comment is exactly the public step that
verdict prevents.

Forge has no resolution field. The status is all it records, and the reason goes
in the comment. So the comment says what the status cannot:

- **What the verdict rests on**: what you tried, on which branch, and what
  happened instead.

- **The change that ends it**, where you can name one. That is the issue it was
  filed under, and per branch the commit and its change on the review server.
  Then the first release each commit is in, which `git tag --contains <commit>`
  answers. The `Releases:` trailer names the branches the author wrote the
  change for, not the release that contains it.

- **The branch the fix never reached**, where it reached one and not another. A
  reader still on the older line will ask that.

- **The status to set.** Forge closes with three statuses:

  - **Resolved** where the merged patch was filed under this issue.
  - **Closed** where the behaviour went with a change filed under another issue.
  - **Rejected** where the branch behaves as the project intends.

  A duplicate is a relation on the issue, not a sentence in the comment.

Where you can name no change, say so. "Not reproducible on `main`, verified by
the functional test that copies the record" is a reason to close. A commit
picked only to fill the line is not.

A merged patch closes its own issue for a feature and a task, and not for a
bugfix. So a fixed bug whose change names the issue is still open. This comment
is what ends it.

**You paste the comment into Forge, which renders Textile rather than
Markdown.** A heading, a bullet and a fenced block arrive as the characters you
typed. Plain lines survive, `#12345` for an issue, and a bare URL for a change.
The report around the comment stays markdown.
