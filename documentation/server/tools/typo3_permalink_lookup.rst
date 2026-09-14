.. _typo3_permalink_lookup:

``typo3_permalink_lookup``
==========================

Validate docs.typo3.org permalink identifiers and turn old documentation URLs
into the identifiers that replace them. Pass identifiers such as
t3coreapi:extension-scanner, or a system extension by its Composer package name
in either form the core writes, typo3/cms-felogin:start. Pass urls such as
https://docs.typo3.org/m/typo3/reference-tca/11.5/en-us/Columns/Properties/OnChange.html.
Pass as many at a time as you hold. One manual inventory answers every
identifier of that manual. So a sweep over a whole checkout costs a call per
manual rather than a request per link. Each identifier comes back with the page
and anchor it reaches. It comes with the other forms that reach the same target,
and which of them the manual declares. Each URL comes back with the identifiers
that point at it, or the near names where the page is gone. It says which branch
answered, because the host redirects a manual it has no branch for to main and
does not say so. This answers where a link points and not what a manual says
about a subject — for the prose, ask typo3_documentation_lookup. Answers from:
network.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`network <answer-sources-network>`.

Takes
-----

.. code-block:: yaml

    # Permalink identifiers written <shortcode>:<name>, as they appear in code —
    # t3coreapi:extension-scanner, t3tca:columns-onchange, typo3/cms-felogin:start.
    # An @<branch> at the end pins that identifier to a branch of its own and
    # overrides targetVersion for it. A call carries identifiers, urls, or both.
    identifiers: [string]  # optional
    # docs.typo3.org page URLs to read the other way, anchor included where there is
    # one. They resolve at targetVersion rather than at the branch they name,
    # because a link up for replacement asks what the identifier is now. A call
    # carries identifiers, urls, or both.
    urls: [string]  # optional
    # Covered TYPO3 version whose manuals answer, for example "13.4" or "14". There
    # is no fallback to another release.
    targetVersion: string

Answers with
------------

.. code-block:: yaml

    # One of: answered, empty, unavailable.
    status: string
    # The documentation release the manuals answered at.
    targetVersion: string
    # The external documentation host.
    source: string
    identifiers:
      - # The identifier as the call passed it, @<branch> included.
        identifier: string
        # The manual it names, in the form that manual declares.
        shortcode: string
        # The name inside that manual, in the form its inventory carries.
        name: string
        # The branch the lookup ran on: its own @<branch>, else targetVersion.
        branch: string
        # Whether the manual registers this name. False is a legitimate answer and
        # reason says which of the three it is. The identifier is not written as
        # one, this server knows no manual for the shortcode, or the manual has no
        # such name.
        resolved: boolean
        # The manual on docs.typo3.org, as its path names it.
        manual: string or null
        # What that manual calls itself.
        manualTitle: string or null
        # The page and anchor this identifier reaches. Null on a miss.
        url: string or null
        # The same URL without the anchor.
        page: string or null
        # The fragment on that page, absent for a name that is the page.
        anchor: string or null
        # What this name is in the manual, in Sphinx's own vocabulary. std:label is
        # a section, std:confval a configuration value, php:class and php:method
        # API, std:console:command a command. std:doc is not among them — a page
        # is not addressable as a permalink.
        roles: [string]
        # Every other name that reaches the same target. A configuration value
        # carries two: the std:confval the manual declares, and the std:label Sphinx
        # generates from its anchor with a confval- prefix. An old anchor survives
        # here beside the current one.
        alsoKnownAs:
          - name: string
            roles: [string]
        # Which of the equivalent names to write. The std:confval one where the
        # target has one, because that is what the manual declares and the rest
        # derives from it. Otherwise the name asked for.
        preferred: string or null
        # The branch the manual that answered says it is, read off its inventory.
        # Different from branch means the host has no such branch and served main
        # instead, so the identifier is not proven for the release asked about. Null
        # where no manual answered.
        answeredBranch: string or null
        # Why it did not resolve. Null on a hit.
        reason: string or null
    urls:
      - # The URL as the call passed it.
        url: string
        # The manual it belongs to, as a permalink names it.
        shortcode: string or null
        # That manual on docs.typo3.org, as its path names it.
        manual: string or null
        # The branch its manual answered at, which is targetVersion.
        branch: string
        # The branch the URL itself names, which is what a link left in code points
        # at.
        urlBranch: string or null
        # The page inside the manual.
        page: string or null
        # The fragment the URL names, if any.
        anchor: string or null
        # The names that reach this exact target, which is what replaces the URL.
        # Empty means the manual at this version has no such page or anchor.
        identifiers:
          - name: string
            roles: [string]
        # Names with the words of the URL, best first, where nothing reaches it
        # exactly. They are candidates a reader picks from, not the answer: a manual
        # that moved a subject leaves nothing behind that says where it went.
        nearest:
          - name: string
            roles: [string]
            url: string
        # The branch the manual that answered says it is. Null where none answered.
        answeredBranch: string or null
        # Why nothing exact came back. Null where something did.
        reason: string or null
    # Why the source answered nothing, where status says unavailable. Null
    # otherwise.
    unavailable:
      # One of: version-not-covered, source-not-answering. version-not-covered: the
      # release asked about is outside the ones this server knows the manuals for,
      # and a second call changes nothing. source-not-answering: docs.typo3.org did
      # not answer this time, and the same call may answer the next.
      cause: string
      reason: string

Answered
--------

Recorded on 2026-09-10 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 14.3.7-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed
— vendor/autoload.php is not there either, and composer install writes both.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

permalink: identifiers and a URL at once
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "identifiers": [
            "t3tca:columns-onchange",
            "typo3-cms-lowlevel:start"
        ],
        "urls": [
            "https://docs.typo3.org/m/typo3/reference-tca/11.5/en-us/Columns/Properties/OnChange.html"
        ],
        "targetVersion": "14.3"
    }

Text:

.. code-block:: text

    docs.typo3.org permalinks for TYPO3 14.3.
    Source: https://docs.typo3.org

    ## t3tca:columns-onchange — resolves
    TCA Reference · 14.3 · https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Columns/Index.html#confval-columns-onchange
    Registered as: std:confval
    The same target is also confval-columns-onchange (std:label). Write t3tca:columns-onchange.

    ## typo3-cms-lowlevel:start — does not resolve
    Low Level registers no "start" on 14.3.

    ## https://docs.typo3.org/m/typo3/reference-tca/11.5/en-us/Columns/Properties/OnChange.html
    TCA Reference at 14.3 has no page Columns/Properties/OnChange.html. The near names below are what its inventory carries.
    - t3tca:columns-properties-onchange (std:label) — https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Columns/Index.html#columns-properties-onChange
    - t3tca:columns-onchange (std:confval) — https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Columns/Index.html#confval-columns-onchange
    - t3tca:columns-properties (std:label) — https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Columns/Index.html#columns-properties
    - t3tca:columns-file-properties (std:label) — https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/ColumnsConfig/Type/File/Index.html#columns-file-properties
    - t3tca:columns-flex-properties (std:label) — https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/ColumnsConfig/Type/Flex/Index.html#columns-flex-properties

Data:

.. code-block:: json

    {
        "status": "answered",
        "targetVersion": "14.3",
        "source": "https://docs.typo3.org",
        "identifiers": [
            {
                "identifier": "t3tca:columns-onchange",
                "shortcode": "t3tca",
                "name": "columns-onchange",
                "branch": "14.3",
                "resolved": true,
                "manual": "typo3/reference-tca",
                "manualTitle": "TCA Reference",
                "url": "https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Columns/Index.html#confval-columns-onchange",
                "page": "https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Columns/Index.html",
                "anchor": "confval-columns-onchange",
                "roles": [
                    "std:confval"
                ],
                "alsoKnownAs": [
                    {
                        "name": "confval-columns-onchange",
                        "roles": [
                            "std:label"
                        ]
                    }
                ],
                "preferred": "columns-onchange",
                "answeredBranch": "14.3",
                "reason": null
            },
            {
                "identifier": "typo3-cms-lowlevel:start",
                "shortcode": "typo3-cms-lowlevel",
                "name": "start",
                "branch": "14.3",
                "resolved": false,
                "manual": "typo3/cms-lowlevel",
                "manualTitle": "Low Level",
                "url": null,
                "page": null,
                "anchor": null,
                "roles": [],
                "alsoKnownAs": [],
                "preferred": null,
                "answeredBranch": "14.3",
                "reason": "Low Level registers no \"start\" on 14.3."
            }
        ],
        "urls": [
            {
                "url": "https://docs.typo3.org/m/typo3/reference-tca/11.5/en-us/Columns/Properties/OnChange.html",
                "shortcode": "t3tca",
                "manual": "typo3/reference-tca",
                "branch": "14.3",
                "urlBranch": "11.5",
                "page": "Columns/Properties/OnChange.html",
                "anchor": null,
                "identifiers": [],
                "nearest": [
                    {
                        "name": "columns-properties-onchange",
                        "roles": [
                            "std:label"
                        ],
                        "url": "https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Columns/Index.html#columns-properties-onChange"
                    },
                    {
                        "name": "columns-onchange",
                        "roles": [
                            "std:confval"
                        ],
                        "url": "https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Columns/Index.html#confval-columns-onchange"
                    },
                    {
                        "name": "columns-properties",
                        "roles": [
                            "std:label"
                        ],
                        "url": "https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Columns/Index.html#columns-properties"
                    },
                    {
                        "name": "columns-file-properties",
                        "roles": [
                            "std:label"
                        ],
                        "url": "https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/ColumnsConfig/Type/File/Index.html#columns-file-properties"
                    },
                    {
                        "name": "columns-flex-properties",
                        "roles": [
                            "std:label"
                        ],
                        "url": "https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/ColumnsConfig/Type/Flex/Index.html#columns-flex-properties"
                    }
                ],
                "answeredBranch": "14.3",
                "reason": "TCA Reference at 14.3 has no page Columns/Properties/OnChange.html. The near names below are what its inventory carries."
            }
        ],
        "unavailable": null
    }

permalink: no manual is known for the shortcode
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "identifiers": [
            "quantumflux:start"
        ],
        "targetVersion": "14.3"
    }

Text:

.. code-block:: text

    docs.typo3.org permalinks for TYPO3 14.3.
    Source: https://docs.typo3.org
    Could not answer: No manual named by this call could be read from docs.typo3.org.

Data:

.. code-block:: json

    {
        "status": "unavailable",
        "targetVersion": "14.3",
        "source": "https://docs.typo3.org",
        "identifiers": [
            {
                "identifier": "quantumflux:start",
                "shortcode": "quantumflux",
                "name": "start",
                "branch": "14.3",
                "resolved": false,
                "manual": null,
                "manualTitle": null,
                "url": null,
                "page": null,
                "anchor": null,
                "roles": [],
                "alsoKnownAs": [],
                "preferred": null,
                "answeredBranch": null,
                "reason": "No manual is known for the shortcode \"quantumflux\". The named ones are t3coreapi, t3tsref, t3tca, t3viewhelper, changelog, t3start, t3sitepackage, t3editors, t3contribute, h2document, t3renderguides, fluid, and a system extension is addressed by its Composer package name, for example typo3/cms-felogin or typo3-cms-felogin."
            }
        ],
        "urls": [],
        "unavailable": {
            "cause": "source-not-answering",
            "reason": "No manual named by this call could be read from docs.typo3.org."
        }
    }

permalink: unsupported version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "identifiers": [
            "t3coreapi:start"
        ],
        "targetVersion": "999"
    }

Text:

.. code-block:: text

    docs.typo3.org permalinks for TYPO3 999.
    Source: https://docs.typo3.org
    Could not answer: TYPO3 999 is outside the covered versions: 12.4, 13.4, 14.3, main.

Data:

.. code-block:: json

    {
        "status": "unavailable",
        "targetVersion": "999",
        "source": "https://docs.typo3.org",
        "identifiers": [],
        "urls": [],
        "unavailable": {
            "cause": "version-not-covered",
            "reason": "TYPO3 999 is outside the covered versions: 12.4, 13.4, 14.3, main."
        }
    }
