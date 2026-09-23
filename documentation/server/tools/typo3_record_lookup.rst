.. _typo3_record_lookup:

``typo3_record_lookup``
=======================

*Read a table's rows*

Read the rows of any table this installation has TCA for. That is pages,
tt_content, a table of one of this project's own extensions, or one a dependency
brings. It answers how many there are, which page they sit on, and whether they
are live, hidden or deleted. It answers the rows themselves. That is uid, the
label the table names in its own TCA, the timestamps, the two flags, and any
column named in columns. That is what a backend visit tells you and the one
question typo3_schema_lookup cannot answer. That tool returns the shape of the
table, and this one returns what is in it. It is also the fact that decides
where editors maintain records. A table with a few dozen rows fits the generic
record list. One with three thousand on a single storage folder needs a module
with its own filter and pages. Pass groupBy to get what values a column holds
across the table. The TCA default stands beside them, with the uid of every row
that departs from it. That answer decides whether a CType, a TCA default, a
markup class or a template branch can go. The single row that departs is the one
a cleanup breaks. Narrow it with where, which takes exact values for any column
of the table, pid among them. Pass count to get the numbers without the rows,
and limit to say how many rows come back. The tool refuses a table TCA does not
describe, which is the caches, the queues and the session store. Omit the table
to see which ones it will read. It reads with the shell user's database access
rather than a backend user's, so no permission, workspace or language filter
narrows what comes back. It never writes. Answers from: installation.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`.

Takes
-----

.. code-block:: yaml

    # The table to read, for example "tt_content" or "tx_myext_animal". Omit to list
    # the tables this installation has TCA for, which are the ones the tool reads.
    table: string  # optional
    # Exact values to narrow by, one per column: {"pid": 2, "status": "adopted"}.
    # You can name every column of the table, pid and uid among them. A column the
    # table does not have gets an answer that says so rather than an empty result.
    # Exact equality only — there is no operator, no wildcard and no range, which
    # is what keeps this a lookup rather than a query language.
    where: object  # optional
    # True to answer with the numbers alone and read no row. Use it where the
    # question is how much is in there rather than what.
    count: boolean  # optional
    # One column to count per distinct value of, for example "CType",
    # "header_layout" or "status". The answer then carries one line per value with
    # how many rows carry it. That is the distribution a call per value asks
    # thirteen times for. It also carries the column's TCA default and the uid and
    # pid of the rows that depart from it, capped. That says whether a value is the
    # site's convention or its one exception. Combines with where, which narrows the
    # count. A column the table does not have gets an answer that says so.
    groupBy: string  # optional
    # Columns each row carries beside the ones it always has: ["CType",
    # "frame_class", "header_layout"]. Name as many as the question needs, and
    # typo3_schema_lookup lists what the table has. A column the table does not have
    # gets an answer that says so rather than an empty value.
    columns: [string]  # optional
    # How many rows to return, ordered by uid. The default is one page of the record
    # list. Zero means every row that matches, which on a full table is the whole
    # table in one answer.
    limit: integer  # optional

Answers with
------------

.. code-block:: yaml

    # The table asked about. Null where the call named none and the answer is the
    # list of readable ones.
    table: string or null
    # Rows that match the filter, whatever the limit returned. Tables for a call
    # that named none. Zero on a table the tool refuses as well as on one that is
    # empty, and the text says which.
    matchCount: integer  # optional
    # One of: installation. installation: its assembled runtime state answered.
    answeredBy: string  # optional
    # The filter the read ran under, echoed so a count reported onwards carries what
    # it counted. A list rather than a map keyed by column, because an empty map is
    # [] in JSON and a schema that says object refuses it. A client reads one shape
    # either way. Empty where the read covered the whole table.
    where:  # optional
      - column: string
        # The value the column matched against, exactly as the call passed it.
        value: string or number or boolean or null
    # Null where the call read no table.
    counts:  # optional
      # Every row that matches, whatever state it is in.
      total: integer
      # Rows that are neither hidden nor deleted.
      live: integer
      # Rows the disable field hides. Zero where the table declares no such field.
      hidden: integer
      # Rows the delete field marks. They are still in the table until the garbage
      # collection runs.
      deleted: integer
    # One entry per distinct value of the grouped column, the fullest first. A value
    # with no rows is not here: the distribution is what the table holds, and you
    # read a status nothing carries off its absence. Empty where groupBy was not
    # passed.
    groups:  # optional
      - # The value of the grouped column, as the database stores it. Null is a row
        # that has none, which on a select column is the empty string rather than
        # null.
        value: string or number or boolean or null
        # Rows with that value, deleted and hidden included.
        total: integer
        live: integer
        hidden: integer
        deleted: integer
    # What the grouped column's TCA declares as its default, so you read a value as
    # the convention or as a departure from it. Null where groupBy was not passed
    # and where the column declares no default, which is not the same answer as a
    # default of zero.
    groupDefault: string or number or boolean or null  # optional
    # The rows whose grouped column is not the TCA default, by uid, capped at one
    # page of the record list. This is the half of a distribution that decides
    # something. A value one row in a hundred carries is what a cleanup drops and
    # then breaks. Empty where groupBy was not passed, where the column declares no
    # default, and where every row carries it.
    departing:  # optional
      - # What the backend edits the row by.
        uid: integer
        pid: integer
        # What that row carries instead of the default.
        value: string or number or boolean or null
    # One entry per page that holds a matched row, the fullest first. Empty where
    # the call read no table.
    pages:  # optional
      - # The page the rows sit on. Zero is the root, which is where records that
        # belong to no page end up.
        pid: integer
        total: integer
        live: integer
        hidden: integer
        deleted: integer
    # The rows read, ordered by uid. Empty where count is true, where the call named
    # no table, and where nothing matched.
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
        # The columns the call named, in the order it named them. A list rather than
        # a map keyed by column, for the reason where gives. Empty where the call
        # named none.
        values:
          - column: string
            # What the row stores in that column, as the database has it.
            value: string or number or boolean or null
    # Every table this tool will read in this installation, which is every one TCA
    # describes.
    countable:  # optional
      - table: string
        # The extension whose TCA registers it, read from the EXT: reference in its
        # ctrl title. Empty where the title names none.
        extension: string
    # What made the read. Every answer that carries a number or a row says it. A
    # reader takes what you report onwards as a backend user's view of the table
    # unless it says otherwise.
    readWith: string  # optional
    unsupported:  # optional
      # One of: no-installation, misconfigured, installation-not-answering.
      # no-installation: nothing to ask from here, and searched says where the
      # discovery looked. misconfigured: the caller named an installation the server
      # could not use, so the discovery searched nothing.
      # installation-not-answering: the discovery found one and its console did not
      # answer. A stopped container or a database with no schema is that state, and
      # it ends without a reinstall.
      cause: string
      # What stopped it, in the words the attempt produced.
      reason: string
      # One of: installed, not-installed, undeclared. The state of the repository
      # the caller stands in, which the cause does not say. installed: packages sit
      # below the root the discovery found, so the caller lacks no install.
      # not-installed: the repository declares TYPO3 and has no packages below it
      # yet, so this call answers once composer install has run. undeclared: nothing
      # in the directories the discovery walked declares TYPO3, so an install here
      # answers nothing. Null where the discovery looked at nothing: a named root
      # the server could not use, or an entrypoint that handed no directory in.
      repositoryState: string or null  # optional
      # What the reason means where the message alone does not say it. A console
      # that starts and then fails on a missing table has a database without a
      # schema, not a broken installation. Empty where the server knows nothing
      # beyond the reason.
      diagnosis: string  # optional
      # Every directory the discovery walked, in order. "Nothing found" and "the
      # server started somewhere else" read the same, and only this list tells them
      # apart. Empty where the discovery never ran.
      searched: [string]
      # The setting the server could not use. Null where the caller set nothing.
      misconfiguration: string or null  # optional
      settings:
        # Environment variable that names the installation root.
        root: string
        # Environment variable that names the console command.
        console: string

The answer carries exactly one of these sets of fields:

- ``table``, ``matchCount``, ``answeredBy``, ``where``, ``counts``, ``groups``,
  ``groupDefault``, ``departing``, ``pages``, ``records``, ``countable``,
  ``readWith``
- ``table``, ``unsupported``

Answered
--------

Recorded on 2026-09-23 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on the one a client stands in. Neither
fills the whole surface. Answered against core-checkout, TYPO3 14.3.8-dev, the
14.3 core checkout below .checkouts/. Its console is out of reach:
<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3
exists. Its dependencies are not installed — vendor/autoload.php is not there
either, and composer install writes both. Answered against composer-project,
TYPO3 14.3.0, the installation this repository writes below .fixtures/. Its
console answers. The tools that declare ``answeredBy`` carry an answer from
each, under a heading that names which. Every other answer comes from the first
alone, because nothing in it would differ. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and ``bin/cli tools:check`` holds it.

records: a table of this project
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "tx_acme_events_event"
    }

From the 14.3 core checkout
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

From the 14.3 core checkout
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

records: what one column holds
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "tx_acme_events_event",
        "groupBy": "venue",
        "count": true
    }

From the 14.3 core checkout
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

    tx_acme_events_event has no column venue, so nothing was read. A filter names columns of the table it filters, and typo3_schema_lookup with table="tx_acme_events_event" lists the ones it has.

Data:

.. code-block:: json

    {
        "table": "tx_acme_events_event",
        "matchCount": 0,
        "answeredBy": "installation",
        "where": [],
        "counts": null,
        "groups": [],
        "groupDefault": null,
        "departing": [],
        "pages": [],
        "records": [],
        "countable": [
            {
                "table": "pages",
                "extension": "core"
            },
            {
                "table": "tt_content",
                "extension": "frontend"
            },
            {
                "table": "tx_acme_events_event",
                "extension": "acme_events"
            }
        ],
        "readWith": "Read with the shell user's database access, with no backend permissions applied and no workspace or language filter, so this is every row in the table rather than what a backend user would see."
    }

records: a table it will not read
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "cache_pages"
    }

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both.

Data:

.. code-block:: json

    {
        "table": "cache_pages",
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

    "cache_pages" is not a table this installation has TCA for, so nothing was read. What TCA does not describe is the caches, the queues and the session store, and none of those holds a record.
    It has TCA for 3 tables, listed by the same call with no table named.

Data:

.. code-block:: json

    {
        "table": "cache_pages",
        "matchCount": 0,
        "answeredBy": "installation",
        "where": [],
        "counts": null,
        "groups": [],
        "groupDefault": null,
        "departing": [],
        "pages": [],
        "records": [],
        "countable": [
            {
                "table": "pages",
                "extension": "core"
            },
            {
                "table": "tt_content",
                "extension": "frontend"
            },
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

From the 14.3 core checkout
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

    This installation has TCA for 3 tables. Name one to read what is in it.

    - pages (core)
    - tt_content (frontend)
    - tx_acme_events_event (acme_events)

Data:

.. code-block:: json

    {
        "matchCount": 3,
        "table": null,
        "answeredBy": "installation",
        "where": [],
        "counts": null,
        "groups": [],
        "groupDefault": null,
        "departing": [],
        "pages": [],
        "records": [],
        "countable": [
            {
                "table": "pages",
                "extension": "core"
            },
            {
                "table": "tt_content",
                "extension": "frontend"
            },
            {
                "table": "tx_acme_events_event",
                "extension": "acme_events"
            }
        ],
        "readWith": "Read with the shell user's database access, with no backend permissions applied and no workspace or language filter, so this is every row in the table rather than what a backend user would see."
    }
