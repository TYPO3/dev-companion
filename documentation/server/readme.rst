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


.. diagram::
    :zoomable:
    :caption: The route is explicit, so a plausible answer does not silently become the wrong one.

    flow canvas=960
      columns 1,1,1
    title "Context first, specialist lookup second"
    description "A task starts with a read of the project and names its workflow. Then it uses a specialist tool and returns the source, version, scope and limits with the answer."

    project `typo3_project_describe`
      at 0,0
      eyebrow "1 · read the project"
      ink
      body "version · extensions · commands"
    guide `typo3_task_guide`
      at 1,0
      eyebrow "2 · identify the task"
      body "workflow · checks · next lookup"
    specialist "Ask the specialist tool"
      at 2,0
      eyebrow "3 · look it up"
      body "component · icon · label · schema · changelog · rule · hint · commit · Forge · Gerrit"
    group sources "What the tool answers from"
      knowledge "Bundled knowledge"
        at 0,1
        body "Version-bound and available offline."
      installation "Project or installation"
        at 1,1
        body "The state that is active here."
      live "Official live source"
        at 2,1
        body "Canonical docs or core service."
    answer "Answer with evidence"
      at 0,2
      span 3
      body "version · scope · answeredBy · limits · structuredContent"
      emphasis

    project -> guide
    guide -> specialist
    specialist -> sources
    sources -> answer

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
