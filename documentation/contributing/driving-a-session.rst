:navigation-title: Driving a session

Driving a session nobody types in
=================================

Two things here run in an agent session started from a command line rather than
by a person in an editor: a forward run, which is evidence and has to be
findable afterwards, and the several sessions that work a todo each in their own
worktree. What the launch has to get right is the same in both, so it is here
once rather than on either page.

Clients differ in what the flags are called. They do not differ in what has to
be true of the launch.

* **The build that is current.** A client that also ships inside an editor keeps
  its own binary there, and the one on ``PATH`` can be an old release pinned to
  a model that has since been retired. It then fails looking like the work
  failing rather than the client being wrong, which is the expensive way round.
* **A session id the caller chose.** That is what makes the transcript findable
  afterwards. A session started without one still does the work; it cannot be
  graded, and for a forward run the grading is the point.
* **The working directory of the checkout the session is for**, and that
  checkout's own MCP configuration — the server the environment would have
  offered a person. Where the session works a worktree, both are the worktree.
* **Debug output on stderr.** The only place a stalled tool call is visible. A
  client waiting on a call that will never return looks exactly like a client
  thinking hard, and *When a run stops without an error* in
  :doc:`../records/forward-runs` is read off that stream.

**Where the sessions work a claim each, that launch is declared once rather than
typed each time.** ``.session-command`` at the root of the checkout holds the
command line this machine starts a session with, and ``bin/cli todo:claim`` runs
it per worktree: the worktree is the working directory, the message arrives on
standard input, and ``TODO_SESSION_ID`` is in the environment. The three things
a command can supply are supplied, and the flags they are passed by stay the
client's — which is why the file is the machine's and gitignored.

Approving the tool calls without asking is the point of a session nobody is
watching. Switching the permission layer off altogether is not the way to reach
it: a client may refuse the launch outright, and a session that never started is
not a result.

**Where the transcript lands.** One file per session, JSONL, below the client's
own state directory and under a name derived from the checkout the session ran
in — derived, so a project whose name carries an underscore or a dot is not
found by searching for it literally. Which skills activated and which tools were
called are read from there rather than from what the session says about itself.

What a session is **told** is a separate question, and it is answered where the
work is: :doc:`../records/working-todos-in-parallel` carries the prompt a
worktree session is started with, and :doc:`../records/forward-runs` says that a
forward run is given its scenario prompt verbatim and nothing besides.
