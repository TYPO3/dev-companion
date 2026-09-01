.. _typo3_translation_domain_lookup:

``typo3_translation_domain_lookup``
===================================

Compute the translation domain an XLF file resolves to, from its path. The
domain is the canonical way to reference a label (backend.alt_doc:key) in TCA,
LanguageService::sL() and f:translate, and it is registered nowhere: it follows
from the path by the rules the core itself applies, in TranslationDomainMapper
on one branch and TranslationDomainResolver on the next. Being computed, it also
answers for a file outside the core and for one a patch is about to add. On a
version older than translation domains it answers with the full LLL:EXT:
reference instead, because the domain form renders nothing there and fails at
runtime rather than at build time. That version is targetVersion, or the
installation this server was started in where none is stated — state one when
the work is on another branch than what is installed. It computes a reference
from a path and reads no label: whether the installation already registers one
to reuse, and under which id, is typo3_label_lookup. The answer also carries the
specifier a backend JavaScript module imports that domain under, which is the
same value in the form that module needs. Answers from: knowledge.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`knowledge <answer-sources-knowledge>`.

Takes
-----

.. code-block:: yaml

    # The XLF file path, either as an EXT: reference
    # ("EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf") or relative
    # to a core checkout
    # ("typo3/sysext/backend/Resources/Private/Language/locallang_alt_doc.xlf").
    path: string
    # The TYPO3 version the label is being written for, for example "13.4" or "14".
    # It decides one thing here and it decides it entirely: below the version that
    # resolves domains the domain form renders nothing, so the answer is the
    # LLL:EXT: reference instead. Defaults to the installation this server was
    # started in, which is the wrong answer for a backport branch or a second
    # checkout — state it there.
    targetVersion: string  # optional

Answers with
------------

.. code-block:: yaml

    # The XLF path the domain was computed from.
    path: string
    # The TYPO3 major the answer was composed for — stated by the caller, or read
    # from the installation. Null means neither said, and the domain comes back
    # unqualified: it is the form from 14 onwards, and nothing placed this call on a
    # version.
    targetVersion: integer or null  # optional
    # The translation domain it resolves to. Null when the path names no extension,
    # and also when the version this was composed for is too old to resolve domains
    # at all — there the full LLL:EXT: reference is the answer.
    domain: string or null
    # Set only in that second case: what the domain would be on a version that has
    # them. It is not usable on this installation.
    domainOnNewerVersions: string or null  # optional
    # The specifier a backend JavaScript module imports the same domain under:
    # import labels from '~labels/<domain>', read with labels.get(). Returned where
    # a domain was handed over, and absent where none was — the import map prefix
    # arrived with the domains themselves, so there is nothing to write on a version
    # below them.
    moduleImport: string or null  # optional

Answered
--------

Recorded on 2026-08-26 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 15.0.0-dev, the main core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed
— vendor/autoload.php is not there either, and composer install writes both.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

domain: EXT reference
~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
    }

Text:

.. code-block:: text

    EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf resolves to the translation domain:

      backend.alt_doc

    Reference a label in it as "backend.alt_doc:<trans-unit id>" — in TCA, in LanguageService::sL(), and in f:translate as separate domain and key attributes.
    Composed for the installation here, TYPO3 15.0.0-dev. State targetVersion where the label is being written for another branch.
    Which trans-units the file actually holds is a property of your checkout: read the file, and remember that an installation can override it through LANG/resourceOverrides.

Data:

.. code-block:: json

    {
        "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf",
        "targetVersion": 15,
        "domain": "backend.alt_doc",
        "domainOnNewerVersions": null
    }

domain: checkout path
~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "path": "typo3/sysext/core/Resources/Private/Language/locallang.xlf"
    }

Text:

.. code-block:: text

    typo3/sysext/core/Resources/Private/Language/locallang.xlf resolves to the translation domain:

      core.messages

    Reference a label in it as "core.messages:<trans-unit id>" — in TCA, in LanguageService::sL(), and in f:translate as separate domain and key attributes.
    Composed for the installation here, TYPO3 15.0.0-dev. State targetVersion where the label is being written for another branch.
    Which trans-units the file actually holds is a property of your checkout: read the file, and remember that an installation can override it through LANG/resourceOverrides.

Data:

.. code-block:: json

    {
        "path": "typo3/sysext/core/Resources/Private/Language/locallang.xlf",
        "targetVersion": 15,
        "domain": "core.messages",
        "domainOnNewerVersions": null
    }

domain: on an older target
~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf",
        "targetVersion": "13.4"
    }

Text:

.. code-block:: text

    TYPO3 13, which you asked about, has no translation domains: the API that resolves them arrived after it. Reference the file itself instead:

      LLL:EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:<trans-unit id>

    For the record, the domain this path would resolve to on a version that has them is "backend.alt_doc". Writing it into a label there renders nothing, and fails at runtime rather than at build time.

Data:

.. code-block:: json

    {
        "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf",
        "targetVersion": 13,
        "domain": null,
        "domainOnNewerVersions": "backend.alt_doc"
    }

domain: miss
~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "path": "somewhere/else.xlf"
    }

Text:

.. code-block:: text

    "somewhere/else.xlf" names no extension, so no translation domain follows from it.
    Pass either an EXT: reference ("EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf") or a checkout path ("typo3/sysext/backend/Resources/Private/Language/locallang_alt_doc.xlf").

Data:

.. code-block:: json

    {
        "path": "somewhere/else.xlf",
        "targetVersion": 15,
        "domain": null
    }
