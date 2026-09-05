<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Everything this repository binds to a core version, against the checkouts.
 *
 * The four below each answer for one subject and are the ones to run while
 * working on it. This is the other question — whether anything has fallen
 * behind a core release — and it is one question rather than four, because a
 * release moves all of them at once and a session that runs three of the four
 * has checked nothing in particular.
 *
 * It is not part of `repository:check`, which reads only what this checkout can
 * answer on its own: the clones below `.checkouts/` are a second thing to have,
 * and a check that fails for not having one is a check nobody keeps green.
 */
#[AsCommand(
    name: 'checkouts:verify',
    description: 'every check that reads .checkouts/: components, references, system extensions and versions',
)]
final class CheckoutVerify
{
    /** In the order a reader wants them: the catalogs first, then what is bound to a version. */
    private const VERIFIED = ['components', 'references', 'system-extensions', 'versions'];

    public function __invoke(OutputInterface $output, Application $application): int
    {
        $directory = Checkouts::directory();
        if (!is_dir($directory)) {
            Voice::problem($output, sprintf('No checkouts below %s — run bin/cli checkouts:update.', $directory));

            return 2;
        }

        $worst = 0;
        foreach (self::VERIFIED as $subject) {
            Voice::heading($output, $subject);
            $worst = max($worst, $application->doRun(new StringInput($subject . ':check'), $output));
        }

        return $worst;
    }
}
