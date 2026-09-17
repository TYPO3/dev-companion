:navigation-title: Working with it

Working with it once it runs
============================

There is nothing to learn to drive. You describe the work you want, in whatever
language you speak to your agent in. The server is what the agent reaches for
while it does it. What follows is what that changes about your side of the
conversation. The few places where a sentence of yours decides whether the
answer comes from TYPO3 or from memory about it.

You describe the task
---------------------

The server tells every client at connect where to start: with what the project
is, and then with the workflow the task belongs to. So an ordinary request is
enough to reach the right order of steps. *add a backend module to this
extension*, *review this patch*, *find out why this label is not translated*.
You do not have to name a tool yourself.

What you get back is your agent's work, not the server's answer. It reads what
the checkout says, asks this server what it cannot see there, and mixes the two
in the reply. Where you want to know which half is which, ask which tools it
called.

Names reach further
-------------------

The knowledge is English and the match runs word by word, so the agent
translates your subject before it asks. That works for a subject and not for a
paraphrase. ``sys_file_reference``, the identifier of an icon, the name of the
XLF resource your code loads, a class name out of the patch you review. All of
those land. "that thing that shows the picture chooser" reaches nothing.

Where a lookup came back empty and you know the subject exists, its name is
usually the whole fix.

Before it writes markup
-----------------------

Three answers come from the installation rather than from memory, and they are
the three that fail at runtime on a guess. The markup and classes of a backend
component, an icon identifier, and a label resource. The server tells your agent
to look each of them up before it writes.

That is worth a note because it is checkable. Backend markup that appeared with
no ``typo3_component_lookup`` behind it, an icon identifier nobody validated, a
label key invented rather than looked up. An ask for the lookup afterwards costs
one call, and it is the same call the agent should have made.

What an answer carries
----------------------

Every statement says which TYPO3 versions it holds for. Where something changed
between two of your covered lines, the answer names the range rather than picks
one. So an LTS never gets a convention only the development line has. Where you
work on an older line, say so in the task and that narrows it.

Each answer also says which kind of work it belongs to: the core's own
contribution process, a project, an extension. A rule that only binds core
patches arrives marked as one, because the same person is often two of those in
one checkout.

A miss is an answer
-------------------

Where nothing matches, the server says so instead of an answer that reads like
knowledge. That is the behaviour the whole thing rests on, and it is worth a
read as information. The subject is outside what it covers, and your agent now
works from its own knowledge and from the checkout.

``typo3_server_scope`` is where the boundary stands written out: what it covers,
and what it deliberately declines to answer.

.. _working-with-it-the-prompts:

The MCP prompts
---------------

A tool is what your agent calls in the middle of a task. An MCP prompt is what
you pick by name, and the client puts its text into the conversation as your own
message. ``prompts/list`` carries the ones this server offers, with their
descriptions and arguments, see
:doc:`the initialize page <../server/initialize>`.

* ``commit_message`` takes a ``summary``, and ``keyword``, ``workflow`` and
  ``issue`` where you give them. It calls ``typo3_commit_message_guide`` with
  the four and hands you the checked draft as the guide wrote it. The rules
  stand in the guide, so the prompt is a shorter route to the same answer.
* ``debrief`` takes nothing. It puts the questions of
  :doc:`asking for a debrief <../records/asking-for-a-debrief>` to the session
  that has just finished. Only a standalone checkout lists it, because only
  there is the channel the answers go into.

How a client offers one is the client's. Claude Code lists each MCP prompt under
``/`` as ``/typo3-dev-companion:commit_message (MCP)``, and
``/mcp__typo3-dev-companion__commit_message`` runs the same. It hands what
follows the command to the arguments in the order the server declares them, and
it splits that text on whitespace. So each argument is one word, and it drops a
surplus word::

    /mcp__typo3-dev-companion__commit_message Rename the scope enum

hands ``Rename`` as the summary, ``the`` as the keyword, ``scope`` as the
workflow and ``enum`` as the issue. A summary with a space in it does not fit
through that command. Ask your agent for the draft instead. Every task skill
routes it to ``typo3_commit_message_guide``, which takes the summary as one
string. A client that asks for each argument in a field of its own, Claude
Desktop and the MCP Inspector among them, takes the summary as it is.

``debrief`` has no argument, so every client runs it the same. Run it after the
work, in a message of its own. A session that knows the questions are coming
makes calls to have an answer for them, and the debrief then reports a run that
did not happen.

Nothing in this repository runs either prompt as part of its own work. A forward
run pastes ``documentation/records/debrief.txt`` into the session by hand,
because it runs in a client this checkout cannot reach. The prompt reads that
same file, so the paste and the prompt cannot drift apart. The task skills name
``typo3_commit_message_guide`` and never the prompt. The model reads a skill at
the start of a task, and a person picks a prompt after it,
`D-FBK-048 <../../decisions/feedback/fbk-048-the-debrief-is-offered-as-a-prompt-where-the-channel-is.md>`_.
What holds them is ``StdioServerTest``, which drives ``prompts/list`` and
``prompts/get`` over the wire, and ``bin/cli tools:index``, which prints the
list into the initialize page.

What it never does
------------------

It does not write into your installation. Nothing you ask it changes a file
there. The one place it writes at all is its own checkout, when a session files
feedback from a clone of this repository.

It also starts nothing. The server reports a stopped DDEV project as stopped,
with the command that would start it, rather than starts it under you. So a
lookup never costs you containers you did not ask for.

Where a gap goes
----------------

The channel a session reports gaps through only exists where this package is the
repository somebody works in. From a project it is not there. What reaches the
people who maintain it from a normal install is the repository's own issues. The
useful report is the one that carries what you asked, what came back, and what
you had to establish yourself instead.
