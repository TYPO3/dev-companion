Judging a feedback
==================

A feedback is a session's report about this server, left by an agent working
somewhere else. It is not a work item. It names what went wrong from where that
session stood, and what to do about it is a judgement nobody has made yet. This
page is that judgement. What a session asks of a feedback, in which order, on
what evidence, and which of the answers it may give without a question first.

**What the channel is for is deciding what to build.** The sessions that use
this server are the only ones who find out what it does not answer. The corpus
is the only place that knowledge lands. So a judgement that ends in no build has
to earn that as much as one that ends in a new tool. The default is not caution,
it is a decision. Where the evidence is here, a run, a transcript, several
sessions with the same thing to say, the judgement decides. What waits is only
what nobody in this repository can establish.

:doc:`index` says where a feedback lives and what happens to it once a session
has worked it off. This is the step between the two.

The one question
----------------

Every feedback gets the same question, and it is not whether the feedback is
right:

**Could anything about this server have prevented it?**

Half the feedback this server receives is a session criticising its own work —
it did not consider Extbase, it never activated the testing skill. Read as
self-criticism those are somebody else's laundry. Read as this section's
question they are a list of gaps this server could have closed and did not. That
is the most valuable half of the corpus.

Whether the self-criticism is accurate is not assessed, and cannot be: the
session was there and the reader was not. Only the lever gets an assessment.

The corpus first
----------------

.. code-block:: bash

    bin/cli feedback:list

One call, grouped by the checkout each feedback came from and marked where no
todo names it. It comes before the ladder because the same observation is a
different judgement by how many sessions arrived at it. One report is a report,
and thirty out of one directory is a domain that has asked for something since
the first of them. ``D-FBK-025`` is the board that got its judgement card by
card while the sideways read nobody had made was the answer.

What the counts count is sessions with a debrief, not sessions that had
something to report. A session files nothing unprompted
(:doc:`asking-for-a-debrief`). So a subsystem with no feedback is a subsystem
nobody asked about as readily as one that works. Read the store for what a
report says and for how many sessions say it; never for the silence around it.

Strengths
---------

Some feedback report what worked. The ladder has no rung for them: every step
names something missing, misplaced or misworded. So the question comes from the
other side: what is the strength evidence of?

Not that a decision holds. A recorded run confirms a decision, not an account of
one. What a strength carries is where a boundary runs. The costs reported around
it are the other side of the same boundary, usually from the same debrief. That
reading is what goes into ``decisions/``, and the commit that writes it archives
the feedback —
`D-FBK-018 <../../decisions/feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md>`_.

The ladder
----------

The question comes in five steps, cheapest first. Each step has evidence a
session can check in this repository, and the first one that answers stops the
descent. A feedback that does not walk down the ladder gets a guess, and the
guess is step one either way. Written as a fourth entry beside three that
already say it, or waved off as something the wording will fix.

The ladder orders the *diagnosis*, not the appetite. Step 1 is the most
expensive answer and regularly the right one. The cheap rungs exist so that
nobody rebuilds a rule that already exists and never arrived, not so that the
answer stays small.

.. image:: ../images/feedback-judging-ladder.svg
    :zoomable:
    :alt: A feedback is diagnosed from gap through delivery, routing and wording
          to a design decision, stopping at the first step supported by
          repository evidence.

1. Gap
~~~~~~

**The answer is not here.**

Two halves, told apart by what is missing rather than by what the feedback asks
for.

**1a — the knowledge is missing.**
``bin/cli hints:probe "<the feedback's own query>"`` reaches nothing, and a
search of ``knowledge/`` and ``skills/`` confirms it. Becomes the work of
establishing what actually holds — against ``.checkouts/``, and the manual after
it — and writing that. Never the feedback's own suggestion copied into
``knowledge/``: its author was guessing about TYPO3 exactly as much as the
judging run would be.

**1b — the shape is missing.** The answer is in principle available here, and
there is no way to get it in the form the task needed. **A tool answers a
question, a skill orders a task.** Where a tool is missing, an answer cannot be
had. Where a skill is missing, the answers are all available and nothing says in
which order to ask for them.

Neither needs the feedback to ask for it, and it usually will not. The session
that reports the cost does not know what this server could offer. What triggers
1b is **what the session did instead**. Every "I had to read it by hand", every
"I established this from my own knowledge", every call repeated with different
arguments. The debrief prompt asks for those (:doc:`asking-for-a-debrief`), so
almost every feedback states them.

*Missing tool.* The six verbs in `AGENTS.md <../../AGENTS.md>`_ make the
diagnosis precise, since the verb is what tells a caller the shape of an answer.
The bootstrap_package sweep is exactly this: ``typo3_changelog_lookup`` matches
title words, and enumerating every deprecation of a version is a ``list``. Not a
broken lookup — a missing verb.

*Absent skill.* Three signals, and the third is the strongest because no single
feedback carries it. A session that invented the right order itself. A session
that went in an order that cost it the task. The same sequence two sessions
arrived at on their own. That last one is the evidence
:doc:`../contributing/writing-a-skill` says nothing can read off a file — *that
a domain earned a skill at all*. Its bar still stands. The feedback shows the
session reached it, not that anybody may skip it.

Both are *taken on* rather than closed on the spot, because a tool and a skill
are contracts. A skill lands in somebody else's project, where the next release
of this server does not correct a mistake. What may not wait is the decision. A
1b left as a question is a domain nobody owns, filed again by the next session
that hits it.

**The category is not the answer.** ``tool-gap``, ``missing-knowledge``,
``wrong-answer`` are how the reporting session saw it from where it stood, with
no view of this repository. A feedback filed as ``missing-knowledge`` is
regularly a routing failure, and one filed as ``tool-gap`` is regularly a rule
that exists and never arrived. The walk down the ladder starts from the
observation, not from the front matter.

2. Delivery
~~~~~~~~~~~

**It is here and never reached the session.**

**Evidence:** the rule exists, but it is not in the active skill and not in the
``instructions`` sent at initialize. Only a tool nobody called reaches it.
``bin/cli hints:coverage`` is this step read across the whole corpus.

**Becomes:** placement — the rule moves to where the task actually passes.

3. Routing
~~~~~~~~~~

**The right skill or tool exists and did not fire.**

**Evidence:** ``knowledge/task-intents.json``, the ``routing`` block of
``knowledge/server-scope.json``, and the skill's own trigger.

**Becomes:** a routing entry, or a trigger the task shape actually matches.

4. Wording
~~~~~~~~~~

**It arrived and did not take.**

**Evidence:** the wording itself. Written as a recommendation where it needed to be a rule. Buried below the part that answers the common case. Ambiguous enough that two readings are defensible.

**Becomes:** a rewrite. This is the cheapest fix on the ladder and the one most
often mistaken for step 1.

5. Decision
~~~~~~~~~~~

**Everything worked as designed, and the design is the price.**

**Evidence:** an entry in ``decisions/`` whose **Wrong if** this feedback
satisfies. A feedback from practice *is* the event a **Wrong if** describes, and
nothing reads them against each other today.

The signal is usually two feedback rather than one. The same property reported
as a strength by one session and as a cost by another. Both are then evidence,
and neither is wrong.

**Becomes:** a paragraph in the decision, and a question for the person who
maintains this repository. Never a change made quietly.

What the ladder costs
---------------------

The sources come in the order `AGENTS.md <../../AGENTS.md>`_ already sets to
settle a question, and the cheap end answers most feedback:

======  ==============================  ==============================  ==================================================
Source  What is asked                   Cost                            Answers
======  ==============================  ==============================  ==================================================
0       ``bin/cli hints:probe``         milliseconds                    is the answer here at all
1       ``knowledge/``, ``skills/``     cheap                           where exactly, and in the right skill
2       ``.checkouts/``                 local                           whether what the feedback claims about TYPO3 holds
3       ``typo3_documentation_lookup``  network                         what the official manual says
4       the open web                    most expensive, least reliable  only where 0–3 give nothing
======  ==============================  ==============================  ==================================================


Any feedback that makes a claim about TYPO3 itself owes steps 2 to 4. A
changelog number, a deprecation, a version boundary. A feedback taken on trust
becomes a knowledge entry with the confidence of a read and the substance of a
guess. That is the one failure nothing downstream can detect.

The gap, not the fix
--------------------

A judgement ends at the diagnosis. Which step of the ladder, on what evidence,
and what the gap is, not what the entry that fills it will say.

That is a limit rather than an omission. The judgement run has established
nothing about TYPO3. It ran a probe and a few searches over this repository,
which is what makes it cheap enough to walk the corpus at all. A solution named
from that position comes from recall rather than from a read. The todo that
follows copies it into ``knowledge/`` with a verified entry's authority.

**That reason does not reach step 1b.** What a tool or a skill lacks comes from
runs, transcripts, skill descriptions and the corpus. All of it is in this
repository, all of it read rather than recalled. So a judgement that lands on 1b
decides **that** the thing gets built and where its boundary runs. It leaves
only what it will *say* about TYPO3 to the read. Withholding that half turns a
domain thirty sessions have described into a todo asking whether the domain
exists, queued at ``low`` behind the wording nits. ``D-SKL-005`` is what the
rule cost. A core patch review that called this server nothing at all first came
out as *establish whether a core review earns a skill*. The corpus that answers
it sat unread on the same board.

The expensive half belongs to the todo anyway, so the outcomes read as *what the
work is* rather than *what the answer is*.

The answers
-----------

Seven, and only five of them are open to a session without a question.

Taken on
~~~~~~~~

*Autonomous.*

The feedback names something this server should be able to do and does not, and
the evidence that it should is here. That is a decision, and the judgement makes
it. ``decisions/`` records **that** it gets built, what its boundary is and what
would show the boundary wrong. The card carries the first concrete step at a
priority the judgement sets. The feedback stays open until the commit that ships
it archives it.

What justifies it is the corpus, not the ask. One session's suggestion is a
suggestion, and two sessions arriving at the same shape from different tasks is
the thing itself.

The measure is what it takes off the caller. A question that costs a session
four round trips is worth a tool that answers it in one. The maintenance that
moves here is the trade rather than the objection
(`D-FBK-027 <../../decisions/feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md>`_).
Most feedback that reports such a cost has already counted it; that count is
what the judgement reads.

What still waits is what nobody here can establish. What the tool answers about
TYPO3, what the skill says, which of two shapes the practice has. That is the
todo's first step, and it is research rather than a question for the maintainer.

Closed on the spot
~~~~~~~~~~~~~~~~~~

*Autonomous.*

Only where nothing remains to establish. The wording of a rule that is already
there, a move of one to where the task passes, a routing line onto a skill that
exists. The change lands in the same run and the commit that makes it archives
the feedback.

Two things put a feedback on the other side of that line, and either is enough:

* **The change touches ``src/``, a tool's declared schema, or a skill's
  contract.** Those get a review rather than an improvisation.
* **Something about TYPO3 still needs a lookup.** Then it is a todo, however
  small, because the run that judged it has read nothing but this repository. A
  lookup the judgement run already made is not that. Where it read the checkout
  and holds the evidence, a card sends the next session to read the same files
  again. That is what
  `D-FBK-052 <../../decisions/feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md>`_
  measured.

Already answered
~~~~~~~~~~~~~~~~

*Autonomous.*

The feedback describes a version of this server that no longer exists. Re-run
its query. Where today's answer is correct, the feedback has its answer. The
commit says which query ran and what came back, so a reader can dispute the
judgement.

Queued
~~~~~~

*Autonomous.*

Something that already exists has to change, and it is too large for the spot.
It touches code, a schema, a contract, or it needs a decision. Where the change
is a capability this server does not have yet, the answer above it is the one.
This rung is repair, that one is a build. A requirement records what must hold,
and a todo records the next concrete step. The feedback stays open until the
commit that implements it archives it.

**The judgement sets the priority and says what set it.** A card arrives at
``low`` because nobody has judged it, so a card left there is the one outcome
that records nothing. A queue where every card says ``low`` has no order at all.
What more than one session reported does not stay at ``low``.

Where several cards turn out to be the same gap, one of them carries the work
and names the others in its ``serves:``. Each would otherwise carry a quarter of
it. **The same commit deletes the cards it took over.** A feedback gets one card
and never a second. One left in place is a card that asks the next session for
the judgement this one has just made. ``bin/cli todo:check`` reports the pair,
and ``R-FBK-014`` is why.

Trimmed
~~~~~~~

*Autonomous.*

Part of the feedback has its answer and part has not. The answered part goes,
the rest stays open.

Proposed
~~~~~~~~

*Needs an answer.*

Nothing on the ladder produced a lever worth pulling, or the cost is out of
proportion to what it buys. That is a legitimate outcome and not one this
process may reach on its own.

It is also the outcome that has to argue hardest, because it is the one that
looks like diligence from every angle. So it says what the build would have cost
and what the corpus behind it weighs, how many sessions, from how many task
shapes. It says both in the file rather than in the commit. A *proposed*
somebody could have written without a read of ``bin/cli feedback:list`` is a
skip in an answer's clothes.

Nobody here discards a feedback. What stands instead is the proposal. Which step
of the ladder it reached, what evidence turned up, what the change would cost,
and why the session does not recommend it. It waits for its answer.

Contradicts a decision
~~~~~~~~~~~~~~~~~~~~~~

*Needs an answer.*

Step 5. The record puts the feedback against the decision it bears on, and the
question goes up. What comes back is either a decision that stands with the cost
now in it, or a revised one. Both are answers the feedback earned.

Answering with a document
-------------------------

The seven answers say what becomes of the feedback. What the judgement
established still has to land, and three directories carry most of it:
``requirements/`` a rule, ``decisions/`` a rationale, ``todo/`` a next step.

Some feedback fits none of the three, and what marks it is this: the session
found a **structure** unclear rather than a statement. In which order the steps
go, what a thing consists of, what one of them looks like. Written as a rule
that is one sentence saying the shape should be clear; written as a document it
is the shape.

Then the answer is a document, and who was lost decides which one.

* **``knowledge/documents/``** where the caller was. That corpus is what this
  server hands out. A document in it declares what it is and when to reach for
  it, see
  `D-KNW-057 <../../decisions/knowledge/knw-057-a-document-declares-what-it-is-and-when-to-reach-for-it.md>`_.
  This is step 1a landing as prose rather than as a hint: a hint states one
  thing, and what was missing is a procedure.
* **``documentation/``** where a session working in this repository was. How to
  carry a procedure here out, grouped by subject.

Which of the two the judgement run may write is the line this page already
draws. A ``documentation/`` page describes this repository, which the run has
just read. So it goes into the same commit under the test *closed on the spot*
sets: no contract moves, and nothing needs a lookup about TYPO3. A
``knowledge/documents/`` page states what holds about TYPO3, so it is *taken on*
and the reading is the todo's first step.

The invariant is unchanged. A document written on the spot archives the
feedback. One taken on leaves a todo that serves it, see
`D-FBK-043 <../../decisions/feedback/fbk-043-a-structure-is-answered-with-a-document-rather-than-with-a-rule.md>`_.

One at a time
-------------

Every open feedback has a card on the board, from the call that recorded it.
``bin/cli todo:next`` hands over one card like any other todo. A fresh card is
``low``, below everything somebody has judged to be worth more. So the oldest
feedback without a judgement is what comes up once the decided work ends. A run
is one judgement, and the loop ends when nothing waits for a judgement rather
than after a fixed number.

What that costs is the run that saw several at once. A feedback that corrects
three earlier ones, or the same gap from four sessions, is a relationship no
single judgement can state. ``decisions/`` is what carries it. A judgement made
against an entry goes into that entry, and where it establishes something no
entry says yet, a new one comes. So what a run decided is readable where
decisions already are, rather than in the commit that made it. The commit is the
one place nobody can search. There is no journal beside the archive: a second
list of the same judgements is a second thing to keep true.

A **skip** is the exception and stays out of both. It lasts one pass, stands
nowhere, and is not a state a feedback can stay in. A feedback that deserves a
state gets one of the seven answers.

The three states
----------------

A judgement does not close a feedback. It turns it into work, and the work is
what closes it.

=======================  ==========================  =================================================
Where it is              What that means             What holds it
=======================  ==========================  =================================================
open, no todo serves it  nobody has judged it        ``bin/cli todo:check``, which names it
open, a todo serves it   judged, the work is queued  ``Todo::serves()``, which ``feedback:list`` marks
``feedback/archive/``    the improvement landed      ``bin/cli feedback:archive``, in that commit
=======================  ==========================  =================================================


The middle state is the one worth being exact about. ``typo3_feedback_list``
answers an agent somewhere else, and the archive is what that agent reads as
``closed``. A feedback closed the moment somebody decided about it would tell a
session its report is done. The thing it reported would still be there. So the
archive waits for the change, and ``Todo::unreadable()`` enforces it from the
other side. It reports a todo that serves an archived feedback as a problem.

**The invariant:** a commit that judges a feedback either archives it or leaves
at least one todo that serves it. It leaves no card that still asks for the
judgement it has just made. Anything else drops it back to unjudged, where the
session to read it starts from the feedback again and the judgement is lost.
"Nothing to do" is therefore not a special case but the *close* answer: archive
in the same commit.

What no check can hold is whether the judgement reached ``decisions/``. A state
check cannot see it, and a judgement may legitimately confirm an entry without
changing a file. It stays the second **Wrong if** of
`D-FBK-012 <../../decisions/feedback/fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md>`_,
watched rather than held.

A feedback nobody **can** judge does not close either. The card moves to
``todo/waiting/`` with the question in its ``waitingOn:``, where it still serves
the feedback. So it has a judgement rather than none, and no session gets a
question nobody can answer.

What the todo adds
------------------

A todo that serves a feedback does not restate it. It carries what the feedback
does not: the answer — which step of the ladder, on what evidence — and the next
concrete step. One or two sentences, not a paragraph.

Otherwise the same account sits in ``feedback/``, in ``todo/`` and, once a
requirement exists, a third time in ``requirements/``. That is three places to
maintain, and an update to any one of them can leave the other two with
something else to say. ``bin/cli todo:next`` prints the feedback a todo serves
along with it. The feedback is what happened once, the requirement is what must
hold from now on, the todo is what to do next.
