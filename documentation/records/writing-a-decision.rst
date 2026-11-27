:navigation-title: Writing a decision

Writing a decision, and going back to one
=========================================

This page covers where a decision entry goes, how to write it, and what a later
session adds to it. :doc:`decisions` says what a decision is, what rests on it
and what its states mean.

Where an entry lives
--------------------

One decision is one file, named after its id, in the group its id names. The
group is what the decision is about, and the prefix of the id names the group.
So the id of a file decides its path, and two entries cannot quietly share a
number.

* `audience/ <../../decisions/audience/readme.md>`__ — Who the server answers
  for, and how it says so
* `discovery/ <../../decisions/discovery/readme.md>`__ — Which installation the
  server reads, and how
* `answers/ <../../decisions/answers/readme.md>`__ — What a lookup returns, and
  what decides it
* `knowledge/ <../../decisions/knowledge/readme.md>`__ — What the corpus holds
  and how to write it
* `versions/ <../../decisions/versions/readme.md>`__ — What a statement holds on
* `catalog/ <../../decisions/catalog/readme.md>`__ — The curated indexes and
  where their contract comes from
* `scope/ <../../decisions/scope/readme.md>`__ — Core conventions where they
  apply, and nowhere else
* `guides/ <../../decisions/guides/readme.md>`__ — What a returned draft is
  worth
* `evidence/ <../../decisions/evidence/readme.md>`__ — How to measure this
  server
* `task-skills/ <../../decisions/task-skills/readme.md>`__ — What an installed
  workflow owes the task
* `feedback/ <../../decisions/feedback/readme.md>`__ — What the feedback channel
  has to stay usable for
* `documentation/ <../../decisions/documentation/readme.md>`__ — How to write
  what stands here
* `code/ <../../decisions/code/readme.md>`__ — How the source is laid out

The number is three digits wide, in the file name and in the id alike. The pad
lists the entries of a group in the order the writers wrote them. Without the
pad, ``dis-10`` sorts between ``dis-1`` and ``dis-2`` in every directory
listing, and in anything that compares the ids as text. A requirement takes its
number the same way, so one habit covers both. ``bin/cli decisions:check`` fails
on any other width.

The ``readme.md`` of each group says what that group is about.
``bin/cli decisions:index`` generates the listing at its foot. The same command
generates the listing at the foot of ``decisions/readme.md``, which lists every
entry under a heading per group. Inside a group the order is newest first.
``bin/cli decisions:list`` prints the latest decisions across all groups.

The revoked entries have a run of their own at the foot of a listing. Mixed into
the rest, they looked exactly like something to build on.

Prose above either listing may not use a third-level heading. The command reads
such a heading as the start of the generated half, and the next regeneration
would remove it.

Nobody uses an id twice, and nobody deletes an entry. A decision that turned out
wrong is the one most worth a read, and a revocation happens in place.

Renumbering one
---------------

.. code-block:: bash

    bin/cli decisions:renumber <decision> [<number>]

Two sessions that read one ``main`` write one id, and the second one to come
home moves. The move is the dangerous half, not the collision. So the command
splits the references in two:

* It rewrites everything whose own line names the file of the entry. That is the
  entry, its name, every link, and the reference definitions at the end of the
  generated listings.

* It prints everything else, with its file, its line and its text.

**Read what it prints, one at a time.** A bare id says nothing about which entry
it means, and both wrong pointers on record were bare ids. One was in the
``restsOn:`` of a requirement, where a check covers existence and not
correctness. The other was an id in a sentence, where no check covers either.
``git diff main -- <file>`` settles each one: a line this branch added means
this branch's entry.

A search and replace over the whole list is the failure this command makes
visible. The command does not prevent it.
`D-DOC-015 <../../decisions/documentation/doc-015-a-renumber-moves-what-a-link-path-settles-and-names-the-rest.md>`_
is the measure behind the split.

With no number named, the entry takes the next free number in its group. That is
one past the highest, not the first gap. The generated listings go back into
order where they already listed the entry, because a group sorts on the number.

What an entry holds
-------------------

.. code-block:: markdown

    ---
    id: D-DIS-004
    title: The version comes from the core package, not from the console
    date: 2026-07-29
    status: open
    coveredBy:
      - SomeTest::theMethodThatWouldCatchIt      # written by decisions:cover
    ---

    # D-DIS-004 — The version comes from the core package, not from the console

    **The installed version is read from the core package's `Typo3Version` class
    rather than asked of `bin/typo3 --version`.**

    The catalogs are pinned to one revision and every answer was phrased as
    timeless fact, while the server had the other number all along.

    ## Evidence

    - What was measured or read at the time, with its numbers.

    ## Decided

    - What was done, and what was rejected in doing it.

    ## Assumed

    - What the decision rests on that nobody has verified.

    ## Wrong if

    - What would show it to have been wrong, concretely enough that somebody could
      notice it happening.


* The **title** is the one in the front matter. The heading repeats it, so a
  reader of the file sees it where a heading belongs.
  ``bin/cli decisions:check`` holds the two to each other, so a rewrite of one
  is a rewrite of both.

  The title is the name a reader finds the entry by, in a listing of hundreds.
  It is not the statement. The statement is the bold sentence under it, which
  ``bin/cli prose:check`` holds to the STE measure that
  `D-DOC-070 <../../decisions/documentation/doc-070-this-repository-writes-in-asd-ste100.md>`_
  sets. A title says one thing, in twelve words or so. Where an ``and`` joins
  two claims, the second one belongs to the statement, and is usually in it
  already.

  ``bin/cli prose:check`` counts the titles that say more than one thing, worst
  first. It fails on none of them.

  A title says what holds from now on, in the affirmative. It names neither the
  deficiency it corrects nor a negation. Write "The ladder needs an outcome that
  builds something", not "the ladder had no outcome that builds" and not "the
  ladder is not a filter". What was wrong is evidence, and it belongs in the
  paragraph under the bold sentence.

  A reader reads the whole listing for what each entry settled. A title in the
  past tense or in the negative makes the reader reconstruct that. It is
  `AGENTS.md <../../AGENTS.md>`_'s "say what is, not what it is not", applied
  where readers meet it most and writers follow it least.

  The rule holds for every entry, revoked ones included. A revoked entry keeps
  its statement and its **Revoked on** section. A listing where some titles name
  a fix and others name a gap reads as two kinds of entry, when there is one.

  The check holds only that the two spellings agree, and nothing holds the
  shape. A read of the corpus for the shape on 2026-08-22 returned thirty-five
  titles, of which one had drifted. The rest were the affirmative "A is B, not
  C", which states what is and then excludes. A check would report those
  thirty-four every time. So a second read of the title before the commit keeps
  this rule.

* The **bold first sentence** is the decision. A reader who stops after it knows
  the decision. Everything under it is what settled it.

* The sections are a fixed set, in this order: **Evidence**, **Decided**,
  **Assumed**, **Wrong if**. Only **Wrong if** is a must. An entry that cannot
  say what would falsify it is not a decision worth a record. ``date`` is the
  day of the decision.

* Each section holds one bullet per item. Half the entries decide more than one
  thing, and a fifth rest on more than one assumption. That is why these are
  sections, and not a bullet that repeats its own label.

* ``readings`` lists the days somebody went back to the entry and found nothing
  to change, newest last. A read that did change something gets a dated section
  instead. Nobody writes both for one day.

* ``coveredBy`` lists the tests that would catch a fired **Wrong if**, and a
  command **generates** it. The test declares ``#[Decision('D-DIS-004')]``.
  ``bin/cli decisions:cover`` writes the front matter from every such attribute.
  The next run undoes an edit to the list by hand, and
  ``bin/cli decisions:check`` fails meanwhile and names the command. So the two
  ends cannot say different things.

  `D-DOC-048 <../../decisions/documentation/doc-048-a-test-declares-the-decision-it-holds.md>`_
  says why the attribute is the source and not the entry.

* Most entries are about process, and nothing runs over them. Where something
  does, the attribute turns the promise into something the suite keeps. A failed
  test also prints the attribute, so the session that made the test red lands
  here. A test that declares an id no entry has fails the check as well. Every
  test an entry names, even aside, has to exist —
  ``DecisionsTest::everyTestADecisionNamesExists``.

* ``coveredBy`` is front matter, not a section, because it is data. A listing
  counts it, a check reads it, and a failed test prints it. Where nothing can
  hold an entry, ``coveredBy: []`` says so, and a bullet of **Decided** gives
  the reason. Otherwise an empty list somebody read and a list nobody wrote look
  identical.

The dated sections
------------------

The foot of an entry holds dated sections and nothing else:

* **Confirmed on** ``<date>`` where somebody went back and the decision held.
* **Revoked on** ``<date>`` where it did not.
* **Since then** for what followed without a date of its own.

**A dated section says what the read changed**, in twelve lines or fewer. That
is a **Wrong if** that fired, a statement that no longer describes this server,
or a boundary that moved. What does not fit is no prose to trim. It is a
finding, and it belongs in **Decided**, in **Wrong if**, or in an entry of its
own.

**A read that changed nothing is a date and no section.** The date goes into
``readings:`` in the front matter, newest last. That is all such a read says:
somebody went back to the entry, and when. A move to ``confirmed`` or
``revoked`` is a change and keeps its section. Every later read of the same
entry is a date under it.

``bin/cli decisions:check`` counts the sections over the measure, and fails on
none of them. The corpus compacts onto the rule over time, rather than hold to
it from one commit.
`D-DOC-066 <../../decisions/documentation/doc-066-a-dated-section-says-what-the-reading-changed.md>`_
measured what the form had cost. A quarter of ``decisions/`` sat below a dated
heading, and two entries had become journals of their own applications.

``revokedBy`` tells the reader of a revoked entry where to go instead. It names
one decision, only a revoked entry may have it, and the generated listing shows
it. So nobody has to open a dead entry to find the live one.

Revoking a statement
--------------------

A revocation is not only for an entry that turned out wrong. An entry whose
**statement** no longer describes this server gets a revocation as well, and a
new entry for what holds instead. The reader of a listing has the headline and
the status and nothing else. ``confirmed`` beside a sentence that is no longer
the case reads as a claim about today.

`D-ANS-023 <../../decisions/answers/ans-023-a-viewhelper-question-is-answered-by-widening-the-manual-index.md>`_
is the worked example. Its statement said that no manual this lookup indexes
documents a ViewHelper. That was true, and the change it asked for landed the
same day. A measure of its three **Wrong if** found that none of them held. From
that moment the statement described a lookup that no longer existed. It has its
**Confirmed on** for the read, and a **Revoked on** below it for the
supersession.

``revokedBy`` points at
`D-ANS-026 <../../decisions/answers/ans-026-the-viewhelper-reference-is-indexed.md>`_,
and the successor is what a reader builds on.

The successor is a new entry, not a rewrite of the old one, and the reason needs
precision. Its **Wrong if** is a different list. What can go wrong now is a book
that moves, and what could go wrong before was a book that is gone. A rewrite of
the old entry would throw away the finding and keep its number. Nothing
afterwards could tell the two apart.

A revocation takes the tests with it. A test that declares a revoked entry
claims to hold something this server says it no longer does. So
``bin/cli decisions:check`` fails on one and names ``revokedBy``. The
``#[Decision]`` attribute moves to the successor, or goes.
`D-DOC-052 <../../decisions/documentation/doc-052-a-revoked-entry-names-no-test.md>`_
is the measure behind that.

Of the eleven revoked entries that a test declared, nine named a test the
successor already had. One named a test that disproves it.

``confirmed`` keeps its purpose: an entry whose statement survives the read. A
decision about the shape of an answer, or about which source answers a question,
is as true after a visit as before. That is the ordinary case.

What rests on one
-----------------

A requirement names the decisions it rests on, in its own front matter —
``restsOn: [D-FBK-005]``. That is the one link that neither directory can see on
its own. A decision can get a revocation while the requirement on top of it
keeps its ``held`` status and its green test. The reason under the requirement
is then gone. ``bin/cli unresolved:list`` reports that. Nothing fails on it,
because whether the requirement still stands is a judgement.

Going back to one
-----------------

Most decisions are open and stay open, which makes the state easy to overlook. A
**Wrong if** that nobody reads is a promise, and nothing says when to keep it.
``bin/cli unresolved:list`` counts them, and names the oldest one that nobody
has been back to. Age disproves nothing, but that entry is the one the
repository has moved furthest away from since. To go back to one and add
**Confirmed on** or **Revoked on** is a legitimate task with no feature behind
it.

A visit that settles nothing is a legitimate task too, and it is the ordinary
outcome. A **Wrong if** that only a forward run, an installation or an outside
event could answer stays open, however careful the read. Such a read leaves a
**Since then** that says what the session looked at and what would still settle
it. The entry stays ``open``. It now differs from one that nobody has opened,
and the listing counts that difference.

``bin/cli decisions:check`` holds every file to the shape this page describes.
``composer test`` runs the same check through ``DecisionsTest``, except for the
listing. The listing comes from every file in a group, so it can only be true on
a checkout that has all of them. ``DecisionsTest`` would fail every branch that
adds an entry —
`D-FBK-011 <../../decisions/feedback/fbk-011-the-suite-holds-what-one-branch-can-be-right-about.md>`_.
