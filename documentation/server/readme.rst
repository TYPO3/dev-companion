:navigation-title: Server

The server
==========

What a caller can ask it, and where each answer comes from.

* :doc:`tools/ <tools/index>` — every tool, one page each. What it is for, what
  it takes, which fields it answers with, and what one call to it came back
  with.
* :doc:`resources/ <resources/index>` — the other surface a client gets, the one
  a user picks out of a list rather than calls mid-task.
* :doc:`answer-sources` — the five sources an answer can come from, and what
  each one needs up before it can.
* :doc:`knowledge-base` — what ships in ``knowledge/``, which is where almost
  every answer comes from.
* :doc:`versions` — how a statement that holds for some TYPO3 versions and not
  others says so. Also why a catalog withholds an entry instead of a
  qualification.
* :doc:`asking-the-installation` — the order the server looks an installation's
  own answers up in, how it delivers the probe, and what a fallback owes the
  caller.
* :doc:`interface-contract` — a community proposal for an MCP interface contract
  for TYPO3, read as a reference. What it says and what would change here if the
  community ever adopted it.

.. image:: ../images/answer-flow.svg
    :zoomable:
        :alt: A task starts with a read of the project and names its workflow. Then
          it uses a specialist tool and returns the source, version, scope and
          limits with the answer.

The tools are the server. Everything else on this page is either the other
surface beside them or an account of where their answers come from. That is the
one thing a caller cannot see from a tool's own description. It is the reason
every answer names its source, its version bound and what it left out.

.. toctree::
    :hidden:

    tools/index
    resources/index
    answer-sources
    knowledge-base
    versions
    asking-the-installation
    interface-contract
