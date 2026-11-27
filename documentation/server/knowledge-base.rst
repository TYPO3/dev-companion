:navigation-title: Knowledge base

The bundled knowledge
=====================

This page covers the knowledge the package ships in
`knowledge/ <../../knowledge/>`_. It says what is there, how the server reads
it, and how a writer words a statement in it.

The tools and resources answer from files in the package. They need nothing
else: no installation, no network, no database.
:doc:`asking the installation <asking-the-installation>` says what the server
reads from an installation instead, and why.

.. directory-tree::
   :level: 2

   * ``knowledge/``

     * ``documents/`` the prose corpus that ``typo3://guides/{documentId}``
       serves and ``typo3_rule_lookup`` searches, one directory per scope

       * ``any/``
       * ``core/``
       * ``extension/``
       * ``project/``

     * ``hints/`` one file per subject, such as ``datahandler.json``

     * ``catalog/`` the component catalog and the shipped system extensions

       * ``component/``
       * ``reference/``
       * ``system-extension/``
       * ``meta.json``

     * ``task-intents.json`` the workflows a task routes to
     * ``test-suite-hints.json`` the suites a path reaches
     * ``icon-concepts.json`` the icons a concept maps to
     * ``server-scope.json`` what the server covers, and its instructions
     * ``versions.json`` the TYPO3 lines the knowledge covers
     * ``release-lines.json`` which core branches take a patch today
     * ``manuals.json`` the manuals docs.typo3.org publishes

Each hint names in its own ``domains`` field the domains a query reaches it
from. So the file says what the hint is about, and the tag says which query
reaches it.

The server reads all of it fresh on every request. So an edit to a file takes
effect at once, with no restart and no rebuild.

Which TYPO3 lines a statement holds for is data on the statement, not a version
number in its sentence: :doc:`versions`.

How a statement is worded
-------------------------

A reader acts on a statement and does not check it again. So the statement says
what the reader can check:

* **The condition, where somebody can measure one.** A reader meets a mechanism
  by its use, and a condition only by its result.

  "The focus indicator reaches the contrast WCAG sets against the surface it
  sits on" is a check. "Focus is visible with ``:focus-visible``" is true of a
  ring nobody can see. Where the mechanism is the fact, the statement keeps it
  and adds the condition it must meet.

* **Whether a list is the whole list.** A reader takes a list of stale idioms as
  every stale idiom and stops the check. "The four types below are the whole
  list" and "two of them are" tell the reader what is left to do.

* **The correction, where the statement names a defect.**
  `D-KNW-011 <../../decisions/knowledge/knw-011-a-rule-that-names-a-defect-names-its-correction.md>`_.

* **The strength of the claim and its source.**
  `D-KNW-043 <../../decisions/knowledge/knw-043-a-rule-carries-the-strength-of-its-claim-and-its-source.md>`_.

`D-KNW-175 <../../decisions/knowledge/knw-175-a-statement-names-the-condition-a-reader-can-check.md>`_
records the first two and the feedback they came from.

The corpus rests on two upstream sources. A reader who wants the original finds
them here:

* TYPO3 Core Contribution Guide:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/

* TYPO3 Core Commit Message Rules:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html
