:layout: marketing

TYPO3 Dev Companion
===================

.. hero:: images/dev-companion-robot-server-hero.webp
    :plain:

    A local MCP server in plain PHP. It helps coding agents implement, review
    and verify TYPO3 work for three audiences: the core contributor, the
    extension author and the site developer. It finds the project and the
    installation the agent works in. It supplies current TYPO3 knowledge with
    the versions each statement holds for. The workflow for a kind of task is a
    task skill, and ``typo3_task_guide`` names the skill that covers it.

    It answers for TYPO3 **12.4**, **13.4**, **14.3** and **main**. Where a
    statement does not hold on all of them, it names the lines it holds for. So
    an LTS never gets a convention that only the development line has.

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

    **Experimental.** This is a 0.x package, and its surface can still change.
    The package name, the binary, the namespace and the environment variables
    all changed at once on 2026-08-06. The tool names and the shapes of their
    answers can change the same way. Pin a commit where you depend on it.

.. band:: Quickstart

You need **PHP 8.2+** and Composer. One checkout serves every project on the
machine. Run the last two lines from the root of the project your agent works
in:

.. code-block:: bash

    git clone https://github.com/TYPO3/dev-companion.git typo3-dev-companion
    composer install --working-dir=typo3-dev-companion
    cd /path/to/your/project
    /absolute/path/to/typo3-dev-companion/bin/typo3-dev-companion install --agent=claude

``--agent=claude`` is for Claude Code.
:ref:`The client table <installing-clients>` gives the value for every other
client.

Then restart the client, and approve the server when the client asks. Ask one
question that a client cannot answer from memory: *Which icon identifier is the
right one for a delete button in the TYPO3 backend?* A call to
``typo3_icon_lookup`` behind the answer shows that the server runs. Where no
call is behind it, :doc:`the four steps <usage/index>` say what to do.

.. grid:: flush

    .. card:: :doc:`Usage <usage/index>`
        :icon: actions-rocket

        How this server comes to answer in your own project: the four steps,
        every client the install supports, and the task workflows it publishes.

    .. card:: :doc:`Server <server/index>`
        :icon: actions-book

        What you can ask it and where each answer comes from: one page for each
        tool, the resources beside the tools, and the version binding.

    .. card:: :doc:`Contributing <contributing/index>`
        :icon: actions-code-merge

        How to work on the server itself: the commands that keep this
        repository in order, and the rules for a task skill.

    .. card:: :doc:`Records <records/index>`
        :icon: actions-list

        How feedback from a real session becomes an improvement, and what a
        requirement and a decision each contain.

.. band:: What it will not do
    :quiet:

.. grid::

    .. surface:: It reads

        The server writes nothing into the TYPO3 installation it reads. It does
        write in other places. At start, it puts back stale task skills in the
        project. The feedback channel writes into this server's own checkout,
        and only a standalone checkout offers it.

    .. surface:: It starts nothing

        A lookup starts nothing on your machine. For a stopped DDEV project, the
        server reports the command that would fix it.

    .. surface:: It stays where you started it

        The client starts it as a subprocess over stdio. So you host no server,
        expose nothing to the network and configure no authentication. The
        process boundary is the trust boundary.

    .. surface:: Ask it in English

        The knowledge is in English, and the match is lexical. So the agent
        translates the subject before the call, and the answer back after it.
        The server tells the agent this at initialize.

.. toctree::
    :hidden:

    usage/index
    server/index
    contributing/index
    records/index
