:navigation-title: Knowledge base

The bundled knowledge
=====================

Everything the tools and resources answer from ships in the package, in
`knowledge/ <../../knowledge/>`_. It needs nothing up: no installation, no
network, no database. What the server reads from an installation instead, and
why, is :doc:`asking the installation <asking-the-installation>`.

* ``documents/`` — the prose corpus ``typo3://guides/{documentId}`` serves and
  ``typo3_rule_lookup`` searches: ``core/contribution/rules.md``,
  ``core/testing/scripts.md``, ``core/contribution/commit-messages.md``,
  ``core/contribution/gerrit-workflow.md``, ``core/contribution/sources.md``
* ``hints/`` — one file per subject: ``datahandler.json``, ``fal.json``,
  ``labels.json``, ``site-sets.json`` and many more. Each entry names the
  domains a query asks it from in its own ``domains`` field. So the file says
  what the hint is about and the tag says which query reaches it
* ``catalog/`` — the component catalog (``component/entries.json``,
  ``component-checklist.json``, ``references.json``, ``meta.json``) and the
  shipped system extensions (``system-extensions.json``)
* ``test-suite-hints.json``, ``task-intents.json``, ``icon-concepts.json``,
  ``server-scope.json``, ``versions.json``

The server reads all of it fresh on every request, so an edit to a file takes
effect at once. No restart and no rebuild. Which TYPO3 lines a statement holds
for is data on the statement rather than a version number in its sentence:
:doc:`versions`.

How a statement is worded
-------------------------

A reader acts on a statement and does not check it again. So the statement says
what the reader can check:

* **The condition, where one can be measured.** A reader meets a mechanism by
  its use and a condition only by its result. "The focus indicator reaches the
  contrast WCAG sets against the surface it sits on" is a check. "Focus is
  visible with ``:focus-visible``" is met by a ring nobody can see. Where the
  mechanism is the fact, the statement keeps it and adds the condition it has to
  meet.
* **Whether a list is the whole list.** A reader takes a list of stale idioms as
  every stale idiom and stops the check. "The four types below are the whole
  list" and "two of them are" tell the reader what is left to do.
* **The correction, where the statement names a defect.**
  `D-KNW-011 <../../decisions/knowledge/knw-011-a-rule-that-names-a-defect-names-its-correction.md>`_.
* **The strength of the claim and its source.**
  `D-KNW-043 <../../decisions/knowledge/knw-043-a-rule-carries-the-strength-of-its-claim-and-its-source.md>`_.

`D-KNW-175 <../../decisions/knowledge/knw-175-a-statement-names-the-condition-a-reader-can-check.md>`_
records the first two and the feedback they came from.

Two upstream sources this corpus stands against, for a reader who wants the
original:

* TYPO3 Core Contribution Guide:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/
* TYPO3 Core Commit Message Rules:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html
