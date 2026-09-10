:navigation-title: Todos in parallel

Working several todos at once
=============================

One session works one todo, and everything about that is :doc:`working-a-todo` —
unchanged here. This page is the part around it: how several sessions get
different work, where each of them writes, and how what they wrote comes back.
Nothing on it replaces the reading, the research or the question a todo is owed.

It is worth doing where the queue holds work that does not overlap, which is
most of what accumulates here: entries that name a decision each and are read
against different parts of the checkout. It is not worth doing for two todos
about one file, and it is not worth doing for one.

.. image:: ../images/parallel-todos.svg
    :zoomable:
    :alt: A worktree per todo says which session has it, and one branch carries
          each session's unfinished work; completed branches return one at a
          time through rebase, checks and a fast-forward merge.

The worktree and the branch
---------------------------

**The worktree says who has what, the branch carries the work.**

That is the whole arrangement, and every rule below follows from it. A todo one
worktree stands on is one somebody has in hand, and every command that hands out
work passes over it. The half-finished diff is on that worktree's branch, where
nobody else has to look at it.

Nothing is written down to say so — ``D-DOC-060``. The branch is the todo's id
and the worktree is named after the branch, so ``bin/cli todo:list`` answers the
question from what is standing and cannot disagree with itself.

**A todo is named by its id**, ``T-<yymmdd>-<hash>``, which every listing prints
and every command takes. That the work happens in a worktree is how this
repository holds a todo in hand, not something a caller has to know — so
``todo:home`` and ``todo:drop`` take the id, and accept the worktree's directory
name because it is there.

What that buys is that taking a todo on and giving it back are not moves. A todo
whose worktree came down is workable again with nothing to put back, and a todo
that was finished is a deletion the branch already carries.

Taking them on
--------------

.. code-block:: bash

    bin/cli todo:claim 3

**That command is the whole setup.** Three worktrees, one per todo, each on the
branch that todo's name derives and each with its own ``composer install``; from
then on ``bin/cli todo:next`` offers those three to nobody, and the fourth
session is handed the item behind them. Then the message the three sessions are
started with. What is left to do is start them.

Nothing is moved and nothing is committed. The queue is the same file it was —
what changed is that three of its todos now have a worktree standing on them.

Where a todo has been worked before, the branch it derives to may still be
there. It is passed over and named rather than reused or deleted, because that
branch still holds the half that is done — a worktree that quietly attaches to
an old branch is the one failure here that looks like success.

What it prints besides the branches is an overlap, in the three ways two claims
can have one. Nothing here knows which lines a step will touch, so all three are
a warning to read before the worktrees exist rather than a refusal.

Two claims **answering for** one entry are two sessions editing one file, and
taking one of them is cheaper than merging both. Two **naming** one class are
the same a step less certainly — a todo says where it is about to work, as a
path, as ``Class::method()``, or as the bare name, and the claim resolves all
three to the file. Two **standing on** one requirement or decision without
serving it are working from a single judgement, which is where a pair of steps
that have to agree comes from.

The last two are here because ``serves:`` alone missed the collision that cost
the most. On 2026-08-02 two todos with different ``serves:`` keys each added a
handler for one token to one function; the rebase put them in sequence, each
ending in ``continue``, and the second was never reached. Both had named
``R-ANS-012`` and both had named the class — one as ``Extension::describe()``,
the other as ``src/Installation/Extension.php``. Neither is a declaration and
neither had to be: it is a session saying where it is going, in the file the
claim reads anyway.

``bin/cli todo:drop <id>`` is the way back out, for a todo nobody is working — a
session that never started, one that was abandoned, a claim taken by mistake.
There is nothing to put back: the todo is where it always was, and the worktree
coming down is what offers it again.

What it decides is the branch. One carrying commits is left alone, because it is
the only place that work exists; one carrying none is deleted, because a branch
nobody takes down is a todo ``todo:claim`` passes over for good.
``bin/cli todo:home`` is the other end, for work that is finished — it rebases,
checks and merges, none of which means anything for a branch carrying nothing.

The worktree
------------

One per claim, made by ``todo:claim``: a worktree on the claim's branch, its own
``composer install``, and ``.checkouts/`` symlinked in. What it does by hand is
here because it is what the command is doing, and because a worktree made some
other way has to do the same.

**Run ``composer install`` in the worktree. Never symlink ``vendor/``.**

That one costs an afternoon to find. ``Paths::root()`` is the directory above
``src/``, and Composer's autoload map resolves ``src/`` from wherever the
autoloader physically sits — so a symlinked ``vendor/`` points every path in
this repository back at the main checkout. ``bin/cli`` then reads and writes the
todos, requirements and decisions of the checkout the session is not in, and
nothing about the output looks wrong.

``.checkouts/`` is the opposite case and is symlinked on purpose. It is 861 MB,
it is gitignored, and a session working a todo only ever reads it. Only
``bin/cli checkouts:update`` writes there, and that is not a claim's work.

Starting the sessions
---------------------

One session per worktree, started in that worktree, and all three get the same
message — the one ``todo:claim`` printed. It is not on this page, because a copy
here and a copy in the command are two things to keep in step and one of them
would be sent. How the session is launched at all — which build, from where,
with what switched on — is :doc:`../contributing/driving-a-session`, and it is
the same launch a forward run uses.

**Better: let the claim start them.** Put the command line that starts a session
on this machine into ``.session-command`` at the root of the checkout, and
``todo:claim`` runs it once per worktree — that worktree as the working
directory, the message on standard input, ``TODO_SESSION_ID`` in the
environment. The file is gitignored, because how a session is launched is a
property of the machine and not of the repository, and
:doc:`../contributing/driving-a-session` is where what the launch has to get
right is written down. Each session reports into
``.worktrees/.sessions/<name>.log``.

That is the fourth step joining the other three, and it is here for the same
reason they are. A step left over for somebody to carry out by reading is the
one that breaks: the run of 2026-08-02 started every session in the directory
that was already open, and three worktrees stood untouched while the sessions
read a queue belonging to somebody else.

Where the file is absent nothing is started and the handover prints instead —
one absolute ``cd`` per worktree and the message under a line saying the rest of
the output is not part of it. Which directory a session is started in was a
sentence once, *with that worktree as its working directory*, and a property
somebody satisfies is a blank wearing prose.

**Nothing in that message is filled in**, and that is the whole of what it took
to fix. It was a template with the worktree path and the branch left as blanks,
and the run that broke sent it as it stood: the session read
``<absolute path to the worktree>`` as a line it was being given rather than one
somebody had forgotten, and no check it could make would have told it otherwise.
A message with a blank in it is a message somebody fills in, so there is none —
every session is started with the same characters, and there is nothing to get
wrong.

What the message therefore cannot name is which todo is whose, and that half was
never the prompt's to answer. A path a session is handed is one it has no reason
to doubt: a worktree on a branch the claim never named — or one cut from a
``main`` that did not carry the claim yet — passes every check the session can
make and then reads the queue, where it finds real work belonging to somebody
else. So it is read out of the checkout instead:

.. code-block:: bash

    bin/cli todo:next --worktree

The same command every session in this repository starts with, and the flag is
the sentence the prompt used to carry: *this session is one of several*.
Standing on a claim, it hands over that claim and names the branch it is
committed on. Standing on none — the wrong branch, a claim that was not on
``main`` yet, or a session started in the main checkout at all — it says which
of those it is and stops. Where it refuses, that is the end of the session and
not a cue to find something else to do.

What the session does
---------------------

Nothing about it is special. It reads what the todo serves, settles what the
step turns on, and leaves the file true — all of :doc:`working-a-todo`, which
the command names as usual.

Five things are different, and all of them are consequences of ``main`` being
elsewhere. The claim is handed over with them attached; this is why they are
there:

* **Commit on the branch, never on ``main``.** That includes the todo file
  itself. A finished claim is a deletion in the branch, and the merge is what
  carries it.
* **Leave the group listings alone** — the block at the foot of a
  ``requirements/<group>/readme.md`` or ``decisions/<group>/readme.md``. It is
  generated from every file in the group, and a worktree can only see its own
  new entry. That means the command, ``bin/cli requirements:index`` or
  ``bin/cli decisions:index``, and it means the line: two sessions editing one
  listing by hand conflict where two sessions leaving it alone do not.
  ``todo:home`` runs both commands in the worktree once the rebase has made them
  right, and amends what they wrote onto the branch's own commit. Nothing in
  ``composer ci`` says it, deliberately: a suite that held the listing would
  fail every branch that adds an entry, on the one line the branch is told not
  to touch — ``D-FBK-011``.
* **Rewrap what this branch changed, and leave the corpus to the checkout.** A
  card another claim is deleting is a conflict whichever side rewrapped it, and
  twice on 2026-08-27 that stopped ``todo:home`` with the rebase aborted behind
  it. ``bin/cli prose:format`` named no path narrows itself here rather than
  asking anybody to remember a path, so the line is the same one every session
  runs — ``D-DOC-063``.
* **Say nothing about what another branch has done.** A sibling's state is the
  one fact a worktree cannot check, and writing it down is how a run of ten
  leaves two feedback answering for nothing. Twice now the same sentence has
  been written by both halves of a pair — *the feedback stays open behind the
  sibling todo, which is another session's claim* — and both times the sibling
  was finished. Each session was right about its own half and wrong about the
  half it could not see. What is true instead is shorter: this half is done, and
  whether the entry closes depends on the other, which was not read here.
  ``TodoTest::everyOpenFeedbackIsOnTheBoard`` is what catches the leftover, on
  the rebase, once both halves are on ``main``.
* **Point at entries, not at positions.** In a file two sessions are both adding
  to, *above*, *below*, *once*, *the first* and *the other* are all claims about
  a layout that has one more section in it by the time anybody reads them. The
  run of 2026-08-02 wrote *this entry has been cited once* into an entry that
  ended the day with three citations, *the paragraph above* into one that gained
  three sections in between, and *the **Wrong if** got its other answer* twice,
  in two accounts that could not both be the other. Name the feedback, the
  requirement or the decision instead: those survive whatever lands beside them.

A question mid-work
-------------------

A todo that turns out to need an answer nobody here can give is the normal case,
not the exception, and a session working alone asks and waits. One of several
cannot: waiting blocks a worktree on a person who is answering three others.

So it does not ask, it records. The question goes into the ``waitingOn:`` line
on the todo itself, in the words it would have been asked in, together with what
the reading already established. Then the session commits what it has and ends.
The branch keeps the half that is done.

What happens next depends on whether the work behind the question stands on its
own. Most of the time it does — the session settled one half of its todo and the
question is about the other — and then the branch merges like any other and the
todo carrying the question comes with it. Where it does not, only the todo comes
back:

.. code-block:: bash

    git checkout <branch> -- todo/open/<id>-<name>.md

That keeps ``main`` free of a half-finished change while still saying, in one
place, what is open and where the work behind it is.

**A todo carrying a question does not stay in the queue.** It would be offered
to the next session as ordinary work, and what it needs is a person.
``bin/cli todo:park`` moves every queued todo that names one into ``waiting/``,
and ``todo:home`` runs it in the worktree so the move reaches ``main`` in the
commit the work is in.

A worktree standing on a todo nobody is working is the other thing to look for.
Nothing notices on its own — ``bin/cli todo:home`` with no argument prints what
is standing and whether anything on it is uncommitted, which is that reading.

Bringing the branches home
--------------------------

**A run that is finished comes home now.** Checked, merged, worktree gone — and
none of it waits for the sessions still going. Nine branches held back until the
tenth reports are nine that each need a bigger rebase when they finally move,
against a ``main`` that kept going without them, and the only thing the waiting
bought was a tidier-looking moment. There is no batch here: there are ten
sequences of the same steps, started whenever their session ends.

**One at a time, rebased onto ``main`` and fast-forwarded — no merge commits.**

.. code-block:: bash

    bin/cli todo:home <id>

**That command is the whole of it**, the way ``todo:claim`` is the whole of the
setup, and it is here for the reason that one is: the steps have to happen in
one order, and an order is the thing prose cannot hold anybody to. Asked with no
name it reports what is standing and which of those has a tree nobody committed.
It refuses in a worktree and it refuses off ``main``, because a fast-forward
onto somebody's branch is the failure that looks like success.

Nothing here decides that a session has ended, and nothing can: the todos are
the caller's to name. What the command carries out for each of them is this, in
the worktree that todo is being worked in.

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
fast-forward, and ``--ff-only`` is what says so: where it refuses, something is
not what this procedure assumes, and that is worth stopping for. What comes out
is one sequence of commits on ``main`` rather than a merge commit per claim
saying nothing but that a claim existed.

**The sequence belongs to one branch, and the next branch starts it again from
the top.** ``main`` moved when the last merge landed, so a branch rebased before
it is behind again — running two merges from one rebase is the mistake the run
of 2026-08-02 made, and ``--ff-only`` caught it, which is what it is for. And
the worktree goes **after** the merge, never before: removed early it takes the
only checkout the rebase and the suite can run in, and getting it back costs a
fresh ``composer install``.

**``composer ci`` runs in the worktree, after the rebase.** That is the first
moment the session's work stands on what ``main`` has become, and it is the only
run that says anything: one from before the rebase checked a tree that no longer
exists. One at a time for the same reason — a suite that fails after three
branches says nothing about which one broke it. The claims themselves never
conflict, each session having touched one file in ``todo/`` and its own.

Where one branch fixes something the others are also failing on, that one goes
first. Otherwise every merge behind it is checked against a suite that was
already red, which is the one thing this order exists to avoid.

**The rebase is also what makes the group listings writable, and they are
written on the branch.** The listing at the foot of a group readme is generated
from every file in that group, so a session working its todo sees only its own
new entry — which is why it is told to leave the block alone. Once the branch
stands on ``main``, nothing rebases onto it again and the two index commands
produce exactly what the fast-forward should carry. What they wrote is amended
onto the branch's own commit, because a listing line and the entry it lists are
one change: written onto ``main`` afterwards instead, it was 37 of the 200
commits before 2026-08-18, each saying nothing but that the one under it had
been merged — ``D-FBK-011``.

**A link to a feedback this branch archived is repointed on the same tree.**
``feedback:archive`` runs before the link that breaks exists: a decision written
on another branch in the same window names the report where it stood, and
whichever of the two merges second carries a path to a file that moved. Three
branches stopped there on 2026-08-27. The repair is the same every time — the
path gains ``archive/`` — so ``bin/cli links:repair`` makes it, and
``links:check`` names it for a checkout that met the same thing some other way —
``D-DOC-064``.

Then, on ``main``, which ``todo:home`` also carries out once the branch is in:

.. code-block:: bash

    bin/cli repository:check

What used to stand here as well was the queue: two sessions that each queued new
work both read the same last number and both took it. There is no number now, so
there is nothing to collide — two todos are both ``normal`` and the older one is
older, whichever branch each arrived on.

**An id still collides, and that is the one to expect.** A requirement and a
decision are numbered, every session reads the same last number, and ten of them
reading it at once produce duplicates: the run of 2026-08-02 wrote ``D-ANS-009``
twice and ``D-FBK-018`` twice. Nothing can prevent it and nothing needs to —
``composer ci`` in the second branch fails on *two decision files claim the same
id* once the first is on ``main``, which is the rebase doing its job. That
failure names both files and the renumber command, so it is read where it lands
rather than here. Renumber the later one, fix what names it, amend, and the
check goes quiet. Whichever branch merged first keeps the number, so the order
is decided by the order the work came home rather than by anybody arbitrating
it.

**What is dangerous is the renumbering, not the collision.** Four runs of ten
produced seven of them, and twice the files naming the old number did not all
mean the same entry: ``R-PRJ-008`` rested on the ``D-ANS-013`` that kept it
while five other files meant the one that became ``D-ANS-015``, and ``ans-006``
named the ``D-ANS-016`` that stayed while a requirement and a todo named the one
that became ``D-ANS-019``. A search and replace over the id is wrong in exactly
those cases, it is silent, and no check fails afterwards — the entry it now
points at is real.

.. code-block:: bash

    bin/cli decisions:renumber <the branch's own file>

Name the file rather than the id. Both files carry the id, so the id says which
number is meant and not which entry moves — the command refuses one two files
claim, and names both. Two runs of 2026-08-18 moved the entry already on
``main`` back when it picked between them itself.

That moves the entry and every reference whose own line names its file, and
prints the rest — the bare ones, which is what both mis-pointings were. Those
are read one at a time, and ``git diff main -- <file>`` is what settles an
ambiguous one: a line this branch added means this branch's entry. The list is
the whole of them, so a reference nobody read is one somebody skipped rather
than one nothing mentioned —
`D-DOC-015 <../../decisions/documentation/doc-015-a-renumber-moves-what-a-link-path-settles-and-names-the-rest.md>`_.

**A marker that survives the resolution is what nothing used to catch.** A file
with a ``>>>>>>>`` left in it parses, lints, and passes every test that does not
happen to read it; the run of 2026-08-02 put one into a decision and
``composer ci`` went green over it, in a commit whose diff looked deliberate.
``StructureTest::noFileCarriesAConflictMarker`` reads every file this repository
keeps, so the suite the branch already runs after its rebase is where that now
surfaces.

**Two sessions can also land on one entry** where nothing declared it: the
overlap ``todo:claim`` reports is read off what the todos say, and this one is
created by the judging rather than written down before it. Two of the ten judged
different feedback into the same ``D-SKL-001``, which the rebase surfaced as a
conflict in the file. Usually both paragraphs belong — each is an account of one
reading, which is what a **Since then** carries, so the resolution is a heading
each rather than a choice between them.

**A todo carrying a question is parked here**, and it is the one thing the
session could not do itself. It ended on the question and left the todo in the
queue, where the next session would be offered it as ordinary work.
``todo:home`` therefore runs ``bin/cli todo:park`` in the worktree and beside
the listings, so the move reaches ``main`` in the commit the work is in. Giving
the todo back needs nothing at all: the worktree comes down two steps later, and
that is what offered it to nobody.
