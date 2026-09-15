:navigation-title: Driving a session

Driving a session nobody types in
=================================

Two things here run in an agent session started from a command line rather than
by a person in an editor. A forward run, which is evidence and has to be
findable afterwards, and the several sessions that work a todo each in their own
worktree. What the launch has to get right is the same in both, so it is here
once rather than on either page.

Clients differ in the names of the flags. They do not differ in what has to be
true of the launch.

* **The build that is current.** A client that also ships inside an editor keeps
  its own binary there. The one on ``PATH`` can be an old release pinned to a
  model that has since retired. It then fails, and the failure looks like the
  work's rather than the client's, which is the expensive way round.
* **A session id the caller chose.** That is what makes the transcript findable
  afterwards. A session started without one still does the work. Nobody can
  grade it, and for a forward run the grade is the point.
* **The work directory of the checkout the session is for**, and that checkout's
  own MCP configuration, the server the environment would have offered a person.
  Where the session works a worktree, both are the worktree.
* **Debug output on stderr.** The only place a stalled tool call is visible. A
  client that waits on a call that will never return looks exactly like a client
  that thinks hard. *When a run stops without an error* in
  :doc:`../records/forward-runs` reads off that stream.

**Where the sessions work a claim each, the launch stands declared once rather
than typed each time.** ``.session-command`` at the root of the checkout holds
the command line this machine starts a session with, and ``bin/cli todo:claim``
runs it per worktree. The worktree is the work directory, the message arrives on
standard input, and ``TODO_SESSION_ID`` is in the environment. The command
supplies the three things a command can supply, and the flags that pass them
stay the client's. That is why the file is the machine's and gitignored.

Approval of the tool calls without a question is the point of a session nobody
watches. The permission layer switched off altogether is not the way to reach
it. A client may refuse the launch outright, and a session that never started is
not a result.

**Where the transcript lands.** One file per session, JSONL, below the client's
own state directory and under a name that derives from the checkout the session
ran in. It derives, so a literal search for a project whose name carries an
underscore or a dot finds nothing. Which skills activated and which tools the
session called come from there rather than from what the session says about
itself.

What a session **hears** is a separate question, and the answer sits where the
work is. :doc:`../records/working-todos-in-parallel` carries the prompt a
worktree session starts with. :doc:`../records/forward-runs` says that a forward
run gets its scenario prompt verbatim and nothing besides.
