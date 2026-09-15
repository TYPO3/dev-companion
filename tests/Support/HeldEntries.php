<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Support;

use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\ErroredSubscriber;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\FailedSubscriber;
use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\Requirements;

/**
 * What a failed test held, printed where a reader meets the failure.
 *
 * The tests that declare `#[Decision]` and `#[Requirement]` hold a decision and
 * a requirement. The session that changes the behaviour stands in the test
 * rather than in `decisions/` or `requirements/`. The attribute reaches whoever
 * opens the file; this reaches whoever reads the run. Each entry comes with the
 * path to it. So a test made green again is an entry somebody looked at rather
 * than one nobody knew was there.
 *
 * The state is static because a run has one of these, and the three subscribers
 * are one collector seen from three events. A run where nothing fails prints
 * nothing.
 */
final class HeldEntries implements Extension
{
    /** @var array<string, array{class: string, method: string}> */
    private static array $failed = [];

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        self::$failed = [];

        $facade->registerSubscribers(
            new class implements FailedSubscriber {
                public function notify(Failed $event): void
                {
                    HeldEntries::remember($event->test());
                }
            },
            new class implements ErroredSubscriber {
                public function notify(Errored $event): void
                {
                    HeldEntries::remember($event->test());
                }
            },
            new class implements ExecutionFinishedSubscriber {
                public function notify(ExecutionFinished $event): void
                {
                    HeldEntries::report();
                }
            },
        );
    }

    /**
     * One failed test, by the name a decision writes it under: the class
     * without its namespace, and the method.
     */
    public static function remember(mixed $test): void
    {
        if (!$test instanceof TestMethod) {
            return;
        }

        $class = substr((string) strrchr('\\' . $test->className(), '\\'), 1);
        self::$failed[$class . '::' . $test->methodName()] = [
            'class' => $class,
            'method' => $test->methodName(),
        ];
    }

    /** Every entry the failures of this run held, once each. */
    public static function report(): void
    {
        $lines = [];
        foreach (self::$failed as $name => $test) {
            $held = [
                ...Decisions::restingOn($test['class'], $test['method']),
                ...Requirements::restingOn($test['class'], $test['method']),
            ];
            foreach ($held as $entry) {
                $lines[] = sprintf('  %s — %s', $entry['id'], $entry['title']);
                $lines[] = sprintf('    %s (%s)', $entry['file'], $name);
            }
        }

        if ($lines === []) {
            return;
        }

        fwrite(STDERR, "\nWhat failed above is what these entries rest on:\n" . implode("\n", $lines) . "\n");
    }
}
