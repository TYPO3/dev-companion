.. _typo3_fluid_namespace_list:

``typo3_fluid_namespace_list``
==============================

List the Fluid ViewHelper namespaces that are global in the TYPO3 installation
you work in. So a template knows which prefixes it may use without a
declaration. A template has to declare every other namespace with an xmlns
attribute. On TYPO3 v14 and later, the fluid:namespaces console command answers.
Where the console does not answer, the tool reads the
Configuration/Fluid/Namespaces.php files that version introduced instead.
Earlier versions boot and answer from SYS/fluid/namespaces in TYPO3_CONF_VARS.
Answers from: installation, packages.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`,
:ref:`packages <answer-sources-packages>`.

Takes
-----

Nothing.

Answers with
------------

.. code-block:: yaml

    matchCount: integer  # optional
    # One of: installation, packages. installation: its assembled runtime state
    # answered. packages: the server read the files the installed packages ship,
    # because it could not ask the console. The answer misses the overrides that
    # apply at runtime.
    answeredBy: string  # optional
    namespaces:  # optional
      - # The prefix a template can use without a declaration, for example "core".
        prefix: string
        # The PHP namespaces it resolves ViewHelpers from.
        phpNamespaces: [string]
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

- ``matchCount``, ``answeredBy``, ``namespaces``
- ``unsupported``

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

namespaces
~~~~~~~~~~

Called with:

.. code-block:: json

    {}

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    3 globally registered Fluid namespace(s):
    - core: TYPO3\CMS\Core\ViewHelpers
    - f: TYPO3\CMS\Adminpanel\ViewHelpers\Fluid, TYPO3Fluid\Fluid\ViewHelpers, TYPO3\CMS\Fluid\ViewHelpers
    - formvh: TYPO3\CMS\Form\ViewHelpers

    These prefixes work in any template without being declared. Every other namespace is declared in the template itself — xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" on the root element, together with data-namespace-typo3-fluid="true" so the declaration is stripped from the output.

    Read from the Configuration/Fluid/Namespaces.php of the installed packages: the console could not be asked (<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both). That is what the packages declare, not what the container assembled from them.

Data:

.. code-block:: json

    {
        "matchCount": 3,
        "namespaces": [
            {
                "prefix": "core",
                "phpNamespaces": [
                    "TYPO3\\CMS\\Core\\ViewHelpers"
                ]
            },
            {
                "prefix": "f",
                "phpNamespaces": [
                    "TYPO3\\CMS\\Adminpanel\\ViewHelpers\\Fluid",
                    "TYPO3Fluid\\Fluid\\ViewHelpers",
                    "TYPO3\\CMS\\Fluid\\ViewHelpers"
                ]
            },
            {
                "prefix": "formvh",
                "phpNamespaces": [
                    "TYPO3\\CMS\\Form\\ViewHelpers"
                ]
            }
        ],
        "answeredBy": "packages"
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    2 globally registered Fluid namespace(s):
    - core: TYPO3\CMS\Core\ViewHelpers
    - f: TYPO3Fluid\Fluid\ViewHelpers, TYPO3\CMS\Fluid\ViewHelpers

    These prefixes work in any template without being declared. Every other namespace is declared in the template itself — xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" on the root element, together with data-namespace-typo3-fluid="true" so the declaration is stripped from the output.

Data:

.. code-block:: json

    {
        "matchCount": 2,
        "namespaces": [
            {
                "prefix": "core",
                "phpNamespaces": [
                    "TYPO3\\CMS\\Core\\ViewHelpers"
                ]
            },
            {
                "prefix": "f",
                "phpNamespaces": [
                    "TYPO3Fluid\\Fluid\\ViewHelpers",
                    "TYPO3\\CMS\\Fluid\\ViewHelpers"
                ]
            }
        ],
        "answeredBy": "installation"
    }
