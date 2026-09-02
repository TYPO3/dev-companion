.. _typo3_ter_lookup:

``typo3_ter_lookup``
====================

Read what the TYPO3 Extension Repository has published under an extension key,
live from extensions.typo3.org. Pass extension with the key — the one
extra.typo3/cms.extension-key declares, not the Composer package name — and
every published version comes back, highest number first: the number, the state,
the day it was uploaded, the TYPO3 majors it declares and the
constraints.depends.typo3 it was released with. This is the question a release
audit cannot answer from the repository it is auditing: Tailor refuses to
package unless ext_emconf.php names the version being released, so that file
still names it after the upload and a checkout that has been published reads
exactly like one that has not. Pass extensionVersion as well to be told whether
the registry already holds that number; it reports what is published and judges
no version free, and comparing it against the working tree is yours. A key
nothing is published under is answered as such, which is not a statement that no
such package exists — an extension distributed through Composer alone is never
registered here. What publishing requires of the extension itself is
typo3_hint_lookup with id="extension-ter-release". Reading only, and no
credential: registering a key, uploading a version and transferring an extension
stay yours, through Tailor and the token it carries. Answers from: network.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`network <answer-sources-network>`.

Takes
-----

.. code-block:: yaml

    # The extension key, for example "news" or "bootstrap_package". That is the key
    # extra.typo3/cms.extension-key declares in the package's composer.json, which
    # every TYPO3 extension package has to carry to install at all — not the
    # Composer package name, which the registry does not take: "georgringer/news"
    # and "bootstrap-package" are the two shapes that reach it as a name of the
    # wrong kind. Lowercase letters, digits and underscores, three to thirty
    # characters.
    extension: string
    # One version number to be answered about, for example "14.0.1" — typically
    # the one ext_emconf.php names. The answer says whether the registry holds it
    # and, where it does, what that release declared. Compared as the registry
    # writes it, which is exactly three numbers: a suffix of any kind belongs to no
    # published version, because the upload route accepts none.
    extensionVersion: string  # optional
    # How many versions come back, newest number first. The count of everything
    # published comes with them, so a cut list says so. A widely maintained
    # extension has a hundred, and what a release audit reads is the top of them.
    limit: integer  # optional

Answers with
------------

.. code-block:: yaml

    # One of: answered, empty, unavailable.
    status: string
    # The registry the answer came from.
    source: string
    # What was read, so the same question can be asked again by hand. Empty where
    # the key was answered without a read.
    url: string
    # Where a person reads the extension's own page in the registry. Empty where the
    # key is not one the registry takes.
    page: string
    # The key that was asked for, lowercased, as it was sent.
    extension: string
    # The version number the call asked about, as it was passed. Empty where none
    # was.
    extensionVersion: string
    # Whether the registry has published that exact number. Null where the call
    # named no version, and null where nothing was read at all — a false here is
    # the registry answering, never a question that failed. It is a fact about the
    # registry and not a judgement that the number is free to release.
    held: boolean or null
    # How many versions are published under the key in total, of which versions
    # carries at most limit. Zero where none is.
    total: integer
    # The published versions, highest number first. That is version order and not
    # upload order: a maintenance release on an older line sits further down and may
    # be the most recent upload of all, which is what the days beside the numbers
    # say. Empty where nothing is published under the key.
    versions:
      - # The version as it was published.
        number: string
        # What the uploader declared it as: stable, beta, alpha, experimental, test,
        # obsolete.
        state: string
        # The day it was uploaded, as YYYY-MM-DD in UTC. The registry writes the
        # same moment in its own timezone, so a release made late in the day is
        # dated one earlier here.
        uploaded: string
        # The TYPO3 majors the release declares it runs on, ascending.
        majors: [integer]
        # The constraints.depends.typo3 the release declared, as ext_emconf.php
        # wrote it — for example ">=13.4.15 <=14.3.99". Empty where the release
        # declared none, which the registry accepts on an upload made by a
        # controller.
        constraint: string
    # Why nothing was answered, where status says unavailable. Null otherwise, and
    # null on a key nothing is published under — that one is an answer.
    unavailable:
      # One of: source-not-answering, source-not-parseable. source-not-answering:
      # the registry did not answer this time. source-not-parseable: something
      # answered with a page rather than with the API.
      cause: string
      reason: string

Answered
--------

Recorded on 2026-09-02 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 15.0.0-dev, the main core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed
— vendor/autoload.php is not there either, and composer install writes both.
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
        "unavailable": null
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
        "unavailable": null
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
        "extensionVersion": "",
        "held": null,
        "total": 0,
        "versions": [],
        "unavailable": null
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
    A key is lowercase letters, digits and underscores, three to thirty characters, starting with a letter. A slash or a dash means a Composer package name — the key is what extra.typo3/cms.extension-key declares in that package's own composer.json.

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://extensions.typo3.org",
        "url": "",
        "page": "",
        "extension": "georgringer/news",
        "extensionVersion": "",
        "held": null,
        "total": 0,
        "versions": [],
        "unavailable": null
    }
