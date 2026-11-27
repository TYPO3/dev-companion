:navigation-title: Records

The records, and where they live
================================

This page covers the four kinds of record this repository keeps: feedback,
requirements, decisions and todos. It says what each one is for and how the work
moves between them. `AGENTS.md <../../AGENTS.md>`_ has the rules.

Each kind lives in files of its own. That separation keeps every one of them
readable.

These pages describe one step each:

.. grid::

    .. card:: :doc:`working-a-todo`

        What a session reads before it changes the todo that
        ``bin/cli todo:next`` gave it, and what the queue says afterwards.

    .. card:: :doc:`working-todos-in-parallel`

        How several sessions get different todos, where each writes, and how its
        work comes back.

    .. card:: :doc:`requirements`

        What a requirement states, and what its three states promise a reader.

    .. card:: :doc:`writing-a-requirement`

        The sections of a requirement.

    .. card:: :doc:`decisions`

        What a decision records that a commit message cannot.

    .. card:: :doc:`writing-a-decision`

        The sections of a decision, and what a later session adds at its foot.

    .. card:: :doc:`forward-runs`

        How to run a forward review, judge it, and read one that stopped without
        an error.

    .. card:: :doc:`judging`

        What a session asks of one open feedback, in which order and on what
        evidence.

    .. card:: :doc:`asking-for-a-debrief`

        The prompt a session in somebody else's agent gets when the work ends,
        and why it asks what it asks.


Where a session starts
----------------------

.. code-block:: bash

    bin/cli todo:next

The command prints one todo. It does not print the queue, or everything that
nothing has answered for. Context is not free: a session that gets all of it
reads for ten minutes and then starts from a summary of what it read.

A todo is due when both of these hold:

* **The clock has come round.** The ``every:`` of the todo says when, as
  ``session`` or as a number of days. So five sessions in one afternoon do not
  ask the same question five times.

* **There is something to do.** The ``run:`` command of the todo says so. It
  exits nonzero when it finds work.

The sighting is the recurring todo that judges what nothing has answered for. It
stops being the next todo as soon as the last entry has a name. Nobody edits a
todo to say so.

Each feedback and each unresolved entry gets a judgement from the sighting. That
judgement is a todo that takes it on, or the sentence that says why it stays as
it is. The sighting does not do the work itself. The queue holds the work.

The queue comes first
---------------------

``next`` gives out work in this order:

1. What has a clock.
2. The queue.
3. The sighting, once the queue is empty.

The order follows from what a judgement is. To judge a feedback is to decide
whether it becomes work. The queue is the work that decision produced.

Every feedback is in the queue, as a card of its own. The card arrives at
``low``, below everything somebody has judged to be worth more. So the priority
keeps the order.

Only the sighting of what nothing answers for stays behind the queue. A session
reaches it when the queue runs dry. Then nothing decided remains, *and* nothing
has arrived without a judgement. A sighting left in place to judge more feedback
decides twice and does nothing. The pile it decides over grows faster than any
session can read it.

``bin/cli todo:next`` gives out **one** card, like any other todo. A fresh card
is ``low``, so the oldest feedback without a judgement comes up once the decided
work ends. A session that also has work of its own can re-run one query. It
cannot re-run sixty-seven. A session with all of them in hand closes whatever is
easiest.

A judgement goes into ``decisions/``. :doc:`judging` and
`D-FBK-012 <../../decisions/feedback/fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md>`_
say how. ``bin/cli feedback:list`` still prints all open feedback, for whoever
wants the overview.

``typo3_feedback_record`` writes the card, in the same call that stores the
report. The feedback and its card land together, wherever the session that
records it stood. Nothing has to run afterwards for the board to be right —
`D-FBK-045 <../../decisions/feedback/fbk-045-a-feedback-is-queued-by-the-call-that-records-it.md>`_.

Nothing writes a card afterwards. ``bin/cli todo:check`` and CI report an open
feedback that no todo answers for. That is a feedback somebody added by hand, or
one whose card went while it stayed open. The repair is a card that somebody
writes into ``todo/open/``.

``next`` never re-runs the query of a feedback against the server as it is now.
A feedback is evidence about a version of this server that may no longer exist.
That read is the work of the session.

How the order came about
~~~~~~~~~~~~~~~~~~~~~~~~

For a while the queue did not come first, and the effect is worth a record. A
todo that recurs every session is due for as long as anything waits for a
judgement. ``next`` asked the recurring ones first. Feedback arrives from every
session everywhere, while one session closes a handful. So every session opened
on the same sighting, and no session reached the queue behind it.

Entries sat in the queue untouched for as long as ``feedback/`` was not empty.
That is always.

Since 2026-08-02 the feedback are *in* the queue rather than behind it. The
order stayed, and what enforces it moved. The priority does it now, where a
group boundary did it before.

Until 2026-08-02 the portion was five feedback at a time. It was cut for a
reader who could then find the judgements only in the commit that made them.
``decisions/`` now holds those judgements instead.

A ``bin/cli todo:sync`` wrote cards until 2026-08-14. A pre-commit hook ran it
on any commit that touched ``feedback/``. Both went with the write that made
them necessary. A repair kept for a case the record no longer produces is a
second way for a card to come about. The hook was the half that only ever ran in
this checkout
(`D-FBK-022 <../../decisions/feedback/fbk-022-a-feedback-brings-its-card-in-the-commit-that-brings-it-in.md>`_,
revoked).

Working the todo
----------------

Everything on this page is about the order of the work. :doc:`working-a-todo`
covers what a session reads before it changes the todo at the front.
``bin/cli todo:next`` names that page with every todo it gives out. The page
says three things:

* Why the session judges the step rather than executes it.

* Where the session settles a question the work turns on, rather than recalls
  it.

* What the session asks, because nothing here can answer it.

Keeping the queue current
-------------------------

* Write a change of order into the file **before** the work starts. Then the
  reason is in the file, not in a session that has ended.

* New work found along the way becomes a todo that names what it serves. Work
  that serves nothing yet is an idea, and it belongs in the feedback that had
  it.

The last section of :doc:`working-a-todo` says what the commit that finishes,
trims or puts back a todo leaves behind.
`todo/readme.md <../../todo/readme.md>`_ says how to write a todo.

What a feedback contains
------------------------

A feedback arrives because somebody asked for one. A session in a client this
repository cannot read reports its own run. :doc:`asking-for-a-debrief` is the
prompt it gets after the work.

Each feedback names the work directory of the session that left it, as
``directory:`` in its front matter. So a check of a gap runs against the project
the gap turned up in, not against whatever is at hand. It is the same directory
the stdio entrypoint gives to instance discovery. A feedback left over an
endpoint that has no such directory names none.

That directory is also why the answer reports the feedback as an absolute path.
The feedback goes into the checkout of this server, not into the project the
session is in. A relative path sent to a caller somewhere else reads as a write
that failed.

Each feedback also names ``model:``, the model that left it, as the model named
itself. Much of what arrives is about what a session did, not about what an
answer said. That is the behaviour of one model. Without the name, the habits of
two models are one mixed report, and nobody can work either off.

The write never fails on ``model:``. A model that does not know its own
identifier sends ``unknown``, never an invented one. So a feedback that nobody
can attribute says so.

Both feedback tools exist **only in a standalone checkout**. As a Composer
dependency, the package lives in ``vendor/``, and the next ``composer install``
would lose anything written there. So there the server stays strictly read-only,
and neither tool appears in ``tools/list``.

.. diagram::
    :zoomable:
    :caption: Evidence stays in the repository instead of leaving with the session.

    flow canvas=960 margin=60
      columns 1.4,1
    title "A gap becomes a tested improvement"
    description "The five steps of the loop, one per row: a real task uses the server, finds a gap, records it as feedback, settles it into a requirement or decision with the check that holds it, and a later task verifies the change. The right column names what the repository keeps at each step, and the emphasis closes the loop from the last step back to the first."

    column 1 "Kept in the repository"
    use "Real task"
      at 0,0
      eyebrow "01 · use"
      body "Ask the server before searching elsewhere."
    find "One gap"
      at 0,1
      eyebrow "02 · find"
      body "Missing, wrong or unhelpful, with proof."
    record "Feedback"
      at 0,2
      eyebrow "03 · record"
      body "One subject, one file, reproducible."
    settle "Improvement"
      at 0,3
      eyebrow "04 · settle"
      body "The change, and the check that holds it."
    verify "Next task"
      at 0,4
      eyebrow "05 · test"
      body "Verify what changed; keep the evidence."
    feedback `feedback/`
      at 1,2
      store
    records `requirements/ · decisions/`
      at 1,3
      store

    use -> find
    find -> record
    record -> settle
    settle -> verify
    record -> feedback
    settle -> records
    verify => use
      via h

Working a feedback off
----------------------

.. code-block:: bash

    bin/cli feedback:archive feedback/2026-07-31-…-the-lookup-found-nothing.md

The command moves the feedback to ``feedback/archive/`` and stamps it
``closed``. The agent that reported it can then read the commit that moved it.
``typo3_feedback_list`` with ``status="closed"`` lists each archived feedback
with the subject of the commit that closed it. So write the subject as the
answer to "what came of my feedback".

Close one feedback per commit where you can. When one change closes several,
archive all of them in that commit, with one call, since they are one commit.
Name them in the commit body.

The archive
-----------

Nobody deletes a feedback anywhere. A feedback holds a session's report about
this server. It says which skill activated and which calls the task needed. It
says what the session had to establish instead, from the checkout or from its
own knowledge. Nothing else in the repository has that evidence about this
server.

The requirement a feedback established says what must be true from now on. It
does not say what a session in somebody else's agent ran into on a Tuesday. That
half is the one worth a read when the next session runs into it too.

The archive also makes the closed half of ``typo3_feedback_list`` usable. A
feedback read from a commit was a filename and a subject. The category, the
tools and the model had gone with the file. So only the open half could answer a
query about one tool. Now both halves are the same files, read the same way.

Feedback from before the archive
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The feedback worked off before the archive existed came back into it from the
commits that deleted them. Each names the commit that closed it in its own front
matter: ``closed:``, ``commit:`` and ``subject:``. They all moved in one commit,
and that move says nothing about any of them. Since then, the commit that
archived a feedback answers for it.

Where the answer goes
---------------------

``feedback:archive`` takes the question out of the open ones, and the commit
message records the answer. What outlives both goes to four places, and only
``todo/`` belongs to this workflow. ``requirements/``, ``decisions/`` and the
documents are the record this repository keeps, whether or not a feedback
produced an entry. :doc:`writing-a-requirement` and :doc:`writing-a-decision`
say how to write each one.

* ``requirements/`` — what must be true from now on. A feedback is a question.
  The requirement it established has to hold while everything around it changes.
  So the requirement names what holds it to that: a test, or ``not guarded``.

  An accepted requirement that nobody has implemented yet is in the same
  directory, marked **open**: decided and not done. Add the entry in the commit
  that works the feedback off. The test declares the entry it holds with
  ``#[Requirement]``, and ``bin/cli requirements:cover`` writes that into the
  entry. So the two get their names in one commit and in one place. An entry
  goes only when somebody withdraws the requirement.

* ``decisions/`` — what the change rests on. When it rests on an assumption that
  could turn out wrong later, record the assumption. Record the evidence there
  was at the time, and what would show the assumption wrong.

  One decision is one file, in the group its id names. The entry opens with the
  decision in one sentence. A change that nobody would need to reconsider gets
  no decision. When evidence later disproves an assumption, correct the entry in
  place, and do not delete it. The wrong assumption is the useful part, because
  it shows where the next one is likely to be.

* ``todo/`` — the order of the work, and where the last session stopped. The
  other files say what must be true, what somebody asked and what somebody
  assumed. None of them says what to do next. A session can end anywhere, and
  the next one reads this first.

  One todo is one file. It names what it serves and the next concrete step. When
  the todo is done, its file goes; nobody ticks it off. The directory it sits in
  says what it is: the queue, what recurs, what waits on an answer nothing here
  can give, or reference. A queued todo has the priority that decides how soon.
  ``bin/cli todo:next`` reads that priority, and ``bin/cli todo:check`` checks
  it.

* ``knowledge/documents/`` and ``documentation/`` — the thing itself, where the
  other three would describe it instead. What the caller lacked is a document in
  the corpus, which is what this server hands out. What a session at work here
  lacked is a page under ``documentation/``. :doc:`judging` says which of the
  two, and which of them a judging run may write rather than queue.

Unresolved entries
------------------

The three states that mean unfinished are legitimate. A principle no test can
hold is not a defect. Neither is a decision that nothing has come back about. So
no check may fail on them, and for that reason nothing read them for as long as
they existed.

``bin/cli unresolved:list`` reads them. ``bin/cli todo:next`` opens with it, and
``bin/cli repository:check`` closes with it. It names every requirement that
nothing answers for, and says whether a queued todo names it. It learns that
from what each todo in the queue declares it serves. So the page that lists what
stays out of the queue on purpose does not count as a todo that takes an entry
on.

``unresolved:list`` names the oldest open decision too, as the one the
repository has moved furthest away from.

An entry on that list is no problem. An entry that no todo names is a decision
nobody has taken. A session makes that decision: a todo, or the sentence in
``decisions/`` that says why not.

.. toctree::
    :hidden:

    working-a-todo
    working-todos-in-parallel
    requirements
    writing-a-requirement
    decisions
    writing-a-decision
    forward-runs
    judging
    asking-for-a-debrief
