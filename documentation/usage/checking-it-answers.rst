:navigation-title: Checking it answers

Checking that it answers
========================

An entry on disk registers nothing. The client has to have read it, and in most
clients somebody has to have approved it, so the ordinary end of an install is a
configuration that is entirely correct and a session with no ``typo3_`` tool in
it.

Nothing tells you. An agent with no server answers from the checkout and from
what it already knows — same tone, same speed, same confidence — and the
sentence that is wrong looks exactly like the ones that are right. Two sessions
went a whole task that way, each with a published skill beside it naming eleven
tools that were not there. So the check is worth the minute, and the minute is
one question.

Ask it something
----------------

Not a command — a question your project has and this server answers. In the
client you actually work in:

    Which icon identifier is the right one for a delete button in the TYPO3
    backend?

Then look at what the agent did, not at what it said. A client that shows tool
calls shows one named ``typo3_icon_lookup``; where yours does not show them, ask
it afterwards which tools it used. **An answer with no call behind it is the
failure this page is about** — the model knows enough about TYPO3 to produce
something plausible, and plausible is what you cannot check.

Where no tool was called
------------------------

The install printed what was left to do under the line reporting the entry, and
it is different per client: an approval, a restart, a trusted directory, or
nothing at all. That list, with each client's own documentation behind it, is
:ref:`in the install <installing-finishing-in-the-client>`.

The client's own listing is where it is read back — ``/mcp`` in Claude Code,
``amp mcp doctor``, ``codex mcp list``, ``grok mcp doctor``,
``droid mcp permissions``. A server that is listed but not running is a
different problem from one that was never written, and the two look identical
from inside the session.

Ask what it reads
-----------------

    Ask the TYPO3 server what it covers and which installation it is reading.

That is ``typo3_server_scope``, the one tool nothing can take away, and it
answers in prose meant to be read: what it will answer for, which project it
found and how, how many packages that project has, and whether it can reach that
installation's console — with the reason where it cannot.

Read the project it names. A server started in the wrong directory is the other
quiet failure: it finds *an* installation, answers about that one, and nothing
in the answers is wrong except which site they are about. Where it guessed
wrong, an ``env`` block in the client entry ends the guessing — that block is
the one part of the entry ``install`` and ``update`` leave alone:

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

The first names the installation, the second the command that runs its console
where the server cannot work that out. The other two variables the block may
carry are :ref:`in the install <installing-what-the-entry-may-carry>`.

When the site is down
---------------------

Some questions only the installation can answer: which icons are registered,
which labels exist, what a setting is after every extension has had its say. For
those the server asks the installation itself — and where it cannot, it reads
the files the packages ship instead and says so, in the answer, as
``answeredBy: packages``.

**That word changes what the answer proves.** With the installation reachable, a
lookup that finds no icon has established that the identifier is not registered.
With it down, the same answer has established nothing: what an extension
registers while it runs is not in the files. Two sessions read such an answer as
a fact and concluded that a well-known sitepackage registers no icons at all,
which is false — it registers about forty.

So an empty answer is worth one glance at that line before you act on it. If it
says the files answered and you meant the site, start the containers, run
``composer install``, and ask again in the same session: a failure is never
remembered, and the next call goes to the installation.

The skills are files
--------------------

The install copied them into the client's own skills directory. From then on
they are yours: a later release of this server does not reach them, so the
server compares what it published against what is there every time a client
connects and says so where they differ. ``update`` is what refreshes them. The
:doc:`task-skill catalog <task-skills/index>` names the published set and opens
each complete workflow.

A skill that never activates is the other half, and it is not a fault in the
install. A client chooses a skill by its description before it loads anything of
it, so a task described in words that description does not carry runs without
it, and nothing reports that it happened. Where a workflow you expected did not
run, say the subject the way the skill names it and watch whether it activates.

Without a client at all
-----------------------

Where none of the above settles it, ask the server directly. Two lines on stdin
start it and list what it offers, which separates a server that cannot start
from a client that never started it:

.. code-block:: bash

    printf '%s\n' \
      '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"t","version":"1"}}}' \
      '{"jsonrpc":"2.0","id":2,"method":"tools/list"}' \
      | php bin/typo3-dev-companion

Run it from the project you installed into. The server has no directory of its
own: what it reads is decided by where it was started, which is the same reason
the wrong working directory produces answers about the wrong site.
