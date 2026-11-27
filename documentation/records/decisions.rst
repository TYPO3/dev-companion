:navigation-title: Decisions

What a decision is
==================

This page covers what an entry in ``decisions/`` is, what holds it, and what its
state means. :doc:`writing-a-decision` says where an entry goes and how to write
it.

A commit message says what changed and why. It cannot hold the part that may not
survive. That part is the assumption the change rests on, the evidence at hand
at the time, and what would show the decision wrong.

One entry is one decision worth a second look. A change nobody would need to
reconsider gets no entry. The commit that implements an entry writes it. Nothing
here is a proposal stage: by the time a decision exists, the change it describes
is in the code.

An entry backs what rests on it. What must be true from now on is a requirement,
and the requirement names the decisions it rests on in its own ``restsOn:``. So
a reader finds a revoked decision from the requirement on top of it, see
:doc:`what a requirement is <requirements>`.

A feedback is one occasion on which a decision gets made. It is not the purpose
of this directory. A reader reads the entry long after that question had its
answer. A decision arrives as readily from a review, a recorded run, or a
question somebody had to answer.

What holds an entry
-------------------

A test holds an entry, where a test can. The test declares the id it holds with
``#[Decision]``, and ``coveredBy`` comes from that. A failed run names the entry
the test held. So a session that changes the behaviour arrives at what rested on
it.

Most entries are about process, and nothing runs over them.
``bin/cli decisions:check`` reads out the entries that point at this
repository's code and name no test. It fails on none of them.

What the state means
--------------------

``status`` is one of ``open``, ``confirmed`` and ``revoked``, the
``DecisionStatus`` enum. It names the **last** dated section, not the only one.
A decision has a history. A run on the morning of 2026-08-02 confirmed
``D-KNW-003``, and the evidence that arrived the same day revoked it. Both are
in the file, and a reader relies on the latest.

The status is not a workflow. ``open`` does not mean unbuilt. It means that
nothing has settled the **Wrong if** either way. Most decisions are open and
stay open, and that makes the state easy to overlook.
:doc:`bin/cli unresolved:list <index>` reads them out.

Going back to an entry
~~~~~~~~~~~~~~~~~~~~~~

A session no longer schedules a second read of an entry a test declares. The
failure prints the entry. So the read happens when the behaviour moves, and the
session that moved it does the read.

``bin/cli unresolved:list`` counts the open entries, the ones nobody has been
back to, and the ones no test holds. It names the oldest of the last kind.
`D-DOC-054 <../../decisions/documentation/doc-054-a-held-decision-is-read-when-its-behaviour-moves.md>`_
is the measure behind that: of 155 entries nobody had been back to, a test
declares 120.

An ``open`` entry is one of two cases: an entry nobody has opened, or an entry a
read left open. Only the first case is work. A read has two outcomes:

* A read that settles the **Wrong if** changes the status.

* A read that settles it neither way leaves the status open. It writes a **Since
  then** where it changed something, or a ``readings:`` date where it changed
  nothing.

Either one tells an entry somebody has been back to from one nobody has opened.
So the listing counts the two cases apart, and names the oldest entry nobody has
opened and no test holds.

A revoked entry
~~~~~~~~~~~~~~~

A revoked entry names in ``revokedBy`` where its reader goes instead.
``revokedBy`` names one decision, and only a revoked entry may have it. The
generated listing shows it. So nobody has to open a dead entry to find the live
one.
