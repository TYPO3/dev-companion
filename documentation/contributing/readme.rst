:navigation-title: Contributing

Contributing
============

The commands that keep this repository in order, and what has to be true of the
session that does the work.

* :doc:`working-on-the-server` — the checkouts a session verifies a knowledge
  change against, the environments, the test suite, and how the documentation
  gets published.
* :doc:`driving-a-session` — what has to be true of an agent session started
  from a command line. That holds whether it carries a forward run or works a
  todo in a worktree.
* :doc:`writing-a-skill` — what a new task skill has to show before it exists,
  the rules it follows, and what holds each one.
* :doc:`glossary` — the name of everything here, one line each.

A skill is the one thing written here that leaves the repository and stays. It
goes as a copy into somebody's project, where the next release of this server
does not correct it. That is why it follows rules of its own and why every one
of them names the test that holds it.

Where the work comes from and what stays behind for the next session is
:doc:`the records <../records/index>`. The conventions every session works under
are `AGENTS.md <../../AGENTS.md>`_, because a session reads them before anything
else.

.. toctree::
    :hidden:

    driving-a-session
    working-on-the-server
    writing-a-skill
    glossary
