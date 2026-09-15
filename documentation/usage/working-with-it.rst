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
