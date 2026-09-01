.. _typo3_service_lookup:

``typo3_service_lookup``
========================

Find what the dependency injection container of the TYPO3 installation you are
working in assembles: which class stands behind a service id or an interface,
whether it is public, shared and autowired, the tags it carries, and what each
of its constructor arguments is handed — as the service id that lands there, per
position and after autowiring. Search by a substring of the id or the class, or
ask for one exact tag to enumerate what registers into an extension point:
event.listener, fluid.viewhelper, typo3.singleton, and the tags a package
declares itself. It answers what this installation resolved rather than what a
Services.yaml says, so a decoration, an override or an alias shows up as the
class that is really injected. Nothing is instantiated to answer it. Answers
from: installation.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`.

Takes
-----

.. code-block:: yaml

    # A case-insensitive substring matched against the service id and the class, for
    # example "PageRenderer" or "Imaging". Omit to ask by tag alone.
    query: string  # optional
    # One exact service tag, for example "event.listener". Omit to search by query
    # alone.
    tag: string  # optional
    # Maximum services to return.
    limit: integer  # optional

Answers with
------------

.. code-block:: yaml

    # The substring asked for, null where none was.
    query: string or null
    # The tag asked for, null where none was.
    tag: string or null
    # Services matching before the limit. Zero is an answer: nothing this
    # installation assembles carries that id, class or tag.
    matchCount: integer  # optional
    # One of: installation. installation: its assembled runtime state answered.
    answeredBy: string  # optional
    # Every service definition the container holds, which is what the match was made
    # against.
    definitionCount: integer  # optional
    # The aliases beside them. An interface usually reaches its implementation
    # through one.
    aliasCount: integer  # optional
    services:  # optional
      - # The service id, which is the class name for nearly all of them.
        id: string
        # The class the container instantiates, which a decoration or an override
        # makes different from the id.
        class: string
        # True where the container hands it out by id. A private service is only
        # ever injected.
        public: boolean
        # True where every caller gets the same instance.
        shared: boolean
        autowired: boolean
        abstract: boolean
        # True where the instance is set into the container at boot rather than
        # built by it.
        synthetic: boolean
        # The tags it carries, which is what an extension point enumerates by.
        tags: [string]
        # What the constructor is handed, after autowiring. Empty where it takes
        # nothing.
        arguments:
          - # The constructor position, counted from zero.
            position: integer
            # The service id handed to it, or "value" where a configured value is
            # passed instead of a service.
            resolves: string
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
      # What the reason means where the message alone does not say it — a console
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

The answer carries exactly one of these sets of fields: ``query``, ``tag``,
``matchCount``, ``answeredBy``, ``definitionCount``, ``aliasCount``,
``services`` — or ``query``, ``tag``, ``unsupported``.

Answered
--------

Recorded on 2026-09-01 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against composer-project, TYPO3
14.3.5, the E-SITE-14.3 this repository makes below .environments/, whose
console answers. Answered against composer-project, TYPO3 14.3.0, the
installation this repository writes below .fixtures/, whose console answers.
The tools that declare ``answeredBy`` carry an answer from each, under a
heading naming which; every other answer is from the first alone, because
nothing in it would differ. Nothing checks what is below this heading;
everything above it is derived from the class that answers the call, and
``bin/cli tools:check`` holds it.

services: by class
~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "PageRenderer"
    }

From the E-SITE-14.3 environment
""""""""""""""""""""""""""""""""

Text:

.. code-block:: text

    2 of the 1212 services this installation assembles match. What a constructor is handed is the id that really lands there, after autowiring.

    - TYPO3\CMS\Core\Page\PageRenderer (public) [typo3.singleton]
        0: TYPO3\CMS\Core\Context\Context
        1: cache.assets
        2: TYPO3\CMS\Core\Service\MarkerBasedTemplateService
        3: TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry
        4: TYPO3\CMS\Core\Page\AssetRenderer
        5: TYPO3\CMS\Core\Page\AssetCollector
        6: value
        7: TYPO3\CMS\Core\Localization\LanguageServiceFactory
        8: Psr\Http\Message\ResponseFactoryInterface
        9: Psr\Http\Message\StreamFactoryInterface
        10: TYPO3\CMS\Core\Imaging\IconRegistry_decorated_1
        11: TYPO3\CMS\Core\SystemResource\Publishing\SystemResourcePublisherInterface_decorated_1
        12: TYPO3\CMS\Core\SystemResource\SystemResourceFactory_decorated_1
        13: TYPO3\CMS\Core\Page\ResourceHashCollection
        14: TYPO3\CMS\Core\Security\ContentSecurityPolicy\DirectiveHashCollection_decorated_1
    - TYPO3\CMS\Fluid\ViewHelpers\Be\PageRendererViewHelper (public) [fluid.viewhelper]

Data:

.. code-block:: json

    {
        "query": "PageRenderer",
        "tag": null,
        "matchCount": 2,
        "answeredBy": "installation",
        "definitionCount": 1212,
        "aliasCount": 230,
        "services": [
            {
                "id": "TYPO3\\CMS\\Core\\Page\\PageRenderer",
                "class": "TYPO3\\CMS\\Core\\Page\\PageRenderer",
                "public": true,
                "shared": true,
                "autowired": true,
                "abstract": false,
                "synthetic": false,
                "tags": [
                    "typo3.singleton"
                ],
                "arguments": [
                    {
                        "position": 0,
                        "resolves": "TYPO3\\CMS\\Core\\Context\\Context"
                    },
                    {
                        "position": 1,
                        "resolves": "cache.assets"
                    },
                    {
                        "position": 2,
                        "resolves": "TYPO3\\CMS\\Core\\Service\\MarkerBasedTemplateService"
                    },
                    {
                        "position": 3,
                        "resolves": "TYPO3\\CMS\\Core\\MetaTag\\MetaTagManagerRegistry"
                    },
                    {
                        "position": 4,
                        "resolves": "TYPO3\\CMS\\Core\\Page\\AssetRenderer"
                    },
                    {
                        "position": 5,
                        "resolves": "TYPO3\\CMS\\Core\\Page\\AssetCollector"
                    },
                    {
                        "position": 6,
                        "resolves": "value"
                    },
                    {
                        "position": 7,
                        "resolves": "TYPO3\\CMS\\Core\\Localization\\LanguageServiceFactory"
                    },
                    {
                        "position": 8,
                        "resolves": "Psr\\Http\\Message\\ResponseFactoryInterface"
                    },
                    {
                        "position": 9,
                        "resolves": "Psr\\Http\\Message\\StreamFactoryInterface"
                    },
                    {
                        "position": 10,
                        "resolves": "TYPO3\\CMS\\Core\\Imaging\\IconRegistry_decorated_1"
                    },
                    {
                        "position": 11,
                        "resolves": "TYPO3\\CMS\\Core\\SystemResource\\Publishing\\SystemResourcePublisherInterface_decorated_1"
                    },
                    {
                        "position": 12,
                        "resolves": "TYPO3\\CMS\\Core\\SystemResource\\SystemResourceFactory_decorated_1"
                    },
                    {
                        "position": 13,
                        "resolves": "TYPO3\\CMS\\Core\\Page\\ResourceHashCollection"
                    },
                    {
                        "position": 14,
                        "resolves": "TYPO3\\CMS\\Core\\Security\\ContentSecurityPolicy\\DirectiveHashCollection_decorated_1"
                    }
                ]
            },
            {
                "id": "TYPO3\\CMS\\Fluid\\ViewHelpers\\Be\\PageRendererViewHelper",
                "class": "TYPO3\\CMS\\Fluid\\ViewHelpers\\Be\\PageRendererViewHelper",
                "public": true,
                "shared": false,
                "autowired": true,
                "abstract": false,
                "synthetic": false,
                "tags": [
                    "fluid.viewhelper"
                ],
                "arguments": []
            }
        ]
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: the installation booted and its container could not be assembled a second time: Error: Class "TYPO3\CMS\Core\Package\PackageManager" not found.

Data:

.. code-block:: json

    {
        "query": "PageRenderer",
        "tag": null,
        "unsupported": {
            "cause": "installation-not-answering",
            "reason": "the installation booted and its container could not be assembled a second time: Error: Class \"TYPO3\\CMS\\Core\\Package\\PackageManager\" not found",
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

services: by tag
~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "tag": "event.listener",
        "limit": 3
    }

From the E-SITE-14.3 environment
""""""""""""""""""""""""""""""""

Text:

.. code-block:: text

    65 of the 1212 services this installation assembles match, and the first 3 are here. What a constructor is handed is the id that really lands there, after autowiring.

    - TYPO3\CMS\Backend\EventListener\AfterBackendPageRenderEventListener (public) [event.listener]
        0: TYPO3\CMS\Core\Page\PageRenderer
    - TYPO3\CMS\Backend\EventListener\FailedLoginAttemptNotification (public) [event.listener]
        0: TYPO3\CMS\Core\Mail\TemplatedEmailFactory
        1: TYPO3\CMS\Core\Mail\Mailer
    - TYPO3\CMS\Backend\EventListener\FailedMfaAttemptNotification (public) [event.listener]
        0: TYPO3\CMS\Core\Mail\TemplatedEmailFactory
        1: TYPO3\CMS\Core\Mail\Mailer

Data:

.. code-block:: json

    {
        "query": null,
        "tag": "event.listener",
        "matchCount": 65,
        "answeredBy": "installation",
        "definitionCount": 1212,
        "aliasCount": 230,
        "services": [
            {
                "id": "TYPO3\\CMS\\Backend\\EventListener\\AfterBackendPageRenderEventListener",
                "class": "TYPO3\\CMS\\Backend\\EventListener\\AfterBackendPageRenderEventListener",
                "public": true,
                "shared": true,
                "autowired": true,
                "abstract": false,
                "synthetic": false,
                "tags": [
                    "event.listener"
                ],
                "arguments": [
                    {
                        "position": 0,
                        "resolves": "TYPO3\\CMS\\Core\\Page\\PageRenderer"
                    }
                ]
            },
            {
                "id": "TYPO3\\CMS\\Backend\\EventListener\\FailedLoginAttemptNotification",
                "class": "TYPO3\\CMS\\Backend\\EventListener\\FailedLoginAttemptNotification",
                "public": true,
                "shared": true,
                "autowired": true,
                "abstract": false,
                "synthetic": false,
                "tags": [
                    "event.listener"
                ],
                "arguments": [
                    {
                        "position": 0,
                        "resolves": "TYPO3\\CMS\\Core\\Mail\\TemplatedEmailFactory"
                    },
                    {
                        "position": 1,
                        "resolves": "TYPO3\\CMS\\Core\\Mail\\Mailer"
                    }
                ]
            },
            {
                "id": "TYPO3\\CMS\\Backend\\EventListener\\FailedMfaAttemptNotification",
                "class": "TYPO3\\CMS\\Backend\\EventListener\\FailedMfaAttemptNotification",
                "public": true,
                "shared": true,
                "autowired": true,
                "abstract": false,
                "synthetic": false,
                "tags": [
                    "event.listener"
                ],
                "arguments": [
                    {
                        "position": 0,
                        "resolves": "TYPO3\\CMS\\Core\\Mail\\TemplatedEmailFactory"
                    },
                    {
                        "position": 1,
                        "resolves": "TYPO3\\CMS\\Core\\Mail\\Mailer"
                    }
                ]
            }
        ]
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: the installation booted and its container could not be assembled a second time: Error: Class "TYPO3\CMS\Core\Package\PackageManager" not found.

Data:

.. code-block:: json

    {
        "query": null,
        "tag": "event.listener",
        "unsupported": {
            "cause": "installation-not-answering",
            "reason": "the installation booted and its container could not be assembled a second time: Error: Class \"TYPO3\\CMS\\Core\\Package\\PackageManager\" not found",
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
