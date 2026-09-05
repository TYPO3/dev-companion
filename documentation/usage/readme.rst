:navigation-title: Usage

Using the server
================

Four steps from nothing to the first answered question. Each is short here and
names the page that has the detail.

1. Install it
-------------

One standalone checkout serves every project on the machine. ``install`` writes
into the directory it is run in, so the last two lines are run from the root of
the project the agent works in, never from the checkout:

.. code-block:: bash

    git clone https://github.com/TYPO3/dev-companion.git typo3-dev-companion
    composer install --working-dir=typo3-dev-companion
    cd /path/to/your/project
    /absolute/path/to/typo3-dev-companion/bin/typo3-dev-companion install

Without ``--agent`` that writes the entry into ``.mcp.json`` and the skills into
``.agents/skills``, the two places a client finds without being configured for
it. A client that reads elsewhere is named — ``--agent=cursor``,
``--agent=copilot`` — and :ref:`the client table <installing-clients>` says
which file each one gets. A project can require the package instead of pointing
at a checkout; that and every other case is :doc:`installing`.

2. Finish in the client
-----------------------

A file on disk registers nothing. The client has to read it, and most ask you to
approve a project server before starting it. Claude Code reads ``.mcp.json``
when a session starts, so restart the session and approve at the prompt. The
install prints what your client needs under the line that reports the entry, and
:ref:`installing-finishing-in-the-client` has the same list with each client's
own documentation behind it.

3. Ask it one question
----------------------

In the client you work in:

    Which icon identifier is the right one for a delete button in the TYPO3
    backend?

Then look at what the agent did: a call to ``typo3_icon_lookup`` means the
server answers. An answer with no call behind it means the client never started
it, and :doc:`checking-it-answers` is how to find out why.

4. Describe your task
---------------------

There is nothing more to drive. Describe the work in whatever language you speak
to your agent in, and the server tells the agent at connect where to start.
:doc:`working-with-it` is what that changes on your side of the conversation,
and :doc:`task-skills/ <task-skills/index>` lists the workflows the install
published beside the server, with their complete instructions.

Afterwards
----------

* ``typo3-dev-companion update`` refreshes the skills and the entry after this
  package moved; a server that finds stale copies puts them back on its own —
  :ref:`installing-keeping-it-current`.
* Which tools are offered, and the four environment variables the entry may
  carry — :ref:`installing-what-the-entry-may-carry`.
* Taking it out again — :ref:`installing-removing-it`.

It is a local subprocess, started by the client over stdio, and it reads. It
writes nothing into the TYPO3 installation it is pointed at — the one exception
is the feedback channel, which writes into this server's own checkout and is
offered from a standalone checkout alone.

.. toctree::
    :hidden:

    installing
    task-skills/index
    checking-it-answers
    working-with-it
