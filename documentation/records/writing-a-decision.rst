:navigation-title: Writing a decision

Writing a decision, and going back to one
=========================================

What a decision is, what rests on it and what its states mean is
:doc:`decisions`. This page is where an entry goes, how to write it, and what a
later session adds to one.

Where an entry lives
--------------------

One decision is one file, named after its id, in the group its id names. The
group is what the decision is about, and the prefix carries it. So a file's id
decides its path and two entries cannot quietly share a number.

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

The number is three digits wide, in the file name and in the id alike. That is
what lists a group in the order of its writers. Without the pad, ``dis-10``
sorts between ``dis-1`` and ``dis-2`` in every directory listing and in anything
that compares the ids as text. A requirement takes its number the same way, so
one habit covers both. ``bin/cli decisions:check`` fails on any other width.

Each group's ``readme.md`` says what that group is about, and
``bin/cli decisions:index`` generates the listing at the foot of it. The same
command generates the listing at the foot of ``decisions/readme.md``, which
carries the whole of it under a heading per group. Inside a group the order is
newest first. The latest decisions across all of them are
``bin/cli decisions:list``. The revoked entries keep a run of their own at the
foot, because mixed into the rest they looked exactly like something to build
on.

Prose above either listing may not use a third-level heading. That is what the
command reads as the start of the generated half, and it would go with the next
regeneration.

An id never sees a second use, and an entry never goes. A decision that turned
out wrong is the one most worth a read, and a revocation happens in place.

Renumbering one
---------------

.. code-block:: bash

    bin/cli decisions:renumber <decision> [<number>]

Two sessions reading one ``main`` write one id, and the second one to come home
moves. The dangerous half is the move rather than the collision, so the command
splits the references in two. It rewrites everything whose own line names the
entry's file: the entry, its name, every link, and the reference definitions the
generated listings end with. It prints everything else with its file, its line
and its text.

**What it prints is what needs a read, one at a time.** A bare id says nothing
about which entry it means, and both wrong pointers on record were one. A
requirement's ``restsOn:``, where a check covers existence and not correctness,
and an id in a sentence, where no check covers either.
``git diff main -- <file>`` settles it, since a line this branch added means
this branch's entry. A search and replace over the whole list is the failure
this command exists to make visible, not one it prevents.
`D-DOC-015 <../../decisions/documentation/doc-015-a-renumber-moves-what-a-link-path-settles-and-names-the-rest.md>`_
is the measure behind the split.

Named no number, the entry takes the next one free in its group, which is one
past the highest rather than the first gap. The generated listings go back in
order where they already carried the entry, because the number is what a group
sorts on.

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


* The **title** is the front matter's, and the heading repeats it so a reader of
  the file sees it where a heading belongs. ``bin/cli decisions:check`` holds
  the two to each other, so a rewrite of one is a rewrite of both.

  It is the name a reader finds the entry by in a listing of hundreds, and it is
  not the statement. The statement is the bold sentence below it, which
  ``bin/cli prose:check`` holds to the STE measure
  `D-DOC-070 <../../decisions/documentation/doc-070-this-repository-writes-in-asd-ste100.md>`_
  sets. A title says one thing, in twelve or so. Where an ``and`` joins two
  claims, the second one is the statement's and usually stands in it already.
  ``bin/cli prose:check`` counts the titles that carry more, worst first, and
  fails on none of them.

  It says what holds from now on, in the affirmative. Not the deficiency it
  corrects and not a negation. "The ladder needs an outcome that builds
  something", not "the ladder had no outcome that builds" and not "the ladder is
  not a filter". What was wrong is evidence and belongs in the paragraph under
  the bold sentence. A reader reads a listing of 138 entries for what each one
  settled. A title in the past tense or in the negative makes the reader
  reconstruct that. It is `AGENTS.md <../../AGENTS.md>`_'s "say what is, not
  what it is not", applied where readers meet it most and writers follow it
  least. It holds for every entry, revoked ones included. What a revoked entry
  preserves is its statement and its **Revoked on** section. A listing where
  some titles name a fix and others name a gap reads as two kinds of entry when
  there is one.

  What the check holds is that the two spellings agree, and nothing holds the
  shape. A read of the corpus for it on 2026-08-22 returned thirty-five titles
  of which one had drifted. The rest were the affirmative "A is B, not C", which
  states what is and then excludes. A check would report those thirty-four every
  time, so what keeps this is somebody rereading the title before the commit.
* The **bold first sentence** is the decision. A reader who stops after it knows
  the decision. Everything under it is what settled it.
* The sections are a fixed set, in that order: **Evidence**, **Decided**,
  **Assumed**, **Wrong if**. Only **Wrong if** is a must. An entry that cannot
  say what would falsify it is not a decision worth a record. ``date`` is the
  day of the decision.
* Each section holds one bullet per item. Half the entries decide more than one
  thing and a fifth rest on more than one assumption. That is why these are
  sections and not a bullet that repeats its own label.
* ``readings`` lists the days somebody went back to the entry and found nothing
  to change, newest last. A reading that did change something is a dated section
  instead, and the two are never both written for one day.
* ``coveredBy`` lists the tests that would catch a fired **Wrong if**, and it is
  **generated**. The test declares ``#[Decision('D-DIS-004')]`` and
  ``bin/cli decisions:cover`` writes the front matter from every such attribute.
  The next run undoes an edit to the list by hand, and
  ``bin/cli decisions:check`` fails meanwhile and names the command. So the two
  ends cannot say different things.
  `D-DOC-048 <../../decisions/documentation/doc-048-a-test-declares-the-decision-it-holds.md>`_
  is why the attribute is the source and not the entry.
* Most entries are about process and nothing runs over them. Where something
  does, the attribute is what turns the promise into something the suite keeps.
  It is also what a failed test prints, so the session that made it red lands
  here. A test that declares an id no entry has fails the check as well. Every
  test an entry names in passing has to exist, see
  ``DecisionsTest::everyTestADecisionNamesExists``.
* It is front matter rather than a section because it is data. A listing counts
  it, a check reads it, and a failed test prints it. Where nothing can hold an
  entry, ``coveredBy: []`` says so and the reason is a bullet of **Decided**. An
  empty list somebody read and a list nobody wrote look identical otherwise.

The dated sections
------------------

A dated section at the foot and nothing else. **Confirmed on ``<date>``** where
somebody went back and it held, **Revoked on ``<date>``** where it did not.
**Since then** for what followed without a date of its own.

**A dated section says what the read changed**, in twelve lines or fewer. A
**Wrong if** that fired, a statement that no longer describes this server, a
boundary that moved. What does not fit is not prose to trim. It is a finding,
and it belongs in **Decided**, in **Wrong if**, or in an entry of its own.

**A reading that changed nothing is a date and no section.** It goes into
``readings:`` in the front matter, newest last, because that is all such a read
says. Somebody went back to the entry, and when. Going ``confirmed`` or
``revoked`` is a change and keeps its section; every later reading of the same
entry is a date under it.

``bin/cli decisions:check`` counts the sections over the measure and fails on
none of them. The corpus compacts onto the rule rather than holds to it from one
commit.
`D-DOC-066 <../../decisions/documentation/doc-066-a-dated-section-says-what-the-reading-changed.md>`_
measured what the form had cost. A quarter of ``decisions/`` sat below a dated
heading, and two entries had become journals of their own applications.

``revokedBy`` is what a revoked entry owes its reader: where to go instead. It
names one decision, only a revoked entry may carry it, and the generated listing
shows it. So nobody has to open a dead entry to find the live one.

Revoking a statement
--------------------

Revoking is not only for an entry that turned out wrong. An entry whose
**statement** no longer describes this server gets a revocation as well, and a
new one for what holds instead. The reader of a listing has the headline and the
status and nothing else. ``confirmed`` beside a sentence that is no longer the
case reads as a claim about today.

`D-ANS-023 <../../decisions/answers/ans-023-a-viewhelper-question-is-answered-by-widening-the-manual-index.md>`_
is the worked example. Its statement said that no manual this lookup indexes
documents a ViewHelper. That was true, the change it asked for landed the same
day, and a measure of its three **Wrong if** found none of them held. From that
moment the statement described a lookup that no longer existed. It carries its
**Confirmed on** for the read and a **Revoked on** below it for the
supersession. ``revokedBy`` points at
`D-ANS-026 <../../decisions/answers/ans-026-the-viewhelper-reference-is-indexed.md>`_,
and the successor is what a reader builds on.

The successor is a new entry rather than a rewrite of the old one, and the
reason deserves precision. Its **Wrong if** is a different list. What can go
wrong now is a book that moves, and what could go wrong before was a book that
is missing. Rewriting the old entry would throw away the finding while keeping
its number, and nothing afterwards could tell the two apart.

Revoking takes the tests with it. A test that declares a revoked entry claims to
hold something this server says it no longer does. So
``bin/cli decisions:check`` fails on one and names ``revokedBy``. The
``#[Decision]`` attribute moves to the successor, or goes.
`D-DOC-052 <../../decisions/documentation/doc-052-a-revoked-entry-names-no-test.md>`_
is the measure behind that. Of the eleven revoked entries a test declared, nine
named a test the successor already carried and one named a test that disproves
it.

``confirmed`` stays what it is for: an entry whose statement survives the
reading. A decision about the shape of an answer, or about which source answers
a question, stays as true after a visit as before. That is the ordinary case.

What rests on one
-----------------

A requirement says which decisions it stands on, in its own front matter —
``restsOn: [D-FBK-005]``. That is the one crossing neither directory can see on
its own. A decision gets a revocation, and the requirement on top of it keeps
its ``held`` status and its green test. The reason under it is gone.
``bin/cli unresolved:list`` reads that out; nothing fails on it, because whether
the requirement still stands is a judgement.

Going back to one
-----------------

Most decisions are open and stay that way, which is what makes the state easy to
overlook. A **Wrong if** nobody reads is a promise, and nothing says when to
keep it. ``bin/cli unresolved:list`` counts them and names the oldest nobody has
been back to. Not because age disproves anything, but because that is the entry
the repository has moved furthest away from since. Going back to one and adding
**Confirmed on** or **Revoked on** is a legitimate task with no feature behind
it.

A visit that settles nothing is one too, and it is the ordinary outcome. A
**Wrong if** that only a forward run, an installation or an outside event could
answer stays open however careful the read. What that read leaves is a **Since
then** that says what the session looked at and what would still settle it. The
entry stays ``open``, and it is no longer indistinguishable from one nobody has
opened — which is the difference the listing counts.

``bin/cli decisions:check`` holds every file to the shape this page describes,
and ``composer test`` runs the same check through ``DecisionsTest``, except the
listing. That one comes from every file in a group, so it can only be true on a
checkout that has all of them. ``DecisionsTest`` would fail every branch that
adds an entry, see
`D-FBK-011 <../../decisions/feedback/fbk-011-the-suite-holds-what-one-branch-can-be-right-about.md>`_.
