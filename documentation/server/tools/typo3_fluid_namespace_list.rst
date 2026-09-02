.. _typo3_fluid_namespace_list:

``typo3_fluid_namespace_list``
==============================

List the Fluid ViewHelper namespaces that are globally available in the TYPO3
installation you are working in, so a template knows which prefixes it may use
without declaring them. Every other namespace has to be declared per template
with an xmlns attribute. On TYPO3 v14 and later, the fluid:namespaces console
command answers; where it cannot be reached, the
Configuration/Fluid/Namespaces.php files introduced in that version are read
instead. Earlier versions are booted and answered from SYS/fluid/namespaces in
TYPO3_CONF_VARS. Answers from: installation, packages.

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
    # answered. packages: read from the files the installed packages ship, because
    # the console could not be asked — overrides applied at runtime are not
    # reflected.
    answeredBy: string  # optional
    namespaces:  # optional
      - # The prefix usable in a template without declaring it, for example "core".
        prefix: string
        # The PHP namespaces it resolves ViewHelpers from.
        phpNamespaces: [string]
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

The answer carries exactly one of these sets of fields: ``matchCount``,
``answeredBy``, ``namespaces`` — or ``unsupported``.

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

namespaces
~~~~~~~~~~

Called with:

.. code-block:: json

    {}

From the main core checkout
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
