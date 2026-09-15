Writing a requirement
=====================

What a requirement is, what it stands on and what its states mean is
:doc:`requirements`. This page is where an entry goes and how to write it.

Where an entry lives
--------------------

One requirement is one file, named after its id, in the group its id names. The
group is not a file preference. It is what the requirement is about, and the
prefix carries it. So a file's id decides its path and two entries cannot
quietly share a number.

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

The number is three digits wide, in the file name and in the id alike. That is
what lists a group in the order of its writers. Without the pad, ``dis-10``
sorts between ``dis-1`` and ``dis-2`` in every directory listing and in anything
that compares the ids as text. A decision takes its number the same way, so one
habit covers both. ``bin/cli requirements:check`` fails on any other width.

Each group's ``readme.md`` says what that group is about, and
``bin/cli requirements:index`` generates the listing at the foot of it. The same
command generates the listing at the foot of ``requirements/readme.md``, which
carries the whole of it under a heading per group. A listing kept by hand is a
second copy of the directory that only says what was true once.

Prose above either listing may not use a third-level heading. That is what the
command reads as the start of the generated half, and it would go with the next
regeneration.

An id never sees a second use. A withdrawn requirement takes its number with it.
So a number that appears in an old commit, feedback or scenario still means the
one thing it always meant. An implemented entry never goes. An entry goes only
when somebody withdraws the requirement itself, and then the reason goes in
``decisions/``.

Where a feedback is the route, the entry comes when the session works the
feedback off rather than when it arrives. A feedback nobody has judged yet is a
feedback, not a requirement.

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

    **Nothing that says "there is no installation" is remembered.**

    A successful resolution is memoized for the process; a failure is retried on
    every call, because the caller who reads that answer is the one likely to
    install, migrate or start something and ask again in the same session.

    ## From

    A session lost to a cached negative — the agent ran `composer install`, started
    DDEV, verified `bin/typo3` answered, and every tool kept reporting no
    installation until the client was restarted (2026-07-29).


* The **title** is the front matter's, and the heading repeats it so a reader of
  the file sees it where a heading belongs. ``bin/cli requirements:check`` holds
  the two to each other, so a rewrite of one is a rewrite of both. It names the
  demand in twelve words or so and leaves the demand itself to the bold
  sentence. :doc:`writing-a-decision` has the measure, and this corpus is what
  showed it is writable.
* The **bold first sentence** is the requirement. Everything under it is why it
  is one, and a reader who stops after the bold line has read the whole demand.
* **From** is the session, review or feedback the demand came out of, with its
  date. It is evidence, not decoration: it is what tells the next person whether
  the requirement still describes a real failure.
* ``heldBy`` lists the tests that hold it, and it is **generated**. The test
  declares ``#[Requirement('R-DIS-002')]`` and ``bin/cli requirements:cover``
  writes the front matter from every such attribute. A whole test class is a
  legitimate item. The attribute stands over the class, and ``VersionsTest`` in
  full is a claim about every method in it.
  `D-DOC-049 <../../decisions/documentation/doc-049-a-requirements-tests-are-declared-where-the-test-is.md>`_
  is why the attribute is the source and the entry the copy.
* **Held by** is the section for what is not a test. A ``bin/cli`` command that
  re-derives the claim, a clause that says what one of the tests holds, a half
  nothing guards. A bullet that is only a test name fails
  ``bin/cli requirements:check`` — that name is the front matter's. An entry
  nothing holds keeps the section and says ``not guarded`` in as many words.

What it rests on
----------------

A requirement may name the decisions it stands on in its front matter:

.. code-block:: markdown

    ---
    id: R-FBK-007
    status: held
    restsOn: [D-FBK-005]
    heldBy:
      - SomeTest::theTestThatHoldsIt      # written by requirements:cover
    ---


``bin/cli requirements:check`` fails on an id no decision has. Whether somebody
later revoked a decision it rests on is a read rather than a failure, and
``bin/cli unresolved:list`` is where it comes out. A revoked decision leaves the
requirement's test green and the reason under it gone, which nothing else would
say.

.. _judging-a-requirement:

Judging one that stays
----------------------

**open** and **not guarded** are legitimate states, which is why no check may
fail on them and why ``bin/cli unresolved:list`` reads them out instead. What
that read could not see is the difference between an entry nobody has decided
about and one a session read and deliberately left alone. It named every
unguarded entry every time, so every session that ran it derived the same
judgement again.

``judged`` is that difference, and it is a date:

.. code-block:: markdown

    ---
    id: R-COD-003
    status: held
    judged: 2026-08-22
    restsOn: [D-COD-004]
    ---

**Why** it stays is not in the front matter. That is the sentence **Held by**
owes, ``not guarded`` in as many words. Or, where the reason is a decision, the
entry the requirement rests on. The date says only that somebody went and read
it.

A date rather than a word, because the judgement is about the entry as it read
that day. Somebody can rewrite the entry under it. Nothing catches that.
``bin/cli requirements:check`` holds the shape, and the date is what lets a
reader see that the judgement is older than what it judged. Re-judging one is
overwriting the date.

``bin/cli requirements:check`` holds every file to the shape this page
describes, and ``composer test`` runs the same check through
``RequirementsTest``, except the listing. That one comes from every file in a
group, so the command alone holds it, see
`D-FBK-011 <../../decisions/feedback/fbk-011-the-suite-holds-what-one-branch-can-be-right-about.md>`_.
