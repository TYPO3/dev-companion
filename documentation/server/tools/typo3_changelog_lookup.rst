.. _typo3_changelog_lookup:

``typo3_changelog_lookup``
==========================

*Search the core changelog*

Search the TYPO3 changelog: one entry per breaking change, deprecation, feature
and important note, in the version of its release. This reads the entries. An
entry for a core patch of your own is the other direction, and it is
typo3_rule_lookup with documentId "core/contribution/changelog". Answers "what
did this version deprecate", "what changed about X", "which release introduced
Y". This is the first stop when you build on a major you have not built on
recently. What separates a current answer from a two-major-old one stands here
and almost nowhere else. A deprecation carries the version it stops to work in
where the entry states one, and the rule that answers the rest beside it. The
tool reads the entries from docs.typo3.org, which renders them after every
merge, so a version you have not installed and a change merged today are both in
reach. Where docs.typo3.org does not answer, it reads the core package on disk.
An entry has to carry every word of the query; narrow further with type and
version. A version and a type with the query omitted list whole under a raised
limit. That is the deprecation sweep of one major in a single call. A method or
class you found in the code is a query of its own. An identifier reaches the
entries that name it, whether or not the change has its title. A PHP class or
method does so in every version, by the class index docs.typo3.org publishes.
Any other identifier, a constant or a configuration key, does so only where the
installation answers for the version, from the text on disk. Answers from:
packages, network.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`packages <answer-sources-packages>`,
:ref:`network <answer-sources-network>`.

Takes
-----

.. code-block:: yaml

    # Words the entry has to carry, matched against its file name and the words that
    # name spells. Where no entry carries all of them by name, the search reads the
    # title inside the file as well. That reaches a method name the file name leaves
    # out. A class, method or constant name reaches the entries that write it in
    # their text. So you ask for a removed API by the identifier you have, in any
    # spelling: bare, qualified by its class, or fully qualified. The issue number
    # is among the words a file name carries. So a deprecation's own number reaches
    # every entry filed under it, the Feature that announced the replacement with
    # its release version among them. When nothing carries all of them there either,
    # the answer names the largest part of the query that does reach entries. That
    # is what to ask again with. Omit to list a version or a type as a whole.
    query: string  # optional
    # One of: breaking, deprecation, feature, important. Restrict to one kind of
    # change. Breaking and deprecation are what affects code you have.
    type: string  # optional
    # Restrict to a version, by prefix: "14" covers 14.0 through 14.3.x, "13.4"
    # covers 13.4 and 13.4.x.
    version: string  # optional
    # Restrict to entries with this index tag. "ext:form" is the system extension a
    # change is in. "FullyScanned" or "NotScanned" is what the Extension Scanner has
    # a matcher for. "PHP-API", "TCA", "Backend" and "Frontend" are the surface.
    # This bounds one question inside a version and a type. The sweep of a major
    # does not need it. That version and type come back whole under a raised limit,
    # and every entry carries its own tags to read by. The changelog says nothing
    # about which third-party extension a change affects, so an extension key of
    # your own matches no tag.
    tag: string  # optional
    # Maximum number of entries. Raise it to list a version and a type whole. The
    # largest covered major holds 128 deprecations, and that sweep is one call
    # rather than one per tag.
    limit: integer  # optional

Answers with
------------

.. code-block:: yaml

    query: string
    # Entries that carry every word of the query and the tag, before the limit.
    matchCount: integer  # optional
    # Where the query matched. "name" is the entry names. "body" is where no name
    # carried it and the inside of the file did. That is the title as the file
    # states it, or an identifier the text writes. A body match can name the
    # identifier and not be about it, so read the title of each. Returned where the
    # answer carries entries.
    matchedIn: string  # optional
    # Every index tag the entries of this version and type carry, with the ones
    # already filtered by among them. Returned where the call named a tag, so you
    # replace a tag that matched nothing with one that exists.
    tags: [string]  # optional
    entries:  # optional
      - # One of: Breaking, Deprecation, Feature, Important.
        type: string
        # The version directory of its release.
        version: string
        # Forge issue number.
        issue: string
        title: string
        # The version a Deprecation states the deprecated thing stops to work in,
        # which is what an upgrade decides on. Empty on the other three types, and
        # on a deprecation whose entry states none. That is most of a major and does
        # not mean "no removal planned"; removalRule answers it there.
        removal: string
        # What to write instead, as the entry's own Migration section states it,
        # code blocks included. Carried where the call reached one entry, by an
        # issue number or a query that matched one. A sweep of seventy-five is a
        # list of titles and not seventy-five migrations. Empty on every entry of a
        # longer answer, and on an entry whose file states no migration.
        migration: string
        # Index tags. FullyScanned or PartiallyScanned means the extension scanner
        # has a matcher for it.
        tags: [string]
        # Where to read the description and the migration: an EXT: reference where
        # the installation ships the entry, and the docs.typo3.org URL where it does
        # not.
        file: string
        # The entry as docs.typo3.org renders it, on every entry.
        url: string
        # Which side the entry came from. "manual" is docs.typo3.org, which renders
        # the changelog after every merge and is the ordinary source. "installation"
        # is the core package on disk, read where docs.typo3.org did not answer or
        # does not list the version. An entry for a major without a release yet
        # moves either way.
        publishedIn: string
    # What each word of the query reaches on its own, inside the version and the
    # type the call named. A word at 0 is the one that emptied the answer: a
    # misspelt word, or nothing here carries its name. Returned on a miss that
    # carried words. These are counts and not a query; termSubsets is what you ask
    # outright.
    termCounts:  # optional
      - # The word, lowercased as the search used it.
        term: string
        matchCount: integer
    # The same words counted over the whole changelog rather than inside the version
    # and the type. Returned only where a word reaches there and nothing inside the
    # narrowed set. Then the filter emptied this answer rather than the words: ask
    # again without it.
    termCountsWithoutTheNarrowing:  # optional
      - # The word, lowercased as the search used it.
        term: string
        matchCount: integer
    # The largest parts of the query that do reach entries, narrowest first. Every
    # one of them, because the one a tie-break puts first is not always the one you
    # look for. Withheld where the call names a tag. The count runs off the entry
    # names and a tag sits inside the file. So a subset offered there promises
    # entries the same call does not return.
    termSubsets:  # optional
      - # Words of the query, as a query to ask again with.
        terms: [string]
        # Entries that carry every word of this subset, inside the same version and
        # type.
        matchCount: integer
    # When a deprecation stops to work where the entry itself does not say. Returned
    # where the answer carries a deprecation.
    removalRule: string  # optional
    # The versions this installation ships changelog entries for, newest first.
    # Empty where no installation was found.
    versions: [string]  # optional
    # The versions read from docs.typo3.org, newest first, inside the version and
    # the type the call named. Absent where docs.typo3.org did not answer, which is
    # the one case this answer lacks a version rather than the changelog.
    versionsFromTheManual: [string]  # optional
    # One of: packages. packages: the server read the files the installed packages
    # ship, because it could not ask the console. The answer misses the overrides
    # that apply at runtime.
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

- ``query``, ``matchCount``, ``entries``, ``versions``, ``answeredBy``
- ``query``, ``unsupported``

Answered
--------

Recorded on 2026-09-18 by ``bin/cli tools:record``. Of two working directories,
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

changelog: hit
~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "ext_tables.php"
    }

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    1 changelog entry carrying "ext_tables.php":
    - 14.3 Deprecation: ext_tables.php in extensions (#109438) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.html — PHP-API, NotScanned, ext:core

    Migration
    Move all registration from `ext_tables.php` to the appropriate
    configuration files.

    ### User settings {#user-settings}

    User settings previously registered via
    `TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings()` in
    `ext_tables.php` should now be registered via
    `TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserSetting()` in
    `Configuration/TCA/Overrides/be_users.php`.

    Before:

    **ext_tables.php**

    ```php
    use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

    $GLOBALS['TYPO3_USER_SETTINGS']['columns']['myCustomSetting'] = [
        'type' => 'check',
        'label' => 'LLL:EXT:my_ext/Resources/Private/Language/locallang.xlf:myCustomSetting',
    ];
    ExtensionManagementUtility::addFieldsToUserSettings(
        'myCustomSetting',
        'after:emailMeAtLogin'
    );
    ```

    After:

    **Configuration/TCA/Overrides/be_users.php**

    ```php
    use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

    ExtensionManagementUtility::addUserSetting(
        'myCustomSetting',
        [
            'label' => 'LLL:EXT:my_ext/Resources/Private/Language/locallang.xlf:myCustomSetting',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'after:emailMeAtLogin'
    );
    ```

    ### Page doktype allowed record types {#page-doktype-allowed-record-types}

    Page doktypes previously registered via `PageDoktypeRegistry->add()` in
    `ext_tables.php` should now use the TCA option
    `allowedRecordTypes` in `Configuration/TCA/Overrides/pages.php`.

    Before:

    **ext_tables.php**

    ```php
    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry::class
    )->add(116, [
        'allowedTables' => ['tt_content', 'my_custom_record'],
    ]);
    ```

    After:

    **Configuration/TCA/Overrides/pages.php**

    ```php
    $GLOBALS['TCA']['pages']['types']['116']['allowedRecordTypes'] = [
        'tt_content',
        'my_custom_record',
    ];
    ```

    Once all registrations have been moved, the `ext_tables.php` file
    can be removed from the extension.

    The migration above is the entry's own section. Read the file for the rest of the description. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
    An entry marked manual is what docs.typo3.org renders today, after every merge, and it links by URL. For a major that is not released yet it is still being written. An identifier search reaches only the entries this installation ships, whose text is on disk.
    A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned.

Data:

.. code-block:: json

    {
        "query": "ext_tables.php",
        "matchCount": 1,
        "matchedIn": "name",
        "tags": [],
        "entries": [
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109438",
                "title": "ext_tables.php in extensions",
                "removal": "15.0",
                "migration": "Move all registration from `ext_tables.php` to the appropriate\nconfiguration files.\n\n### User settings {#user-settings}\n\nUser settings previously registered via\n`TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility::addFieldsToUserSettings()` in\n`ext_tables.php` should now be registered via\n`TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility::addUserSetting()` in\n`Configuration/TCA/Overrides/be_users.php`.\n\nBefore:\n\n**ext_tables.php**\n\n```php\nuse TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility;\n\n$GLOBALS['TYPO3_USER_SETTINGS']['columns']['myCustomSetting'] = [\n    'type' => 'check',\n    'label' => 'LLL:EXT:my_ext/Resources/Private/Language/locallang.xlf:myCustomSetting',\n];\nExtensionManagementUtility::addFieldsToUserSettings(\n    'myCustomSetting',\n    'after:emailMeAtLogin'\n);\n```\n\nAfter:\n\n**Configuration/TCA/Overrides/be_users.php**\n\n```php\nuse TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility;\n\nExtensionManagementUtility::addUserSetting(\n    'myCustomSetting',\n    [\n        'label' => 'LLL:EXT:my_ext/Resources/Private/Language/locallang.xlf:myCustomSetting',\n        'config' => [\n            'type' => 'check',\n            'renderType' => 'checkboxToggle',\n        ],\n    ],\n    'after:emailMeAtLogin'\n);\n```\n\n### Page doktype allowed record types {#page-doktype-allowed-record-types}\n\nPage doktypes previously registered via `PageDoktypeRegistry->add()` in\n`ext_tables.php` should now use the TCA option\n`allowedRecordTypes` in `Configuration/TCA/Overrides/pages.php`.\n\nBefore:\n\n**ext_tables.php**\n\n```php\n\\TYPO3\\CMS\\Core\\Utility\\GeneralUtility::makeInstance(\n    \\TYPO3\\CMS\\Core\\DataHandling\\PageDoktypeRegistry::class\n)->add(116, [\n    'allowedTables' => ['tt_content', 'my_custom_record'],\n]);\n```\n\nAfter:\n\n**Configuration/TCA/Overrides/pages.php**\n\n```php\n$GLOBALS['TCA']['pages']['types']['116']['allowedRecordTypes'] = [\n    'tt_content',\n    'my_custom_record',\n];\n```\n\nOnce all registrations have been moved, the `ext_tables.php` file\ncan be removed from the extension.",
                "tags": [
                    "PHP-API",
                    "NotScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.html",
                "publishedIn": "manual"
            }
        ],
        "versions": [
            "14.3",
            "14.3.x",
            "14.2",
            "14.1",
            "14.0",
            "13.4",
            "13.4.x",
            "13.3",
            "13.2",
            "13.1",
            "13.0",
            "12.4",
            "12.4.x",
            "12.3",
            "12.2",
            "12.1",
            "12.0",
            "11.5",
            "11.5.x",
            "11.4",
            "11.3",
            "11.2",
            "11.1",
            "11.0",
            "10.4",
            "10.4.x",
            "10.3",
            "10.2",
            "10.1",
            "10.0",
            "9.5",
            "9.5.x",
            "9.4",
            "9.3",
            "9.2",
            "9.1",
            "9.0",
            "8.7",
            "8.7.x",
            "8.6",
            "8.5",
            "8.4",
            "8.3",
            "8.2",
            "8.1",
            "8.0",
            "7.6",
            "7.6.x",
            "7.5",
            "7.4",
            "7.3",
            "7.2",
            "7.1",
            "7.0"
        ],
        "answeredBy": "packages",
        "versionsFromTheManual": [
            "15.0",
            "14.3",
            "14.3.x",
            "14.2",
            "14.1",
            "14.0",
            "13.4",
            "13.4.x",
            "13.3",
            "13.2",
            "13.1",
            "13.0",
            "12.4",
            "12.4.x",
            "12.3",
            "12.2",
            "12.1",
            "12.0"
        ],
        "removalRule": "A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned."
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    1 changelog entry carrying "ext_tables.php":
    - 14.3 Deprecation: ext_tables.php in extensions (#109438) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.html — PHP-API, NotScanned, ext:core

    Migration
    Move all registration from `ext_tables.php` to the appropriate
    configuration files.

    ### User settings {#user-settings}

    User settings previously registered via
    `TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings()` in
    `ext_tables.php` should now be registered via
    `TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserSetting()` in
    `Configuration/TCA/Overrides/be_users.php`.

    Before:

    **ext_tables.php**

    ```php
    use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

    $GLOBALS['TYPO3_USER_SETTINGS']['columns']['myCustomSetting'] = [
        'type' => 'check',
        'label' => 'LLL:EXT:my_ext/Resources/Private/Language/locallang.xlf:myCustomSetting',
    ];
    ExtensionManagementUtility::addFieldsToUserSettings(
        'myCustomSetting',
        'after:emailMeAtLogin'
    );
    ```

    After:

    **Configuration/TCA/Overrides/be_users.php**

    ```php
    use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

    ExtensionManagementUtility::addUserSetting(
        'myCustomSetting',
        [
            'label' => 'LLL:EXT:my_ext/Resources/Private/Language/locallang.xlf:myCustomSetting',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'after:emailMeAtLogin'
    );
    ```

    ### Page doktype allowed record types {#page-doktype-allowed-record-types}

    Page doktypes previously registered via `PageDoktypeRegistry->add()` in
    `ext_tables.php` should now use the TCA option
    `allowedRecordTypes` in `Configuration/TCA/Overrides/pages.php`.

    Before:

    **ext_tables.php**

    ```php
    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry::class
    )->add(116, [
        'allowedTables' => ['tt_content', 'my_custom_record'],
    ]);
    ```

    After:

    **Configuration/TCA/Overrides/pages.php**

    ```php
    $GLOBALS['TCA']['pages']['types']['116']['allowedRecordTypes'] = [
        'tt_content',
        'my_custom_record',
    ];
    ```

    Once all registrations have been moved, the `ext_tables.php` file
    can be removed from the extension.

    The migration above is the entry's own section. Read the file for the rest of the description. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
    An entry marked manual is what docs.typo3.org renders today, after every merge, and it links by URL. For a major that is not released yet it is still being written. An identifier search reaches only the entries this installation ships, whose text is on disk.
    A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned.

Data:

.. code-block:: json

    {
        "query": "ext_tables.php",
        "matchCount": 1,
        "matchedIn": "name",
        "tags": [],
        "entries": [
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109438",
                "title": "ext_tables.php in extensions",
                "removal": "15.0",
                "migration": "Move all registration from `ext_tables.php` to the appropriate\nconfiguration files.\n\n### User settings {#user-settings}\n\nUser settings previously registered via\n`TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility::addFieldsToUserSettings()` in\n`ext_tables.php` should now be registered via\n`TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility::addUserSetting()` in\n`Configuration/TCA/Overrides/be_users.php`.\n\nBefore:\n\n**ext_tables.php**\n\n```php\nuse TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility;\n\n$GLOBALS['TYPO3_USER_SETTINGS']['columns']['myCustomSetting'] = [\n    'type' => 'check',\n    'label' => 'LLL:EXT:my_ext/Resources/Private/Language/locallang.xlf:myCustomSetting',\n];\nExtensionManagementUtility::addFieldsToUserSettings(\n    'myCustomSetting',\n    'after:emailMeAtLogin'\n);\n```\n\nAfter:\n\n**Configuration/TCA/Overrides/be_users.php**\n\n```php\nuse TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility;\n\nExtensionManagementUtility::addUserSetting(\n    'myCustomSetting',\n    [\n        'label' => 'LLL:EXT:my_ext/Resources/Private/Language/locallang.xlf:myCustomSetting',\n        'config' => [\n            'type' => 'check',\n            'renderType' => 'checkboxToggle',\n        ],\n    ],\n    'after:emailMeAtLogin'\n);\n```\n\n### Page doktype allowed record types {#page-doktype-allowed-record-types}\n\nPage doktypes previously registered via `PageDoktypeRegistry->add()` in\n`ext_tables.php` should now use the TCA option\n`allowedRecordTypes` in `Configuration/TCA/Overrides/pages.php`.\n\nBefore:\n\n**ext_tables.php**\n\n```php\n\\TYPO3\\CMS\\Core\\Utility\\GeneralUtility::makeInstance(\n    \\TYPO3\\CMS\\Core\\DataHandling\\PageDoktypeRegistry::class\n)->add(116, [\n    'allowedTables' => ['tt_content', 'my_custom_record'],\n]);\n```\n\nAfter:\n\n**Configuration/TCA/Overrides/pages.php**\n\n```php\n$GLOBALS['TCA']['pages']['types']['116']['allowedRecordTypes'] = [\n    'tt_content',\n    'my_custom_record',\n];\n```\n\nOnce all registrations have been moved, the `ext_tables.php` file\ncan be removed from the extension.",
                "tags": [
                    "PHP-API",
                    "NotScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.html",
                "publishedIn": "manual"
            }
        ],
        "versions": [
            "14.3"
        ],
        "answeredBy": "packages",
        "versionsFromTheManual": [
            "15.0",
            "14.3",
            "14.3.x",
            "14.2",
            "14.1",
            "14.0",
            "13.4",
            "13.4.x",
            "13.3",
            "13.2",
            "13.1",
            "13.0",
            "12.4",
            "12.4.x",
            "12.3",
            "12.2",
            "12.1",
            "12.0"
        ],
        "removalRule": "A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned."
    }

changelog: swept by tag
~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "type": "deprecation",
        "tag": "FullyScanned"
    }

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    390 of the 972 entries narrowed by version and type are tagged "FullyScanned" — showing the first 20:
    - 15.0 Deprecation: Experimental backend ViewHelpers (#110148) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110148-ExperimentalBackendViewHelpers.html — Fluid, FullyScanned, ext:fluid
    - 15.0 Deprecation: StringUtility::multibyteStringPad() method (#110202) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110202-StringUtilityMultibyteStringPad.html — PHP-API, FullyScanned, ext:core
    - 15.0 Deprecation: DataHandler->setCorrelationId() (#110285) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110285-DataHandlerSetCorrelationId.html — PHP-API, FullyScanned, ext:core
    - 15.0 Deprecation: AbstractXmlSitemapDataProvider (#110334) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110334-AbstractXmlSitemapDataProvider.html — PHP-API, FullyScanned, ext:seo
    - 15.0 Deprecation: LazyLoadingProxy and LazyObjectStorage (#110347) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110347-LazyLoadingProxyAndLazyObjectStorage.html — PHP-API, FullyScanned, ext:extbase
    - 15.0 Deprecation: CommandRegistry->getSchedulableCommands() (#110477) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110477-CommandRegistryGetSchedulableCommands.html — CLI, FullyScanned, PHP-API, ext:core
    - 14.3 Deprecation: Lowlevel DatabaseIntegrityCheck class (#107931) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-107931-LowlevelDatabaseIntegrityCheck.html — PHP-API, FullyScanned, ext:lowlevel
    - 14.3 Deprecation: BackendUtility item list label methods (#109519) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109519-BackendUtilityItemListLabelMethods.html — PHP-API, FullyScanned, ext:backend
    - 14.3 Deprecation: GeneralUtility::isOnCurrentHost() without PSR-7 request (#109523)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109523-GeneralUtilityIsOnCurrentHostWithoutRequest.html — PHP-API, FullyScanned, ext:core
    - 14.3 Deprecation: GeneralUtility::sanitizeLocalUrl() needs PSR-7 request (#109544)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109544-GeneralUtilitySanitizeLocalUrlWithoutRequest.html — PHP-API, FullyScanned, ext:core
    - 14.3 Deprecation: GeneralUtility::locationHeaderUrl() without PSR-7 request (#109548)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109548-GeneralUtilityLocationHeaderUrlWithoutRequest.html — PHP-API, FullyScanned, ext:core
    - 14.3 Deprecation: GeneralUtility::getIndpEnv() (#109551)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109551-GeneralUtilityGetIndpEnv.html — PHP-API, FullyScanned, ext:core
    - 14.2 Deprecation: BackendUserAuthentication::recordEditAccessInternals() and $errorMsg (#108568)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.html — PHP-API, FullyScanned, ext:core
    - 14.2 Deprecation: BackendUtility TSconfig-related methods (#108761) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.html — PHP-API, FullyScanned, ext:backend
    - 14.2 Deprecation: BackendUtility localization-related methods (#108810) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.html — PHP-API, FullyScanned, ext:backend
    - 14.2 Deprecation: ExtensionManagementUtility::addFieldsToUserSettings (#108843) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.html — PHP-API, FullyScanned, ext:core
    - 14.2 Deprecation: Deprecate PageRenderer->addInlineLanguageDomain() (#108963)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.html — Backend, JavaScript, FullyScanned, ext:backend
    - 14.2 Deprecation: Move language:update command and events to EXT:core (#109027) — removed in v15
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.html — CLI, PHP-API, FullyScanned, ext:install
    - 14.2 Deprecation: FormResultCompiler (#109230) — removed in v15
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109230-FormResultCompiler.html — Backend, FullyScanned, ext:backend
    - 14.2 Deprecation: TypoScript-based form YAML registration (#109412) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.html — YAML, Frontend, Backend, FullyScanned, ext:form

    Read the file for the description and the migration, or ask again for the one entry by its issue number, which carries its migration section whole. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
    An entry marked manual is what docs.typo3.org renders today, after every merge, and it links by URL. For a major that is not released yet it is still being written. An identifier search reaches only the entries this installation ships, whose text is on disk.
    A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned.

Data:

.. code-block:: json

    {
        "query": "",
        "matchCount": 390,
        "matchedIn": "name",
        "tags": [
            "Backend",
            "CLI",
            "Database",
            "FAL",
            "FileList",
            "FlexForm",
            "Fluid",
            "Frontend",
            "FullyScanned",
            "JavaScript",
            "LocalConfiguration",
            "NotScanned",
            "PHP-API",
            "PartiallyScanned",
            "RTE",
            "Scheduler",
            "TCA",
            "TSConfig",
            "TypoScript",
            "YAML",
            "ext:adminpanel",
            "ext:backend",
            "ext:core",
            "ext:css_styled_content",
            "ext:dashboard",
            "ext:dbal",
            "ext:extbase",
            "ext:extensionmanager",
            "ext:feedit",
            "ext:felogin",
            "ext:filelist",
            "ext:fluid",
            "ext:fluid_styled_content",
            "ext:form",
            "ext:frontend",
            "ext:impexp",
            "ext:indexed_search",
            "ext:info",
            "ext:install",
            "ext:lang",
            "ext:linkvalidator",
            "ext:lowlevel",
            "ext:recordlist",
            "ext:recycler",
            "ext:redirects",
            "ext:reports",
            "ext:rsaauth",
            "ext:rte_ckeditor",
            "ext:saltedpasswords",
            "ext:scheduler",
            "ext:seo",
            "ext:setup",
            "ext:t3editor",
            "ext:taskcenter",
            "ext:tstemplate",
            "ext:workspaces"
        ],
        "entries": [
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110148",
                "title": "Experimental backend ViewHelpers",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "Fluid",
                    "FullyScanned",
                    "ext:fluid"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110148-ExperimentalBackendViewHelpers.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110148-ExperimentalBackendViewHelpers.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110202",
                "title": "StringUtility::multibyteStringPad() method",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110202-StringUtilityMultibyteStringPad.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110202-StringUtilityMultibyteStringPad.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110285",
                "title": "DataHandler->setCorrelationId()",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110285-DataHandlerSetCorrelationId.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110285-DataHandlerSetCorrelationId.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110334",
                "title": "AbstractXmlSitemapDataProvider",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:seo"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110334-AbstractXmlSitemapDataProvider.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110334-AbstractXmlSitemapDataProvider.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110347",
                "title": "LazyLoadingProxy and LazyObjectStorage",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:extbase"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110347-LazyLoadingProxyAndLazyObjectStorage.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110347-LazyLoadingProxyAndLazyObjectStorage.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110477",
                "title": "CommandRegistry->getSchedulableCommands()",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "CLI",
                    "FullyScanned",
                    "PHP-API",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110477-CommandRegistryGetSchedulableCommands.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110477-CommandRegistryGetSchedulableCommands.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "107931",
                "title": "Lowlevel DatabaseIntegrityCheck class",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:lowlevel"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-107931-LowlevelDatabaseIntegrityCheck.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-107931-LowlevelDatabaseIntegrityCheck.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109519",
                "title": "BackendUtility item list label methods",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109519-BackendUtilityItemListLabelMethods.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109519-BackendUtilityItemListLabelMethods.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109523",
                "title": "GeneralUtility::isOnCurrentHost() without PSR-7 request",
                "removal": "",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109523-GeneralUtilityIsOnCurrentHostWithoutRequest.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109523-GeneralUtilityIsOnCurrentHostWithoutRequest.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109544",
                "title": "GeneralUtility::sanitizeLocalUrl() needs PSR-7 request",
                "removal": "",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109544-GeneralUtilitySanitizeLocalUrlWithoutRequest.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109544-GeneralUtilitySanitizeLocalUrlWithoutRequest.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109548",
                "title": "GeneralUtility::locationHeaderUrl() without PSR-7 request",
                "removal": "",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109548-GeneralUtilityLocationHeaderUrlWithoutRequest.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109548-GeneralUtilityLocationHeaderUrlWithoutRequest.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109551",
                "title": "GeneralUtility::getIndpEnv()",
                "removal": "",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109551-GeneralUtilityGetIndpEnv.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109551-GeneralUtilityGetIndpEnv.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108568",
                "title": "BackendUserAuthentication::recordEditAccessInternals() and $errorMsg",
                "removal": "",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108761",
                "title": "BackendUtility TSconfig-related methods",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108810",
                "title": "BackendUtility localization-related methods",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108843",
                "title": "ExtensionManagementUtility::addFieldsToUserSettings",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108963",
                "title": "Deprecate PageRenderer->addInlineLanguageDomain()",
                "removal": "",
                "migration": "",
                "tags": [
                    "Backend",
                    "JavaScript",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109027",
                "title": "Move language:update command and events to EXT:core",
                "removal": "15",
                "migration": "",
                "tags": [
                    "CLI",
                    "PHP-API",
                    "FullyScanned",
                    "ext:install"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109230",
                "title": "FormResultCompiler",
                "removal": "15",
                "migration": "",
                "tags": [
                    "Backend",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109230-FormResultCompiler.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109230-FormResultCompiler.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109412",
                "title": "TypoScript-based form YAML registration",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "YAML",
                    "Frontend",
                    "Backend",
                    "FullyScanned",
                    "ext:form"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.html",
                "publishedIn": "manual"
            }
        ],
        "versions": [
            "14.3",
            "14.3.x",
            "14.2",
            "14.1",
            "14.0",
            "13.4",
            "13.4.x",
            "13.3",
            "13.2",
            "13.1",
            "13.0",
            "12.4",
            "12.4.x",
            "12.3",
            "12.2",
            "12.1",
            "12.0",
            "11.5",
            "11.5.x",
            "11.4",
            "11.3",
            "11.2",
            "11.1",
            "11.0",
            "10.4",
            "10.4.x",
            "10.3",
            "10.2",
            "10.1",
            "10.0",
            "9.5",
            "9.5.x",
            "9.4",
            "9.3",
            "9.2",
            "9.1",
            "9.0",
            "8.7",
            "8.7.x",
            "8.6",
            "8.5",
            "8.4",
            "8.3",
            "8.2",
            "8.1",
            "8.0",
            "7.6",
            "7.6.x",
            "7.5",
            "7.4",
            "7.3",
            "7.2",
            "7.1",
            "7.0"
        ],
        "answeredBy": "packages",
        "versionsFromTheManual": [
            "15.0",
            "14.3",
            "14.2",
            "14.1",
            "14.0",
            "13.4",
            "13.3",
            "13.2",
            "13.1",
            "13.0",
            "12.4",
            "12.4.x",
            "12.3",
            "12.2",
            "12.1",
            "12.0"
        ],
        "removalRule": "A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned."
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    140 of the 274 entries narrowed by version and type are tagged "FullyScanned" — showing the first 20:
    - 15.0 Deprecation: Experimental backend ViewHelpers (#110148) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110148-ExperimentalBackendViewHelpers.html — Fluid, FullyScanned, ext:fluid
    - 15.0 Deprecation: StringUtility::multibyteStringPad() method (#110202) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110202-StringUtilityMultibyteStringPad.html — PHP-API, FullyScanned, ext:core
    - 15.0 Deprecation: DataHandler->setCorrelationId() (#110285) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110285-DataHandlerSetCorrelationId.html — PHP-API, FullyScanned, ext:core
    - 15.0 Deprecation: AbstractXmlSitemapDataProvider (#110334) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110334-AbstractXmlSitemapDataProvider.html — PHP-API, FullyScanned, ext:seo
    - 15.0 Deprecation: LazyLoadingProxy and LazyObjectStorage (#110347) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110347-LazyLoadingProxyAndLazyObjectStorage.html — PHP-API, FullyScanned, ext:extbase
    - 15.0 Deprecation: CommandRegistry->getSchedulableCommands() (#110477) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110477-CommandRegistryGetSchedulableCommands.html — CLI, FullyScanned, PHP-API, ext:core
    - 14.3 Deprecation: Lowlevel DatabaseIntegrityCheck class (#107931) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-107931-LowlevelDatabaseIntegrityCheck.html — PHP-API, FullyScanned, ext:lowlevel
    - 14.3 Deprecation: BackendUtility item list label methods (#109519) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109519-BackendUtilityItemListLabelMethods.html — PHP-API, FullyScanned, ext:backend
    - 14.3 Deprecation: GeneralUtility::isOnCurrentHost() without PSR-7 request (#109523)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109523-GeneralUtilityIsOnCurrentHostWithoutRequest.html — PHP-API, FullyScanned, ext:core
    - 14.3 Deprecation: GeneralUtility::sanitizeLocalUrl() needs PSR-7 request (#109544)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109544-GeneralUtilitySanitizeLocalUrlWithoutRequest.html — PHP-API, FullyScanned, ext:core
    - 14.3 Deprecation: GeneralUtility::locationHeaderUrl() without PSR-7 request (#109548)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109548-GeneralUtilityLocationHeaderUrlWithoutRequest.html — PHP-API, FullyScanned, ext:core
    - 14.3 Deprecation: GeneralUtility::getIndpEnv() (#109551)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109551-GeneralUtilityGetIndpEnv.html — PHP-API, FullyScanned, ext:core
    - 14.2 Deprecation: BackendUserAuthentication::recordEditAccessInternals() and $errorMsg (#108568)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.html — PHP-API, FullyScanned, ext:core
    - 14.2 Deprecation: BackendUtility TSconfig-related methods (#108761) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.html — PHP-API, FullyScanned, ext:backend
    - 14.2 Deprecation: BackendUtility localization-related methods (#108810) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.html — PHP-API, FullyScanned, ext:backend
    - 14.2 Deprecation: ExtensionManagementUtility::addFieldsToUserSettings (#108843) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.html — PHP-API, FullyScanned, ext:core
    - 14.2 Deprecation: Deprecate PageRenderer->addInlineLanguageDomain() (#108963)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.html — Backend, JavaScript, FullyScanned, ext:backend
    - 14.2 Deprecation: Move language:update command and events to EXT:core (#109027) — removed in v15
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.html — CLI, PHP-API, FullyScanned, ext:install
    - 14.2 Deprecation: FormResultCompiler (#109230) — removed in v15
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109230-FormResultCompiler.html — Backend, FullyScanned, ext:backend
    - 14.2 Deprecation: TypoScript-based form YAML registration (#109412) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.html — YAML, Frontend, Backend, FullyScanned, ext:form

    Read the file for the description and the migration, or ask again for the one entry by its issue number, which carries its migration section whole. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
    An entry marked manual is what docs.typo3.org renders today, after every merge, and it links by URL. For a major that is not released yet it is still being written. An identifier search reaches only the entries this installation ships, whose text is on disk.
    A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned.

Data:

.. code-block:: json

    {
        "query": "",
        "matchCount": 140,
        "matchedIn": "name",
        "tags": [
            "Backend",
            "CLI",
            "Database",
            "FAL",
            "FileList",
            "FlexForm",
            "Fluid",
            "Frontend",
            "FullyScanned",
            "JavaScript",
            "LocalConfiguration",
            "NotScanned",
            "PHP-API",
            "PartiallyScanned",
            "RTE",
            "Scheduler",
            "TCA",
            "TSConfig",
            "TypoScript",
            "YAML",
            "ext:backend",
            "ext:core",
            "ext:dashboard",
            "ext:extbase",
            "ext:extensionmanager",
            "ext:felogin",
            "ext:filelist",
            "ext:fluid",
            "ext:form",
            "ext:frontend",
            "ext:impexp",
            "ext:indexed_search",
            "ext:install",
            "ext:lowlevel",
            "ext:redirects",
            "ext:rte_ckeditor",
            "ext:scheduler",
            "ext:seo",
            "ext:t3editor"
        ],
        "entries": [
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110148",
                "title": "Experimental backend ViewHelpers",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "Fluid",
                    "FullyScanned",
                    "ext:fluid"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110148-ExperimentalBackendViewHelpers.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110148-ExperimentalBackendViewHelpers.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110202",
                "title": "StringUtility::multibyteStringPad() method",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110202-StringUtilityMultibyteStringPad.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110202-StringUtilityMultibyteStringPad.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110285",
                "title": "DataHandler->setCorrelationId()",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110285-DataHandlerSetCorrelationId.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110285-DataHandlerSetCorrelationId.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110334",
                "title": "AbstractXmlSitemapDataProvider",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:seo"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110334-AbstractXmlSitemapDataProvider.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110334-AbstractXmlSitemapDataProvider.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110347",
                "title": "LazyLoadingProxy and LazyObjectStorage",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:extbase"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110347-LazyLoadingProxyAndLazyObjectStorage.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110347-LazyLoadingProxyAndLazyObjectStorage.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110477",
                "title": "CommandRegistry->getSchedulableCommands()",
                "removal": "16.0",
                "migration": "",
                "tags": [
                    "CLI",
                    "FullyScanned",
                    "PHP-API",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110477-CommandRegistryGetSchedulableCommands.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110477-CommandRegistryGetSchedulableCommands.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "107931",
                "title": "Lowlevel DatabaseIntegrityCheck class",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:lowlevel"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-107931-LowlevelDatabaseIntegrityCheck.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-107931-LowlevelDatabaseIntegrityCheck.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109519",
                "title": "BackendUtility item list label methods",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109519-BackendUtilityItemListLabelMethods.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109519-BackendUtilityItemListLabelMethods.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109523",
                "title": "GeneralUtility::isOnCurrentHost() without PSR-7 request",
                "removal": "",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109523-GeneralUtilityIsOnCurrentHostWithoutRequest.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109523-GeneralUtilityIsOnCurrentHostWithoutRequest.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109544",
                "title": "GeneralUtility::sanitizeLocalUrl() needs PSR-7 request",
                "removal": "",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109544-GeneralUtilitySanitizeLocalUrlWithoutRequest.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109544-GeneralUtilitySanitizeLocalUrlWithoutRequest.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109548",
                "title": "GeneralUtility::locationHeaderUrl() without PSR-7 request",
                "removal": "",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109548-GeneralUtilityLocationHeaderUrlWithoutRequest.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109548-GeneralUtilityLocationHeaderUrlWithoutRequest.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109551",
                "title": "GeneralUtility::getIndpEnv()",
                "removal": "",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109551-GeneralUtilityGetIndpEnv.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.3/Deprecation-109551-GeneralUtilityGetIndpEnv.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108568",
                "title": "BackendUserAuthentication::recordEditAccessInternals() and $errorMsg",
                "removal": "",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108761",
                "title": "BackendUtility TSconfig-related methods",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108810",
                "title": "BackendUtility localization-related methods",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108843",
                "title": "ExtensionManagementUtility::addFieldsToUserSettings",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108963",
                "title": "Deprecate PageRenderer->addInlineLanguageDomain()",
                "removal": "",
                "migration": "",
                "tags": [
                    "Backend",
                    "JavaScript",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109027",
                "title": "Move language:update command and events to EXT:core",
                "removal": "15",
                "migration": "",
                "tags": [
                    "CLI",
                    "PHP-API",
                    "FullyScanned",
                    "ext:install"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109230",
                "title": "FormResultCompiler",
                "removal": "15",
                "migration": "",
                "tags": [
                    "Backend",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109230-FormResultCompiler.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109230-FormResultCompiler.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109412",
                "title": "TypoScript-based form YAML registration",
                "removal": "15.0",
                "migration": "",
                "tags": [
                    "YAML",
                    "Frontend",
                    "Backend",
                    "FullyScanned",
                    "ext:form"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.html",
                "url": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.html",
                "publishedIn": "manual"
            }
        ],
        "versions": [
            "14.3"
        ],
        "answeredBy": "packages",
        "versionsFromTheManual": [
            "15.0",
            "14.3",
            "14.2",
            "14.1",
            "14.0",
            "13.4",
            "13.3",
            "13.2",
            "13.1",
            "13.0",
            "12.4",
            "12.4.x",
            "12.3",
            "12.2",
            "12.1",
            "12.0"
        ],
        "removalRule": "A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned."
    }

changelog: miss
~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "quantumflux"
    }

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    No changelog entry in this installation carries all of "quantumflux".
    A changelog records change events, so a miss can mean the question belongs to another corpus. Whether a mechanism nobody changed still holds is typo3_documentation_lookup with targetVersion; whether a core patch of your own owes an entry is typo3_rule_lookup with documentId "core/contribution/changelog".
    15.0, 14.3, 14.3.x, 14.2, 14.1, 14.0, 13.4, 13.4.x, 13.3, 13.2, 13.1, 13.0, 12.4, 12.4.x, 12.3, 12.2, 12.1, 12.0 are read from docs.typo3.org — what it renders today, after every merge, which for a major that is not released yet is still being written. This installation ships 14.3, 14.3.x, 14.2, 14.1, 14.0, 13.4, 13.4.x, 13.3 and older.

Data:

.. code-block:: json

    {
        "query": "quantumflux",
        "matchCount": 0,
        "tags": [],
        "entries": [],
        "versions": [
            "14.3",
            "14.3.x",
            "14.2",
            "14.1",
            "14.0",
            "13.4",
            "13.4.x",
            "13.3",
            "13.2",
            "13.1",
            "13.0",
            "12.4",
            "12.4.x",
            "12.3",
            "12.2",
            "12.1",
            "12.0",
            "11.5",
            "11.5.x",
            "11.4",
            "11.3",
            "11.2",
            "11.1",
            "11.0",
            "10.4",
            "10.4.x",
            "10.3",
            "10.2",
            "10.1",
            "10.0",
            "9.5",
            "9.5.x",
            "9.4",
            "9.3",
            "9.2",
            "9.1",
            "9.0",
            "8.7",
            "8.7.x",
            "8.6",
            "8.5",
            "8.4",
            "8.3",
            "8.2",
            "8.1",
            "8.0",
            "7.6",
            "7.6.x",
            "7.5",
            "7.4",
            "7.3",
            "7.2",
            "7.1",
            "7.0"
        ],
        "answeredBy": "packages",
        "versionsFromTheManual": [
            "15.0",
            "14.3",
            "14.3.x",
            "14.2",
            "14.1",
            "14.0",
            "13.4",
            "13.4.x",
            "13.3",
            "13.2",
            "13.1",
            "13.0",
            "12.4",
            "12.4.x",
            "12.3",
            "12.2",
            "12.1",
            "12.0"
        ],
        "termCounts": [
            {
                "term": "quantumflux",
                "matchCount": 0
            }
        ]
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    No changelog entry in this installation carries all of "quantumflux".
    A changelog records change events, so a miss can mean the question belongs to another corpus. Whether a mechanism nobody changed still holds is typo3_documentation_lookup with targetVersion; whether a core patch of your own owes an entry is typo3_rule_lookup with documentId "core/contribution/changelog".
    15.0, 14.3, 14.3.x, 14.2, 14.1, 14.0, 13.4, 13.4.x, 13.3, 13.2, 13.1, 13.0, 12.4, 12.4.x, 12.3, 12.2, 12.1, 12.0 are read from docs.typo3.org — what it renders today, after every merge, which for a major that is not released yet is still being written. This installation ships 14.3 and older.

Data:

.. code-block:: json

    {
        "query": "quantumflux",
        "matchCount": 0,
        "tags": [],
        "entries": [],
        "versions": [
            "14.3"
        ],
        "answeredBy": "packages",
        "versionsFromTheManual": [
            "15.0",
            "14.3",
            "14.3.x",
            "14.2",
            "14.1",
            "14.0",
            "13.4",
            "13.4.x",
            "13.3",
            "13.2",
            "13.1",
            "13.0",
            "12.4",
            "12.4.x",
            "12.3",
            "12.2",
            "12.1",
            "12.0"
        ],
        "termCounts": [
            {
                "term": "quantumflux",
                "matchCount": 0
            }
        ]
    }
