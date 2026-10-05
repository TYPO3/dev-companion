<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Http\Recent;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * A core issue drafted and handed over as the tracker's own form, filled in —
 * `D-ANS-171`.
 */
#[Decision('D-ANS-171')]
final class IssueReportTest extends TestCase
{
    #[After]
    public function forgetTheTracker(): void
    {
        Recent::forget();
        Forge::useReader(null);
    }

    #[Test]
    public function theDraftFillsEveryFieldItWasGivenAndTagsItself(): void
    {
        $this->tracker();

        $draft = Registry::call('typo3_issue_report_guide', [
            'subject' => 'Styleguide generator hashes every demo password again',
            'problem' => 'styleguide:generate -c tca takes 13 seconds.',
            'stepsToReproduce' => ['Install the styleguide', 'Run @typo3 styleguide:generate -c tca@'],
            'suggestedFix' => "-        \$hash = \$hasher->getHashedPassword(\$password);\n+        \$hash = \$this->hash;",
            'typo3Version' => '14.3.5',
            'phpVersion' => '8.4.12',
            'category' => 'styleguide',
            'priority' => 'Could have',
            'fixedVersion' => 'next-patchlevel',
            'isRegression' => true,
            'complexity' => 'easy',
            'tags' => ['performance'],
            'parentIssue' => 100,
            'searchFor' => 'styleguide password',
        ])->data;

        self::assertSame(
            "h2. Problem\n\nstyleguide:generate -c tca takes 13 seconds.\n\nh2. Steps to reproduce\n\n"
                . "# Install the styleguide\n# Run @typo3 styleguide:generate -c tca@\n\nh2. Suggested fix\n\n"
                . "<pre><code class=\"diff\">\n-        \$hash = \$hasher->getHashedPassword(\$password);\n"
                . "+        \$hash = \$this->hash;\n</code></pre>",
            $draft['description'],
        );
        self::assertSame([
            'Tracker' => 'Bug',
            'Subject' => 'Styleguide generator hashes every demo password again',
            'Priority' => 'Could have',
            'Category' => 'Styleguide',
            'Target version' => 'next-patchlevel',
            'TYPO3 Version' => '14',
            'PHP Version' => '8.4',
            'Complexity' => 'easy',
            'Is Regression' => 'Yes',
            'Tags' => 'performance, dev-companion',
            'Parent task' => '#100',
        ], array_column($draft['form'], 'value', 'field'));

        parse_str((string) parse_url($draft['url'], PHP_URL_QUERY), $query);
        self::assertStringStartsWith('https://forge.typo3.org/projects/typo3cms-core/issues/new?', $draft['url']);
        self::assertSame([
            'tracker_id' => '1',
            'subject' => 'Styleguide generator hashes every demo password again',
            'priority_id' => '5',
            'category_id' => '77',
            'fixed_version_id' => '2354',
            'custom_field_values' => ['4' => '14', '5' => '8.4', '8' => 'easy', '15' => '1', '3' => 'performance, dev-companion'],
            'parent_issue_id' => '100',
            'description' => $draft['description'],
        ], $query['issue'] ?? null);
        self::assertTrue($draft['descriptionInLink']);

        self::assertSame([105403], array_column($draft['possibleDuplicates'], 'issue'));
        self::assertContains('possible-duplicates', array_column($draft['checks'], 'code'));
        self::assertContains('fixed-version-set', array_column($draft['checks'], 'code'));
        self::assertContains('typo3-version-shortened', array_column($draft['checks'], 'code'));
        self::assertNotContains('bare-diff', array_column($draft['checks'], 'code'), 'a diff inside <pre> is no finding');
    }

    #[Test]
    public function whatTheTrackerWouldRenderWrongIsACheck(): void
    {
        $this->tracker();

        $draft = Registry::call('typo3_issue_report_guide', [
            'subject' => 'Something',
            'description' => "## Problem\n\nThe `Foo::bar()` call fails.\n\n```php\nfoo();\n```\n\n1. one\n2. two",
        ])->data;

        $codes = array_column($draft['checks'], 'code');
        foreach (['markdown-fence', 'markdown-code', 'markdown-heading', 'numbered-lines', 'missing-typo3-version'] as $code) {
            self::assertContains($code, $codes);
        }
        self::assertSame('Tags', $draft['form'][2]['field']);
        self::assertSame('dev-companion', $draft['form'][2]['value'], 'a draft without tags still carries its own');
    }

    #[Test]
    public function aValueTheProjectDoesNotHaveStaysOutOfTheForm(): void
    {
        $this->tracker();

        $draft = Registry::call('typo3_issue_report_guide', [
            'subject' => 'Something',
            'problem' => 'It breaks.',
            'typo3Version' => '14',
            'category' => 'No such area',
            'fixedVersion' => '11.5',
        ])->data;

        self::assertNotContains('Category', array_column($draft['form'], 'field'));
        self::assertNotContains('Target version', array_column($draft['form'], 'field'));
        self::assertStringNotContainsString('category_id', urldecode($draft['url']));
        self::assertContains('unknown-category', array_column($draft['checks'], 'code'));
        self::assertContains('unknown-fixed-version', array_column($draft['checks'], 'code'));
    }

    /**
     * A login link past what the tracker answers is a form that never opens.
     * So the link fills the rest and says what to paste.
     */
    #[Test]
    public function aDescriptionTooLongForTheLinkIsLeftToPaste(): void
    {
        $this->tracker();

        $draft = Registry::call('typo3_issue_report_guide', [
            'subject' => 'Something',
            'problem' => str_repeat('The @Foo::bar()@ call fails & returns [nothing].' . "\n", 200),
            'typo3Version' => '14',
        ])->data;

        self::assertFalse($draft['descriptionInLink']);
        self::assertStringNotContainsString('description', urldecode($draft['url']));
        self::assertStringContainsString('subject', urldecode($draft['url']));
        self::assertContains('description-not-in-link', array_column($draft['checks'], 'code'));
    }

    /** The tracker as it answers these reads, in the shapes measured on 2026-10-05. */
    private function tracker(): void
    {
        Forge::useReader(static function (string $url): string {
            if (str_contains($url, '/search.json')) {
                return (string) json_encode([
                    'results' => [[
                        'id' => 105403,
                        'title' => 'Bug #105403 (New): Styleguide hashes passwords slowly',
                        'url' => 'https://forge.typo3.org/issues/105403',
                    ]],
                    'total_count' => 1,
                ]);
            }
            if (str_contains($url, 'include=issue_categories')) {
                return (string) json_encode(['project' => ['issue_categories' => [['id' => 77, 'name' => 'Styleguide']]]]);
            }
            if (str_contains($url, '/versions.json')) {
                return (string) json_encode(['versions' => [
                    ['id' => 2354, 'name' => 'next-patchlevel', 'status' => 'open'],
                    ['id' => 100, 'name' => '11.5', 'status' => 'closed'],
                ], 'total_count' => 2]);
            }

            return (string) json_encode(['issues' => [], 'total_count' => 0]);
        });
    }
}
