.. _typo3_feedback_record:

``typo3_feedback_record``
=========================

Leave feedback about a gap, wrong answer, or missing capability of this
knowledge server. Leave it about what it did well too, because what worked is
what nobody may break later. The server stores it as markdown in its own
checkout, and typo3_feedback_list reads it back. It is not in the project you
work in, so do not look for the file there. One feedback per subject: a feedback
carrying three complaints is worked off three times over or not at all. Answers
from: checkout.

``readOnlyHint: false`` · ``destructiveHint: false`` · ``idempotentHint: false`` · ``openWorldHint: false``

Answers from :ref:`checkout <answer-sources-checkout>`.

Takes
-----

.. code-block:: yaml

    # One short line saying what only this feedback reports, in English — "a
    # release branch's log answers about the history from before it was cut". It
    # becomes the title and the file name, which is all a maintainer reads when
    # deciding what to open. Left out, both come from the opening of the
    # observation. A session that files several at once then gets several that begin
    # on the same words. The observation opens with the task, so every feedback from
    # one session opens alike. Say what this one says and nothing the others do. At
    # most 100 characters, and a longer line is cut to fit rather than refused.
    subject: string  # optional
    # What was missing, wrong, or unhelpful, specific enough to act on later, in
    # English. Open with one line naming the task you were given, so the feedback
    # can be traced back to what exposed it. A finding is the path of a value, the
    # shape of what came back, and where it came from. It is never the value itself
    # where the installation keeps it secret: an encryption key, a password, a
    # token, the credentials in a connection string. A commit pushes this into a
    # checkout that installation's owner does not watch, so a secret pasted here as
    # proof has left it for good. The finding is "the key at SYS/encryptionKey is
    # the active one, hardcoded in config/system/settings.php"; the 96 characters
    # after it establish nothing further. At most 4000 characters, and a longer text
    # is cut there rather than refused.
    observation: string
    # The model recording this feedback, as it identifies itself, for example
    # claude-opus-5 or gpt-5.3-codex. Read it where it stands: what your client
    # reports for the current session, or the person who runs you. Do not read it
    # from what you remember about yourself. A feedback is evidence about one
    # model's behaviour, and nobody can tell one filed as "unknown" apart from
    # another model's. That fallback is for a session that looked and could not find
    # out; an invented identifier is worse than none.
    model: string
    # One of: missing-knowledge, wrong-answer, tool-gap, bug, idea.
    # missing-knowledge: the knowledge base lacks the answer. wrong-answer: the
    # answer was incorrect. tool-gap: no tool covers the need. bug: the server
    # misbehaved. idea: anything else.
    category: string  # optional
    # The tool the observation is about, for example typo3_component_lookup, or the
    # skill it activated, for example typo3-extension-health. Several are named in
    # one string, separated by commas.
    tool: string  # optional
    # The arguments that produced the unsatisfying result, or the task text where a
    # whole session produced it. So somebody can re-run the feedback against a later
    # version of the server instead of a read. The rule from observation holds: the
    # arguments and the path they named, never a value the installation keeps
    # secret. A re-run needs to know that the call asked for SYS/encryptionKey and
    # that a key came back, not what the key was. Name a password or a token that
    # was itself an argument rather than quote it. At most 4000 characters, and a
    # longer text is cut there rather than refused.
    query: string  # optional
    # What the server should have answered or should be able to do instead. At most
    # 4000 characters, and a longer text is cut there rather than refused.
    suggestion: string  # optional

Answers with
------------

.. code-block:: yaml

    # Path of the recorded feedback, relative to this server's own checkout.
    file: string
    # The same feedback as an absolute path. It is in the server's checkout, not in
    # the project the feedback was recorded from.
    path: string
    # Path of the todo this feedback was queued as, relative to this server's own
    # checkout. Every feedback arrives with one, so the report is on the board
    # rather than waiting for somebody to notice the file.
    todo: string
    # What was removed before the feedback was written, one entry per value, naming
    # the field it stood in and the shape it had. Empty where nothing was removed,
    # which is the ordinary case. Each removal stands in the file as a [redacted:
    # ...] marker, so the report says of itself that it was altered.
    redacted: [string]
    # What was cut for length before the feedback was written, one entry per field,
    # naming the field and how much of it went. Empty where nothing was cut, which
    # is the ordinary case. A cut field stands in the file as a [cut: ...] marker
    # where it stops, so the report says of itself that it is short. A cut subject
    # stands as the ... its title ends in, because a listing shows a marker inside a
    # title.
    cut: [string]

Not answered
------------

And deliberately: it is the one tool here that writes, and this table has two
drivers rather than one. A call recorded from it would file a real feedback into
the open ones every time ``ToolContractTest`` runs, not only when the recording
does.
