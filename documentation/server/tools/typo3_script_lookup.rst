.. _typo3_script_lookup:

``typo3_script_lookup``
=======================

Find notes for TYPO3 core scripts and commands: how Build/Scripts/runTests.sh is
started and what it needs first, what an argument after -- reaches and which
options one run takes, the commands per subject, and what the pre-commit hook
does to a commit. They are the core checkout's own: a query that reads as a
project or third-party extension is answered with the boundary instead of with
commands that do not exist there. Which suite a change actually needs, and what
one of them does when it runs — what it provisions, what it passes through,
which environment variables change it — is typo3_test_run_guide, which filters
the suites by version. Answers from: knowledge.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`knowledge <answer-sources-knowledge>`.

Takes
-----

.. code-block:: yaml

    # The TYPO3 core task, in English, for example unit tests, functional tests,
    # CGL, npm, or dependency install.
    task: string
    # The TYPO3 version the answer has to hold on, for example "13.4" or "14". A
    # section bound to another major is left out. Defaults to every major this
    # repository declares typo3/cms-core for, or to the installation this server was
    # started in; where there is neither, every section comes back with the range it
    # holds for.
    targetVersion: string  # optional

Answers with
------------

.. code-block:: yaml

    query: string
    # The exact XLF resource the lookup restricted the result to. Null means the
    # caller gave no usage context.
    resource: string or null  # optional
    matchCount: integer
    matches:
      - documentId: string
        # Title of the knowledge document.
        title: string
        # The typo3://guides resource that holds the full document.
        uri: string
        # Heading of the matched section.
        heading: string
        # The section as written, formatting included.
        body: string
        # The TYPO3 majors this section holds for, in words. Empty means every
        # covered major, which is what a section that declares nothing says.
        versions: string  # optional
        # Share of the query terms the section covers, 0 to 1. Zero where no search
        # ranked this record, which is a page the caller named by documentId.
        coverage: number
        # Weighted match score; headings weigh more than body text. Zero where no
        # search ranked this record.
        score: integer
        # Whether the lookup cut the body. Read the resource for the rest.
        truncated: boolean
    # Documents in the knowledge base with the topics they cover. The lookup returns
    # them when nothing matched.
    documents:  # optional
      - id: string
        title: string
        topics: [string]
    # Documents outside the searched ones that do match the query.
    elsewhere: [string]  # optional
    # Hints that match the same query. They are a second corpus, which
    # typo3_hint_lookup searches, and it takes one of these ids.
    alsoInHints:  # optional
      - id: string
        title: string
    # One of: core, uncertain, project, extension. Which kind of work this answer is
    # for. core: a patch to the TYPO3 core itself. project: the site repository
    # around an installation. extension: a package in it, a sitepackage or a
    # third-party one. uncertain: nothing in the call placed the work, and the
    # answer is the core's own.
    scope: string

Answered
--------

Derived by ``bin/cli tools:index``, and ``bin/cli tools:check`` holds it —
the same as everything above this heading. This tool reads nothing an
installation contains: what reaches its answer is the bundled knowledge and
which TYPO3 major the caller is on, so what comes back is written down rather
than recorded from one machine's checkout. Answered against the core checkout
this repository writes below .fixtures/, declaring TYPO3 14.3.0.

scripts: hit
~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "task": "functional tests"
    }

Text:

.. code-block:: text

    A section carries the range it holds for where it has one. What is bound elsewhere: call typo3_hint_lookup with targetVersion for a convention, and typo3_test_run_guide with targetVersion for a runTests.sh command.

    ## Invoking runTests.sh
    Source: TYPO3 Core Script Help (typo3://guides/core/testing/scripts) — matches 100% of the query terms

    `Build/Scripts/runTests.sh` runs every suite inside a container. You start it
    from the core checkout root.

    - Prefix a scripted or non-interactive run with `CI=true`. It drops the
      interactive container flags, skips the SIGINT trap, and picks the CI phpstan
      configuration. Without a TTY the script removes the interactive flags on its
      own, but `CI=true` is the explicit form.
    - The script passes everything after `--` through unchanged. It goes to phpunit
      for the test suites, to npm for `-s npm`, to composer for `-s composer`.
    - Run one file or one method while you iterate. A full suite costs minutes per
      round.
    - `./Build/Scripts/runTests.sh -h` lists the suites and option values the
      checked-out branch supports.

    ```bash
    # one unit test file
    CI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php

    # one unit test method
    CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php

    # one functional test file, sqlite (default)
    CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/impexp/Tests/Functional/Export/ExportTest.php
    ```

    Frequently needed options:

    - `-d sqlite|mariadb|mysql|postgres` selects the database for `-s functional`
      and for whichever installer suite the branch carries. sqlite is the default
      and the fastest.
    - `-a mysqli|pdo_mysql` selects the driver for mysql and mariadb.
    - `-i <version>` pins a database version, for example `-d mariadb -i 11.4`.
    - `-p <php minor>` selects the PHP version of the container.
    - `-n` turns the `cgl` suites into a dry run that only reports. The same holds
      for any other suite the branch lists under it.
    - `-c <chunk>/<total>` splits `-s functional`, and the browser suite where the
      branch has one, into chunks.
    - `-x` (with optional `-y <port>`) enables xdebug towards a listening IDE.
    - `-b docker|podman` selects the container runtime. podman is the default.

    ## Common Commands
    Source: TYPO3 Core Script Help (typo3://guides/core/testing/scripts) — matches 62% of the query terms

    ### Install Dependencies

    ```bash
    CI=true ./Build/Scripts/runTests.sh -s composerInstall
    ```

    A suite runs against the `vendor/` and `bin/` of the directory you start it
    from. `runTests.sh` mounts that directory and nothing else. A fresh clone has
    neither, and neither does a git worktree of a checkout that has them. Git
    ignores `/vendor/*` and `/bin/*`, so it never brings them.

    The first suite there stops at `exec: line 9: bin/phpunit: not found`. That
    names phpunit rather than the directory, so you cannot read the cause from the
    symptom. Run the install once in that directory first.

    A symlink of `vendor/` and `bin/` from another checkout does not stand in for
    it. The target sits outside the one mount and does not resolve inside the
    container. That holds whether the link is absolute or relative.

    `composer install` on the host installs the same dependencies, but it wants the
    PHP the branch requires. The container form is why `runTests.sh` exists. Either
    way this is a precondition and not a step. A checkout that already has `vendor/`
    needs it again only after `composer.json` or `composer.lock` changed.

    Each of those two changes has a symptom of its own, and neither names the
    install. The section *When a Suite Fails for the Install Rather Than the Code*
    has them.

    ### Run PHP Unit Tests

    ```bash
    CI=true ./Build/Scripts/runTests.sh -s unit
    ```

    Runs the TYPO3 core unit test suite. Add a path or `--filter` after `--` when
    you work on a narrow area.

    ### Run Functional Tests

    ```bash
    CI=true ./Build/Scripts/runTests.sh -s functional
    ```

    Runs functional tests. Use these for changes that touch TYPO3 services,
    persistence, configuration, or integrations. Add `-d mariadb` or `-d postgres`
    to reproduce DBMS-specific behaviour.

    ### Run Coding Standards

    ```bash
    CI=true ./Build/Scripts/runTests.sh -s cgl -n
    ```

    Checks the coding guidelines for all core PHP files and reports without a change
    to them. Drop `-n` to have the suite fix them. `-s cglGit` runs
    `Build/Scripts/cglFixMyCommit.sh` over the latest commit alone and is quicker,
    but only from a normal checkout.

    That script asks git for its file list inside the container. A git worktree
    keeps its gitdir outside the mounted directory, so git fails and the list comes
    back empty. The suite then reports SUCCESS after it read no file. `-s cgl` asks
    git nothing and works from either.

    (section truncated — read core/testing/scripts whole for the rest)

    Each excerpt above is one section of a longer document, and each page below carries the `##` headings that are not above. Where the task is the whole procedure rather than the fact you searched for, read the page — typo3_rule_lookup with documentId, which needs no resource list:
    - core/testing/scripts — TYPO3 Core Script Help: 3 of its 5 headings are not above — When a Suite Fails for the Install Rather Than the Code, The Pre-Commit Hook, Script Notes.

    These commands run in a TYPO3 core checkout. In any other repository, what to run is declared in its own composer.json, package.json and CI configuration.

Data:

.. code-block:: json

    {
        "query": "functional tests",
        "matchCount": 2,
        "matches": [
            {
                "documentId": "core/testing/scripts",
                "title": "TYPO3 Core Script Help",
                "uri": "typo3://guides/core/testing/scripts",
                "heading": "Invoking runTests.sh",
                "body": "`Build/Scripts/runTests.sh` runs every suite inside a container. You start it\nfrom the core checkout root.\n\n- Prefix a scripted or non-interactive run with `CI=true`. It drops the\n  interactive container flags, skips the SIGINT trap, and picks the CI phpstan\n  configuration. Without a TTY the script removes the interactive flags on its\n  own, but `CI=true` is the explicit form.\n- The script passes everything after `--` through unchanged. It goes to phpunit\n  for the test suites, to npm for `-s npm`, to composer for `-s composer`.\n- Run one file or one method while you iterate. A full suite costs minutes per\n  round.\n- `./Build/Scripts/runTests.sh -h` lists the suites and option values the\n  checked-out branch supports.\n\n```bash\n# one unit test file\nCI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php\n\n# one unit test method\nCI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php\n\n# one functional test file, sqlite (default)\nCI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/impexp/Tests/Functional/Export/ExportTest.php\n```\n\nFrequently needed options:\n\n- `-d sqlite|mariadb|mysql|postgres` selects the database for `-s functional`\n  and for whichever installer suite the branch carries. sqlite is the default\n  and the fastest.\n- `-a mysqli|pdo_mysql` selects the driver for mysql and mariadb.\n- `-i <version>` pins a database version, for example `-d mariadb -i 11.4`.\n- `-p <php minor>` selects the PHP version of the container.\n- `-n` turns the `cgl` suites into a dry run that only reports. The same holds\n  for any other suite the branch lists under it.\n- `-c <chunk>/<total>` splits `-s functional`, and the browser suite where the\n  branch has one, into chunks.\n- `-x` (with optional `-y <port>`) enables xdebug towards a listening IDE.\n- `-b docker|podman` selects the container runtime. podman is the default.",
                "versions": "",
                "coverage": 1,
                "score": 18,
                "truncated": false
            },
            {
                "documentId": "core/testing/scripts",
                "title": "TYPO3 Core Script Help",
                "uri": "typo3://guides/core/testing/scripts",
                "heading": "Common Commands",
                "body": "### Install Dependencies\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s composerInstall\n```\n\nA suite runs against the `vendor/` and `bin/` of the directory you start it\nfrom. `runTests.sh` mounts that directory and nothing else. A fresh clone has\nneither, and neither does a git worktree of a checkout that has them. Git\nignores `/vendor/*` and `/bin/*`, so it never brings them.\n\nThe first suite there stops at `exec: line 9: bin/phpunit: not found`. That\nnames phpunit rather than the directory, so you cannot read the cause from the\nsymptom. Run the install once in that directory first.\n\nA symlink of `vendor/` and `bin/` from another checkout does not stand in for\nit. The target sits outside the one mount and does not resolve inside the\ncontainer. That holds whether the link is absolute or relative.\n\n`composer install` on the host installs the same dependencies, but it wants the\nPHP the branch requires. The container form is why `runTests.sh` exists. Either\nway this is a precondition and not a step. A checkout that already has `vendor/`\nneeds it again only after `composer.json` or `composer.lock` changed.\n\nEach of those two changes has a symptom of its own, and neither names the\ninstall. The section *When a Suite Fails for the Install Rather Than the Code*\nhas them.\n\n### Run PHP Unit Tests\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s unit\n```\n\nRuns the TYPO3 core unit test suite. Add a path or `--filter` after `--` when\nyou work on a narrow area.\n\n### Run Functional Tests\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s functional\n```\n\nRuns functional tests. Use these for changes that touch TYPO3 services,\npersistence, configuration, or integrations. Add `-d mariadb` or `-d postgres`\nto reproduce DBMS-specific behaviour.\n\n### Run Coding Standards\n\n```bash\nCI=true ./Build/Scripts/runTests.sh -s cgl -n\n```\n\nChecks the coding guidelines for all core PHP files and reports without a change\nto them. Drop `-n` to have the suite fix them. `-s cglGit` runs\n`Build/Scripts/cglFixMyCommit.sh` over the latest commit alone and is quicker,\nbut only from a normal checkout.\n\nThat script asks git for its file list inside the container. A git worktree\nkeeps its gitdir outside the mounted directory, so git fails and the list comes\nback empty. The suite then reports SUCCESS after it read no file. `-s cgl` asks\ngit nothing and works from either.",
                "versions": "",
                "coverage": 0.624,
                "score": 11,
                "truncated": true
            }
        ],
        "scope": "core"
    }

scripts: miss
~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "task": "quantum entanglement pineapple"
    }

Text:

.. code-block:: text

    No section of the TYPO3 core script notes matched "quantum entanglement pineapple". They cover: Invoking runTests.sh, Common Commands, When a Suite Fails for the Install Rather Than the Code, The Pre-Commit Hook, Script Notes.

Data:

.. code-block:: json

    {
        "query": "quantum entanglement pineapple",
        "matchCount": 0,
        "matches": [],
        "elsewhere": [],
        "scope": "core"
    }
