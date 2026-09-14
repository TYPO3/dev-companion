.. _typo3_reference_list:

``typo3_reference_list``
========================

List the worked examples the TYPO3 core ships of its own conventions, and what
each one is a reference for. That is the theme extension, the styleguide, the
Extbase fixture extension, the content element render, the browser test suite,
the static analysis setup. Read one of these before you invent a layout or a
test harness. They are the version-correct form of what a convention describes,
and they pass today. Every hint here is a summary of one. Paths are relative to
a core checkout; where the answer names a Composer package, an installation that
has it holds the same files below vendor/. Answers from: knowledge.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`knowledge <answer-sources-knowledge>`.

Takes
-----

.. code-block:: yaml

    # The TYPO3 version to list for, for example "13.4" or "14". The answer leaves
    # out an example that branch does not have rather than qualifies it. Defaults to
    # the version of the installation this server started in. Where there is none,
    # every entry comes back with the range it exists on.
    targetVersion: string  # optional

Answers with
------------

.. code-block:: yaml

    # The TYPO3 major the list is for, stated by the caller or read from the
    # installation. Null means every covered version is in it and each entry carries
    # its own range.
    targetVersion: integer or null
    # How many worked examples exist on the version asked about.
    matchCount: integer
    references:
      - # Stable identifier of the example.
        id: string
        # Where it is, relative to the root of a core checkout.
        path: string
        # The Composer package that ships it, so an installation can read it below
        # vendor/. Null means it exists only in the core repository, as everything
        # below Build/ does.
        package: string or null
        # What it is a worked example of.
        reference: string
        # What not to conclude from it: that you read it rather than depend on it,
        # or which part of it is the core's own. Null where there is nothing to warn
        # about.
        caveat: string or null
        # The hint whose conventions it demonstrates, for typo3_hint_lookup. Null
        # where no hint covers the subject yet, which is exactly when a read of the
        # example is worth most.
        hint: string or null
        # First covered major that has it. Null means every covered major does.
        since: integer or null
        # Last covered major that has it. Null means the newest one still does.
        until: integer or null
        # The range in words, empty when every covered version has it.
        existsOn: string
    # The TYPO3 majors this answer derives from.
    coveredVersions: [integer]

Answered
--------

Derived by ``bin/cli tools:index``, and ``bin/cli tools:check`` holds it —
the same as everything above this heading. This tool reads nothing an
installation contains: what reaches its answer is the bundled knowledge and
which TYPO3 major the caller is on, so what comes back is written down rather
than recorded from one machine's checkout. Answered against the core checkout
this repository writes below .fixtures/, declaring TYPO3 14.3.0.

references
~~~~~~~~~~

Called with:

.. code-block:: json

    {}

Text:

.. code-block:: text

    Worked examples in the TYPO3 core, as TYPO3 v14 has them.
    Paths are relative to a core checkout. Where none is at hand, they are also the paths in github.com/TYPO3/typo3 on the matching branch.

    - typo3/sysext/theme_camino — TYPO3 v14 and newer
      A sitepackage worked out in full: one template root with Pages/, Content/ and ContentPreviews/, a site set carrying its TypoScript, page TSconfig, settings and labels, backend layouts, and its content elements registered as record types of tt_content.
      Read it, do not depend on it: it is the theme of one release line and is announced to move out of the core into a repository of its own.
      In an installation: vendor/typo3/theme-camino/, below the same path with the typo3/sysext/<key>/ prefix removed.
      Conventions: typo3_hint_lookup id="sitepackage-layout"
    - typo3/sysext/styleguide — TYPO3 v13 and newer
      Every backend component as rendered markup and every TCA field type as a record you can open: the demo pages the component catalog names, and a TCA corpus that answers what a column configuration looks like when it works.
      In an installation: vendor/typo3/cms-styleguide/, below the same path with the typo3/sysext/<key>/ prefix removed.
      Conventions: typo3_hint_lookup id="css-styleguide-demos"
    - typo3/sysext/extbase/Tests/Functional/Fixtures/Extensions/blog_example
      An Extbase extension the core keeps green: domain models with their TCA, repositories, controllers and validators — and, in the functional tests around it, how a repository is exercised at all, request and configuration manager included.
      In an installation: vendor/typo3/cms-extbase/, below the same path with the typo3/sysext/<key>/ prefix removed.
      Conventions: typo3_hint_lookup id="extbase"
    - typo3/sysext/fluid_styled_content
      The content elements every installation has: how a record type is registered, the TypoScript that renders it on lib.contentElement, the templates a sitepackage overrides, and the backend previews beside them.
      In an installation: vendor/typo3/cms-fluid-styled-content/, below the same path with the typo3/sysext/<key>/ prefix removed.
      Conventions: typo3_hint_lookup id="content-elements"
    - Build/tests/playwright/e2e — TYPO3 v14 and newer
      A browser suite that runs: specs one directory per module here, the page objects they compose themselves from in ../fixtures/, the login setup every project depends on in ../helper/, and the accessibility scan among them. Build/playwright.config.ts is where the projects and their dependencies are declared.
      Only in the core repository — no Composer package ships it.
      Conventions: typo3_hint_lookup id="browser-tests"
    - Build/phpstan
      The PHPStan setup of a large TYPO3 codebase: the configuration a project copies, the baseline that makes adopting it possible at all, and the custom rules in src/.
      Only in the core repository — no Composer package ships it.
    - Build/php-cs-fixer
      The PHP-CS-Fixer configuration the core's own CGL check runs, with the file header the core requires as a rule of its own.
      The header comment is the core's licence block; a project keeps the rule set and replaces that.
      Only in the core repository — no Composer package ships it.

Data:

.. code-block:: json

    {
        "targetVersion": 14,
        "matchCount": 7,
        "references": [
            {
                "id": "sitepackage",
                "path": "typo3/sysext/theme_camino",
                "package": "typo3/theme-camino",
                "reference": "A sitepackage worked out in full: one template root with Pages/, Content/ and ContentPreviews/, a site set carrying its TypoScript, page TSconfig, settings and labels, backend layouts, and its content elements registered as record types of tt_content.",
                "caveat": "Read it, do not depend on it: it is the theme of one release line and is announced to move out of the core into a repository of its own.",
                "hint": "sitepackage-layout",
                "since": 14,
                "until": null,
                "existsOn": "TYPO3 v14 and newer"
            },
            {
                "id": "backend-components",
                "path": "typo3/sysext/styleguide",
                "package": "typo3/cms-styleguide",
                "reference": "Every backend component as rendered markup and every TCA field type as a record you can open: the demo pages the component catalog names, and a TCA corpus that answers what a column configuration looks like when it works.",
                "caveat": null,
                "hint": "css-styleguide-demos",
                "since": 13,
                "until": null,
                "existsOn": "TYPO3 v13 and newer"
            },
            {
                "id": "extbase-plugin",
                "path": "typo3/sysext/extbase/Tests/Functional/Fixtures/Extensions/blog_example",
                "package": "typo3/cms-extbase",
                "reference": "An Extbase extension the core keeps green: domain models with their TCA, repositories, controllers and validators — and, in the functional tests around it, how a repository is exercised at all, request and configuration manager included.",
                "caveat": null,
                "hint": "extbase",
                "since": null,
                "until": null,
                "existsOn": ""
            },
            {
                "id": "content-elements",
                "path": "typo3/sysext/fluid_styled_content",
                "package": "typo3/cms-fluid-styled-content",
                "reference": "The content elements every installation has: how a record type is registered, the TypoScript that renders it on lib.contentElement, the templates a sitepackage overrides, and the backend previews beside them.",
                "caveat": null,
                "hint": "content-elements",
                "since": null,
                "until": null,
                "existsOn": ""
            },
            {
                "id": "browser-tests",
                "path": "Build/tests/playwright/e2e",
                "package": null,
                "reference": "A browser suite that runs: specs one directory per module here, the page objects they compose themselves from in ../fixtures/, the login setup every project depends on in ../helper/, and the accessibility scan among them. Build/playwright.config.ts is where the projects and their dependencies are declared.",
                "caveat": null,
                "hint": "browser-tests",
                "since": 14,
                "until": null,
                "existsOn": "TYPO3 v14 and newer"
            },
            {
                "id": "static-analysis",
                "path": "Build/phpstan",
                "package": null,
                "reference": "The PHPStan setup of a large TYPO3 codebase: the configuration a project copies, the baseline that makes adopting it possible at all, and the custom rules in src/.",
                "caveat": null,
                "hint": null,
                "since": null,
                "until": null,
                "existsOn": ""
            },
            {
                "id": "coding-standards",
                "path": "Build/php-cs-fixer",
                "package": null,
                "reference": "The PHP-CS-Fixer configuration the core's own CGL check runs, with the file header the core requires as a rule of its own.",
                "caveat": "The header comment is the core's licence block; a project keeps the rule set and replaces that.",
                "hint": null,
                "since": null,
                "until": null,
                "existsOn": ""
            }
        ],
        "coveredVersions": [
            12,
            13,
            14,
            15
        ]
    }
