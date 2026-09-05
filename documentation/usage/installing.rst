:navigation-title: Installing

Installing the server
=====================

Requirements: **PHP 8.2+** and Composer. A standalone checkout is the ordinary
way in: one clone serves every project on the machine and stays out of each
project's dependencies. A project can require the package instead, which is the
second section. The `readme <../../readme.md>`_ has the short version; this page
has the cases it leaves out.

.. image:: ../images/install-flow.svg
    :zoomable:
    :alt: A standalone checkout or Composer dependency is installed into a
          project, which writes client configuration, publishes skills, records
          the setup and is then approved and verified in the client.

``install`` and ``update`` write into the directory they are run in, which is
the project being set up. Every command below is therefore run from the root of
the project the agent works in, and never from inside the server's own checkout.

Standalone
----------

Clone the repository and install its dependencies once:

.. code-block:: bash

    git clone https://github.com/TYPO3/dev-companion.git typo3-dev-companion
    cd typo3-dev-companion
    composer install


The clone is named for the binary rather than for the repository, so the
absolute path in every command below is the directory ``git clone`` just made.

Then change into the project the agent works in and install the entrypoint into
its ``.mcp.json``:

.. code-block:: bash

    cd /path/to/your/project
    /absolute/path/to/typo3-dev-companion/bin/typo3-dev-companion install


It writes the following shape with the actual absolute path:

.. code-block:: json

    {
      "mcpServers": {
        "typo3-dev-companion": {
          "type": "stdio",
          "command": "php",
          "args": ["/absolute/path/to/typo3-dev-companion/bin/typo3-dev-companion"]
        }
      }
    }


This is the setup to use when working on the knowledge base itself, since the
``feedback/`` tools only exist in a checkout.

One entry for every project
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Everything above is per project, and a machine with a dozen of them can carry
one entry instead. Nothing here writes it: the client documents its own command
and what ``install`` writes stays inside the project it was pointed at —
`D-DIS-018 <../../decisions/discovery/dis-018-what-install-writes-stays-inside-the-project.md>`_.
Two steps on the machine, written down rather than scripted for the same reason.

Put the entrypoint where the shell finds it, so no entry has to spell the
checkout out:

.. code-block:: bash

    ln -s /absolute/path/to/typo3-dev-companion/bin/typo3-dev-companion ~/.local/bin/


The symlink resolves to the checkout, so the autoloader is found and ``install``
still records the real path. Then register it once, for every project, with the
client's own command — Claude Code's user scope, which its documentation
describes as being for the development tools somebody uses across projects:

.. code-block:: bash

    claude mcp add --scope user typo3-dev-companion -- typo3-dev-companion


Two things follow, and both are the client's rule rather than this package's. A
project that has its own entry keeps using it: the scopes rank local, project,
user, and the whole entry from the first of those wins rather than being merged.
And the task skills do not come with it — an MCP entry registers a server, while
a skill is a file in a project, so ``install`` stays the way they get there.

What this does buy for the skills is the refresh under
:ref:`installing-keeping-it-current`: the server now starts in every project, so
a publication that has drifted is put back wherever one has, instead of only
where somebody remembered to look.

As a dependency
---------------

The package is on Packagist as ``typo3/dev-companion``, so the consuming project
requires it and nothing else:

.. code-block:: bash

    composer require "typo3/dev-companion:@dev"


The constraint names a stability because no release is tagged: ``dev-main`` is
the only version there is, and a plain require is refused against the default
minimum stability. The experimental note in the `readme <../../readme.md>`_ is
why — pin a commit where the project depends on it.

That is the way in where the entry has to be shareable — a DDEV project, or a
team where each checkout should carry the same one — because the entry then
names a path inside the project. Composer exposes the stdio entrypoint as
``vendor/bin/typo3-dev-companion``. Install it from the consuming project's
root:

.. code-block:: bash

    vendor/bin/typo3-dev-companion install


``vendor/bin/typo3-dev-companion help`` lists both commands and every client
they accept. Passing anything else fails with that same text: without an
argument the entrypoint is the MCP transport itself and waits on stdin, which at
a terminal is indistinguishable from a hang.

The entry names the entrypoint inside the project where the client can resolve
that, and this server's absolute path everywhere else:

.. code-block:: json

    {
      "mcpServers": {
        "typo3-dev-companion": {
          "type": "stdio",
          "command": "php",
          "args": ["/absolute/path/to/project/vendor/bin/typo3-dev-companion"]
        }
      }
    }


Which clients resolve a path inside the project, and why the others get an
absolute one, is :ref:`installing-which-path-is-written`. The knowledge base
ships inside the package, so nothing else needs to be deployed or configured.

To work against a checkout instead — changing the server and the project that
uses it in one go — clone it and name it as a path repository, which Composer
symlinks into ``vendor/`` so an edit is live in the project:

.. code-block:: bash

    git clone https://github.com/TYPO3/dev-companion.git typo3-dev-companion


.. code-block:: json

    {
      "repositories": [
        { "type": "path", "url": "/absolute/path/to/typo3-dev-companion" }
      ]
    }


The require is the same one and resolves against the checkout instead.

In a DDEV project
~~~~~~~~~~~~~~~~~

Run the installer inside DDEV:

.. code-block:: bash

    ddev exec vendor/bin/typo3-dev-companion install


The project directory is mounted, so the skills are available to the host at
``.agents/skills``. The generated MCP entry starts the server with the project's
container PHP, at the entrypoint inside the project — below ``vendor/bin``, or
below the ``bin-dir`` the project's ``composer.json`` declares instead, which is
``.build/bin`` in the layout most extension repositories use:

.. code-block:: json

    {
      "mcpServers": {
        "typo3-dev-companion": {
          "type": "stdio",
          "command": "ddev",
          "args": ["exec", "php", ".build/bin/typo3-dev-companion"]
        }
      }
    }


A DDEV project that never required the package gets the absolute path of the
checkout instead, because the container cannot see a checkout outside the
project.

.. _installing-clients:

Naming the client
-----------------

Naming no client at all is a setup of its own, recorded as ``generic``:
``install`` then writes the ``.mcp.json`` entry and publishes the skills to
``.agents/skills``, the two locations a client finds without being configured
for it. A client that reads elsewhere is named with ``--agent=``, and each one
receives the skills at its native project path and, where it supports one, its
native MCP configuration. ``--agent=`` does not take ``generic``, because it is
nobody's name.

===========  ===============  ===========================  ===================
Client       ``--agent=``     MCP entry                    Skills
===========  ===============  ===========================  ===================
none named   —                ``.mcp.json``                ``.agents/skills``
Claude Code  ``claude``       ``.mcp.json``                ``.claude/skills``
Codex        ``codex``        ``.codex/config.toml``       ``.agents/skills``
VS Code      ``copilot``      ``.vscode/mcp.json``         ``.github/skills``
Cursor       ``cursor``       ``.cursor/mcp.json``         ``.cursor/skills``
Amp          ``amp``          ``.amp/settings.json``       ``.agents/skills``
Zed          ``zed``          ``.zed/settings.json``       ``.agents/skills``
Kiro         ``kiro``         ``.kiro/settings/mcp.json``  ``.kiro/skills``
Droid        ``factory``      ``.factory/mcp.json``        ``.factory/skills``
Junie        ``junie``        ``.junie/mcp/mcp.json``      ``.junie/skills``
opencode     ``opencode``     ``opencode.json``            ``.agents/skills``
Grok         ``grok``         ``.grok/config.toml``        ``.grok/skills``
Antigravity  ``antigravity``  none                         ``.agents/skills``
Pi           ``pi``           none                         ``.pi/skills``
===========  ===============  ===========================  ===================


Antigravity and Pi receive skills only, so for them there is no entry and
nothing to finish. ``typo3-dev-companion help`` prints the same identifiers.

The VS Code switch
~~~~~~~~~~~~~~~~~~

``--agent=copilot`` writes the skills to ``.github/skills``, which is one of the
two locations VS Code searches by default — but only if ``chat.useAgentSkills``
is on, and it is not:

.. code-block:: json

    "chat.useAgentSkills": true


Without it the client assembles no search paths at all, so nothing reports that
the skills are sitting in the repository unread; a session there answers from
the checkout as if none had been installed (measured on VS Code 1.131.0,
2026-07-31). ``chat.agentSkillsLocations`` is the list itself and needs no
change: it already covers ``.github/skills`` and ``.claude/skills`` per
workspace. ``github.copilot.chat.skillTool.enabled`` is a different,
experimental switch and not the one that makes them visible.

.. _installing-finishing-in-the-client:

Finishing in the client
-----------------------

Putting the entry on disk registers the server with nothing: a client that
scopes project servers behind an approval has not been asked yet, and a session
that was already open when the file was written is running against the
configuration it started with. Both end with an entry that is entirely correct,
a published skill naming the tools beside it, and no tool in the session — which
is where two sessions in one project went, on 2026-07-29 and 2026-07-31.

``install`` and ``update`` print what follows under the line that reports the
entry, so it is read at the terminal rather than here. What each client needs is
the client's own property, so each line below is that client's own
documentation, read on 2026-08-02, and a client whose documentation does not
answer is left unestablished rather than filled in:

* **Claude Code** — a restart and an approval. "Claude Code reads ``.mcp.json``
  at session start. Exit and restart the session after editing the file", and
  "the first time Claude Code sees a project-scoped server, it asks you to
  approve it". Approve at the prompt or in ``/mcp``; a server once refused is
  reset with ``claude mcp reset-project-choices``.
  (`quickstart <https://code.claude.com/docs/en/mcp-quickstart>`_,
  `reference <https://code.claude.com/docs/en/mcp>`_)
* **Amp** — an approval. "MCP servers in workspace settings
  (``.amp/settings.json``) require explicit approval before they can run", and
  "in the CLI, you'll be prompted to approve workspace servers when they're
  first detected". ``amp mcp approve typo3-dev-companion`` does it without the
  prompt, and ``amp mcp doctor`` shows one ``awaiting approval``.
  (`manual <https://ampcode.com/manual>`_)
* **VS Code** — a trust confirmation. "When you add an MCP server to your
  workspace or change its configuration, you need to confirm that you trust the
  server and its capabilities before starting it." The experimental
  ``chat.mcp.autoStart`` restarts the server when the configuration changes.
  (`MCP servers <https://code.visualstudio.com/docs/copilot/customization/mcp-servers>`_)
* **Codex** — a trusted project. MCP servers can be scoped "to a project with
  ``.codex/config.toml`` (trusted projects only)", so the trust prompt for the
  directory is what admits them. Whether a running session reads the file again
  is not documented; ``codex mcp list`` reports what it has.
  (`MCP <https://learn.chatgpt.com/docs/extend/mcp>`_)
* **Zed** — a trusted worktree. The MCP page describes ``context_servers`` only
  in the file opened with ``zed: open settings file``, but the project file is
  where the rest of the documentation puts it: "every worktree opened may
  contain a ``.zed/settings.json`` file with extra configuration options that
  may require installing and spawning language servers or MCP servers", and
  Zed's own advisory for the vulnerability the trust model answers says "the Zed
  IDE loads Model Context Protocol (MCP) configurations from the
  ``settings.json`` file located within a project's ``.zed`` subdirectory". So
  the written entry is read — behind a gate the other clients do not have.
  Restricted Mode, which every worktree starts in, prevents "project settings
  (``.zed/settings.json``) from being parsed and applied" and "MCP servers from
  being installed and spawned"; the title bar carries an exclamation mark until
  the directory is trusted there or with ``workspace::ToggleWorktreeSecurity``.
  Whether a window that was already open reads a new file is not documented
  (read 2026-08-02, when the current release was v1.13.1; the trust model
  arrived in v0.218.2-pre). (`MCP <https://zed.dev/docs/ai/mcp>`_,
  `trusted worktrees <https://zed.dev/docs/worktree-trust>`_,
  `GHSA-cv6g-cmxc-vw8j <https://github.com/zed-industries/zed/security/advisories/GHSA-cv6g-cmxc-vw8j>`_)
* **Kiro** — nothing. "Changes to MCP configuration apply automatically when you
  save the file" and "servers will reconnect". A tool ``autoApprove`` does not
  name is still asked about on the call.
  (`MCP configuration <https://kiro.dev/docs/mcp/configuration/>`_)
* **Droid** — nothing. "Droid reloads automatically when an ``mcp.json`` file
  changes, so new servers are available immediately." Each tool is approved on
  first use, and ``droid mcp permissions`` keeps that approval.
  (`MCP <https://docs.factory.ai/cli/configuration/mcp>`_)
* **Junie** — no approval: servers "imported from the ``mcp.json`` file are
  enabled by default". Whether an IDE that was already open reads a new one is
  not documented; the list is *Settings | Tools | Junie | MCP Settings*.
  (`MCP configuration <https://junie.jetbrains.com/docs/junie-cli-mcp-configuration.html>`_)
* **Cursor** — unestablished. Servers are listed under *Customize*, where one is
  toggled off, and "Cursor asks for approval before using MCP tools by default"
  — which is the tool call, not the server. Whether a window that was already
  open reads a new file is not documented.
  (`MCP <https://cursor.com/docs/mcp>`_)
* **opencode** — unestablished. ``enabled: false`` switches a server off, which
  the written entry does not; whether a session that was already open reads the
  file again is not documented.
  (`MCP servers <https://opencode.ai/docs/mcp-servers/>`_)
* **Grok** — unestablished. A project ``.grok/config.toml`` does contribute
  ``[mcp_servers]``, walking up to the git root; whether a running session reads
  it again, and whether anything gates it, is not documented.
  ``grok mcp doctor`` reports what it has.
  (`MCP servers <https://docs.x.ai/build/features/mcp-servers>`_)

Where a session has the entry and still offers no ``typo3_`` tool,
:doc:`checking-it-answers` is the rest of the ladder.

.. _installing-keeping-it-current:

Keeping it current
------------------

A published skill is a copy, so it goes stale the moment this package moves.
``update`` is what refreshes it, along with the client entry:

.. code-block:: bash

    vendor/bin/typo3-dev-companion update


``update`` takes ``--agent=<client>`` as well, but rarely needs to: ``install``
records every client it set up in ``.typo3-dev-companion/state.json``, and
without an agent ``update`` refreshes all of them. A project is usually worked
on by more than one, and which ones is knowledge only the project has.

Both commands write the client entry, because what belongs in it is a property
of the project rather than of the run: a project that required this package
after it was first installed, or that gained a DDEV configuration since, needs a
different entry than the one that is there, and ``update`` is what moves it. An
entry that starts something other than this server is somebody else's and is
refused instead — the two commands then say so and change nothing.

They own the command in that entry and nothing else. Whatever the caller put
beside it stays — ``env`` above all, which is where the entry carries the
:ref:`variables below <installing-what-the-entry-may-carry>`. In a
``.codex/config.toml`` or a ``.grok/config.toml`` that means every line of the
section this package does not write, so a value continued on the next line is
refused with the line number rather than rewritten around: keeping a line means
knowing where it ends.

When they go stale
~~~~~~~~~~~~~~~~~~

The record carries a digest of what was published, and a server started in that
project compares it before the first call —
`R-DIS-025 <../../requirements/discovery/dis-025-a-publication-that-went-stale-says-so-before-the-first-call.md>`_.
Where they no longer match, the server republishes what the record names, for
the clients it names, and writes no client configuration. Then it says so twice:
the long line goes to stderr, naming what differed and what was republished, and
one short sentence goes into the instructions a client is handed at initialize,
because a skill the client loaded when the session opened is the copy that was
there before.

A project with no record is untouched, and a refresh that fails leaves the
notice as it was and the server starts anyway. Why the server does it rather
than a command somebody runs is
`D-DIS-021 <../../decisions/discovery/dis-021-a-stale-publication-is-put-back-where-the-server-starts.md>`_:
on the machine that prompted it, twelve projects had been told at every session
start for weeks.

Set ``TYPO3_DEV_COMPANION_SKILL_REFRESH=off`` to keep the notice and nothing
else. That is for a reader who wants the copies in their project to move when
they say so — reviewing what a release changed, or a project where the skills
are read as part of a diff.

Refreshing on update
~~~~~~~~~~~~~~~~~~~~

A project can have the thing that moved the package run the refresh. Composer
fires ``post-update-cmd`` after ``composer update``, and the project's own
``composer.json`` is where that is wired:

.. code-block:: json

    {
        "scripts": {
            "post-update-cmd": [
                "typo3-dev-companion update"
            ]
        }
    }


Nothing here writes that line. ``install`` and ``update`` write client
configuration, the skills and the record, and a file that decides what the
project consists of is not among them.

The command needs no path: Composer pushes the project's declared ``bin-dir``
onto ``PATH`` before running a script, so the bare name resolves whether the
project puts its binaries in ``vendor/bin`` or in ``.build/bin``. It runs in the
project root, which is where the record is.

A project where nothing is installed is told so and the run succeeds. That is
the ordinary case for everybody but the person who set it up: the record sits
below a directory that ignores itself, so it is in no checkout but theirs, and a
script that exits non-zero fails the whole Composer run.

What the hook does not cover is the fresh clone: ``post-update-cmd`` fires on
``composer update`` and on an ``install`` with no lock file, so a colleague
installing from the lock runs nothing. There the notice at the next server start
is what says the copies are behind.

.. _installing-what-the-entry-may-carry:

What the entry may carry
------------------------

Four environment variables, all set in the ``env`` block of the client entry,
which is the one part of it ``install`` and ``update`` leave alone:

* ``TYPO3_DEV_COMPANION_ROOT`` — the installation to read, where the one found
  from the working directory is the wrong one — :doc:`checking-it-answers`.
* ``TYPO3_DEV_COMPANION_CONSOLE`` — the command that runs that installation's
  console, such as ``ddev exec .build/bin/typo3`` — :doc:`checking-it-answers`.
* ``TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`` — tool names, comma-separated, the
  client is not offered — the section below.
* ``TYPO3_DEV_COMPANION_SKILL_REFRESH`` — ``off`` reports stale skills instead
  of putting them back — the section above.

Which tools are offered
~~~~~~~~~~~~~~~~~~~~~~~

Every one of them, wherever the server was started. Some of what it knows is the
core's own contribution process — the review rules, the Gerrit workflow, the
core testing suites — and none of that transfers to a project. What it is worth
is said in the answer, per topic and per path, because whether a task is core
work is a property of the task and not of the directory it is asked from.

The server used to leave those three tools out of a Composer project. That read
the repository where the task was meant, and a core patch written from a site
installation was answered as core work and then sent to a tool the client had
not been given.

* ``TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`` removes tools by their comma-separated
  names, and is the only thing that shortens the list. Three names never shorten
  it: ``typo3_server_scope``, because it is what explains a shorter list, and
  ``typo3_feedback_record`` and ``typo3_feedback_list``, because the feedback
  channel is a development tool for building this server rather than part of
  using it —
  `R-SCO-009 <../../requirements/scope/sco-009-individual-tools-can-be-excluded.md>`_.
* A name in it that takes no tool away is reported rather than absorbed, on
  stderr before the transport starts and again under ``excludedTools.ignored``
  in ``typo3_server_scope``. That covers both reasons a name takes nothing away:
  no tool of this server answers to it — a rename, or a typo — or it is one of
  the three above.

``typo3_server_scope`` names what was really excluded, and nothing routes to a
tool that is not there. What the server says is gone is what is gone: a name
that changed nothing is never reported as a missing capability, because a client
cannot check the claim and pays for it out of the instructions it is sent.

What comes with it
~~~~~~~~~~~~~~~~~~

Clients that expose MCP prompts also list ``commit_message``. It turns a summary
into the same checked draft as ``typo3_commit_message_guide``; the rules remain
in the guide rather than being duplicated in the prompt.

``debrief`` stands beside it in a standalone checkout of this repository, where
the feedback channel is offered. It takes no arguments and asks the session that
has just finished what this server did for it and what it lacked. The two
feedback tools are gated the same way, so a project that installed the server as
a dependency lists none of the three.

Task skills are authored once below ``skills/``. They contain routing and order,
not a second copy of tool answers; client installation publishes them from that
source.

.. _installing-removing-it:

Removing it
-----------

Three things landed in the project, and no command takes them out again, so
removing the server is deleting them by hand:

* the ``typo3-dev-companion`` entry in the client file the table above names,
  and only that entry — the file may carry other servers;
* one directory per published skill in the skills directory the table names;
* ``.typo3-dev-companion/``, where the record sits.

Neither command touches the project's ``.gitignore``. Every directory this
package writes — each published skill, and ``.typo3-dev-companion/`` — carries a
``.gitignore`` of its own that says ``*``, which covers the directory and that
file with it: git reports nothing there, and a skill the project wrote itself,
in the same skills directory, stays visible. Merged agent or MCP configuration
such as ``.codex/config.toml`` or ``.mcp.json`` is ignored nowhere, because the
project may share it.

Development builds before this wrote a ``typo3-dev-companion.json`` at the
project root and a block between ``# BEGIN typo3-dev-companion`` and
``# END typo3-dev-companion`` into the project's ``.gitignore``. Neither is read
or removed by anything here — a project that has them was set up by hand and
takes them out the same way, and the next ``install`` records the clients again.

.. _installing-which-path-is-written:

Which path is written
---------------------

Three shapes, and which one a client gets is a property of that client. Where
the project has this server as a Composer dependency, the entry names the path
inside the project — through ``ddev exec`` where there is a DDEV configuration,
and through ``${workspaceFolder}`` in VS Code and Cursor. Everywhere else it is
this server's absolute path on the machine the install ran on, which is a value
one machine is right about, written into a file its own client documents as the
shared, committed one. There the install says so; the reading below is why there
is nothing better to write.

A relative path would have to resolve against the working directory the client
launches the process in. The MCP specification does not define one: the stdio
transport is "the client launches the MCP server as a subprocess" and nothing
about a directory. So it is each client's property, read on 2026-08-09 from the
same pages as :ref:`installing-finishing-in-the-client`.

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
===========  =====================================================  ===============================================================


One client documents the workspace as the default, three offer a ``cwd`` to set,
two resolve a variable that names the project root, and one refuses expansion in
those fields outright. Claude Code is the sharpest of them: it sets
``CLAUDE_PROJECT_DIR`` in the spawned server's environment "so your server can
resolve project-relative paths **without depending on the working directory**",
and the same variable in a project-scoped ``.mcp.json`` "requires a default such
as ``${CLAUDE_PROJECT_DIR:-.}``" — which is the working directory again.

The variable is what the two who have one get, rather than the plain relative
path VS Code's default working directory would also carry. It says the same
thing without resting on where the process was started, which is the property
the client this server is used with most asks for by name:

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


For the other nine a relative entry would be wrong on the machine that wrote it
too, where an absolute one is at least right there. So the install says it, per
client and at the terminal, beside the line reporting the entry —
`D-DIS-016 <../../decisions/discovery/dis-016-how-an-entrypoint-may-be-named-is-a-per-client-question.md>`_.

None of this reaches a standalone checkout. ``${workspaceFolder}`` names a path
inside the project, and a server running from somewhere else has none: there the
absolute path is the only one that exists, whatever the client resolves.

The sources are the same as the section above, plus
`the MCP transports specification <https://modelcontextprotocol.io/specification/2025-06-18/basic/transports>`_,
`VS Code's configuration reference <https://code.visualstudio.com/docs/agents/reference/mcp-configuration>`_
and `Cursor's MCP page <https://cursor.com/docs/context/mcp>`_.
