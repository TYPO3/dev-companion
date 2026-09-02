.. _typo3_record_lookup:

``typo3_record_lookup``
=======================

Read the rows of a table belonging to one of this project's own extensions: how
many there are, which page they sit on, whether they are live, hidden or
deleted, and the rows themselves — uid, the label the table names in its own
TCA, the timestamps and the two flags. That is what a backend visit would have
told you and the one question typo3_schema_lookup cannot answer: it returns the
shape of the table, this returns what is in it. It is also the fact that decides
where records are maintained, because a table with a few dozen rows is edited in
the generic record list and one with three thousand on a single storage folder
needs a module with its own filtering and paging. Narrow it with where, which
takes exact values for any column of the table, pid among them; pass count to
get the numbers without the rows, and limit to say how many rows come back. It
refuses every table a project-owned extension does not register: pages,
tt_content, the user tables and everything a dependency brings are outside it,
and reading those is the backend's or the installation's own console. Omit the
table to see which ones it will read. It never writes. Answers from:
installation.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`.

Takes
-----

.. code-block:: yaml

    # The table to read, for example "tx_myext_animal". Omit to list the tables this
    # project's own extensions register, which are the ones that can be read.
    table: string  # optional
    # Exact values to narrow by, one per column: {"pid": 2, "status": "adopted"}.
    # Every column of the table can be named, pid and uid among them, and a column
    # the table does not have is an answer saying so rather than an empty result.
    # Exact equality only — there is no operator, no wildcard and no range, which
    # is what keeps this a lookup rather than a query language.
    where: object  # optional
    # True to answer with the numbers alone and read no row. Use it where the
    # question is how much is in there rather than what.
    count: boolean  # optional
    # One column to count per distinct value of, for example "status" or "sex". The
    # answer then carries one line per value with how many rows carry it, which is
    # the distribution a call per value asks thirteen times for. Combines with
    # where, which narrows what is counted. A column the table does not have is an
    # answer saying so.
    groupBy: string  # optional
    # How many rows to return, ordered by uid. The default is one page of the record
    # list. Zero means every matching row, which on a full table is the whole table
    # in one answer.
    limit: integer  # optional

Answers with
------------

.. code-block:: yaml

    # The table asked about. Null where none was named and the answer is the list of
    # readable ones.
    table: string or null
    # Rows matching the filter, whatever the limit returned. Tables for a call that
    # named none. Zero on a table that is refused as well as on one that is empty,
    # and the text says which.
    matchCount: integer  # optional
    # One of: installation. installation: its assembled runtime state answered.
    answeredBy: string  # optional
    # The filter the answer was read under, echoed so a count reported onwards
    # carries what it counted. A list rather than a map keyed by column, because an
    # empty map is [] in JSON and a schema saying object refuses it — a client
    # reads one shape either way. Empty where the whole table was read.
    where:  # optional
      - column: string
        # The value the column was matched against, exactly as it was passed.
        value: object
    # Null where no table was read.
    counts:  # optional
      # Every matching row, whatever state it is in.
      total: integer
      # Rows that are neither hidden nor deleted.
      live: integer
      # Rows the disable field hides. Zero where the table declares no such field.
      hidden: integer
      # Rows the delete field marks. They are still in the table until the garbage
      # collection runs.
      deleted: integer
    # One entry per distinct value of the grouped column, the fullest first. A value
    # with no rows is not here: the distribution is what the table holds, and a
    # status nothing carries is read off its absence. Empty where groupBy was not
    # passed.
    groups:  # optional
      - # The value of the grouped column, as the database stores it. Null is a row
        # that has none, which on a select column is the empty string rather than
        # null.
        value: object
        # Rows carrying that value, deleted and hidden included.
        total: integer
        live: integer
        hidden: integer
        deleted: integer
    # One entry per page that holds a matching row, the fullest first. Empty where
    # no table was read.
    pages:  # optional
      - # The page the rows sit on. Zero is the root, which is where records that
        # belong to no page end up.
        pid: integer
        total: integer
        live: integer
        hidden: integer
        deleted: integer
    # The rows read, ordered by uid. Empty where count was asked for, where no table
    # was named, and where nothing matched.
    records:  # optional
      - # What the backend edits the record by, and what a URL into it carries.
        uid: integer
        pid: integer
        # The column the table names as its label in ctrl. Empty where it names
        # none, which is a property of the table rather than of the row.
        label: string
        # Unix time of the last change, 0 where the table has no tstamp column.
        changed: integer
        # Unix time of creation, 0 where the table has no crdate column.
        created: integer
        deleted: boolean
        hidden: boolean
    # Every table this tool will read in this installation.
    countable:  # optional
      - table: string
        # The project-owned extension whose TCA registers it.
        extension: string
    # What the reading was made with. Said on every answer that carries one, because
    # a number or a row reported onwards is read as a backend user's view of the
    # table unless it says otherwise.
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
      # What the reason means where the message alone does not say it. A console
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
``matchCount``, ``answeredBy``, ``where``, ``counts``, ``groups``, ``pages``,
``records``, ``countable``, ``readWith`` — or ``table``, ``unsupported``.

Answered
--------

Recorded on 2026-09-02 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
15.0.0-dev, the main core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Its dependencies are not installed —
vendor/autoload.php is not there either, and composer install writes both.
Answered against composer-project, TYPO3 14.3.0, the installation this
repository writes below .fixtures/, whose console answers. The tools that
declare ``answeredBy`` carry an answer from each, under a heading naming which;
every other answer is from the first alone, because nothing in it would differ.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

records: a table of this project
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "tx_acme_events_event"
    }

From the main core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both.

Data:

.. code-block:: json

    {
        "table": "tx_acme_events_event",
        "unsupported": {
            "cause": "installation-not-answering",
            "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both",
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

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: the installation booted and could not read tx_acme_events_event: Error: Class "TYPO3\CMS\Core\Database\ConnectionPool" not found.

Data:

.. code-block:: json

    {
        "table": "tx_acme_events_event",
        "unsupported": {
            "cause": "installation-not-answering",
            "reason": "the installation booted and could not read tx_acme_events_event: Error: Class \"TYPO3\\CMS\\Core\\Database\\ConnectionPool\" not found",
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

records: counted rather than read
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "tx_acme_events_event",
        "count": true
    }

From the main core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both.

Data:

.. code-block:: json

    {
        "table": "tx_acme_events_event",
        "unsupported": {
            "cause": "installation-not-answering",
            "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both",
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

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: the installation booted and could not read tx_acme_events_event: Error: Class "TYPO3\CMS\Core\Database\ConnectionPool" not found.

Data:

.. code-block:: json

    {
        "table": "tx_acme_events_event",
        "unsupported": {
            "cause": "installation-not-answering",
            "reason": "the installation booted and could not read tx_acme_events_event: Error: Class \"TYPO3\\CMS\\Core\\Database\\ConnectionPool\" not found",
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

records: a table it will not read
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "tt_content"
    }

From the main core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both.

Data:

.. code-block:: json

    {
        "table": "tt_content",
        "unsupported": {
            "cause": "installation-not-answering",
            "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both",
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

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    "tt_content" is not read here. This tool answers for the tables this project's own extensions register, and a row of any other table is the installation's own — the backend and vendor/bin/typo3 are where those are read, with the permissions that belong to them.
    What it does read: tx_acme_events_event.

Data:

.. code-block:: json

    {
        "table": "tt_content",
        "matchCount": 0,
        "answeredBy": "installation",
        "where": [],
        "counts": null,
        "groups": [],
        "pages": [],
        "records": [],
        "countable": [
            {
                "table": "tx_acme_events_event",
                "extension": "acme_events"
            }
        ],
        "readWith": "Read with the shell user's database access, with no backend permissions applied and no workspace or language filter, so this is every row in the table rather than what a backend user would see."
    }

records: what it reads
~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {}

From the main core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both.

Data:

.. code-block:: json

    {
        "table": null,
        "unsupported": {
            "cause": "installation-not-answering",
            "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both",
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

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    This project's own extensions register 1 tables. Name one to read what is in it.

    - tx_acme_events_event (acme_events)

Data:

.. code-block:: json

    {
        "matchCount": 1,
        "table": null,
        "answeredBy": "installation",
        "where": [],
        "counts": null,
        "groups": [],
        "pages": [],
        "records": [],
        "countable": [
            {
                "table": "tx_acme_events_event",
                "extension": "acme_events"
            }
        ],
        "readWith": "Read with the shell user's database access, with no backend permissions applied and no workspace or language filter, so this is every row in the table rather than what a backend user would see."
    }
