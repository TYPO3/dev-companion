:navigation-title: Requirements

What a requirement is
=====================

One thing that must be true of this server, with where the demand came from and
what holds it to that. A test where there is one, ``not guarded`` where there is
none.

It has to hold on while everything around it changes. That is what makes it
worth a file of its own rather than a sentence in the change that established
it. The change ends, the demand does not.

This is the base. What a change rested on and what would show it wrong is a
decision. A requirement names the ones it stands on in its own ``restsOn:``, see
:doc:`what a decision is <decisions>`. Where an entry goes and how to write it
is :doc:`writing-a-requirement`.

A feedback is one route a demand arrives by, and the most common one. It is not
what this directory is for. A reader reads an entry long after the question that
produced it had its answer. A demand arrives as readily from a review, from a
recorded run, or from a read that found nothing that holds a rule everybody
assumed.

What the state means
--------------------

``status`` is ``held`` or ``open``, and a third state derives from them:

* **open** — accepted and not built yet. It comes out beside **not guarded**
  rather than in a list of its own. A requirement nobody has implemented and one
  that could silently regress are the same kind of thing.
* **not guarded** — built, and ``heldBy`` names no test. It never stands in the
  front matter. An entry may not claim it of itself, and it is what a claim of
  ``held`` turns out to be. It is the honest answer for a requirement no test
  can hold. It is the one worth a look in a listing, because from afar it looks
  exactly like ``held``.
* **held** — built, and the tests it names hold it there. They name themselves.
  A test carries ``#[Requirement]`` and ``bin/cli requirements:cover`` writes
  ``heldBy`` from it, so the state follows the tests rather than a list somebody
  keeps.

They are the ``RequirementState`` enum. ``bin/cli requirements:check`` cannot
fail on **open** or **not guarded**, since both are legitimate.
:doc:`bin/cli unresolved:list <index>` reads them out instead, together with the
decision about each. That is a todo that names the id, or a ``judged:`` date
that says a session read the entry and left it as it is. See
:ref:`judging one <judging-a-requirement>`. Nothing in ``requirements/`` reaches
the order of the work on its own; that listing is the whole of the tie.
