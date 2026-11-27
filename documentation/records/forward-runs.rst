Forward runs
============

This page covers how a session runs a forward review, judges it and records what
the run teaches. It also covers a run that hangs.
`scenarios/forward/readme.md <../../scenarios/forward/readme.md>`_ says what a
forward review is, and lists the five of them.

.. diagram::
    :zoomable:
    :caption: The prompt is fixed, the session is unsteered, and the transcript decides the judgment.

    flow canvas=960
      columns 1,1,1.15,1.15
    title "From scenario to a reusable finding"
    description "A recorded scenario runs in a clean real project with no steer. It produces a transcript and a read of the working tree. A judge holds it to fixed criteria, and reusable findings leave as feedback or a contract case."

    prepare "Prepare"
      at 0,0
      tag "1"
      item "recorded scenario"
      item "clean real project"
      item "current skills + server"
    run "Run"
      at 1,0
      tag "2"
      item "fresh agent session"
      item "prompt pasted verbatim"
      item "no steering"
      ink
    collect "Collect"
      at 2,0
      tag "3"
      item "transcript + tool arguments"
      item "skills activated"
      item "git status afterwards"
    judge "Judge and record"
      at 3,0
      tag "4"
      item "criteria as written"
      item "one judgment per criterion"
      item "one file in scenarios/runs/"
    transfer "Ask what transfers beyond this project"
      at 2,1
      span 2
      body "The verdict stays in the run; the reusable lesson moves on."
      emphasis
    feedback "Server feedback"
      at 0,2
      span 2
      body "missing, wrong or unhelpful answer"
    contract "New contract case"
      at 2,2
      span 2
      body "repeatable task or failure shape"

    prepare -> run
    run -> collect
    collect -> judge
    judge -> transfer
    transfer -> feedback
    transfer -> contract

Running one
-----------

1. Start the MCP client in the environment the review names. Use a fresh
   session, with the skills published there as they are in this checkout now.

   A person at the keyboard is optional. A client driven non-interactively gives
   the same evidence, as long as it gets the prompt and nothing else.
   :doc:`../contributing/driving-a-session` says what such a launch has to get
   right, the session id the judge needs later among it.

   `todo/reference/ <../../todo/reference/>`_ says which checkout plays which
   environment on this machine, and how to reach the client there. Run a
   recorded review in one of those, not in the ``E-SITE`` this repository makes
   for itself. In a scaffold, a review would find what this repository put there
   (`D-EVI-004 <../../decisions/evidence/evi-004-the-environment-is-made-here-and-the-repository-under-review-is-not.md>`_).

2. Write the empty run with ``bin/cli scenarios:record <id> <client>``. Print
   the prompt and the numbered criteria with ``bin/cli scenarios:show <id>``.

3. Paste the prompt verbatim. Add nothing: no tool names, no hint that a TYPO3
   knowledge server is there, no correction when the agent goes the wrong way.
   What the agent does with an under-specified request is part of the measure.

4. Let the review reach its own stop. Do not steer it toward a known subsystem
   or finding.

5. Run ``git status`` in the environment afterwards, and record what it says.
   Start from a clean tree, so that the read means something. Where the tree is
   not clean, note what somebody had modified.

   The review itself may not write, which is
   `D-EVI-003 <../../decisions/evidence/evi-003-a-review-runs-the-checks-that-cannot-change-the-code.md>`_.
   So a modified tree means one of two things, and both are worth the ten
   seconds. Either the session overstepped, which is a criterion, or a lookup
   wrote.

   `D-DIS-005 <../../decisions/discovery/dis-005-a-registry-with-no-console-command-is-read-by-booting-the-installation.md>`_
   watches for the second case. The registry lookups answer with a boot of the
   installation in a subprocess. A boot that writes outside the cache is the
   symptom that decision named and cannot hold itself.

   The transcript shows the other half of the same **Wrong if**. A lookup that
   answers ``answeredBy: packages`` against an installation that was up and
   configured is a boot that did not finish. A 90-second gap in front of it is
   the timeout.

6. Grade against **What has to come out of it** and **How it fails**. Write the
   judgment and its evidence into the recorded run. Add the skills that
   activated and the tools the session called.

   A call is the name **and** the arguments it came with, copied from the
   transcript. Write ``{}`` where the tool takes none. The name says that a
   lookup happened, but most of the judgment turns on the question it got. For
   example: one query per surface or one broad one, which version a lookup got,
   whether the session followed a returned id.

   ``bin/cli scenarios:check`` and ``composer test`` then hold that run to its
   review.

Judging one
-----------

Grade from the transcript, not from how the session felt. The client stores the
transcript as JSONL, one file per session. So which skills activated and which
tools the session called are evidence, not recollection. Check a handful of the
answer's findings against the checkout before the judgment. To grade an answer
and to grade a claim are two different things.

* **Judge against the criteria as written**, not against how much better this
  run was than the last. Three ``REVIEW-01`` runs in a row scored four of five,
  and they missed a defect none of the five asked about. The criterion that
  catches it now exists because somebody wrote the gap down and did not excuse
  it.

* **Judge the verdict a finding puts on top of its evidence.** A run can get
  every path, count and line right and still call a design decision a defect.
  ``REVIEW-02`` was ``partial`` on its first run and ``covered`` on its second,
  with the same read underneath.

* **A judgment quotes only a tool the trace has a call for.**
  ``bin/cli scenarios:check`` prints every tool name in a run's evidence that
  the run's own trace has no call for. It prints them below the table, and they
  do not change the exit code.

  The check reports and does not fail, because from there two cases read alike.
  One is a judgment that quotes a call the session never made. The other names a
  tool to say that the session never called it. ``REVIEW-03`` is the second, and
  the defect it reports turned up by hand
  (`D-EVI-009 <../../decisions/evidence/evi-009-a-run-is-read-against-its-own-trace.md>`_).

* **One change, one run.** An edit to the criteria resets the recorded run to
  ``unrun``, by design. The digest check catches a judgment that answers
  criteria somebody has since rewritten. So the superseded run survives only in
  its commit. Name that commit in the new run's feedback.

* **Reset the environment between runs.** A restored work tree is not enough
  where the session wrote to the database. The next run finds what the last one
  left, and behaves differently.

What a run produces
-------------------

A run produces one file below `scenarios/runs/ <../../scenarios/runs/>`_. The
file holds the environment, the server it ran against and the skills the session
activated. It holds the tools the session reached for, what it asked them, and
one judgment with evidence per criterion. The file holds no verdict, because the
verdict follows from the judgments. A review whose mark disagrees with what its
run establishes fails a check. It does not become a sentence nobody rereads.

Everything else a run produces comes on top of that file, and a run that went
well produces it too. What a run teaches is rarely the verdict. Three
``REVIEW-02`` runs in two repositories all came out ``covered`` or better
against the same criteria. The thing worth a note from them: not one of the
three executed a single project-owned command, and not one of the answers said
so. That is neither a criterion nor a property of the repository under review.
Left in the run's evidence field, it reaches one reader once: whoever judged
that run.

So after the judgment, ask what the run taught beyond the one repository. File
each answer where the recurring work already looks:

* ``typo3_feedback_record`` for what was absent, wrong or unhelpful. Put the
  review id in the observation, so a reader can trace the feedback back to the
  task that exposed it.

  Give it the run's own prompt as its query, so a later server can take the
  feedback again like every other feedback. Give it the model the run ran as.
  What a run teaches about behaviour belongs to that model, not to the next one.

* A new contract case, where the session exposes a repeatable task or failure
  shape worth a direct hold. That is the more valuable outcome of the two.

Whoever judged the run writes both, from the transcript. Some runs happen in an
agent whose transcript nobody here can read. There the session alone can report
the run. Ask it for its own debrief after the work ends.
:doc:`asking-for-a-debrief` holds the generic prompt for that.

What comes back is weaker evidence than a transcript, and the run says so. It is
what the session claims about itself, and the answer it gave still gets the
usual judgment.

A defect the same session fixes is the exception. It becomes a requirement and
the commit that closed it, not a feedback that would go to the archive on
creation. Otherwise the usual route applies. A commit that archives the feedback
works it off, and what has to hold afterwards goes into
`requirements/ <../../requirements/readme.md>`_.

For a ``gap`` review, do not file again what is already written down: its
**Status today** line names the requirement. File what the task needed beyond
it.

A run that hangs
----------------

Treat a run that hangs as a defect in this server until you prove another cause.
A client that waits on a tool call that never returns looks exactly like a
client that thinks hard. Neither side reports anything.

Measure before you form a theory:

* Constant CPU time and a still ``rchar`` in ``/proc/<pid>/io`` mean idle, not
  busy.
* No TCP socket in ``ls -l /proc/<pid>/fd`` rules out a wait on the model.
* Client debug output names the tool that never came back, and that is the whole
  diagnosis.

Two ``REVIEW-02`` attempts died 24 minutes apart, on the first pair of tool
calls a client sent at once. The cause was this server: it passed its own stdin
to a console command
(`R-DIS-018 <../../requirements/discovery/dis-018-a-console-command-never-inherits-the-clients-stdin.md>`_).
