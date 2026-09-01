.. _typo3_record_lookup:

``typo3_record_lookup``
=======================

Count the rows a table of one of this project's own extensions holds, broken
down by the page they sit on and by whether they are live, hidden or deleted.
That is the fact that decides where records are maintained: a table with a few
dozen rows is edited in the generic record list, and one with three thousand on
a single storage folder needs a backend module with its own filtering and
paging, which nothing else in this server will tell you. It counts and nothing
more — no column of any row is read, so it answers nothing about what a record
contains, and asking what one says is the backend's or the installation's own
console. It refuses every table a project-owned extension does not register:
pages, tt_content, the user tables and everything a dependency brings are
outside it, because a count of those is an inventory of somebody else's
installation rather than a fact about the work. Omit the table to see which ones
it will count. Answers from: installation.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`.

Takes
-----

.. code-block:: yaml

    # The table to count, for example "tx_myext_animal". Omit to list the tables
    # this project's own extensions register, which are the ones that can be
    # counted.
    table: string  # optional

Answers with
------------

.. code-block:: yaml

    # The table asked about. Null where none was named and the answer is the list of
    # countable ones.
    table: string or null
    # Rows for a named table, countable tables for a call that named none. Zero on a
    # table that is refused as well as on one that is empty, and the text says
    # which.
    matchCount: integer  # optional
    # One of: installation. installation: its assembled runtime state answered.
    answeredBy: string  # optional
    # Null where no table was counted.
    rows:  # optional
      # Every row in the table, whatever state it is in.
      total: integer
      # Rows that are neither hidden nor deleted.
      live: integer
      # Rows the disable field hides. Zero where the table declares no such field.
      hidden: integer
      # Rows the delete field marks. They are still in the table until the garbage
      # collection runs.
      deleted: integer
    # One entry per page that holds a row, the fullest first. Empty where no table
    # was counted.
    pages:  # optional
      - # The page the rows sit on. Zero is the root, which is where records that
        # belong to no page end up.
        pid: integer
        total: integer
        live: integer
        hidden: integer
        deleted: integer
    # Every table this tool will count in this installation.
    countable:  # optional
      - table: string
        # The project-owned extension whose TCA registers it.
        extension: string
    # What the count was read with. Said on every answer that carries one, because a
    # number reported onwards is read as a backend user's view of the table unless
    # it says otherwise.
    readWith: string  # optional
    unsupported:  # optional
      # One of: no-installation, misconfigured, installation-not-answering.
      # no-installation: nothing to ask from here, and searched says where it
      # looked. misconfigured: an installation was named and could not be used, so
      # nothing was searched for. installation-not-answering: one was found and its
      # console did not answer — a stopped container or a database with no schema,
      # which is a state that ends without reinstalling anything.
      cause: string
      # What stopped it, in the words the attempt produced.
      reason: string
      # One of: installed, not-installed, undeclared, null. Which state the
      # repository the caller stands in is in, which the cause does not say.
      # installed: packages are installed below the root that was found, so an
      # install is not what is missing here. not-installed: the repository declares
      # TYPO3 and nothing is installed below it yet, so this call is answerable once
      # composer install has run. undeclared: nothing in the directories walked
      # declares TYPO3, so an install here would answer nothing. Null where nothing
      # was looked at: a named root that could not be used, or an entrypoint that
      # handed no directory in.
      repositoryState: string or null  # optional
      # What the reason means where the message alone does not say it — a console
      # that starts and then fails on a missing table has a database without a
      # schema, not a broken installation. Empty where nothing beyond the reason is
      # known.
      diagnosis: string  # optional
      # Every directory the discovery walked, in order. "Nothing was found" and "the
      # server was started somewhere else" wear one sentence, and only this tells
      # them apart. Empty where discovery never ran.
      searched: [string]
      # What was set and could not be used. Null where nothing was set.
      misconfiguration: string or null  # optional
      settings:
        # Environment variable that names the installation root.
        root: string
        # Environment variable that names the console command.
        console: string

The answer carries exactly one of these sets of fields: ``table``,
``matchCount``, ``answeredBy``, ``rows``, ``pages``, ``countable``, ``readWith``
— or ``table``, ``unsupported``.

Answered
--------

Recorded on 2026-09-01 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against composer-project, TYPO3
14.3.5, the E-SITE-14.3 this repository makes below .environments/, whose
console answers. Answered against composer-project, TYPO3 14.3.0, the
installation this repository writes below .fixtures/, whose console answers.
The tools that declare ``answeredBy`` carry an answer from each, under a
heading naming which; every other answer is from the first alone, because
nothing in it would differ. Nothing checks what is below this heading;
everything above it is derived from the class that answers the call, and
``bin/cli tools:check`` holds it.

records: a table of this project
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "tx_acme_events_event"
    }

From the E-SITE-14.3 environment
""""""""""""""""""""""""""""""""

Text:

.. code-block:: text

    "tx_acme_events_event" is not counted here. This tool answers for the tables this project's own extensions register, and a row of any other table is the installation's own — the backend and vendor/bin/typo3 are where those are read, with the permissions that belong to them.
    No extension of this project registers a table of its own.

Data:

.. code-block:: json

    {
        "table": "tx_acme_events_event",
        "matchCount": 0,
        "answeredBy": "installation",
        "rows": null,
        "pages": [],
        "countable": [],
        "readWith": "Counted with the shell user's database access, with no backend permissions applied and no workspace or language filter, so this is every row in the table rather than what a backend user would see."
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: the installation booted and could not count tx_acme_events_event: Error: Class "TYPO3\CMS\Core\Database\ConnectionPool" not found.

Data:

.. code-block:: json

    {
        "table": "tx_acme_events_event",
        "unsupported": {
            "cause": "installation-not-answering",
            "reason": "the installation booted and could not count tx_acme_events_event: Error: Class \"TYPO3\\CMS\\Core\\Database\\ConnectionPool\" not found",
            "repositoryState": "installed",
            "diagnosis": "",
            "searched": [
                "<installation>"
            ],
            "misconfiguration": null,
            "settings": {
                "root": "TYPO3_DEV_COMPANION_ROOT",
                "console": "TYPO3_DEV_COMPANION_CONSOLE"
            }
        }
    }

records: a table it will not count
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "tt_content"
    }

From the E-SITE-14.3 environment
""""""""""""""""""""""""""""""""

Text:

.. code-block:: text

    "tt_content" is not counted here. This tool answers for the tables this project's own extensions register, and a row of any other table is the installation's own — the backend and vendor/bin/typo3 are where those are read, with the permissions that belong to them.
    No extension of this project registers a table of its own.

Data:

.. code-block:: json

    {
        "table": "tt_content",
        "matchCount": 0,
        "answeredBy": "installation",
        "rows": null,
        "pages": [],
        "countable": [],
        "readWith": "Counted with the shell user's database access, with no backend permissions applied and no workspace or language filter, so this is every row in the table rather than what a backend user would see."
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    "tt_content" is not counted here. This tool answers for the tables this project's own extensions register, and a row of any other table is the installation's own — the backend and vendor/bin/typo3 are where those are read, with the permissions that belong to them.
    What it does count: tx_acme_events_event.

Data:

.. code-block:: json

    {
        "table": "tt_content",
        "matchCount": 0,
        "answeredBy": "installation",
        "rows": null,
        "pages": [],
        "countable": [
            {
                "table": "tx_acme_events_event",
                "extension": "acme_events"
            }
        ],
        "readWith": "Counted with the shell user's database access, with no backend permissions applied and no workspace or language filter, so this is every row in the table rather than what a backend user would see."
    }

records: what it counts
~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {}

From the E-SITE-14.3 environment
""""""""""""""""""""""""""""""""

Text:

.. code-block:: text

    No extension of this project registers a table of its own, so there is nothing here to count. A project whose content lives in pages and tt_content is that case.

Data:

.. code-block:: json

    {
        "table": null,
        "matchCount": 0,
        "answeredBy": "installation",
        "rows": null,
        "pages": [],
        "countable": [],
        "readWith": "Counted with the shell user's database access, with no backend permissions applied and no workspace or language filter, so this is every row in the table rather than what a backend user would see."
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    This project's own extensions register 1 tables. Name one to count its rows, which is what says whether it is still edited in the record list.

    - tx_acme_events_event (acme_events)

Data:

.. code-block:: json

    {
        "table": null,
        "matchCount": 1,
        "answeredBy": "installation",
        "rows": null,
        "pages": [],
        "countable": [
            {
                "table": "tx_acme_events_event",
                "extension": "acme_events"
            }
        ],
        "readWith": "Counted with the shell user's database access, with no backend permissions applied and no workspace or language filter, so this is every row in the table rather than what a backend user would see."
    }
