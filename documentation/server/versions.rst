:navigation-title: Version binding

Which versions an answer holds for
==================================

This page covers how a statement in the knowledge base says which TYPO3 versions
it holds for. It gives the form of that data and the rules that follow from it,
for hints and for the catalogs.

The rule is in `AGENTS.md <../../AGENTS.md>`_: a statement that does not hold on
every covered TYPO3 says so as data.

``knowledge/versions.json`` declares which versions the server covers, and no
other file does. Every check that needs the list reads it from there.

The covered lines are the LTS line of each major and ``main``. A minor release
before an LTS is outside them. So a bound at a major is exact for every covered
line.

.. diagram::
    :zoomable:
    :caption: Bind only what changed; keep the surrounding knowledge shared.

    bars canvas=960
      bands "12.4 · LTS,13.4 · LTS,14.3 · stable,main · development"
      axis "Covered line"
    title "Version differences are data"
    description "TYPO3 12 through 15 form the covered timeline. A statement without since or until holds on all of it. Since and until bind one statement to the lines it holds on, and a check runs on both sides of that boundary."

    row `no since / until`
      sub "Holds on every covered line."
      segments 4
    row `since: 14`
      sub "Absent on 13; present on 14."
      segments absent=2 2
      emphasis
    row `until: 13`
      sub "Present on 13; absent on 14."
      segments 2 absent=2

    legend
      ink "the statement holds"
      absent "the statement is absent"

The form
--------

A statement that does not hold on all covered lines says so **as data, not as
prose**. ``since`` and ``until`` give the major at which it starts and stops to
hold. The answer renders that beside the sentence. A statement without either
field holds everywhere the knowledge base reaches, and most statements do.

.. code-block:: json

    "hints": [
      "Template files below Resources/Private/ are resolved by the view factory.",
      { "text": "A label is referenced by its translation domain.", "since": 14 },
      { "text": "Every content column needs an explicit identifier.", "since": 15 }
    ]


That is the whole mechanism. It exists because the alternative is worse. A
caller on an LTS who gets a ``main`` answer changes code that then fails at
runtime, and the failure is silent. Everything else on this page follows from
it.

Binding a statement
-------------------

* **Bind the statement, not the hint.** A subsystem does not change as a whole;
  one sentence in it does. A hint split per version repeats the six statements
  that did not change.

* **Verify a bound statement on both sides of its boundary.** ``since: 14``
  means a check against 13.4 found it absent, and a check against 14 found it
  present. Name both branches in the commit message. Nobody can reconstruct that
  evidence later.

* **Split a narrowed statement before you bind it.** ``until`` says the sentence
  no longer holds. So a subject that survived under a new condition needs two
  statements:

  * the unbound half that still describes it;

  * the bound half with what arrived.

  If you bind the statement whole, the half that survives reads as removed. The
  range is contiguous either way, so no check sees it — ``D-VER-006``.

* **Keep version numbers out of the prose.** The bound is in the field, so the
  sentence does not need "since v14". Nothing can filter, re-render or check a
  sentence with a version number in it. ``HintsTest`` enforces this.

What a hint states
------------------

* **State the shape that is current, not the history it replaced.** A bullet
  that says "X is deprecated" becomes a bullet that says "this is what new code
  looks like". The bound is the version in which the new shape arrived. The
  predecessor is then implied. It stays a clause, not a bullet of its own.

* **Where an area was rebuilt, the hints say so themselves.** A session under
  time pressure skips a route to ``typo3_changelog_lookup`` for what the version
  changed. It acts instead on the statement that described the area before the
  change.

* **Where a range cannot express a branch-specific answer, give the procedure,
  not the result.** Name what to read in the checkout:

  * an ``@deprecated`` annotation;

  * a ``trigger_error(..., E_USER_DEPRECATED)`` call;

  * ``Documentation/Changelog/``;

  * the extension scanner matchers.

  What a given version deprecated is a list. How to find it is a procedure.

* **Name no changelog files, and give no counts.** Both are a snapshot of one
  checkout and go stale silently. Counts the author measures while the author
  writes a hint are evidence for the author. They belong in the commit message,
  not in the answer.

* **"Check whether X" is a check, not a hint.** ``hints`` has statements, and
  ``checks`` has commands that run. A sentence shaped like a check with no
  command behind it tells the caller nothing new.

Who must follow it
------------------

A version range is not the only thing an answer must know per caller. Who must
follow a statement is data as well.

``binding: "core"`` marks what is a condition of a patch to the core and a
convention anywhere else. Examples are the design system of the backend, the
changelog artifact and the paths of the mono repository. The marker sits on the
hint where the whole subject is like that. It sits on the single statement where
only one sentence of an otherwise transferable hint is.

Nothing gets dropped for the marker. Outside the core, the content stays and
shows the marker. A project that builds a backend module wants exactly those
rules.

Without the marker, which is the usual case, a statement holds wherever somebody
writes TYPO3. An API that throws, throws in a sitepackage too.
``VersionsTest::whoIsObligedIsWrittenAsDataToo`` limits the vocabulary to that
one value. A second value is a decision, not a data entry.

The catalogs
------------

* **The catalogs use the same binding, and there it decides.** A
  ``knowledge/catalog/`` entry is markup taken from one revision. So
  ``since``/``until`` there applies to the whole entry, not to one sentence.

  ``targetVersion`` withholds the entry, not a qualification, because a class
  that does not exist fails silently in a browser. The bound derives from the
  checkouts, with no judgement.

  ``bin/cli components:check`` reads every covered checkout again. It reports
  each entry whose recorded range no longer matches, so a core update makes the
  entry fail loudly.

  The command derives the bound from the names the entry gives: Sass files,
  classes, custom properties, tag names and attributes. It does not read the
  structure of the markup, so a demo rewritten around the same classes reads as
  unchanged. So the entry records a digest of what each covered demo said. The
  same command fails on a rewrite that no name would show (``D-CAT-001``).

* **A class the query names is a second question, with its own range.** Usually
  a custom property that arrived after the classes beside it withholds an entry.
  A caller that borrowed one backend class asks where that class goes, not for
  something to paste.

  So each class records the majors it exists on and where it sits: around the
  component, on it, or inside it. A class named outright gets an answer below
  the bound of the entry, never as markup (``D-CAT-006``, ``D-CAT-008``).

* **Where a class sits is itself version-bound, and it derives from the
  checkouts.** ``bin/cli components:derive`` reads the ``backend.css`` each
  covered branch commits. It writes the position and the range of every class
  the catalog names.

  ``bin/cli components:check`` derives them again and fails where the committed
  state is behind. A read of four committed files is the verification. So no
  installation takes part, and nobody keeps a range by hand (``D-CAT-008``).

* **A directory is no evidence that what it demonstrates is inside it.** A
  worked example can promise a shape, not a path. Then the entry names in
  ``files`` the two or three files that have that shape, and the range derives
  from those too. Otherwise a rewrite that keeps the directory and moves its
  content is exactly what a range on existence cannot see (``D-CAT-007``).
