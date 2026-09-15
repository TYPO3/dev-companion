:navigation-title: Decisions

What a decision is
==================

A commit message says what changed and why. What it cannot carry is the part
that may not survive. The assumption the change rests on, the evidence at hand
at the time, and what would show the decision wrong.

One entry is one decision worth a second look, and a change nobody would need to
reconsider does not get one. The commit that implements an entry writes it.
Nothing here is a proposal stage, and by the time a decision exists the change
it describes is in the code.

An entry backs what stands on it. What must be true from now on is a
requirement, and it names the decisions it rests on in its own ``restsOn:``. So
a reader finds a revoked one from the requirement on top of it, see
:doc:`what a requirement is <requirements>`. Where an entry goes and how to
write it is :doc:`writing-a-decision`.

What holds an entry is a test, where one can. It declares the id it holds with
``#[Decision]``, and ``coveredBy`` comes from that. A failed run names the entry
it held, so a session that changes the behaviour lands at what rested on it.
Most entries are about process and nothing runs over them.
``bin/cli decisions:check`` reads out the ones that point at this repository's
code and name no test, and fails on none of them.

A feedback is one occasion on which a decision gets made. It is not what this
directory is for. A reader reads the entry long after that question had its
answer. A decision arrives as readily from a review, a recorded run, or a
question somebody had to answer.

What the state means
--------------------

``status`` is one of ``open``, ``confirmed`` and ``revoked`` — the
``DecisionStatus`` enum — and it names the **last** dated section rather than
the only one. A decision has a history. A run on the morning of 2026-08-02
confirmed ``D-KNW-003`` and the evidence that arrived the same day revoked it,
and both are in the file. What a reader relies on is the latest.

The status is not a workflow. ``open`` does not mean unbuilt. It means nothing
has settled the **Wrong if** either way. Most decisions are open and stay that
way, which is what makes the state easy to overlook.
:doc:`bin/cli unresolved:list <index>` is what reads them out.

Going back is no longer something a session schedules for an entry a test
declares. The failure prints it, so the read happens when the behaviour moves
and it happens to whoever moved it. ``bin/cli unresolved:list`` counts the open
entries, the ones nobody has been back to, and the ones no test holds. It names
the oldest of the last kind.
`D-DOC-054 <../../decisions/documentation/doc-054-a-held-decision-is-read-when-its-behaviour-moves.md>`_
is the measure behind that. Of 155 entries nobody had been back to, a test
declares 120.

It covers two cases, and only one of them is work. A read that settles the
**Wrong if** changes the status. A read that settles neither leaves it open and
writes a **Since then** where it changed something or a ``readings:`` date where
it changed nothing. Either one separates an entry somebody has been back to from
one nobody has opened. So the listing counts them apart and names the oldest of
the second kind.

``revokedBy`` is what a revoked entry owes its reader: where to go instead. It
names one decision, only a revoked entry may carry it, and the generated listing
shows it. So nobody has to open a dead entry to find the live one.
