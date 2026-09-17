.. _typo3_configuration_lookup:

``typo3_configuration_lookup``
==============================

Read an effective TYPO3_CONF_VARS value from the installation you work in. That
is the value as it is at runtime after every extension has had its say, not the
shipped default. Use it for configuration whose assembled shape matters, such as
SYS/formEngine/formDataGroup, SYS/caching/cacheConfigurations, or SYS/fluid. Ask
it for one form data group, SYS/formEngine/formDataGroup/tcaDatabaseRecord, and
the answer also carries the order the providers run in. The installation
resolves that order from the depends and before each declares, and it decides
whether one provider sees what another wrote. It answers for the installation as
it stands, in the environment it is in. A value that has to resolve under
another environment is the project's own console, one run per environment. That
is a variable set, or a development-environment marker absent. Answers from:
installation.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`.

Takes
-----

.. code-block:: yaml

    # Slash-separated path into TYPO3_CONF_VARS, for example "SYS/fluid" or
    # "SYS/formEngine/formDataGroup". Named apart from path, which is a file
    # everywhere else on this server — D-ANS-137.
    configurationPath: string

Answers with
------------

.. code-block:: yaml

    # The TYPO3_CONF_VARS path the call read.
    configurationPath: string
    # Whether the installation has a value at that path. Present only where the call
    # asked one. False is a statement about an installation, and where there was
    # none to ask, unsupported stands in place of this answer.
    found: boolean  # optional
    # The effective runtime value, of whatever shape the configuration has.
    value: object  # optional
    # The order the providers actually run in, present only where the path names one
    # form data group and the installation answered. The registry under it is a
    # dependency graph, so this is what it resolves to rather than what the files
    # write.
    resolvedOrder:  # optional
      - # Position in the run, counted from zero.
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

- ``configurationPath``, ``found``, ``answeredBy``
- ``configurationPath``, ``unsupported``

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

configuration
~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "configurationPath": "SYS/fluid"
    }

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both.

Data:

.. code-block:: json

    {
        "configurationPath": "SYS/fluid",
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
        "configurationPath": "SYS/fluid",
        "found": false,
        "value": null,
        "answeredBy": "installation"
    }
