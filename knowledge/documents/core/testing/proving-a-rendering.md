---
description: >-
  How a throwaway functional test produces a rendering, how you read the HTML it produced at all, and how you make it say which part of it changed.
whenToUse: >-
  When a finding turns on what a rendering contains and nothing in the checkout produces it, so the value is the unknown rather than the expectation. A TypoScript change whose diff does not say what it renders is one case. A PHP change to the frontend request pipeline, an error handler or a page renderer caller is the same one. To assert a response whose expected value you already know, use the frontend request hint instead.
hints:
  - core-tests
  - extension-test-frontend-request
---

# Proving What a Rendering Change Renders

What a rendering contains is not in the diff that changed it. Where no test
covers the constellation, nothing else in the checkout says it either. What
settles it is a throwaway functional test that renders one page and prints what
came out. A TypoScript change is one subject, and a PHP change to the request
pipeline is the same one. In both the value is the unknown rather than the
expectation.

This page is the harness. The probe exists to show what `lib.parseFunc_RTE` does
to a snippet, and nothing here states that.

## The Probe

The file goes below `typo3/sysext/frontend/Tests/Functional/Rendering/`, because
a file the runner does not collect proves nothing. One page row, one site
configuration and one `sys_template` row are the whole fixture.

```php
<?php
declare(strict_types=1);

namespace TYPO3\CMS\Frontend\Tests\Functional\Rendering;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Tests\Functional\SiteHandling\SiteBasedTestTrait;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class RenderingProbeTest extends FunctionalTestCase
{
    use SiteBasedTestTrait;

    protected const LANGUAGE_PRESETS = [
        'EN' => ['id' => 0, 'title' => 'English', 'locale' => 'en_US.UTF8'],
    ];

    #[Test]
    public function probe(): void
    {
        $connectionPool = $this->get(ConnectionPool::class);
        $connectionPool->getConnectionForTable('pages')->insert(
            'pages',
            ['uid' => 1, 'pid' => 0, 'title' => 'Root', 'slug' => '/', 'doktype' => 1, 'perms_everybody' => 15]
        );
        $this->writeSiteConfiguration(
            'test',
            $this->buildSiteConfiguration(1, '/'),
            [$this->buildDefaultLanguageConfiguration('EN', '/en/')]
        );
        $connectionPool->getConnectionForTable('sys_template')->insert(
            'sys_template',
            [
                'pid' => 1,
                'root' => 1,
                'clear' => 3,
                'config' => <<<EOT
page = PAGE
page.10 = TEXT
page.10.value (
<figure class="table">
<figcaption>A caption</figcaption>
</figure>
)
page.10.parseFunc =< lib.parseFunc_RTE
EOT,
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(1));
        echo "\n===PROBE===\n" . (string)$response->getBody() . "\n===END===\n";
        self::assertTrue(true);
    }
}
```

`TEXT` is the cObj that renders a snippet you hand it. It takes `value` and runs
the rest of its configuration as `stdWrap`. So `parseFunc` beside `value` puts
the snippet through the RTE setup. A `FLUIDTEMPLATE` around an `<f:format.html>`
reaches the same code through two more layers. A snippet written as entities
there never arrives at `parseFunc` at all.

## Putting the Snippet Into TypoScript

Markup that spans lines needs the multi-line form, `value ( … )`. A single-line
assignment ends at the newline, and the parser reads the lines after it as
TypoScript.

Three spellings differ by one space, and two of them are not an assignment:

- `value = <figure …>` assigns the markup as text. A leading `<` in a value is
  text, whatever it looks like.
- `value =<figure …>` is the reference operator. The parser reads what follows
  it as an identifier and stops at the first space. So the line becomes a
  reference to `figure`. Nothing fails: the page renders `< figure` where the
  markup should be.
- `value < lib.foo` is the copy operator.

In a `parseFunc` position both `parseFunc =< lib.parseFunc_RTE` and
`parseFunc = < lib.parseFunc_RTE` work, and they resolve at different times. The
parser resolves the first. `ContentObjectRenderer::mergeTSRef()` resolves the
second while the page renders. The core's own TypoScript writes the second.

## Reading What It Rendered

A functional test that passes prints nothing, and the rendered HTML is the
unknown the probe exists to see.

`echo` from the test body prints it. The core's functional PHPUnit configuration
does not set `beStrictAboutOutputDuringTests`. So PHPUnit neither swallows the
output nor marks the test risky. The output appears whether the probe passes or
fails.

A sentinel assertion — `self::assertSame('PROBE', $body)` — prints the whole
body as the failure diff. It is the fallback where something does swallow
output. It costs a red run whose failure means nothing.

## Saying Which Part of the Response Changed

A response that came out different says that a rendering changed and not where.
One marker per region says where, because each region lands in one place in the
document:

```
page.10 = TEXT
page.10.value = PROBE-BODY
page.headerData.10 = TEXT
page.headerData.10.value = <!-- PROBE-HEADERDATA -->
page.footerData.10 = TEXT
page.footerData.10.value = <!-- PROBE-FOOTERDATA -->
page.meta.description = PROBE-META
```

The title, the meta tags and `headerData` render inside `<head>`. The body tag
and the page content follow it, and `footerData` is the last thing before
`</body>`. A marker that is absent altogether is a region TYPO3 never rendered.
That is a different finding from one whose content moved.

## Printing What a Service Holds Mid-Request

Where the question is what a service held while the page rendered, a `USER`
object reads it inside the request. Its class goes in a `Fixtures/` directory
beside the probe. The core's `autoload-dev` already maps the test namespace
there, and the runner does not collect it as a test.

```
page.10 = USER
page.10.userFunc = TYPO3\CMS\Frontend\Tests\Functional\Rendering\Fixtures\StateProbe->render
```

```php
namespace TYPO3\CMS\Frontend\Tests\Functional\Rendering\Fixtures;

use TYPO3\CMS\Core\Attribute\AsAllowedCallable;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class StateProbe
{
    #[AsAllowedCallable]
    public function render(): string
    {
        $state = GeneralUtility::makeInstance(PageRenderer::class)->getState();
        fwrite(STDERR, "\n===STATE===\n" . strlen($state['bodyContent']) . "\n");

        return 'PROBE-BODY';
    }
}
```

`getState()` is `@internal`. A probe you delete at the end of the session may
read it, and production code may not.

Use `fwrite(STDERR, …)` rather than `echo`. The testing framework closes every
output buffer the sub-request opened before it hands the response back. So what
a content object echoed into one is gone. STDERR has no buffer and arrives
whether the probe passes or fails. `echo` from the test body still works,
because by then the request is over.

What such a probe reads is the state at that moment, which is rarely the state
the response shows. Every content object runs before TYPO3 hands the rendered
body to the page renderer. The renderer empties it again once it has assembled
the document. So a `bodyContent` of length 0 is a probe in the wrong moment, not
a body that never came out.

## Why the userFunc Carries an Attribute

**Since:** 14

TYPO3 checks a `userFunc` before it calls it. A method without
`#[AsAllowedCallable]` throws `AllowedCallableException` with code `1758626232`
instead of a rendering. The fixture is a class the core has no other reason to
trust. So the attribute is what makes the probe run at all.

## Where lib.parseFunc_RTE Comes From

**Since:** 13

`typo3/sysext/frontend/ext_localconf.php` registers `lib.parseFunc` and
`lib.parseFunc_RTE`. So both are in every functional test, and the probe needs
no extension and no static template.

## Where lib.parseFunc_RTE Comes From

**Until:** 12

`lib.parseFunc_RTE` lives in `fluid_styled_content`'s static TypoScript and in
nothing the frontend loads by itself. Without it the request dies with
`LogicException: Invoked ContentObjectRenderer::parseFunc without any configuration`,
code `1641989097`.

Two things bring it in, and you need both. One is
`protected array $coreExtensionsToLoad = ['fluid_styled_content'];` on the test
class. The other is
`'include_static_file' => 'EXT:fluid_styled_content/Configuration/TypoScript/'`
in the `sys_template` row. Either one alone leaves the same exception.

## Running It

```bash
CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- typo3/sysext/frontend/Tests/Functional/Rendering/RenderingProbeTest.php
```

sqlite is the default and the fastest, which makes several rounds affordable.
`typo3_script_lookup` has the rest of the options. Name the file after `--`,
because you run a probe again after every change to it.

## Removing the Probe

Delete the test and any fixture it needed when you have the answer. Confirm with
`git status` that the checkout is clean. What the probe established goes into
the review or the issue. The probe itself asserts nothing, and it is evidence of
nothing once somebody commits it.
