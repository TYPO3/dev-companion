.. _typo3_commit_message_guide:

``typo3_commit_message_guide``
==============================

Draft and check a TYPO3 commit message. The message is read by a person who
wants to know what the commit did, so write it in plain English and only as long
as that answer needs: the diff carries the detail. Either assemble one from
parts (keyword plus summary) or pass an existing message to check and correct
it. The returned draft is ready to commit: the body is wrapped at 72 characters,
and the checks name every run of lines the wrapping joined and every line it
could not bring under the width. Defaults to a repository of your own, where the
subject and body conventions apply and no Forge issue, Releases: trailer or
changelog is demanded. The issues you pass are still written as Resolves: and
Related: trailers there — the same form a TYPO3 repository on GitHub links a
commit to what it closes by. Pass workflow="core" for a patch against the TYPO3
core, where the Forge issue and the Releases: trailer are required. The answer
names the branches for that trailer where the call carries none: the lines
taking a patch today, and the ones a change of this shape goes to. Answers from:
knowledge.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`knowledge <answer-sources-knowledge>`.

Takes
-----

.. code-block:: yaml

    # A complete existing commit message to check, subject and trailers included.
    # Unknown trailers such as Change-Id are kept, so an amended patch set stays
    # valid. The exception is workflow="core", which takes Co-Authored-By and an
    # agent's own session trailer off the draft and says so: a core commit message
    # carries neither. A core message without Signed-off-by is an error there —
    # the certificate is required, and the draft carries a placeholder because only
    # whoever commits can sign it.
    message: string  # optional
    # One of: core, project. Which rules to apply. "project", the default: any
    # repository of your own — the keyword, the 52/72 character limits and the
    # wrapping are checked, no trailer is demanded or invented, the issues you pass
    # are written out all the same, and [SECURITY] is allowed. "core": a patch
    # against the TYPO3 core, with the Forge issue and the Releases: trailer
    # required.
    workflow: string  # optional
    # One of: BUGFIX, FEATURE, TASK, DOCS, SECURITY. TYPO3 commit message keyword.
    # [SECURITY] is reserved for the TYPO3 Security Team and is only accepted with
    # workflow="project".
    keyword: string  # optional
    # Summary text without the TYPO3 keyword prefix. Say what the commit did, in
    # words a reader understands from the log alone.
    summary: string  # optional
    # The issue this commit resolves, with or without leading #: the Forge issue
    # number for a core patch, the number in your own tracker otherwise. It is
    # written as a Resolves: trailer in either workflow. Resolving more than one is
    # written out in a message and passed as message, which keeps every trailer it
    # carries.
    issue: string  # optional
    # Issues this commit relates to without resolving, read as issue is and written
    # as Related: trailers.
    relatedIssues: [string]  # optional
    # Target releases, for example main or 13.4. Left out, the draft carries a
    # RELEASE_TARGET placeholder and the checks name the lines taking a patch today
    # — the branches a change is released on are not guessed. Each one passed is
    # held against those lines: a branch out of regular support is an error, since
    # ELTS releases come from the ELTS partners rather than from a patch to that
    # branch.
    releases: [string]  # optional
    # Optional commit body, for what the diff does not say: why the change was made,
    # what it rests on. It is wrapped at 72 characters in the draft: indent a block
    # to keep the line breaks you wrote, and keep those lines under the width
    # yourself.
    body: string  # optional
    # Whether this is a breaking change requiring [!!!]. Left out, the checks say
    # the classification was assumed: it is a property of the diff, which this tool
    # never sees.
    isBreaking: boolean  # optional
    # Whether this is a deprecation. Left out, it is assumed the same way and the
    # checks say so.
    isDeprecation: boolean  # optional

Answers with
------------

.. code-block:: yaml

    # The commit message, ready to use.
    message: string
    checks:
      - # One of: error, warning, info.
        level: string
        # Stable identifier of the check, for example summary-too-long.
        code: string
        message: string
    # One of: core, project. Which rules the draft was written and checked against.
    # "core" adds the Forge issue and the Releases: trailer and demands them;
    # "project" applies the subject and body rules and writes only the trailers the
    # call carried.
    workflow: string
    # The workflow this commit is one step of, named as the call that answers it. A
    # message is the last act of a piece of work rather than the whole of it, and
    # what the work owes beside the message is not in this answer.
    nextTools:
      - tool: string
        # What to pass and why this call is the next one.
        when: string

Answered
--------

Derived by ``bin/cli tools:index``, and ``bin/cli tools:check`` holds it —
the same as everything above this heading. This tool reads nothing an
installation contains: what reaches its answer is the bundled knowledge and
which TYPO3 major the caller is on, so what comes back is written down rather
than recorded from one machine's checkout. Answered against the core checkout
this repository writes below .fixtures/, declaring TYPO3 14.3.0.

commit: from parts
~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "keyword": "BUGFIX",
        "summary": "Show hidden records in the import preview",
        "issue": "106123"
    }

Text:

.. code-block:: text

    Commit message draft:
    ```text
    [BUGFIX] Show hidden records in the import preview

    Resolves: #106123
    ```

    Checks:
    - INFO: No commit message readiness issues found by the local checks.

    Checked without the core workflow: keyword, 52/72 limits and wrapping apply, no Forge issue and no Releases: trailer are demanded, and the Resolves: and Related: lines carry the issues this call passed. workflow="core" for a patch against the TYPO3 core.

    typo3_task_guide — with the paths this commit touches. This commit is one step of work in your own repository, and the brief names the core conventions that transfer to it and the hints for those paths.

Data:

.. code-block:: json

    {
        "message": "[BUGFIX] Show hidden records in the import preview\n\nResolves: #106123",
        "checks": [
            {
                "level": "info",
                "code": "no-issues-found",
                "message": "No commit message readiness issues found by the local checks."
            }
        ],
        "workflow": "project",
        "nextTools": [
            {
                "tool": "typo3_task_guide",
                "when": "with the paths this commit touches. This commit is one step of work in your own repository, and the brief names the core conventions that transfer to it and the hints for those paths."
            }
        ]
    }

commit: from a message
~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "message": "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main"
    }

Text:

.. code-block:: text

    Commit message, corrected:
    ```text
    [TASK] Do a thing

    Body.

    Resolves: #1
    Releases: main
    ```

    Checks:
    - INFO: No commit message readiness issues found by the local checks.

    Checked without the core workflow: keyword, 52/72 limits and wrapping apply, no Forge issue and no Releases: trailer are demanded, and the Resolves: and Related: lines carry the issues this call passed. workflow="core" for a patch against the TYPO3 core.

    typo3_task_guide — with the paths this commit touches. This commit is one step of work in your own repository, and the brief names the core conventions that transfer to it and the hints for those paths.

Data:

.. code-block:: json

    {
        "message": "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main",
        "checks": [
            {
                "level": "info",
                "code": "no-issues-found",
                "message": "No commit message readiness issues found by the local checks."
            }
        ],
        "workflow": "project",
        "nextTools": [
            {
                "tool": "typo3_task_guide",
                "when": "with the paths this commit touches. This commit is one step of work in your own repository, and the brief names the core conventions that transfer to it and the hints for those paths."
            }
        ]
    }
