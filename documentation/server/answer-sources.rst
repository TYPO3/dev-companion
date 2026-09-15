:navigation-title: Answer sources

.. _answer-sources:

Where an answer comes from
==========================

Every tool declares which sources can answer it, and says so at the foot of its
own description and on its page here. What that answers is whether a caller can
ask a tool at all right now, not what the tool is about. With nothing up, the
tools under knowledge and packages are the ones still worth a call. Which source
answered one call is ``answeredBy`` in that answer, where the tool has two.
``bin/cli tools:index`` writes this page from the Source enum.

.. image:: ../images/answer-sources.svg
    :zoomable:
    :alt: The five sources against how much of the machine has to run. Bundled knowledge
          and this server's own checkout answer with nothing up. Packages need
          files on disk, the installation source needs a booted installation,
          and network sources need outbound reach.

.. _answer-sources-installation:

installation
------------

The installation this server started in, booted or asked through its console.
Its assembled state after every extension has had its say, and nothing at all
where it is out of reach.

:doc:`typo3_backend_module_lookup <tools/typo3_backend_module_lookup>`,
:doc:`typo3_configuration_lookup <tools/typo3_configuration_lookup>`,
:doc:`typo3_extension_describe <tools/typo3_extension_describe>`,
:doc:`typo3_flexform_lookup <tools/typo3_flexform_lookup>`,
:doc:`typo3_fluid_namespace_list <tools/typo3_fluid_namespace_list>`,
:doc:`typo3_icon_lookup <tools/typo3_icon_lookup>`,
:doc:`typo3_label_lookup <tools/typo3_label_lookup>`,
:doc:`typo3_record_lookup <tools/typo3_record_lookup>`,
:doc:`typo3_schema_lookup <tools/typo3_schema_lookup>`,
:doc:`typo3_server_scope <tools/typo3_server_scope>`,
:doc:`typo3_service_lookup <tools/typo3_service_lookup>`.

.. _answer-sources-packages:

packages
--------

The files the installed packages ship, read rather than executed. Answers on a
fresh clone and with the containers down. What a package registers at runtime is
not in it.

:doc:`typo3_changelog_lookup <tools/typo3_changelog_lookup>`,
:doc:`typo3_component_lookup <tools/typo3_component_lookup>`,
:doc:`typo3_extension_describe <tools/typo3_extension_describe>`,
:doc:`typo3_fluid_namespace_list <tools/typo3_fluid_namespace_list>`,
:doc:`typo3_forge_lookup <tools/typo3_forge_lookup>`,
:doc:`typo3_icon_lookup <tools/typo3_icon_lookup>`,
:doc:`typo3_label_lookup <tools/typo3_label_lookup>`,
:doc:`typo3_project_describe <tools/typo3_project_describe>`,
:doc:`typo3_snapshot_scope <tools/typo3_snapshot_scope>`.

.. _answer-sources-knowledge:

knowledge
---------

The knowledge base inside this package. Needs nothing up, and binds to TYPO3
versions rather than to an installation.

:doc:`typo3_commit_message_guide <tools/typo3_commit_message_guide>`,
:doc:`typo3_component_lookup <tools/typo3_component_lookup>`,
:doc:`typo3_hint_lookup <tools/typo3_hint_lookup>`,
:doc:`typo3_reference_list <tools/typo3_reference_list>`,
:doc:`typo3_rule_lookup <tools/typo3_rule_lookup>`,
:doc:`typo3_script_lookup <tools/typo3_script_lookup>`,
:doc:`typo3_server_scope <tools/typo3_server_scope>`,
:doc:`typo3_snapshot_scope <tools/typo3_snapshot_scope>`,
:doc:`typo3_system_extension_lookup <tools/typo3_system_extension_lookup>`,
:doc:`typo3_task_guide <tools/typo3_task_guide>`,
:doc:`typo3_test_run_guide <tools/typo3_test_run_guide>`,
:doc:`typo3_translation_domain_lookup <tools/typo3_translation_domain_lookup>`.

.. _answer-sources-network:

network
-------

A service outside this machine. An unreachable one says so out loud rather than
answers as empty.

:doc:`typo3_changelog_lookup <tools/typo3_changelog_lookup>`,
:doc:`typo3_documentation_lookup <tools/typo3_documentation_lookup>`,
:doc:`typo3_forge_lookup <tools/typo3_forge_lookup>`,
:doc:`typo3_gerrit_lookup <tools/typo3_gerrit_lookup>`,
:doc:`typo3_permalink_lookup <tools/typo3_permalink_lookup>`,
:doc:`typo3_ter_lookup <tools/typo3_ter_lookup>`.

.. _answer-sources-checkout:

checkout
--------

This server's own checkout, which is why the tool offering it exists only in a
standalone one.

:doc:`typo3_feedback_list <tools/typo3_feedback_list>`,
:doc:`typo3_feedback_record <tools/typo3_feedback_record>`.
