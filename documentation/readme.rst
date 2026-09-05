:layout: marketing

TYPO3 Dev Companion
===================

.. hero:: images/dev-companion-robot-server-hero.png

    A local MCP server (plain PHP) that helps coding agents implement, review
    and verify TYPO3 work for the three audiences that do it: the core
    contributor, the extension author and the site developer. It establishes
    the project and installation the agent is working in, supplies current,
    version-bound TYPO3 knowledge, and hands task-specific workflows to the
    skills that own them.

    It answers for TYPO3 **12.4**, **13.4**, **14.3** and **main**. A statement
    that does not hold on all of them names the ones it does, so an LTS is never
    handed a convention that only the development line has.

    .. button-bar::

        .. button:: :doc:`Get started <usage/index>`
            :size: lg
            :icon: actions-rocket

        .. button:: GitHub
            :href: https://github.com/TYPO3/dev-companion
            :size: lg
            :variant: secondary
            :icon: actions-brand-github
            :rel: external

.. warning::

    **Experimental.** This is a 0.x package and its surface is not settled. The
    package name, the binary, the namespace and the environment variables all
    changed in one cut on 2026-08-06. The tool names and the shapes they answer
    in can move the same way. Pin a commit where you depend on it.

.. band:: Quickstart

Requirements: **PHP 8.2+** and Composer. One checkout serves every project on
the machine, and the last two lines are run from the root of the project the
agent works in:

.. code-block:: bash

    git clone https://github.com/TYPO3/dev-companion.git typo3-dev-companion
    composer install --working-dir=typo3-dev-companion
    cd /path/to/your/project
    /absolute/path/to/typo3-dev-companion/bin/typo3-dev-companion install

Then restart the client, approve the server when it asks, and ask it one
question a client cannot answer from memory: *which icon identifier is the right
one for a delete button in the TYPO3 backend?* A call to ``typo3_icon_lookup``
behind the answer means it runs. :doc:`The four steps <usage/index>` say what to
do where it does not.

.. grid:: flush

    .. card:: :doc:`Usage <usage/index>`
        :icon: actions-rocket

        Having this server answering in your own project: the four steps, every
        client the install supports, and the task workflows it publishes.

    .. card:: :doc:`Server <server/index>`
        :icon: actions-book

        What it can be asked and where each answer comes from: every tool one
        page each, the resources beside them, and the version binding.

    .. card:: :doc:`Contributing <contributing/index>`
        :icon: actions-code-merge

        Working on the server itself: the commands this repository is kept in
        order by, and the rules a task skill is written under.

    .. card:: :doc:`Records <records/index>`
        :icon: actions-list

        How feedback from a real session becomes an improvement, and what a
        requirement and a decision each hold.

.. band:: What it will not do
    :quiet:

.. grid::

    .. surface:: It reads

        Nothing is written into the TYPO3 installation it is pointed at. The one
        exception is the feedback channel, which writes into this server's own
        checkout and is offered from a standalone checkout alone.

    .. surface:: It starts nothing

        Nothing on your machine is started as a side effect of a lookup. A
        stopped DDEV project is reported with the command that would fix it.

    .. surface:: It stays where you started it

        The client launches it as a subprocess over stdio, so there is no server
        to host, no network exposure and no auth to configure — the process
        boundary is the trust boundary.

    .. surface:: It is queried in English

        The knowledge is written in English and the matching is lexical, so the
        agent translates the subject before calling and the answer back
        afterwards. The server states this at initialize.

.. toctree::
    :hidden:

    usage/index
    server/index
    contributing/index
    records/index
