:navigation-title: Entry paths

.. _installing-which-path-is-written:

Which path the entry names
==========================

This page covers which path ``install`` writes into the client entry, and why it
is an absolute path for most clients. :doc:`installing` is the page for the
install itself.

Where the project has this server as a Composer dependency or is its checkout,
the entry names the entrypoint inside the project. It does that in one of three
ways:

* through ``ddev exec``, where the project has a DDEV configuration;

* with ``${workspaceFolder}``, in VS Code and Cursor;

* by its absolute path, in every other client.

In every other project, the entry names the absolute path of this server, on the
machine where ``install`` ran.

The absolute path is right on one machine only. It is in a file that the client
documents as shared and committed. ``install`` says so at the terminal. The rest
of this section says why nothing better exists.

A relative path resolves against the working directory the client starts the
process in. The MCP specification does not define that directory. The stdio
transport says only "the client launches the MCP server as a subprocess". So
each client decides. A session read the table below on 2026-08-09, from the same
pages as :ref:`installing-finishing-in-the-client`.

===========  =====================================================  ===============================================================
Client       Working directory                                      Project root in ``command``/``args``
===========  =====================================================  ===============================================================
VS Code      ``cwd``, "defaults to the workspace folder"            ``${workspaceFolder}``
opencode     ``cwd``, relative paths "resolve from the workspace"   none documented
Codex        ``cwd``, "working directory to start the server from"  not documented
Cursor       not documented                                         ``${workspaceFolder}``, in both fields
Claude Code  not documented, and advised against                    needs ``${CLAUDE_PROJECT_DIR:-.}``
Grok         not documented                                         ``${VAR}`` expands, no root variable
Amp          not documented                                         ``${VAR}`` for environment values only
Kiro         not documented                                         ``${VAR}`` shown for ``env`` only
Junie        not documented                                         not documented
Zed          not documented                                         not documented
Droid        not documented                                         expansion "does not apply to ``command``, ``args``, or ``url``"
Antigravity  ``cwd``, else the plugin's own directory               not documented
===========  =====================================================  ===============================================================

One client documents the workspace as the default, and four let you set a
``cwd``. Two resolve a variable for the project root. One refuses expansion in
those fields.

Claude Code says the most. It sets ``CLAUDE_PROJECT_DIR`` in the environment of
the server it starts, "so your server can resolve project-relative paths
**without depending on the working directory**". But the same variable in a
project ``.mcp.json`` "requires a default such as ``${CLAUDE_PROJECT_DIR:-.}``".
That default is the working directory again.

The two clients with a variable get it, not the plain relative path. VS Code's
default working directory would also make a relative path work. The variable
says the same and does not depend on where the process started. The client most
sessions use asks for exactly that:

.. code-block:: json

    {
      "servers": {
        "typo3-dev-companion": {
          "type": "stdio",
          "command": "php",
          "args": ["${workspaceFolder}/vendor/bin/typo3-dev-companion"]
        }
      }
    }

For the other nine, a relative entry would be wrong even on the machine that
wrote it. An absolute one is at least right there. So ``install`` says so, per
client, at the terminal, beside the line that reports the entry —
`D-DIS-016 <../../decisions/discovery/dis-016-how-an-entrypoint-may-be-named-is-a-per-client-question.md>`_.

Antigravity gets the absolute path as ``cwd`` too. The path is in a plugin
directory that git ignores, so ``install`` has nothing to add —
`D-DIS-032 <../../decisions/discovery/dis-032-antigravity-gets-a-workspace-plugin-that-starts-the-server-in-the-project.md>`_.

A standalone checkout gets none of this. ``${workspaceFolder}`` names a path
inside the project, and a server outside the project has none. There the
absolute path is the only one, whatever the client resolves.

The sources are the pages of
:ref:`finishing in the client <installing-finishing-in-the-client>`, and three
more:

* `the MCP transports specification <https://modelcontextprotocol.io/specification/2025-06-18/basic/transports>`_;

* `VS Code's configuration reference <https://code.visualstudio.com/docs/agents/reference/mcp-configuration>`_;

* `Cursor's MCP page <https://cursor.com/docs/context/mcp>`_.
