:navigation-title: Contributing

Contributing
============

This section covers work on the server itself. It names the commands that keep
this repository in order. It also says what must be true of the session that
does the work.

.. grid::

    .. card:: :doc:`driving-a-session`

        What must be true of an agent session that a command line starts. The
        session runs a forward review or works a todo in a worktree.

    .. card:: :doc:`working-on-the-server`

        The checkouts a session checks a knowledge change against, the
        environments, the test suite, and how the site publishes the
        documentation.

    .. card:: :doc:`writing-a-skill`

        What a new task skill must show before it exists, the rules it follows,
        and the test that holds each rule.

    .. card:: :doc:`glossary`

        The name of everything here, one line each.


A skill follows rules of its own, and each rule names the test that holds it. A
skill is the one text from here that leaves the repository and stays. It goes as
a copy into somebody's project, and the next release of this server does not
correct it there.

:doc:`The records <../records/index>` say where the work comes from and what
stays for the next session. `AGENTS.md <../../AGENTS.md>`_ holds the conventions
every session works under, because a session reads it before anything else.

.. toctree::
    :hidden:

    driving-a-session
    working-on-the-server
    writing-a-skill
    glossary
    ../records/index
