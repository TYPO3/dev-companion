.. _typo3_extension_describe:

``typo3_extension_describe``
============================

Describe what one installed extension registers: the tables its TCA defines and
extends; the content elements it adds to tt_content, with the template each
renders through and the FlexForm each binds; its backend modules and routes; its
icons; its site sets and the files each carries; its form configurations and the
form definitions they store; its service tags, middlewares, Fluid roots and
global Fluid namespaces; the shape of its Classes/ directory; the files it ships
that core has stopped reading, with what each costs — four predicates, so not an
upgrade check; and beside all that its manual, README, test layers and XLF
files. Tables, content elements and icons come from the booted installation
where there is one. Everything else is read from its own files and never
executed, so it answers on a fresh clone and for a third-party extension too.
Call it with a key typo3_project_describe lists. A key the installation does not
have is answered with the keys it does have, and no installation at all with
unsupported. It answers what the extension registers and never what is stored
through it: how many rows one of its tables holds, on which page and what they
are is typo3_record_lookup. Answers from: installation, packages.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`,
:ref:`packages <answer-sources-packages>`.

Takes
-----

.. code-block:: yaml

    # The extension key, as typo3_project_describe reports it, for example
    # "my_sitepackage" or "news".
    extension: string

Answers with
------------

.. code-block:: yaml

    # The extension key that was asked for.
    key: string
    # Absolute path of the extension. Null when the installation does not have it.
    path: string or null  # optional
    # One of: system, project, third-party, fixture, null. system: TYPO3's own.
    # project: inside the repository. third-party: installed as a dependency.
    # fixture: below a Tests/ directory, so it belongs to the test setup.
    origin: string or null  # optional
    # The Composer package name it declares.
    composerName: string or null  # optional
    # What its composer.json says it is.
    description: string or null  # optional
    # What it requires, which is where a version conflict during an upgrade comes
    # from.
    requires:  # optional
      - package: string
        constraint: string
    # Tables its Configuration/TCA/ defines. Where the booted installation answered,
    # attributed to this extension by the EXT: reference each entry carries, so a
    # table added by a PHP call is among them. Otherwise by file name.
    # typo3_schema_lookup takes one of these names and answers what columns the core
    # derives for it.
    tcaTables: [string]  # optional
    # Tables it extends below Configuration/TCA/Overrides/. typo3_schema_lookup
    # answers these the same way.
    tcaOverrides: [string]  # optional
    # The content elements it adds to tt_content, where each renders, and what it
    # configures through. Where the booted installation answered, the identifiers
    # are what it registered, attributed to this extension by the EXT: reference
    # each carries. So an element whose identifier came out of a variable is among
    # them. Otherwise they are read from the override files, as identifier says.
    contentElements:  # optional
      - # The CType value, read from an addTcaSelectItem(), addRecordType() or
        # registerPlugin() call in one of those override files. An identifier
        # assembled at runtime or taken from a constant is not among them.
        identifier: string
        # One of: element, plugin. plugin: an Extbase plugin, registered by
        # ExtensionUtility::registerPlugin(), which renders through the dispatcher
        # rather than through a templateName of its own. element: everything else.
        kind: string
        # The Fluid template it renders through, from
        # tt_content.<identifier>.templateName in this extension's TypoScript. Null
        # where its TypoScript does not set one — another extension or the site
        # configuration may. On a plugin, one set here replaces the Generic wrapper
        # configurePlugin() generates instead of naming the plugin's template, and
        # null is the normal case.
        templateName: string or null
        # The TypoScript file that set it, relative to the extension.
        source: string or null
        # On a plugin: the TypoScript file of this extension that configures
        # plugin.tx_<identifier>, which is where its templateRootPaths and settings
        # are. Null where its TypoScript configures nothing there, and on anything
        # that is not a plugin.
        pluginSettings: string or null
        # The FlexForm data structure it binds, as the call declares it — a
        # FILE:EXT: reference, or "inline" where the XML stands in the override file
        # itself. Null where it binds none, which is a different element to review
        # than one that does.
        flexForm: string or null
    # Content types this extension renders and does not register, which is what a
    # package taking the rendering frame over from fluid_styled_content ends up
    # owning. Deleting one of these definitions leaves an element editors can still
    # select with nothing to render it, and no other field of this answer names it.
    # Empty where the extension renders only what it registers.
    renderedContentTypes:  # optional
      - # The CType this extension renders, from a tt_content.<identifier>
        # assignment in its own TypoScript.
        identifier: string
        # The Fluid template it renders through, where the same TypoScript sets one.
        # Null where the definition only copies lib.contentElement and leaves the
        # name to what it copied.
        templateName: string or null
        # The TypoScript file that set it, relative to the extension.
        source: string
        # The extension whose TCA registers that CType, read off the EXT: reference
        # in its label. Null where no installation answered, and on a booted one a
        # rendering definition for an element nothing registers — an editor cannot
        # select it and the file is dead.
        registeredBy: string or null
    # The Generic template this extension ships, relative to it, or null where it
    # ships none. ExtensionUtility::configurePlugin() renders every Extbase plugin
    # registered as a content type through templateName = Generic, so a package
    # defining lib.contentElement itself owes this file. Nothing in the package
    # points at it, which makes it the one most likely to be deleted as unused, and
    # deleting it empties every plugin on the site.
    pluginFrame: string or null  # optional
    # FlexForm bindings read from the override files whose content type none of the
    # contentElements entries above carries. typo3_flexform_lookup resolves one to
    # its fields. Usually empty. An entry here is a registration this answer read
    # and could not attribute. The identifier is real and the binding is real, and
    # whatever else registers that element was not established.
    unlistedFlexForms:  # optional
      - # The content type the binding names.
        identifier: string
        # The data structure, as above.
        flexForm: string
    # Module identifiers from Configuration/Backend/Modules.php.
    backendModules: [string]  # optional
    # Route names from Configuration/Backend/Routes.php and AjaxRoutes.php.
    backendRoutes: [string]  # optional
    # Identifiers from Configuration/Icons.php. Where the booted installation
    # answered, every icon it registered below this extension's EXT: reference, a
    # list built in a loop included. typo3_icon_lookup searches every package at
    # once.
    icons: [string]  # optional
    siteSets:  # optional
      - # The composer-style set name a site depends on.
        name: string
        # Relative to the extension.
        path: string
        # Which of the files core reads a set directory for are in it:
        # settings.definitions.yaml, settings.yaml, route-enhancers.yaml,
        # labels.xlf, page.tsconfig, constants.typoscript, setup.typoscript and
        # include_static_file.txt. config.yaml is not among them, being what makes
        # the directory a set. route-enhancers.yaml is read from v14.1; on v13 a set
        # carrying one is loaded and that file ignored. The last four are the
        # defaults a set gets where its config.yaml declares no typoscript, pagets
        # or labels path of its own. One that declares them reads from there
        # instead, and this list does not say so.
        files: [string]
    # The form configurations it registers, both ways in. Empty where it registers
    # none. An extension that ships a .form.yaml and registers no storage for it has
    # a form nothing loads, which is what this list is read for.
    formConfigurations:  # optional
      - # The YAML file, relative to the extension.
        path: string
        # The set name its config.yaml declares, which is what disabledSets matches
        # against. Null for a TypoScript-registered file, which has none.
        name: string or null
        # One of: set, typoscript. set: the directory convention
        # Configuration/Form/<SetName>/config.yaml, collected from every active
        # extension since v14.2 without being registered anywhere. typoscript:
        # plugin.tx_form.settings.yamlConfigurations or the module. one beside it,
        # which is the way before it, deprecated in v14.2 and removed in v15.0.
        registeredBy: string
        # What it declares under persistenceManager.allowedExtensionPaths — where
        # the form definitions it stores live. A storage configured as a file mount
        # instead is a record and is in no answer read from files.
        storagePaths: [string]
        # The .form.yaml files below those of the storage paths that are inside this
        # extension, relative to it.
        formDefinitions: [string]
    # Middleware identifiers from Configuration/RequestMiddlewares.php, across the
    # request scopes.
    middlewares: [string]  # optional
    # Tags its Services.yaml carries, such as data.processor, event.listener or
    # console.command.
    serviceTags: [string]  # optional
    # Which of Resources/Private/Templates, Partials and Layouts exist.
    fluidRoots: [string]  # optional
    # Prefixes it registers globally in Configuration/Fluid/Namespaces.php.
    fluidNamespaces: [string]  # optional
    # Files below Configuration/TypoScript/.
    typoScript: [string]  # optional
    # The shape of its Classes/ directory, read off the file tree rather than off a
    # registration.
    classes:  # optional
      # Every directory directly below Classes/, whatever it is named. Nothing is
      # filtered, so a directory here is not a registration of any kind — it is
      # what the extension calls it.
      directories:
        - # The directory, directly below Classes/, for example EventListener or
          # Utility.
          name: string
          # PHP files anywhere below it, its own subdirectories included.
          files: integer
      # PHP files lying directly in Classes/, under no directory of their own.
      looseFiles: integer
      # Every PHP file below Classes/, which is what `find Classes -name '*.php' |
      # wc -l` gives. The rows above and looseFiles add up to it.
      total: integer
    # Registration files it ships, from ext_localconf.php to
    # Initialisation/data.t3d.
    files: [string]  # optional
    # The files this extension ships that core has stopped reading, or is stopping,
    # each with what shipping it costs. Four predicates are checked, each a file and
    # what stands beside it. ext_tables.php. ext_emconf.php beside a composer.json
    # declaring neither providesPackages nor a version. ext_icon.svg/.png/.gif where
    # no Resources/Public/Icons/Extension.* stands to be read first. An
    # ext_typoscript_*.txt with no .typoscript file of the same name beside it. Each
    # is read from the extension's own tree, which is where every one of these
    # predicates lives, so no changelog sweep over what its code calls reaches any
    # of them. An empty list says none of the four holds here, not that the
    # extension is ready for the next major. Nothing else here is checked for a
    # deprecation, and typo3_changelog_lookup is what answers that question.
    deprecatedFiles:  # optional
      - # The file, relative to the extension. Not always a registration file:
        # ext_icon.* and ext_typoscript_*.txt are read by nothing now, so they are a
        # registration point nowhere and are checked here alone.
        file: string
        # The changelog entry, for typo3_changelog_lookup, which has the description
        # and the migration whole.
        changelog: string
        # What the entry turns on, which is what holds here — shipping the file,
        # and what stands beside it: what composer.json declares, or the file core
        # reads before this one.
        predicate: string
        # What it raises, from which version, and what the removal does instead.
        cost: string
    # Declaration files that are there but whose entries do not stand in their own
    # text. Each assembles its list while it runs, so what it registers is missing
    # from the lists above rather than absent. The booted installation is what
    # answers for them. An empty list says each declaration file that exists stood
    # in its own text, not that everything the extension ships was read.
    # ext_localconf.php and ext_tables.php register by running and are read by
    # nothing here.
    notReadStatically: [string]  # optional
    # What it ships beside its registrations. Every key is present even when the
    # artifact is not, because the absence of a manual, a test or a translation is
    # the answer a file listing cannot give.
    artifacts:  # optional
      # Its manual entry point, "Documentation/" where the directory exists without
      # one, null where the extension ships no manual at all.
      manual: string or null
      # The README it ships, null where there is none.
      readme: string or null
      # The layers below Tests/, for example Unit and Functional. Empty where the
      # extension ships no tests.
      tests: [string]
      languageFiles:
        - # Relative to the extension.
          path: string
          # The source-language its own <file> element declares, null where it
          # declares none. This is what the file says, not what it should say.
          sourceLanguage: string or null
          # Locales of the prefixed files beside it, such as de for de.messages.xlf.
          translations: [string]
    # On a miss: the extension keys this installation does have.
    installed: [string]  # optional
    # One of: installation, packages. installation: its assembled runtime state
    # answered. packages: read from the files the installed packages ship, because
    # the console could not be asked — overrides applied at runtime are not
    # reflected.
    answeredBy: string  # optional
    unsupported:  # optional
      # One of: no-installation, misconfigured, installation-not-answering.
      # no-installation: nothing to ask from here, and searched says where it
      # looked. misconfigured: an installation was named and could not be used, so
      # nothing was searched for. installation-not-answering: one was found and its
      # console did not answer — a stopped container or a database with no schema,
      # which is a state that ends without reinstalling anything.
      cause: string
      # What stopped it, in the words the attempt produced.
      reason: string
      # One of: installed, not-installed, undeclared, null. Which state the
      # repository the caller stands in is in, which the cause does not say.
      # installed: packages are installed below the root that was found, so an
      # install is not what is missing here. not-installed: the repository declares
      # TYPO3 and nothing is installed below it yet, so this call is answerable once
      # composer install has run. undeclared: nothing in the directories walked
      # declares TYPO3, so an install here would answer nothing. Null where nothing
      # was looked at: a named root that could not be used, or an entrypoint that
      # handed no directory in.
      repositoryState: string or null  # optional
      # What the reason means where the message alone does not say it. A console
      # that starts and then fails on a missing table has a database without a
      # schema, not a broken installation. Empty where nothing beyond the reason is
      # known.
      diagnosis: string  # optional
      # Every directory the discovery walked, in order. "Nothing was found" and "the
      # server was started somewhere else" wear one sentence, and only this tells
      # them apart. Empty where discovery never ran.
      searched: [string]
      # What was set and could not be used. Null where nothing was set.
      misconfiguration: string or null  # optional
      settings:
        # Environment variable that names the installation root.
        root: string
        # Environment variable that names the console command.
        console: string

The answer carries exactly one of these sets of fields: ``key``, ``path``,
``origin``, ``tcaTables``, ``tcaOverrides``, ``contentElements``,
``renderedContentTypes``, ``pluginFrame``, ``unlistedFlexForms``,
``backendModules``, ``icons``, ``siteSets``, ``formConfigurations``,
``serviceTags``, ``files``, ``deprecatedFiles``, ``notReadStatically``,
``artifacts``, ``answeredBy`` — or ``key``, ``unsupported``.

Answered
--------

Recorded on 2026-09-04 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.7-dev, the 14.3 core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Its dependencies are not installed —
vendor/autoload.php is not there either, and composer install writes both.
Answered against composer-project, TYPO3 14.3.0, the installation this
repository writes below .fixtures/, whose console answers. The tools that
declare ``answeredBy`` carry an answer from each, under a heading naming which;
every other answer is from the first alone, because nothing in it would differ.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

extension
~~~~~~~~~

Called with:

.. code-block:: json

    {
        "extension": "backend"
    }

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    backend (system) — <installation>/typo3/sysext/backend
    TYPO3 CMS Backend

    TCA tables it extends: be_users

    Backend modules: web_layout, records, content_status, site_configuration, link_management, about, pagetsconfig, pagetsconfig_pages, pagetsconfig_active, pagetsconfig_includes, content_security_policy, user_setup

    Backend routes: login, main, state-tracker, logout, password_forget, password_forget_initiate_reset, password_reset_validate, password_reset_finish, sudo_mode_module, sudo_mode_apply, sudo_mode_error, login_frameset, login_request_token, auth_mfa, setup_mfa, mfa, wizard_add, wizard_list, wizard_edit, wizard_element_browser, wizard_link, online_media, record_download, record_history, db_new, db_new_pages, pages_sort, pages_new, new_content_element_wizard, move_page, move_element, show_item, dummy, tce_db, tce_file, record_edit, record_edit_contextual, image_processing, clipboard_process, resource_request_thumbnail, language_domain, resource_rename, resource_gather, resource_replace, link_resource, file_process, file_exists, file_reference_details, file_reference_create, file_reference_synchronizelocalize, file_reference_expandcollapse, record_inline_details, record_inline_create, record_inline_synchronizelocalize, record_inline_expandcollapse, site_configuration_inline_create, record_slug_suggest, site_configuration_inline_details, record_flex_container_add, record_suggest, record_tree_data, page_tree_data, page_tree_rootline, page_tree_filter, page_tree_configuration, page_tree_browser_configuration, page_tree_set_temporary_mount_point, filestorage_tree_data, filestorage_tree_rootline, filestorage_tree_filter, bookmark_list, bookmark_create, bookmark_update, bookmark_delete, bookmark_reorder, bookmark_delete_multiple, bookmark_move, bookmark_group_create, bookmark_group_update, bookmark_group_delete, bookmark_group_reorder, clearcache_group_pages, clearcache_group_all, clearcache_page, systeminformation_render, modulemenu, topbar, login, logout, login_preflight, login_refresh, login_timedout, switch_user, switch_user_exit, mfa, contextmenu, contextmenu_clipboard, record_process, usersettings_process, wizard_image_manipulation, livesearch, livesearch_form, online_media_create, icons, link_browser_encodetypolink, wizard_localization_get_record, wizard_localization_get_targets, wizard_localization_get_sources, wizard_localization_get_modes, wizard_localization_get_handlers, wizard_localization_get_content, wizard_localization_localize, show_columns, show_columns_selector, record_download_settings, record_toggle_visibility, password_generate, security_csp_control, sudo_mode_control, codeeditor_tsref, codeeditor_codecompletion_loadtemplates, color_scheme_update, qrcode_generator, qrcode_download, wizard_page_get_doktypes, wizard_page_get_page_detail, wizard_page_get_processed_value, wizard_config, wizard_submit

    Middlewares: typo3/cms-core/normalized-params-attribute, typo3/cms-backend/locked-backend, typo3/cms-backend/https-redirector, typo3/cms-backend/csp-report, typo3/cms-backend/backend-routing, typo3/cms-core/request-token-middleware, typo3/cms-backend/authentication, typo3/cms-backend/backend-module-validator, typo3/cms-backend/sudo-mode-interceptor, typo3/cms-backend/site-resolver, typo3/cms-backend/page-context, typo3/cms-backend/csp-headers, typo3/cms-backend/js-label-importmap-resolver, typo3/cms-backend/response-headers, typo3/cms-core/response-propagation

    Registration files: ext_localconf.php, ext_tables.sql, Configuration/page.tsconfig, Configuration/user.tsconfig, Configuration/RequestMiddlewares.php, Configuration/Services.yaml, Configuration/JavaScriptModules.php
    typo3_schema_lookup with one of those table names answers what columns the core derives for it, with the type, the default and the length of each. That is the resolved schema rather than what the TCA file declares, and two tables generated from one TCA do not have to agree on the order they come back in.

    Fluid root directories it ships: Resources/Private/Templates/, Resources/Private/Partials/, Resources/Private/Layouts/
    Each is a directory that is there rather than a root something declared. An Extbase controller of this extension falls back to these three; every other view is pointed at a root by TypoScript or by a call while the request runs, and neither of those is in this list.

    Classes: Attribute (3), Authentication (4), Backend (22), Breadcrumb (6), Clipboard (2), CodeEditor (6), Command (8), Configuration (5), Context (2), ContextMenu (7), Controller (90), Date (3), DependencyInjection (3), Domain (5), Dto (9), ElementBrowser (6), Event (1), EventListener (4), Exception (8), Form (203), History (4), Hooks (2), Http (4), LinkHandler (9), Localization (11), LoginProvider (4), Middleware (12), Module (17), Preview (5), RecordList (10), Resource (2), Routing (15), Search (22), Security (18), Service (2), Sidebar (7), Template (41), Toolbar (4), Tree (21), Upgrades (3), User (1), UserFunctions (1), Utility (1), View (36), ViewHelpers (15), Wizard (8), 2 directly in Classes/ — 674 PHP files in total.
    Every directory below Classes/ is named here, and each count is every PHP file below that directory, its own subdirectories included. The total is what `find Classes -name '*.php' | wc -l` gives.

    Requires: ext-intl *, ext-libxml *, psr/event-dispatcher ^1.0, typo3/cms-core 14.3.*@dev

    Ships: manual none, readme README.rst, tests Functional+Unit, language files 49
    - Resources/Private/Language/Modules/about.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Modules/content-security-policy.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Modules/layout.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Modules/link_management.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Modules/list.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Modules/pagetsconfig.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Modules/pagetsconfig_active.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Modules/pagetsconfig_includes.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Modules/pagetsconfig_pages.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Modules/site_configuration.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Modules/site_settings.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Modules/status.xlf — source-language not declared, no translations beside it
    - Resources/Private/Language/Modules/user_settings.xlf — source-language en, no translations beside it
    - Resources/Private/Language/SudoMode.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Wizards/general.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Wizards/localization.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Wizards/move_content_elements.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Wizards/move_page.xlf — source-language en, no translations beside it
    - Resources/Private/Language/Wizards/page.xlf — source-language en, no translations beside it
    - Resources/Private/Language/links.xlf — source-language not declared, no translations beside it
    - Resources/Private/Language/locallang.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_alt_doc.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_browse_links.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_codeeditor.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_column_selector.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_copytoclipboard.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_download.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_layout.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_login.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_mfa.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_mod.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_pages_new.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_pages_sort.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_pagetsconfig.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_reset_password.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_resource.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_settingseditor.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_show_rechis.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_siteconfiguration.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_siteconfiguration_module.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_siteconfiguration_tca.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_sitesettings.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_sitesettings_module.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_toolbar.xlf — source-language en, no translations beside it
    - Resources/Private/Language/locallang_view_help.xlf — source-language en, no translations beside it
    - Resources/Private/Language/pages/messages.xlf — source-language not declared, no translations beside it
    - Resources/Private/Language/qrcode.xlf — source-language en, no translations beside it
    - Resources/Private/Language/siteconfiguration_fieldinformation.xlf — source-language en, no translations beside it
    - Resources/Private/Language/user_profile.xlf — source-language en, no translations beside it
    The source language is what each file declares, not what it should declare — typo3_hint_lookup owns that rule.
    It ships no manual: `typo3-extension-documentation` is the workflow that writes one.

    Read from the files, so this is what the extension declares — not what it does at runtime. A table or an icon list built in a loop, and anything a hook or an event listener changes, are not in this list; the files that could hold them are named above. The installation itself was not asked: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both.
    ext_localconf.php is named above and read by nothing here. Each registers by running, so a hook, an RTE preset or a global Fluid namespace it sets is in none of the lists above. The booted installation answers the tables, content elements and icons it adds, and none of the rest.

Data:

.. code-block:: json

    {
        "key": "backend",
        "path": "<installation>/typo3/sysext/backend",
        "origin": "system",
        "composerName": "typo3/cms-backend",
        "description": "TYPO3 CMS Backend",
        "requires": [
            {
                "package": "ext-intl",
                "constraint": "*"
            },
            {
                "package": "ext-libxml",
                "constraint": "*"
            },
            {
                "package": "psr/event-dispatcher",
                "constraint": "^1.0"
            },
            {
                "package": "typo3/cms-core",
                "constraint": "14.3.*@dev"
            }
        ],
        "tcaTables": [],
        "tcaOverrides": [
            "be_users"
        ],
        "contentElements": [],
        "renderedContentTypes": [],
        "pluginFrame": null,
        "unlistedFlexForms": [],
        "backendModules": [
            "web_layout",
            "records",
            "content_status",
            "site_configuration",
            "link_management",
            "about",
            "pagetsconfig",
            "pagetsconfig_pages",
            "pagetsconfig_active",
            "pagetsconfig_includes",
            "content_security_policy",
            "user_setup"
        ],
        "backendRoutes": [
            "login",
            "main",
            "state-tracker",
            "logout",
            "password_forget",
            "password_forget_initiate_reset",
            "password_reset_validate",
            "password_reset_finish",
            "sudo_mode_module",
            "sudo_mode_apply",
            "sudo_mode_error",
            "login_frameset",
            "login_request_token",
            "auth_mfa",
            "setup_mfa",
            "mfa",
            "wizard_add",
            "wizard_list",
            "wizard_edit",
            "wizard_element_browser",
            "wizard_link",
            "online_media",
            "record_download",
            "record_history",
            "db_new",
            "db_new_pages",
            "pages_sort",
            "pages_new",
            "new_content_element_wizard",
            "move_page",
            "move_element",
            "show_item",
            "dummy",
            "tce_db",
            "tce_file",
            "record_edit",
            "record_edit_contextual",
            "image_processing",
            "clipboard_process",
            "resource_request_thumbnail",
            "language_domain",
            "resource_rename",
            "resource_gather",
            "resource_replace",
            "link_resource",
            "file_process",
            "file_exists",
            "file_reference_details",
            "file_reference_create",
            "file_reference_synchronizelocalize",
            "file_reference_expandcollapse",
            "record_inline_details",
            "record_inline_create",
            "record_inline_synchronizelocalize",
            "record_inline_expandcollapse",
            "site_configuration_inline_create",
            "record_slug_suggest",
            "site_configuration_inline_details",
            "record_flex_container_add",
            "record_suggest",
            "record_tree_data",
            "page_tree_data",
            "page_tree_rootline",
            "page_tree_filter",
            "page_tree_configuration",
            "page_tree_browser_configuration",
            "page_tree_set_temporary_mount_point",
            "filestorage_tree_data",
            "filestorage_tree_rootline",
            "filestorage_tree_filter",
            "bookmark_list",
            "bookmark_create",
            "bookmark_update",
            "bookmark_delete",
            "bookmark_reorder",
            "bookmark_delete_multiple",
            "bookmark_move",
            "bookmark_group_create",
            "bookmark_group_update",
            "bookmark_group_delete",
            "bookmark_group_reorder",
            "clearcache_group_pages",
            "clearcache_group_all",
            "clearcache_page",
            "systeminformation_render",
            "modulemenu",
            "topbar",
            "login",
            "logout",
            "login_preflight",
            "login_refresh",
            "login_timedout",
            "switch_user",
            "switch_user_exit",
            "mfa",
            "contextmenu",
            "contextmenu_clipboard",
            "record_process",
            "usersettings_process",
            "wizard_image_manipulation",
            "livesearch",
            "livesearch_form",
            "online_media_create",
            "icons",
            "link_browser_encodetypolink",
            "wizard_localization_get_record",
            "wizard_localization_get_targets",
            "wizard_localization_get_sources",
            "wizard_localization_get_modes",
            "wizard_localization_get_handlers",
            "wizard_localization_get_content",
            "wizard_localization_localize",
            "show_columns",
            "show_columns_selector",
            "record_download_settings",
            "record_toggle_visibility",
            "password_generate",
            "security_csp_control",
            "sudo_mode_control",
            "codeeditor_tsref",
            "codeeditor_codecompletion_loadtemplates",
            "color_scheme_update",
            "qrcode_generator",
            "qrcode_download",
            "wizard_page_get_doktypes",
            "wizard_page_get_page_detail",
            "wizard_page_get_processed_value",
            "wizard_config",
            "wizard_submit"
        ],
        "icons": [],
        "siteSets": [],
        "formConfigurations": [],
        "middlewares": [
            "typo3/cms-core/normalized-params-attribute",
            "typo3/cms-backend/locked-backend",
            "typo3/cms-backend/https-redirector",
            "typo3/cms-backend/csp-report",
            "typo3/cms-backend/backend-routing",
            "typo3/cms-core/request-token-middleware",
            "typo3/cms-backend/authentication",
            "typo3/cms-backend/backend-module-validator",
            "typo3/cms-backend/sudo-mode-interceptor",
            "typo3/cms-backend/site-resolver",
            "typo3/cms-backend/page-context",
            "typo3/cms-backend/csp-headers",
            "typo3/cms-backend/js-label-importmap-resolver",
            "typo3/cms-backend/response-headers",
            "typo3/cms-core/response-propagation"
        ],
        "serviceTags": [],
        "fluidRoots": [
            "Resources/Private/Templates/",
            "Resources/Private/Partials/",
            "Resources/Private/Layouts/"
        ],
        "fluidNamespaces": [],
        "typoScript": [],
        "classes": {
            "directories": [
                {
                    "name": "Attribute",
                    "files": 3
                },
                {
                    "name": "Authentication",
                    "files": 4
                },
                {
                    "name": "Backend",
                    "files": 22
                },
                {
                    "name": "Breadcrumb",
                    "files": 6
                },
                {
                    "name": "Clipboard",
                    "files": 2
                },
                {
                    "name": "CodeEditor",
                    "files": 6
                },
                {
                    "name": "Command",
                    "files": 8
                },
                {
                    "name": "Configuration",
                    "files": 5
                },
                {
                    "name": "Context",
                    "files": 2
                },
                {
                    "name": "ContextMenu",
                    "files": 7
                },
                {
                    "name": "Controller",
                    "files": 90
                },
                {
                    "name": "Date",
                    "files": 3
                },
                {
                    "name": "DependencyInjection",
                    "files": 3
                },
                {
                    "name": "Domain",
                    "files": 5
                },
                {
                    "name": "Dto",
                    "files": 9
                },
                {
                    "name": "ElementBrowser",
                    "files": 6
                },
                {
                    "name": "Event",
                    "files": 1
                },
                {
                    "name": "EventListener",
                    "files": 4
                },
                {
                    "name": "Exception",
                    "files": 8
                },
                {
                    "name": "Form",
                    "files": 203
                },
                {
                    "name": "History",
                    "files": 4
                },
                {
                    "name": "Hooks",
                    "files": 2
                },
                {
                    "name": "Http",
                    "files": 4
                },
                {
                    "name": "LinkHandler",
                    "files": 9
                },
                {
                    "name": "Localization",
                    "files": 11
                },
                {
                    "name": "LoginProvider",
                    "files": 4
                },
                {
                    "name": "Middleware",
                    "files": 12
                },
                {
                    "name": "Module",
                    "files": 17
                },
                {
                    "name": "Preview",
                    "files": 5
                },
                {
                    "name": "RecordList",
                    "files": 10
                },
                {
                    "name": "Resource",
                    "files": 2
                },
                {
                    "name": "Routing",
                    "files": 15
                },
                {
                    "name": "Search",
                    "files": 22
                },
                {
                    "name": "Security",
                    "files": 18
                },
                {
                    "name": "Service",
                    "files": 2
                },
                {
                    "name": "Sidebar",
                    "files": 7
                },
                {
                    "name": "Template",
                    "files": 41
                },
                {
                    "name": "Toolbar",
                    "files": 4
                },
                {
                    "name": "Tree",
                    "files": 21
                },
                {
                    "name": "Upgrades",
                    "files": 3
                },
                {
                    "name": "User",
                    "files": 1
                },
                {
                    "name": "UserFunctions",
                    "files": 1
                },
                {
                    "name": "Utility",
                    "files": 1
                },
                {
                    "name": "View",
                    "files": 36
                },
                {
                    "name": "ViewHelpers",
                    "files": 15
                },
                {
                    "name": "Wizard",
                    "files": 8
                }
            ],
            "looseFiles": 2,
            "total": 674
        },
        "files": [
            "ext_localconf.php",
            "ext_tables.sql",
            "Configuration/page.tsconfig",
            "Configuration/user.tsconfig",
            "Configuration/RequestMiddlewares.php",
            "Configuration/Services.yaml",
            "Configuration/JavaScriptModules.php"
        ],
        "deprecatedFiles": [],
        "notReadStatically": [],
        "artifacts": {
            "manual": null,
            "readme": "README.rst",
            "tests": [
                "Functional",
                "Unit"
            ],
            "languageFiles": [
                {
                    "path": "Resources/Private/Language/Modules/about.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/content-security-policy.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/layout.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/link_management.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/list.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/pagetsconfig.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/pagetsconfig_active.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/pagetsconfig_includes.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/pagetsconfig_pages.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/site_configuration.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/site_settings.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/status.xlf",
                    "sourceLanguage": null,
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Modules/user_settings.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/SudoMode.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Wizards/general.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Wizards/localization.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Wizards/move_content_elements.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Wizards/move_page.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/Wizards/page.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/links.xlf",
                    "sourceLanguage": null,
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_alt_doc.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_browse_links.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_codeeditor.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_column_selector.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_copytoclipboard.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_download.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_layout.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_login.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_mfa.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_mod.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_pages_new.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_pages_sort.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_pagetsconfig.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_reset_password.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_resource.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_settingseditor.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_show_rechis.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_siteconfiguration.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_siteconfiguration_module.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_siteconfiguration_tca.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_sitesettings.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_sitesettings_module.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_toolbar.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/locallang_view_help.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/pages/messages.xlf",
                    "sourceLanguage": null,
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/qrcode.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/siteconfiguration_fieldinformation.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                },
                {
                    "path": "Resources/Private/Language/user_profile.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                }
            ]
        },
        "answeredBy": "packages"
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    backend (system) — <installation>/vendor/typo3/cms-backend
    The fixture installation's backend package.

    Backend modules: web_list, acme_events

    Backend routes: login, main

    Icons: actions-close, actions-document-open, actions-open

    Service tags: backend.controller

    Registration files: Configuration/Services.yaml

    Classes: Controller (1) — 1 PHP file in total.
    Every directory below Classes/ is named here, and each count is every PHP file below that directory, its own subdirectories included. The total is what `find Classes -name '*.php' | wc -l` gives.

    Ships: manual none, readme none, tests none, language files 1
    - Resources/Private/Language/locallang.xlf — source-language en, no translations beside it
    The source language is what each file declares, not what it should declare — typo3_hint_lookup owns that rule.
    It ships no manual and no README: `typo3-extension-documentation` is the workflow that writes them.
    It ships no test: `typo3-extension-testing` is the workflow that sets the first one up.

    The tables, content elements and icons are what the booted installation has, attributed to this extension by the EXT: reference each entry carries; everything else is read from its files. What a hook or an event listener changes at request time is in neither.

Data:

.. code-block:: json

    {
        "key": "backend",
        "path": "<installation>/vendor/typo3/cms-backend",
        "origin": "system",
        "composerName": "typo3/cms-backend",
        "description": "The fixture installation's backend package.",
        "requires": [],
        "tcaTables": [],
        "tcaOverrides": [],
        "contentElements": [],
        "renderedContentTypes": [],
        "pluginFrame": null,
        "unlistedFlexForms": [],
        "backendModules": [
            "web_list",
            "acme_events"
        ],
        "backendRoutes": [
            "login",
            "main"
        ],
        "icons": [
            "actions-close",
            "actions-document-open",
            "actions-open"
        ],
        "siteSets": [],
        "formConfigurations": [],
        "middlewares": [],
        "serviceTags": [
            "backend.controller"
        ],
        "fluidRoots": [],
        "fluidNamespaces": [],
        "typoScript": [],
        "classes": {
            "directories": [
                {
                    "name": "Controller",
                    "files": 1
                }
            ],
            "looseFiles": 0,
            "total": 1
        },
        "files": [
            "Configuration/Services.yaml"
        ],
        "deprecatedFiles": [],
        "notReadStatically": [],
        "artifacts": {
            "manual": null,
            "readme": null,
            "tests": [],
            "languageFiles": [
                {
                    "path": "Resources/Private/Language/locallang.xlf",
                    "sourceLanguage": "en",
                    "translations": []
                }
            ]
        },
        "answeredBy": "installation"
    }
