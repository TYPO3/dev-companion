<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

/**
 * A new issue for the TYPO3 Core project on forge.typo3.org, drafted and
 * handed over as the form, filled in.
 *
 * Nothing here files anything. The link opens the tracker's own form with every
 * field this draft could fill, and the person who submits it is the one with an
 * account. Redmine fills its new-issue form from `issue[...]` parameters, and
 * the login it sends an anonymous reader through keeps them, `D-ANS-171`.
 */
final class IssueReport
{
    public const NEW_ISSUE = Forge::HOST . '/projects/' . Forge::PROJECT . '/issues/new';

    /** The trackers the core project offers, as `/projects/typo3cms-core.json` named them on 2026-10-05. */
    public const TRACKERS = ['Bug' => 1, 'Feature' => 2, 'Task' => 4, 'Story' => 6, 'Epic' => 10];

    /** `/enumerations/issue_priorities.json` on 2026-10-05, `Should have` the default the form preselects. */
    public const PRIORITIES = ['Must have' => 3, 'Should have' => 4, 'Could have' => 5, "Won't have this time" => 6];

    public const COMPLEXITIES = ['trivial', 'easy', 'medium', 'hard'];

    /**
     * Every draft carries it, so the issues this server drafted are one filter
     * on the tracker. The maintainer asked for the tag on 2026-10-05, and for
     * no line in the description.
     */
    public const TAG = 'dev-companion';

    /** The custom field ids a filed core issue carried on 2026-10-05. */
    private const TYPO3_VERSION = 4;
    private const PHP_VERSION = 5;
    private const TAGS = 3;
    private const COMPLEXITY = 8;
    private const IS_REGRESSION = 15;

    /** Redmine's own limit on a subject. */
    private const LONGEST_SUBJECT = 255;

    /**
     * The longest login link that still reaches the form. An anonymous reader
     * goes through `/login?back_url=<the link, encoded again>`, and the tracker
     * answered a login link of 7934 characters and refused one of 8486 on
     * 2026-10-05. A signed-in reader's limit is higher, so this one holds for
     * both.
     */
    private const LONGEST_LINK = 7900;

    private const LOGIN = Forge::HOST . '/login?back_url=';

    /**
     * @param array<string, mixed> $args
     * @return array{
     *     subject: string,
     *     tracker: string,
     *     description: string,
     *     form: list<array{field: string, value: string}>,
     *     url: string,
     *     descriptionInLink: bool,
     *     duplicateSearch: array{query: string, status: string, total: int},
     *     possibleDuplicates: list<array{issue: int, subject: string, tracker: string, status: string, url: string}>,
     *     checks: list<array{level: string, code: string, message: string}>
     * }
     */
    public static function draft(array $args, Forge $forge): array
    {
        $checks = [];
        $subject = trim((string) ($args['subject'] ?? ''));
        if (mb_strlen($subject) > self::LONGEST_SUBJECT) {
            $checks[] = self::check('error', 'subject-too-long', sprintf(
                'The subject has %d characters and the tracker takes %d. Name the subsystem and what it does wrong.',
                mb_strlen($subject),
                self::LONGEST_SUBJECT,
            ));
        }

        $tracker = (string) ($args['tracker'] ?? 'Bug');
        $description = self::description($args);
        $checks = [...$checks, ...self::markup($description)];

        $form = [['field' => 'Tracker', 'value' => $tracker], ['field' => 'Subject', 'value' => $subject]];
        $parameters = [
            'issue[tracker_id]' => (string) self::TRACKERS[$tracker],
            'issue[subject]' => $subject,
        ];

        if (isset($args['priority'])) {
            $priority = (string) $args['priority'];
            $form[] = ['field' => 'Priority', 'value' => $priority];
            $parameters['issue[priority_id]'] = (string) self::PRIORITIES[$priority];
        }

        $category = trim((string) ($args['category'] ?? ''));
        if ($category !== '') {
            $categories = $forge->categories();
            $id = self::named($categories, $category);
            if ($id === null) {
                $checks[] = self::check('error', 'unknown-category', $categories === []
                    ? sprintf('The tracker did not answer its list of categories, so "%s" stays out of the form.', $category)
                    : sprintf(
                        'The core project has no category "%s", so it stays out of the form. typo3_forge_lookup with '
                            . 'category in your own words answers the tracker\'s spelling.',
                        $category,
                    ));
            } else {
                $form[] = ['field' => 'Category', 'value' => (string) array_search($id, $categories, true)];
                $parameters['issue[category_id]'] = (string) $id;
            }
        }

        $fixedVersion = trim((string) ($args['fixedVersion'] ?? ''));
        if ($fixedVersion !== '') {
            $versions = $forge->versions();
            $id = self::named($versions, $fixedVersion);
            if ($id === null) {
                $checks[] = self::check('error', 'unknown-fixed-version', $versions === []
                    ? sprintf('The tracker did not answer its versions, so "%s" stays out of the form.', $fixedVersion)
                    : sprintf(
                        'The core project has no open version "%s", so it stays out of the form. Open: %s.',
                        $fixedVersion,
                        implode(', ', array_keys($versions)),
                    ));
            } else {
                $form[] = ['field' => 'Target version', 'value' => (string) array_search($id, $versions, true)];
                $parameters['issue[fixed_version_id]'] = (string) $id;
                $checks[] = self::check('info', 'fixed-version-set', 'The target version is the schedule\'s call '
                    . 'rather than the reporter\'s. Set it where you take the fix on yourself.');
            }
        }

        $typo3 = self::major((string) ($args['typo3Version'] ?? ''));
        if ($typo3 !== '') {
            $form[] = ['field' => 'TYPO3 Version', 'value' => $typo3];
            $parameters['issue[custom_field_values][' . self::TYPO3_VERSION . ']'] = $typo3;
            if ($typo3 !== trim((string) $args['typo3Version'])) {
                $checks[] = self::check('info', 'typo3-version-shortened', sprintf(
                    'The field takes the major alone, so "%s" went in as %s.',
                    trim((string) $args['typo3Version']),
                    $typo3,
                ));
            }
        } elseif ($tracker === 'Bug') {
            $checks[] = self::check('error', 'missing-typo3-version', 'A Bug does not go without its TYPO3 Version: '
                . 'the major you saw it on. Pass typo3Version.');
        }

        $php = self::minor((string) ($args['phpVersion'] ?? ''));
        if ($php !== '') {
            $form[] = ['field' => 'PHP Version', 'value' => $php];
            $parameters['issue[custom_field_values][' . self::PHP_VERSION . ']'] = $php;
        }

        if (isset($args['complexity'])) {
            $form[] = ['field' => 'Complexity', 'value' => (string) $args['complexity']];
            $parameters['issue[custom_field_values][' . self::COMPLEXITY . ']'] = (string) $args['complexity'];
        }

        if (isset($args['isRegression'])) {
            $regression = (bool) $args['isRegression'];
            $form[] = ['field' => 'Is Regression', 'value' => $regression ? 'Yes' : 'No'];
            $parameters['issue[custom_field_values][' . self::IS_REGRESSION . ']'] = $regression ? '1' : '0';
        }

        $tags = [];
        foreach ([...array_map('strval', (array) ($args['tags'] ?? [])), self::TAG] as $tag) {
            $tag = trim($tag);
            if ($tag !== '' && !in_array(strtolower($tag), array_map('strtolower', $tags), true)) {
                $tags[] = $tag;
            }
        }
        $form[] = ['field' => 'Tags', 'value' => implode(', ', $tags)];
        $parameters['issue[custom_field_values][' . self::TAGS . ']'] = implode(', ', $tags);

        if (isset($args['parentIssue']) && (int) $args['parentIssue'] > 0) {
            $form[] = ['field' => 'Parent task', 'value' => '#' . (int) $args['parentIssue']];
            $parameters['issue[parent_issue_id]'] = (string) (int) $args['parentIssue'];
        }

        $url = self::link($parameters + ['issue[description]' => $description]);
        $inLink = strlen(self::LOGIN . rawurlencode($url)) <= self::LONGEST_LINK;
        if (!$inLink) {
            $url = self::link($parameters);
            $checks[] = self::check('warning', 'description-not-in-link', 'The description is too long for a link the '
                . 'tracker accepts, so the link fills every other field. Paste the description below into the form.');
        }

        $query = trim((string) ($args['searchFor'] ?? '')) ?: $subject;
        $search = $forge->search($query, 10);
        $duplicates = [];
        foreach ($search['results'] as $hit) {
            $duplicates[] = [
                'issue' => (int) $hit['issue'],
                'subject' => (string) $hit['subject'],
                'tracker' => (string) $hit['tracker'],
                'status' => (string) $hit['status'],
                'url' => Forge::HOST . '/issues/' . (int) $hit['issue'],
            ];
        }
        $checks[] = match ($search['status']) {
            'answered' => self::check('warning', 'possible-duplicates', sprintf(
                '%d issues match "%s". Read them before you file this one.',
                $search['total'],
                $query,
            )),
            'empty' => self::check('info', 'no-match', sprintf(
                'No issue carries every word of "%s". A wording reaches only the issues worded that way. '
                    . 'typo3_forge_lookup with backlog="newest" and createdSince from the day the defect could '
                    . 'first be seen settles a negative.',
                $query,
            )),
            default => self::check('warning', 'search-unavailable', 'The tracker did not answer the search, so '
                . 'nothing here says whether somebody filed this already.'),
        };

        return [
            'subject' => $subject,
            'tracker' => $tracker,
            'description' => $description,
            'form' => $form,
            'url' => $url,
            'descriptionInLink' => $inLink,
            'duplicateSearch' => ['query' => $query, 'status' => $search['status'], 'total' => $search['total']],
            'possibleDuplicates' => $duplicates,
            'checks' => $checks,
        ];
    }

    /**
     * The description as passed, or composed from its parts in the order the
     * core's own reports use: Problem, Steps to reproduce, Cause, Suggested fix.
     *
     * @param array<string, mixed> $args
     */
    private static function description(array $args): string
    {
        $written = trim((string) ($args['description'] ?? ''));
        if ($written !== '') {
            return $written;
        }

        $sections = [];
        $problem = trim((string) ($args['problem'] ?? ''));
        if ($problem !== '') {
            $sections[] = "h2. Problem\n\n" . $problem;
        }
        $steps = array_values(array_filter(array_map(
            static fn(mixed $step): string => trim((string) $step),
            (array) ($args['stepsToReproduce'] ?? []),
        ), static fn(string $step): bool => $step !== ''));
        if ($steps !== []) {
            $sections[] = "h2. Steps to reproduce\n\n" . implode("\n", array_map(
                static fn(string $step): string => '# ' . $step,
                $steps,
            ));
        }
        $cause = trim((string) ($args['cause'] ?? ''));
        if ($cause !== '') {
            $sections[] = "h2. Cause\n\n" . $cause;
        }
        $fix = trim((string) ($args['suggestedFix'] ?? ''), "\n");
        if (trim($fix) !== '') {
            $sections[] = "h2. Suggested fix\n\n<pre><code class=\"diff\">\n" . $fix . "\n</code></pre>";
        }

        return implode("\n\n", $sections);
    }

    /**
     * What Redmine rewrites in a description, outside `<pre>` blocks.
     *
     * @return list<array{level: string, code: string, message: string}>
     */
    private static function markup(string $description): array
    {
        $outside = (string) preg_replace('~<pre\b.*?</pre>~s', '', $description);
        $checks = [];
        if (str_contains($outside, '```')) {
            $checks[] = self::check('error', 'markdown-fence', 'Three backticks render as three backticks. A code '
                . 'block is <pre><code class="php"> … </code></pre>.');
        }
        if (preg_match('~(?<![`\w])`[^`\n]+`(?!`)~', $outside) === 1) {
            $checks[] = self::check('warning', 'markdown-code', 'A backtick renders as a backtick. Inline code is '
                . '@Foo::bar()@.');
        }
        if (preg_match('~^#{2,6} \S~m', $outside) === 1) {
            $checks[] = self::check('warning', 'markdown-heading', 'A line that starts with ## is a nested list item '
                . 'in Textile. A heading is h2. or h3.');
        }
        if (preg_match('~^\d+\. .*\n\d+\. ~m', $outside) === 1) {
            $checks[] = self::check('warning', 'numbered-lines', 'Lines that start 1., 2., 3. render as one '
                . 'paragraph. An ordered list is # and a space per line.');
        }
        if (preg_match('~^[+-][^+-].*\n[+-][^+-]~m', $outside) === 1) {
            $checks[] = self::check('warning', 'bare-diff', 'Redmine reads lines that start with + and - as markup '
                . 'and drops the removed ones. A diff goes into <pre><code class="diff">.');
        }

        return $checks;
    }

    /** @param array<string, string> $parameters */
    private static function link(array $parameters): string
    {
        return self::NEW_ISSUE . '?' . http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
    }

    /** @param array<string, int> $listed */
    private static function named(array $listed, string $name): ?int
    {
        foreach ($listed as $listedName => $id) {
            if (strcasecmp($listedName, $name) === 0) {
                return $id;
            }
        }

        return null;
    }

    private static function major(string $version): string
    {
        return preg_match('~^\s*(\d+)~', $version, $matched) === 1 ? $matched[1] : '';
    }

    private static function minor(string $version): string
    {
        return preg_match('~^\s*(\d+\.\d+)~', $version, $matched) === 1 ? $matched[1] : '';
    }

    /** @return array{level: string, code: string, message: string} */
    private static function check(string $level, string $code, string $message): array
    {
        return ['level' => $level, 'code' => $code, 'message' => $message];
    }
}
