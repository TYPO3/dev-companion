:navigation-title: Server

The server
==========

What a caller can ask it, and where each answer comes from.

.. grid::

    .. card:: :doc:`initialize`

        What a client gets at ``initialize`` and in the lists it fetches right
        after: the result as the server sends it, the instructions in their
        wording, the prompts, and what the tool list weighs.

    .. card:: :doc:`Tools <tools/index>`

        Every tool, one page each. What it is for, what it takes, which fields
        it answers with, and what one call to it came back with.

    .. card:: :doc:`Resources <resources/index>`

        The other surface a client gets, the one a user picks out of a list
        rather than calls mid-task.

    .. card:: :doc:`answer-sources`

        The five sources an answer can come from, and what each one needs up
        before it can.

    .. card:: :doc:`knowledge-base`

        What ships in ``knowledge/``, which is where almost every answer comes
        from.

    .. card:: :doc:`versions`

        How a statement that holds for some TYPO3 versions and not others says
        so, and why a catalog withholds an entry instead of a qualification.

    .. card:: :doc:`asking-the-installation`

        The order the server looks an installation's own answers up in, how it
        delivers the probe, and what a fallback owes the caller.

    .. card:: :doc:`interface-contract`

        A community proposal for an MCP interface contract for TYPO3, read as a
        reference. What it says, and what would change here if the community
        adopted it.


.. diagram::
    :zoomable:
    :caption: The route is explicit, so a plausible answer does not silently become the wrong one.

    flow
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

The tools are the server. Everything else on this page is either the other
surface beside them or an account of where their answers come from. That is the
one thing a caller cannot see from a tool's own description. It is the reason
every answer names its source, its version bound and what it left out.

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
