Glossary
========

This page defines the words this repository uses in a sense of its own. Each
word has one meaning, and the page gives it in one entry.

.. facts::

    :answer: What became of a feedback: the change, and the commit that made it.
    :archive: To move a feedback to ``feedback/archive/`` once it has an answer.
    :cache: What ``Http\Cache`` keeps of an answer from a host outside this
        machine, per process. Each source states its *cache duration* in
        seconds. The word for it is cache, never hold.
    :catalog: The component catalog in ``knowledge/catalog/``.
    :checkout: One TYPO3 core worktree below ``.checkouts/``, this repository's
        own. ``bin/cli checkouts:update`` creates it.
    :contract case: A targeted scenario in ``scenarios/contracts/``, which names
        one task shape.
    :decision: What a change relied on, and what would show it wrong: one file
        in ``decisions/``.
    :a feedback: One report about this server, from a session that works
        somewhere else. Countable here: one report, one file, one subject.
    :the feedback channel: ``feedback/``, where feedback arrives and waits.
        ``typo3_feedback_record`` writes into it, ``bin/cli feedback:list``
        lists it, and ``bin/cli feedback:archive`` moves a feedback into
        ``feedback/archive/``.
    :forward review: An open scenario in ``scenarios/forward/`` that names no
        subsystem, skill or expected finding.
    :hint: One statement in ``knowledge/hints/``. ``bin/cli hints:probe``
        reaches it.
    :hold: Two senses, and the preposition tells them apart. A statement *holds
        on* a version or *for* a scope: it is true there. A test *holds* a rule
        and the rule is *held by* it: the test fails when the rule breaks.
        Beside "contains", every other sense takes a plain word. A person is
        *assigned* an issue, one thing is *compared with* another, a cache
        *keeps* an answer, and a caller *has* a value.
    :installation: The TYPO3 a caller works in. The server reads the facts of
        the installation from it and bundles none of them.
    :judge: To work out what should become of one open feedback, on evidence.
    :knowledge: Everything below ``knowledge/``: what the tools answer from.
    :record: The verb a feedback arrives by, and one of the six tool verbs.
    :requirement: What must be true from now on, and what holds it there: one
        file in ``requirements/``.
    :result: What a tool returns: the text and the same answer as data.
        ``src/Result/`` holds what several tools build one from — the shared
        schemas, the renderers, the unanswered case.
    :run: One recorded execution of a forward review, in ``scenarios/runs/``.
    :scenario: One prompt and what has to come out of it, in ``scenarios/``.
    :skill: One canonical task workflow under ``skills/``, installed into
        somebody else's project.
    :standalone checkout: This repository as the Composer root package, where a
        session can record feedback.
    :todo: One entry in the order of the work.
    :tool: One ``typo3_<subject>_<verb>`` this server offers its callers. It is
        the MCP primitive of that name and one class in ``src/Tool/``.
        ``TYPO3\DevCompanion\Tool\Registry`` lists it.
    :tool verb: One of ``lookup``, ``guide``, ``list``, ``scope``, ``describe``,
        ``record``. It tells a caller the shape of the answer.
    :verdict: How a recorded run came out: ``covered``, ``partial``, ``gap``.