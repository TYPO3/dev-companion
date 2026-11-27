Asking for a debrief
====================

This page holds the prompt that gets a feedback out of a session this repository
cannot read. It says when to ask, how to send the prompt, and why the prompt
asks what it asks.

A session in a client this repository can read leaves a transcript. Which skills
activated and which tools the session called are evidence in it. A session in
somebody else's agent leaves nothing here. Only the session itself can report
such a run, and it does not do so unprompted. An agent that finished its task
considers itself done.

When to ask
-----------

Ask in a message of its own, **after** the work ends. A debrief sent before the
task or with it becomes part of the task. An agent that knows a question about
helpful tools is on its way calls tools to have an answer.

The prompt is generic on purpose. It names no scenario, no skill and no tool but
the one that records the feedback. So the same text works after a review, an
implementation, or a question the server could not answer at all.

The prompt
----------

.. literalinclude:: debrief.txt
   :language: text

Paste it verbatim and add nothing: no tool names, no hint about what the last
session reported. The summary the agent gives afterwards is not the record. The
feedback is the record, and ``typo3_feedback_list`` reads it back.

Where the server runs from a standalone checkout, that file is also the
``debrief`` prompt. The prompt has the condition that ``Channel::isAvailable()``
already puts on the two feedback tools. A session that cannot record a feedback
has no use for the questions.

The person invokes the prompt, and the model gets no offer of it. So the session
does not learn of the debrief while it still works —
`D-FBK-048 <../../decisions/feedback/fbk-048-the-debrief-is-offered-as-a-prompt-where-the-channel-is.md>`_.
The prompt takes no arguments and reads the file this page includes. So neither
route is a version behind the other.

Why it asks this
----------------

The report, not the shape
~~~~~~~~~~~~~~~~~~~~~~~~~

The prompt asks for the report, never for the shape of the feedback.
``typo3_feedback_record`` states what each field wants in its description and
its parameters, the only documentation a client reads. That covers one feedback
per subject, the task named in the first line, and where to read the model
identifier rather than remember it. A prompt that restates it is a second copy
that ages. It would also reach only the sessions somebody gave it to.

What did not happen
~~~~~~~~~~~~~~~~~~~

Half of what the prompt asks about did not happen. The calls a session made are
in front of it. The skill that never activated, the tool it passed over and the
question it never asked leave nothing behind. A list that asks only what
happened confirms the surface the server already has.

`D-AUD-003 <../../decisions/audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md>`_
records a ``REVIEW-01`` session that never activated the skill. The body of that
skill would have met both criteria the run failed. The session made every call
through Bash instead.

The knowledge
~~~~~~~~~~~~~

The list also has to work for the knowledge. A session reports what it did, and
what it did is call tools. So bullets weighted towards skills, calls and names
come back with skills, calls and names. The corpus the task turned on goes
unmentioned. So three bullets ask about the knowledge instead —
`D-FBK-047 <../../decisions/feedback/fbk-047-the-debrief-asks-what-an-answer-left-out.md>`_:

* the answer that stopped one step short, which is how the corpus fails, rather
  than by absence
* the wish, asked without the scope test the session would otherwise apply to it
* what the session established elsewhere, asked for whole and sorted here rather
  than there

The documents
~~~~~~~~~~~~~

The documents get a question of their own because the model never picks one. The
host application or the user chooses a resource, not the model mid-task. So a
client that lists none leaves the session nothing to find.
:doc:`the resource surface <../server/resources/index>` says what that surface
is and what a picker chooses by.

The documents are the half this side sees least of. The server sees the calls
that happened, and a list nobody rendered makes no call at all.

The cost
~~~~~~~~

The cost at the end of the prompt is for whoever asked for the debrief, and it
reaches no file.
`D-FBK-020 <../../decisions/feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md>`_
measured what a session costs from the transcripts of this repository's own
worktree sessions. The sessions that get this prompt are the ones nothing here
can read. What comes back is one session's own figure, worth what the client
behind it reports.

That is why the session reads the figure rather than estimates it. It is also
why the session names what its client does not report. The prompt asks for the
cost only after the work, for the reason it asks about the tools only then. An
agent that knows a count of its calls is on its way makes fewer of them. What it
reports is then not the run that would have happened.

A transcript that starts late
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The prompt has one qualification,
`R-FBK-012 <../../requirements/feedback/fbk-012-a-debrief-reports-the-window-the-session-could-see.md>`_.
The prompt is where the ask for the transcript is. So the prompt is where a
session whose transcript begins at a summary has to say so.
