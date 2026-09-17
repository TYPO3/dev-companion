.. _typo3_flexform_lookup:

``typo3_flexform_lookup``
=========================

Resolve one TCA field of type=flex to the data structure the installation uses.
That is the identifier TYPO3 produces for it, that identifier decoded, and every
sheet and field of the structure with label, type and items. This is what the
backend form builds, not what the referenced FlexForm file says. The
installation resolves it through its own FlexFormTools. So a data structure a
listener replaced and a sheet in a file of its own are in the answer. So are the
default sDEF sheet a structure without sheets gets, and the TCA migration and
preparation each field goes through. Which structure applies can depend on the
record, so pass the values that decide it in record. That is CType for a content
element or a plugin, and list_type beside it on TYPO3 12 and 13. Nothing loads a
row; the tool emulates the record from exactly those values. Where the
resolution throws, that is the answer. An empty ds, a column that is not
type=flex, and a record type with no structure are that case. The exception
comes back with the keys and the record fields that would have resolved. For the
columns the table itself gets, ask typo3_schema_lookup; for what a content
element registers, typo3_extension_describe. Answers from: installation.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`.

Takes
-----

.. code-block:: yaml

    # The table the column is on, for example "tt_content".
    table: string
    # The type=flex column to resolve, for example "pi_flexform".
    field: string
    # Column values the emulated record carries, as column => value. Pass only what
    # decides which data structure applies. That is "CType" for a content element,
    # and "list_type" beside it for a plugin on TYPO3 12 and 13. Omit it for a
    # column that declares one structure and no record type.
    record: object  # optional

Answers with
------------

.. code-block:: yaml

    # The table asked about.
    table: string
    # The column asked about.
    field: string
    # Whether the installation resolved the column to a data structure. False means
    # the answer is the failure or the declaration beside it, never that the column
    # has none.
    resolved: boolean  # optional
    # The data structure identifier as TYPO3 produced it: the JSON string that
    # resolves to this structure without the record again. Empty where nothing
    # resolved.
    identifier: string  # optional
    # The same identifier as an object. The default carries type, tableName,
    # fieldName and dataStructureKey; a listener may return another shape entirely.
    decoded: object or null  # optional
    # Every sheet of the parsed structure, in the order it declares them.
    sheets:  # optional
      - # The sheet key the values sit under. A structure that declares no sheets
        # gets sDEF here, which the parse adds.
        sheet: string
        title: string
        description: string  # optional
        fields:
          - # The name the value sits under, which is what a Fluid template and a
            # settings array read it by.
            field: string
            # As the structure declares it, an LLL: reference included.
            label: string  # optional
            description: string  # optional
            # The TCA type of this field, or "section" for a repeatable section.
            type: string
            # Empty where the type has no render type.
            renderType: string  # optional
            required: boolean  # optional
            # The default the field declares, null where it declares none or where
            # it is not scalar.
            default: object  # optional
            # The selectable items, where the field has any.
            items:  # optional
              - value: string
                label: string
            # The container types of a section, empty for every other field.
            containers:
              - # The container type key, which is what a section entry stores its
                # type as.
                container: string
                title: string  # optional
                fields:
                  - # The name the value sits under, which is what a Fluid template
                    # and a settings array read it by.
                    field: string
                    # As the structure declares it, an LLL: reference included.
                    label: string  # optional
                    description: string  # optional
                    # The TCA type of this field, or "section" for a repeatable
                    # section.
                    type: string
                    # Empty where the type has no render type.
                    renderType: string  # optional
                    required: boolean  # optional
                    # The default the field declares, null where it declares none or
                    # where it is not scalar.
                    default: object  # optional
                    # The selectable items, where the field has any.
                    items:  # optional
                      - value: string
                        label: string
    # The exception the resolution threw, with its class and code. Empty where it
    # did not throw. It is an answer rather than a breakage. An empty ds, a column
    # that is not type=flex and a record type with no structure all report
    # themselves this way.
    failure: string  # optional
    # What the TCA declares about this column, which is what you retry a call that
    # resolved nothing with.
    declaration:  # optional
      # The TCA type of the column, empty where the table has no such column.
      type: string
      # The column TYPO3 reads the record type from, empty where the table has no
      # record types.
      recordTypeField: string
      # The data structure keys this column declares. Where the TCA holds an array
      # of structures they are its keys. Where it holds one they are "default" plus
      # every record type that overrides it.
      keys: [string]
      # The columns ds_pointerField names, which is what the keys above resolve by.
      # Empty on TYPO3 14 and up, where columnsOverrides replaced the mechanism.
      pointerFields: [string]
      # Every type=flex column this table has, which is what to ask for instead
      # where the named one is not one.
      flexFields: [string]
    # One of: installation. installation: its assembled runtime state answered.
    answeredBy: string  # optional
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

- ``table``, ``field``, ``resolved``, ``identifier``, ``sheets``, ``failure``,
  ``declaration``, ``answeredBy``
- ``table``, ``field``, ``unsupported``

Answered
--------

Recorded on 2026-09-15 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on the one a client stands in. Neither
fills the whole surface. Answered against core-checkout, TYPO3 14.3.7-dev, the
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

flexform: the structure a content element resolves to
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "tt_content",
        "field": "pi_flexform",
        "record": {
            "CType": "acme_events_teaser"
        }
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
        "field": "pi_flexform",
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

    tt_content.pi_flexform resolves to this data structure in this installation, for a record with CType=acme_events_teaser.

    Identifier: {"type":"tca","tableName":"tt_content","fieldName":"pi_flexform","dataStructureKey":"acme_events_teaser"}

    Sheet sDEF — LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:flexform.teaser
      - settings.headline (input) LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:flexform.headline — required
      - settings.layout (select) LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:flexform.layout — items: wide, narrow
      - settings.slides (section) LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:flexform.slides
        container slide
          - settings.slide.title (input) LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:flexform.slide.title

    This went through the installation's own FlexFormTools, so it is what the backend form builds rather than what the referenced file says: a listener may have replaced it, a sheet in a file of its own is resolved, and every field is migrated and prepared. The record was emulated from the values above and no row was read. What is listed per field is what writing or reading the FlexForm needs; the rest of each field's prepared TCA is not carried.

Data:

.. code-block:: json

    {
        "table": "tt_content",
        "field": "pi_flexform",
        "resolved": true,
        "identifier": "{\"type\":\"tca\",\"tableName\":\"tt_content\",\"fieldName\":\"pi_flexform\",\"dataStructureKey\":\"acme_events_teaser\"}",
        "decoded": {
            "type": "tca",
            "tableName": "tt_content",
            "fieldName": "pi_flexform",
            "dataStructureKey": "acme_events_teaser"
        },
        "sheets": [
            {
                "sheet": "sDEF",
                "title": "LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:flexform.teaser",
                "description": "",
                "fields": [
                    {
                        "field": "settings.headline",
                        "label": "LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:flexform.headline",
                        "description": "",
                        "type": "input",
                        "renderType": "",
                        "required": true,
                        "default": null,
                        "items": [],
                        "containers": []
                    },
                    {
                        "field": "settings.layout",
                        "label": "LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:flexform.layout",
                        "description": "",
                        "type": "select",
                        "renderType": "selectSingle",
                        "required": false,
                        "default": "wide",
                        "items": [
                            {
                                "value": "wide",
                                "label": "Wide"
                            },
                            {
                                "value": "narrow",
                                "label": "Narrow"
                            }
                        ],
                        "containers": []
                    },
                    {
                        "field": "settings.slides",
                        "label": "LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:flexform.slides",
                        "description": "",
                        "type": "section",
                        "renderType": "",
                        "required": false,
                        "default": null,
                        "items": [],
                        "containers": [
                            {
                                "container": "slide",
                                "title": "LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:flexform.slide",
                                "fields": [
                                    {
                                        "field": "settings.slide.title",
                                        "label": "LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:flexform.slide.title",
                                        "description": "",
                                        "type": "input",
                                        "renderType": "",
                                        "required": false,
                                        "default": null,
                                        "items": [],
                                        "containers": []
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        ],
        "failure": "",
        "declaration": {
            "type": "flex",
            "recordTypeField": "CType",
            "keys": [
                "default",
                "acme_events_teaser"
            ],
            "pointerFields": [],
            "flexFields": [
                "pi_flexform"
            ]
        },
        "answeredBy": "installation"
    }

flexform: a column that is not one
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "table": "tt_content",
        "field": "bodytext"
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
        "field": "bodytext",
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

    tt_content.bodytext is type=text in this installation, not type=flex, so there is no data structure behind it.

    The resolution said: RuntimeException (1732198004): TCA misconfiguration in table "tt_content" field "bodytext" config section: The field is either not configured as type="flex" or no valid data structure is defined.

    The flex columns of tt_content: pi_flexform.

Data:

.. code-block:: json

    {
        "table": "tt_content",
        "field": "bodytext",
        "resolved": false,
        "identifier": "",
        "decoded": null,
        "sheets": [],
        "failure": "RuntimeException (1732198004): TCA misconfiguration in table \"tt_content\" field \"bodytext\" config section: The field is either not configured as type=\"flex\" or no valid data structure is defined.",
        "declaration": {
            "type": "text",
            "recordTypeField": "CType",
            "keys": [],
            "pointerFields": [],
            "flexFields": [
                "pi_flexform"
            ]
        },
        "answeredBy": "installation"
    }
