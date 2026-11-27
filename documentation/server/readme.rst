:navigation-title: Server

The server
==========

This section covers what a caller can ask the server, and where each answer
comes from.

.. grid::

    .. card:: :doc:`initialize`

        What a client gets at ``initialize`` and in the lists it fetches right
        after. It shows the result as the server sends it, the exact
        instructions, the prompts, and what the tool list weighs.

    .. card:: :doc:`Tools <tools/index>`

        One page for each tool. Each page says what the tool is for, what it
        takes, which fields it answers with, and what one call returned.

    .. card:: :doc:`Resources <resources/index>`

        The other surface a client gets. A user picks a resource from a list,
        and the agent does not call it in the middle of a task.

    .. card:: :doc:`answer-sources`

        The five sources an answer can come from, and what must run before each
        source can answer.

    .. card:: :doc:`knowledge-base`

        What ``knowledge/`` contains. Almost every answer comes from there.

    .. card:: :doc:`versions`

        How a statement shows that it holds for some TYPO3 versions only. Also
        why a catalog withholds an entry rather than add a qualification.

    .. card:: :doc:`asking-the-installation`

        The order in which the server asks an installation for its own answers,
        how it delivers the probe, and what a fallback must tell the caller.

    .. card:: :doc:`interface-contract`

        A community proposal for an MCP interface contract for TYPO3, as a
        reference. What it says, and what would change here if the community
        adopted it.

Every task takes the same route, so a plausible answer does not silently become
the wrong one:

1. ``typo3_project_describe`` reads the project: its version, its extensions and
   its commands.
2. ``typo3_task_guide`` names the workflow, its checks and the next lookup.
3. A specialist tool answers, from bundled knowledge, the installation or an
   official live source.

Each answer states its version, its scope, the source it came from and its
limits.

The tools are the server. The rest of this section is the other surface beside
them, or says where their answers come from. The description of a tool names the
sources it can answer from. Only the answer shows which source answered one
call. So every answer names its source, its version bound and what it left out.

.. toctree::
    :hidden:

    initialize
    tools/index
    resources/index
    answer-sources
    knowledge-base
    versions
    asking-the-installation
    interface-contract
