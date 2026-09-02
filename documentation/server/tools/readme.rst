:navigation-title: Tools

The tool surface
================

Every tool this server offers, one page each. A page says what the tool is
called, what it takes, the fields it answers with and what a call to it comes
back with, and this is the list of them.

Every tool answers twice: the readable text, and the same answer as
``structuredContent`` matching the ``outputSchema`` the tool declares — matches with
their source, coverage and score, checks as command strings, components, icons
and labels as typed records, commit diagnostics as ``level``/``code``/``message``. So
composing several tools does not mean parsing headings and code fences back out
of prose. All tools are annotated ``readOnlyHint``; only ``typo3_feedback_record``
writes anything, and then only a new file.

Names are ``typo3_<subject>_<verb>``, with the verb taken from a fixed set —
``lookup`` finds and may find nothing, ``guide`` composes an answer for a task,
``list`` enumerates, ``scope`` states what a source covers, ``describe`` states what
one thing you name is, ``record`` writes. So the name already says what shape the
answer has.

The half of a page above its ``Answered`` heading is written by ``bin/cli
tools:index`` from the classes that answer the calls, and ``bin/cli tools:check``
fails where it has gone stale — a surface written out a second time by hand
stops describing the answer at the first change nobody carried across. What is
below that heading is one of two things, and the sentence it opens with says
which. Where a tool's answers read nothing an installation contains, they are
derived and held by that same check. Where they do, an installation has to be
called for them: ``bin/cli tools:record`` writes those and nothing checks them, so
such a page may say what it answered on a day the code has since moved past. The
check counts how many of them were answered before this repository last changed
``knowledge/`` or ``src/``, and fails on none of them. Two tools have no answered
half at all, on purpose, and say so in its place.

A client may be offered fewer than these. ``TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`` names the
tools a caller does not want offered, the two feedback tools exist only in a
standalone checkout, and ``typo3_server_scope`` names what was left out.

The schema on a page is YAML: a key per field, the fields of an object or of a
list entry nested under it, and the value is the type. A field carries
``# optional`` where it may be absent, because required is the promise — a
required output field is present on every path through the tool, misses
included. Absolute paths in a recorded answer are written as ``<repository>``,
``<installation>`` and ``<home>``, so no page carries one machine's layout.

Each page names the sources that can answer that tool, under its annotations and
at the foot of its description, and links them into
:doc:`Where an answer comes from <../answer-sources>` — which is the same
statement read the other way round, one heading per source with the tools it
answers. What it settles is not what a tool is about but whether it can be asked
at all in the state the machine is in.

.. The cards below are written by ``bin/cli tools:index``.

.. grid:: wide

    .. card:: :doc:`typo3_backend_module_lookup <typo3_backend_module_lookup>`
        :label: Lookup
        :action: Open reference

        List the backend modules registered in the TYPO3 installation you are
        working in, with the extension that declares each one, its place in the
        module tree, its labels, its access level, the route each one answers on
        and every sub-route it registers.

    .. card:: :doc:`typo3_changelog_lookup <typo3_changelog_lookup>`
        :label: Lookup
        :action: Open reference

        Search the TYPO3 changelog.

    .. card:: :doc:`typo3_commit_message_guide <typo3_commit_message_guide>`
        :label: Guide
        :action: Open reference

        Draft and check a TYPO3 commit message.

    .. card:: :doc:`typo3_component_lookup <typo3_component_lookup>`
        :label: Lookup
        :action: Open reference

        Look up TYPO3 backend UI components by name or topic.

    .. card:: :doc:`typo3_configuration_lookup <typo3_configuration_lookup>`
        :label: Lookup
        :action: Open reference

        Read an effective TYPO3_CONF_VARS value from the installation you are
        working in.

    .. card:: :doc:`typo3_documentation_lookup <typo3_documentation_lookup>`
        :label: Lookup
        :action: Open reference

        Search or read the official live TYPO3 documentation for a covered TYPO3
        line.

    .. card:: :doc:`typo3_extension_describe <typo3_extension_describe>`
        :label: Describe
        :action: Open reference

        Describe what one installed extension registers.

    .. card:: :doc:`typo3_feedback_list <typo3_feedback_list>`
        :label: List
        :action: Open reference

        List improvement feedback recorded via typo3_feedback_record, newest
        first, so they can be worked off.

    .. card:: :doc:`typo3_feedback_record <typo3_feedback_record>`
        :label: Record
        :action: Open reference

        Leave feedback about a gap, wrong answer, or missing capability of this
        knowledge server.

    .. card:: :doc:`typo3_flexform_lookup <typo3_flexform_lookup>`
        :label: Lookup
        :action: Open reference

        Resolve one TCA field of type=flex to the data structure the
        installation would actually use.

    .. card:: :doc:`typo3_fluid_namespace_list <typo3_fluid_namespace_list>`
        :label: List
        :action: Open reference

        List the Fluid ViewHelper namespaces that are globally available in the
        TYPO3 installation you are working in, so a template knows which
        prefixes it may use without declaring them.

    .. card:: :doc:`typo3_forge_lookup <typo3_forge_lookup>`
        :label: Lookup
        :action: Open reference

        Reads the TYPO3 issue tracker at forge.typo3.org through the bot
        protection the core's own AGENTS.md warns a hand-written request about.

    .. card:: :doc:`typo3_gerrit_lookup <typo3_gerrit_lookup>`
        :label: Lookup
        :action: Open reference

        Find out whether a TYPO3 core patch already exists and what state its
        review is in, from the review server at review.typo3.org.

    .. card:: :doc:`typo3_hint_lookup <typo3_hint_lookup>`
        :label: Lookup
        :action: Open reference

        Return hints for TYPO3 core paths or task topics, grouped by section.

    .. card:: :doc:`typo3_icon_lookup <typo3_icon_lookup>`
        :label: Lookup
        :action: Open reference

        Validate or find icon identifiers in the TYPO3 backend icon registry of
        the installation you are working in.

    .. card:: :doc:`typo3_label_lookup <typo3_label_lookup>`
        :label: Lookup
        :action: Open reference

        Search the labels registered in the TYPO3 installation you are working
        in and the XLF files below project config/sites.

    .. card:: :doc:`typo3_permalink_lookup <typo3_permalink_lookup>`
        :label: Lookup
        :action: Open reference

        Validate docs.typo3.org permalink identifiers and turn old documentation
        URLs into the identifiers that replace them.

    .. card:: :doc:`typo3_project_describe <typo3_project_describe>`
        :label: Describe
        :action: Open reference

        Describe the repository this server was started in and the TYPO3
        installation it has made.

    .. card:: :doc:`typo3_record_lookup <typo3_record_lookup>`
        :label: Lookup
        :action: Open reference

        Read the rows of a table belonging to one of this project's own
        extensions.

    .. card:: :doc:`typo3_reference_list <typo3_reference_list>`
        :label: List
        :action: Open reference

        List the worked examples the TYPO3 core ships of its own conventions,
        and what each one is a reference for.

    .. card:: :doc:`typo3_rule_lookup <typo3_rule_lookup>`
        :label: Lookup
        :action: Open reference

        Search the TYPO3 rules and procedures this server carries, by topic.

    .. card:: :doc:`typo3_schema_lookup <typo3_schema_lookup>`
        :label: Lookup
        :action: Open reference

        List the columns TYPO3 derives for a table from its TCA.

    .. card:: :doc:`typo3_script_lookup <typo3_script_lookup>`
        :label: Lookup
        :action: Open reference

        Find notes for TYPO3 core scripts and commands.

    .. card:: :doc:`typo3_server_scope <typo3_server_scope>`
        :label: Scope
        :action: Open reference

        Orientation for this server.

    .. card:: :doc:`typo3_service_lookup <typo3_service_lookup>`
        :label: Lookup
        :action: Open reference

        Find what the dependency injection container of the TYPO3 installation
        you are working in assembles.

    .. card:: :doc:`typo3_snapshot_scope <typo3_snapshot_scope>`
        :label: Scope
        :action: Open reference

        Report whether component contracts come from the active installation or
        the bundled fallback, which TYPO3 core revision the fallback catalogs
        were taken from, what they cover, and how to re-check them.

    .. card:: :doc:`typo3_system_extension_lookup <typo3_system_extension_lookup>`
        :label: Lookup
        :action: Open reference

        Answer whether an extension is part of the TYPO3 core, and on which
        versions.

    .. card:: :doc:`typo3_task_guide <typo3_task_guide>`
        :label: Guide
        :action: Open reference

        Answers what one change owes, which a repository's own conventions file
        cannot.

    .. card:: :doc:`typo3_ter_lookup <typo3_ter_lookup>`
        :label: Lookup
        :action: Open reference

        Read what the TYPO3 Extension Repository has published under an
        extension key, live from extensions.typo3.org.

    .. card:: :doc:`typo3_test_run_guide <typo3_test_run_guide>`
        :label: Guide
        :action: Open reference

        Say what this core checkout needs before a test can run at all, and
        which Build/Scripts/runTests.sh commands to run once it can.

    .. card:: :doc:`typo3_translation_domain_lookup <typo3_translation_domain_lookup>`
        :label: Lookup
        :action: Open reference

        Compute the translation domain an XLF file resolves to, from its path.

.. toctree::
    :hidden:

    typo3_backend_module_lookup
    typo3_changelog_lookup
    typo3_commit_message_guide
    typo3_component_lookup
    typo3_configuration_lookup
    typo3_documentation_lookup
    typo3_extension_describe
    typo3_feedback_list
    typo3_feedback_record
    typo3_flexform_lookup
    typo3_fluid_namespace_list
    typo3_forge_lookup
    typo3_gerrit_lookup
    typo3_hint_lookup
    typo3_icon_lookup
    typo3_label_lookup
    typo3_permalink_lookup
    typo3_project_describe
    typo3_record_lookup
    typo3_reference_list
    typo3_rule_lookup
    typo3_schema_lookup
    typo3_script_lookup
    typo3_server_scope
    typo3_service_lookup
    typo3_snapshot_scope
    typo3_system_extension_lookup
    typo3_task_guide
    typo3_ter_lookup
    typo3_test_run_guide
    typo3_translation_domain_lookup
