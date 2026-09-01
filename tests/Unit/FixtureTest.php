<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Icons;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Fixture;
use TYPO3\DevCompanion\Upkeep\ToolCalls;

/**
 * The installation this repository writes, held to what it is written for.
 *
 * The second half of a tool's page is recorded rather than checked, because it
 * needs an installation and no test run discovers one — `D-DOC-006`. This is the
 * exception the fixture creates: it is an installation this repository produces
 * itself, so a fixture that stopped booting fails here rather than in the next
 * recording quietly writing `unsupported` onto nine pages. What is held is that
 * it answers, not what it answers with, which is the recording's.
 */
final class FixtureTest extends TestCase
{
    #[After]
    public function forgetTheInstance(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();
        Icons::forget();
    }

    /**
     * The fixture is an installation the recording is taken from, so it has to
     * boot and answer through its own console rather than only look like one —
     * `D-DOC-012`.
     */
    #[Decision('D-DOC-012')]
    #[Test]
    public function theWrittenInstallationBootsAndItsConsoleAnswers(): void
    {
        $this->standIn();

        self::assertTrue(Typo3Cli::isAvailable(), Typo3Cli::reason());

        $answer = Typo3Runtime::ask();

        self::assertSame(Typo3Runtime::STATE_FULL, $answer['state'], $answer['reason']);
        self::assertSame(
            ['icons', 'tables', 'contentElements', 'derivedColumns', 'formDataGroups', 'modules'],
            array_keys($answer['topics']),
            'the topics the probe reads, all of them: a container missing one answers that topic unavailable',
        );
    }

    /**
     * Every call of an installation-backed tool comes back as an answer.
     *
     * `unsupported` is what those tools say where there was nothing to ask, and
     * a second recording of it says what the first already said — the core
     * checkout has no console and reports exactly that. So the property that
     * makes this root worth recording against is that not one call falls to it
     * — `D-DOC-012`.
     */
    #[Decision('D-DOC-012')]
    #[Test]
    public function everyInstallationBackedToolAnswersFromIt(): void
    {
        $this->standIn();

        $unanswered = [];
        foreach (ToolCalls::all() as $label => [$name, $arguments]) {
            if (!$this->declaresAnsweredBy($name)) {
                continue;
            }
            // The one call this root cannot stand in for. It assembles the
            // installation's container a second time through the core's own
            // builder, and a fixture that could answer it would have to be a
            // TYPO3 rather than a shape of one — `D-DIS-023`.
            if ($name === 'typo3_service_lookup') {
                continue;
            }
            // And the one call that reaches the database. The fixture is a
            // shape of an installation and holds no rows to count, so what it
            // stands in for here is the boundary — the other two calls of this
            // tool are answered from it — `D-AUD-016`.
            if ($label === 'records: a table of this project') {
                continue;
            }
            $data = Registry::call($name, $arguments)->data;
            if (isset($data['unsupported'])) {
                $unanswered[] = $label . ': ' . ($data['unsupported']['reason'] ?? '');
            }
        }

        self::assertSame([], $unanswered, 'calls the fixture installation could not answer');
    }

    private function declaresAnsweredBy(string $name): bool
    {
        foreach (Registry::definitions() as $definition) {
            if ($definition['name'] === $name) {
                return isset($definition['outputSchema']['properties']['answeredBy']);
            }
        }

        return false;
    }

    /** Writes it and points every reading of an installation at it. */
    private function standIn(): void
    {
        Instance::discoverFrom(Fixture::write());
        Typo3Cli::forget();
        Typo3Runtime::forget();
        Icons::forget();
    }
}
