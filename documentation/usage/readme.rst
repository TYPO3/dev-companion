:navigation-title: Usage

Using the server
================

This page covers the four steps from nothing to the first answered question.
Each step is short here and names the page with the detail.

.. steps::

    .. step:: Install it

        One standalone checkout serves every project on the machine. ``install``
        writes into the directory you run it in. So run the last two lines from
        the root of the project your agent works in, never from the checkout:

        .. code-block:: bash

            git clone https://github.com/TYPO3/dev-companion.git typo3-dev-companion
            composer install --working-dir=typo3-dev-companion
            cd /path/to/your/project
            /absolute/path/to/typo3-dev-companion/bin/typo3-dev-companion install

        Without ``--agent``, ``install`` writes the entry into ``.mcp.json``
        and the skills into ``.agents/skills``. A client finds those two places
        without any configuration. For a client that reads from other places,
        name it, as in ``--agent=claude`` for Claude Code or ``--agent=cursor``.
        :ref:`The client table <installing-clients>` says which file each
        client gets.

        A project can also require the package instead of naming a checkout.
        :doc:`installing` covers that case and every other one.

    .. step:: Finish in the client

        A file on disk registers nothing. The client must read it, and most
        clients ask you to approve a project server before they start it.
        Claude Code reads ``.mcp.json`` when a session starts. So restart the
        session, and approve the server at the prompt.

        The install prints what your client needs under the line that reports
        the entry. :ref:`installing-finishing-in-the-client` has the same list,
        with the documentation of each client behind it.

    .. step:: Ask it one question

        In the client you work in:

            Which icon identifier is the right one for a delete button in the
            TYPO3 backend?

        Then look at what the agent did. A call to ``typo3_icon_lookup`` means
        that the server answers. An answer with no call behind it means that the
        client never started the server. :doc:`checking-it-answers` shows how to
        find out why.

    .. step:: Describe your task

        You need to do nothing more. Describe the work in the language you use
        with your agent. At connect, the server tells the agent where to start.

        :doc:`working-with-it` says what that changes on your side of the
        conversation. :doc:`task-skills/ <task-skills/index>` lists the
        workflows the install published beside the server, with their complete
        instructions.

Afterwards
----------

* ``typo3-dev-companion update`` refreshes the skills and the entry after this
  package changes. A server that finds stale copies puts them back by itself.
  See :ref:`installing-keeping-it-current`.

* :ref:`installing-what-the-entry-may-carry` lists which tools the server offers
  and the five environment variables the entry can set.

* :ref:`The section on removal <installing-removing-it>` says how to take the
  server out again.

The server is a local subprocess that the client starts over stdio. It writes
nothing into the TYPO3 installation it reads. It does write in other places. At
start, it puts back stale task skills in the project. The feedback channel
writes into this server's own checkout, and only a standalone checkout offers
it.

.. toctree::
    :hidden:

    installing
    task-skills/index
    checking-it-answers
    working-with-it
