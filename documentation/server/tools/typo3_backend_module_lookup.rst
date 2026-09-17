.. _typo3_backend_module_lookup:

``typo3_backend_module_lookup``
===============================

*List the backend modules*

List the backend modules registered in the TYPO3 installation you work in. Each
comes with the extension that declares it, its place in the module tree, its
labels and its access level. Each comes with the route it answers on and every
sub-route it registers. It carries the navigation component as the module tree
resolves it, which is the value a Configuration/Backend/Modules.php cannot give
you. A module inherits it from its parent, so a read of the registration files
says a module has no page tree when it has one. A project extension's modules
are in it, because the tool boots and asks the installation rather than reads a
snapshot. Answers from: installation.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`.

Takes
-----

.. code-block:: yaml

    # Module identifier, label, route, navigation component, or extension name to
    # filter by. Omit to list every module.
    query: string  # optional
    # A Configuration/Backend/Modules.php to check instead of listing the registry:
    # which parent, which iconIdentifier and which labels it names that this
    # installation does not have. Answered without a cache flush and without a save
    # of the file into an installation. The registry needs both before it can say
    # anything. The tool reads the file as text and executes nothing in it. So it
    # reports a value a variable or a constant computes as unresolvable rather than
    # follows it. Not with query.
    file: string  # optional

Answers with
------------

.. code-block:: yaml

    query: string
    matchCount: integer  # optional
    # One of: installation. installation: its assembled runtime state answered.
    answeredBy: string  # optional
    # One entry per module the named file declares, in the order it declares them.
    # Empty where the call named no file.
    checked:  # optional
      - # The module the file declares, as its key spells it.
        identifier: string
        # The parent it names. Empty where it names none, which makes it a
        # first-level module and registers no route unless it also declares
        # standalone.
        parent: string
        # Whether this installation has a module under that identifier. Null where
        # the entry names no parent, and null where the value was not a plain string
        # in the file.
        parentRegistered: boolean or null
        # The icon it names. Empty where it names none.
        iconIdentifier: string
        # Whether the registry here holds that identifier. Null where the entry
        # names no icon, and null where the value is not a plain string.
        iconRegistered: boolean or null
        # The translation domain or LLL reference it names, as written. Empty where
        # it names none, and empty where the value was not a plain string.
        labels: string
        # The XLF file this installation has behind that reference. Empty where none
        # does, which for a domain means no label file here derives it.
        labelsResource: string
        # Whether the trans-unit the module title comes from is in that file:
        # mlang_tabs_tab behind an LLL reference, title behind a domain. False with
        # no labelsResource means nothing here resolves the reference at all. False
        # with one means the file is here and the unit is not. Either way the module
        # renders an empty title. Null where the entry names no labels, and null
        # where the value is not a plain string.
        labelsRegistered: boolean or null
    modules:  # optional
      - identifier: string
        # The modules it sits under, outermost first.
        parents: [string]
        # The package that declares it.
        extension: string
        # Its label, with the translation domain reference behind it.
        labels: string  # optional
        # The backend route it answers on.
        path: string
        # Its declared before/after position, if any.
        position: string  # optional
        # The navigation component as resolved, inheritance included —
        # "@typo3/backend/tree/page-tree-element" is the page tree. Empty where the
        # module has none. The value differs between TYPO3 versions, which is why it
        # comes from the installation.
        navigationComponent: string
        # Who may call it: "user", "admin", "systemMaintainer".
        access: string  # optional
        # Every route the module registers. Empty for a first-level module that is
        # not standalone, which registers none.
        routes:
          - # The name the registration gives it; "_default" is what the module
            # opens with.
            name: string
            # The route identifier the registry holds it under: the module
            # identifier for "_default", "<module>.<name>" for every other one.
            identifier: string
            path: string
            # Controller::method it dispatches to.
            target: string
    unsupported:  # optional
      # One of: no-installation, misconfigured, installation-not-answering.
      # no-installation: nothing to ask from here, and searched says where the
      # discovery looked. misconfigured: the caller named an installation the server
      # could not use, so the discovery searched nothing.
      # installation-not-answering: the discovery found one and its console did not
      # answer. A stopped container or a database with no schema is that state, and
      # it ends without a reinstall.
      cause: string
      # What stopped it, in the words the attempt produced.
      reason: string
      # One of: installed, not-installed, undeclared. The state of the repository
      # the caller stands in, which the cause does not say. installed: packages sit
      # below the root the discovery found, so the caller lacks no install.
      # not-installed: the repository declares TYPO3 and has no packages below it
      # yet, so this call answers once composer install has run. undeclared: nothing
      # in the directories the discovery walked declares TYPO3, so an install here
      # answers nothing. Null where the discovery looked at nothing: a named root
      # the server could not use, or an entrypoint that handed no directory in.
      repositoryState: string or null  # optional
      # What the reason means where the message alone does not say it. A console
      # that starts and then fails on a missing table has a database without a
      # schema, not a broken installation. Empty where the server knows nothing
      # beyond the reason.
      diagnosis: string  # optional
      # Every directory the discovery walked, in order. "Nothing found" and "the
      # server started somewhere else" read the same, and only this list tells them
      # apart. Empty where the discovery never ran.
      searched: [string]
      # The setting the server could not use. Null where the caller set nothing.
      misconfiguration: string or null  # optional
      settings:
        # Environment variable that names the installation root.
        root: string
        # Environment variable that names the console command.
        console: string

The answer carries exactly one of these sets of fields:

- ``query``, ``matchCount``, ``answeredBy``, ``modules``, ``checked``
- ``query``, ``unsupported``

Answered
--------

Recorded on 2026-09-15 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on the one a client stands in. Neither
fills the whole surface. Answered against core-checkout, TYPO3 14.3.7-dev, the
14.3 core checkout below .checkouts/. Its console is out of reach:
<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3
exists. Its dependencies are not installed — vendor/autoload.php is not there
either, and composer install writes both. Answered against composer-project,
TYPO3 14.3.0, the installation this repository writes below .fixtures/. Its
console answers. The tools that declare ``answeredBy`` carry an answer from
each, under a heading that names which. Every other answer comes from the first
alone, because nothing in it would differ. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and ``bin/cli tools:check`` holds it.

modules
~~~~~~~

Called with:

.. code-block:: json

    {}

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both.

Data:

.. code-block:: json

    {
        "query": "",
        "unsupported": {
            "cause": "installation-not-answering",
            "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both",
            "repositoryState": "installed",
            "diagnosis": "",
            "searched": [
                "<installation>"
            ],
            "misconfiguration": null,
            "settings": {
                "root": "TYPO3_DEV_COMPANION_ROOT",
                "console": "TYPO3_DEV_COMPANION_CONSOLE"
            }
        }
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    4 backend module(s):
    - web
      /module/web  (backend)
      Web [LLL:EXT:backend/Resources/Private/Language/locallang.xlf]
      navigation: @typo3/backend/tree/page-tree-element
    - web > web_list
      /module/web/list  (backend)
      Records [LLL:EXT:backend/Resources/Private/Language/locallang.xlf]
      navigation: @typo3/backend/tree/page-tree-element
    - web > acme_events
      /module/web/acme-events  (acme_events)
      Events [LLL:EXT:acme_events/Resources/Private/Language/locallang.xlf]
      navigation: @typo3/backend/tree/page-tree-element
      route acme_events.detail  /module/web/acme-events/detail
    - site
      /module/site  (backend)
      Site Management [LLL:EXT:backend/Resources/Private/Language/locallang.xlf]

    A module is declared in its extension's Configuration/Backend/Modules.php; the label in brackets is a translation domain reference. The navigation component is the resolved one: a module inherits its parent's, so the registration file of a page-tree navigated module often names none.

Data:

.. code-block:: json

    {
        "query": "",
        "matchCount": 4,
        "modules": [
            {
                "identifier": "web",
                "parents": [],
                "extension": "backend",
                "labels": "Web [LLL:EXT:backend/Resources/Private/Language/locallang.xlf]",
                "path": "/module/web",
                "position": "",
                "navigationComponent": "@typo3/backend/tree/page-tree-element",
                "access": "",
                "routes": []
            },
            {
                "identifier": "web_list",
                "parents": [
                    "web"
                ],
                "extension": "backend",
                "labels": "Records [LLL:EXT:backend/Resources/Private/Language/locallang.xlf]",
                "path": "/module/web/list",
                "position": "{\"after\":\"web_layout\"}",
                "navigationComponent": "@typo3/backend/tree/page-tree-element",
                "access": "user",
                "routes": [
                    {
                        "name": "_default",
                        "identifier": "web_list",
                        "path": "/module/web/list",
                        "target": "TYPO3\\CMS\\Backend\\Controller\\RecordListController::mainAction"
                    }
                ]
            },
            {
                "identifier": "acme_events",
                "parents": [
                    "web"
                ],
                "extension": "acme_events",
                "labels": "Events [LLL:EXT:acme_events/Resources/Private/Language/locallang.xlf]",
                "path": "/module/web/acme-events",
                "position": "{\"after\":\"web_list\"}",
                "navigationComponent": "@typo3/backend/tree/page-tree-element",
                "access": "user",
                "routes": [
                    {
                        "name": "_default",
                        "identifier": "acme_events",
                        "path": "/module/web/acme-events",
                        "target": "TYPO3\\CMS\\Backend\\Controller\\FixtureModuleController::listAction"
                    },
                    {
                        "name": "detail",
                        "identifier": "acme_events.detail",
                        "path": "/module/web/acme-events/detail",
                        "target": "TYPO3\\CMS\\Backend\\Controller\\FixtureModuleController::detailAction"
                    }
                ]
            },
            {
                "identifier": "site",
                "parents": [],
                "extension": "backend",
                "labels": "Site Management [LLL:EXT:backend/Resources/Private/Language/locallang.xlf]",
                "path": "/module/site",
                "position": "",
                "navigationComponent": "",
                "access": "",
                "routes": []
            }
        ],
        "checked": [],
        "answeredBy": "installation"
    }
