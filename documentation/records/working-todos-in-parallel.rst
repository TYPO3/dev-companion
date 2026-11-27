:navigation-title: Todos in parallel

Working several todos at once
=============================

This page covers how several sessions work several todos at once. It says how
each session gets different work, where each writes, and how its work comes
back. One session still works one todo as :doc:`working-a-todo` describes, and
that page holds here unchanged. Nothing on this page replaces the read, the
research or the question that a todo needs.

Parallel work pays where the queue holds work that does not overlap. That is
most of what accumulates here: entries that each name a decision and touch
different parts of the checkout. It does not pay for two todos about one file,
and it does not pay for one todo.

The steps at a glance:

1. Run ``bin/cli todo:claim <n>``.
2. Start one session per worktree, with the message the claim printed.
3. Each session works its todo and commits on its own branch.
4. A question mid-work goes into the ``waitingOn:`` line of the todo.
5. Bring each finished branch home with ``bin/cli todo:home <id>``.

.. diagram::
    :zoomable:
    :caption: A worktree is the claim. Half-finished changes remain isolated until verified.

    flow canvas=960
      columns 1,1,1
    title "Main holds state; branches hold work"
    description "A worktree per todo says which session has it, and one branch carries each session's unfinished work. Completed branches return one at a time through rebase, checks and a fast-forward merge."

    main `main`
      at 0,0
      span 3
      body "todo/open stays as it is. bin/cli todo:claim <n> gives a todo its branch and worktree, and that worktree is the claim."
      ink
    alpha "Branch + worktree"
      at 0,1
      tag `todo/alpha`
      item "own composer install"
      item "one session, one worktree"
      item "unfinished diff stays here"
    bravo "Branch + worktree"
      at 1,1
      tag `todo/bravo`
      item "reads the same queue"
      item "touches its own todo"
      item "knows nothing of siblings"
    charlie "Branch + worktree"
      at 2,1
      tag `todo/charlie`
      item "a question becomes Waiting on"
      item "the branch keeps completed work"
      item "todo:park moves the question"
    home "Bring home one branch at a time"
      at 0,2
      span 3
      body "rebase main → composer ci → merge --ff-only → remove worktree. Then repeat for the next completed branch."
      emphasis

    main -> alpha
    main -> bravo
    main -> charlie
    alpha -> home
    bravo -> home
    charlie -> home

The worktree and the branch
---------------------------

**The worktree says who has what, and the branch holds the work.**

That is the whole arrangement, and every rule on this page follows from it. A
worktree on the branch of a todo says that somebody has that todo in hand. Every
command that gives out work passes over that todo. The half-finished diff is on
the branch of that worktree, where nobody else has to look at it.

No file records the claim (``D-DOC-060``). The branch is the id of the todo, and
the worktree takes the name of the branch. So ``bin/cli todo:list`` answers the
question from the worktrees that exist, and it cannot disagree with itself.

**A todo's name is its id**, ``T-<yymmdd>-<hash>``. Every listing prints it, and
every command takes it. The worktree is how this repository holds a todo in
hand, and a caller does not need to know about it. So ``todo:home`` and
``todo:drop`` take the id. They also accept the directory name of the worktree,
because it is there.

So a session moves no file to take a todo on or to give it back. A todo whose
worktree came down is workable again, with nothing to put back. A finished todo
is a deletion that the branch already holds.

Taking them on
--------------

.. code-block:: bash

    bin/cli todo:claim 3

**That command is the whole setup.** It makes three worktrees, one per todo.
Each is on the branch that the name of its todo derives, and each has its own
``composer install``. From then on ``bin/cli todo:next`` offers those three to
nobody, and the fourth session gets the todo behind them. The command also
prints the message the three sessions start with. What remains is to start them.

Nothing moves, and nothing goes into a commit. The queue is the same file it
was. What changed is that three of its todos now have a worktree on their
branch.

A session may have worked a todo before, and the branch that todo derives may
still exist. The command passes it over and names it, rather than reuse or
delete it, because that branch still holds the finished half. A worktree that
quietly attaches to an old branch is the one failure here that looks like
success.

The command also prints an overlap, in the three ways two claims can overlap.
Nothing here knows which lines a step will touch. So all three are a warning to
read before the worktrees exist, not a refusal:

* Two claims that **answer for** one entry are two sessions that edit one file.
  One of them is cheaper to take than both are to merge.

* Two claims that **name** one class are the same case, one step less certain. A
  todo says where it is about to work: as a path, as ``Class::method()``, or as
  the bare name. The claim resolves all three to the file.

* Two claims that **stand on** one requirement or decision, without service to
  it, work from a single judgement. A pair of steps that have to agree comes
  from there.

``bin/cli todo:drop <id>`` is the way back out, for a todo that nobody works.
That is a session that never started, one that somebody left, or a claim taken
by mistake. There is nothing to put back. The todo is where it always was, and
the removal of the worktree offers it again.

``todo:drop`` decides about the branch. A branch with commits stays, because it
is the only place where that work exists. A branch with none goes, because a
branch nobody takes down is a todo that ``todo:claim`` passes over for good.
``bin/cli todo:home`` is the other end, for finished work. It rebases, checks
and merges, and none of that means anything for a branch with nothing on it.

Why three kinds of overlap
~~~~~~~~~~~~~~~~~~~~~~~~~~

The last two kinds exist because ``serves:`` alone missed the collision that
cost the most. On 2026-08-02, two todos with different ``serves:`` keys each
added a handler for one token to one function. The rebase put them in sequence,
each with a ``continue`` at its end, and nothing ever reached the second.

Both had named ``R-ANS-012``, and both had named the class: one as
``Extension::describe()``, the other as ``src/Installation/Extension.php``.
Neither is a declaration, and neither had to be. It is a session that says where
it goes, in the file the claim reads anyway.

The worktree
------------

``todo:claim`` makes one worktree per claim. The worktree is on the branch of
the claim, has its own ``composer install``, and has ``.checkouts/`` symlinked
in. This section says what the command does, because a worktree made some other
way has to do the same.

**Run ``composer install`` in the worktree. Never symlink ``vendor/``.**

That mistake costs an afternoon to find. ``Paths::root()`` is the directory
above ``src/``. The autoload map of Composer resolves ``src/`` from wherever the
autoloader physically sits. So a symlinked ``vendor/`` points every path in this
repository back at the main checkout. ``bin/cli`` then reads and writes the
todos, requirements and decisions of a checkout the session is not in, and
nothing in the output looks wrong.

``.checkouts/`` is the opposite case, and a symlink on purpose. It is 861 MB,
git ignores it, and a session at work on a todo only ever reads it. Only
``bin/cli checkouts:update`` writes there, and that is not the work of a claim.

Starting the sessions
---------------------

Start one session per worktree, in that worktree. Give all three the same
message: the one ``todo:claim`` printed. This page does not repeat it, because a
copy here and a copy in the command are two things to keep in step. Only one of
them would go out.

:doc:`../contributing/driving-a-session` says how a session launches at all:
which build, from where, with what switched on. It is the same launch a forward
run uses.

**Better: let the claim start them.** Put the command line that starts a session
on this machine into ``.session-command`` at the root of the checkout.
``todo:claim`` runs it once per worktree, with three inputs:

* that worktree as the work directory
* the message on standard input
* ``TODO_SESSION_ID`` in the environment

Git ignores the file, because how a session launches is a property of the
machine, not of the repository. :doc:`../contributing/driving-a-session` says
what the launch has to get right. Each session reports into
``.worktrees/.sessions/<name>.log``.

That is the fourth step beside the other three, and it is here for the same
reason they are. A step left over for somebody to carry out from a read is the
step that breaks.

Without the file, nothing starts, and the command prints the handover instead.
That is one absolute ``cd`` per worktree, and the message under a line that says
the rest of the output is not part of it.

**Nothing in that message needs a fill.** Every session starts with the same
characters, and there is nothing to get wrong.

So the message cannot name which todo belongs to which session. The prompt never
had to answer that. A session has no reason to doubt a path it gets. A worktree
on a branch the claim never named passes every check the session can make. So
does one cut from a ``main`` that did not have the claim yet. The session then
reads the queue and finds real work that belongs to somebody else.

So the answer comes out of the checkout instead:

.. code-block:: bash

    bin/cli todo:next --worktree

It is the same command every session in this repository starts with. The flag
says what the prompt used to say: *this session is one of several*. On a claim,
the command gives out that claim and names the branch the claim is on. On none,
it says which case it is and stops. The cases are the wrong branch, a claim that
was not on ``main`` yet, or a session started in the main checkout at all. Where
it refuses, the session ends there, and does not look for something else to do.

What went wrong before
~~~~~~~~~~~~~~~~~~~~~~

The run of 2026-08-02 started every session in the directory that was already
open. Three worktrees stood untouched, while the sessions read a queue that
belonged to somebody else. Which directory a session starts in was a sentence
once, *with that worktree as its working directory*. A property somebody
satisfies is a blank in prose.

The message was a template, with the worktree path and the branch left as
blanks. The run that broke sent it as it was. The session read
``<absolute path to the worktree>`` as a line meant for it, not as one somebody
had forgotten. No check it could make would have told it otherwise. A message
with a blank in it is a message somebody fills in. So the message now has no
blank, and that was the whole fix.

What the session does
---------------------

Nothing about the session is special. It reads what the todo serves, settles
what the step turns on, and leaves the file true. That is all of
:doc:`working-a-todo`, which the command names as usual.

Five rules are different, and all of them follow because ``main`` is elsewhere.
The claim comes with them attached. Each rule gives its reason:

* **Commit on the branch, never on ``main``.** That includes the todo file
  itself. A finished claim is a deletion in the branch, and the merge brings it
  to ``main``.

* **Leave the group listings alone.** A group listing is the block at the foot
  of a ``requirements/<group>/readme.md`` or ``decisions/<group>/readme.md``. It
  comes from every file in the group, and a worktree sees only its own new
  entry. Leave alone both the commands, ``bin/cli requirements:index`` and
  ``bin/cli decisions:index``, and the line. Two sessions that edit one listing
  by hand conflict, where two sessions that leave it alone do not.

  ``todo:home`` runs both commands in the worktree once the rebase has made them
  right. It amends what they wrote onto the branch's own commit. Nothing in
  ``composer ci`` checks the listing, on purpose. A suite that held the listing
  would fail every branch that adds an entry, on the one line the branch may not
  touch (``D-FBK-011``).

* **Rewrap what this branch changed, and leave the corpus to the checkout.** A
  card that another claim deletes is a conflict, whichever side rewrapped it.
  Twice on 2026-08-27 that stopped ``todo:home``, with the rebase aborted behind
  it. ``bin/cli prose:format`` with no path narrows itself here, so nobody has
  to remember a path. So the line is the same one every session runs
  (``D-DOC-063``).

* **Say nothing about what another branch has done.** The state of a sibling is
  the one fact a worktree cannot check. A written claim about it is how a run of
  ten leaves two feedback that answer for nothing.

  Twice now, both halves of a pair wrote the same sentence: *the feedback stays
  open behind the sibling todo, which is another session's claim*. Both times
  the sibling had finished. Each session was right about its own half and wrong
  about the half it could not see. What is true instead is shorter: this half
  ends here, and whether the entry closes depends on the other, which nobody
  read here. ``TodoTest::everyOpenFeedbackIsOnTheBoard`` catches the leftover on
  the rebase, once both halves are on ``main``.

* **Point at entries, not at positions.** In a file that two sessions both add
  to, *above*, *below*, *once*, *the first* and *the other* are all claims about
  a layout. By the time anybody reads them, that layout has one more section in
  it. Name the feedback, the requirement or the decision instead: those survive
  whatever lands beside them.

  The run of 2026-08-02 wrote *this entry has been cited once* into an entry
  that ended the day with three citations. It wrote *the paragraph above* into
  one that gained three sections in between. It wrote *the **Wrong if** got its
  other answer* twice, in two accounts that could not both be the other.

A question mid-work
-------------------

A todo that needs an answer nobody here can give is the normal case, not the
exception. A session that works alone asks and waits. One of several cannot: a
wait blocks a worktree on a person who answers three others.

So the session records the question instead of asking it. The question goes into
the ``waitingOn:`` line of the todo itself, in the words of the ask. What the
read already established goes with it. Then the session commits what it has and
ends. The branch keeps the finished half.

What happens next depends on whether the work behind the question stands on its
own. Most of the time it does: the session settled one half of its todo, and the
question is about the other. Then the branch merges like any other, and the todo
with the question comes with it. Where the work does not stand on its own, only
the todo comes back:

.. code-block:: bash

    git checkout <branch> -- todo/open/<id>-<name>.md

That keeps a half-finished change off ``main``. ``main`` still says, in one
place, what is open and where the work behind it is.

**A todo with a question does not stay in the queue.** The next session would
get it as ordinary work, and what it needs is a person. ``bin/cli todo:park``
moves every queued todo that names a question into ``waiting/``. ``todo:home``
runs it in the worktree, so the move reaches ``main`` in the commit the work is
in.

Look also for a worktree on a todo that nobody works. Nothing notices one on its
own. ``bin/cli todo:home`` with no argument makes that check. It prints the
worktrees that exist, and whether anything on them waits for a commit.

Bringing the branches home
--------------------------

**Bring a finished run home now.** Check it, merge it and remove its worktree,
and wait for none of the sessions still at work. Nine branches held back until
the tenth reports each need a bigger rebase when they finally move. ``main``
went on without them, and the wait bought only a tidier moment. There is no
batch here. There are ten sequences of the same steps, each started when its
session ends.

**One at a time, rebased onto ``main`` and fast-forwarded — no merge commits.**

.. code-block:: bash

    bin/cli todo:home <id>

**That command is the whole of it**, the way ``todo:claim`` is the whole of the
setup. It exists for the same reason. The steps have to happen in one order, and
prose cannot hold anybody to an order. With no name, it reports the worktrees
that exist, and which of them has a tree that nobody committed. It refuses in a
worktree, and it refuses off ``main``. A fast-forward onto somebody's branch is
the failure that looks like success.

Nothing here decides that a session has ended, and nothing can: the caller names
the todos. For each of them, the command runs this, in the worktree of that
todo:

.. code-block:: bash

    git -C .worktrees/<name> rebase main
    (cd .worktrees/<name> && bin/cli requirements:index && bin/cli decisions:index)
    (cd .worktrees/<name> && bin/cli links:repair)
    git -C .worktrees/<name> commit --amend --no-edit -- <what they rewrote>
    (cd .worktrees/<name> && composer ci)
    git merge --ff-only todo/<name>
    git worktree remove .worktrees/<name> && git branch -d todo/<name>

``main`` moves while the sessions run. So a branch cut hours ago is behind it,
and git cannot fast-forward it as it is. The rebase makes the merge a
fast-forward, and ``--ff-only`` checks that. Where it refuses, something is not
what this procedure assumes, and that is worth a stop. The result is one
sequence of commits on ``main``. A merge commit per claim would say only that a
claim existed.

**The sequence belongs to one branch, and the next branch starts it again from
the top.** ``main`` moved when the last merge landed, so a branch rebased before
it is behind again. The run of 2026-08-02 made two merges from one rebase.
``--ff-only`` caught that, which is what it is for. Remove the worktree
**after** the merge, never before. Removed early, it takes the only checkout the
rebase and the suite can run in, and its return costs a fresh
``composer install``.

**``composer ci`` runs in the worktree, after the rebase.** That is the first
moment the work of the session rests on what ``main`` has become. It is the only
run that says anything. A run from before the rebase checked a tree that no
longer exists. One branch at a time, for the same reason: a suite that fails
after three branches says nothing about which one broke it. The claims
themselves never conflict, because each session touched one file in ``todo/``
and its own.

Where one branch fixes something the others also fail on, that branch goes
first. Otherwise every merge behind it runs against a suite that was already
red, and this order exists to avoid exactly that.

**The rebase also makes the group listings writable, and they go onto the
branch.** The listing at the foot of a group readme comes from every file in
that group. So a session at work on its todo sees only its own new entry, and it
has to leave the block alone. Once the branch is on top of ``main``, nothing
rebases onto it again. The two index commands then produce exactly what the
fast-forward should bring. ``todo:home`` amends what they wrote onto the
branch's own commit, because a listing line and the entry it lists are one
change.

Before 2026-08-18, the listings went onto ``main`` afterwards instead. They were
37 of the 200 commits before that day. Each said nothing but that the one under
it had merged (``D-FBK-011``).

**A link to a feedback this branch archived gets its repair on the same tree.**
``feedback:archive`` runs before the link that breaks exists. A decision written
on another branch in the same window names the report where it was. Whichever of
the two merges second brings a path to a file that moved. Three branches stopped
there on 2026-08-27.

The repair is the same every time: the path gains ``archive/``. So
``bin/cli links:repair`` makes it. ``links:check`` names it, for a checkout that
met the same thing some other way (``D-DOC-064``).

Then ``todo:home`` also runs this on ``main``, once the branch is in:

.. code-block:: bash

    bin/cli repository:check

The queue once collided here too. Two sessions that each queued new work read
the same last number, and both took it. A todo has no number now, so nothing
collides. Two todos are both ``normal``, and the older one is older, whichever
branch each arrived on.

**An id still collides, and that is the one to expect.** A requirement and a
decision have a number, and every session reads the same last number. Ten
sessions that read it at once produce duplicates. The run of 2026-08-02 wrote
``D-ANS-009`` twice and ``D-FBK-018`` twice. Nothing can prevent it, and nothing
needs to.

Once the first branch is on ``main``, ``composer ci`` in the second branch fails
on *two decision files claim the same id*. That is the rebase at its job. The
failure names both files and the renumber command, so read it where it lands
rather than here. Renumber the later one, fix what names it, and amend; the
check goes quiet. Whichever branch merged first keeps the number. So the order
in which the work came home decides it, not an arbiter.

**The renumbering is what is dangerous, not the collision.** Four runs of ten
produced seven collisions. Twice, the files that named the old number did not
all mean the same entry. ``R-PRJ-008`` rested on the ``D-ANS-013`` that kept it,
while five other files meant the one that became ``D-ANS-015``. ``ans-006``
named the ``D-ANS-016`` that stayed, while a requirement and a todo named the
one that became ``D-ANS-019``. A search and replace over the id is wrong in
exactly those cases. It is silent, and no check fails afterwards: the entry it
now points at is real.

.. code-block:: bash

    bin/cli decisions:renumber <the branch's own file>

Name the file, not the id. Both files have the id, so the id says which number
the caller means, not which entry moves. The command refuses an id that two
files claim, and names both files. Two runs of 2026-08-18 moved the entry
already on ``main`` back, when the command picked between them itself.

The command moves the entry and every reference whose own line names its file.
It prints the rest. The rest are the bare references, and both wrong pointers
were bare ones. Read those one at a time. ``git diff main -- <file>`` settles an
ambiguous one: a line this branch added means this branch's entry. The list is
complete, so a reference nobody read is one that somebody skipped, not one that
nothing mentioned —
`D-DOC-015 <../../decisions/documentation/doc-015-a-renumber-moves-what-a-link-path-settles-and-names-the-rest.md>`_.

**A marker that survives the resolution is what nothing used to catch.** A file
with a ``>>>>>>>`` left in it parses, lints, and passes every test that does not
happen to read it. The run of 2026-08-02 put one into a decision.
``composer ci`` went green over it, in a commit whose diff looked deliberate.
``StructureTest::noFileCarriesAConflictMarker`` reads every file this repository
keeps. So the suite the branch already runs after its rebase now finds the
marker.

**Two sessions can also land on one entry** where nothing declared it. The
overlap that ``todo:claim`` reports comes from what the todos say. This one
comes from the judgement, not from a file before it. Two of the ten judged
different feedback into the same ``D-SKL-001``, and the rebase showed that as a
conflict in the file.

Usually both paragraphs belong. Each is an account of one read, which is what a
**Since then** holds. So resolve the conflict with a heading for each, not with
a choice between them.

**A todo with a question parks here**, and it is the one thing the session could
not do itself. The session ended on the question and left the todo in the queue,
where the next session would get it as ordinary work. So ``todo:home`` runs
``bin/cli todo:park`` in the worktree, beside the listings. The move then
reaches ``main`` in the commit the work is in. To give the todo back needs
nothing at all. The worktree comes down two steps later, and that is what
offered it to nobody.
