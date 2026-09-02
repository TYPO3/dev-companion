.. _typo3_configuration_lookup:

``typo3_configuration_lookup``
==============================

Read an effective TYPO3_CONF_VARS value from the installation you are working in
— the value as it is at runtime after every extension has had its say, not the
shipped default. Use it for configuration whose assembled shape matters, such as
SYS/formEngine/formDataGroup, SYS/caching/cacheConfigurations, or SYS/fluid. Ask
it for one form data group — SYS/formEngine/formDataGroup/tcaDatabaseRecord —
and the answer also carries the order the providers actually run in, resolved by
the installation from the depends and before each declares, which is what
decides whether one provider sees what another wrote. It answers for the
installation as it stands, in the environment it is in: a value that has to be
shown resolving under another environment — a variable set, a
development-environment marker absent — is the project's own console, one run
per environment. Answers from: installation.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`.

Takes
-----

.. code-block:: yaml

    # Slash-separated path into TYPO3_CONF_VARS, for example "SYS/fluid" or
    # "SYS/formEngine/formDataGroup".
    path: string

Answers with
------------

.. code-block:: yaml

    # The TYPO3_CONF_VARS path that was read.
    path: string
    # Whether the installation has a value at that path. Present only where one was
    # asked: false is a statement about an installation, and where there was none to
    # ask, unsupported stands in place of this answer.
    found: boolean  # optional
    # The effective runtime value, of whatever shape the configuration has.
    value: object  # optional
    # The order the providers actually run in, present only where the path names one
    # form data group and the installation answered. The registry under it is a
    # dependency graph, so this is what it resolves to rather than what it is
    # written as.
    resolvedOrder:  # optional
      - # Position in the run, counting from zero.
        index: integer
        # Fully qualified class name of the form data provider.
        provider: string
        # What it declares it runs after.
        depends: [string]
        # What it declares it runs before.
        before: [string]
    # One of: installation. installation: its assembled runtime state answered.
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

The answer carries exactly one of these sets of fields: ``path``, ``found``,
``answeredBy`` — or ``path``, ``unsupported``.

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

configuration
~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "path": "SYS/fluid"
    }

From the main core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both.

Data:

.. code-block:: json

    {
        "path": "SYS/fluid",
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

    The installation has no configuration at "SYS/fluid".

Data:

.. code-block:: json

    {
        "path": "SYS/fluid",
        "found": false,
        "value": null,
        "answeredBy": "installation"
    }
