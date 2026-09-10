Working a todo
==============

``bin/cli todo:next`` hands over one todo, and what happens between reading it
and the commit is the work. This page is that part alone. What is due, what
recurs, and what a finished todo leaves behind is in :doc:`index`; nothing here
repeats it.

The todo is not the instruction it looks like. Its paragraph is one session's
belief about where to start, written before the work it describes was done, by a
session that has ended and cannot be asked. It is the best starting point the
repository has and it is still a claim: the file it names may have moved, the
check it proposes may already exist, the thing it says is missing may have
arrived in the four commits since. A session that opens by editing has tested
none of that, and it will find out in the one place where it is expensive —
after the change is written.

Before the first change
-----------------------

* **What it serves.** The ``serves:`` key names a requirement, a decision, a
  scenario, a feedback, a directory, and that is where the todo's reason lives —
  the paragraph is only its next step. Read them whole. A todo serving a
  decision is usually that entry's **Wrong if** compressed into a sentence, and
  the entry itself says what evidence would settle it; a todo serving a
  requirement says what must hold, and often names the test that would hold it.
* **What the code does now**, rather than what the todo says it does. The
  reading is the class, the check or the corpus the step names, plus the test
  that covers it today — because the honest form of most steps here is "the
  assertion this needs is next to one that already exists", and that assertion
  decides the shape of the change.
* **What is written about the code it touches.**
  ``bin/cli entries:lookup <path>`` answers that in one call: the decisions and
  requirements that name the classes at that path, and the test classes that
  name them with the entries they hold. What it finds is what somebody wrote in
  backticks — an entry describing the behaviour without naming the class is not
  in the answer, so a quiet one is not a statement that nothing was decided.
* **What was already established.** The archived feedback, the recorded run, the
  decision that touched the same ground. A question this repository answered
  three weeks ago is answered in a file, and re-deciding it is how two entries
  end up saying different things about one subject.

Judged, not executed
--------------------

The reading exists to be acted on. When it says the todo is wrong — the step is
already done, is impossible here, is two steps, or serves something that no
longer stands — the file is corrected **first**, and the reason is written into
it rather than into a session that is about to end. That is the rule the order
already has: a change of order is written down before the work starts.

Say what the reading found before changing anything, and say what it changed
about the plan. A session that silently substitutes a different piece of work
for the one it was handed leaves the next session a queue that no longer
describes the repository, and leaves whoever queued the todo unable to disagree
with the substitution — which is the only reason the file is prose in the first
place.

.. _working-a-todo-settled:

Settled, not recalled
---------------------

What is produced here is acted on without being checked: a statement in
``knowledge/`` reaches a session in somebody else's project, a skill is a file
no release of this server corrects, a requirement is what the next author is
held to, a decision is what nobody reopens until it fails. An answer that was
established and one that was remembered are indistinguishable in all four — same
sentence, same confidence — and the second is wrong from the day the thing it
describes moved.

So a gap that opens while working a todo is researched rather than filled in.
Where the answer is:

* **about TYPO3 itself** — the core checkouts under ``.checkouts/`` come first,
  because they are the code the statement is about and both sides of a version
  boundary are readable in them; ``bin/cli checkouts:update`` is what makes them
  current. Then the official documentation at the versions in play, and this
  server's own answers — ``typo3_documentation_lookup``,
  ``typo3_changelog_lookup``, ``typo3_hint_lookup`` — which are also the fastest
  way to find that a question is already answered and needs no statement at all.
* **about a tool this repository does not own** — Composer, DDEV, the SDK, a CI
  runner, a registry: its own documentation and its own release feedback. What a
  tool does by default is precisely the fact that moves after somebody wrote it
  down here, and the version in ``composer.lock`` is what says which behaviour
  is the one in play.
* **about this server** — the query is re-run against the server as it is now. A
  feedback, a recorded run and a quoted answer are all evidence about a version
  that may no longer exist, and reading one instead of running it is how a fixed
  problem gets fixed twice.
* **about the practice a skill describes** — that research is already written
  down, in :doc:`../contributing/writing-a-skill`, and it is the same shape.

Answering from recall is the failure this is written against; leaving a question
open is not. Where it cannot be settled here — it needs an installation, a run
in another environment, a version nothing has checked out — that is a result and
it is recorded as one: the todo is trimmed to the part that is still open, the
requirement says ``not guarded``, the decision says what the evidence would have
to be. All three are legitimate states this repository already carries.

Asked, not guessed
------------------

Not every question has a source. A question about the code, the version, a
tool's behaviour or the current practice does, and finding it is
:ref:`settling it <working-a-todo-settled>`. A question about what is **wanted**
does not: which of two shapes the change should take when both hold, whether a
step is worth what it costs, what the todo meant where its paragraph reads two
ways, whether a finding is the one that was being asked for. Nothing in the
repository answers those, so no amount of further reading will — and the person
who queued the todo is one message away.

So where the research runs out and the choice would change what is built, ask,
and ask **before** the change rather than in the commit that presents it. A
session that picks one reading silently has not saved anybody the question: it
has moved it to after the work, where the answer costs a rewrite instead of a
sentence. The same holds for a step that turns out to be two, an option the todo
did not consider, and a conflict between what the todo says and what the code
shows.

What is asked is the decision, not the work. Say what was read and what it
showed, name the two or three options with what each one costs, recommend one
and say why. "What do you want here?" hands the reading back.

**Putting the todo back is one of the answers**, and it is offered rather than
waited for. The person asked may not have the answer either, and that is not a
smaller outcome than doing the work.

Where the todo can still be worked once the question is out of the way, it stays
in the queue and **the priority is what says when** — lowered where the session
judges that something else should go first, left alone where it should not.
``bin/cli todo:next`` hands over the first queued todo and has no notion of one
being blocked, so a todo whose priority nobody touched is handed to the next
session too. That is correct where it really is the next thing, and where it is
not, the fix is a word somebody wrote and can be disagreed with rather than a
place a command moved it to.

Where nothing can be done until somebody answers, it moves to ``todo/waiting/``
instead and carries the question in its ``waitingOn:``. That is the difference
the state exists for: parked among the workable ones such a todo reads as
ordinary work while it is actually waiting on a person, and every session that
reaches it re-derives the same question. No session is offered it, and the way
back is a todo of its own — every seven days, ``bin/cli todo:waiting`` prints
what is blocked and the questions get asked again. The answer is what moves one
back into the queue.

Either way it keeps its file, and the session's job is to leave it startable
cold: the open question written into its paragraph in the words it was asked in,
and what the reading already established. A question asked once and answered
nowhere costs one session; one re-derived every time the todo surfaces costs
every session.

What comes back is written into the file it belongs in — the todo when it
changes the order, the step or what it is waiting on, ``decisions/`` when it is
the kind of answer somebody will want the reason for later — because an answer
that lives only in the conversation is gone when the session ends, and so is a
deferral nobody wrote down.

What the commit leaves
----------------------

What was read, and what it showed — not as a report of effort, but because the
next session's cheapest source is a commit that says where the answer came from.
A statement bound to a version names the checkout it was read in; a decision
corrected in place names what disproved it; a question that stayed open names
what would close it; an answer that came from the person who queued the todo
says so, because it is the one part no file behind the commit can be re-read
for.

The same commit leaves `todo/ <../../todo/readme.md>`_ true, which is four cases
and ``bin/cli todo:next`` prints the one that applies:

* A **queued** todo that is finished is **deleted**. What it established is in
  ``requirements/`` by then and the commit is the record that it happened.
* One that turns out half done is **trimmed** to the part that is left, with the
  next concrete step rewritten. A todo nobody can start from is worse than none,
  and "half done" includes the half that is waiting on an answer. One put back
  unstarted is the same case, with one addition: it keeps its file, gains the
  open question, and carries whichever priority the session now believes it has,
  because ``next`` would otherwise hand it to the following one unchanged.
* One that cannot be started at all until somebody answers **moves to
  ``waiting/``**, with the question in its head. It is offered to no session, so
  the question has to be readable there by whoever can answer it.
* A **recurring** one is never deleted: what it settles goes where that is kept,
  and one measured in days gets today's date in its ``checked:`` — that date is
  the whole of what keeps five sessions in an afternoon from asking the same
  question five times.

From then on the commit is the only thing that remembers why the step was the
one taken.

What nothing holds
------------------

Nothing on this page can be checked. A todo worked from the checkouts and the
manuals is shaped exactly like one worked from memory — same diff, same message,
and the sentence that turns out false is the confident one in both. A choice
made silently is shaped exactly like one that had nothing to choose. What can be
done is done: the page exists, ``bin/cli todo:next`` names it with every todo it
hands over, and
`R-FBK-009 <../../requirements/feedback/fbk-009-a-todo-is-worked-from-what-was-read.md>`_
carries it with the reason. Whether the reading happened is the session's, and
the session is the only one who will ever know.
