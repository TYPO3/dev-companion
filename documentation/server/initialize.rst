:navigation-title: Initialize

What a client gets at initialize
================================

A client launches this server as a subprocess and sends ``initialize`` before
anything else. The answer is the first text the model reads from this server.
For most sessions it is also the only text that arrives without a call. A client
that defers its tools sends the model their names alone,
`D-AUD-003 <../../decisions/audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md>`_.
This page shows that answer as the server sends it, and the three lists a client
fetches right after it.

The four members
----------------

* **``protocolVersion``** — the newest revision the stdio transport speaks. A
  client that offers a newer one gets this one back rather than a refusal, and
  ``StdioServerTest`` holds that.
* **``capabilities``** — what the SDK detects from what
  `Factory <../../src/Server/Factory.php>`_ registered: tools, resources with
  ``subscribe``, prompts, logging and completions. No list carries
  ``listChanged``, because nothing here changes a list while a session runs.
* **``serverInfo``** — the name and the version ``Factory`` declares.
* **``instructions``** — the statement
  `Coverage <../../src/Knowledge/Coverage.php>`_ assembles from
  ``knowledge/server-scope.json``. The four parts of it are the opening, the
  index of which tool answers what, the note under the index and the close.

What the instructions carry
---------------------------

The instructions open with the first call of any task, because the tool
descriptions are not a channel a deferred session reads. Then they route the
questions a session asks most, name the skill listing, and say that the server
answers in English. The whole of it fits the 2048 characters the recorded client
keeps,
`R-ANS-013 <../../requirements/answers/ans-013-the-instructions-fit-what-a-client-keeps.md>`_.
A client cuts past that without a word to either side, so the budget covers
everything assembled, prefix and suffix included.

Three things change the text from one start to the next:

* **A notice in front.** Where the task skills published into the project are
  stale, ``Installer`` puts them back and opens the instructions with one
  sentence that says so,
  `D-DIS-021 <../../decisions/discovery/dis-021-a-stale-publication-is-put-back-where-the-server-starts.md>`_.
* **The exclusion in front of the routing.** Where
  ``TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`` names tools, the instructions say which
  are left out and drop every index entry that routed to one,
  `R-SCO-009 <../../requirements/scope/sco-009-individual-tools-can-be-excluded.md>`_.
  Past what the budget holds, the count stands in for the names.
* **The write sentence at the end.** A standalone checkout offers
  ``typo3_feedback_record``, and the instructions say so in their last sentence.
  A project that installed the server as a dependency gets neither the tool nor
  the sentence. The page below shows the checkout's form.

What arrives after
------------------

* **``tools/list``** — every tool with its description, its two schemas and its
  annotations, which is the whole of :doc:`tools/ <tools/index>`. A client that
  follows the API's guidance defers a list this size. It then searches the list
  by name and description when the model names a subject,
  `D-AUD-019 <../../decisions/audience/aud-019-a-lookups-description-names-the-subjects-it-covers.md>`_.
* **``prompts/list``** — the prompts a user picks by name. ``debrief`` is there
  where the feedback channel is, and nowhere else,
  `D-FBK-048 <../../decisions/feedback/fbk-048-the-debrief-is-offered-as-a-prompt-where-the-channel-is.md>`_.
* **``resources/list``** and **``resources/templates/list``** — the surface a
  user picks from rather than the model calls, which
  :doc:`resources/ <resources/index>` describes whole.

What holds it
-------------

``bin/cli tools:index`` writes everything below the marker, from a session it
runs against ``Factory::create()`` in its own process. ``bin/cli tools:check``
and ``ToolSurfaceTest`` fail where the page says something else than the server
answers. ``StdioServerTest`` reads the same answer over stdio. So a count on
this page is what the server answered at the commit that wrote it.

.. What follows is written by ``bin/cli tools:index``.

The initialize result
---------------------

.. code-block:: json

    {
        "protocolVersion": "2025-11-25",
        "capabilities": {
            "logging": {},
            "completions": {},
            "prompts": {},
            "resources": {
                "subscribe": true
            },
            "tools": {}
        },
        "serverInfo": {
            "name": "typo3-dev-companion",
            "version": "0.3.0"
        }
    }

The fourth member is ``instructions``, and it reads:

.. code-block:: text

    Start every task with typo3_project_describe. It answers the installation's TYPO3 version, the extensions that are the project's own, the sites it configures, and the commands the repository declares. A check you recommend that the repository does not declare is a wrong answer however sensible it sounds. Then call typo3_task_guide for the workflow the task belongs to. Call it again at the first test, check, commit or shipped file the task did not name. Not every task ends in a patch. It also answers a triage of the backlog, whether a report still reproduces, and what a fix costs. What changed, which branch you are on, and whether a path still exists are yours to read in the checkout.

    What to call for what:
    - backend markup or a CSS class: typo3_component_lookup with the targetVersion
    - a backend icon identifier: typo3_icon_lookup
    - a label, added or reworded: typo3_label_lookup with the XLF resource the code at hand uses; a match elsewhere is not reusable
    - what a version broke, deprecated or added, on a major you have not built on lately: typo3_changelog_lookup
    - the commit message, yours as much as the core's, and its branches: typo3_commit_message_guide
    - the whole procedure, not one fact out of it: typo3_rule_lookup with a documentId typo3_project_describe lists

    Activate the typo3-* skill in your listing that covers the task: it makes these calls.

    targetVersion or the version the server read filters the answers, and a statement that does not hold on every covered line carries its range.

    Query this server in English whatever language you speak with the user. Its match is lexical, so a query in another language reaches only the loanwords. Translate the subject before you call, and the answer back.

    typo3_server_scope says what it covers, by which tool, and which installation it reads. Every tool here is read-only except typo3_feedback_record, which creates a new markdown feedback under feedback/ and writes nothing else.

1,959 characters of the 2,048 a client keeps, ``Coverage::INSTRUCTIONS_BUDGET``.

The prompts
-----------

- ``commit_message`` — Draft a TYPO3 commit message. Turn a summary into the
  checked commit-message draft already provided by typo3_commit_message_guide.
  Takes ``summary``, and ``keyword``, ``workflow`` and ``issue`` where given.
- ``debrief`` — Debrief the session that has just finished. Ask a finished
  session what this server did for it and what it lacked, and have it record
  what it found with typo3_feedback_record. Run it after the work, in a message
  of its own. Takes nothing.

The tool list
-------------

``tools/list`` carries 31 tools. Each entry is the ``name``, ``description``,
``inputSchema``, ``outputSchema`` and ``annotations`` the tool's own page under
:doc:`tools/ <tools/index>` states. On the wire the entries weigh 309,554 bytes:
32,027 of description, 47,522 of ``inputSchema`` and 224,361 of
``outputSchema``.

The resource template
---------------------

- ``typo3://skill/{skill}/references/{reference}`` — What a TYPO3 task workflow
  hands over at a step. What a skill under typo3://skill/ links to: the order
  every task starts in, its checklist, and the implementation guide for the
  layer it settled on. Read the one its body sends you to, at the step that
  sends you.
