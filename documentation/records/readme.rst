:navigation-title: Records

The records, and where they live
================================

Four files hold four different kinds of thing, and their separation is what
keeps any of them readable. `AGENTS.md <../../AGENTS.md>`_ has the rules; this
is what each one is for and how the work moves between them.

The pages that carry one of those steps out:

* :doc:`judging` — what a session asks of one open feedback, in which order and
  on what evidence.
* :doc:`working-a-todo` — what a session reads before it changes the todo
  ``bin/cli todo:next`` handed over, and what the queue says afterwards.
* :doc:`working-todos-in-parallel` — how several sessions get different todos,
  where each writes, and how it comes back.
* :doc:`requirements` and :doc:`writing-a-requirement` — what one states, and
  the sections it consists of.
* :doc:`decisions` and :doc:`writing-a-decision` — what one carries that a
  commit message cannot, and what a later session adds to its foot.
* :doc:`forward-runs` — how to run a forward review, judge it, and read one that
  stopped without an error.
* :doc:`asking-for-a-debrief` — the prompt a session in somebody else's agent
  gets once the work ends, and why it asks what it asks.

Where a session starts
----------------------

.. code-block:: bash

    bin/cli todo:next

One todo, not the queue and not everything nothing has answered for. Context is
not free: a session handed all of it reads for ten minutes and then starts with
a summary of what it read.

Due is two questions. Has the clock come round, which the todo's ``every:``
answers. ``session``, or a number of days, so five sessions in an afternoon do
not ask the same question five times. And is there anything to do, which the
todo's ``run:`` command answers with a nonzero exit when it found work. The
sighting stops to be the next thing the moment the last entry has a name, and
nobody edits a todo to say so. A feedback or an unresolved entry has a claim on
that judgement. A todo that takes it on, or the sentence that says why it stays
as it is. Not the work itself, which is what the queue is for.

The queue comes first
---------------------

For a while it did not, and the effect is worth a record. A todo that recurs
every session is due for as long as anything waits for a judgement, and ``next``
asked the recurring ones first. Feedback arrives from every session everywhere
while one session closes a handful. So every session opened on the same sighting
and no session reached the queue behind it. Entries sat in it untouched for as
long as ``feedback/`` was not empty. That is always.

The order is now: what has a clock, then the queue, then the sightings once the
queue is empty. It follows from what a judgement is. To judge a feedback is to
decide whether it becomes work. The queue is the work that decision produced.

Since 2026-08-02 the feedback are *in* the queue rather than behind it. One card
each, written as the feedback arrives at ``low``, which is below everything
somebody has judged to be worth more. The order stays and what enforces it has
moved. The priority does it now, where a group boundary did it before. What is
still behind the queue is the sighting of what nothing answers for, and a
session reaches it when the queue runs dry. That now means that nothing decided
remains *and* nothing has arrived without a judgement. A sighting left in place
in order to judge more feedback decides twice and does nothing. The pile it
decides over grows faster than any session can read it.

The second half of the same problem is the size of the read, and the board is
what solves it now. Every open feedback has one card, and ``bin/cli todo:next``
hands over **one** of them, like any other todo. A fresh card is ``low``, so the
oldest feedback without a judgement comes up once the decided work ends. A
session that also has work of its own can re-run one query. It cannot re-run
sixty-seven, and a session with all of them in hand closes whatever is easiest.
The portion was five until 2026-08-02, cut for a reader who could then only find
the judgements in the commit that made them. What carries that instead is
``decisions/``, which is where a judgement goes now, see :doc:`judging` and
`D-FBK-012 <../../decisions/feedback/fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md>`_.
``bin/cli feedback:list`` is still the whole of it, for whoever wants the
overview.

What writes the card is ``typo3_feedback_record``, in the same call that stores
the report. The feedback and its card land together, wherever the session that
records it stood. Nothing has to run afterwards for the board to be right, see
`D-FBK-045 <../../decisions/feedback/fbk-045-a-feedback-is-queued-by-the-call-that-records-it.md>`_.

Nothing writes one afterwards. A ``bin/cli todo:sync`` did until 2026-08-14, run
from a pre-commit hook on any commit that touched ``feedback/``. Both went with
the write that made them necessary. A repair kept for a case the record no
longer produces is a second way for a card to come about. The hook was the half
that only ever ran in this checkout
(`D-FBK-022 <../../decisions/feedback/fbk-022-a-feedback-brings-its-card-in-the-commit-that-brings-it-in.md>`_,
revoked). What remains is the report. ``bin/cli todo:check`` and CI name an open
feedback no todo answers for, one added by hand, or one whose card went while it
stayed open. The repair is a card written into ``todo/open/``.

What ``next`` can never do is a run of a feedback's own query against the server
as it is now. A feedback is evidence about a version of this server that may no
longer exist, and that read is the session's.

Working the todo
----------------

Everything on this page is about the order of the work. One page of its own
covers what a session reads before it changes the todo at the front. It says why
the session judges the step rather than executes it. Also where it settles a
question the work turns on instead of recalls it. Also what it asks because
nothing here can answer it. That page is :doc:`working-a-todo`, which
``bin/cli todo:next`` names with every todo it hands over.

Keeping the queue current
-------------------------

* A change of order goes into the file **before** the work starts, so the reason
  exists there rather than in a session that has ended.
* New work found along the way becomes a todo that names what it serves. If it
  serves nothing yet, it is an idea and belongs in the feedback that had it.

What the commit that finishes, trims or puts back a todo leaves behind is the
last section of :doc:`working-a-todo`. How to write a todo is
`todo/readme.md <../../todo/readme.md>`_ itself.

What a feedback carries
-----------------------

A feedback arrives because somebody asked for one. A session in a client this
repository cannot read reports its own run, and :doc:`asking-for-a-debrief` is
the prompt it gets after the work.

Each one names the work directory the session that left it ran in, as
``directory:`` in its front matter. So a check of a gap runs against the project
it turned up in rather than against whatever is at hand. It is the same
directory the stdio entrypoint hands to instance discovery; a feedback left over
an endpoint that has none carries none. That directory is also why the answer
reports the feedback back as an absolute path. It goes into this server's
checkout and not into the project the session is in. A relative path sent to a
caller somewhere else reads as a write that failed.

It also carries ``model:`` — the model that left it, as it named itself. Much of
what arrives is about what a session did rather than about what an answer said,
and that is one model's behaviour. Without attribution, two models' habits are
one undifferentiated report and nobody can work either off. The write never
fails on it. A model that does not know its own identifier has to send
``unknown`` rather than an invented one. So a feedback nobody can attribute says
so.

Both feedback tools exist **only in a standalone checkout**. Installed as a
Composer dependency the package lives in ``vendor/``, where the next
``composer install`` would lose anything written there. There the server stays
strictly read-only and neither tool appears in ``tools/list``.

.. image:: ../images/feedback-loop.svg
    :zoomable:
        :alt: A real task exposes a gap and records one feedback. The feedback moves
          through the queue into a guarded improvement. Another real task
          verifies it.

Working a feedback off
----------------------

.. code-block:: bash

    bin/cli feedback:archive feedback/2026-07-31-…-the-lookup-found-nothing.md

That moves it to ``feedback/archive/`` and stamps it ``closed``. The commit that
moved it is readable. ``typo3_feedback_list`` with ``status="closed"`` lists the
archived feedback with the commit subject that closed each one, which is what
the agent that reported it can see. Write the subject so it answers "what came
of my feedback".

One feedback per commit where possible. When one change closes several, archive
all of them in that commit, one call, since they are one commit. Mention them in
the commit body.

The archive
-----------

Nobody deletes a feedback anywhere. What it holds is a session's report about
this server. Which skill activated, which calls the task needed, what it had to
establish from the checkout or from its own knowledge instead. That is evidence
about this server that nothing else in the repository has. The requirement it
established says what must be true from now on. It does not say what a session
in somebody else's agent ran into on a Tuesday. That is the half worth a read
when the next one runs into it too.

The archive is also what makes the closed half of ``typo3_feedback_list``
usable. A feedback read from a commit was a filename and a subject, with the
category, the tools and the model gone with the file. So only the open half
could answer a query about one tool. Both halves are now the same files read the
same way.

The feedback worked off before the archive existed came back into it from the
commits that deleted them. Those carry the commit that closed them in their own
front matter: ``closed:``, ``commit:`` and ``subject:``. They all moved in one
commit, and that move says nothing about any of them. The commit that archived a
feedback since then answers for it.

Where the answer goes
---------------------

Archiving the feedback takes the question out of the open ones, and the commit
message records the answer. What outlives both goes to four places, and only
``todo/`` is this workflow's. ``requirements/``, ``decisions/`` and the
documents are the record this repository keeps whether or not a feedback
produced an entry. How to write each one has a page of its own:
:doc:`writing-a-requirement` and :doc:`writing-a-decision`.

* ``requirements/`` — what must be true from now on. A feedback is a question.
  The requirement it established has to hold while everything around it changes.
  So it stands with what holds it to that: a test, or ``not guarded``. An
  accepted requirement nobody has implemented yet is in the same group, marked
  **open**, decided and not done. Add the entry in the commit that works the
  feedback off. The test declares the entry it holds with ``#[Requirement]``,
  and ``bin/cli requirements:cover`` writes that into the entry. So the two have
  their names in the one commit and in one place. An entry goes only when
  somebody withdraws the requirement.
* ``decisions/`` — what the change rests on. When it rests on an assumption that
  could later turn out wrong, record the assumption. Record what evidence there
  was at the time, and what would show it wrong. One decision is one file, in
  the group its id names, and the entry opens with the decision in one sentence.
  Not every commit earns one; a change nobody would need to reconsider does not.
  When evidence later disproves an assumption, correct the entry in place rather
  than delete it. The wrong assumption is the useful part, because it names
  where the next one is likely to sit.
* ``todo/`` — the order of the work, and where the last session stopped. The
  other files say what must be true, what somebody asked and what somebody
  assumed. None of them says what to do next. A session can end anywhere, and
  the next one starts with a read of this. One todo is one file. It names what
  it serves and what the next concrete step is, and it goes when done rather
  than gets a tick. Where it sits says whether it is the queue, what recurs,
  what waits on an answer nothing here can give, or what stays as reference. A
  queued one carries the priority that decides how soon. That is what
  ``bin/cli todo:next`` reads and what ``bin/cli todo:check`` holds.
* ``knowledge/documents/`` and ``documentation/`` — the thing itself, where the
  other three would describe it instead. What the caller lacked is a document in
  the corpus, which is what this server hands out. What a session at work here
  lacked is a page under ``documentation/``. Which of the two, and which of them
  a judging run may write rather than queue, is :doc:`judging`.

Unresolved entries
------------------

The three states that mean unfinished are legitimate. A principle no test can
hold and a decision nothing has come back about are not defects, so no check may
fail on them. That is exactly why nothing read them for as long as they existed.

``bin/cli unresolved:list`` is that read; ``bin/cli todo:next`` opens with it
and ``bin/cli repository:check`` closes with it. It names every requirement
nothing answers for and says whether a queued todo names it. That comes from
what the queue declares it serves. So the page that lists what deliberately
stays out of the queue does not count as a taken one. It gives the oldest open
decision as the one the repository has moved furthest away from.

Standing on that list is not the problem. An entry with no todo that names it is
a decision nobody has taken. A session owes it that decision, a todo, or the
sentence in ``decisions/`` that says why not.

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
