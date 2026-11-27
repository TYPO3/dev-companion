:navigation-title: Checking it answers

Checking that it answers
========================

This page covers how you find out whether the server answers in your session,
and what to do where it does not. It also covers an installation that is down,
skills that do not activate, and a test without a client.

An entry on disk registers nothing. The client must read it, and in most clients
somebody must approve it. So an install usually ends with a configuration that
is entirely correct, and a session with no ``typo3_`` tool in it.

Nothing tells you. An agent without the server answers from the checkout and
from what it already knows, with the same tone, speed and confidence. The wrong
sentence looks exactly like the right ones. Two sessions did a whole task that
way. Each had a published skill beside it that named eleven tools, and none of
those tools was there. So the check is worth the minute, and the minute is one
question.

Ask it something
----------------

Ask a question that your project has and that this server answers, not a
command. Ask it in the client you actually work in:

    Which icon identifier is the right one for a delete button in the TYPO3
    backend?

Then look at what the agent did, not at what it said. A client that shows tool
calls shows one named ``typo3_icon_lookup``. Where your client does not show
them, ask the agent afterwards which tools it used.

Without a TYPO3 installation, the call still happens, and its answer says that
the question is not answerable here. That answer proves that the server works.
Then ask a question the bundled knowledge answers, with no installation at all:

    Which rules does the subject line of a TYPO3 core commit message follow?

A client that shows tool calls shows one named ``typo3_commit_message_guide`` or
``typo3_rule_lookup``.

**An answer with no call behind it is the failure this page is about.** The
model knows enough about TYPO3 to produce something plausible. You cannot check
something that is only plausible.

Where no call happened
----------------------

The install printed what remained to do under the line that reports the entry.
That differs per client: an approval, a restart, a trusted directory, or nothing
at all. :ref:`The install <installing-finishing-in-the-client>` has that list,
with the documentation of each client behind it.

The listing of the client shows the result: ``/mcp`` in Claude Code,
``amp mcp doctor``, ``codex mcp list``, ``grok mcp doctor`` or
``droid mcp permissions``. A server in the listing that does not run is another
problem than a server nobody wrote. From inside the session, the two look the
same.

Ask what it reads
-----------------

    Ask the TYPO3 server what it covers and which installation it is reading.

That calls ``typo3_server_scope``, the one tool that nothing can take away. It
answers in prose for a reader. It says what it answers for, which project it
found and how, and how many packages that project has. It also says whether it
can reach the console of that installation, and the reason where it cannot.

Read the project it names. A server that started in the wrong directory is the
other quiet failure. It finds *an* installation and answers about that one.
Nothing in the answers is wrong, except the site they are about.

Where the server guessed wrong, add an ``env`` block to the client entry. Then
the server no longer guesses. ``install`` and ``update`` leave that block of the
entry alone:

.. code-block:: json

    {
      "mcpServers": {
        "typo3-dev-companion": {
          "type": "stdio",
          "command": "php",
          "args": ["/absolute/path/to/project/vendor/bin/typo3-dev-companion"],
          "env": {
            "TYPO3_DEV_COMPANION_ROOT": "/absolute/path/to/the/installation",
            "TYPO3_DEV_COMPANION_CONSOLE": "ddev exec .build/bin/typo3"
          }
        }
      }
    }

The first variable names the installation. The second names the command that
runs its console, where the server cannot find that command itself.
:ref:`The install <installing-what-the-entry-may-carry>` lists the other three
variables the block can set.

When the site is down
---------------------

Only the installation can answer some questions. Examples are which icons it
registers, which labels exist, and the value a ``TYPO3_CONF_VARS`` setting has
once every extension has changed it. For those, the server asks the installation
itself. Where it cannot, the icon and label lookups read the files the packages
ship instead. The answer then says so, as ``answeredBy: packages``. The
configuration lookup answers that it cannot read the value.

**That word changes what the answer proves.** With the installation reachable, a
lookup that finds no icon shows that the identifier is not registered. With the
installation down, the same answer shows nothing. The files do not contain what
an extension registers while it runs.

Two sessions read such an answer as a fact. They concluded that a well-known
sitepackage registers no icons at all. That is false: it registers about forty.

So look at that line once before you act on an empty answer. If it says the
files answered and you meant the site, start the containers and run
``composer install``. Then ask again in the same session. The server never
remembers a failure, so the next call goes to the installation.

The skills are files
--------------------

The install copied the skills into the skills directory of the client. A pull of
this server or a ``composer update`` does not reach those copies. So each time a
client starts the server, the server compares what it published with what is
there. Where the two differ, it publishes the skills again and says so.
``TYPO3_DEV_COMPANION_SKILL_REFRESH=off`` keeps the notice and leaves the copies
alone. ``update`` refreshes them too. The
:doc:`task-skill catalog <task-skills/index>` names the published set and opens
each complete workflow.

A skill that never activates is the other half, and the install is not at fault.
A client chooses a skill by its description before it loads any of the skill. So
a task described in words outside that description runs without the skill, and
nothing reports it. Where a workflow you expected did not run, name the subject
the way the skill names it. Then watch whether the skill activates.

Without a client at all
-----------------------

Where none of the above settles it, ask the server directly. Two lines on stdin
start it and list what it offers. That tells a server that cannot start apart
from a client that never started it:

.. code-block:: bash

    printf '%s\n' \
      '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"t","version":"1"}}}' \
      '{"jsonrpc":"2.0","method":"notifications/initialized"}' \
      '{"jsonrpc":"2.0","id":2,"method":"tools/list"}' \
      '{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"typo3_server_scope","arguments":{}}}' \
      | php /absolute/path/to/typo3-dev-companion/bin/typo3-dev-companion

The answer is three JSON lines. The first names the server as
``typo3-dev-companion``, the second lists its ``typo3_`` tools, and the third is
what ``typo3_server_scope`` says about your project. The server stops when its
input ends.

Run it from the project you installed into, with the path your entry names. The
server has no directory of its own, and the directory it starts in decides what
it reads. For the same reason, the wrong work directory gives answers about the
wrong site.

The server writes what it has to say at the start to stderr. Each line starts
with ``typo3-dev-companion:``. Such a line also appears where the server reads
no project or finds no installed skills, and it says what to do. A PHP without
an extension that ``composer.json`` requires gets one of those lines, and the
line names the PHP that ran. Where a client did start the server, every call
answers with the same sentence.

The MCP Inspector sends the same two lines, with a form around them. Its default
protocol era, ``Legacy``, opens with ``initialize``. The server answers with the
handshake revision ``2025-11-25``. ``Modern`` and ``Auto`` open with
``server/discover`` instead. The server then answers with ``2026-07-28`` and the
same capabilities.
