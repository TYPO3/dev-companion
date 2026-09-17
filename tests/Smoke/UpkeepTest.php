<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Smoke;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Requirement;

/**
 * That `bin/cli` still runs, driven the way a session drives it.
 *
 * Unit tests cover the logic behind each subject a class at a time, and that is
 * what left the gap this closes. A command can meet its rules and still be
 * unreachable, because it resolves what it reads from where its own file sits.
 * Moving the subjects one directory deeper broke four of them at once and every
 * test stayed green, since none of them went through the entrypoint. These do.
 */
final class UpkeepTest extends TestCase
{
    /**
     * Every command that only reports. The ones that write stay out on purpose:
     * the three indexes, `tools:record`, `scenarios:record`,
     * `feedback:archive`, `checkouts:update`. A test suite that rewrites the
     * repository it runs in is worse than the gap.
     *
     * The ones that take an argument are here with one, because that is the
     * half a class-at-a-time test cannot see. The console reads what a command
     * takes off the parameters of its own `__invoke`. A command whose arguments
     * the console no longer reads refuses the caller who passes one.
     *
     * @return array<string, array{0: array<int, string>}>
     */
    public static function readingCommands(): array
    {
        return [
            'the command list, which is what an empty invocation prints' => [[]],
            'requirements:check' => [['requirements:check']],
            'requirements:list' => [['requirements:list']],
            'requirements:list, for one group' => [['requirements:list', 'knowledge']],
            'decisions:check' => [['decisions:check']],
            'decisions:list' => [['decisions:list']],
            'scenarios:check' => [['scenarios:check']],
            'scenarios:show, which takes the review to hand over' => [['scenarios:show', 'REVIEW-01']],
            'scenarios:contract, which takes the case to hand over' => [['scenarios:contract', 'SKILL-09']],
            'scenarios:contract, named no case at all' => [['scenarios:contract']],
            'prose:check' => [['prose:check']],
            'checkouts:verify' => [['checkouts:verify']],
            'components:check' => [['components:check']],
            'references:check' => [['references:check']],
            'system-extensions:check' => [['system-extensions:check']],
            'versions:check' => [['versions:check']],
            'hints:coverage' => [['hints:coverage']],
            'hints:probe, which takes the query to read the corpus back through' => [['hints:probe', 'extbase controller']],
            'unresolved:list' => [['unresolved:list']],
            'todo:list' => [['todo:list']],
            'todo:waiting' => [['todo:waiting']],
            'todo:check' => [['todo:check']],
            'tools:check' => [['tools:check']],
            'tools:measure' => [['tools:measure']],
            'feedback:list' => [['feedback:list']],
            'checkouts:status' => [['checkouts:status']],
            'todo:next' => [['todo:next']],
            'repository:check' => [['repository:check']],
        ];
    }

    /**
     * That it reaches its own code and reports, which is a different question
     * from whether it liked what it found. The exit code is the command's to
     * decide. `hints coverage` reports a gap and says so with a 1, an empty
     * invocation is a usage error and says so with a 2. So what this holds is
     * that nothing died on the way: no uncaught error, and an answer.
     *
     * @param array<int, string> $arguments
     */
    #[Requirement('R-COD-001')]
    #[DataProvider('readingCommands')]
    #[Test]
    public function everyReadingCommandRuns(array $arguments): void
    {
        $stdout = '';
        $stderr = '';

        $exit = $this->execute($arguments, $stdout, $stderr);

        self::assertStringNotContainsString('Fatal error', $stderr, 'died rather than answered');
        self::assertStringNotContainsString('Uncaught', $stderr, 'died rather than answered');
        self::assertNotSame(255, $exit, $stderr);
        // Either stream: the usage an empty invocation prints is a message to
        // whoever typed nothing, and goes where a message goes.
        self::assertNotSame('', trim($stdout . $stderr), 'answered with nothing');
    }

    /** @param array<int, string> $arguments */
    private function execute(array $arguments, string &$stdout, string &$stderr): int
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/cli', ...$arguments],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            Paths::root(),
        );
        self::assertIsResource($process);
        fclose($pipes[0]);
        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process);
    }
}
