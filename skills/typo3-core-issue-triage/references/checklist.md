# The verdicts, and what each one owes

One issue gets one verdict. They are not degrees of confidence in a single
finding. Each names a different thing that is true, and each owes different
evidence. Pick the verdict first, and the missing evidence names itself.

Ask the seventh before the other six, of every finding rather than of the ones
that look alarming. It decides where the answer goes rather than what it says:
is what you established a security defect?

## Still happens

The behaviour the report describes is what the branch does today.

Owes: the code path, at its file and line, or the steps that reproduced it and
what you saw. A failing test where a layer can hold it. The branch the
reproduction ran on.

Not enough on its own: that the code looks like it would still do this. A read
of a method is evidence about the method, and the report is usually about the
interaction of two.

## Gone

The behaviour cannot happen any more, and the reason is in the checkout.

Owes: what changed, by name. That is the method that no longer exists, the
branch nobody takes any more, or the entry about the rework. A reproduction that
came out clean is not this verdict. It is the one below.

Fixed by accident is as final as fixed on purpose, but only where you name the
mechanism. "It works for me now" is not a mechanism.

It ends the issue, so it also owes the comment that closes it, below.

## Not reproducible as written

The steps in the report do not produce what it describes. Nothing says whether
that is the report or the branch.

Owes: what you tried, what happened instead, and which of the two it is. The
steps were incomplete, the environment differs, or the report never carried
enough to try. Say which parts of the report you could verify and which you did
not test at all.

This is the verdict people most often write as "gone". They are opposite
outcomes. One closes the issue, the other asks the reporter a question.

## Superseded

Something else already covers this. That is a patch under review, a merged
change, a duplicate, a rework that made the request moot.

Owes: the change or issue number, its state, and whether it does the same work.
An alternative closes an issue only where what it drops is nothing the reporter
was reaching for. Name the arguments and the behaviour the original had and the
replacement does not.

It ends the issue, so it also owes the comment that closes it, below.

## Not a defect

The branch behaves as the project intends. What the report wants is a change of
intent.

Owes: where the intent stands. That is the documentation, the docblock, the test
that pins the behaviour, the changelog entry that introduced it. A verdict of
"works as designed" with no source is an opinion in a maintainer's voice.

This does not close the need. Say what it would take as a feature, and that the
argument for it is a different one. The argument that carries a bugfix is the
same inconsistency inside one version. To find the place where the system
already does the right thing is what turns a wish into a defect.

It ends the issue, so it also owes the comment that closes it, below.

## Cannot be settled here

The question is real, and this checkout cannot answer it.

Owes: what specifically is missing. That is an installation that runs, a
database that behaves differently, or a browser. Or it is the reporter's
configuration, or a version nobody covers any more. And what would settle it, so
the next person starts where this stopped.

Legitimate and underused. It is the honest end of a triage whose reading ran out
before the evidence did.

## A security defect

What the report describes, or what the reading turned up beside it, is something
an attacker can use. That is access to a record the user may not read, or a
value at a sink with no escape. Or it is a check somebody can walk around.

Owes: nothing to the tracker. This verdict is about where the answer goes. So
what it owes is the report the security team receives. The skill's own step says
what that is and which lookup carries the address.

Whichever of the six is also true stays true. You write it for that report
rather than for the issue. A defect that still happens and is exploitable is not
a "still happens" with a note attached. The note is the whole difference in who
may read the answer.

Do not wait until you are sure. A finding that might be exploitable is one the
team rates. The cost of a question to them is an email. The cost of a wrong
decision here is a public exploit against installations with no fix available.

# Before you write any of them

- Which of the report's three claims you verified. That is what the reporter
  saw, what they believed caused it, or what they wanted. Only the first is what
  a checkout settles. To verify the cause and report the issue invalid is the
  standard failure of this work.
- Which branch the verification ran on, said out loud. A verdict with no branch
  is one nobody can repeat.
- Where the suites ran. Inside the checkout's DDEV project or in your own shell
  are two different PHP versions and two different databases. An old report
  about behaviour that depends on either stays open if you leave this unsaid.
- Whether a suite that reported success inspected anything. A green over no
  files turns "not reproducible as written" into "gone" without anybody's
  notice.
- Whether you asked the review server. The cheapest outcome sits there, and it
  costs one call.
- What you did not establish. The part you skipped is the next person's whole
  task, and a verdict that reads as complete hides it.
- Whether the verdict ends the issue. Three of them do, and each hands over the
  comment that closes it, which is the section below. To close, reassign and
  reopen are the maintainer's acts. The text is the triage's.

# The comment that closes it

**Gone**, **Superseded** and **Not a defect** end the issue, and each hands over
the comment that closes it. A verdict that leaves the reason to somebody else
has stopped one step short of what it established.

The other three hand over nothing. **Not reproducible as written** and **Cannot
be settled here** ask the reporter a question instead. To write either up as a
closure is the trap the Gone verdict already names. **A security defect** owes
the tracker nothing. A closing comment is the public step that verdict exists to
prevent.

Forge carries no resolution field. The status is the whole of it, and the
comment is where the reason lives. So the comment says what the status cannot:

- What the verdict rests on: what you tried, on which branch, and what happened
  instead.
- The change that ends it, where you can name one. That is the issue somebody
  filed it under, and per branch the commit and its change on the review server.
  Then the first release each commit is in, which `git tag --contains <commit>`
  answers. The `Releases:` trailer names the branches the author wrote the
  change for, not the release that carries it.
- The branch the fix never reached, where it reached one and not another. A
  reader still on the older line is about to ask that.
- The status to set. Forge closes with three words. **Resolved** where the
  merged patch was filed under this issue. **Closed** where the behaviour went
  with a change filed under another one. **Rejected** where the branch behaves
  as the project intends. A duplicate is a relation on the issue rather than a
  sentence in the comment.

Where you can name no change, say so. "Not reproducible on `main`, verified by
the functional test that copies the record" is a reason to close. A commit
picked to fill the line is not.

A merged patch closes its own issue for a feature and a task, and not for a
bugfix. So a fixed bug whose change names the issue is still open, and this
comment is what ends it.

**You paste the comment into Forge, which renders Textile rather than
Markdown.** A heading, a bullet and a fenced block arrive as the characters you
typed. What survives is plain lines, `#12345` for an issue and a bare URL for a
change. The report around it stays markdown.
