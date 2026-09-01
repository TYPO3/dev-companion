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
than answering empty when it cannot boot. Answers from: installation.

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
``matchCount``, ``answeredBy``, ``columns``, ``tables``, ``actual``, ``updates``
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

schema: one table
~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "tt_content"
    }

From the E-SITE-14.3 environment
""""""""""""""""""""""""""""""""

Text:

.. code-block:: text

    TYPO3 derives 67 columns for tt_content from its TCA. An ext_tables.sql that declares one of them again is declaring what the core already creates.
    The database has tt_content with 68 columns and 5 indexes, and it matches what this installation declares.

    - uid integer NOT NULL
    - pid integer NOT NULL DEFAULT 0
    - tstamp integer NOT NULL DEFAULT 0
    - crdate integer NOT NULL DEFAULT 0
    - deleted smallint NOT NULL DEFAULT 0
    - hidden smallint NOT NULL DEFAULT 0
    - starttime integer NOT NULL DEFAULT 0
    - endtime integer NOT NULL DEFAULT 0
    - fe_group string NOT NULL DEFAULT '0'
    - sorting integer NOT NULL DEFAULT 0
    - rowDescription text
    - editlock smallint NOT NULL DEFAULT 0
    - sys_language_uid integer NOT NULL DEFAULT 0
    - l18n_parent integer NOT NULL DEFAULT 0
    - l10n_source integer NOT NULL DEFAULT 0
    - l10n_state text
    - l18n_diffsource blob
    - t3ver_oid integer NOT NULL DEFAULT 0
    - t3ver_wsid integer NOT NULL DEFAULT 0
    - t3ver_state smallint NOT NULL DEFAULT 0
    - t3ver_stage integer NOT NULL DEFAULT 0
    - CType string NOT NULL DEFAULT 'text'
    - categories integer NOT NULL DEFAULT 0
    - layout integer NOT NULL DEFAULT 0
    - frame_class string NOT NULL DEFAULT 'default'
    - space_before_class string NOT NULL DEFAULT ''
    - space_after_class string NOT NULL DEFAULT ''
    - colPos text
    - date bigint NOT NULL DEFAULT 0
    - header string NOT NULL DEFAULT ''
    - header_layout integer NOT NULL DEFAULT 0
    - header_position string NOT NULL DEFAULT ''
    - header_link text NOT NULL DEFAULT ''
    - subheader string NOT NULL DEFAULT ''
    - bodytext text
    - image integer NOT NULL DEFAULT 0
    - assets integer NOT NULL DEFAULT 0
    - imagewidth integer
    - imageheight integer
    - imageorient integer NOT NULL DEFAULT 0
    - imageborder smallint NOT NULL DEFAULT 0
    - image_zoom smallint NOT NULL DEFAULT 0
    - imagecols integer NOT NULL DEFAULT 2
    - pages text
    - recursive integer NOT NULL DEFAULT 0
    - media integer NOT NULL DEFAULT 0
    - records text
    - sectionIndex smallint NOT NULL DEFAULT 1
    - linkToTop smallint NOT NULL DEFAULT 0
    - pi_flexform text
    - selected_categories text
    - category_field string NOT NULL DEFAULT ''
    - bullets_type integer NOT NULL DEFAULT 0
    - cols integer NOT NULL DEFAULT 0
    - table_class string NOT NULL DEFAULT ''
    - table_caption string NOT NULL DEFAULT ''
    - table_delimiter integer NOT NULL DEFAULT 124
    - table_enclosure integer NOT NULL DEFAULT 0
    - table_header_position integer NOT NULL DEFAULT 0
    - table_tfoot smallint NOT NULL DEFAULT 0
    - file_collections text
    - filelink_size smallint NOT NULL DEFAULT 0
    - filelink_sorting string NOT NULL DEFAULT ''
    - filelink_sorting_direction string NOT NULL DEFAULT ''
    - target string NOT NULL DEFAULT ''
    - uploads_description smallint NOT NULL DEFAULT 0
    - uploads_type integer NOT NULL DEFAULT 0

Data:

.. code-block:: json

    {
        "table": "tt_content",
        "matchCount": 67,
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
                "name": "crdate",
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
            },
            {
                "name": "hidden",
                "type": "smallint",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "starttime",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "endtime",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "fe_group",
                "type": "string",
                "notnull": true,
                "default": "0",
                "length": 255
            },
            {
                "name": "sorting",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "rowDescription",
                "type": "text",
                "notnull": false,
                "default": null,
                "length": 65535
            },
            {
                "name": "editlock",
                "type": "smallint",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "sys_language_uid",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "l18n_parent",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "l10n_source",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "l10n_state",
                "type": "text",
                "notnull": false,
                "default": null,
                "length": 65535
            },
            {
                "name": "l18n_diffsource",
                "type": "blob",
                "notnull": false,
                "default": null,
                "length": 16777215
            },
            {
                "name": "t3ver_oid",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "t3ver_wsid",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "t3ver_state",
                "type": "smallint",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "t3ver_stage",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "CType",
                "type": "string",
                "notnull": true,
                "default": "text",
                "length": 255
            },
            {
                "name": "categories",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "layout",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "frame_class",
                "type": "string",
                "notnull": true,
                "default": "default",
                "length": 255
            },
            {
                "name": "space_before_class",
                "type": "string",
                "notnull": true,
                "default": "",
                "length": 60
            },
            {
                "name": "space_after_class",
                "type": "string",
                "notnull": true,
                "default": "",
                "length": 60
            },
            {
                "name": "colPos",
                "type": "text",
                "notnull": false,
                "default": null,
                "length": null
            },
            {
                "name": "date",
                "type": "bigint",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "header",
                "type": "string",
                "notnull": true,
                "default": "",
                "length": 255
            },
            {
                "name": "header_layout",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "header_position",
                "type": "string",
                "notnull": true,
                "default": "",
                "length": 255
            },
            {
                "name": "header_link",
                "type": "text",
                "notnull": true,
                "default": "",
                "length": 65535
            },
            {
                "name": "subheader",
                "type": "string",
                "notnull": true,
                "default": "",
                "length": 255
            },
            {
                "name": "bodytext",
                "type": "text",
                "notnull": false,
                "default": null,
                "length": null
            },
            {
                "name": "image",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "assets",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "imagewidth",
                "type": "integer",
                "notnull": false,
                "default": null,
                "length": null
            },
            {
                "name": "imageheight",
                "type": "integer",
                "notnull": false,
                "default": null,
                "length": null
            },
            {
                "name": "imageorient",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "imageborder",
                "type": "smallint",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "image_zoom",
                "type": "smallint",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "imagecols",
                "type": "integer",
                "notnull": true,
                "default": 2,
                "length": null
            },
            {
                "name": "pages",
                "type": "text",
                "notnull": false,
                "default": null,
                "length": null
            },
            {
                "name": "recursive",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "media",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "records",
                "type": "text",
                "notnull": false,
                "default": null,
                "length": null
            },
            {
                "name": "sectionIndex",
                "type": "smallint",
                "notnull": true,
                "default": 1,
                "length": null
            },
            {
                "name": "linkToTop",
                "type": "smallint",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "pi_flexform",
                "type": "text",
                "notnull": false,
                "default": null,
                "length": null
            },
            {
                "name": "selected_categories",
                "type": "text",
                "notnull": false,
                "default": null,
                "length": null
            },
            {
                "name": "category_field",
                "type": "string",
                "notnull": true,
                "default": "",
                "length": 64
            },
            {
                "name": "bullets_type",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "cols",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "table_class",
                "type": "string",
                "notnull": true,
                "default": "",
                "length": 60
            },
            {
                "name": "table_caption",
                "type": "string",
                "notnull": true,
                "default": "",
                "length": 255
            },
            {
                "name": "table_delimiter",
                "type": "integer",
                "notnull": true,
                "default": 124,
                "length": null
            },
            {
                "name": "table_enclosure",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "table_header_position",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "table_tfoot",
                "type": "smallint",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "file_collections",
                "type": "text",
                "notnull": false,
                "default": null,
                "length": null
            },
            {
                "name": "filelink_size",
                "type": "smallint",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "filelink_sorting",
                "type": "string",
                "notnull": true,
                "default": "",
                "length": 64
            },
            {
                "name": "filelink_sorting_direction",
                "type": "string",
                "notnull": true,
                "default": "",
                "length": 4
            },
            {
                "name": "target",
                "type": "string",
                "notnull": true,
                "default": "",
                "length": 30
            },
            {
                "name": "uploads_description",
                "type": "smallint",
                "notnull": true,
                "default": 0,
                "length": null
            },
            {
                "name": "uploads_type",
                "type": "integer",
                "notnull": true,
                "default": 0,
                "length": null
            }
        ],
        "tables": [],
        "actual": {
            "present": true,
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
                    "default": "0",
                    "length": null
                },
                {
                    "name": "tstamp",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "crdate",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "deleted",
                    "type": "smallint",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "hidden",
                    "type": "smallint",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "starttime",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "endtime",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "fe_group",
                    "type": "string",
                    "notnull": true,
                    "default": "0",
                    "length": 255
                },
                {
                    "name": "sorting",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "rowDescription",
                    "type": "text",
                    "notnull": false,
                    "default": null,
                    "length": null
                },
                {
                    "name": "editlock",
                    "type": "smallint",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "sys_language_uid",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "l18n_parent",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "l10n_source",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "l10n_state",
                    "type": "text",
                    "notnull": false,
                    "default": null,
                    "length": null
                },
                {
                    "name": "l18n_diffsource",
                    "type": "blob",
                    "notnull": false,
                    "default": null,
                    "length": null
                },
                {
                    "name": "t3ver_oid",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "t3ver_wsid",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "t3ver_state",
                    "type": "smallint",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "t3ver_stage",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "frame_class",
                    "type": "string",
                    "notnull": true,
                    "default": "default",
                    "length": 60
                },
                {
                    "name": "colPos",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "table_caption",
                    "type": "string",
                    "notnull": false,
                    "default": null,
                    "length": 255
                },
                {
                    "name": "tx_impexp_origuid",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "CType",
                    "type": "string",
                    "notnull": true,
                    "default": "text",
                    "length": 255
                },
                {
                    "name": "categories",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "layout",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "space_before_class",
                    "type": "string",
                    "notnull": true,
                    "default": "",
                    "length": 60
                },
                {
                    "name": "space_after_class",
                    "type": "string",
                    "notnull": true,
                    "default": "",
                    "length": 60
                },
                {
                    "name": "date",
                    "type": "bigint",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "header",
                    "type": "string",
                    "notnull": true,
                    "default": "",
                    "length": 255
                },
                {
                    "name": "header_layout",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "header_position",
                    "type": "string",
                    "notnull": true,
                    "default": "",
                    "length": 255
                },
                {
                    "name": "header_link",
                    "type": "text",
                    "notnull": true,
                    "default": "",
                    "length": null
                },
                {
                    "name": "subheader",
                    "type": "string",
                    "notnull": true,
                    "default": "",
                    "length": 255
                },
                {
                    "name": "bodytext",
                    "type": "text",
                    "notnull": false,
                    "default": null,
                    "length": null
                },
                {
                    "name": "image",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "assets",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "imagewidth",
                    "type": "integer",
                    "notnull": false,
                    "default": null,
                    "length": null
                },
                {
                    "name": "imageheight",
                    "type": "integer",
                    "notnull": false,
                    "default": null,
                    "length": null
                },
                {
                    "name": "imageorient",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "imageborder",
                    "type": "smallint",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "image_zoom",
                    "type": "smallint",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "imagecols",
                    "type": "integer",
                    "notnull": true,
                    "default": "2",
                    "length": null
                },
                {
                    "name": "pages",
                    "type": "text",
                    "notnull": false,
                    "default": null,
                    "length": null
                },
                {
                    "name": "recursive",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "media",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "records",
                    "type": "text",
                    "notnull": false,
                    "default": null,
                    "length": null
                },
                {
                    "name": "sectionIndex",
                    "type": "smallint",
                    "notnull": true,
                    "default": "1",
                    "length": null
                },
                {
                    "name": "linkToTop",
                    "type": "smallint",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "pi_flexform",
                    "type": "text",
                    "notnull": false,
                    "default": null,
                    "length": null
                },
                {
                    "name": "selected_categories",
                    "type": "text",
                    "notnull": false,
                    "default": null,
                    "length": null
                },
                {
                    "name": "category_field",
                    "type": "string",
                    "notnull": true,
                    "default": "",
                    "length": 64
                },
                {
                    "name": "bullets_type",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "cols",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "table_class",
                    "type": "string",
                    "notnull": true,
                    "default": "",
                    "length": 60
                },
                {
                    "name": "table_delimiter",
                    "type": "integer",
                    "notnull": true,
                    "default": "124",
                    "length": null
                },
                {
                    "name": "table_enclosure",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "table_header_position",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "table_tfoot",
                    "type": "smallint",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "file_collections",
                    "type": "text",
                    "notnull": false,
                    "default": null,
                    "length": null
                },
                {
                    "name": "filelink_size",
                    "type": "smallint",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "filelink_sorting",
                    "type": "string",
                    "notnull": true,
                    "default": "",
                    "length": 64
                },
                {
                    "name": "filelink_sorting_direction",
                    "type": "string",
                    "notnull": true,
                    "default": "",
                    "length": 4
                },
                {
                    "name": "target",
                    "type": "string",
                    "notnull": true,
                    "default": "",
                    "length": 30
                },
                {
                    "name": "uploads_description",
                    "type": "smallint",
                    "notnull": true,
                    "default": "0",
                    "length": null
                },
                {
                    "name": "uploads_type",
                    "type": "integer",
                    "notnull": true,
                    "default": "0",
                    "length": null
                }
            ],
            "indexes": [
                {
                    "name": "primary",
                    "columns": [
                        "uid"
                    ],
                    "unique": true,
                    "primary": true
                },
                {
                    "name": "t3ver_oid_be974a6e",
                    "columns": [
                        "t3ver_oid",
                        "t3ver_wsid"
                    ],
                    "unique": false,
                    "primary": false
                },
                {
                    "name": "translation_source_fcef6e2f",
                    "columns": [
                        "l10n_source"
                    ],
                    "unique": false,
                    "primary": false
                },
                {
                    "name": "language_identifier_f445694b",
                    "columns": [
                        "l18n_parent",
                        "sys_language_uid"
                    ],
                    "unique": false,
                    "primary": false
                },
                {
                    "name": "parent_8b16a914",
                    "columns": [
                        "pid",
                        "sorting"
                    ],
                    "unique": false,
                    "primary": false
                }
            ]
        },
        "updates": []
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

From the E-SITE-14.3 environment
""""""""""""""""""""""""""""""""

Text:

.. code-block:: text

    TYPO3 derives columns for 23 tables in this installation.
    Name one to see its columns. What is listed for it is what an ext_tables.sql may leave out.

    - be_groups: 25 columns
    - be_users: 29 columns
    - pages: 69 columns
    - sys_category: 21 columns
    - sys_file: 12 columns
    - sys_file_collection: 23 columns
    - sys_file_metadata: 19 columns
    - sys_file_reference: 25 columns
    - sys_file_storage: 15 columns
    - sys_filemounts: 10 columns
    - sys_news: 10 columns
    - backend_layout: 15 columns
    - fe_groups: 10 columns
    - fe_users: 30 columns
    - sys_template: 19 columns
    - tt_content: 67 columns
    - be_dashboards: 10 columns
    - tx_impexp_presets: 4 columns
    - form_definition: 8 columns
    - sys_reaction: 17 columns
    - sys_note: 11 columns
    - sys_webhook: 17 columns
    - sys_category_record_mm: 6 columns (created for an MM relation; declare nothing for it)

Data:

.. code-block:: json

    {
        "table": null,
        "matchCount": 23,
        "answeredBy": "installation",
        "columns": [],
        "tables": [
            {
                "table": "be_groups",
                "columnCount": 25,
                "relationTable": false
            },
            {
                "table": "be_users",
                "columnCount": 29,
                "relationTable": false
            },
            {
                "table": "pages",
                "columnCount": 69,
                "relationTable": false
            },
            {
                "table": "sys_category",
                "columnCount": 21,
                "relationTable": false
            },
            {
                "table": "sys_file",
                "columnCount": 12,
                "relationTable": false
            },
            {
                "table": "sys_file_collection",
                "columnCount": 23,
                "relationTable": false
            },
            {
                "table": "sys_file_metadata",
                "columnCount": 19,
                "relationTable": false
            },
            {
                "table": "sys_file_reference",
                "columnCount": 25,
                "relationTable": false
            },
            {
                "table": "sys_file_storage",
                "columnCount": 15,
                "relationTable": false
            },
            {
                "table": "sys_filemounts",
                "columnCount": 10,
                "relationTable": false
            },
            {
                "table": "sys_news",
                "columnCount": 10,
                "relationTable": false
            },
            {
                "table": "backend_layout",
                "columnCount": 15,
                "relationTable": false
            },
            {
                "table": "fe_groups",
                "columnCount": 10,
                "relationTable": false
            },
            {
                "table": "fe_users",
                "columnCount": 30,
                "relationTable": false
            },
            {
                "table": "sys_template",
                "columnCount": 19,
                "relationTable": false
            },
            {
                "table": "tt_content",
                "columnCount": 67,
                "relationTable": false
            },
            {
                "table": "be_dashboards",
                "columnCount": 10,
                "relationTable": false
            },
            {
                "table": "tx_impexp_presets",
                "columnCount": 4,
                "relationTable": false
            },
            {
                "table": "form_definition",
                "columnCount": 8,
                "relationTable": false
            },
            {
                "table": "sys_reaction",
                "columnCount": 17,
                "relationTable": false
            },
            {
                "table": "sys_note",
                "columnCount": 11,
                "relationTable": false
            },
            {
                "table": "sys_webhook",
                "columnCount": 17,
                "relationTable": false
            },
            {
                "table": "sys_category_record_mm",
                "columnCount": 6,
                "relationTable": true
            }
        ],
        "actual": null,
        "updates": null
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
