<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Manual\Manuals;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Whether each manual still declares the shortcode `knowledge/manuals.json`
 * addresses it by.
 *
 * Nothing on docs.typo3.org publishes the set of shortcodes, so the list is
 * maintained here and would otherwise go stale without anything failing — a
 * manual renamed, moved to another collection or dropped is a 404 the next
 * session reports as a gap (`D-ANS-120`). Each manual does publish its own,
 * though: the theme writes the `interlink-shortcode` of its `guides.xml` into
 * every page it renders, so the claim is checkable one entry at a time.
 *
 * It reads the host, which no other command here does, and that is the whole
 * reason it is a command rather than a test.
 */
#[AsCommand(
    name: 'manuals:check',
    description: 'the shortcode and the collection knowledge/manuals.json gives each manual, against what that manual publishes',
)]
final class ManualCheck
{
    /** The branch every listed manual publishes, and the one this reads them at. */
    private const BRANCH = 'main';

    /** What a rendered page states the manual's own shortcode as. */
    private const DECLARED = '/data-interlink-shortcode="([^"]*)"/';

    public function __invoke(OutputInterface $output): int
    {
        $reader = new Fetch(Manuals::reader());
        $problems = 0;
        $bar = Voice::progress($output, count(Manuals::all()));
        $bar->start();
        foreach (Manuals::all() as $manual) {
            $bar->setMessage($manual['shortcode']);
            $bar->display();
            $base = Manuals::base($manual['collection'], $manual['document'], self::BRANCH);
            $page = $reader->get($base . 'Index.html');
            $declared = $page !== null && preg_match(self::DECLARED, $page, $stated) === 1 ? $stated[1] : null;

            $bar->clear();
            Voice::row($output, sprintf(
                '%s %-34s %s',
                Voice::key($manual['shortcode'], 16),
                $manual['collection'] . '/' . $manual['document'],
                $declared ?? ($page === null ? 'did not answer' : 'declares no shortcode'),
            ));
            $bar->advance();
            if ($declared === $manual['shortcode']) {
                continue;
            }

            ++$problems;
            Voice::problem($output, sprintf(
                '%s is addressed as %s here and %s',
                $manual['title'],
                $manual['shortcode'],
                $declared === null
                    ? 'could not be read at ' . $base
                    : 'declares ' . $declared,
            ));
        }

        $bar->clear();

        return Voice::verdict(
            $output,
            $problems,
            sprintf('Every manual declares the shortcode it is addressed by, read at %s.', self::BRANCH),
            Voice::count($problems, 'manual') . ' no longer declare what knowledge/manuals.json says — D-ANS-120 named this the drift nothing else reports.',
        );
    }
}
