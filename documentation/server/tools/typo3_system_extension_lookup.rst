.. _typo3_system_extension_lookup:

``typo3_system_extension_lookup``
=================================

Answer whether an extension is part of the TYPO3 core, and on which versions. It
lists the system extensions of every covered TYPO3 line, by extension key and
Composer package name. Each comes with what it is for and the range the core
ships it on. Independent of any installation, which is the point. The question
comes up for a package that is not installed, and otherwise memory answers "is
this core". A miss means the name is not a system extension on the covered
versions, never that it does not exist. Answers from: knowledge.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`knowledge <answer-sources-knowledge>`.

Takes
-----

.. code-block:: yaml

    # An extension key ("theme_camino"), a Composer package name
    # ("typo3/cms-impexp"), or a word from what it does ("redirects"). Omit to list
    # everything the core ships.
    query: string  # optional
    # The TYPO3 version to answer for, for example "13.4" or "14". Restricts the
    # answer to what that line ships. Defaults to the version of the installation
    # this server started in. Where there is none, every entry comes back with the
    # range the core ships it on.
    targetVersion: string  # optional

Answers with
------------

.. code-block:: yaml

    query: string
    # The TYPO3 major the answer is for, stated by the caller or read from the
    # installation. Null means every covered version is in the answer and each entry
    # carries its own range.
    targetVersion: integer or null  # optional
    # How many system extensions matched. Zero means the name is not one of them on
    # the versions asked about, not that no such package exists.
    matchCount: integer
    extensions:
      - # The extension key, which is the name of the directory below typo3/sysext.
        key: string
        # The Composer package name to require it by, where an installation does not
        # have it already.
        package: string
        # What it is for.
        description: string
        # First covered major that ships it. Null means every covered major does.
        since: integer or null
        # Last covered major that ships it. Null means it is still shipped on the
        # newest one.
        until: integer or null
        # The range in words, empty when the core ships it everywhere this knowledge
        # base reaches.
        shippedOn: string
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

system extensions: hit
~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "impexp"
    }

Text:

.. code-block:: text

    The core ships these on TYPO3 v14.

    - impexp (typo3/cms-impexp)
      Import/Export - Tool for importing and exporting records using XML or the custom T3D format.

Data:

.. code-block:: json

    {
        "query": "impexp",
        "targetVersion": 14,
        "matchCount": 1,
        "extensions": [
            {
                "key": "impexp",
                "package": "typo3/cms-impexp",
                "description": "Import/Export - Tool for importing and exporting records using XML or the custom T3D format.",
                "since": null,
                "until": null,
                "shippedOn": ""
            }
        ],
        "coveredVersions": [
            12,
            13,
            14,
            15
        ]
    }

system extensions: miss
~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "typo3/cms-content-blocks"
    }

Text:

.. code-block:: text

    "typo3/cms-content-blocks" is not a system extension on TYPO3 v14. That is an answer about the core, not about the package: it may well exist on Packagist or in the TER, where it is a third-party extension with its own release cycle. Call this without a query for everything the core does ship.

Data:

.. code-block:: json

    {
        "query": "typo3/cms-content-blocks",
        "targetVersion": 14,
        "matchCount": 0,
        "extensions": [],
        "coveredVersions": [
            12,
            13,
            14,
            15
        ]
    }

system extensions: everything
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {}

Text:

.. code-block:: text

    The core ships these on TYPO3 v14.

    - adminpanel (typo3/cms-adminpanel)
      Admin Panel - The Admin Panel displays information about your site in the frontend and contains a range of metrics including debug and caching information.
    - backend (typo3/cms-backend)
      Backend
    - belog (typo3/cms-belog)
      Log - View logs from the sys_log table in the TYPO3 backend modules System>Log
    - beuser (typo3/cms-beuser)
      Backend User - TYPO3 backend module Administration > Users for managing backend users and groups.
    - core (typo3/cms-core)
      Core
    - dashboard (typo3/cms-dashboard)
      Dashboard - TYPO3 backend module used to configure and create backend widgets.
    - extbase (typo3/cms-extbase)
      Extbase - Extension framework to create TYPO3 frontend plugins and TYPO3 backend modules.
    - extensionmanager (typo3/cms-extensionmanager)
      Extension Manager - Backend module (System > Extensions) for viewing and managing extensions.
    - felogin (typo3/cms-felogin)
      Frontend Login - A template-based plugin to log in website users in the TYPO3 frontend.
    - filelist (typo3/cms-filelist)
      Filelist - TYPO3 backend module 'Media' used for managing files.
    - filemetadata (typo3/cms-filemetadata)
      File Metadata - Adds additional metadata to file management.
    - fluid (typo3/cms-fluid)
      Fluid Integration - Integration of the Fluid templating engine into TYPO3.
    - fluid_styled_content (typo3/cms-fluid-styled-content)
      Fluid Styled Content - Fluid templates for TYPO3 content elements.
    - form (typo3/cms-form)
      Form - Flexible TYPO3 frontend form framework that comes with a backend editor interface.
    - frontend (typo3/cms-frontend)
      Frontend
    - impexp (typo3/cms-impexp)
      Import/Export - Tool for importing and exporting records using XML or the custom T3D format.
    - indexed_search (typo3/cms-indexed-search)
      Indexed Search - Provides indexing functionality for TYPO3 pages and records as well as files including PDF, Word, HTML and plain text.
    - info (typo3/cms-info)
      Info - TYPO3 backend module for displaying information, such as a pagetree overview and localization information.
    - install (typo3/cms-install)
      Install Tool - The Install Tool is used for installation, upgrade, system administration and setup tasks.
    - linkvalidator (typo3/cms-linkvalidator)
      LinkValidator - Checks for broken links and displays results in the (Info>LinkValidator) backend module.
    - lowlevel (typo3/cms-lowlevel)
      Lowlevel - Technical analysis of the system. This includes raw database search, checking relations, counting pages and records etc.
    - opendocs (typo3/cms-opendocs)
      Open Docs - Shows opened documents for the TYPO3 backend user.
    - reactions (typo3/cms-reactions)
      Reactions - Handle incoming Webhooks for TYPO3
    - recycler (typo3/cms-recycler)
      Recycler - Restore deleted records or remove them from the database permanently.
    - redirects (typo3/cms-redirects)
      Redirects - Create manual redirects, list existing redirects and automatically create
    redirects on slug changes.
    - reports (typo3/cms-reports)
      Reports - Show status reports and installed services in the (System>Reports) backend module.
    - rte_ckeditor (typo3/cms-rte-ckeditor)
      RTE CKEditor - Integration of CKEditor as a Rich Text Editor for the TYPO3 backend.
    - scheduler (typo3/cms-scheduler)
      Scheduler - Schedule tasks to run once or periodically at a specific time.
    - seo (typo3/cms-seo)
      SEO - SEO features including specific fields for SEO purposes, rendering of HTML meta tags and sitemaps.
    - styleguide (typo3/cms-styleguide) — TYPO3 v13 and newer
      Styleguide - Showcase of the TYPO3 backend components and their markup.
    - sys_note (typo3/cms-sys-note)
      System Notes - Records with messages which can be placed on any page and contain instructions or other information related to a page or section.
    - theme_camino (typo3/theme-camino) — TYPO3 v14 and newer
      Theme: "Camino" - The default theme for TYPO3 sites in the TYPO3 v14 series.
    - tstemplate (typo3/cms-tstemplate)
      TypoScript - TYPO3 backend module for the management of TypoScript records for the CMS frontend.
    - viewpage (typo3/cms-viewpage)
      Viewpage - Use the (Web>View) backend module to view a frontend page inside the TYPO3 backend.
    - webhooks (typo3/cms-webhooks)
      Webhooks - Handle outgoing Webhooks for TYPO3
    - workspaces (typo3/cms-workspaces)
      Workspaces - Workflows with custom stages and versioning for a better editing and publishing experience.

Data:

.. code-block:: json

    {
        "query": "",
        "targetVersion": 14,
        "matchCount": 36,
        "extensions": [
            {
                "key": "adminpanel",
                "package": "typo3/cms-adminpanel",
                "description": "Admin Panel - The Admin Panel displays information about your site in the frontend and contains a range of metrics including debug and caching information.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "backend",
                "package": "typo3/cms-backend",
                "description": "Backend",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "belog",
                "package": "typo3/cms-belog",
                "description": "Log - View logs from the sys_log table in the TYPO3 backend modules System>Log",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "beuser",
                "package": "typo3/cms-beuser",
                "description": "Backend User - TYPO3 backend module Administration > Users for managing backend users and groups.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "core",
                "package": "typo3/cms-core",
                "description": "Core",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "dashboard",
                "package": "typo3/cms-dashboard",
                "description": "Dashboard - TYPO3 backend module used to configure and create backend widgets.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "extbase",
                "package": "typo3/cms-extbase",
                "description": "Extbase - Extension framework to create TYPO3 frontend plugins and TYPO3 backend modules.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "extensionmanager",
                "package": "typo3/cms-extensionmanager",
                "description": "Extension Manager - Backend module (System > Extensions) for viewing and managing extensions.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "felogin",
                "package": "typo3/cms-felogin",
                "description": "Frontend Login - A template-based plugin to log in website users in the TYPO3 frontend.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "filelist",
                "package": "typo3/cms-filelist",
                "description": "Filelist - TYPO3 backend module 'Media' used for managing files.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "filemetadata",
                "package": "typo3/cms-filemetadata",
                "description": "File Metadata - Adds additional metadata to file management.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "fluid",
                "package": "typo3/cms-fluid",
                "description": "Fluid Integration - Integration of the Fluid templating engine into TYPO3.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "fluid_styled_content",
                "package": "typo3/cms-fluid-styled-content",
                "description": "Fluid Styled Content - Fluid templates for TYPO3 content elements.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "form",
                "package": "typo3/cms-form",
                "description": "Form - Flexible TYPO3 frontend form framework that comes with a backend editor interface.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "frontend",
                "package": "typo3/cms-frontend",
                "description": "Frontend",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "impexp",
                "package": "typo3/cms-impexp",
                "description": "Import/Export - Tool for importing and exporting records using XML or the custom T3D format.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "indexed_search",
                "package": "typo3/cms-indexed-search",
                "description": "Indexed Search - Provides indexing functionality for TYPO3 pages and records as well as files including PDF, Word, HTML and plain text.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "info",
                "package": "typo3/cms-info",
                "description": "Info - TYPO3 backend module for displaying information, such as a pagetree overview and localization information.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "install",
                "package": "typo3/cms-install",
                "description": "Install Tool - The Install Tool is used for installation, upgrade, system administration and setup tasks.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "linkvalidator",
                "package": "typo3/cms-linkvalidator",
                "description": "LinkValidator - Checks for broken links and displays results in the (Info>LinkValidator) backend module.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "lowlevel",
                "package": "typo3/cms-lowlevel",
                "description": "Lowlevel - Technical analysis of the system. This includes raw database search, checking relations, counting pages and records etc.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "opendocs",
                "package": "typo3/cms-opendocs",
                "description": "Open Docs - Shows opened documents for the TYPO3 backend user.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "reactions",
                "package": "typo3/cms-reactions",
                "description": "Reactions - Handle incoming Webhooks for TYPO3",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "recycler",
                "package": "typo3/cms-recycler",
                "description": "Recycler - Restore deleted records or remove them from the database permanently.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "redirects",
                "package": "typo3/cms-redirects",
                "description": "Redirects - Create manual redirects, list existing redirects and automatically create\nredirects on slug changes.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "reports",
                "package": "typo3/cms-reports",
                "description": "Reports - Show status reports and installed services in the (System>Reports) backend module.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "rte_ckeditor",
                "package": "typo3/cms-rte-ckeditor",
                "description": "RTE CKEditor - Integration of CKEditor as a Rich Text Editor for the TYPO3 backend.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "scheduler",
                "package": "typo3/cms-scheduler",
                "description": "Scheduler - Schedule tasks to run once or periodically at a specific time.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "seo",
                "package": "typo3/cms-seo",
                "description": "SEO - SEO features including specific fields for SEO purposes, rendering of HTML meta tags and sitemaps.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "styleguide",
                "package": "typo3/cms-styleguide",
                "description": "Styleguide - Showcase of the TYPO3 backend components and their markup.",
                "since": 13,
                "until": null,
                "shippedOn": "TYPO3 v13 and newer"
            },
            {
                "key": "sys_note",
                "package": "typo3/cms-sys-note",
                "description": "System Notes - Records with messages which can be placed on any page and contain instructions or other information related to a page or section.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "theme_camino",
                "package": "typo3/theme-camino",
                "description": "Theme: \"Camino\" - The default theme for TYPO3 sites in the TYPO3 v14 series.",
                "since": 14,
                "until": null,
                "shippedOn": "TYPO3 v14 and newer"
            },
            {
                "key": "tstemplate",
                "package": "typo3/cms-tstemplate",
                "description": "TypoScript - TYPO3 backend module for the management of TypoScript records for the CMS frontend.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "viewpage",
                "package": "typo3/cms-viewpage",
                "description": "Viewpage - Use the (Web>View) backend module to view a frontend page inside the TYPO3 backend.",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "webhooks",
                "package": "typo3/cms-webhooks",
                "description": "Webhooks - Handle outgoing Webhooks for TYPO3",
                "since": null,
                "until": null,
                "shippedOn": ""
            },
            {
                "key": "workspaces",
                "package": "typo3/cms-workspaces",
                "description": "Workspaces - Workflows with custom stages and versioning for a better editing and publishing experience.",
                "since": null,
                "until": null,
                "shippedOn": ""
            }
        ],
        "coveredVersions": [
            12,
            13,
            14,
            15
        ]
    }
