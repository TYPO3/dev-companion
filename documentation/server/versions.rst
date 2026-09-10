:navigation-title: Version binding

Which versions an answer holds for
==================================

The rule is in `AGENTS.md <../../AGENTS.md>`_: a statement that does not hold on
every covered TYPO3 says so as data. This is how it is written, and what follows
from it.

Which versions are covered is declared in ``knowledge/versions.json`` and
nowhere else; every check that needs the list reads it from there.

.. image:: ../images/version-binding.svg
    :zoomable:
    :alt: TYPO3 12 through 15 form the covered timeline; unbound statements
          apply to all of it, while since and until bind one statement and
          require verification on both sides of the boundary.

A statement that does not hold on all of them says so **as data, not as prose**.
``since`` and ``until`` carry the major it starts and stops holding at, and the
answer renders that beside the sentence. A statement without either holds
everywhere the knowledge base reaches, which is what most of them do.

.. code-block:: json

    "hints": [
      "Template files below Resources/Private/ are resolved by the view factory.",
      { "text": "A label is referenced by its translation domain.", "since": 14 },
      { "text": "Every content column needs an explicit identifier.", "since": 15 }
    ]


That is the whole mechanism, and it exists because the alternative is worse: a
caller on an LTS given a ``main`` answer changes code that then fails at
runtime, and the failure is silent. Everything else on this page follows from
it.

* **Bind the statement, not the hint.** A subsystem does not change wholesale;
  one sentence in it does. Splitting a hint per version duplicates the six
  statements that did not change.
* **A bound statement is verified on both sides of its boundary.** ``since: 14``
  means it was checked against 13.4 and found absent, and against 14 and found
  present. Name both branches in the commit message — that is the evidence
  nobody can reconstruct later.
* **Split a narrowed statement before binding it.** ``until`` says the sentence
  stopped holding, so a subject that survived under a new condition needs two
  statements: the unbound half that still describes it, and the bound half
  carrying what arrived. Bound whole, the surviving half reads as removed, and
  the range is contiguous either way so nothing sees it — ``D-VER-006``.
* **Prose stays free of version numbers.** The binding is in the field, so the
  sentence does not need "since v14" in it, and a sentence that carries one
  cannot be filtered, re-rendered, or checked. ``HintsTest`` enforces this.
* **State the shape that is current, not the history it replaced.** A bullet
  whose payload is "X is deprecated" becomes a bullet whose payload is "this is
  what new code looks like", bound to the version the new shape arrived in. The
  predecessor is then implied and stays a clause, not a bullet of its own.
* **Where an area was rebuilt underneath, the hints say so themselves.** Routing
  the caller to ``typo3_changelog_lookup`` for what the version changed here is
  a call a session under time pressure skips, and what it acts on instead is the
  statement that described the area before it moved.
* **Where the answer is branch-specific in a way a range cannot express, give
  the procedure, not the result.** Name what to read in the checkout — an
  ``@deprecated`` annotation, a ``trigger_error(..., E_USER_DEPRECATED)`` call,
  ``Documentation/Changelog/``, the extension scanner matchers. What a given
  version deprecated is a list; how to find it is a procedure.
* **No concrete changelog file names, no counts.** Both are a snapshot of one
  checkout and go stale silently. Counts measured while writing a hint are
  evidence for the author and belong in the commit message, not in the answer.
* **"Check whether X" is not a hint, it is a check.** ``hints`` carries
  statements, ``checks`` carries commands that run. A check-shaped sentence with
  no command behind it tells the caller nothing it did not know already.
* **Who is obliged is data as well.** A version range is not the only thing an
  answer has to know per caller. ``binding: "core"`` marks what is a condition
  of a patch to the core and a convention anywhere else — the backend's own
  design system, the changelog artifact, the paths of the mono repository. It
  sits on the hint where the whole subject is that, and on the single statement
  where one sentence in an otherwise transferable hint is. Nothing is dropped
  for it: outside the core the content stays and carries the marker, because a
  project building a backend module wants exactly those rules. Absent is the
  ordinary case and means it holds wherever TYPO3 is written — an API that
  throws throws in a sitepackage too.
  ``VersionsTest::whoIsObligedIsWrittenAsDataToo`` holds the vocabulary to that
  one value; a second one is a decision, not a data entry.
* **The catalogs carry the same binding, and it decides.** A
  ``knowledge/catalog/`` entry is markup taken from one revision, so
  ``since``/``until`` there is the whole entry rather than one sentence, and
  ``targetVersion`` withholds it instead of qualifying it — a class that does
  not exist fails in a browser, silently. The binding is derived, not judged:
  ``bin/cli components:check`` re-reads every covered checkout and reports each
  entry whose recorded range no longer matches, so a core update invalidates it
  loudly. It is derived from names, so a demo rewritten around the same classes
  reads as unchanged — the entry records a digest of what each covered demo
  said, and the same command fails on a rewrite no name would show
  (``D-CAT-001``).
* **A class the query names is a second question, and carries its own range.**
  What withholds an entry is usually a custom property that arrived after the
  classes beside it, and a caller that borrowed one backend class is asking
  where that class goes rather than for something to paste. So each class
  carries the majors it is written on and where it sits — around the component,
  on it, or inside it — and a class named outright is answered below the entry's
  own binding, never as markup (``D-CAT-006``, ``D-CAT-008``).
* **Where a class sits is itself version-bound, and it is derived.**
  ``bin/cli components:derive`` reads the ``backend.css`` each covered branch
  commits and writes the position and the range of every class the catalog
  names; ``bin/cli components:check`` re-derives and fails where what is
  committed has fallen behind. Reading four committed files is the verification,
  so no installation is involved and no range is kept by hand (``D-CAT-008``).
* **A directory is not evidence that what it demonstrates is inside it.** Where
  a worked example promises a shape rather than a path, the entry names the two
  or three files that carry that shape in ``files``, and the range is derived
  from those as well — a rewrite that keeps the directory and moves what is in
  it is otherwise exactly what a range on existence cannot see (``D-CAT-007``).
