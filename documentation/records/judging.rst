Judging a feedback
==================

This page covers how a session judges one feedback. It says what the session
asks, in which order and on what evidence. It also says which answers the
session may give without a question first.

A feedback is a session's report about this server, left by an agent that worked
somewhere else. It is not a work item. It names what went wrong from where that
session stood. What to do about it is a judgement that nobody has made yet.

The steps at a glance:

1. Read the corpus with ``bin/cli feedback:list``.
2. Ask the one question of the feedback.
3. Walk the feedback down the ladder, to the first step the evidence supports.
4. Name the gap, not the fix.
5. Give one of the seven answers.
6. Leave the feedback archived, or served by a todo.

**The channel is there to decide what to build.** Only the sessions that use
this server find out what it does not answer. The corpus is the only place where
that knowledge lands. So a judgement that ends in no build has to earn that as
much as one that ends in a new tool. The default is a decision, not caution.
Where the evidence is here, the judgement decides; only what nobody in this
repository can establish waits.

That evidence is a run, a transcript, or several sessions with the same thing to
say.

:doc:`index` says where a feedback lives, and what happens to it once a session
has worked it off. This page is the step between the two.

The one question
----------------

Ask every feedback the same question. The question is not whether the feedback
is right:

**Could anything about this server have prevented it?**

Half the feedback this server receives is a session's criticism of its own work.
For example, it did not consider Extbase, or it never activated the testing
skill. Read as self-criticism, those are somebody else's laundry. Read against
this question, they are a list of gaps this server could have closed and did
not. That half is the most valuable half of the corpus.

The judgement does not assess whether the self-criticism is accurate, and it
cannot: the session was there and the reader was not. The judgement assesses
only the lever.

The corpus first
----------------

.. code-block:: bash

    bin/cli feedback:list

The command is one call. It groups the feedback by the checkout each one came
from, and it marks each feedback that no todo names.

Run it before the ladder. The number of sessions that arrive at an observation
changes the judgement. One report is a report. Thirty from one directory are a
domain that has asked for something since the first of them. ``D-FBK-025`` is
the board that got its judgement card by card, while the answer was the sideways
read that nobody had made.

The counts count sessions with a debrief, not sessions that had something to
report. A session files nothing unprompted (:doc:`asking-for-a-debrief`). So a
subsystem with no feedback is as likely one that nobody asked about as one that
works. Read the store for what a report says, and for how many sessions say it.
Never read it for the silence around it.

Strengths
---------

Some feedback reports what worked. The ladder has no step for it: every step
names something absent, misplaced or misworded. So ask from the other side: what
is the strength evidence of?

It is not evidence that a decision holds. A recorded run confirms a decision,
and an account of one does not. A strength shows where a boundary runs. The
costs reported around it are the other side of the same boundary, usually from
the same debrief. That read goes into ``decisions/``, and the commit that writes
it archives the feedback —
`D-FBK-018 <../../decisions/feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md>`_.

The ladder
----------

The question comes in five steps, cheapest first. Each step has evidence that a
session can check in this repository. The first step that answers stops the
descent.

A feedback that does not walk down the ladder gets a guess instead. Somebody
writes it as a fourth entry beside three that already say it. Or somebody waves
it off, as something that other words will fix.

The ladder orders the *diagnosis*, not the appetite. Step 1 is the most
expensive answer and often the right one. The cheap steps exist so that nobody
rebuilds a rule that already exists and never arrived. They do not exist to keep
the answer small.

1. Gap
~~~~~~

**The answer is not here.**

The gap has two halves. What is absent tells them apart, not what the feedback
asks for.

**1a — the knowledge is absent.**
``bin/cli hints:probe "<the feedback's own query>"`` reaches nothing, and a
search of ``knowledge/`` and ``skills/`` confirms it. The work is then to
establish what holds, against ``.checkouts/`` and then the manual, and to write
that. Never copy the suggestion of the feedback into ``knowledge/``. Its author
guessed about TYPO3 exactly as much as the judge would.

**1b — the shape is absent.** The answer is available here in principle, but
nothing gets it in the form the task needed. **A tool answers a question, a
skill orders a task.** Where a tool is absent, no answer exists. Where a skill
is absent, all the answers are available, and nothing says in which order to ask
for them.

Neither needs the feedback to ask for it, and the feedback usually does not. The
session that reports the cost does not know what this server could offer. **What
the session did instead** is what triggers 1b:

* every "I had to read it by hand"
* every "I established this from my own knowledge"
* every call repeated with other arguments

The debrief prompt asks for those (:doc:`asking-for-a-debrief`), so almost every
feedback states them.

*Missing tool.* The six verbs in `AGENTS.md <../../AGENTS.md>`_ make the
diagnosis precise, because the verb tells a caller the shape of an answer. The
bootstrap_package sweep is exactly this case. ``typo3_changelog_lookup`` matches
title words, and an enumeration of every deprecation of a version is a ``list``.
The lookup is not broken — the verb is absent.

*Absent skill.* There are three signals. The third is the strongest, because no
single feedback contains it:

* A session that invented the right order itself.
* A session that went in an order that cost it the task.
* The same sequence, which two sessions arrived at on their own.

That last one is the evidence that :doc:`../contributing/writing-a-skill` says
nothing can read off a file — *that a domain earned a skill at all*. Its bar
still stands. The feedback shows that the session reached the bar, not that
anybody may skip it.

Take both on, rather than close them on the spot, because a tool and a skill are
contracts. A skill lands in somebody else's project, where the next release of
this server does not correct a mistake. The decision may not wait. A 1b left as
a question is a domain that nobody is responsible for. The next session that
hits it files it again.

**The category is not the answer.** ``tool-gap``, ``missing-knowledge`` and
``wrong-answer`` say how the session that reported it saw it. That session had
no view of this repository. A feedback filed as ``missing-knowledge`` is often a
routing failure. One filed as ``tool-gap`` is often a rule that exists and never
arrived. Start the walk down the ladder from the observation, not from the front
matter.

2. Delivery
~~~~~~~~~~~

**It is here and never reached the session.**

**Evidence:** the rule exists, but it is not in the active skill and not in the
``instructions`` sent at initialize. Only a tool that nobody called reaches it.
``bin/cli hints:coverage`` reads this step across the whole corpus.

**Becomes:** placement — the rule moves to where the task actually passes.

3. Routing
~~~~~~~~~~

**The right skill or tool exists and did not fire.**

**Evidence:** ``knowledge/task-intents.json``, the ``routing`` block of
``knowledge/server-scope.json``, and the trigger of the skill.

**Becomes:** a routing entry, or a trigger that the task shape actually matches.

4. Wording
~~~~~~~~~~

**It arrived and did not take.**

**Evidence:** the wording itself.

* It is a recommendation where it needed to be a rule.
* It stands below the part that answers the common case.
* It is so ambiguous that two readings are defensible.

**Becomes:** a rewrite. This is the cheapest fix on the ladder, and the one that
people most often mistake for step 1.

5. Decision
~~~~~~~~~~~

**Everything worked as designed, and the design is the price.**

**Evidence:** an entry in ``decisions/`` whose **Wrong if** this feedback
satisfies. A feedback from practice *is* the event a **Wrong if** describes.
Nothing reads the two against each other today.

The signal is usually two feedback, not one. One session reports a property as a
strength, and another reports the same property as a cost. Both are then
evidence, and neither is wrong.

**Becomes:** a paragraph in the decision, and a question for the person who
maintains this repository. Never a change made quietly.

What the ladder costs
---------------------

The sources come in the order `AGENTS.md <../../AGENTS.md>`_ already sets to
settle a question. The cheap end answers most feedback:

======  ==============================  ==============================  ==================================================
Source  What is asked                   Cost                            Answers
======  ==============================  ==============================  ==================================================
0       ``bin/cli hints:probe``         milliseconds                    is the answer here at all
1       ``knowledge/``, ``skills/``     cheap                           where exactly, and in the right skill
2       ``.checkouts/``                 local                           whether what the feedback claims about TYPO3 holds
3       ``typo3_documentation_lookup``  network                         what the official manual says
4       the open web                    most expensive, least reliable  only where 0–3 give nothing
======  ==============================  ==============================  ==================================================

Every feedback that makes a claim about TYPO3 itself needs sources 2 to 4. Such
a claim is a changelog number, a deprecation or a version boundary. A feedback
taken on trust becomes a knowledge entry with the confidence of a read and the
substance of a guess. Nothing downstream can detect that one failure.

The gap, not the fix
--------------------

A judgement ends at the diagnosis: which step of the ladder, on what evidence,
and what the gap is. It does not say what the entry that fills the gap will say.

That is a limit, not an omission. The judgement run has established nothing
about TYPO3. It ran a probe and a few searches over this repository, which makes
it cheap enough to walk the corpus at all. A solution it names from that
position comes from recall, not from a read. The todo that follows copies that
solution into ``knowledge/`` with the authority of a verified entry.

**That reason does not reach step 1b.** What a tool or a skill lacks comes from
runs, transcripts, skill descriptions and the corpus. All of it is in this
repository, and the run reads all of it rather than recalls it. So a judgement
that lands on 1b decides **that** the thing gets built, and where its boundary
runs. It leaves to the read only what the thing will *say* about TYPO3.

Without that half, a domain that thirty sessions have described turns into a
todo that asks whether the domain exists. That todo queues at ``low``, behind
the small fixes to words. ``D-SKL-005`` is what the rule cost. A core patch
review that made no call to this server at all first came out as *establish
whether a core review earns a skill*. The corpus that answers it sat unread on
the same board.

The expensive half belongs to the todo anyway. So the outcomes read as *what the
work is*, not as *what the answer is*.

The answers
-----------

There are seven answers. A session may give five of them without a question.

Taken on
~~~~~~~~

*Autonomous.*

The feedback names something this server should be able to do and does not, and
the evidence that it should is here. That is a decision, and the judgement makes
it. ``decisions/`` records **that** it gets built, what its boundary is, and
what would show the boundary wrong. The card names the first concrete step, at a
priority that the judgement sets. The feedback stays open until the commit that
ships the change archives it.

The corpus justifies it, not the ask. One session's suggestion is a suggestion.
Two sessions that arrive at the same shape from different tasks are the thing
itself.

The measure is what it takes off the caller. A question that costs a session
four round trips is worth a tool that answers it in one. The maintenance that
moves here is the trade, not the objection
(`D-FBK-027 <../../decisions/feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md>`_).
Most feedback that reports such a cost has already counted it. The judgement
reads that count.

Only what nobody here can establish still waits. That is what the tool answers
about TYPO3, what the skill says, or which of two shapes the practice has. It is
the first step of the todo. It is research, not a question for the maintainer.

Closed on the spot
~~~~~~~~~~~~~~~~~~

*Autonomous.*

Close a feedback on the spot only where nothing remains to establish. Examples
are new words for a rule that is already there, and a move of a rule to where
the task passes. A routing line onto a skill that exists is one too. The change
lands in the same run, and the commit that makes it archives the feedback.

Either of two things puts a feedback on the other side of that line:

* **The change touches** ``src/``, **a tool's declared schema, or a skill's
  contract.** Those get a review, not an improvisation.

* **Something about TYPO3 still needs a lookup.** Then it is a todo, however
  small, because the run that judged it has read nothing but this repository.

  A lookup that the judgement run already made does not count. Where the run
  read the checkout and holds the evidence, a card sends the next session to
  read the same files again.
  `D-FBK-052 <../../decisions/feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md>`_
  measured that.

Already answered
~~~~~~~~~~~~~~~~

*Autonomous.*

The feedback describes a version of this server that no longer exists. Re-run
its query. Where the answer of today is correct, the feedback has its answer.
The commit says which query ran and what came back, so a reader can dispute the
judgement.

Queued
~~~~~~

*Autonomous.*

Something that already exists has to change, and the change is too large for the
spot. It touches code, a schema or a contract, or it needs a decision. Where the
change is a capability this server does not have yet, the answer is *Taken on*
instead. *Queued* is repair, and *Taken on* is a build. A requirement records
what must hold, and a todo records the next concrete step. The feedback stays
open until the commit that implements the change archives it.

**The judgement sets the priority and says what set it.** A card arrives at
``low`` because nobody has judged it. So a card left at ``low`` is the one
outcome that records nothing. A queue where every card says ``low`` has no order
at all. What more than one session reported does not stay at ``low``.

Several cards can turn out to be the same gap. Then one of them takes the work
and names the others in its ``serves:``. Otherwise each would hold a quarter of
it. **The same commit deletes the cards it took over.** A feedback gets one card
and never a second. A card left in place asks the next session for the judgement
this one has just made. ``bin/cli todo:check`` reports the pair, and
``R-FBK-014`` is why.

Trimmed
~~~~~~~

*Autonomous.*

Part of the feedback has its answer and part has not. The answered part goes,
and the rest stays open.

Proposed
~~~~~~~~

*Needs an answer.*

Nothing on the ladder produced a lever worth a pull, or the cost is out of
proportion to what it buys. That is a legitimate outcome, and this process may
not reach it on its own.

It is also the outcome that has to argue hardest, because from every angle it
looks like diligence. So it says what the build would have cost and what the
corpus behind it weighs: how many sessions, from how many task shapes. It says
both in the file, not in the commit. A *proposed* that somebody could have
written without a read of ``bin/cli feedback:list`` is a skip dressed as an
answer.

Nobody here discards a feedback. The proposal stands instead. It says which step
of the ladder it reached, what evidence turned up, and what the change would
cost. It also says why the session does not recommend it. It waits for its
answer.

Contradicts a decision
~~~~~~~~~~~~~~~~~~~~~~

*Needs an answer.*

This is step 5. The record puts the feedback against the decision it bears on,
and the question goes to the person who maintains this repository. The answer is
either a decision that stands with the cost now in it, or a revised decision.
The feedback earned either answer.

Answering with a document
-------------------------

The seven answers say what becomes of the feedback. What the judgement
established still has to land somewhere. Three directories take most of it:
``requirements/`` a rule, ``decisions/`` a rationale, ``todo/`` a next step.

Some feedback fits none of the three. Its mark is that the session found a
**structure** unclear, not a statement. That is the order of the steps, what a
thing consists of, or what one of them looks like. Written as a rule, it is one
sentence that says the shape should be clear. Written as a document, it is the
shape.

Then the answer is a document, and the reader who was lost decides which one:

* ``knowledge/documents/`` where the caller was lost. That corpus is what this
  server hands out. A document in it declares what it is and when to reach for
  it —
  `D-KNW-057 <../../decisions/knowledge/knw-057-a-document-declares-what-it-is-and-when-to-reach-for-it.md>`_.
  This is step 1a, landed as prose rather than as a hint. A hint states one
  thing, and what was absent is a procedure.

* ``documentation/`` where a session at work in this repository was lost. It
  says how to do a procedure here, grouped by subject.

The line this page already draws decides which of the two the judgement run may
write. A ``documentation/`` page describes this repository, which the run has
just read. So it goes into the same commit, under the test that *closed on the
spot* sets: no contract moves, and nothing needs a lookup about TYPO3. A
``knowledge/documents/`` page states what holds about TYPO3. So it is *taken
on*, and the read is the first step of the todo.

The invariant still holds. A document written on the spot archives the feedback.
A document taken on leaves a todo that serves the feedback —
`D-FBK-043 <../../decisions/feedback/fbk-043-a-structure-is-answered-with-a-document-rather-than-with-a-rule.md>`_.

One at a time
-------------

Every open feedback has a card on the board, from the call that recorded it.
``bin/cli todo:next`` gives out one card, like any other todo. A fresh card is
``low``, below everything somebody has judged to be worth more. So the oldest
feedback without a judgement comes up once the decided work ends. A run is one
judgement. The loop ends when nothing waits for a judgement, not after a fixed
number.

The cost is the run that saw several at once. A feedback that corrects three
earlier ones is a relationship that no single judgement can state. So is the
same gap from four sessions. ``decisions/`` holds that relationship. A judgement
made against an entry goes into that entry. Where it establishes something that
no entry says yet, a new entry comes.

So what a run decided is readable where the decisions already are, not in the
commit that made it. The commit is the one place nobody can search. There is no
journal beside the archive: a second list of the same judgements is a second
thing to keep true.

A **skip** is the exception and stays out of both. It lasts one pass, no file
records it, and a feedback cannot stay in it as a state. A feedback that
deserves a state gets one of the seven answers.

The three states
----------------

A judgement does not close a feedback. It turns the feedback into work, and the
work closes it.

=======================  ==========================  =================================================
Where it is              What that means             What holds it
=======================  ==========================  =================================================
open, no todo serves it  nobody has judged it        ``bin/cli todo:check``, which names it
open, a todo serves it   judged, the work is queued  ``Todo::serves()``, which ``feedback:list`` marks
``feedback/archive/``    the improvement landed      ``bin/cli feedback:archive``, in that commit
=======================  ==========================  =================================================

Be exact about the middle state. ``typo3_feedback_list`` answers an agent
somewhere else, and that agent reads the archive as ``closed``. A feedback
closed the moment somebody decided about it would tell a session its report is
complete. The thing it reported would still be there. So the archive waits for
the change. ``Todo::unreadable()`` enforces that from the other side: it reports
a todo that serves an archived feedback as a problem.

**The invariant:** a commit that judges a feedback either archives it or leaves
at least one todo that serves it. It leaves no card that still asks for the
judgement it has just made. Anything else drops the feedback back to unjudged.
The next session to read it then starts from the feedback again, and the
judgement is lost. So "nothing to do" is no special case. It is an answer that
closes the feedback: archive it in the same commit.

No check can hold whether the judgement reached ``decisions/``. A state check
cannot see it, and a judgement may confirm an entry without a change to any
file. It stays the second **Wrong if** of
`D-FBK-012 <../../decisions/feedback/fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md>`_,
watched and not held.

A feedback that nobody **can** judge does not close either. Its card moves to
``todo/waiting/`` with the question in its ``waitingOn:``, where the card still
serves the feedback. So the feedback has a judgement rather than none. No
session gets a question that nobody can answer.

What the todo adds
------------------

A todo that serves a feedback does not restate it. It adds what the feedback
lacks: the answer and the next concrete step. The answer is which step of the
ladder, on what evidence. One or two sentences are enough, not a paragraph.

Otherwise the same account is in ``feedback/``, in ``todo/`` and, once a
requirement exists, a third time in ``requirements/``. That is three places to
maintain. An update to one of them can leave the other two with something else
to say. ``bin/cli todo:next`` names the feedback a todo serves along with the
todo. The feedback is what happened once, the requirement is what must hold from
now on, and the todo is what to do next.
