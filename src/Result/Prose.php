<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Knowledge\Versions;

/**
 * Matched sections of the markdown knowledge documents, as an answer.
 *
 * Three tools render the same corpus, the rule lookup, the script lookup and
 * the task guide. A caller that learns to read one of those answers has to find
 * the next one built the same way.
 */
final class Prose
{
    /**
     * Where a range that is not this corpus's own travels.
     *
     * A section says what it holds for beside itself since `D-VER-005`, so this
     * no longer stands in for a binding. What it still answers is the question
     * the ranges here cannot. A runTests.sh command binds to the suite in
     * test-suite-hints.json rather than to the section that quotes it. A
     * convention binds to the statement in the hints. A 12.4 reader who follows
     * the wrong one of those finds nothing.
     */
    private const BOUND_ELSEWHERE = 'A section carries the range it holds for where it has one. '
        . 'What is bound elsewhere: call typo3_hint_lookup with targetVersion for a convention';

    /**
     * The half of that which is a core command.
     *
     * `runTests.sh` is the core repository's script, so outside the core this
     * line sends the caller to a tool whose answer is a decline (`D-SCO-015`).
     */
    private const BOUND_TO_A_SUITE = ', and typo3_test_run_guide with targetVersion for a runTests.sh command';

    /**
     * Renders matched knowledge sections as coherent excerpts. The section
     * keeps its own heading and original format, so code blocks and nested
     * lists survive.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     */
    public static function sections(array $results, bool $outsideCore): string
    {
        return self::boundElsewhere($outsideCore) . "\n\n"
            . self::excerpts($results) . "\n\n" . self::readWhole($results);
    }

    /**
     * What carries a range that is not this corpus's own, in the scope the
     * caller is in.
     */
    private static function boundElsewhere(bool $outsideCore): string
    {
        return self::BOUND_ELSEWHERE . ($outsideCore ? '' : self::BOUND_TO_A_SUITE) . '.';
    }

    /**
     * The page every match came out of, instead of the matches.
     *
     * A search whose several hits all sit in one document has established which
     * page answers the task. A cut there buys nothing, because the second
     * search is the expensive part, `D-ANS-076`. How many hits that takes is
     * the caller's floor rather than this renderer's, `D-ANS-101`. The page
     * goes over as it stands. A section left out for a major it does not hold
     * on is a hole in a page. One of the matches may be the text above the
     * first heading. All of them cannot, since a page has one such section and
     * the floor takes two.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     */
    public static function wholePage(string $documentId, array $results, bool $outsideCore): string
    {
        $headings = Documents::headings($documentId);

        return self::boundElsewhere($outsideCore) . "\n\n"
            . sprintf(
                'Every section this query matched is in one document, so this is the page rather than the '
                    . 'excerpts — %s (%s), %d headings, of which the query matched %s%s. It is the page as written, so '
                    . "a section that holds on other majors than yours is in it and says so under its heading.\n\n",
                $results[0]['title'],
                Documents::uri($documentId),
                count($headings),
                implode(', ', self::matchedHeadings($results)),
                self::matchedTheOpening($results) ? ', and the opening above the first heading' : '',
            )
            . Documents::read($documentId);
    }

    /**
     * The headings the query matched, for the answer that hands the page over.
     *
     * The text above a page's first heading is a section this corpus returns
     * and carries no heading. So it is not one of these. Listed as one it named
     * nothing at all: "of which the query matched The Probe, .". The count it
     * stands against is `Documents::headings()`, which leaves the start out for
     * the same reason.
     *
     * @param array<int, array<string, mixed>> $results
     * @return array<int, string>
     */
    public static function matchedHeadings(array $results): array
    {
        return array_values(array_unique(array_filter(array_column($results, 'heading'))));
    }

    /**
     * Whether one of the matches is the text above the page's first heading.
     *
     * @param array<int, array<string, mixed>> $results
     */
    private static function matchedTheOpening(array $results): bool
    {
        return in_array('', array_column($results, 'heading'), true);
    }

    /**
     * The page a search concentrated in, as the one record its answer carries.
     *
     * The pair that ranks it is the best-ranked match's, which is the section
     * the order put the answer on. It was the constants `0` and `1.0` until
     * `D-ANS-101`. A session read the score as "nothing matched" and was right
     * by accident. The coverage asserted that the page answers the whole query
     * on a client that may validate the declared schema and act on it.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     * @return array<int, array<string, mixed>>
     */
    public static function pageRecords(string $documentId, array $results): array
    {
        return self::pageRecord(
            $documentId,
            $results[0]['title'],
            $results[0]['score'],
            round($results[0]['coverage'], 3),
        );
    }

    /**
     * The page a caller named by documentId, as the one record that answer
     * carries.
     *
     * No search ran, so both halves of the pair are zero rather than a
     * measurement nothing made — `D-ANS-101`.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function namedPageRecords(string $documentId, string $title): array
    {
        return self::pageRecord($documentId, $title, 0, 0.0);
    }

    /** @return array<int, array<string, mixed>> */
    private static function pageRecord(string $documentId, string $title, int $score, float $coverage): array
    {
        return [[
            'documentId' => $documentId,
            'title' => $title,
            'uri' => Documents::uri($documentId),
            'heading' => $title,
            'body' => Documents::read($documentId),
            'versions' => '',
            'coverage' => $coverage,
            'score' => $score,
            'truncated' => false,
        ]];
    }

    /**
     * The pages the excerpts came out of, as the resources that hold them
     * whole.
     *
     * A search answers the section the query matched, and a procedure is
     * several of them, `D-AUD-007`. The `Source:` line above each excerpt
     * carries the same uri and read as attribution, which is what this says out
     * loud instead. Each page says what the search left of it, because the
     * page's name alone did not take (`D-ANS-070`). The headings rather than
     * the share alone, because the next query comes out of them.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     */
    private static function readWhole(array $results): string
    {
        $titles = [];
        $returned = [];
        foreach ($results as $result) {
            $titles[$result['id']] = $result['title'];
            $returned[$result['id']][] = $result['heading'];
        }

        $pages = [];
        foreach ($titles as $id => $title) {
            $headings = Documents::headings($id);
            $left = array_values(array_diff($headings, $returned[$id]));
            $pages[] = sprintf(
                '- %s — %s: %s',
                $id,
                $title,
                $left === []
                    ? sprintf('all %d of its headings are above.', count($headings))
                    : sprintf(
                        '%d of its %d headings are not above — %s.',
                        count($left),
                        count($headings),
                        implode(', ', $left),
                    ),
            );
        }

        // Named as a call rather than as an address. The uri was here and three
        // core sessions held one and never fetched the page. One of them
        // finished a full patch review with no document read end to end. It
        // searched the checkout for a section the same document carried. A
        // `typo3://guides` address is delivery to a client that renders MCP
        // resources. `typo3_rule_lookup` reaches every client there is,
        // `D-ANS-061`, `R-ANS-028`.
        return 'Each excerpt above is one section of a longer document, and each page below carries the `##` '
            . 'headings that are not above. Where the task is the whole procedure rather than the fact you '
            . 'searched for, read the page — typo3_rule_lookup with documentId, which needs no resource list:'
            . "\n" . implode("\n", $pages);
    }

    /**
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     */
    private static function excerpts(array $results): string
    {
        return implode("\n\n", array_map(static function (array $result): string {
            $heading = $result['heading'] === '' ? $result['title'] : $result['heading'];
            $versions = Versions::label($result['since'] ?? null, $result['until'] ?? null);
            $source = sprintf(
                'Source: %s (%s)%s — matches %d%% of the query terms',
                $result['title'],
                Documents::uri($result['id']),
                $versions === '' ? '' : ' [' . $versions . ']',
                (int) round($result['coverage'] * 100),
            );

            $body = $result['body'];
            if ($result['truncated']) {
                // Named as the call the line below names, rather than as the
                // uri it used to be. A cut is exactly where a caller stops, and
                // a client that lists no resources cannot act on an address.
                $body .= "\n\n(section truncated — read " . $result['id'] . ' whole for the rest)';
            }

            return '## ' . $heading . "\n" . $source . "\n\n" . $body;
        }, $results));
    }

    /**
     * The same matched sections as data. The document they come from, how much
     * of the query they cover, and the resource that holds the full text.
     *
     * @param array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}> $results
     * @return array<int, array<string, mixed>>
     */
    public static function records(array $results): array
    {
        return array_map(static fn(array $result): array => [
            'documentId' => $result['id'],
            'title' => $result['title'],
            'uri' => Documents::uri($result['id']),
            'heading' => $result['heading'] === '' ? $result['title'] : $result['heading'],
            'body' => $result['body'],
            'versions' => Versions::label($result['since'] ?? null, $result['until'] ?? null),
            'coverage' => round($result['coverage'], 3),
            'score' => $result['score'],
            'truncated' => $result['truncated'],
        ], $results);
    }

    /** The topics one document covers, for an answer that has to say what it searched. */
    public static function topics(string $documentId): string
    {
        foreach (Documents::topics() as $document) {
            if ($document['id'] === $documentId) {
                return implode(', ', $document['topics']);
            }
        }

        return '';
    }
}
