Forward runs
============

What a forward review is, and the five of them, are in
`scenarios/forward/readme.md <../../scenarios/forward/readme.md>`_. This is how
a session carries one out.

.. image:: ../images/forward-run.svg
    :zoomable:
    :alt: A recorded scenario runs in a clean real project without steering,
          produces a transcript and working-tree reading, is judged against
          fixed criteria and leaves reusable findings as feedback or a contract
          case.

Running one
-----------

1. Start the MCP client in the environment the review names. A fresh session,
   with the skills published there as they are in this checkout right now. It
   need not be a person at the keyboard. A client driven non-interactively is
   the same evidence, as long as it gets the prompt and nothing else. What such
   a launch has to get right — the session id the grading later needs among it —
   is :doc:`../contributing/driving-a-session`.
   `todo/reference/ <../../todo/reference/>`_ says which checkout plays which
   environment on this machine, and how to reach the client there. A recorded
   review runs in one of those and not in the ``E-SITE`` this repository makes
   for itself. What a review would find in a scaffold is what this repository
   put there
   (`D-EVI-004 <../../decisions/evidence/evi-004-the-environment-is-made-here-and-the-repository-under-review-is-not.md>`_).
2. ``bin/cli scenarios:record <id> <client>`` writes the empty run, and
   ``bin/cli scenarios:show <id>`` prints the prompt and the numbered criteria.
3. Paste the prompt verbatim. Add nothing: no tool names, no hints that a TYPO3
   knowledge server is there, no correction when the agent goes the wrong way.
   What the agent does with an under-specified request is part of the measure.
4. Let the review reach its own stopping point. Do not steer it toward a known
   subsystem or finding.
5. Run ``git status`` in the environment afterwards, and record what it says.
   The review itself may not write, which is
   `D-EVI-003 <../../decisions/evidence/evi-003-a-review-runs-the-checks-that-cannot-change-the-code.md>`_
   . So a modified tree is one of two things, and both are worth the ten
   seconds. Either the session overstepped, which is a criterion, or a lookup
   did. The second is what
   `D-DIS-005 <../../decisions/discovery/dis-005-a-registry-with-no-console-command-is-read-by-booting-the-installation.md>`_
   watches for. The registry lookups answer with a boot of the installation in a
   subprocess. A boot that writes outside the cache is the symptom that decision
   named and cannot hold itself. Start from a clean tree so the reading means
   something, and note what was already modified where it is not. The transcript
   carries the other half of the same **Wrong if**. A lookup that answers
   ``answeredBy: packages`` against an installation that was up and configured
   is a boot that did not finish. A 90-second gap in front of it is the timeout.

6. Grade against **What has to come out of it** and **How it fails**. Write the
   judgment and its evidence into the recorded run, together with the skills
   that activated and the tools the session called. A call is the name **and**
   the arguments it came with, copied from the transcript, ``{}`` where the tool
   takes none. The name says a lookup happened, and the question it got is most
   of what the judgment turns on. One query per surface or one broad one, which
   version a lookup got, whether the session followed a returned id.
   ``bin/cli scenarios:check`` — and ``composer test`` — then hold that run to
   its review.

Judging one
-----------

The grade comes from the transcript, not from what the session felt like. The
client stores it as JSONL, one file per session, so which skills activated and
which tools the session called are evidence rather than recollection. A handful
of the answer's findings are worth a second check against the checkout before
the judgment. Grading an answer and grading a claim are two different things.

* **Judge against the criteria as written**, not against how much better this
  run was than the last. Three ``REVIEW-01`` runs in a row scored four of five
  while they missed a defect none of the five asked about. The criterion that
  now catches it exists because somebody wrote the gap down instead of excused
  it.
* **Judge the verdict a finding puts on top of its evidence.** A run can get
  every path, count and line right and still call a design decision a defect.
  ``REVIEW-02`` was ``partial`` on its first run and ``covered`` on its second
  with the same reading underneath.
* **A tool a judgment quotes is one the trace carries.**
  ``bin/cli scenarios:check`` prints every tool name in a run's evidence that
  its own trace has no call for. It prints them under the table and outside the
  exit code. It reports rather than fails because the two cases read alike from
  there. A judgment that quotes a call the session never made, and one that
  names a tool in order to say the session never called it. ``REVIEW-03`` is the
  second, and the defect it reports turned up by hand
  (`D-EVI-009 <../../decisions/evidence/evi-009-a-run-is-read-against-its-own-trace.md>`_).
* **One change, one run.** An edit to the criteria resets the recorded run to
  ``unrun`` by design. The digest check catches a judgment that answers criteria
  somebody has since rewritten, so the superseded run survives only in its
  commit. Name that commit in the new run's feedback.
* **Reset the environment between runs.** A restored work tree is not enough if
  the session wrote to the database. The next run finds what the last one left
  and behaves differently.

What a run produces
-------------------

The run itself, as one file below `scenarios/runs/ <../../scenarios/runs/>`_.
The environment, the server it ran against, and the skills the session
activated. The tools it reached for and what it asked them, and one judgment
with evidence per criterion. The verdict stands nowhere in it. It follows from
the judgments. A review whose mark disagrees with what its run establishes is a
failed check rather than a sentence nobody rereads.

Everything else a run produces, it produces on top of that — and a run that went
well produces it too. What a run teaches is rarely the verdict. Three
``REVIEW-02`` runs in two repositories all came out ``covered`` or better
against the same criteria. The thing worth a note from them is that not one of
the three executed a single project-owned command. Not one of the answers said
so. That is neither a criterion nor a property of the repository under review.
Left in the run's evidence field, one person reads it once, whoever judged that
run.

So after the judgment, ask what the run taught that is **not** specific to the
one repository. File each answer where the recurring work already walks:

* ``typo3_feedback_record`` for what was absent, wrong, or unhelpful, with the
  review id in the observation. So a reader can trace the feedback back to the
  task that exposed it. Give it the run's own prompt as its query, so a later
  server can take the feedback again the way it takes every other feedback. Give
  it the model the run ran as, because what a run teaches about behaviour
  belongs to that model and not to the next one.
* A new contract case, when the session exposes a repeatable task or failure
  shape worth holding directly. That is the more valuable outcome of the two.

Whoever judged the run writes both of those, from the transcript. When the run
happened in an agent whose transcript nobody here can read, the session is the
only thing that can report it. It gets an ask for its own debrief after the work
ends, and the generic prompt for that is :doc:`asking-for-a-debrief`. What comes
back is weaker evidence than a transcript and the run says so. It is what the
session claims about itself, and the answer it gave still gets the usual
judgment.

A defect the same session fixes is the exception. That is a requirement and the
commit that closed it, not a feedback that would go to the archive on creation.
Otherwise the usual route applies. A commit that archives the feedback works it
off, and what has to hold afterwards goes into
`requirements/ <../../requirements/readme.md>`_.

For a ``gap`` review, do not re-file the part that is already written down — its
**Status today** line names the requirement. File what the task needed beyond
it.

A run that hangs
----------------

Treat it as a defect in this server until something else is proven. A client
waiting on a tool call that will never return looks exactly like a client
thinking hard, and neither side reports anything.

Measure before theorising. Constant CPU time and a still ``rchar`` in
``/proc/<pid>/io`` mean idle rather than busy. No TCP socket in
``ls -l /proc/<pid>/fd`` rules out a wait on the model. Client debug output
names the tool that never came back, which is the whole diagnosis. Two
``REVIEW-02`` attempts died 24 minutes apart on the first pair of tool calls a
client dispatched at once. The cause was this server, which handed its own stdin
to a console command
(`R-DIS-018 <../../requirements/discovery/dis-018-a-console-command-never-inherits-the-clients-stdin.md>`_).
