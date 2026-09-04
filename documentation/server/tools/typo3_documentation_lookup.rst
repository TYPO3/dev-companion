.. _typo3_documentation_lookup:

``typo3_documentation_lookup``
==============================

Search or read the official live TYPO3 documentation for a covered TYPO3 line.
Four manuals are searched: TYPO3 Explained, TypoScript Explained, the TCA
Reference and the Fluid ViewHelper Reference. Search with several short English
queries; every result carries a canonical URL. Pass one of those URLs back as
page with the same targetVersion to receive that page as text, including
headings and code examples. A query naming a Fluid tag such as f:if is answered
from the ViewHelper reference alone; ask without the prefix for the other
manuals' Fluid chapters. This reaches docs.typo3.org, unlike the bundled
convention lookups. Answers from: network.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`network <answer-sources-network>`.

Takes
-----

.. code-block:: yaml

    # Short search queries in English. Pass alternatives separately, for example
    # ["page title event", "page title provider"]. A call carries queries or page,
    # never both.
    queries: [string]  # optional
    # Canonical page URL returned by an earlier search, read as text. Pass it with
    # the same targetVersion. A call carries queries or page, never both.
    page: string  # optional
    # Covered TYPO3 version whose official manual must answer, for example "13.4" or
    # "14". There is no fallback to another release.
    targetVersion: string
    # How many pages come back per query.
    limit: integer  # optional

The call carries exactly one of these sets of arguments: ``queries`` — or
``page``.

Answers with
------------

.. code-block:: yaml

    # One of: search, page.
    mode: string
    # One of: answered, empty, unavailable.
    status: string
    # The exact documentation release searched.
    targetVersion: string
    # The external documentation host.
    source: string
    queries: [string]
    # Present on a miss where a query is shaped like a PHP identifier. This index is
    # page titles, section paths and the property names each manual declares, so a
    # class or method name has no page to be titled after, while the property or
    # ViewHelper it belongs to does.
    insteadOf:  # optional
      - # The query that reads as a code identifier.
        query: string
        # The bare names to ask with instead, most specific first.
        ask: [string]
    results:
      - title: string
        # Canonical URL of the matching documentation page.
        url: string
        # Official document identifier.
        document: string
        documentTitle: string
        documentVersion: string
        section: string
        # Short route into the source, empty only when the result page could not be
        # read after its index matched.
        excerpt: string
        # The selected page as text in page mode; empty in search mode.
        content: string
        # Share of the query's weight this page carries, 0 to 1, for the query it is
        # returned for. Below 0.5 the page carries some words of the question and
        # not its subject, and the answer says so above the results — it is
        # returned anyway, because over a table of contents the page that answers a
        # three-word question covers about a third of it. Null in page mode, where
        # nothing was searched for.
        coverage: number or null
        # What this page was matched on. Every query word missing from it reached
        # this page nowhere, so a result whose match is made of the words around the
        # subject is an aimed answer rather than one about the subject; ask again
        # with the subject alone. Empty in page mode.
        matched:
          - # The query word, reduced to the stem that was searched for.
            term: string
            # One of: title, path, manual. Where it was found: the page title, the
            # section path it sits in, or the name of the manual.
            field: string
    # Why nothing was answered, where status says unavailable. Null otherwise.
    unavailable:
      # One of: version-not-covered, source-not-answering. version-not-covered: the
      # release asked about is outside the ones this server knows the manuals for,
      # and asking again changes nothing. source-not-answering: docs.typo3.org did
      # not answer this time, and the same call may answer the next.
      cause: string
      reason: string

Answered
--------

Recorded on 2026-08-26 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 15.0.0-dev, the main core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed
— vendor/autoload.php is not there either, and composer install writes both.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

documentation: search
~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "queries": [
            "page title event",
            "page title provider"
        ],
        "targetVersion": "14.3",
        "limit": 3
    }

Text:

.. code-block:: text

    Official TYPO3 documentation for 14.3.
    Source: https://docs.typo3.org
    Matched against page titles and section paths, never the text of a page.

    ## Page title API
    typo3/reference-coreapi · 14.3 · https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Seo/PageTitleApi.html
    Matched on: page (title), title (title) — covers 86% of the query.
    In order to keep setting the page titles in control, you can use the page title API. The API uses page title providers to define the page title based on page record and the content on the page. Based on the priority of the providers, the \TYPO3\CMS\Core\PageTitle\PageTitleProviderManager will check the providers if a title is given by the provider. Besides the providers shipped by the Core, you can add own providers. An integrator can define the priority of the providers for his project. New in version 14.0

    ## ModuleProvider
    typo3/reference-coreapi · 14.3 · https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Backend/BackendModules/ModuleProviderAPI.html
    Matched on: provid (title) — covers 36% of the query.
    The ModuleProvider API allows extension authors to work with the registered modules. This API is the central point to retrieve modules, since it automatically performs necessary access checks and prepares specific structures, for example for the use in menus. This is the central point to retrieve modules from the ModuleRegistry, while performing the necessary access checks, which ModuleRegistry does not deal with. Simple wrapper for the registry, which just checks if a module is registered. Does NOT perform any access checks.

    ## Page.title ViewHelper <f:page.title>
    typo3/view-helper-reference · 14.3 · https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/Global/Page/Title.html
    Matched on: page (title), title (title) — covers 47% of the query.
    New in version 14.0 The ViewHelper allows setting the page title directly from Fluid templates. This is especially useful for Extbase plugins that need to set a page title in their detail views without having to implement their own custom page title provider. The ViewHelper can also be used with static content: Go to the source code of this ViewHelper: Page\TitleViewHelper.php (GitHub). The ViewHelper integrates seamlessly with TYPO3's existing page title provider system and respects the configured provider priorities.

Data:

.. code-block:: json

    {
        "mode": "search",
        "status": "answered",
        "targetVersion": "14.3",
        "source": "https://docs.typo3.org",
        "queries": [
            "page title event",
            "page title provider"
        ],
        "results": [
            {
                "title": "Page title API",
                "url": "https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Seo/PageTitleApi.html",
                "document": "typo3/reference-coreapi",
                "documentTitle": "TYPO3 Explained",
                "documentVersion": "14.3",
                "section": "Page title API",
                "excerpt": "In order to keep setting the page titles in control, you can use the page title API. The API uses page title providers to define the page title based on page record and the content on the page. Based on the priority of the providers, the \\TYPO3\\CMS\\Core\\PageTitle\\PageTitleProviderManager will check the providers if a title is given by the provider. Besides the providers shipped by the Core, you can add own providers. An integrator can define the priority of the providers for his project. New in version 14.0",
                "content": "",
                "coverage": 0.861,
                "matched": [
                    {
                        "term": "page",
                        "field": "title"
                    },
                    {
                        "term": "title",
                        "field": "title"
                    }
                ]
            },
            {
                "title": "ModuleProvider",
                "url": "https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Backend/BackendModules/ModuleProviderAPI.html",
                "document": "typo3/reference-coreapi",
                "documentTitle": "TYPO3 Explained",
                "documentVersion": "14.3",
                "section": "ModuleProvider",
                "excerpt": "The ModuleProvider API allows extension authors to work with the registered modules. This API is the central point to retrieve modules, since it automatically performs necessary access checks and prepares specific structures, for example for the use in menus. This is the central point to retrieve modules from the ModuleRegistry, while performing the necessary access checks, which ModuleRegistry does not deal with. Simple wrapper for the registry, which just checks if a module is registered. Does NOT perform any access checks.",
                "content": "",
                "coverage": 0.361,
                "matched": [
                    {
                        "term": "provid",
                        "field": "title"
                    }
                ]
            },
            {
                "title": "Page.title ViewHelper <f:page.title>",
                "url": "https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/Global/Page/Title.html",
                "document": "typo3/view-helper-reference",
                "documentTitle": "Fluid ViewHelper Reference",
                "documentVersion": "14.3",
                "section": "Page.title ViewHelper <f:page.title>",
                "excerpt": "New in version 14.0 The ViewHelper allows setting the page title directly from Fluid templates. This is especially useful for Extbase plugins that need to set a page title in their detail views without having to implement their own custom page title provider. The ViewHelper can also be used with static content: Go to the source code of this ViewHelper: Page\\TitleViewHelper.php (GitHub). The ViewHelper integrates seamlessly with TYPO3's existing page title provider system and respects the configured provider priorities.",
                "content": "",
                "coverage": 0.466,
                "matched": [
                    {
                        "term": "page",
                        "field": "title"
                    },
                    {
                        "term": "title",
                        "field": "title"
                    }
                ]
            }
        ],
        "unavailable": null
    }

documentation: page
~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "page": "https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Seo/PageTitleApi.html",
        "targetVersion": "14.3"
    }

Text:

.. code-block:: text

    Official TYPO3 documentation for 14.3.
    Source: https://docs.typo3.org

    ## Page title API
    typo3/reference-coreapi · 14.3 · https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Seo/PageTitleApi.html

    # Page title API

    In order to keep setting the page titles in control, you can use the page title API. The API uses page title providers to define the page title based on page record and the content on the page.

    Based on the priority of the providers, the \TYPO3\CMS\Core\PageTitle\PageTitleProviderManager will check the providers if a title is given by the provider.

    Besides the providers shipped by the Core, you can add own providers. An integrator can define the priority of the providers for his project.

    New in version 14.0

    The page title can also be set via the Page.title ViewHelper <f:page.title>.

    See also

    The page title is further influenced by Properties of 'config' and websiteTitle.

    Table of contents

    - List of page title providers shipped by the Core SeoTitlePageTitleProvider RecordTitleProvider RecordPageTitleProvider

    - Create your own page title provider Example: set the page title from your extension's controller Example: use values from the site configuration in the page title

    - Define the priority of PageTitleProviders

    ## List of page title providers shipped by the Core

    The TYPO3 Core ships the following page title providers by default, listed from highest to lowest priority.

    ### SeoTitlePageTitleProvider

    System extension typo3/cms-seo ships the \TYPO3\CMS\Seo\PageTitle\SeoTitlePageTitleProvider . It is only available if the extension is installed. It has the identifier seo.

    When an editor has set a value for the SEO title in the page properties of the page, this provider will provide that title.

    If you have not installed the SEO system extension, the field and provider are not available.

    ### RecordTitleProvider

    New in version 14.0

    The fallback provider with the lowest priority is the \TYPO3\CMS\Core\PageTitle\RecordTitleProvider . It has the identifier recordTitle.

    This provider can be used by third-party extensions to set the page title.

    ```
    <?php

    declare(strict_types=1);

    namespace MyVendor\MyExtension\Controller;

    use MyVendor\MyExtension\Domain\Model\Item;
    use Psr\Http\Message\ResponseInterface;
    use TYPO3\CMS\Core\PageTitle\RecordTitleProvider;
    use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

    final class ItemController extends ActionController
    {
        public function __construct(
            private readonly RecordTitleProvider $recordTitleProvider,
        ) {}

        public function showAction(Item $item): ResponseInterface
        {
            $this->recordTitleProvider->setTitle($item->getTitle());
            $this->view->assign('item', $item);
            return $this->htmlResponse();
        }
    }
    ```

    ### RecordPageTitleProvider

    The fallback provider with the lowest priority is the \TYPO3\CMS\Core\PageTitle\RecordPageTitleProvider . It has the identifier record.

    When no other title is set by a provider, this provider will return the title of the page as defined in the page properties.

    ## Create your own page title provider

    Extension developers may want to have an own provider for page titles. For example, if you have an extension with records and a detail view, the title of the page record will not be the correct title. To make sure to display the correct page title, you have to create your own page title provider. It is quite easy to create one.

    New in version 14.0

    In many use cases, the provider RecordTitleProvider can be used instead of writing a custom page title provider.

    ### Example: set the page title from your extension's controller

    First, create a PHP class in your extension that implements the \TYPO3\CMS\Core\PageTitle\PageTitleProviderInterface , for example by extending \TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider . Within this method you can create your own logic to define the correct title.

    ```
    <?php

    declare(strict_types=1);

    namespace MyVendor\MySitepackage\PageTitle;

    use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

    final class MyOwnPageTitleProvider extends AbstractPageTitleProvider
    {
        public function setTitle(string $title): void
        {
            $this->title = $title;
        }
    }
    ```

    Usage example in an Extbase controller:

    ```
    <?php

    use MyVendor\MySitepackage\PageTitle\MyOwnPageTitleProvider;
    use Psr\Http\Message\ResponseInterface;
    use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

    final class SomeController extends ActionController
    {
        public function __construct(
            private readonly MyOwnPageTitleProvider $titleProvider,
        ) {}

        public function someAction(): ResponseInterface
        {
            $this->titleProvider->setTitle('Title from controller action');
            // do something
            return $this->htmlResponse();
        }
    }
    ```

    Configure the new page title provider in your TypoScript setup:

    ```
    config {
      pageTitleProviders {
        sitepackage {
          provider = MyVendor\MySitepackage\PageTitle\MyOwnPageTitleProvider
          before = record
        }
      }
    }
    ```

    ### Example: use values from the site configuration in the page title

    If you want to use data from the site configuration, for example the site title, you can implement a page title provider as follows:

    ```
    <?php

    declare(strict_types=1);

    namespace MyVendor\MySitepackage\PageTitle;

    use Psr\Http\Message\ServerRequestInterface;
    use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
    use TYPO3\CMS\Core\PageTitle\PageTitleProviderInterface;
    use TYPO3\CMS\Core\Site\SiteFinder;
    use TYPO3\CMS\Frontend\Page\PageInformation;

    #[Autoconfigure(public: true)]
    final readonly class WebsiteTitleProvider implements PageTitleProviderInterface
    {
        private ServerRequestInterface $request;

        public function __construct(
            private SiteFinder $siteFinder,
        ) {}

        public function getTitle(): string
        {
            $site = $this->siteFinder->getSiteByPageId($this->getPageInformation()->getId());
            $titles = [
                $this->getPageInformation()->getPageRecord()['title'] ?? '',
                $site->getAttribute('websiteTitle'),
            ];

            return implode(' - ', $titles);
        }

        public function setRequest(ServerRequestInterface $request): void
        {
            $this->request = $request;
        }

        private function getPageInformation(): PageInformation
        {
            $pageInformation = $this->request->getAttribute('frontend.page.information');
            if (!$pageInformation instanceof PageInformation) {
                throw new \Exception('Current frontend page information not available', 1730098625);
            }
            return $pageInformation;
        }
    }
    ```

    The class must be set to public, because we inject the class SiteFinder as dependency.

    Then flush the cache in System > Maintenance > Flush TYPO3 and PHP Cache.

    Configure the new page title provider to be used in your TypoScript setup:

    ```
    config {
      pageTitleProviders {
        sitepackage {
          provider = MyVendor\MySitepackage\PageTitle\WebsiteTitleProvider
          before = record
          after = seo
        }
      }
    }
    ```

    The registered page title providers are called after each other in the configured order. The first provider that returns a non-empty value is used, the providers later in the order are ignored.

    Therefore our custom provider should be loaded before record, the default provider which always returns a value. If the system extension typo3/cms-seo is loaded the default SEO Title has a particular format, you can change this by loading your custom provider before seo.

    ## Define the priority of PageTitleProviders

    The priority of the providers is set by the TypoScript property config.pageTitleProviders. This way an integrator is able to set the priorities for their project and can even have conditions in place.

    By default, the Core has the following setup:

    ```
    config.pageTitleProviders {
      record.provider = TYPO3\CMS\Core\PageTitle\RecordPageTitleProvider
      recordTitle {
        provider = TYPO3\CMS\Core\PageTitle\RecordTitleProvider
        before = record
      }
    }
    ```

    The sorting of the providers is based on the before and after parameters. If you want a provider to be handled before a specific other provider, just set that provider in the before , do the same with after .

    For example, if you want the RecordTitleProvider to take priority over the SeoTitlePageTitleProvider you can change the order via TypoScript:

    ```
    config.pageTitleProviders {
      recordTitle {
        before = seo
      }
    }
    ```

    First the SeoTitlePageTitleProvider (because it will be handled before record ) and, if this providers did not provide a title, the RecordPageTitleProvider will be checked.

    You can override these settings within your own installation. You can add as many providers as you want. Be aware that if a provider returns a non-empty value, all provider with a lower priority will not be checked.

Data:

.. code-block:: json

    {
        "mode": "page",
        "status": "answered",
        "targetVersion": "14.3",
        "source": "https://docs.typo3.org",
        "queries": [],
        "results": [
            {
                "title": "Page title API",
                "url": "https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Seo/PageTitleApi.html",
                "document": "typo3/reference-coreapi",
                "documentTitle": "TYPO3 Explained",
                "documentVersion": "14.3",
                "section": "Page title API",
                "excerpt": "# Page title API\n\nIn order to keep setting the page titles in control, you can use the page title API. The API uses page title providers to define the page title based on page record and the content on the page.\n\nBased on the priority of the providers, the \\TYPO3\\CMS\\Core\\PageTitle\\PageTitleProviderManager will check the providers if a title is given by the provider.\n\nBesides the providers shipped by the Core, you can add own providers. An integrator can define the priority of the providers for his project.\n\nNew in version 14.0\n\nThe page title can also be set via the Page.title ViewHelper <f:page.title>.\n\nSee also\n\nThe page title is further influenced by Properties of 'config' and websiteTit",
                "content": "# Page title API\n\nIn order to keep setting the page titles in control, you can use the page title API. The API uses page title providers to define the page title based on page record and the content on the page.\n\nBased on the priority of the providers, the \\TYPO3\\CMS\\Core\\PageTitle\\PageTitleProviderManager will check the providers if a title is given by the provider.\n\nBesides the providers shipped by the Core, you can add own providers. An integrator can define the priority of the providers for his project.\n\nNew in version 14.0\n\nThe page title can also be set via the Page.title ViewHelper <f:page.title>.\n\nSee also\n\nThe page title is further influenced by Properties of 'config' and websiteTitle.\n\nTable of contents\n\n- List of page title providers shipped by the Core SeoTitlePageTitleProvider RecordTitleProvider RecordPageTitleProvider\n\n- Create your own page title provider Example: set the page title from your extension's controller Example: use values from the site configuration in the page title\n\n- Define the priority of PageTitleProviders\n\n## List of page title providers shipped by the Core\n\nThe TYPO3 Core ships the following page title providers by default, listed from highest to lowest priority.\n\n### SeoTitlePageTitleProvider\n\nSystem extension typo3/cms-seo ships the \\TYPO3\\CMS\\Seo\\PageTitle\\SeoTitlePageTitleProvider . It is only available if the extension is installed. It has the identifier seo.\n\nWhen an editor has set a value for the SEO title in the page properties of the page, this provider will provide that title.\n\nIf you have not installed the SEO system extension, the field and provider are not available.\n\n### RecordTitleProvider\n\nNew in version 14.0\n\nThe fallback provider with the lowest priority is the \\TYPO3\\CMS\\Core\\PageTitle\\RecordTitleProvider . It has the identifier recordTitle.\n\nThis provider can be used by third-party extensions to set the page title.\n\n```\n<?php\n\ndeclare(strict_types=1);\n\nnamespace MyVendor\\MyExtension\\Controller;\n\nuse MyVendor\\MyExtension\\Domain\\Model\\Item;\nuse Psr\\Http\\Message\\ResponseInterface;\nuse TYPO3\\CMS\\Core\\PageTitle\\RecordTitleProvider;\nuse TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController;\n\nfinal class ItemController extends ActionController\n{\n    public function __construct(\n        private readonly RecordTitleProvider $recordTitleProvider,\n    ) {}\n\n    public function showAction(Item $item): ResponseInterface\n    {\n        $this->recordTitleProvider->setTitle($item->getTitle());\n        $this->view->assign('item', $item);\n        return $this->htmlResponse();\n    }\n}\n```\n\n### RecordPageTitleProvider\n\nThe fallback provider with the lowest priority is the \\TYPO3\\CMS\\Core\\PageTitle\\RecordPageTitleProvider . It has the identifier record.\n\nWhen no other title is set by a provider, this provider will return the title of the page as defined in the page properties.\n\n## Create your own page title provider\n\nExtension developers may want to have an own provider for page titles. For example, if you have an extension with records and a detail view, the title of the page record will not be the correct title. To make sure to display the correct page title, you have to create your own page title provider. It is quite easy to create one.\n\nNew in version 14.0\n\nIn many use cases, the provider RecordTitleProvider can be used instead of writing a custom page title provider.\n\n### Example: set the page title from your extension's controller\n\nFirst, create a PHP class in your extension that implements the \\TYPO3\\CMS\\Core\\PageTitle\\PageTitleProviderInterface , for example by extending \\TYPO3\\CMS\\Core\\PageTitle\\AbstractPageTitleProvider . Within this method you can create your own logic to define the correct title.\n\n```\n<?php\n\ndeclare(strict_types=1);\n\nnamespace MyVendor\\MySitepackage\\PageTitle;\n\nuse TYPO3\\CMS\\Core\\PageTitle\\AbstractPageTitleProvider;\n\nfinal class MyOwnPageTitleProvider extends AbstractPageTitleProvider\n{\n    public function setTitle(string $title): void\n    {\n        $this->title = $title;\n    }\n}\n```\n\nUsage example in an Extbase controller:\n\n```\n<?php\n\nuse MyVendor\\MySitepackage\\PageTitle\\MyOwnPageTitleProvider;\nuse Psr\\Http\\Message\\ResponseInterface;\nuse TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController;\n\nfinal class SomeController extends ActionController\n{\n    public function __construct(\n        private readonly MyOwnPageTitleProvider $titleProvider,\n    ) {}\n\n    public function someAction(): ResponseInterface\n    {\n        $this->titleProvider->setTitle('Title from controller action');\n        // do something\n        return $this->htmlResponse();\n    }\n}\n```\n\nConfigure the new page title provider in your TypoScript setup:\n\n```\nconfig {\n  pageTitleProviders {\n    sitepackage {\n      provider = MyVendor\\MySitepackage\\PageTitle\\MyOwnPageTitleProvider\n      before = record\n    }\n  }\n}\n```\n\n### Example: use values from the site configuration in the page title\n\nIf you want to use data from the site configuration, for example the site title, you can implement a page title provider as follows:\n\n```\n<?php\n\ndeclare(strict_types=1);\n\nnamespace MyVendor\\MySitepackage\\PageTitle;\n\nuse Psr\\Http\\Message\\ServerRequestInterface;\nuse Symfony\\Component\\DependencyInjection\\Attribute\\Autoconfigure;\nuse TYPO3\\CMS\\Core\\PageTitle\\PageTitleProviderInterface;\nuse TYPO3\\CMS\\Core\\Site\\SiteFinder;\nuse TYPO3\\CMS\\Frontend\\Page\\PageInformation;\n\n#[Autoconfigure(public: true)]\nfinal readonly class WebsiteTitleProvider implements PageTitleProviderInterface\n{\n    private ServerRequestInterface $request;\n\n    public function __construct(\n        private SiteFinder $siteFinder,\n    ) {}\n\n    public function getTitle(): string\n    {\n        $site = $this->siteFinder->getSiteByPageId($this->getPageInformation()->getId());\n        $titles = [\n            $this->getPageInformation()->getPageRecord()['title'] ?? '',\n            $site->getAttribute('websiteTitle'),\n        ];\n\n        return implode(' - ', $titles);\n    }\n\n    public function setRequest(ServerRequestInterface $request): void\n    {\n        $this->request = $request;\n    }\n\n    private function getPageInformation(): PageInformation\n    {\n        $pageInformation = $this->request->getAttribute('frontend.page.information');\n        if (!$pageInformation instanceof PageInformation) {\n            throw new \\Exception('Current frontend page information not available', 1730098625);\n        }\n        return $pageInformation;\n    }\n}\n```\n\nThe class must be set to public, because we inject the class SiteFinder as dependency.\n\nThen flush the cache in System > Maintenance > Flush TYPO3 and PHP Cache.\n\nConfigure the new page title provider to be used in your TypoScript setup:\n\n```\nconfig {\n  pageTitleProviders {\n    sitepackage {\n      provider = MyVendor\\MySitepackage\\PageTitle\\WebsiteTitleProvider\n      before = record\n      after = seo\n    }\n  }\n}\n```\n\nThe registered page title providers are called after each other in the configured order. The first provider that returns a non-empty value is used, the providers later in the order are ignored.\n\nTherefore our custom provider should be loaded before record, the default provider which always returns a value. If the system extension typo3/cms-seo is loaded the default SEO Title has a particular format, you can change this by loading your custom provider before seo.\n\n## Define the priority of PageTitleProviders\n\nThe priority of the providers is set by the TypoScript property config.pageTitleProviders. This way an integrator is able to set the priorities for their project and can even have conditions in place.\n\nBy default, the Core has the following setup:\n\n```\nconfig.pageTitleProviders {\n  record.provider = TYPO3\\CMS\\Core\\PageTitle\\RecordPageTitleProvider\n  recordTitle {\n    provider = TYPO3\\CMS\\Core\\PageTitle\\RecordTitleProvider\n    before = record\n  }\n}\n```\n\nThe sorting of the providers is based on the before and after parameters. If you want a provider to be handled before a specific other provider, just set that provider in the before , do the same with after .\n\nFor example, if you want the RecordTitleProvider to take priority over the SeoTitlePageTitleProvider you can change the order via TypoScript:\n\n```\nconfig.pageTitleProviders {\n  recordTitle {\n    before = seo\n  }\n}\n```\n\nFirst the SeoTitlePageTitleProvider (because it will be handled before record ) and, if this providers did not provide a title, the RecordPageTitleProvider will be checked.\n\nYou can override these settings within your own installation. You can add as many providers as you want. Be aware that if a provider returns a non-empty value, all provider with a lower priority will not be checked.",
                "coverage": null,
                "matched": []
            }
        ],
        "unavailable": null
    }

documentation: unsupported version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "queries": [
            "page title event"
        ],
        "targetVersion": "999"
    }

Text:

.. code-block:: text

    Official TYPO3 documentation for 999.
    Source: https://docs.typo3.org
    Could not answer: TYPO3 999 is outside the covered versions: 12.4, 13.4, 14.3, main.

Data:

.. code-block:: json

    {
        "mode": "search",
        "status": "unavailable",
        "targetVersion": "999",
        "source": "https://docs.typo3.org",
        "queries": [
            "page title event"
        ],
        "results": [],
        "unavailable": {
            "cause": "version-not-covered",
            "reason": "TYPO3 999 is outside the covered versions: 12.4, 13.4, 14.3, main."
        }
    }
