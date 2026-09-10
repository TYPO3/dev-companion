.. _typo3_schema_lookup:

``typo3_schema_lookup``
=======================

List the columns TYPO3 derives for a table from its TCA — uid, pid, the
timestamps, the delete and disable fields, the language and versioning columns,
and one column per TCA field — each with the Doctrine type it gets, whether it
is NOT NULL, and the default the core gives it. That is the DDL side of a TCA
configuration: what column this field produces, whether it can hold SQL NULL,
and what it stores when nothing is written. Those are also exactly the columns
an ext_tables.sql does not have to declare, so this is what a redundant
declaration is checked against. It asks the booted installation about a table
that is in it, so it answers nothing about a table that exists only inside a
functional test, and nothing about a TCA type in the abstract. It describes what
TYPO3 would create, never what the database currently has, and it says so rather
than answering empty when it cannot boot. It is about the shape of the table and
not about what is in it: how many rows one of this project's own tables holds
and what they are is typo3_record_lookup. A type=flex column is one column here
and a data structure elsewhere: what this installation resolves it to, sheet by
sheet, is typo3_flexform_lookup. Answers from: installation.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`.

Takes
-----

.. code-block:: yaml

    # The table to list the derived columns of, for example "tt_content". Omit to
    # list every table TYPO3 derives columns for, with how many each gets.
    table: string  # optional

Answers with
------------

.. code-block:: yaml

    # The table asked about. Null where none was named and the answer is the list of
    # them.
    table: string or null
    # Columns for a named table, tables for a call that named none. Zero means the
    # name is not a TCA table in this installation, never that TYPO3 derives
    # nothing.
    matchCount: integer  # optional
    # One of: installation. installation: its assembled runtime state answered.
    answeredBy: string  # optional
    # Empty where no table was named.
    columns:  # optional
      - name: string
        # The Doctrine type the core declares it as: integer, string, text,
        # datetime, json, blob.
        type: string
        notnull: boolean
        # The default the core gives it, null where it declares none.
        default: object  # optional
        # Length where the type carries one.
        length: integer or null  # optional
    # Every table TYPO3 derives columns for. Returned on a call that named none, and
    # on one whose name is not among them.
    tables:  # optional
      - table: string
        columnCount: integer
        # True where TYPO3 creates the table itself for an MM relation. No
        # ext_tables.sql declares one at all.
        relationTable: boolean
    # What the database has for the named table, read from the connection that table
    # maps to. Null where no table was named, or where the schema could not be read
    # — a project that is down, or an installation whose tables were never
    # created.
    actual:  # optional
      # Whether the database has the table at all.
      present: boolean
      columns:
        - name: string
          # The Doctrine type the column has, read from the connection.
          type: string
          notnull: boolean
          # The default the column carries, null where it has none.
          default: object  # optional
          # Length where the type carries one.
          length: integer or null  # optional
      indexes:
        - name: string
          columns: [string]
          unique: boolean
          primary: boolean
    # What TYPO3 would change to make the database match the schema its active
    # extensions and its TCA declare. Empty where the two match, and null where no
    # schema could be read. Where a table was named, only that table's changes are
    # here.
    updates: array or null  # optional
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
``matchCount``, ``answeredBy``, ``columns``, ``tables``, ``actual``, ``updates``
— or ``table``, ``unsupported``.

Answered
--------

Recorded on 2026-09-10 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.7-dev, the 14.3 core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Its dependencies are not installed —
vendor/autoload.php is not there either, and composer install writes both.
Answered against composer-project, TYPO3 14.3.0, the installation this
repository writes below .fixtures/, whose console answers. The tools that
declare ``answeredBy`` carry an answer from each, under a heading naming which;
every other answer is from the first alone, because nothing in it would differ.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

schema: one table
~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "tt_content"
    }

From the 14.3 core checkout
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

    TYPO3 derives 4 columns for tt_content from its TCA. An ext_tables.sql that declares one of them again is declaring what the core already creates.
    The database was not readable from here, so what follows is the derived side alone.

    - uid integer NOT NULL
    - pid integer NOT NULL DEFAULT 0
    - tstamp integer NOT NULL DEFAULT 0
    - deleted smallint NOT NULL DEFAULT 0

Data:

.. code-block:: json

    {
        "table": "tt_content",
        "matchCount": 4,
        "answeredBy": "installation",
        "columns": [
            {
                "name": "uid",
                "type": "integer",
                "notnull": true,
                "default": null,
                "length": null
            },
            {
                "name": "pid",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "tstamp",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "deleted",
                "type": "smallint",
                "notnull": true,
                "default": 0,
                "length": null
            }
        ],
        "tables": [],
        "actual": null,
        "updates": null
    }

schema: every table
~~~~~~~~~~~~~~~~~~~

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

    TYPO3 derives columns for 4 tables in this installation.
    Name one to see its columns. What is listed for it is what an ext_tables.sql may leave out.

    - tt_content: 4 columns
    - pages: 4 columns
    - tx_acme_events_event: 4 columns
    - tx_acme_events_event_category_mm: 3 columns (created for an MM relation; declare nothing for it)

Data:

.. code-block:: json

    {
        "table": null,
        "matchCount": 4,
        "answeredBy": "installation",
        "columns": [],
        "tables": [
            {
                "table": "tt_content",
                "columnCount": 4,
                "relationTable": false
            },
            {
                "table": "pages",
                "columnCount": 4,
                "relationTable": false
            },
            {
                "table": "tx_acme_events_event",
                "columnCount": 4,
                "relationTable": false
            },
            {
                "table": "tx_acme_events_event_category_mm",
                "columnCount": 3,
                "relationTable": true
            }
        ],
        "actual": null,
        "updates": null
    }
