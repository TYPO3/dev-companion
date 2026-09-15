Glossary
========

* **answer** — what became of a feedback: the change, and the commit that made
  it.
* **archive** — to move a feedback to ``feedback/archive/`` once it has an
  answer.
* **catalog** — the component catalog in ``knowledge/catalog/``.
* **checkout** — one TYPO3 core worktree below ``.checkouts/``, this
  repository's own, created by ``bin/cli checkouts:update``.
* **contract case** — a targeted scenario in ``scenarios/contracts/``, which
  names one task shape.
* **decision** — what a change rested on, and what would show it wrong: one file
  in ``decisions/``.
* **a feedback** — one report about this server, written by a session working
  somewhere else. Countable here: one report, one file, one subject.
* **the feedback channel** — ``feedback/``, where feedback arrives and waits.
  Written by ``typo3_feedback_record``, read by ``bin/cli feedback``.
* **forward review** — an open scenario in ``scenarios/forward/`` that names no
  subsystem, skill or expected finding.
* **hint** — one statement in ``knowledge/hints/``, reached by
  ``bin/cli hints:probe``.
* **hold** — two senses, told apart by the preposition. A statement *holds on* a
  version or *for* a scope: it is true there. A test *holds* a rule and the rule
  is *held by* it: the test fails when the rule breaks.
* **installation** — the TYPO3 a caller works in. The server reads its own facts
  from it rather than bundles them.
* **judge** — to work out what should become of one open feedback, on evidence.
* **knowledge** — everything below ``knowledge/``: what the tools answer from.
* **record** — the verb a feedback arrives by, and one of the six tool verbs.
* **requirement** — what must be true from now on, and what holds it there: one
  file in ``requirements/``.
* **result** — what a tool hands back: the text and the same answer as data.
  ``src/Result/`` holds what several tools build one from — the shared schemas,
  the renderers, the unanswered case.
* **run** — one recorded execution of a forward review, in ``scenarios/runs/``.
* **scenario** — one prompt and what has to come out of it, in ``scenarios/``.
* **skill** — one canonical task workflow under ``skills/``, installed into
  somebody else's project.
* **standalone checkout** — this repository as the Composer root package, where
  a session can record feedback.
* **todo** — one entry in the order of the work.
* **tool** — one ``typo3_<subject>_<verb>`` this server offers its callers: the
  MCP primitive of that name, one class in ``src/Tool/``, listed in
  ``TYPO3\DevCompanion\Tools``.
* **tool verb** — one of ``lookup``, ``guide``, ``list``, ``scope``,
  ``describe``, ``record``: what tells a caller the shape of the answer.
* **verdict** — how a recorded run came out: ``covered``, ``partial``, ``gap``.
