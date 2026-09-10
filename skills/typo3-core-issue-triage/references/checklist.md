# The verdicts, and what each one owes

One issue gets one verdict. They are not degrees of confidence in a single
finding: each names a different thing that is true, and each is owed different
evidence. Pick the verdict first and the missing evidence names itself.

The seventh is asked before the other six, of every finding rather than of the
ones that look alarming, because it decides where the answer goes rather than
what it says: is what was established a security defect?

## Still happens

The behaviour the report describes is what the branch does today.

Owes: the code path, at its file and line, or the steps that reproduced it and
what was seen. A failing test where a layer can hold it. The branch the
reproduction ran on.

Not enough on its own: that the code looks like it would still do this. Reading
a method is evidence about the method and the report is usually about the
interaction of two.

## Gone

The behaviour cannot happen any more, and the reason is in the checkout.

Owes: what changed, named — the method that no longer exists, the branch that is
no longer taken, the entry that says it was reworked. A reproduction that came
out clean is not this verdict; it is the one below.

Fixed by accident is as final as fixed on purpose, but only where the mechanism
is named. "It works for me now" is not a mechanism.

Ends the issue, so it also owes the comment that closes it — below.

## Not reproducible as written

The steps in the report do not produce what it describes, and nothing says
whether that is the report or the branch.

Owes: what was tried, what happened instead, and which of the two it is — the
steps were incomplete, the environment differs, or the report never carried
enough to try. Say which parts of the report were verifiable and which were not
tested at all.

This is the verdict that most often gets written as "gone". They are opposite
outcomes: one closes the issue, the other asks the reporter a question.

## Superseded

Something else already covers this — a patch under review, a merged change, a
duplicate, a rework that made the request moot.

Owes: the change or issue number, its state, and whether it does the same work.
An alternative closes an issue only where what it drops is nothing the reporter
was reaching for. Name the arguments and the behaviour the original had and the
replacement does not.

Ends the issue, so it also owes the comment that closes it — below.

## Not a defect

The branch behaves as the project intends. What the report wants is a change of
intent.

Owes: where the intent is stated — the documentation, the docblock, the test
that pins the behaviour, the changelog entry that introduced it. A verdict of
"works as designed" with no source is an opinion in a maintainer's voice.

This does not close the need. Say what it would take as a feature, and that the
argument for it is a different one: the argument that carries a bugfix is the
same inconsistency inside one version, and finding the place where the system
already does the right thing is what turns a wish into a defect.

Ends the issue, so it also owes the comment that closes it — below.

## Cannot be settled here

The question is real and this checkout cannot answer it.

Owes: what specifically is missing — a running installation, a database that
behaves differently, a browser, the reporter's configuration, a version no
longer covered. And what would settle it, so the next person starts where this
stopped.

Legitimate and underused. It is the honest end of a triage whose reading ran out
before the evidence did.

## A security defect

What the report describes, or what the reading turned up beside it, is something
an attacker can use: access to a record the user may not read, a value that
reaches a sink unescaped, a check that can be walked around.

Owes: nothing to the tracker. This verdict is about where the answer goes, so
what it owes is the report the security team receives, and the skill's own step
says what that is and which lookup carries the address.

Whichever of the six is also true stays true and is written for that report
rather than for the issue. A defect that still happens and is exploitable is not
a "still happens" with a note attached: the note is the whole difference in who
may read the answer.

Do not wait until you are sure. A finding that might be exploitable is one the
team rates, and the cost of asking them is an email, while the cost of deciding
it here and being wrong is a public exploit against installations with no fix
available.

# Before writing any of them

- Which of the report's three claims was verified: what was seen, what was
  believed to cause it, or what was wanted. Only the first is what a checkout
  settles, and verifying the cause and reporting the issue invalid is the
  standard failure of this work.
- Which branch the verification ran on, said out loud. A verdict with no branch
  is unrepeatable.
- Where the suites ran. Inside the checkout's DDEV project or in your own shell
  are two different PHP versions and two different databases, and an old report
  about behaviour that depends on either is settled by neither if this is left
  unsaid.
- Whether a suite that reported success inspected anything. A green over no
  files is what turns "not reproducible as written" into "gone" without anybody
  noticing.
- Whether the review server was asked. The cheapest outcome sits there and it
  costs one call.
- What was not established. The part that was skipped is the next person's whole
  task, and a verdict that reads as complete hides it.
- Whether the verdict ends the issue. Three of them do, and each hands over the
  comment it is closed with, which is the section below. Closing, reassigning
  and reopening are the maintainer's acts; the text is the triage's.

# The comment that closes it

**Gone**, **Superseded** and **Not a defect** end the issue, and each hands over
the comment it is closed with. A verdict that leaves the reason to be composed
by somebody else has stopped one step short of what it established.

The other three hand over nothing. **Not reproducible as written** and **Cannot
be settled here** ask the reporter a question instead, and writing either up as
a closing is the trap the Gone verdict already names. **A security defect** owes
the tracker nothing: a closing comment is the public step that verdict exists to
prevent.

Forge carries no resolution field. The status is the whole of it and the comment
is where the reason lives, so the comment says what the status cannot:

- What the verdict rests on: what was tried, on which branch, and what happened
  instead.
- The change that ends it, where one is named: the issue it was filed under, and
  per branch the commit and its change on the review server. Then the first
  release each commit is in, which `git tag --contains <commit>` answers. The
  `Releases:` trailer names the branches the change was written for and not the
  release that carries it.
- The branch the fix never reached, where it reached one and not another. That
  is what a reader still on the older line is about to ask.
- The status to set. Forge closes with three words. **Resolved** where the
  merged patch was filed under this issue, **Closed** where the behaviour went
  with a change filed under another one, **Rejected** where the branch behaves
  as the project intends. A duplicate is a relation on the issue rather than a
  sentence in the comment.

Where no change can be named, say so: "not reproducible on `main`, verified by
the functional test that copies the record" is a closing reason, and a commit
picked to fill the line is not.

A merged patch closes its own issue for a feature and a task, and not for a
bugfix. So a fixed bug whose change names the issue is still open, and this
comment is what ends it.

**The comment is pasted into Forge, which renders Textile rather than
Markdown.** A heading, a bullet and a fenced block arrive as the characters they
were typed as. What survives is plain lines, `#12345` for an issue and a bare
URL for a change. The report around it stays markdown.
