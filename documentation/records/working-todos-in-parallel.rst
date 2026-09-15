:navigation-title: Todos in parallel

Working several todos at once
=============================

One session works one todo, and everything about that is :doc:`working-a-todo` —
unchanged here. This page is the part around it: how several sessions get
different work, where each of them writes, and how what they wrote comes back.
Nothing on it replaces the read, the research or the question a todo has a claim
on.

It is worth it where the queue holds work that does not overlap, which is most
of what accumulates here. Entries that name a decision each and stand against
different parts of the checkout. It is not worth the cost for two todos about
one file, and it is not worth it for one.

.. image:: ../images/parallel-todos.svg
    :zoomable:
        :alt: A worktree per todo says which session has it, and one branch carries
          each session's unfinished work. Completed branches return one at a
          time through rebase, checks and a fast-forward merge.

The worktree and the branch
---------------------------

**The worktree says who has what, the branch carries the work.**

That is the whole arrangement, and every rule below follows from it. A todo one
worktree stands on is one somebody has in hand, and every command that hands out
work passes over it. The half-finished diff is on that worktree's branch, where
nobody else has to look at it.

Nothing stands in a file to say so (``D-DOC-060``). The branch is the todo's id
and the worktree takes the branch's name. So ``bin/cli todo:list`` answers the
question from what stands and cannot disagree with itself.

**A todo's name is its id**, ``T-<yymmdd>-<hash>``, which every listing prints
and every command takes. That the work happens in a worktree is how this
repository holds a todo in hand, not something a caller has to know. So
``todo:home`` and ``todo:drop`` take the id, and accept the worktree's directory
name because it is there.

What that buys is that to take a todo on and to give it back are not moves. A
todo whose worktree came down is workable again with nothing to put back. A
finished todo is a deletion the branch already carries.

Taking them on
--------------

.. code-block:: bash

    bin/cli todo:claim 3

**That command is the whole setup.** Three worktrees, one per todo, each on the
branch that todo's name derives and each with its own ``composer install``. From
then on ``bin/cli todo:next`` offers those three to nobody, and the fourth
session gets the item behind them. Then the message the three sessions start
with. What remains is to start them.

Nothing moves and nothing goes into a commit. The queue is the same file it was
— what changed is that three of its todos now have a worktree that stands on
them.

Where a session worked a todo before, the branch it derives to may still be
there. The command passes it over and names it rather than reuses or deletes it,
because that branch still holds the finished half. A worktree that quietly
attaches to an old branch is the one failure here that looks like success.

What it prints besides the branches is an overlap, in the three ways two claims
can have one. Nothing here knows which lines a step will touch, so all three are
a warning to read before the worktrees exist rather than a refusal.

Two claims that **answer for** one entry are two sessions that edit one file.
One of them is cheaper to take than both are to merge. Two that **name** one
class are the same a step less certainly. A todo says where it is about to work,
as a path, as ``Class::method()``, or as the bare name. The claim resolves all
three to the file. Two that **stand on** one requirement or decision without
service to it work from a single judgement. That is where a pair of steps that
have to agree comes from.

The last two are here because ``serves:`` alone missed the collision that cost
the most. On 2026-08-02 two todos with different ``serves:`` keys each added a
handler for one token to one function. The rebase put them in sequence, each
with a ``continue`` at its end, and nothing ever reached the second. Both had
named ``R-ANS-012`` and both had named the class — one as
``Extension::describe()``, the other as ``src/Installation/Extension.php``.
Neither is a declaration and neither had to be: it is a session that says where
it goes, in the file the claim reads anyway.

``bin/cli todo:drop <id>`` is the way back out, for a todo nobody works. A
session that never started, one somebody left, a claim taken by mistake. There
is nothing to put back: the todo is where it always was, and the removal of the
worktree is what offers it again.

What it decides is the branch. One with commits stays, because it is the only
place that work exists. One with none goes, because a branch nobody takes down
is a todo ``todo:claim`` passes over for good. ``bin/cli todo:home`` is the
other end, for finished work. It rebases, checks and merges, none of which means
anything for a branch with nothing on it.

The worktree
------------

One per claim, made by ``todo:claim``: a worktree on the claim's branch, its own
``composer install``, and ``.checkouts/`` symlinked in. What it does by hand is
here because it is what the command does. A worktree made some other way has to
do the same.

**Run ``composer install`` in the worktree. Never symlink ``vendor/``.**

That one costs an afternoon to find. ``Paths::root()`` is the directory above
``src/``, and Composer's autoload map resolves ``src/`` from wherever the
autoloader physically sits. So a symlinked ``vendor/`` points every path in this
repository back at the main checkout. ``bin/cli`` then reads and writes the
todos, requirements and decisions of the checkout the session is not in, and
nothing about the output looks wrong.

``.checkouts/`` is the opposite case and a symlink on purpose. It is 861 MB, git
ignores it, and a session at work on a todo only ever reads it. Only
``bin/cli checkouts:update`` writes there, and that is not a claim's work.

Starting the sessions
---------------------

One session per worktree, started in that worktree, and all three get the same
message — the one ``todo:claim`` printed. It is not on this page, because a copy
here and a copy in the command are two things to keep in step. Only one of them
would go out. How the session launches at all, which build, from where, with
what switched on, is :doc:`../contributing/driving-a-session`. It is the same
launch a forward run uses.

**Better: let the claim start them.** Put the command line that starts a session
on this machine into ``.session-command`` at the root of the checkout.
``todo:claim`` runs it once per worktree. That worktree as the work directory,
the message on standard input, ``TODO_SESSION_ID`` in the environment. Git
ignores the file, because how a session launches is a property of the machine
and not of the repository. :doc:`../contributing/driving-a-session` is where
what the launch has to get right stands. Each session reports into
``.worktrees/.sessions/<name>.log``.

That is the fourth step beside the other three, and it is here for the same
reason they are. A step left over for somebody to carry out from a read is the
one that breaks. The run of 2026-08-02 started every session in the directory
that was already open. Three worktrees stood untouched while the sessions read a
queue that belonged to somebody else.

Where the file is absent nothing starts and the handover prints instead. One
absolute ``cd`` per worktree and the message under a line that says the rest of
the output is not part of it. Which directory a session starts in was a sentence
once, *with that worktree as its working directory*. A property somebody
satisfies is a blank in prose.

**Nothing in that message needs a fill**, and that is the whole of what it took
to fix. It was a template with the worktree path and the branch left as blanks,
and the run that broke sent it as it stood. The session read
``<absolute path to the worktree>`` as a line meant for it rather than one
somebody had forgotten. No check it could make would have told it otherwise. A
message with a blank in it is a message somebody fills in, so there is none.
Every session starts with the same characters, and there is nothing to get
wrong.

What the message therefore cannot name is which todo is whose, and that half was
never the prompt's to answer. A path a session gets is one it has no reason to
doubt. A worktree on a branch the claim never named passes every check the
session can make. So does one cut from a ``main`` that did not carry the claim
yet. It then reads the queue, where it finds real work that belongs to somebody
else. So it comes out of the checkout instead:

.. code-block:: bash

    bin/cli todo:next --worktree

The same command every session in this repository starts with. The flag is the
sentence the prompt used to carry: *this session is one of several*. On a claim,
it hands over that claim and names the branch the claim sits on. On none, it
says which case it is and stops. The wrong branch, a claim that was not on
``main`` yet, or a session started in the main checkout at all. Where it
refuses, that is the end of the session and not a cue to find something else to
do.

What the session does
---------------------

Nothing about it is special. It reads what the todo serves, settles what the
step turns on, and leaves the file true. That is all of :doc:`working-a-todo`,
which the command names as usual.

Five things are different, and all of them follow from ``main`` elsewhere. The
claim comes with them attached. This is why they are there:

* **Commit on the branch, never on ``main``.** That includes the todo file
  itself. A finished claim is a deletion in the branch, and the merge is what
  carries it.
* **Leave the group listings alone** — the block at the foot of a
  ``requirements/<group>/readme.md`` or ``decisions/<group>/readme.md``. It
  comes from every file in the group, and a worktree can only see its own new
  entry. That means the command, ``bin/cli requirements:index`` or
  ``bin/cli decisions:index``, and it means the line. Two sessions that edit one
  listing by hand conflict where two sessions that leave it alone do not.
  ``todo:home`` runs both commands in the worktree once the rebase has made them
  right, and amends what they wrote onto the branch's own commit. Nothing in
  ``composer ci`` says it, deliberately. A suite that held the listing would
  fail every branch that adds an entry, on the one line the branch may not touch
  (``D-FBK-011``).
* **Rewrap what this branch changed, and leave the corpus to the checkout.** A
  card another claim deletes is a conflict whichever side rewrapped it. Twice on
  2026-08-27 that stopped ``todo:home`` with the rebase aborted behind it.
  ``bin/cli prose:format`` named no path narrows itself here rather than asks
  anybody to remember a path. So the line is the same one every session runs
  (``D-DOC-063``).
* **Say nothing about what another branch has done.** A sibling's state is the
  one fact a worktree cannot check. A written claim about it is how a run of ten
  leaves two feedback that answer for nothing. Twice now both halves of a pair
  wrote the same sentence, *the feedback stays open behind the sibling todo,
  which is another session's claim*. Both times the sibling had finished. Each
  session was right about its own half and wrong about the half it could not
  see. What is true instead is shorter. This half ends here, and whether the
  entry closes depends on the other, which nobody read here.
  ``TodoTest::everyOpenFeedbackIsOnTheBoard`` is what catches the leftover, on
  the rebase, once both halves are on ``main``.
* **Point at entries, not at positions.** In a file two sessions both add to,
  *above*, *below*, *once*, *the first* and *the other* are all claims about a
  layout. That layout has one more section in it by the time anybody reads them.
  The run of 2026-08-02 wrote *this entry has been cited once* into an entry
  that ended the day with three citations. It wrote *the paragraph above* into
  one that gained three sections in between. It wrote *the **Wrong if** got its
  other answer* twice, in two accounts that could not both be the other. Name
  the feedback, the requirement or the decision instead: those survive whatever
  lands beside them.

A question mid-work
-------------------

A todo that turns out to need an answer nobody here can give is the normal case,
not the exception. A session that works alone asks and waits. One of several
cannot: a wait blocks a worktree on a person who answers three others.

So it does not ask, it records. The question goes into the ``waitingOn:`` line
on the todo itself, in the words of the ask, together with what the read already
established. Then the session commits what it has and ends. The branch keeps the
finished half.

What happens next depends on whether the work behind the question stands on its
own. Most of the time it does, since the session settled one half of its todo
and the question is about the other. Then the branch merges like any other and
the todo with the question comes with it. Where it does not, only the todo comes
back:

.. code-block:: bash

    git checkout <branch> -- todo/open/<id>-<name>.md

That keeps ``main`` free of a half-finished change while it still says, in one
place, what is open and where the work behind it is.

**A todo that carries a question does not stay in the queue.** The next session
would get it as ordinary work, and what it needs is a person.
``bin/cli todo:park`` moves every queued todo that names one into ``waiting/``.
``todo:home`` runs it in the worktree so the move reaches ``main`` in the commit
the work is in.

A worktree that stands on a todo nobody works is the other thing to look for.
Nothing notices on its own. ``bin/cli todo:home`` with no argument prints what
stands and whether anything on it waits for a commit, which is that read.

Bringing the branches home
--------------------------

**A finished run comes home now.** Checked, merged, worktree gone — and none of
it waits for the sessions still at work. Nine branches held back until the tenth
reports are nine that each need a bigger rebase when they finally move. ``main``
went on without them. The only thing the wait bought was a tidier moment. There
is no batch here: there are ten sequences of the same steps, started whenever
their session ends.

**One at a time, rebased onto ``main`` and fast-forwarded — no merge commits.**

.. code-block:: bash

    bin/cli todo:home <id>

**That command is the whole of it**, the way ``todo:claim`` is the whole of the
setup. It is here for the reason that one is. The steps have to happen in one
order, and an order is the thing prose cannot hold anybody to. Asked with no
name it reports what stands and which of those has a tree nobody committed. It
refuses in a worktree and it refuses off ``main``, because a fast-forward onto
somebody's branch is the failure that looks like success.

Nothing here decides that a session has ended, and nothing can: the todos are
the caller's to name. What the command carries out for each of them is this, in
the worktree that todo sits in.

.. code-block:: bash

    git -C .worktrees/<name> rebase main
    (cd .worktrees/<name> && bin/cli requirements:index && bin/cli decisions:index)
    (cd .worktrees/<name> && bin/cli links:repair)
    git -C .worktrees/<name> commit --amend --no-edit -- <what they rewrote>
    (cd .worktrees/<name> && composer ci)
    git merge --ff-only todo/<name>
    git worktree remove .worktrees/<name> && git branch -d todo/<name>

``main`` moves while the sessions run, so a branch cut hours ago is behind it
and cannot be fast-forwarded as it stands. The rebase is what makes the merge a
fast-forward, and ``--ff-only`` is what says so. Where it refuses, something is
not what this procedure assumes, and that is worth a stop. What comes out is one
sequence of commits on ``main`` rather than a merge commit per claim that says
nothing but that a claim existed.

**The sequence belongs to one branch, and the next branch starts it again from
the top.** ``main`` moved when the last merge landed, so a branch rebased before
it is behind again. Two merges from one rebase is the mistake the run of
2026-08-02 made, and ``--ff-only`` caught it, which is what it is for. And the
worktree goes **after** the merge, never before. Removed early it takes the only
checkout the rebase and the suite can run in, and its return costs a fresh
``composer install``.

**``composer ci`` runs in the worktree, after the rebase.** That is the first
moment the session's work stands on what ``main`` has become, and it is the only
run that says anything. One from before the rebase checked a tree that no longer
exists. One at a time for the same reason — a suite that fails after three
branches says nothing about which one broke it. The claims themselves never
conflict, because each session touched one file in ``todo/`` and its own.

Where one branch fixes something the others also fail on, that one goes first.
Otherwise every merge behind it runs against a suite that was already red, which
is the one thing this order exists to avoid.

**The rebase is also what makes the group listings writable, and they go onto
the branch.** The listing at the foot of a group readme comes from every file in
that group. So a session at work on its todo sees only its own new entry. That
is why it has to leave the block alone. Once the branch stands on ``main``,
nothing rebases onto it again and the two index commands produce exactly what
the fast-forward should carry. What they wrote gets an amend onto the branch's
own commit, because a listing line and the entry it lists are one change.
Written onto ``main`` afterwards instead, it was 37 of the 200 commits before
2026-08-18. Each had nothing to say but that the one under it had merged
(``D-FBK-011``).

**A link to a feedback this branch archived gets its repair on the same tree.**
``feedback:archive`` runs before the link that breaks exists. A decision written
on another branch in the same window names the report where it stood. Whichever
of the two merges second carries a path to a file that moved. Three branches
stopped there on 2026-08-27. The repair is the same every time, the path gains
``archive/``, so ``bin/cli links:repair`` makes it. ``links:check`` names it for
a checkout that met the same thing some other way (``D-DOC-064``).

Then, on ``main``, which ``todo:home`` also carries out once the branch is in:

.. code-block:: bash

    bin/cli repository:check

What used to stand here as well was the queue. Two sessions that each queued new
work both read the same last number and both took it. There is no number now, so
there is nothing to collide. Two todos are both ``normal`` and the older one is
older, whichever branch each arrived on.

**An id still collides, and that is the one to expect.** A requirement and a
decision carry a number, and every session reads the same last number. Ten of
them that read it at once produce duplicates. The run of 2026-08-02 wrote
``D-ANS-009`` twice and ``D-FBK-018`` twice. Nothing can prevent it and nothing
needs to. ``composer ci`` in the second branch fails on *two decision files
claim the same id* once the first is on ``main``. That is the rebase at its job.
That failure names both files and the renumber command, so read it where it
lands rather than here. Renumber the later one, fix what names it, amend, and
the check goes quiet. Whichever branch merged first keeps the number, so the
order the work came home decides it rather than any arbiter.

**What is dangerous is the renumbering, not the collision.** Four runs of ten
produced seven of them, and twice the files that named the old number did not
all mean the same entry. ``R-PRJ-008`` rested on the ``D-ANS-013`` that kept it
while five other files meant the one that became ``D-ANS-015``. ``ans-006``
named the ``D-ANS-016`` that stayed while a requirement and a todo named the one
that became ``D-ANS-019``. A search and replace over the id is wrong in exactly
those cases, it is silent, and no check fails afterwards. The entry it now
points at is real.

.. code-block:: bash

    bin/cli decisions:renumber <the branch's own file>

Name the file rather than the id. Both files carry the id, so the id says which
number the caller means and not which entry moves. The command refuses one two
files claim, and names both. Two runs of 2026-08-18 moved the entry already on
``main`` back when it picked between them itself.

That moves the entry and every reference whose own line names its file, and
prints the rest. Those are the bare ones, which is what both wrong pointers
were. Read those one at a time, and ``git diff main -- <file>`` is what settles
an ambiguous one. A line this branch added means this branch's entry. The list
is the whole of them, so a reference nobody read is one somebody skipped rather
than one nothing mentioned —
`D-DOC-015 <../../decisions/documentation/doc-015-a-renumber-moves-what-a-link-path-settles-and-names-the-rest.md>`_.

**A marker that survives the resolution is what nothing used to catch.** A file
with a ``>>>>>>>`` left in it parses, lints, and passes every test that does not
happen to read it. The run of 2026-08-02 put one into a decision and
``composer ci`` went green over it, in a commit whose diff looked deliberate.
``StructureTest::noFileCarriesAConflictMarker`` reads every file this repository
keeps, so the suite the branch already runs after its rebase is where that now
surfaces.

**Two sessions can also land on one entry** where nothing declared it. The
overlap ``todo:claim`` reports comes off what the todos say, and this one comes
from the judgement rather than from a file before it. Two of the ten judged
different feedback into the same ``D-SKL-001``, which the rebase surfaced as a
conflict in the file. Usually both paragraphs belong. Each is an account of one
read, which is what a **Since then** carries. So the resolution is a heading
each rather than a choice between them.

**A todo with a question parks here**, and it is the one thing the session could
not do itself. It ended on the question and left the todo in the queue, where
the next session would get it as ordinary work. ``todo:home`` therefore runs
``bin/cli todo:park`` in the worktree and beside the listings, so the move
reaches ``main`` in the commit the work is in. Giving the todo back needs
nothing at all: the worktree comes down two steps later, and that is what
offered it to nobody.
