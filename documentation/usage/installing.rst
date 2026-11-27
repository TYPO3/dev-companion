:navigation-title: Installing

Installing the server
=====================

This page covers how the server gets into a project and into your agent's
client. It also covers how you keep it current and how you remove it. The
`readme <../../readme.md>`_ has the short version; this page has the cases it
leaves out.

The steps at a glance:

1. Check the requirements.
2. Choose a standalone checkout or a Composer dependency.
3. Run ``install`` in the project, and name your client.
4. Finish the setup in the client: approve the server, then start a new session.
5. Run ``update`` when the package moves.

Requirements
------------

You need **PHP 8.2+** with the extensions ``composer.json`` requires, and
Composer. ``composer check-platform-reqs`` names an extension that is missing.

A PHP without such an extension still starts the server. The server names the
extension on stderr and answers every call with that sentence —
`D-ANS-176 <../../decisions/answers/ans-176-a-php-that-lacks-a-required-extension-is-refused-by-every-call.md>`_.

Choose a way in
---------------

There are two ways, and the first is the usual one:

* **A standalone checkout.** One clone serves every project on the machine. It
  stays out of the dependencies of each project.

* **A Composer dependency.** The project requires the package. Use it where
  every checkout of the project needs the same client entry, as in a team or a
  DDEV project.

``install`` and ``update`` write into the directory you run them in. So run
every command below from the root of the project your agent works in. Never run
them from inside the server's own checkout.

Standalone
----------

Clone the repository and install its dependencies once:

.. code-block:: bash

    git clone https://github.com/TYPO3/dev-companion.git typo3-dev-companion
    cd typo3-dev-companion
    composer install

The clone takes its name from the binary, not from the repository. In every
command below, the absolute path is the directory ``git clone`` just made.

Then change into your project and install the entrypoint into its ``.mcp.json``:

.. code-block:: bash

    cd /path/to/your/project
    /absolute/path/to/typo3-dev-companion/bin/typo3-dev-companion install

``install`` writes this entry, with the real absolute path:

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

Use this setup to work on the knowledge base itself. The ``feedback/`` tools
exist only in a checkout.

One entry for every project
~~~~~~~~~~~~~~~~~~~~~~~~~~~

``install`` writes one entry per project. A machine with many projects can use
one entry for all of them instead. This package does not write that entry: what
``install`` writes stays inside the project —
`D-DIS-018 <../../decisions/discovery/dis-018-what-install-writes-stays-inside-the-project.md>`_.
Your client documents the command for it.

In Claude Code, that entry is the user scope. Its documentation names the user
scope as the place for tools somebody uses across projects:

.. code-block:: bash

    claude mcp add --scope user typo3-dev-companion -- php /absolute/path/to/typo3-dev-companion/bin/typo3-dev-companion

Name the checkout by its absolute path. Each machine has its own user scope, so
each machine writes its own path. Do not use a bare command name. It depends on
the ``PATH`` the client starts with, and a client started from a desktop often
has another ``PATH`` than your shell.

With that entry, the server starts in every session, also in a project without
TYPO3. Its tool list then costs context in every session. That is why
``install`` per project stays the default.

The client starts the server in the directory of the session, and the server
finds the project there. Claude Code does not document that directory. A
measurement on 2026-10-27 showed that a session in a project gets that project.

Three points follow from the rules of the client:

* **A project entry wins.** The scopes rank local, project, user. The first
  entry found wins whole, and the client merges nothing. ``install`` writes a
  project entry, so a project where it ran starts the server from that entry.

* **The task skills do not come with the user entry.** So ``install`` still
  publishes them into each project. A client lists the skills when the session
  starts, also where it connects this server later.

  A project without the skills gets instructions that name the ``typo3://skill``
  resources instead. A session reads them only after the server is connected —
  `D-DIS-034 <../../decisions/discovery/dis-034-a-project-without-skills-is-sent-to-the-skill-resources.md>`_.

* **Codex needs the skill files.** In runs on 2026-10-27, a Codex session
  without them called no tool of this server at all. So for Codex, use
  ``install``.

As a dependency
---------------

The package is on Packagist as ``typo3/dev-companion``. The project that uses it
requires it, and needs nothing else:

.. code-block:: bash

    composer require "typo3/dev-companion:@dev"

The constraint names a stability, because no release has a tag yet. ``dev-main``
is the only version, and the default minimum stability refuses a plain require.
The experimental note in the `readme <../../readme.md>`_ says why. Pin a commit
where the project depends on the server.

Composer installs the entrypoint as ``vendor/bin/typo3-dev-companion``, so the
entry names a path inside the project. Install it from the root of the project:

.. code-block:: bash

    vendor/bin/typo3-dev-companion install

``vendor/bin/typo3-dev-companion help`` lists both commands and every client
they accept. Any other argument prints the same text and fails.
``install --help`` and ``update --help`` print it too. Neither command writes
anything when it gets an option it does not know.

Without an argument, the entrypoint is the MCP transport itself and waits on
stdin. At a terminal, that looks exactly like a hang.

Where the client can resolve a path inside the project, the entry names the
entrypoint there. Everywhere else it names the absolute path of this server:

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

:ref:`installing-which-path-is-written` says which clients resolve a path inside
the project, and why the others get an absolute one. The knowledge base is part
of the package, so nothing else needs a deployment or a configuration.

To work against a checkout instead, clone it and add it as a path repository.
Composer then links the checkout into ``vendor/``. So an edit to the server is
live in the project at once:

.. code-block:: bash

    git clone https://github.com/TYPO3/dev-companion.git typo3-dev-companion

.. code-block:: json

    {
      "repositories": [
        { "type": "path", "url": "/absolute/path/to/typo3-dev-companion" }
      ]
    }

The require stays the same. Composer resolves it against the checkout instead.

In a DDEV project
~~~~~~~~~~~~~~~~~

Run the installer inside DDEV:

.. code-block:: bash

    ddev exec vendor/bin/typo3-dev-companion install

The project directory is a mount, so the host sees the skills at
``.agents/skills``. The entry starts the server with the PHP of the project's
container. It names the entrypoint inside the project, below ``vendor/bin``.

Where the project's ``composer.json`` declares another ``bin-dir``, the entry
uses that. Most extension repositories declare ``.build/bin``:

.. code-block:: json

    {
      "mcpServers": {
        "typo3-dev-companion": {
          "type": "stdio",
          "command": "sh",
          "args": [".build/bin/typo3-dev-companion-ddev", ".build/bin/typo3-dev-companion"]
        }
      }
    }

This server's own checkout is a DDEV project too, where it has a
``.ddev/config.yaml``. ``install`` in it names ``bin/typo3-dev-companion`` the
same way. A DDEV project that did not require the package gets the absolute path
of the checkout instead. The container cannot see a checkout outside the
project.

.. _installing-clients:

Naming the client
-----------------

``install`` without a client name sets the project up as ``generic``. It writes
the ``.mcp.json`` entry and publishes the skills to ``.agents/skills``. Many
clients find those two locations without any configuration.

``--agent=`` names a client that reads from other places. That client gets the
skills at its own project path, and its own MCP configuration where it has one.
``--agent=`` does not accept ``generic``, because no client has that name.

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
Antigravity  ``antigravity``  a plugin, see below          ``.agents/skills``
Pi           ``pi``           none                         ``.pi/skills``
===========  ===============  ===========================  ===================

``typo3-dev-companion help`` prints the same identifiers. Two clients differ
from the rest:

* **Antigravity** reads no ``.mcp.json``. Its entry goes into a workspace
  plugin, ``.agents/plugins/typo3-dev-companion/``, with ``plugin.json`` and
  ``mcp_config.json``. Git ignores the plugin directory, the same way as a
  published skill.

* **Pi** gets skills only. So for Pi there is no entry and nothing to finish in
  the client.

The VS Code switch
~~~~~~~~~~~~~~~~~~

Turn on ``chat.useAgentSkills`` in VS Code. It is off by default:

.. code-block:: json

    "chat.useAgentSkills": true

``--agent=copilot`` writes the skills to ``.github/skills``. VS Code searches
that location by default, but only with this switch on. Without it, VS Code
searches no skill location, and nothing reports that the skills stay unread. A
session then answers from the checkout as if there were no skills. That was
measured on VS Code 1.131.0, on 2026-07-31.

Two other settings look related:

* ``chat.agentSkillsLocations`` is the list of locations. It already contains
  ``.github/skills`` and ``.claude/skills`` per workspace, so leave it as it is.

* ``github.copilot.chat.skillTool.enabled`` is another, experimental switch. It
  does not make the skills visible.

.. _installing-finishing-in-the-client:

Finishing in the client
-----------------------

The entry in the file does not yet give a session the tools. Two things stop it:

* A client that asks for approval of project servers has not asked yet.

* A session that was already open when ``install`` ran still uses its old
  configuration.

In both cases the entry is correct, the skills name the tools, and the session
has no tool. Two sessions in one project ended like that, on 2026-07-29 and on
2026-07-31.

``install`` and ``update`` print the step for your client below the line that
reports the entry. So you read it at the terminal. The list below quotes each
client's own documentation, read on 2026-08-02. Where that documentation does
not answer, the list says so and guesses nothing.

* **Claude Code** — restart the session and approve the server. "Claude Code
  reads ``.mcp.json`` at session start. Exit and restart the session after
  editing the file". And "the first time Claude Code sees a project-scoped
  server, it asks you to approve it".

  Approve at the prompt or in ``/mcp``. To reset a server you refused, run
  ``claude mcp reset-project-choices``.
  (`quickstart <https://code.claude.com/docs/en/mcp-quickstart>`_,
  `reference <https://code.claude.com/docs/en/mcp>`_)

* **Amp** — approve the server. "MCP servers in workspace settings
  (``.amp/settings.json``) require explicit approval before they can run." And
  "in the CLI, you'll be prompted to approve workspace servers when they're
  first detected".

  ``amp mcp approve typo3-dev-companion`` approves it without the prompt.
  ``amp mcp doctor`` shows a server that is ``awaiting approval``.
  (`manual <https://ampcode.com/manual>`_)

* **VS Code** — confirm that you trust the server. "When you add an MCP server
  to your workspace or change its configuration, you need to confirm that you
  trust the server and its capabilities before starting it."

  The experimental ``chat.mcp.autoStart`` restarts the server when the
  configuration changes.
  (`MCP servers <https://code.visualstudio.com/docs/copilot/customization/mcp-servers>`_)

* **Codex** — trust the project. Codex scopes MCP servers "to a project with
  ``.codex/config.toml`` (trusted projects only)". So the trust prompt for the
  directory admits them.

  No documentation says whether a running session reads the file again.
  ``codex mcp list`` shows what the session has.
  (`MCP <https://learn.chatgpt.com/docs/extend/mcp>`_)

* **Zed** — trust the worktree. Every worktree starts in Restricted Mode. That
  mode prevents "project settings (``.zed/settings.json``) from being parsed and
  applied" and "MCP servers from being installed and spawned".

  Trust the directory at the exclamation mark in the title bar, or with
  ``workspace::ToggleWorktreeSecurity``. No documentation says whether a window
  that was already open reads a new file.

  The MCP page of Zed describes ``context_servers`` only in the user settings
  file. The rest of the documentation puts it in the project file too: "every
  worktree opened may contain a ``.zed/settings.json`` file with extra
  configuration options that may require installing and spawning language
  servers or MCP servers". Zed's own security advisory agrees: "the Zed IDE
  loads Model Context Protocol (MCP) configurations from the ``settings.json``
  file located within a project's ``.zed`` subdirectory".

  Read on 2026-08-02, when the current release was v1.13.1. The trust model came
  with v0.218.2-pre. (`MCP <https://zed.dev/docs/ai/mcp>`_,
  `trusted worktrees <https://zed.dev/docs/worktree-trust>`_,
  `GHSA-cv6g-cmxc-vw8j <https://github.com/zed-industries/zed/security/advisories/GHSA-cv6g-cmxc-vw8j>`_)

* **Kiro** — nothing to do. "Changes to MCP configuration apply automatically
  when you save the file" and "servers will reconnect". Kiro still asks at the
  call for each tool that ``autoApprove`` does not name.
  (`MCP configuration <https://kiro.dev/docs/mcp/configuration/>`_)

* **Droid** — nothing to do. "Droid reloads automatically when an ``mcp.json``
  file changes, so new servers are available immediately." Droid asks for each
  tool at its first use, and ``droid mcp permissions`` keeps the answer.
  (`MCP <https://docs.factory.ai/cli/configuration/mcp>`_)

* **Junie** — no approval. Servers "imported from the ``mcp.json`` file are
  enabled by default". No documentation says whether an IDE that was already
  open reads a new file. The list is at *Settings | Tools | Junie | MCP
  Settings*.
  (`MCP configuration <https://junie.jetbrains.com/docs/junie-cli-mcp-configuration.html>`_)

* **Cursor** — not established. The servers are under *Customize*, where a
  toggle switches one off. "Cursor asks for approval before using MCP tools by
  default" means each tool call, not the server. No documentation says whether a
  window that was already open reads a new file.

  In the CLI, ``--approve-mcps`` approves the server and not its tools. So a
  first task asks once per tool. ``install`` writes no permission. To get fewer
  questions, list the read-only tools under ``permissions.allow`` in
  ``.cursor/cli.json``, one ``Mcp(typo3-dev-companion:typo3_hint_lookup)`` per
  tool.

  Do not use ``Mcp(typo3-dev-companion:*)``. It also allows
  ``typo3_feedback_record``, the one tool that writes.
  (`MCP <https://cursor.com/docs/mcp>`_,
  `Permissions <https://cursor.com/docs/cli/reference/permissions>`_)

* **opencode** — not established. ``enabled: false`` switches a server off, and
  the written entry does not set it. No documentation says whether a session
  that was already open reads the file again.
  (`MCP servers <https://opencode.ai/docs/mcp-servers/>`_)

* **Antigravity** — not established. A plugin in ``.agents/plugins/`` "activates
  only when working in that project". Neither page says whether an open session
  reads a new plugin, or whether anything gates it. ``/mcp`` lists the servers a
  session has. Read on 2026-10-06.
  (`plugins <https://antigravity.google/docs/plugins/>`_,
  `MCP <https://antigravity.google/docs/mcp/>`_)

* **Grok** — not established. A project ``.grok/config.toml`` adds its
  ``[mcp_servers]``, up to the git root. No documentation says whether a running
  session reads it again, or whether anything gates it. ``grok mcp doctor``
  shows what it has.
  (`MCP servers <https://docs.x.ai/build/features/mcp-servers>`_)

A session can have the entry and still offer no ``typo3_`` tool.
:doc:`checking-it-answers` gives the next checks.

.. _installing-keeping-it-current:

Keeping it current
------------------

Restart the MCP server after a pull or a ``composer update``. The client started
the server with the code of that moment, and the new code does not reach the
running process. Until the restart, every answer opens with a sentence that says
so —
`D-DIS-030 <../../decisions/discovery/dis-030-a-process-older-than-its-code-says-so-in-every-answer.md>`_.

A checkout does not learn by itself that ``main`` moved, and this package moves
every day. So a server started from a git checkout asks GitHub once at the start
whether its commit is behind ``main``. All sessions of the checkout share that
answer for an hour.

While the checkout is behind, every answer opens with how far behind it is and
the pull that fixes it. ``typo3_server_scope`` reports the state under
``upstream``. ``TYPO3_DEV_COMPANION_UPSTREAM_CHECK=off`` turns the check off —
`D-DIS-031 <../../decisions/discovery/dis-031-a-checkout-behind-its-upstream-says-so-in-every-answer.md>`_.

Where the check fails, the state is ``unavailable``, and the server writes why
to stderr. ``typo3_server_scope`` gives the same sentence as ``reason``. The
next start asks again.

Run ``update`` when the package moved. A published skill is a copy, so it is out
of date as soon as the package changes. ``update`` refreshes the skills and the
client entry:

.. code-block:: bash

    vendor/bin/typo3-dev-companion update

``update`` takes ``--agent=<client>`` too, but rarely needs it. ``install``
writes every client it set up into ``.typo3-dev-companion/state.json``. This
page calls that file the record. Without ``--agent=``, ``update`` refreshes
every client the record names. More than one client often works on a project,
and only the project knows which.

Both commands write the client entry, because the right entry depends on the
project and not on the run. Two examples: a project that required this package
after its first install, and a project that got a DDEV configuration since. Each
needs another entry than the one it has, and ``update`` writes it.

An entry that starts something other than this server belongs to somebody else.
Then both commands refuse, say so and change nothing.

The two commands change the command in that entry and nothing else. They keep
everything you put beside it. That is mostly ``env``, which holds the
:ref:`variables below <installing-what-the-entry-may-carry>`.

In a ``.codex/config.toml`` or a ``.grok/config.toml``, they keep every line of
the section that this package does not write. So where a value continues on the
next line, the commands refuse and name the line number. They rewrite a line
only where they know where it ends.

.. _installing-when-they-go-stale:

When they go stale
~~~~~~~~~~~~~~~~~~

The record holds a digest of the published skills. A server started in the
project compares that digest before the first call —
`R-DIS-025 <../../requirements/discovery/dis-025-a-publication-that-went-stale-says-so-before-the-first-call.md>`_.

Where the digest no longer matches, the server publishes the skills again. It
does that for the clients the record names, and it writes no client
configuration. Then it says so twice:

* A long line on stderr names what differed and what it published again.

* One short sentence goes into the instructions the client gets at initialize.

A skill that the client loaded at the start of the session stays the old copy.

A project without a record stays as it is. Its instructions open with one
sentence: no task skills are installed there. That sentence names the
``typo3://skill`` resource that serves each one —
`D-DIS-034 <../../decisions/discovery/dis-034-a-project-without-skills-is-sent-to-the-skill-resources.md>`_.

Where the refresh fails, the notice stays, and the server starts anyway.
`D-DIS-021 <../../decisions/discovery/dis-021-a-stale-publication-is-put-back-where-the-server-starts.md>`_
says why the server refreshes the skills itself rather than leave it to a
command. On the machine that led to it, twelve projects had shown the notice at
every session start for weeks.

Set ``TYPO3_DEV_COMPANION_SKILL_REFRESH=off`` to get the notice and no refresh.
Use it where you want the copies in your project to change only when you say so.
Examples are a review of what a release changed, or a project where the skills
are part of a diff.

Refreshing on update
~~~~~~~~~~~~~~~~~~~~

A project can run the refresh whenever Composer updates the package. Composer
runs ``post-update-cmd`` after ``composer update``. Add it to the project's own
``composer.json``:

.. code-block:: json

    {
        "scripts": {
            "post-update-cmd": [
                "typo3-dev-companion update"
            ]
        }
    }

This package does not write that line. ``install`` and ``update`` write the
client configuration, the skills and the record. A file that decides what the
project consists of is not theirs to change.

The command needs no path. Composer puts the project's declared ``bin-dir`` on
the ``PATH`` before it runs a script. So the bare name works with ``vendor/bin``
and with ``.build/bin``. It runs in the project root, where the record is.

In a checkout without an install, the command says so and succeeds. That is the
usual case for everybody but the person who set the project up. Git ignores the
directory of the record, so it exists only in that person's checkout. A script
that fails would fail the whole Composer run.

The hook does not cover a fresh clone. ``post-update-cmd`` runs on
``composer update``, and on ``composer install`` without a lock file. A
colleague who installs from the lock file runs nothing. There, the notice at the
next server start says that the copies are out of date.

.. _installing-what-the-entry-may-carry:

Variables in the entry
----------------------

The entry takes four environment variables. Set them in the ``env`` block of the
client entry. ``install`` and ``update`` leave that block as it is.

* ``TYPO3_DEV_COMPANION_ROOT`` — the installation to read, where the server
  finds the wrong one from the working directory. See
  :doc:`checking-it-answers`.

* ``TYPO3_DEV_COMPANION_CONSOLE`` — the command that runs the console of that
  installation, such as ``ddev exec .build/bin/typo3``. See
  :doc:`checking-it-answers`.

* ``TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`` — the names of the tools the client does
  not get, separated by commas. See
  :ref:`which tools the client gets <installing-which-tools-are-offered>`.

* ``TYPO3_DEV_COMPANION_SKILL_REFRESH`` — ``off`` reports skills that are out of
  date and does not refresh them. See
  :ref:`when they go stale <installing-when-they-go-stale>`.

.. _installing-which-tools-are-offered:

Which tools the client gets
~~~~~~~~~~~~~~~~~~~~~~~~~~~

The client gets every tool, wherever the server started. Some of what the server
knows is about the core's contribution process: the review rules, the Gerrit
workflow and the core test suites. None of that applies to a project. Each
answer says what it is worth for the topic and the path. Whether a task is core
work depends on the task, not on the directory you ask from.

Earlier builds left the core contribution tools out of a Composer project. That
mistook the repository for the task. A core patch written from a site
installation got a core answer and then a route to a tool the client did not
have.

To take tools away, use ``TYPO3_DEV_COMPANION_EXCLUDE_TOOLS``. It is the only
setting that shortens the list. It never removes three tools:

* ``typo3_server_scope``, because it explains a shorter list.

* ``typo3_feedback_record`` and ``typo3_feedback_list``, because the feedback
  channel is a tool for the development of this server, not part of its use. See
  `R-SCO-009 <../../requirements/scope/sco-009-individual-tools-can-be-excluded.md>`_.

A name in the variable can remove nothing: a tool that does not exist, a renamed
tool, a typo, or one of the three above. The server then reports that name. It
writes it to stderr before the transport starts. ``typo3_server_scope`` lists it
again under ``excludedTools.ignored``.

``typo3_server_scope`` names what the variable really removed, and no answer
sends a session to a removed tool. The server reports as gone only what is gone.
A client cannot check such a claim, and pays for a wrong one in its
instructions.

What comes with it
~~~~~~~~~~~~~~~~~~

Clients that show MCP prompts also list ``commit_message``. It turns a summary
into the same checked draft as ``typo3_commit_message_guide``. The rules stay in
that tool, and the prompt has no copy of them.

In a standalone checkout of this repository, the ``debrief`` prompt is there
too. It takes no arguments. It asks the session that just finished what this
server did for it and what was missing. The two feedback tools have the same
condition. So a project that installed the server as a dependency lists none of
the three.

:ref:`working-with-it-the-prompts` says how a client runs a prompt. It also says
what Claude Code does with a summary that has a space in it.

The task skills have one source, the directory ``skills/``. They hold routing
and order, not a copy of tool answers. ``install`` publishes them from that
source.

.. _installing-removing-it:

Removing it
-----------

No command removes the server. Delete these three things by hand:

* the ``typo3-dev-companion`` entry in the client file that the table under
  :ref:`naming the client <installing-clients>` names. Delete only that entry,
  because the file can hold other servers;

* one directory per published skill, in the skills directory the table names;

* ``.typo3-dev-companion/``, where the record is.

Neither command changes the project's ``.gitignore``. Every directory this
package writes has a ``.gitignore`` of its own that says ``*``. That is each
published skill and ``.typo3-dev-companion/``. The rule covers the directory and
that file too.

So git reports nothing there. A skill the project wrote itself, in the same
skills directory, stays visible. No ignore rule covers a shared configuration
file such as ``.codex/config.toml`` or ``.mcp.json``, because the project may
share it.

Files from older builds
~~~~~~~~~~~~~~~~~~~~~~~

Nothing here reads or removes these. Take them out by hand.

* **Development builds before the record** wrote a ``typo3-dev-companion.json``
  at the project root. They also wrote a block into the project's
  ``.gitignore``, between ``# BEGIN typo3-dev-companion`` and
  ``# END typo3-dev-companion``. The next ``install`` records the clients again.

* **Builds from 2026-09-19 to 2026-10-02** wrote a block into the project's
  ``AGENTS.md``, ``CLAUDE.md`` or ``.junie/AGENTS.md``. It is between
  ``<!-- typo3-dev-companion: start -->`` and
  ``<!-- typo3-dev-companion: end -->``. Delete from the start mark to the end
  mark.

  For Antigravity, those builds wrote ``.agents/rules/typo3-dev-companion.md``.
  Delete the whole file. Later builds write nothing into a file the agents of
  the project read as instructions, and leave an old block alone —
  `D-DIS-026 <../../decisions/discovery/dis-026-the-projects-instruction-files-belong-to-the-project.md>`_.

.. _installing-which-path-is-written:

Which path the entry names
--------------------------

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
each client decides. The table below was read on 2026-08-09, from the same pages
as :ref:`installing-finishing-in-the-client`.

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
