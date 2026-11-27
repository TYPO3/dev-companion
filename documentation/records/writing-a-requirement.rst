Writing a requirement
=====================

This page covers where a requirement goes, what an entry holds and what it rests
on. It also covers how a session judges an entry that stays open or unguarded.
:doc:`requirements` says what a requirement is, what it rests on and what its
states mean.

Where an entry lives
--------------------

One requirement is one file, named after its id, in the group its id names. The
group is what the requirement is about, and the prefix of the id names it. So
the id of a file decides its path, and two entries cannot quietly share a
number. The groups:

* `audience/ <../../requirements/audience/readme.md>`__ — Who the answer has to
  be right for
* `discovery/ <../../requirements/discovery/readme.md>`__ — Which installation
  the server reads, and how
* `answers/ <../../requirements/answers/readme.md>`__ — What a caller may
  conclude from one
* `documentation/ <../../requirements/documentation/readme.md>`__ — What the
  live manuals answer
* `task-skills/ <../../requirements/task-skills/readme.md>`__ — What an
  installed workflow owes the task
* `project/ <../../requirements/project/readme.md>`__ — The repository the
  caller stands in
* `scope/ <../../requirements/scope/readme.md>`__ — Core conventions where they
  apply, and nowhere else
* `guides/ <../../requirements/guides/readme.md>`__ — What a returned draft is
  worth
* `feedback/ <../../requirements/feedback/readme.md>`__ — What the feedback
  channel has to stay usable for
* `knowledge/ <../../requirements/knowledge/readme.md>`__ — What the knowledge
  base has to cover
* `code/ <../../requirements/code/readme.md>`__ — What must hold of the source
  itself

Write the number three digits wide, in the file name and in the id alike. The
pad lists a group in the order of its writers. Without it, ``dis-10`` sorts
between ``dis-1`` and ``dis-2`` in every directory listing and in anything that
compares the ids as text. A decision takes its number the same way, so one habit
covers both. ``bin/cli requirements:check`` fails on any other width.

Each group's ``readme.md`` says what that group is about, and
``bin/cli requirements:index`` generates the listing at the foot of it. The same
command generates the listing at the foot of ``requirements/readme.md``. That
listing holds the whole directory, under a heading per group. A listing kept by
hand is a second copy of the directory that only says what was true once.

Use no third-level heading in the prose above either listing. The command reads
such a heading as the start of the generated half, and the next regeneration
would remove it.

Nobody uses an id twice. A withdrawn requirement takes its number with it. So a
number in an old commit, feedback or scenario still means the one thing it
always meant. An implemented entry stays. An entry goes only when somebody
withdraws the requirement itself, and then the reason goes into ``decisions/``.

Where a feedback is the route, the entry comes when the session works the
feedback off, not when the feedback arrives. A feedback nobody has judged yet is
a feedback, not a requirement.

What an entry holds
-------------------

.. code-block:: markdown

    ---
    id: R-DIS-009
    title: A negative is never remembered
    status: held
    heldBy:
      - InstanceTest::anInstallationThatAppearsDuringTheSessionIsFound
    ---

    # R-DIS-009 — A negative is never remembered

    **The server remembers nothing short of a whole answer: neither "there is no
    installation" nor a console reached outside the runtime the project declares.**

    The process memoizes a successful resolution. It retries a failure on every
    call. The caller who reads that answer is the one likely to install, migrate or
    start something and ask again in the same session.

    ## From

    A session lost to a cached negative. The agent ran `composer install`, started
    DDEV and verified that `bin/typo3` answered. Every tool kept its report of no
    installation until a restart of the client (2026-07-29).


* The front matter has the **title**, and the heading repeats it, so a reader of
  the file sees it where a heading belongs. ``bin/cli requirements:check`` holds
  the two to each other, so a rewrite of one is a rewrite of both.

  The title names the demand in twelve words or so, and leaves the demand itself
  to the bold sentence. :doc:`writing-a-decision` has the measure, and this
  corpus showed that the measure is writable.

* The **bold first sentence** is the requirement. Everything under it says why
  it is one. A reader who stops after the bold line has read the whole demand.

* **From** names the session, review or feedback the demand came out of, with
  its date. It is evidence, not decoration. It tells the next person whether the
  requirement still describes a real failure.

* ``heldBy`` lists the tests that hold the requirement, and it is **generated**.
  The test declares ``#[Requirement('R-DIS-002')]``, and
  ``bin/cli requirements:cover`` writes the front matter from every such
  attribute.

  A whole test class is a legitimate item. The attribute then sits above the
  class, and ``VersionsTest`` in full is a claim about every method in it.
  `D-DOC-049 <../../decisions/documentation/doc-049-a-requirements-tests-are-declared-where-the-test-is.md>`_
  says why the attribute is the source and the entry the copy.

* **Held by** is the section for what is not a test. That is a ``bin/cli``
  command that derives the claim again, a clause that says what one of the tests
  holds, or a half nothing guards. A bullet that is only a test name fails
  ``bin/cli requirements:check``, because that name belongs in the front matter.
  An entry nothing holds keeps the section and says ``not guarded`` in as many
  words.

What it rests on
----------------

A requirement may name the decisions it rests on in its front matter:

.. code-block:: markdown

    ---
    id: R-FBK-007
    status: held
    restsOn: [D-FBK-012, D-FBK-016]
    heldBy:
      - SomeTest::theTestThatHoldsIt      # written by requirements:cover
    ---


``bin/cli requirements:check`` fails on an id no decision has. Whether somebody
later revoked a decision the requirement rests on is a read, not a failure.
``bin/cli unresolved:list`` reports it. A revoked decision leaves the test of
the requirement green and removes the reason under it. Nothing else would say
so.

.. _judging-a-requirement:

Judging one that stays
----------------------

**open** and **not guarded** are legitimate states. So no check may fail on
them, and ``bin/cli unresolved:list`` reads them out instead. That read could
not tell an entry nobody has decided about from one a session read and left
alone on purpose. It named every unguarded entry every time. So every session
that ran it derived the same judgement again.

``judged`` records that difference, as a date:

.. code-block:: markdown

    ---
    id: R-COD-003
    status: held
    judged: 2026-08-22
    restsOn: [D-COD-004]
    ---

The reason the entry stays goes into **Held by**: ``not guarded`` in as many
words. Where the reason is a decision, the reason is the entry the requirement
rests on. The date says only that somebody went and read the entry.

The judgement is a date, not a word, because it is about the entry as it read
that day. Somebody can rewrite the entry under it, and nothing catches that.
``bin/cli requirements:check`` holds the shape. The date lets a reader see that
the judgement is older than what it judged. To judge an entry again, overwrite
the date.

What checks an entry
--------------------

``bin/cli requirements:check`` holds every file to the shape this page
describes. ``composer test`` holds the same shape through ``RequirementsTest``,
except for the listing. The listing comes from every file in a group, so the
command alone holds it, see
`D-FBK-011 <../../decisions/feedback/fbk-011-the-suite-holds-what-one-branch-can-be-right-about.md>`_.
