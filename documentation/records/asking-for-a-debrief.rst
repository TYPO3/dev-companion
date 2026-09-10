Asking for a debrief
====================

A session in a client this repository can read leaves a transcript, and which
skills activated and which tools were called are evidence in it. A session in
somebody else's agent leaves nothing here. The only thing that can report such a
run is the session itself, and it will not do so unprompted — an agent that
finished its task considers itself done.

So it is asked, in a message of its own **after** the work is finished. Before
or alongside it, the debrief becomes part of the task: an agent told it will be
asked which tools helped calls tools to have an answer. The prompt is generic on
purpose — it names no scenario, no skill and no tool, so the same text works
after a review, an implementation, or a question the server could not answer at
all.

What it asks for is the report, never the shape of the feedback. What each field
wants — one feedback per subject, the task named in the first line, where to
read the model identifier rather than remember it — belongs to
``typo3_feedback_record`` and is written in its parameters, which is the only
documentation a client actually reads. A prompt that restates it is a second
copy that ages, and it would only reach the sessions somebody handed it to.

Half of what it asks about did not happen. The calls a session made are in front
of it; the skill that never activated, the tool it passed over and the question
it never asked leave nothing behind. A list that asks only what happened
confirms the surface the server already has —
`D-AUD-003 <../../decisions/audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md>`_
is a session the published skill's body would have carried, which never loaded
it and went through Bash instead.

The other thing the list has to work for is the knowledge. A session reports
what it did, and what it did is call tools, so bullets weighted towards skills,
calls and names come back describing skills, calls and names while the corpus
the task actually turned on goes unmentioned. Three of them ask about it
instead: the answer that stopped one step short, which is what the corpus fails
at rather than absence; the wish, asked without the scope test the session would
otherwise apply to it; and what was established elsewhere, asked for all of it
and sorted here rather than there —
`D-FBK-047 <../../decisions/feedback/fbk-047-the-debrief-asks-what-an-answer-left-out.md>`_.

The documents are asked about on their own because the model never picks one. A
resource is chosen by the host application or by the user rather than mid-task,
so a client that lists none leaves the session nothing to find. What that
surface is and what a picker chooses by:
:doc:`the resource surface <../server/resources/index>`. It is the half this
side is blindest to — the server sees the calls that were made, and a list
nobody rendered makes no call at all.

.. literalinclude:: debrief.txt
   :language: text

Where the server runs from a standalone checkout, that file is also the
``debrief`` prompt, offered under the condition ``Channel::isAvailable()``
already puts on the two feedback tools: a session that cannot record a feedback
has no use for the questions. A prompt is invoked by the person rather than
offered to the model, which is what keeps the coming debrief out of the session
while it is still working —
`D-FBK-048 <../../decisions/feedback/fbk-048-the-debrief-is-offered-as-a-prompt-where-the-channel-is.md>`_.
It takes no arguments and reads the file this page includes, so neither route is
a version behind the other.

Paste it verbatim and add nothing — no tool names, no hint about what the last
session reported. The summary the agent gives afterwards is not what was
recorded; the feedback is, and ``typo3_feedback_list`` is where it is read back.

The cost at the end is for whoever asked for the debrief, and it reaches no
file.
`D-FBK-020 <../../decisions/feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md>`_
measured what a session costs from the transcripts of this repository's own
worktree sessions, and the sessions this prompt is handed to are the ones
nothing here can read. What comes back is one session's own figure, worth what
the client behind it reports — which is why it is read rather than estimated,
and why a client that reports none of it is named. It is asked last for the
reason the tools are: an agent that knows its calls will be counted makes fewer
of them, and what gets reported is then not the run that would have happened.

The one qualification it carries is
`R-FBK-012 <../../requirements/feedback/fbk-012-a-debrief-reports-the-window-the-session-could-see.md>`_.
The prompt is where the transcript is asked for, so it is where a session whose
transcript begins at a summary is asked to say so.
