.. _typo3_icon_lookup:

``typo3_icon_lookup``
=====================

*Validate or find icon identifiers*

Validate or find icon identifiers in the TYPO3 backend icon registry of the
installation you work in. Pass identifiers to confirm several at once. Each
comes back registered or not, in one call, which is what to use when you already
read them out of a template. Pass query to search for one by name or by what it
means. The answer comes from the booted installation. So what a package
registers in a loop or from ext_localconf.php is in it, as well as what its
Configuration/Icons.php declares. Where the installation cannot boot, the tool
reads the T3Icons set, the package registration files and the flag images
instead. That is a checkout with no console or no configuration yet. Then
answeredBy says 'packages', and the answer states what that leaves out.
Identifiers spell shapes rather than intents, so the search maps concept words:
"warning" finds actions-exclamation-triangle. Backend only: IconFactory resolves
the identifiers and <core:icon> renders them, and a frontend template can use
neither. Answers from: installation, packages.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`,
:ref:`packages <answer-sources-packages>`.

Takes
-----

.. code-block:: yaml

    # Identifier, identifier fragment, or concept, for example "actions-open",
    # "delete", or "warning". Omit to list the categories and concept words.
    query: string  # optional
    # Complete identifiers to check in one call, for example ["actions-open",
    # "actions-cog"]. Each gets registered or not as its own answer, with no rank
    # behind the ones that are. That is what to pass when you already read the
    # identifiers out of a template and only need them confirmed. A miss still
    # carries suggestions.
    identifiers: [string]  # optional
    # Maximum number of identifiers to return.
    limit: integer  # optional

Answers with
------------

.. code-block:: yaml

    query: string
    # For a complete identifier, 1 only when the registry holds that exact
    # identifier and otherwise 0. For a concept search, the number of icons that
    # match.
    matchCount: integer  # optional
    # Related identifiers returned beside an identifier validation. The
    # actions-/content- usage prefix alone never makes an icon a suggestion.
    suggestionCount: integer  # optional
    # Whether the query was a registered identifier. False for a query shaped like
    # one that is not registered — the listed icons are then suggestions, not the
    # answer.
    exactMatch: boolean  # optional
    # One of: installation, packages. installation: its assembled runtime state
    # answered. packages: the server read the files the installed packages ship,
    # because it could not ask the console. The answer misses the overrides that
    # apply at runtime.
    answeredBy: string  # optional
    icons:  # optional
      - identifier: string
        category: string
        # The identifier this one is an alias of.
        aliasOf: string or null
        # What registers it: t3icons, flags, or the
        # EXT:<key>/Configuration/Icons.php that declares it.
        source: string
        # Query terms it matched.
        matched: integer  # optional
        score: integer  # optional
        why: [string]  # optional
    # One entry per identifier passed in, in that order. Returned when the call
    # passed identifiers.
    validated:  # optional
      - # As it was passed.
        identifier: string
        # Whether the registry holds this exact identifier. False is the answer, not
        # an empty result.
        registered: boolean
        category: string
        # The identifier this one is an alias of.
        aliasOf: string or null
        # Where it is registered. Empty where it is not.
        source: string
        # What this identifier is already the icon of in this installation, as
        # "tt_content.CType=<value>". Registered says the identifier resolves; this
        # says whose picture it is, which is the question a caller who borrows one
        # asks. Empty means nothing binds it here — or that the installation did
        # not answer, which answeredBy is what says.
        usedBy: [string]
        # Related identifiers, for a miss only. A registered identifier carries
        # none, because its neighbours are not an answer to it.
        suggestions: [string]
    # What each word of the query reached on its own. So a list that matched on one
    # of them does not read as an answer to all of them. Zero is a word no
    # registered identifier carries. A concept word that maps to no shape reaches
    # nothing here even where the icon exists under another name. Answered for a
    # concept query, and empty for an identifier validation and where the call
    # passed no query.
    terms:  # optional
      - # The word, lowercased as the search used it.
        term: string
        matchCount: integer
    # Returned when the call passed no query.
    categories: [string]  # optional
    # Concept words that map to a shape. Returned when the call passed no query.
    concepts: [string]  # optional
    # Where these identifiers work: the backend registry, not the frontend render.
    # Carried by every answered lookup.
    scope: string  # optional
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

- ``query``, ``matchCount``, ``suggestionCount``, ``exactMatch``,
  ``answeredBy``, ``icons``
- ``query``, ``unsupported``

Answered
--------

Recorded on 2026-09-23 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on the one a client stands in. Neither
fills the whole surface. Answered against core-checkout, TYPO3 14.3.8-dev, the
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

icons: hit
~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "actions-open"
    }

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.

    "actions-open" is registered in <installation>; 22 related identifier(s) follow as suggestions:
    - actions-open
      matched: name part "open", exact identifier
    - actions-document-history-open
      alias of actions-history
      matched: name part "open"
    - actions-document-open
      alias of actions-document-edit
      matched: name part "open"
    - actions-document-open-read-only
      alias of actions-document-readonly
      matched: name part "open"
    - actions-envelope-open
      matched: name part "open"
    - actions-envelope-open-text
      matched: name part "open"
    - actions-page-open
      alias of actions-file-edit
      matched: name part "open"
    - actions-system-help-open
      alias of actions-question
      matched: name part "open"
    - actions-system-list-open
      alias of actions-list-alternative
      matched: name part "open"
    - actions-system-pagemodule-open
      alias of actions-file-search
      matched: name part "open"
    - actions-system-tree-search-open
      alias of actions-filter
      matched: name part "open"
    - actions-system-typoscript-documentation-open
      alias of actions-notebook-typoscript
      matched: name part "open"
    - actions-version-page-open
      alias of actions-file-edit
      matched: name part "open"
    - actions-window-open
      matched: name part "open"
    - mimetypes-open-document-database
      matched: name part "open"
    - mimetypes-open-document-drawing
      matched: name part "open"
    - mimetypes-open-document-formula
      matched: name part "open"
    - mimetypes-open-document-presentation
      matched: name part "open"
    - mimetypes-open-document-spreadsheet
      matched: name part "open"
    - mimetypes-open-document-text
      matched: name part "open"
    - actions-file-openoffice
      matched: substring "open"
    - apps-filetree-folder-opened
      matched: substring "open"
    - apps-toolbar-menu-opendocs
      alias of actions-file
      matched: substring "open"

Data:

.. code-block:: json

    {
        "query": "actions-open",
        "matchCount": 1,
        "suggestionCount": 22,
        "exactMatch": true,
        "icons": [
            {
                "identifier": "actions-open",
                "category": "actions",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 1004,
                "why": [
                    "name part \"open\"",
                    "exact identifier"
                ]
            },
            {
                "identifier": "actions-document-history-open",
                "category": "actions",
                "aliasOf": "actions-history",
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-document-open",
                "category": "actions",
                "aliasOf": "actions-document-edit",
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-document-open-read-only",
                "category": "actions",
                "aliasOf": "actions-document-readonly",
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-envelope-open",
                "category": "actions",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-envelope-open-text",
                "category": "actions",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-page-open",
                "category": "actions",
                "aliasOf": "actions-file-edit",
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-system-help-open",
                "category": "actions",
                "aliasOf": "actions-question",
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-system-list-open",
                "category": "actions",
                "aliasOf": "actions-list-alternative",
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-system-pagemodule-open",
                "category": "actions",
                "aliasOf": "actions-file-search",
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-system-tree-search-open",
                "category": "actions",
                "aliasOf": "actions-filter",
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-system-typoscript-documentation-open",
                "category": "actions",
                "aliasOf": "actions-notebook-typoscript",
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-version-page-open",
                "category": "actions",
                "aliasOf": "actions-file-edit",
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-window-open",
                "category": "actions",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "mimetypes-open-document-database",
                "category": "mimetypes",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "mimetypes-open-document-drawing",
                "category": "mimetypes",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "mimetypes-open-document-formula",
                "category": "mimetypes",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "mimetypes-open-document-presentation",
                "category": "mimetypes",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "mimetypes-open-document-spreadsheet",
                "category": "mimetypes",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "mimetypes-open-document-text",
                "category": "mimetypes",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            },
            {
                "identifier": "actions-file-openoffice",
                "category": "actions",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 2,
                "why": [
                    "substring \"open\""
                ]
            },
            {
                "identifier": "apps-filetree-folder-opened",
                "category": "apps",
                "aliasOf": null,
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 2,
                "why": [
                    "substring \"open\""
                ]
            },
            {
                "identifier": "apps-toolbar-menu-opendocs",
                "category": "apps",
                "aliasOf": "actions-file",
                "source": "t3icons",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 2,
                "why": [
                    "substring \"open\""
                ]
            }
        ],
        "terms": [],
        "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.",
        "answeredBy": "packages"
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file.

    "actions-open" is registered in <installation>; 1 related identifier(s) follow as suggestions:
    - actions-open
      registered in EXT:backend/Configuration/Icons.php
      matched: name part "open", exact identifier
    - actions-document-open
      registered in EXT:backend/Resources/Public/Icons/actions-document-open.svg
      matched: name part "open"

Data:

.. code-block:: json

    {
        "query": "actions-open",
        "matchCount": 1,
        "suggestionCount": 1,
        "exactMatch": true,
        "icons": [
            {
                "identifier": "actions-open",
                "category": "actions",
                "aliasOf": null,
                "source": "EXT:backend/Configuration/Icons.php",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 1004,
                "why": [
                    "name part \"open\"",
                    "exact identifier"
                ]
            },
            {
                "identifier": "actions-document-open",
                "category": "actions",
                "aliasOf": null,
                "source": "EXT:backend/Resources/Public/Icons/actions-document-open.svg",
                "matched": 1,
                "matchedTerms": [
                    "open"
                ],
                "score": 4,
                "why": [
                    "name part \"open\""
                ]
            }
        ],
        "terms": [],
        "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file.",
        "answeredBy": "installation"
    }

icons: several validated at once
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "identifiers": [
            "actions-open",
            "actions-cog",
            "acme-events-teaser"
        ]
    }

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.

    2 of 3 identifier(s) are registered in <installation>:
    - actions-open: registered
    - actions-cog: registered
    - acme-events-teaser: NOT registered
      did you mean: content-text-teaser

Data:

.. code-block:: json

    {
        "query": "",
        "matchCount": 2,
        "suggestionCount": 1,
        "exactMatch": false,
        "icons": [],
        "validated": [
            {
                "identifier": "actions-open",
                "registered": true,
                "category": "actions",
                "aliasOf": null,
                "source": "t3icons",
                "usedBy": [],
                "suggestions": []
            },
            {
                "identifier": "actions-cog",
                "registered": true,
                "category": "actions",
                "aliasOf": null,
                "source": "t3icons",
                "usedBy": [],
                "suggestions": []
            },
            {
                "identifier": "acme-events-teaser",
                "registered": false,
                "category": "",
                "aliasOf": null,
                "source": "",
                "usedBy": [],
                "suggestions": [
                    "content-text-teaser"
                ]
            }
        ],
        "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.",
        "answeredBy": "packages"
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file.

    2 of 3 identifier(s) are registered in <installation>:
    - actions-open: registered
      registered in EXT:backend/Configuration/Icons.php
    - actions-cog: NOT registered
    - acme-events-teaser: registered
      registered in EXT:acme_events/Configuration/Icons.php

Data:

.. code-block:: json

    {
        "query": "",
        "matchCount": 2,
        "suggestionCount": 0,
        "exactMatch": false,
        "icons": [],
        "validated": [
            {
                "identifier": "actions-open",
                "registered": true,
                "category": "actions",
                "aliasOf": null,
                "source": "EXT:backend/Configuration/Icons.php",
                "usedBy": [],
                "suggestions": []
            },
            {
                "identifier": "actions-cog",
                "registered": false,
                "category": "",
                "aliasOf": null,
                "source": "",
                "usedBy": [],
                "suggestions": []
            },
            {
                "identifier": "acme-events-teaser",
                "registered": true,
                "category": "acme",
                "aliasOf": null,
                "source": "EXT:acme_events/Configuration/Icons.php",
                "usedBy": [],
                "suggestions": []
            }
        ],
        "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file.",
        "answeredBy": "installation"
    }

icons: everything
~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {}

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.

    Icon categories in this installation: actions, apps, avatar, content, default, empty, files, flags, form, information, install, mimetypes, miscellaneous, module, modulegroup, overlay, provider, share, spinner, status, sysnote, tcarecords, theme.

    Concept words that map to a shape: warning, caution, error, danger, info, notice, help, success, confirm, add, new, create, edit, delete, remove, save, search, filter, settings, configuration, user, permission, lock, hidden, visibility, preview, view, upload, download, refresh, reload, sort, close, cancel, copy, duplicate, move, link, translation, localization, language, folder, page, record, history, undo, import, export, message, notification, mail, calendar, time, list, menu, workspace, cache, bookmark, extension.

Data:

.. code-block:: json

    {
        "query": "",
        "matchCount": 0,
        "suggestionCount": 0,
        "exactMatch": false,
        "icons": [],
        "categories": [
            "actions",
            "apps",
            "avatar",
            "content",
            "default",
            "empty",
            "files",
            "flags",
            "form",
            "information",
            "install",
            "mimetypes",
            "miscellaneous",
            "module",
            "modulegroup",
            "overlay",
            "provider",
            "share",
            "spinner",
            "status",
            "sysnote",
            "tcarecords",
            "theme"
        ],
        "concepts": [
            "warning",
            "caution",
            "error",
            "danger",
            "info",
            "notice",
            "help",
            "success",
            "confirm",
            "add",
            "new",
            "create",
            "edit",
            "delete",
            "remove",
            "save",
            "search",
            "filter",
            "settings",
            "configuration",
            "user",
            "permission",
            "lock",
            "hidden",
            "visibility",
            "preview",
            "view",
            "upload",
            "download",
            "refresh",
            "reload",
            "sort",
            "close",
            "cancel",
            "copy",
            "duplicate",
            "move",
            "link",
            "translation",
            "localization",
            "language",
            "folder",
            "page",
            "record",
            "history",
            "undo",
            "import",
            "export",
            "message",
            "notification",
            "mail",
            "calendar",
            "time",
            "list",
            "menu",
            "workspace",
            "cache",
            "bookmark",
            "extension"
        ],
        "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.",
        "answeredBy": "packages"
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file.

    Icon categories in this installation: acme, actions, content, mimetypes.

    Concept words that map to a shape: warning, caution, error, danger, info, notice, help, success, confirm, add, new, create, edit, delete, remove, save, search, filter, settings, configuration, user, permission, lock, hidden, visibility, preview, view, upload, download, refresh, reload, sort, close, cancel, copy, duplicate, move, link, translation, localization, language, folder, page, record, history, undo, import, export, message, notification, mail, calendar, time, list, menu, workspace, cache, bookmark, extension.

Data:

.. code-block:: json

    {
        "query": "",
        "matchCount": 0,
        "suggestionCount": 0,
        "exactMatch": false,
        "icons": [],
        "categories": [
            "acme",
            "actions",
            "content",
            "mimetypes"
        ],
        "concepts": [
            "warning",
            "caution",
            "error",
            "danger",
            "info",
            "notice",
            "help",
            "success",
            "confirm",
            "add",
            "new",
            "create",
            "edit",
            "delete",
            "remove",
            "save",
            "search",
            "filter",
            "settings",
            "configuration",
            "user",
            "permission",
            "lock",
            "hidden",
            "visibility",
            "preview",
            "view",
            "upload",
            "download",
            "refresh",
            "reload",
            "sort",
            "close",
            "cancel",
            "copy",
            "duplicate",
            "move",
            "link",
            "translation",
            "localization",
            "language",
            "folder",
            "page",
            "record",
            "history",
            "undo",
            "import",
            "export",
            "message",
            "notification",
            "mail",
            "calendar",
            "time",
            "list",
            "menu",
            "workspace",
            "cache",
            "bookmark",
            "extension"
        ],
        "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file.",
        "answeredBy": "installation"
    }
