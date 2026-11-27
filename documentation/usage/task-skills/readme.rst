:navigation-title: Task skills

The installed task skills
=========================

This page covers the task skills that the install publishes beside the server
configuration. It lists every published workflow, and each name opens the
complete workflow.

A client chooses a skill by its description, loads it for the task, and follows
the order of steps it defines. The MCP tools supply the facts about the project
and TYPO3 that this order needs.

``install`` writes this set, and ``update`` refreshes it. The path depends on
the client, and :doc:`../installing` lists it. The generic install writes the
set below ``.agents/skills``.

Each installed directory also gets ``references/base.md``, the shared start of
every workflow. So a copy of the source directory by hand does not give the same
skill.

The install publishes every skill this server has, and this page lists all of
them. :doc:`../checking-it-answers` explains how to tell a stale copy from a
workflow the client did not activate.

Published workflows
-------------------

Each name opens one page. That page contains the unchanged Markdown workflow and
every reference the workflow names for the agent. The description after the name
is the selection description of the skill.

Only the published site has the list. In the file in the repository, this page
ends here. `skills/ <../../../skills/>`_ has the same workflows.

.. The list and its pages are written into the published copy by
   ``bin/cli documentation:prepare``.
