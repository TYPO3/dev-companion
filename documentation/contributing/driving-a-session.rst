:navigation-title: Driving a session

Driving a session nobody types in
=================================

This page covers what must be true of an agent session that a command line
starts, rather than a person in an editor. Two kinds of work run in such a
session:

* **A forward run.** It is evidence, so somebody must find it afterwards.

* **A todo in a worktree.** Several sessions work one todo each, each in its own
  worktree.

The launch must get the same things right in both, so this page states them once
rather than on either page.

What the launch needs
---------------------

Clients use different names for the flags. The launch needs the same four things
in every client:

* **The current build.** A client that also ships inside an editor keeps its own
  binary there. The binary on ``PATH`` can be an old release pinned to a model
  that has since retired. That binary fails, and the failure looks like a fault
  of the work rather than of the client. So somebody searches the work for a
  fault that is in the client.

* **A session id the caller chose.** With it, somebody finds the transcript
  afterwards. A session without one still does the work, but nobody can grade
  it. For a forward run the grade is the point.

* **The work directory of the checkout the session is for**, and the MCP
  configuration of that checkout. That configuration names the server the
  environment would offer a person. Where the session works a worktree, both are
  the worktree.

* **Debug output on stderr.** Only there does a stalled tool call show. A client
  that waits on a call that never returns looks exactly like a client that
  thinks hard. *A run that hangs* in :doc:`../records/forward-runs` reads that
  stream.

The launch for a claim
----------------------

**Where each session works a claim, one file declares the launch once, and
nobody types it each time.** ``.session-command`` at the root of the checkout
holds the command line this machine starts a session with.
``bin/cli todo:claim`` runs it per worktree.

``todo:claim`` supplies the three things a command can supply:

* the worktree as the work directory,
* the message on standard input,
* ``TODO_SESSION_ID`` in the environment.

Each client has its own flags to pass them on to the session. So the file
belongs to the machine, and git ignores it.

A session nobody watches needs its tool calls approved without a question. Do
not switch the permission layer off altogether to get there. A client may refuse
that launch outright, and a session that never starts gives no result.

Where the transcript lands
--------------------------

The client writes one JSONL file per session, below its own state directory. The
name of the file derives from the checkout the session ran in. So a literal
search for a project with an underscore or a dot in its name finds nothing. Read
which skills activated and which tools the session called from that file, not
from what the session says about itself.

What the session hears
----------------------

What a session **hears** is a separate question, and the page about the work
answers it. :doc:`../records/working-todos-in-parallel` gives the prompt a
worktree session starts with. :doc:`../records/forward-runs` says that a forward
run gets its scenario prompt verbatim and nothing besides.
