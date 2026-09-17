.. _typo3_feedback_list:

``typo3_feedback_list``
=======================

*List the recorded feedback*

List the feedback typo3_feedback_record recorded, newest first, so a session can
work them off. Filter by status, by category, or by the tool a feedback is
about. The archive keeps a feedback a session worked off, so status="closed"
answers "what became of what I reported". That is the feedback as it arrived,
plus the commit that closed it. Answers from: checkout.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`checkout <answer-sources-checkout>`.

Takes
-----

.. code-block:: yaml

    # One of: open, closed, all. open: the feedback nobody has worked off yet.
    # closed: the ones a session worked off, each with the commit subject that says
    # what came of it. all: both. The category and tool filters apply to either.
    status: string  # optional
    # One of: missing-knowledge, wrong-answer, tool-gap, bug, idea. Restrict the
    # list to one category.
    category: string  # optional
    # Restrict the list to the feedback about one tool, for example
    # typo3_label_lookup. A feedback that names several tools matches each of them.
    tool: string  # optional
    # Maximum number of feedback to return.
    limit: integer  # optional

Answers with
------------

.. code-block:: yaml

    count: integer
    notes:
      - file: string
        date: string
        category: string
        # open while the feedback waits for a session, closed once a session moved
        # it to the archive.
        status: string
        # The model that left the feedback. "unknown" where it named none or
        # predates the field.
        model: string
        # The tools the feedback is about, comma-separated. Empty when it names
        # none.
        tool: string
        # The same names as a list, to filter or group by without a parse.
        tools: [string]
        title: string
        # The commit that worked the feedback off. Null while the feedback is open.
        closedBy:
          commit: string  # optional
          date: string  # optional
          # The commit subject: what came of the feedback.
          subject: string  # optional

Not answered
------------

And deliberately: it answers with the feedback somebody else wrote. That differs
in every checkout and carries the tool names that were current on the day of
each feedback. One recorded title ends in a tool name cut to length, which reads
to ``ToolNamingTest`` as a tool this server does not have.
