.. _typo3_ter_lookup:

``typo3_ter_lookup``
====================

*Find extensions and their published versions*

Read what the TYPO3 Extension Repository has published under an extension key,
live from extensions.typo3.org, or find the published extensions for a subject.
Pass query with the subject, for example "news", "calendar" or "cookie consent",
before you build a feature yourself. It searches extensions.typo3.org and the
TYPO3 extension packages on packagist.org, because an extension can stand on
Packagist alone. Each match names its Composer package, its extension key, its
description, its downloads, the registries that found it, and from the TER the
current version and the TYPO3 majors it declares. Pass extension with the key,
the one extra.typo3/cms.extension-key declares and not the Composer package
name. Every published version comes back, highest number first. That is the
number, the state, the upload day, the TYPO3 majors it declares and the
constraints.depends.typo3 of its release. This is the question a release audit
cannot answer from the repository under audit. Tailor refuses to package unless
ext_emconf.php names the version of the release. So that file still names it
after the upload, and a published checkout reads exactly like one nobody has
published. Pass extensionVersion as well to learn whether the registry already
holds that number. It reports what the registry holds and judges no version
free; the comparison against the working tree is yours. A key with nothing
published under it gets that answer, which does not say that no such package
exists. An extension that ships through Composer alone never registers here.
What a release requires of the extension itself is typo3_hint_lookup with
id="extension-ter-release". This tool reads only, with no credential. You
register a key, upload a version and transfer an extension yourself, through
Tailor and the token it carries. Answers from: network.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`network <answer-sources-network>`.

Takes
-----

.. code-block:: yaml

    # Instead of query. The extension key, for example "news" or
    # "bootstrap_package". That is the key extra.typo3/cms.extension-key declares in
    # the package's composer.json, which every TYPO3 extension package has to carry
    # to install at all. It is not the Composer package name, which the registry
    # does not take. "georgringer/news" and "bootstrap-package" are the two shapes
    # that reach it as a name of the wrong kind. Lowercase letters, digits and
    # underscores, three to thirty characters.
    extension: string  # optional
    # Instead of extension. The subject a feature is about, in English, for example
    # "news", "event calendar" or "cookie consent". A Composer package name works
    # too. Both registries match it against their own index, and the answer lists
    # what either of them found.
    query: string  # optional
    # Beside extension only. One version number to ask about, for example "14.0.1",
    # which is usually the one ext_emconf.php names. The answer says whether the
    # registry holds it and, where it does, what that release declared. The
    # comparison reads it as the registry writes it, which is exactly three numbers.
    # A suffix of any kind belongs to no published version, because the upload route
    # accepts none.
    extensionVersion: string  # optional
    # How many versions come back, newest number first, or how many matches a search
    # returns. The count of everything published or found comes with them, so a cut
    # list says so. A widely maintained extension has a hundred versions, and what a
    # release audit reads is the top of them.
    limit: integer  # optional

The call carries exactly one of these sets of arguments:

- ``extension``
- ``query``

Answers with
------------

.. code-block:: yaml

    # One of: answered, empty, unavailable.
    status: string
    # The registry the answer came from, or the two a search read.
    source: string
    # What the call read, so you can ask the same question again by hand. Empty
    # where the key got its answer without a read, and empty in a search, whose
    # reads searches names.
    url: string
    # Where a person reads the extension's own page in the registry. Empty where the
    # key is not one the registry takes, and empty in a search.
    page: string
    # The key the call named, lowercased, as the call sent it. Empty in a search.
    extension: string
    # The subject the call searched for, as the call passed it. Empty where it named
    # a key.
    query: string
    # The version number the call asked about, as the call passed it. Empty where it
    # passed none.
    extensionVersion: string
    # Whether the registry has published that exact number. Null where the call
    # named no version, and null where the call read nothing at all. A false here is
    # the registry's answer, never a question that failed. It is a fact about the
    # registry and not a judgement that the number is free to release.
    held: boolean or null
    # How many versions the registry holds under the key in total, of which versions
    # carries at most limit. Zero where it holds none, and zero in a search, whose
    # counts searches carries.
    total: integer
    # The published versions, highest number first. That is version order and not
    # upload order. A maintenance release on an older line sits further down and may
    # be the most recent upload of all. The days beside the numbers say so. Empty
    # where the registry holds nothing under the key, and empty in a search.
    versions:
      - # The version as the registry has it.
        number: string
        # What the uploader declared it as: stable, beta, alpha, experimental, test,
        # obsolete.
        state: string
        # The upload day, as YYYY-MM-DD in UTC. The registry writes the same moment
        # in its own timezone, so a release made late in the day carries the day
        # before here.
        uploaded: string
        # The TYPO3 majors the release declares it runs on, lowest first.
        majors: [integer]
        # The constraints.depends.typo3 the release declared, as ext_emconf.php
        # wrote it — for example ">=13.4.15 <=14.3.99". Empty where the release
        # declared none, which the registry accepts on an upload made by a
        # controller.
        constraint: string
    # What a search found: the Packagist matches in its order, then what the TER
    # found and Packagist did not. Empty where the call named a key.
    matches:
      - # The Composer package name, which composer require takes. Empty where the
        # TER knows the extension under its key alone.
        package: string
        # The extension key the TER holds it under, which extension takes in the
        # next call. Empty where only Packagist found it, which does not say that
        # the TER holds no key for it.
        key: string
        # What the package says it does.
        description: string
        # The newest version on the TER. Empty where the TER search did not find it.
        version: string
        # The upload day of that version, as YYYY-MM-DD in UTC. Empty with version.
        uploaded: string
        # The TYPO3 majors that version declares, lowest first. A declaration by the
        # author, not a test result.
        majors: [integer]
        # The constraints.depends.typo3 that version declared. Empty with version.
        constraint: string
        # Downloads the TER counts. Zero where the TER search did not find it.
        terDownloads: integer
        # Installs Packagist counts. Zero where the Packagist search did not find
        # it.
        packagistDownloads: integer
        # The searches that found it.
        foundIn: [string]
    # Each registry a search read, and what it answered. A registry that is
    # unavailable here is a registry whose matches are missing from matches. Empty
    # where the call named a key.
    searches:
      - # One of: https://extensions.typo3.org, https://packagist.org.
        source: string
        # What the search read.
        url: string
        # One of: answered, empty, unavailable.
        status: string
        # How many matches that registry counts, of which matches carries at most
        # limit.
        total: integer
    # Why the registry answered nothing, where status says unavailable. Null
    # otherwise, and null on a key with nothing published under it; that one is an
    # answer. In a search, why neither registry answered.
    unavailable:
      # One of: source-not-answering, source-not-parseable. source-not-answering:
      # the registry did not answer this time. source-not-parseable: something
      # answered with a page rather than with the API.
      cause: string
      reason: string
    # What the caller has to do with this answer that no field states beside the
    # value it is about: the next call, a command to run first, a boundary on what
    # to reuse. A notice that this server is stale or behind its repository opens
    # the list. Empty where nothing is owed.
    owed: [string]

Answered
--------

Recorded on 2026-10-29 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 15.0.0-dev, the main core checkout below .checkouts/. Its
console is out of reach: <installation> has no TYPO3 console — none of
bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed —
vendor/autoload.php is not there either, and composer install writes both.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

ter: what is published under a key
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "extension": "blog",
        "limit": 3
    }

Text:

.. code-block:: text

    TYPO3 Extension Repository: blog — 32 published versions, highest 14.0.1 (stable, uploaded 2026-08-20)
    https://extensions.typo3.org/extension/blog

    ## The newest 3 of 32, by version number
    Version order and not upload order: a release on an older line sits further down and may have been uploaded after the ones above it, which the days say.
    - 14.0.1 · stable · uploaded 2026-08-20 · TYPO3 13, 14 · >=13.4.15 <=14.3.99
    - 14.0.0 · stable · uploaded 2025-08-25 · TYPO3 13 · >=13.4.15 <=13.4.99
    - 13.0.1 · stable · uploaded 2025-08-25 · TYPO3 12, 13 · >=12.4.22 <=13.4.99

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://extensions.typo3.org",
        "url": "https://extensions.typo3.org/api/v1/extension/blog/versions",
        "page": "https://extensions.typo3.org/extension/blog",
        "extension": "blog",
        "query": "",
        "extensionVersion": "",
        "held": null,
        "total": 32,
        "versions": [
            {
                "number": "14.0.1",
                "state": "stable",
                "uploaded": "2026-08-20",
                "majors": [
                    13,
                    14
                ],
                "constraint": ">=13.4.15 <=14.3.99"
            },
            {
                "number": "14.0.0",
                "state": "stable",
                "uploaded": "2025-08-25",
                "majors": [
                    13
                ],
                "constraint": ">=13.4.15 <=13.4.99"
            },
            {
                "number": "13.0.1",
                "state": "stable",
                "uploaded": "2025-08-25",
                "majors": [
                    12,
                    13
                ],
                "constraint": ">=12.4.22 <=13.4.99"
            }
        ],
        "matches": [],
        "searches": [],
        "unavailable": null,
        "owed": []
    }

ter: is this version already out
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "extension": "blog",
        "extensionVersion": "14.0.1",
        "limit": 3
    }

Text:

.. code-block:: text

    TYPO3 Extension Repository: blog — 32 published versions, highest 14.0.1 (stable, uploaded 2026-08-20)
    https://extensions.typo3.org/extension/blog

    The registry holds 14.0.1 · stable · uploaded 2026-08-20 · TYPO3 13, 14 · >=13.4.15 <=14.3.99.

    ## The newest 3 of 32, by version number
    Version order and not upload order: a release on an older line sits further down and may have been uploaded after the ones above it, which the days say.
    - 14.0.1 · stable · uploaded 2026-08-20 · TYPO3 13, 14 · >=13.4.15 <=14.3.99
    - 14.0.0 · stable · uploaded 2025-08-25 · TYPO3 13 · >=13.4.15 <=13.4.99
    - 13.0.1 · stable · uploaded 2025-08-25 · TYPO3 12, 13 · >=12.4.22 <=13.4.99

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://extensions.typo3.org",
        "url": "https://extensions.typo3.org/api/v1/extension/blog/versions",
        "page": "https://extensions.typo3.org/extension/blog",
        "extension": "blog",
        "query": "",
        "extensionVersion": "14.0.1",
        "held": true,
        "total": 32,
        "versions": [
            {
                "number": "14.0.1",
                "state": "stable",
                "uploaded": "2026-08-20",
                "majors": [
                    13,
                    14
                ],
                "constraint": ">=13.4.15 <=14.3.99"
            },
            {
                "number": "14.0.0",
                "state": "stable",
                "uploaded": "2025-08-25",
                "majors": [
                    13
                ],
                "constraint": ">=13.4.15 <=13.4.99"
            },
            {
                "number": "13.0.1",
                "state": "stable",
                "uploaded": "2025-08-25",
                "majors": [
                    12,
                    13
                ],
                "constraint": ">=12.4.22 <=13.4.99"
            }
        ],
        "matches": [],
        "searches": [],
        "unavailable": null,
        "owed": []
    }

ter: nothing is published under this key
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "extension": "quantumflux_transponder"
    }

Text:

.. code-block:: text

    TYPO3 Extension Repository: nothing is published under the key "quantumflux_transponder" at https://extensions.typo3.org.
    The registry holds no extension by that key. That is not a statement that no such package exists: one distributed through Composer alone is never registered here, and a key can be registered without a version having been uploaded to it yet.

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://extensions.typo3.org",
        "url": "https://extensions.typo3.org/api/v1/extension/quantumflux_transponder/versions",
        "page": "https://extensions.typo3.org/extension/quantumflux_transponder",
        "extension": "quantumflux_transponder",
        "query": "",
        "extensionVersion": "",
        "held": null,
        "total": 0,
        "versions": [],
        "matches": [],
        "searches": [],
        "unavailable": null,
        "owed": []
    }

ter: a composer package name
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "extension": "georgringer/news"
    }

Text:

.. code-block:: text

    TYPO3 Extension Repository: "georgringer/news" is not an extension key, so nothing was read. That is about the name and not about what is published.
    A key is lowercase letters, digits and underscores, three to thirty characters, starting with a letter. A slash or a dash means a Composer package name — the key is what extra.typo3/cms.extension-key declares in that package's own composer.json. To find an extension by its package name or by its subject, pass query instead.

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://extensions.typo3.org",
        "url": "",
        "page": "",
        "extension": "georgringer/news",
        "query": "",
        "extensionVersion": "",
        "held": null,
        "total": 0,
        "versions": [],
        "matches": [],
        "searches": [],
        "unavailable": null,
        "owed": []
    }

ter: the extensions for a subject
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "news",
        "limit": 3
    }

Text:

.. code-block:: text

    Published extensions for "news": 522 on https://extensions.typo3.org, 283 extension packages on https://packagist.org

    - georgringer/news · key news · 14.1.2, uploaded 2026-10-12 · TYPO3 13, 14 · 5781129 installs
      News system - Versatile news system based on Extbase & Fluid and using the latest technologies provided by TYPO3 CMS.
    - georgringer/eventnews · 678167 installs
      Extend EXT:news with event and calendar functionality
    - georgringer/news-seo · 119707 installs
      Indivdual indexing/robot information for each news article record

    A version range here is what the author declared, not evidence that the release was tested on it. Read every version of one with typo3_ter_lookup and extension set to its key. Whether to take one or build your own is typo3_hint_lookup with id="extension-reuse".

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://extensions.typo3.org and https://packagist.org",
        "url": "",
        "page": "",
        "extension": "",
        "query": "news",
        "extensionVersion": "",
        "held": null,
        "total": 0,
        "versions": [],
        "matches": [
            {
                "package": "georgringer/news",
                "key": "news",
                "description": "News system - Versatile news system based on Extbase & Fluid and using the latest technologies provided by TYPO3 CMS.",
                "version": "14.1.2",
                "uploaded": "2026-10-12",
                "majors": [
                    13,
                    14
                ],
                "constraint": ">=13.4.29 <=14.99.99",
                "terDownloads": 4354780,
                "packagistDownloads": 5781129,
                "foundIn": [
                    "https://packagist.org",
                    "https://extensions.typo3.org"
                ]
            },
            {
                "package": "georgringer/eventnews",
                "key": "",
                "description": "Extend EXT:news with event and calendar functionality",
                "version": "",
                "uploaded": "",
                "majors": [],
                "constraint": "",
                "terDownloads": 0,
                "packagistDownloads": 678167,
                "foundIn": [
                    "https://packagist.org"
                ]
            },
            {
                "package": "georgringer/news-seo",
                "key": "",
                "description": "Indivdual indexing/robot information for each news article record",
                "version": "",
                "uploaded": "",
                "majors": [],
                "constraint": "",
                "terDownloads": 0,
                "packagistDownloads": 119707,
                "foundIn": [
                    "https://packagist.org"
                ]
            }
        ],
        "searches": [
            {
                "source": "https://extensions.typo3.org",
                "url": "https://extensions.typo3.org/api/v1/extension?filter%5Bsearch%5D=news&per_page=3",
                "status": "answered",
                "total": 522
            },
            {
                "source": "https://packagist.org",
                "url": "https://packagist.org/search.json?q=news&type=typo3-cms-extension&per_page=3",
                "status": "answered",
                "total": 283
            }
        ],
        "unavailable": null,
        "owed": [
            "A version range here is what the author declared, not evidence that the release was tested on it.",
            "Read every version of one with typo3_ter_lookup and extension set to its key.",
            "Whether to take one or build your own is typo3_hint_lookup with id=\"extension-reuse\"."
        ]
    }

ter: no extension for a subject
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "quantumflux transponder"
    }

Text:

.. code-block:: text

    No published extension matches "quantumflux transponder". Another word for the subject may still find one.

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://extensions.typo3.org and https://packagist.org",
        "url": "",
        "page": "",
        "extension": "",
        "query": "quantumflux transponder",
        "extensionVersion": "",
        "held": null,
        "total": 0,
        "versions": [],
        "matches": [],
        "searches": [
            {
                "source": "https://extensions.typo3.org",
                "url": "https://extensions.typo3.org/api/v1/extension?filter%5Bsearch%5D=quantumflux%20transponder&per_page=10",
                "status": "empty",
                "total": 0
            },
            {
                "source": "https://packagist.org",
                "url": "https://packagist.org/search.json?q=quantumflux%20transponder&type=typo3-cms-extension&per_page=10",
                "status": "empty",
                "total": 0
            }
        ],
        "unavailable": null,
        "owed": []
    }
