Working a todo
==============

``bin/cli todo:next`` hands over one todo, and what happens between the read of
it and the commit is the work. This page is that part alone. What is due, what
recurs, and what a finished todo leaves behind is in :doc:`index`; nothing here
repeats it.

The todo is not the instruction it looks like. Its paragraph is one session's
belief about where to start, written before the work it describes, by a session
that has ended and cannot answer. It is the best start the repository has and it
is still a claim. The file it names may have moved, the check it proposes may
already exist. The thing it says is absent may have arrived in the four commits
since. A session that opens with an edit has tested none of that. It will find
out in the one place where it is expensive, after it has written the change.

Before the first change
-----------------------

* **What it serves.** The ``serves:`` key names a requirement, a decision, a
  scenario, a feedback, a directory, and that is where the todo's reason lives.
  The paragraph is only its next step. Read them whole. A todo that serves a
  decision is usually that entry's **Wrong if** in one sentence, and the entry
  itself says what evidence would settle it. A todo that serves a requirement
  says what must hold, and often names the test that would hold it.
* **What the code does now**, rather than what the todo says it does. The read
  is the class, the check or the corpus the step names, plus the test that
  covers it today. The honest form of most steps here is "the assertion this
  needs is next to one that already exists". That assertion decides the shape of
  the change.
* **What the records say about the code it touches.**
  ``bin/cli entries:lookup <path>`` answers that in one call. The decisions and
  requirements that name the classes at that path, and the test classes that
  name them with the entries they hold. What it finds is what somebody wrote in
  backticks. An entry that describes the behaviour without the class name is not
  in the answer. So a quiet one is not a statement that nobody decided anything.
* **What was already established.** The archived feedback, the recorded run, the
  decision that touched the same ground. A question this repository answered
  three weeks ago has its answer in a file. A second decision on it is how two
  entries end up with different things to say about one subject.

Judged, not executed
--------------------

The read exists for action. When it says the todo is wrong, correct the file
**first**. Write the reason into it rather than into a session that is about to
end. Wrong means the step is already done, is impossible here, is two steps, or
serves something that no longer stands. That is the rule the order already has.
A change of order goes into the file before the work starts.

Say what the read found before any change, and say what it changed about the
plan. A session that silently substitutes a different piece of work for the one
it got leaves a queue behind. That queue no longer describes the repository for
the next session. It leaves whoever queued the todo unable to disagree with the
substitution, which is the only reason the file is prose in the first place.

.. _working-a-todo-settled:

Settled, not recalled
---------------------

What comes out of here gets action with no check. A statement in ``knowledge/``
reaches a session in somebody else's project, and a skill is a file no release
of this server corrects. A requirement is what holds the next author, and a
decision is what nobody reopens until it fails. An established answer and a
remembered one look the same in all four, same sentence, same confidence. The
second is wrong from the day the thing it describes moved.

So a gap that opens during a todo gets research rather than a fill. Where the
answer is:

* **about TYPO3 itself** — the core checkouts under ``.checkouts/`` come first.
  They are the code the statement is about and a session can read both sides of
  a version boundary in them. ``bin/cli checkouts:update`` is what makes them
  current. Then the official documentation at the versions in play, and this
  server's own answers, ``typo3_documentation_lookup``,
  ``typo3_changelog_lookup``, ``typo3_hint_lookup``. Those are also the fastest
  way to find that a question already has its answer and needs no statement at
  all.
* **about a tool this repository does not own**, Composer, DDEV, the SDK, a CI
  runner, a registry: its own documentation and its own release feedback. What a
  tool does by default is precisely the fact that moves after somebody wrote it
  down here. The version in ``composer.lock`` is what says which behaviour is
  the one in play.
* **about this server** — the query is re-run against the server as it is now. A
  feedback, a recorded run and a quoted answer are all evidence about a version
  that may no longer exist. A read of one instead of a run is how a fixed
  problem gets a second fix.
* **about the practice a skill describes** — that research is already written
  down, in :doc:`../contributing/writing-a-skill`, and it is the same shape.

An answer from recall is the failure this page stands against. An open question
is not. Where nothing here can settle it, that is a result and the session
records it as one. That is a question that needs an installation, a run in
another environment, a version nothing has checked out. The todo shrinks to the
part that is still open, the requirement says ``not guarded``, the decision says
what the evidence would have to be. All three are legitimate states this
repository already carries.

Asked, not guessed
------------------

Not every question has a source. A question about the code, the version, a
tool's behaviour or the current practice does, and to find it is to
:ref:`settle it <working-a-todo-settled>`. A question about what somebody
**wants** does not. Which of two shapes the change should take when both hold,
and whether a step is worth what it costs. What the todo meant where its
paragraph reads two ways, and whether a finding is the one the ask was for.
Nothing in the repository answers those, so no amount of further reads will —
and the person who queued the todo is one message away.

So where the research runs out and the choice would change the build, ask. Ask
**before** the change rather than in the commit that presents it. A session that
picks one read silently has not saved anybody the question. It has moved it to
after the work, where the answer costs a rewrite instead of a sentence. The same
holds for a step that turns out to be two, and for an option the todo did not
consider. Also for a conflict between what the todo says and what the code
shows.

The ask is the decision, not the work. Say what you read and what it showed,
name the two or three options with what each one costs, recommend one and say
why. "What do you want here?" hands the reading back.

**The todo put back is one of the answers**, and the session offers it rather
than waits for it. The person asked may not have the answer either, and that is
not a smaller outcome than the work itself.

Where the todo is still workable once the question is out of the way, it stays
in the queue. **The priority is what says when.** Lower it where the session
judges that something else should go first, leave it where it should not.
``bin/cli todo:next`` hands over the first queued todo and has no notion of a
blocked one. So a todo whose priority nobody touched goes to the next session
too. That is correct where it really is the next thing. Where it is not, the fix
is a word somebody wrote and can dispute rather than a place a command moved it
to.

Where nothing can happen until somebody answers, it moves to ``todo/waiting/``
instead and carries the question in its ``waitingOn:``. That is the difference
the state exists for. Parked among the workable ones such a todo reads as
ordinary work while it waits on a person. Every session that reaches it derives
the same question again. No session gets it, and the way back is a todo of its
own. Every seven days, ``bin/cli todo:waiting`` prints what waits and the
questions come up again. The answer is what moves one back into the queue.

Either way it keeps its file, and the session's job is to leave it startable
cold. The open question in its paragraph in the words of the ask, and what the
read already established. A question asked once and answered nowhere costs one
session; one re-derived every time the todo surfaces costs every session.

What comes back goes into the file it belongs in. The todo when it changes the
order, the step or what it waits on. ``decisions/`` when it is the kind of
answer somebody will want the reason for later. An answer that lives only in the
conversation is gone when the session ends, and so is a deferral nobody wrote
down.

What the commit leaves
----------------------

What you read, and what it showed. Not as a report of effort, but because the
next session's cheapest source is a commit that says where the answer came from.
A statement bound to a version names the checkout it came from. A decision
corrected in place names what disproved it. A question that stayed open names
what would close it. An answer that came from the person who queued the todo
says so. It is the one part no file behind the commit can supply again.

The same commit leaves `todo/ <../../todo/readme.md>`_ true, which is four cases
and ``bin/cli todo:next`` prints the one that applies:

* A finished **queued** todo goes: **delete** it. What it established is in
  ``requirements/`` by then and the commit is the record that it happened.
* One that turns out half done shrinks to the part that remains, with the next
  concrete step rewritten: **trim** it. A todo nobody can start from is worse
  than none, and "half done" includes the half that waits on an answer. One put
  back unstarted is the same case, with one addition. It keeps its file, gains
  the open question, and carries whichever priority the session now believes it
  has. ``next`` would otherwise hand it to the next session unchanged.
* One nobody can start at all until somebody answers **moves to ``waiting/``**,
  with the question in its head. No session gets it, so the question has to be
  readable there by whoever can answer it.
* A **recurring** one never goes. What it settles goes where that lives, and one
  measured in days gets today's date in its ``checked:``. That date is the whole
  of what keeps five sessions in an afternoon from the same question five times.

From then on the commit is the only thing that remembers why the step was the
one taken.

What nothing holds
------------------

No check can hold anything on this page. A todo worked from the checkouts and
the manuals has exactly the shape of one worked from memory, same diff, same
message. The sentence that turns out false is the confident one in both. A
choice made silently has exactly the shape of one that had nothing to choose.
What can happen has happened. The page exists, ``bin/cli todo:next`` names it
with every todo it hands over, and
`R-FBK-009 <../../requirements/feedback/fbk-009-a-todo-is-worked-from-what-was-read.md>`_
carries it with the reason. Whether the read happened is the session's, and the
session is the only one who will ever know.
