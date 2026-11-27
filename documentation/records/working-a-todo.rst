Working a todo
==============

This page covers the work on one todo, from the moment ``bin/cli todo:next``
prints it to the commit. It says what a session reads before the first change,
how it settles a question, what it asks and what the commit leaves. What is due,
what recurs, and what a finished todo leaves behind is in :doc:`index`. This
page does not repeat it.

The steps at a glance:

1. Read what the todo serves and what the code does now.
2. Correct the todo file where the read shows it wrong.
3. Settle each open question from a source.
4. Ask what has no source, before the change.
5. Commit what you read, and leave ``todo/`` true.

The todo is a claim
-------------------

Treat the todo as a claim, not as an instruction. Its paragraph is one session's
belief about where to start. That session wrote it before the work it describes,
and it has ended and cannot answer. The todo is the best start the repository
has. Still, the file it names may have moved, and the check it proposes may
already exist. The thing it says is absent may have arrived in the four commits
since.

A session that opens with an edit has tested none of that. It finds out at the
point where that costs the most: after it has written the change.

Before the first change
-----------------------

Read four things before the first change:

* **What the todo serves.** The ``serves:`` key names a requirement, a decision,
  a scenario, a feedback or a directory. The reason for the todo is there, and
  the paragraph is only its next step. Read those entries whole.

  A todo that serves a decision is usually that entry's **Wrong if** in one
  sentence. The entry itself says what evidence would settle it. A todo that
  serves a requirement says what must hold, and often names the test that would
  hold it.

* **What the code does now**, not what the todo says it does. Read the class,
  the check or the corpus the step names, and the test that covers it today. In
  honest words, most steps here say: "the assertion this needs is next to one
  that already exists". That assertion decides the shape of the change.

* **What the records say about the code the todo touches.**
  ``bin/cli entries:lookup <path>`` answers that in one call. It lists the
  decisions and requirements that name the classes at that path. It also lists
  the test classes that name them, with the entries they hold.

  The command finds only what somebody wrote in backticks. An entry that
  describes the behaviour without the class name is not in the answer. So an
  empty answer does not mean that nobody decided anything.

* **What the repository already established.** Look at the archived feedback,
  the recorded run and the decision that touched the same ground. A question
  this repository answered three weeks ago has its answer in a file. A second
  decision on it gives two entries with different things to say about one
  subject.

Correct the todo first
----------------------

Where the read shows that the todo is wrong, correct the file **first**. Write
the reason into the file, not into a session that is about to end. A todo is
wrong where its step is already done, is impossible here, is two steps, or
serves something that no longer stands. The queue already has that rule: a
change to the order of the work goes into the todo file before the work starts.

Say what the read found before any change, and what it changed about the plan. A
session that silently does other work than the one it got leaves a queue behind.
That queue no longer describes the repository for the next session. It also
leaves whoever queued the todo unable to disagree with the substitution. That
chance to disagree is the only reason the file is prose at all.

.. _working-a-todo-settled:

Settle questions from a source
------------------------------

Others act on what a session produces here, with no check. A statement in
``knowledge/`` reaches a session in somebody else's project. A skill is a file
that no release of this server corrects. A requirement binds the next author,
and nobody reopens a decision until it fails. In all four, an established answer
and a remembered one look the same: same sentence, same confidence. The
remembered one is wrong from the day the thing it describes moved.

So research a gap that opens during a todo, and do not fill it from memory. The
source depends on what the answer is about:

* **TYPO3 itself.** Read the core checkouts below ``.checkouts/`` first. They
  are the code the statement is about, and a session can read both sides of a
  version boundary in them. ``bin/cli checkouts:update`` makes them current.

  Then read the official documentation at the versions in play. Then ask this
  server: ``typo3_documentation_lookup``, ``typo3_changelog_lookup``,
  ``typo3_hint_lookup``. Those answers also show fastest that a question already
  has its answer and needs no statement at all.

* **A tool from outside this repository**, such as Composer, DDEV, the SDK, a CI
  runner or a registry. Read its own documentation and its own release notes.
  What a tool does by default is exactly the fact that changes after somebody
  writes it down here. The version in ``composer.lock`` says which behaviour is
  in play.

* **This server.** Run the query again against the server as it is now. A
  feedback, a recorded run and a quoted answer are evidence about a version that
  may no longer exist. A session that reads one of them instead of a run gives a
  fixed problem a second fix.

* **The practice a skill describes.** :doc:`../contributing/writing-a-skill`
  already describes that research, and it has the same shape.

This page stands against an answer from recall. An open question is a legitimate
result. Where nothing here can settle a question, the session records it as a
result. Examples are a question that needs an installation, a run in another
environment, or a version nothing has checked out.

Then the todo shrinks to the part that is still open. The requirement says
``not guarded``, and the decision says what the evidence would have to be. All
three are legitimate states this repository already has.

Ask what has no source
----------------------

A question about the code, the version, a tool's behaviour or the current
practice has a source. A session that finds the source can
:ref:`settle it <working-a-todo-settled>`. A question about what somebody
**wants** has no source. Examples:

* which of two shapes the change takes where both hold
* whether a step is worth what it costs
* what the todo meant where its paragraph reads two ways
* whether a finding is the one the ask was for

Nothing in the repository answers those, and more reads will not either. The
person who queued the todo is one message away.

So ask where the research runs out and the choice would change the build. Ask
**before** the change, not in the commit that presents it. A session that
silently picks one meaning has not saved anybody the question. It has moved the
question to after the work, where the answer costs a rewrite instead of a
sentence. Ask too for a step that turns out to be two, and for an option the
todo did not consider. Ask for a conflict between what the todo says and what
the code shows.

The ask leaves only the decision to the person:

1. Say what you read and what it showed.
2. Name the two or three options, with what each one costs.
3. Recommend one and say why.

"What do you want here?" leaves the person to do the read again.

**One answer is to put the todo back**, and the session offers it before anybody
asks for it. The person asked may not have the answer either. Then a todo put
back is as good an outcome as the work itself.

After the question
~~~~~~~~~~~~~~~~~~

Where the todo is still workable once the question is out of the way, it stays
in the queue. **The priority says when.** Lower it where the session judges that
something else goes first, and leave it where nothing else does.
``bin/cli todo:next`` prints the first queued todo and knows nothing of a
blocked one. So a todo whose priority nobody changed goes to the next session
too.

That is correct where the todo really is the next thing. Where it is not, the
fix is a word somebody wrote and can dispute. A place a command moved it to is
no such word.

Where nothing can happen until somebody answers, move the todo to
``todo/waiting/`` instead. Its ``waitingOn:`` key holds the question. That
difference is why the state exists. Among the workable todos, such a todo reads
as ordinary work while it waits on a person. Every session that reaches it
derives the same question again.

No session gets a waiting todo. A recurring todo of its own brings the question
back: every seven days, ``bin/cli todo:waiting`` prints what waits. The answer
moves a todo back into the queue.

Either way, the todo keeps its file. The session leaves it ready for a cold
start. Its paragraph holds the open question in the words of the ask, and what
the read already established. A question asked once and answered nowhere costs
one session. A question derived again each time the todo comes up costs every
session.

Put what comes back into the file it belongs in:

* the todo, where the answer changes the order, the step or what the todo waits
  on
* ``decisions/``, where somebody will later want the reason for the answer

An answer that lives only in the conversation ends with the session. So does a
deferral nobody wrote down.

What the commit leaves
----------------------

The commit message says what you read and what it showed. That is not a report
of effort. The next session's cheapest source is a commit that says where the
answer came from:

* A statement bound to a version names the checkout it came from.
* A decision corrected in place names what disproved it.
* A question that stayed open names what would close it.
* An answer from the person who queued the todo says so.

That is the one part no file behind the commit can supply again.

The same commit leaves `todo/ <../../todo/readme.md>`_ true. There are four
cases, and ``bin/cli todo:next`` prints those that apply to its todo:

* **Delete** a finished **queued** todo. By then, what it established is in
  ``requirements/``, and the commit is the record that it happened.

* **Trim** a todo that turns out half done to the part that remains, and rewrite
  its next concrete step. A todo nobody can start from is worse than none. "Half
  done" includes the half that waits on an answer.

  A todo put back unstarted is the same case, with one addition. It keeps its
  file, gets the open question, and takes the priority the session now believes
  it has. Otherwise ``next`` gives it to the next session unchanged.

* **Move** a todo nobody can start until somebody answers to ``waiting/``, with
  the question in its head. No session gets it, so whoever can answer has to
  find the question readable there.

* A **recurring** todo never goes. What it settles goes where that lives. A todo
  measured in days gets today's date in its ``checked:``. That date alone keeps
  five sessions in one afternoon from the same question five times.

From then on, only the commit remembers why the session took that step.

What no check holds
-------------------

No check can hold anything on this page. A todo worked from the checkouts and
the manuals looks exactly like one worked from memory: same diff, same message.
In both, the sentence that turns out false is the confident one. A choice made
silently looks exactly like one with nothing to choose.

What can happen has happened. The page exists, and ``bin/cli todo:next`` names
it with every todo it prints.
`R-FBK-009 <../../requirements/feedback/fbk-009-a-todo-is-worked-from-what-was-read.md>`_
states it with the reason. Whether the read happened is up to the session, and
only the session will ever know.
