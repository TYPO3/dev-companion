:navigation-title: Working with it

Working with it once it runs
============================

This page covers what the server changes on your side of the conversation. It
names the few places where a sentence of yours decides whether the answer comes
from TYPO3 or from memory about it.

You do not need to learn anything to use it. You describe the work you want, in
the language you use with your agent. The agent uses the server while it does
the work.

You describe the task
---------------------

At connect, the server tells every client where to start. The agent starts with
what the project is, and then with the workflow the task belongs to. So an
ordinary request is enough to reach the right order of steps: *add a backend
module to this extension*, *review this patch*, *find out why this label is not
translated*. You do not have to name a tool yourself.

What you get back is the work of your agent, not the answer of the server. The
agent reads what the checkout says, and asks this server what it cannot see
there. It mixes the two in the reply. Where you want to know which half is
which, ask which tools it called.

Names reach further
-------------------

The knowledge is in English and the match runs word by word. So the agent
translates your subject before it asks. That works for a subject, but not for a
paraphrase.

Names like these land: ``sys_file_reference``, an icon identifier, the XLF
resource your code loads, a class name from the patch you review. "that thing
that shows the picture chooser" reaches nothing.

Where a lookup came back empty and you know the subject exists, ask again with
its name. That is usually the whole fix.

Before it writes markup
-----------------------

Three answers come from the installation rather than from memory. These three
fail at runtime on a guess: the markup and classes of a backend component, an
icon identifier and a label resource. The server tells your agent to look each
of them up before it writes.

You can check that, so it is worth a note. Look for backend markup with no
``typo3_component_lookup`` behind it, an icon identifier nobody validated, or a
label key the agent invented. Ask for the lookup afterwards. That costs one
call, and it is the same call the agent should have made.

What an answer contains
-----------------------

Every statement says which TYPO3 versions it holds for. Where something changed
between two of your covered lines, the answer names the range rather than picks
one. So an LTS never gets a convention only the development line has. Where you
work on an older line, say so in the task, and that narrows the answer.

Each answer also says which kind of work it belongs to: the contribution process
of the core, a project, or an extension. A rule that binds only core patches is
marked as one. The same person is often two of those in one checkout.

A miss is an answer
-------------------

Where nothing matches, the server says so. It gives no answer that only reads
like knowledge. The whole server depends on that behaviour, so read a miss as
information. The subject is outside what the server covers. Your agent now works
from its own knowledge and from the checkout.

``typo3_server_scope`` states the boundary in full: what the server covers, and
what it declines to answer on purpose.

.. _working-with-it-the-prompts:

The MCP prompts
---------------

Your agent calls a tool in the middle of a task. You pick an MCP prompt by name,
and the client puts its text into the conversation as your own message.
``prompts/list`` returns the prompts this server offers, with their descriptions
and arguments. See :doc:`the initialize page <../server/initialize>`.

* ``commit_message`` takes a ``summary``, and ``keyword``, ``workflow`` and
  ``issue`` where you give them. It calls ``typo3_commit_message_guide`` with
  the four. It gives you the checked draft as the guide wrote it. The rules are
  in the guide, so the prompt is a shorter route to the same answer.

* ``debrief`` takes nothing. It asks the session that has just finished the
  questions of :doc:`asking for a debrief <../records/asking-for-a-debrief>`.
  Only a standalone checkout lists it. Only there is the channel that the
  answers go into.

Each client offers a prompt in its own way. Claude Code lists each MCP prompt
under ``/`` as ``/typo3-dev-companion:commit_message (MCP)``.
``/mcp__typo3-dev-companion__commit_message`` runs the same prompt.

Claude Code gives the text after the command to the arguments, in the order the
server declares them. It splits that text on whitespace. So each argument is one
word, and Claude Code drops a surplus word. This command::

    /mcp__typo3-dev-companion__commit_message Rename the scope enum

gives ``Rename`` as the summary, ``the`` as the keyword, ``scope`` as the
workflow and ``enum`` as the issue. A summary with a space in it does not fit
through that command. Ask your agent for the draft instead. Every task skill
sends that request to ``typo3_commit_message_guide``, which takes the summary as
one string.

Some clients ask for each argument in a field of its own, Claude Desktop and the
MCP Inspector among them. Those clients take the summary as it is.

``debrief`` has no argument, so every client runs it the same way. Run it after
the work, in a message of its own. A session that knows the questions will come
makes calls to have an answer for them. The debrief then reports a run that did
not happen.

How this repository uses them
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Nothing in this repository runs either prompt as part of its own work. A forward
run pastes ``documentation/records/debrief.txt`` into the session by hand. It
runs in a client that this checkout cannot reach. The prompt reads that same
file, so the paste and the prompt cannot drift apart.

The task skills name ``typo3_commit_message_guide`` and never the prompt. The
model reads a skill at the start of a task, and a person picks a prompt after
it.
`D-FBK-048 <../../decisions/feedback/fbk-048-the-debrief-is-offered-as-a-prompt-where-the-channel-is.md>`_
gives the reasons.

``StdioServerTest`` holds both prompts. It sends ``prompts/list`` and
``prompts/get`` over the wire. ``bin/cli tools:index`` prints the list into the
initialize page.

What it never does
------------------

It writes nothing into your installation. Nothing you ask it changes a file
there. The one place it writes at all is its own checkout, when a session
records feedback from a clone of this repository.

It also starts nothing. The server reports a stopped DDEV project as stopped,
with the command that would start it. It does not start the project for you. So
a lookup never costs you containers you did not ask for.

Where a gap goes
----------------

The channel for gaps exists only where this package is the repository somebody
works in. A project does not have it. From a normal install, you reach the
people who maintain it through the issues of the repository. A useful report
says what you asked, what came back, and what you had to find out yourself
instead.
