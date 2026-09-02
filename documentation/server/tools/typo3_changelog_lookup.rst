.. _typo3_changelog_lookup:

``typo3_changelog_lookup``
==========================

Search the TYPO3 changelog: one entry per breaking change, deprecation, feature
and important note, in the version it was released in. This reads the entries;
writing one for a core patch of your own is the other direction and is
typo3_rule_lookup with documentId "core/contribution/changelog". Answers "what
did this version deprecate", "what changed about X", "which release introduced
Y". This is the first stop when building on a major you have not built on
recently: what separates a current answer from a two-major-old one is written
down here and almost nowhere else. A deprecation carries the version it stops
working in where the entry states one, and the rule that answers the rest beside
it. The versions the installation ships are read from the core package on disk;
the ones above its own major are read from docs.typo3.org, which is what an
upgrade to a version you have not installed is asking for. Every word of the
query has to be carried by an entry; narrow further with type and version. A
version and a type with the query omitted list whole under a raised limit, which
is the deprecation sweep of one major in a single call. A method or class you
found in the code is a query of its own: an identifier reaches the entries
naming it, whether or not the change was titled after it — inside the installed
versions, which are the ones whose text is on disk. Answers from: packages,
network.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`packages <answer-sources-packages>`,
:ref:`network <answer-sources-network>`.

Takes
-----

.. code-block:: yaml

    # Words the entry has to carry, matched against its file name and the words that
    # name spells. Where no entry carries all of them by name, the title stated
    # inside the file is searched as well, which reaches a method name the file name
    # leaves out; and a class, method or constant name reaches the entries that
    # write it in their text, so a removed API can be asked for by the identifier
    # you have, in any spelling of it: bare, qualified by its class, or fully
    # qualified. The issue number is among the words a file name carries, so a
    # deprecation's own number reaches every entry filed under it — the Feature
    # the replacement was announced in, with the version it was released in. When
    # nothing carries all of them there either, the answer names the largest part of
    # the query that does reach entries, which is what to ask again with. Omit to
    # list a version or a type as a whole.
    query: string  # optional
    # One of: breaking, deprecation, feature, important. Restrict to one kind of
    # change. Breaking and deprecation are what affects existing code.
    type: string  # optional
    # Restrict to a version, by prefix: "14" covers 14.0 through 14.3.x, "13.4"
    # covers 13.4 and 13.4.x.
    version: string  # optional
    # Restrict to entries carrying this index tag: "ext:form" for the system
    # extension a change is in, "FullyScanned" or "NotScanned" for what the
    # Extension Scanner has a matcher for, "PHP-API", "TCA", "Backend", "Frontend"
    # for the surface. This bounds one question inside a version and a type. The
    # sweep of a major does not need it: that version and type come back whole under
    # a raised limit, and every entry carries its own tags to be read by. The
    # changelog says nothing about which third-party extension a change affects, so
    # an extension key of your own matches no tag.
    tag: string  # optional
    # Maximum number of entries. Raise it to list a version and a type whole: the
    # largest covered major holds 128 deprecations, and that sweep is one call
    # rather than one per tag.
    limit: integer  # optional

Answers with
------------

.. code-block:: yaml

    query: string
    # Entries carrying every word of the query and the tag, before the limit.
    matchCount: integer  # optional
    # Where the query was carried: "name" for the entry names, "body" where no name
    # carried it and the inside of the file did — the title as it is stated, or an
    # identifier the text writes. A body match can name the identifier without being
    # about it, so read the title of each. Returned where the answer carries
    # entries.
    matchedIn: string  # optional
    # Every index tag the entries of this version and type carry, with the ones
    # already filtered by among them. Returned where a tag was asked for, so a tag
    # that matched nothing can be replaced by one that exists.
    tags: [string]  # optional
    entries:  # optional
      - # One of: Breaking, Deprecation, Feature, Important.
        type: string
        # The version directory it was released in.
        version: string
        # Forge issue number.
        issue: string
        title: string
        # The version a Deprecation states the deprecated thing stops working in —
        # what an upgrade decides on. Empty on the other three types, and on a
        # deprecation whose entry states none, which is most of a major and is not
        # "no removal planned": removalRule is what answers it there.
        removal: string
        # Index tags. FullyScanned or PartiallyScanned means the extension scanner
        # has a matcher for it.
        tags: [string]
        # Where to read the description and the migration: an EXT: reference where
        # the installation ships the entry, and a docs.typo3.org URL where it does
        # not.
        file: string
        # Which side the entry came from. "installation" is the core package on
        # disk, which is the version that installation runs. "manual" is
        # docs.typo3.org, which is every version above the installed major — what
        # an upgrade reads, and a moving target for a major that is not released
        # yet.
        publishedIn: string
    # What each word of the query reaches on its own, inside the version and the
    # type that were asked for. A word at 0 is the one that emptied the answer —
    # it is misspelled, or nothing here is named after it. Returned on a miss that
    # carried words. These are counts and not a query: termSubsets is what can be
    # asked outright.
    termCounts:  # optional
      - # The word, lowercased as it was searched for.
        term: string
        matchCount: integer
    # The same words counted over the whole changelog rather than inside the version
    # and the type. Returned only where a word reaches there and nothing inside the
    # narrowing, which makes the filter what emptied this answer rather than the
    # words: ask again without it.
    termCountsWithoutTheNarrowing:  # optional
      - # The word, lowercased as it was searched for.
        term: string
        matchCount: integer
    # The largest parts of the query that do reach entries, narrowest first —
    # every one of them, because the one a tie-break puts first is not always the
    # one being looked for. Withheld where a tag was asked for: these are counted
    # off the entry names and a tag is read inside the file, so a subset offered
    # there would promise entries the same call does not return.
    termSubsets:  # optional
      - # Words of the query, as a query to ask again with.
        terms: [string]
        # Entries carrying every word of this subset, inside the same version and
        # type.
        matchCount: integer
    # When a deprecation stops working where the entry itself does not say. Returned
    # where the answer carries a deprecation.
    removalRule: string  # optional
    # The versions this installation ships changelog entries for, newest first.
    versions: [string]  # optional
    # The versions above those, read from docs.typo3.org, newest first. Absent where
    # the host did not answer, which is the one case a version is missing from this
    # answer rather than from the changelog.
    versionsFromTheManual: [string]  # optional
    # One of: packages. packages: read from the files the installed packages ship,
    # because the console could not be asked — overrides applied at runtime are
    # not reflected.
    answeredBy: string  # optional
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

The answer carries exactly one of these sets of fields: ``query``,
``matchCount``, ``entries``, ``versions``, ``answeredBy`` — or ``query``,
``unsupported``.

Answered
--------

Recorded on 2026-08-26 by ``bin/cli tools:record``. Of two working directories,
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

changelog: hit
~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "ext_tables.php"
    }

From the main core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    1 changelog entry carrying "ext_tables.php":
    - 14.3 Deprecation: ext_tables.php in extensions (#109438) — removed in v15.0
      EXT:core/Documentation/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.rst — PHP-API, NotScanned, ext:core

    Read the file for the description and the migration. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
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
                "tags": [
                    "PHP-API",
                    "NotScanned",
                    "ext:core"
                ],
                "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109438-ExtTablesPhpInExtensions.rst",
                "publishedIn": "installation"
            }
        ],
        "versions": [
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
        "versionsFromTheManual": [],
        "removalRule": "A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned."
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    1 changelog entry carrying "ext_tables.php":
    Every one of these is in ext:acme_events, which the query did not name. A changelog records change events, so an area nobody has reworked has no entry at all — an answer that comes from one system extension is usually the place that happens to spell the word rather than the subject. Ask again in the words the changelog writes that subject in, which are not always the ones the code uses.
    - 14.3 Deprecation: ext_tables.php in the fixture extension (#900001) — removed in v15.0
      EXT:core/Documentation/Changelog/14.3/Deprecation-900001-ExtTablesPhpInTheFixtureExtension.rst — PHP-API, FullyScanned, ext:acme_events

    Read the file for the description and the migration. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
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
                "issue": "900001",
                "title": "ext_tables.php in the fixture extension",
                "removal": "15.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:acme_events"
                ],
                "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-900001-ExtTablesPhpInTheFixtureExtension.rst",
                "publishedIn": "installation"
            }
        ],
        "versions": [
            "14.3"
        ],
        "answeredBy": "packages",
        "versionsFromTheManual": [
            "15.0",
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

From the main core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    388 of the 969 entries narrowed by version and type are tagged "FullyScanned" — showing the first 20:
    - 15.0 Deprecation: Experimental backend ViewHelpers (#110148) — removed in v16.0
      EXT:core/Documentation/Changelog/15.0/Deprecation-110148-ExperimentalBackendViewHelpers.rst — Fluid, FullyScanned, ext:fluid
    - 15.0 Deprecation: StringUtility::multibyteStringPad() method (#110202) — removed in v16.0
      EXT:core/Documentation/Changelog/15.0/Deprecation-110202-StringUtilityMultibyteStringPad.rst — PHP-API, FullyScanned, ext:core
    - 15.0 Deprecation: DataHandler->setCorrelationId() (#110285) — removed in v16.0
      EXT:core/Documentation/Changelog/15.0/Deprecation-110285-DataHandlerSetCorrelationId.rst — PHP-API, FullyScanned, ext:core
    - 15.0 Deprecation: AbstractXmlSitemapDataProvider (#110334) — removed in v16.0
      EXT:core/Documentation/Changelog/15.0/Deprecation-110334-AbstractXmlSitemapDataProvider.rst — PHP-API, FullyScanned, ext:seo
    - 14.3 Deprecation: Lowlevel DatabaseIntegrityCheck class (#107931) — removed in v15.0
      EXT:core/Documentation/Changelog/14.3/Deprecation-107931-LowlevelDatabaseIntegrityCheck.rst — PHP-API, FullyScanned, ext:lowlevel
    - 14.3 Deprecation: BackendUtility item list label methods (#109519) — removed in v15.0
      EXT:core/Documentation/Changelog/14.3/Deprecation-109519-BackendUtilityItemListLabelMethods.rst — PHP-API, FullyScanned, ext:backend
    - 14.3 Deprecation: GeneralUtility::isOnCurrentHost() without PSR-7 request (#109523)
      EXT:core/Documentation/Changelog/14.3/Deprecation-109523-GeneralUtilityIsOnCurrentHostWithoutRequest.rst — PHP-API, FullyScanned, ext:core
    - 14.3 Deprecation: GeneralUtility::sanitizeLocalUrl() needs PSR-7 request (#109544)
      EXT:core/Documentation/Changelog/14.3/Deprecation-109544-GeneralUtilitySanitizeLocalUrlWithoutRequest.rst — PHP-API, FullyScanned, ext:core
    - 14.3 Deprecation: GeneralUtility::locationHeaderUrl() without PSR-7 request (#109548)
      EXT:core/Documentation/Changelog/14.3/Deprecation-109548-GeneralUtilityLocationHeaderUrlWithoutRequest.rst — PHP-API, FullyScanned, ext:core
    - 14.3 Deprecation: GeneralUtility::getIndpEnv() (#109551)
      EXT:core/Documentation/Changelog/14.3/Deprecation-109551-GeneralUtilityGetIndpEnv.rst — PHP-API, FullyScanned, ext:core
    - 14.2 Deprecation: BackendUserAuthentication::recordEditAccessInternals() and $errorMsg (#108568)
      EXT:core/Documentation/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.rst — PHP-API, FullyScanned, ext:core
    - 14.2 Deprecation: BackendUtility TSconfig-related methods (#108761) — removed in v15.0
      EXT:core/Documentation/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.rst — PHP-API, FullyScanned, ext:backend
    - 14.2 Deprecation: BackendUtility localization-related methods (#108810) — removed in v15.0
      EXT:core/Documentation/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.rst — PHP-API, FullyScanned, ext:backend
    - 14.2 Deprecation: ExtensionManagementUtility::addFieldsToUserSettings (#108843) — removed in v15.0
      EXT:core/Documentation/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.rst — PHP-API, FullyScanned, ext:core
    - 14.2 Deprecation: Deprecate `PageRenderer->addInlineLanguageDomain()` (#108963)
      EXT:core/Documentation/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.rst — Backend, JavaScript, FullyScanned, ext:backend
    - 14.2 Deprecation: Move `language:update` command and events to `EXT:core` (#109027) — removed in v15
      EXT:core/Documentation/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.rst — CLI, PHP-API, FullyScanned, ext:install
    - 14.2 Deprecation: FormResultCompiler (#109230) — removed in v15
      EXT:core/Documentation/Changelog/14.2/Deprecation-109230-FormResultCompiler.rst — Backend, FullyScanned, ext:backend
    - 14.2 Deprecation: TypoScript-based form YAML registration (#109412) — removed in v15.0
      EXT:core/Documentation/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.rst — YAML, Frontend, Backend, FullyScanned, ext:form
    - 14.1 Deprecation: Fluid namespaces in TYPO3_CONF_VARS (#108524)
      EXT:core/Documentation/Changelog/14.1/Deprecation-108524-FluidNamespacesInTYPO3_CONF_VARS.rst — Fluid, LocalConfiguration, FullyScanned, ext:fluid
    - 14.1 Deprecation: Deprecate CommandNameAlreadyInUseException (#108667)
      EXT:core/Documentation/Changelog/14.1/Deprecation-108667-DeprecateCommandNameAlreadyInUseException.rst — PHP-API, FullyScanned, ext:core

    Read the file for the description and the migration. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
    A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned.

Data:

.. code-block:: json

    {
        "query": "",
        "matchCount": 388,
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
                "tags": [
                    "Fluid",
                    "FullyScanned",
                    "ext:fluid"
                ],
                "file": "EXT:core/Documentation/Changelog/15.0/Deprecation-110148-ExperimentalBackendViewHelpers.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110202",
                "title": "StringUtility::multibyteStringPad() method",
                "removal": "16.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "EXT:core/Documentation/Changelog/15.0/Deprecation-110202-StringUtilityMultibyteStringPad.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110285",
                "title": "DataHandler->setCorrelationId()",
                "removal": "16.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "EXT:core/Documentation/Changelog/15.0/Deprecation-110285-DataHandlerSetCorrelationId.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110334",
                "title": "AbstractXmlSitemapDataProvider",
                "removal": "16.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:seo"
                ],
                "file": "EXT:core/Documentation/Changelog/15.0/Deprecation-110334-AbstractXmlSitemapDataProvider.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "107931",
                "title": "Lowlevel DatabaseIntegrityCheck class",
                "removal": "15.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:lowlevel"
                ],
                "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-107931-LowlevelDatabaseIntegrityCheck.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109519",
                "title": "BackendUtility item list label methods",
                "removal": "15.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109519-BackendUtilityItemListLabelMethods.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109523",
                "title": "GeneralUtility::isOnCurrentHost() without PSR-7 request",
                "removal": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109523-GeneralUtilityIsOnCurrentHostWithoutRequest.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109544",
                "title": "GeneralUtility::sanitizeLocalUrl() needs PSR-7 request",
                "removal": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109544-GeneralUtilitySanitizeLocalUrlWithoutRequest.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109548",
                "title": "GeneralUtility::locationHeaderUrl() without PSR-7 request",
                "removal": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109548-GeneralUtilityLocationHeaderUrlWithoutRequest.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "109551",
                "title": "GeneralUtility::getIndpEnv()",
                "removal": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-109551-GeneralUtilityGetIndpEnv.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108568",
                "title": "BackendUserAuthentication::recordEditAccessInternals() and $errorMsg",
                "removal": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108761",
                "title": "BackendUtility TSconfig-related methods",
                "removal": "15.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108810",
                "title": "BackendUtility localization-related methods",
                "removal": "15.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108843",
                "title": "ExtensionManagementUtility::addFieldsToUserSettings",
                "removal": "15.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108963",
                "title": "Deprecate `PageRenderer->addInlineLanguageDomain()`",
                "removal": "",
                "tags": [
                    "Backend",
                    "JavaScript",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109027",
                "title": "Move `language:update` command and events to `EXT:core`",
                "removal": "15",
                "tags": [
                    "CLI",
                    "PHP-API",
                    "FullyScanned",
                    "ext:install"
                ],
                "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109230",
                "title": "FormResultCompiler",
                "removal": "15",
                "tags": [
                    "Backend",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-109230-FormResultCompiler.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109412",
                "title": "TypoScript-based form YAML registration",
                "removal": "15.0",
                "tags": [
                    "YAML",
                    "Frontend",
                    "Backend",
                    "FullyScanned",
                    "ext:form"
                ],
                "file": "EXT:core/Documentation/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.1",
                "issue": "108524",
                "title": "Fluid namespaces in TYPO3_CONF_VARS",
                "removal": "",
                "tags": [
                    "Fluid",
                    "LocalConfiguration",
                    "FullyScanned",
                    "ext:fluid"
                ],
                "file": "EXT:core/Documentation/Changelog/14.1/Deprecation-108524-FluidNamespacesInTYPO3_CONF_VARS.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.1",
                "issue": "108667",
                "title": "Deprecate CommandNameAlreadyInUseException",
                "removal": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "EXT:core/Documentation/Changelog/14.1/Deprecation-108667-DeprecateCommandNameAlreadyInUseException.rst",
                "publishedIn": "installation"
            }
        ],
        "versions": [
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
        "versionsFromTheManual": [],
        "removalRule": "A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned."
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    383 of the 960 entries narrowed by version and type are tagged "FullyScanned" — showing the first 20:
    - 15.0 Deprecation: Experimental backend ViewHelpers (#110148) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110148-ExperimentalBackendViewHelpers.html — Fluid, FullyScanned, ext:fluid
    - 15.0 Deprecation: StringUtility::multibyteStringPad() method (#110202) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110202-StringUtilityMultibyteStringPad.html — PHP-API, FullyScanned, ext:core
    - 15.0 Deprecation: DataHandler->setCorrelationId() (#110285) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110285-DataHandlerSetCorrelationId.html — PHP-API, FullyScanned, ext:core
    - 15.0 Deprecation: AbstractXmlSitemapDataProvider (#110334) — removed in v16.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110334-AbstractXmlSitemapDataProvider.html — PHP-API, FullyScanned, ext:seo
    - 14.3 Deprecation: ext_tables.php in the fixture extension (#900001) — removed in v15.0
      EXT:core/Documentation/Changelog/14.3/Deprecation-900001-ExtTablesPhpInTheFixtureExtension.rst — PHP-API, FullyScanned, ext:acme_events
    - 14.2 Deprecation: BackendUserAuthentication::recordEditAccessInternals() and $errorMsg (#108568)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.html — PHP-API, FullyScanned, ext:core
    - 14.2 Deprecation: BackendUtility TSconfig-related methods (#108761) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.html — PHP-API, FullyScanned, ext:backend
    - 14.2 Deprecation: BackendUtility localization-related methods (#108810) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.html — PHP-API, FullyScanned, ext:backend
    - 14.2 Deprecation: ExtensionManagementUtility::addFieldsToUserSettings (#108843) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.html — PHP-API, FullyScanned, ext:core
    - 14.2 Deprecation: Deprecate `PageRenderer->addInlineLanguageDomain()` (#108963)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.html — Backend, JavaScript, FullyScanned, ext:backend
    - 14.2 Deprecation: Move `language:update` command and events to `EXT:core` (#109027) — removed in v15
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.html — CLI, PHP-API, FullyScanned, ext:install
    - 14.2 Deprecation: FormResultCompiler (#109230) — removed in v15
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109230-FormResultCompiler.html — Backend, FullyScanned, ext:backend
    - 14.2 Deprecation: TypoScript-based form YAML registration (#109412) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.html — YAML, Frontend, Backend, FullyScanned, ext:form
    - 14.1 Deprecation: Fluid namespaces in TYPO3_CONF_VARS (#108524)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.1/Deprecation-108524-FluidNamespacesInTYPO3_CONF_VARS.html — Fluid, LocalConfiguration, FullyScanned, ext:fluid
    - 14.1 Deprecation: Deprecate CommandNameAlreadyInUseException (#108667)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.1/Deprecation-108667-DeprecateCommandNameAlreadyInUseException.html — PHP-API, FullyScanned, ext:core
    - 14.0 Deprecation: Various methods in BackendUtility (#106393)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Deprecation-106393-VariousMethodsInBackendUtility.html — TCA, FullyScanned, ext:core
    - 14.0 Deprecation: GeneralUtility::resolveBackPath (#106618) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Deprecation-106618-GeneralUtilityresolveBackPath.html — Backend, Frontend, JavaScript, TypoScript, FullyScanned, ext:core
    - 14.0 Deprecation: Move upgrade wizard related interfaces and attribute to `EXT:core` (#106947)
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Deprecation-106947-MoveUpgradeWizardRelatedInterfacesAndAttributeToEXTcore.html — PHP-API, FullyScanned, ext:install
    - 14.0 Deprecation: ExtensionManagementUtility::addPiFlexFormValue() (#107047) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Deprecation-107047-ExtensionManagementUtilityAddPiFlexFormValue.html — Backend, FlexForm, TCA, FullyScanned, ext:core
    - 14.0 Deprecation: Deprecate :php:`Annotation` namespace of Extbase attributes (#107229) — removed in v15.0
      https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Deprecation-107229-DeprecatePhpAnnotationNamespaceOfExtbaseAttributes.html — PHP-API, FullyScanned, ext:extbase

    Read the file for the description and the migration. A Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool can find the call sites for you.
    Entries above 14.3 come from docs.typo3.org rather than from this installation: they are what the host publishes today, they are linked by URL instead of by EXT: path, and for a major that is not released yet they are still being written. An identifier search does not reach them — their text is not on disk, so they are searched by name and by the title the manual states.
    A deprecated API keeps working until the next major release. An entry that states a removal version overrides that, and some state one more than a major away. An empty removal is what the entry states, not a promise that no removal is planned.

Data:

.. code-block:: json

    {
        "query": "",
        "matchCount": 383,
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
            "ext:acme_events",
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
                "tags": [
                    "Fluid",
                    "FullyScanned",
                    "ext:fluid"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110148-ExperimentalBackendViewHelpers.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110202",
                "title": "StringUtility::multibyteStringPad() method",
                "removal": "16.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110202-StringUtilityMultibyteStringPad.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110285",
                "title": "DataHandler->setCorrelationId()",
                "removal": "16.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110285-DataHandlerSetCorrelationId.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "15.0",
                "issue": "110334",
                "title": "AbstractXmlSitemapDataProvider",
                "removal": "16.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:seo"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-110334-AbstractXmlSitemapDataProvider.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.3",
                "issue": "900001",
                "title": "ext_tables.php in the fixture extension",
                "removal": "15.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:acme_events"
                ],
                "file": "EXT:core/Documentation/Changelog/14.3/Deprecation-900001-ExtTablesPhpInTheFixtureExtension.rst",
                "publishedIn": "installation"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108568",
                "title": "BackendUserAuthentication::recordEditAccessInternals() and $errorMsg",
                "removal": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108568-BackendUserAuthenticationRecordEditAccessInternals.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108761",
                "title": "BackendUtility TSconfig-related methods",
                "removal": "15.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108761-BackendUtilityTSconfigMethods.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108810",
                "title": "BackendUtility localization-related methods",
                "removal": "15.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108810-BackendUtilityLocalizationMethods.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108843",
                "title": "ExtensionManagementUtility::addFieldsToUserSettings",
                "removal": "15.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108843-ExtensionManagementUtilityAddFieldsToUserSettings.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "108963",
                "title": "Deprecate `PageRenderer->addInlineLanguageDomain()`",
                "removal": "",
                "tags": [
                    "Backend",
                    "JavaScript",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-108963-DeprecatePageRenderer-addInlineLanguageDomain.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109027",
                "title": "Move `language:update` command and events to `EXT:core`",
                "removal": "15",
                "tags": [
                    "CLI",
                    "PHP-API",
                    "FullyScanned",
                    "ext:install"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109027-MoveLanguageUpdateCommandAndEventsToEXTcore.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109230",
                "title": "FormResultCompiler",
                "removal": "15",
                "tags": [
                    "Backend",
                    "FullyScanned",
                    "ext:backend"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109230-FormResultCompiler.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.2",
                "issue": "109412",
                "title": "TypoScript-based form YAML registration",
                "removal": "15.0",
                "tags": [
                    "YAML",
                    "Frontend",
                    "Backend",
                    "FullyScanned",
                    "ext:form"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.1",
                "issue": "108524",
                "title": "Fluid namespaces in TYPO3_CONF_VARS",
                "removal": "",
                "tags": [
                    "Fluid",
                    "LocalConfiguration",
                    "FullyScanned",
                    "ext:fluid"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.1/Deprecation-108524-FluidNamespacesInTYPO3_CONF_VARS.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.1",
                "issue": "108667",
                "title": "Deprecate CommandNameAlreadyInUseException",
                "removal": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.1/Deprecation-108667-DeprecateCommandNameAlreadyInUseException.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.0",
                "issue": "106393",
                "title": "Various methods in BackendUtility",
                "removal": "",
                "tags": [
                    "TCA",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Deprecation-106393-VariousMethodsInBackendUtility.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.0",
                "issue": "106618",
                "title": "GeneralUtility::resolveBackPath",
                "removal": "15.0",
                "tags": [
                    "Backend",
                    "Frontend",
                    "JavaScript",
                    "TypoScript",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Deprecation-106618-GeneralUtilityresolveBackPath.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.0",
                "issue": "106947",
                "title": "Move upgrade wizard related interfaces and attribute to `EXT:core`",
                "removal": "",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:install"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Deprecation-106947-MoveUpgradeWizardRelatedInterfacesAndAttributeToEXTcore.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.0",
                "issue": "107047",
                "title": "ExtensionManagementUtility::addPiFlexFormValue()",
                "removal": "15.0",
                "tags": [
                    "Backend",
                    "FlexForm",
                    "TCA",
                    "FullyScanned",
                    "ext:core"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Deprecation-107047-ExtensionManagementUtilityAddPiFlexFormValue.html",
                "publishedIn": "manual"
            },
            {
                "type": "Deprecation",
                "version": "14.0",
                "issue": "107229",
                "title": "Deprecate :php:`Annotation` namespace of Extbase attributes",
                "removal": "15.0",
                "tags": [
                    "PHP-API",
                    "FullyScanned",
                    "ext:extbase"
                ],
                "file": "https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Deprecation-107229-DeprecatePhpAnnotationNamespaceOfExtbaseAttributes.html",
                "publishedIn": "manual"
            }
        ],
        "versions": [
            "14.3"
        ],
        "answeredBy": "packages",
        "versionsFromTheManual": [
            "15.0",
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
            "12.0",
            "11.5",
            "11.5.x",
            "11.4",
            "11.3",
            "11.2",
            "11.1",
            "11.0",
            "10.4",
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
            "7.5",
            "7.4",
            "7.3",
            "7.2",
            "7.1",
            "7.0"
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

From the main core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    No changelog entry in this installation carries all of "quantumflux".
    A changelog records change events, so a miss can mean the question belongs to another corpus. Whether a mechanism nobody changed still holds is typo3_documentation_lookup with targetVersion; whether a core patch of your own owes an entry is typo3_rule_lookup with documentId "core/contribution/changelog".
    This installation ships 15.0, 14.3, 14.3.x, 14.2, 14.1, 14.0, 13.4, 13.4.x and older. Nothing above that is published yet.

Data:

.. code-block:: json

    {
        "query": "quantumflux",
        "matchCount": 0,
        "tags": [],
        "entries": [],
        "versions": [
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
        "versionsFromTheManual": [],
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
    This installation ships 14.3 and older. Above that, 15.0, 14.3.x, 14.2, 14.1, 14.0, 13.4, 13.4.x, 13.3, 13.2, 13.1, 13.0, 12.4, 12.4.x, 12.3, 12.2, 12.1, 12.0, 11.5, 11.5.x, 11.4, 11.3, 11.2, 11.1, 11.0, 10.4, 10.4.x, 10.3, 10.2, 10.1, 10.0, 9.5, 9.5.x, 9.4, 9.3, 9.2, 9.1, 9.0, 8.7, 8.7.x, 8.6, 8.5, 8.4, 8.3, 8.2, 8.1, 8.0, 7.6, 7.6.x, 7.5, 7.4, 7.3, 7.2, 7.1, 7.0 are read from docs.typo3.org — what the host publishes today, which for a major that is not released yet is still being written.

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
        "termCounts": [
            {
                "term": "quantumflux",
                "matchCount": 0
            }
        ]
    }
