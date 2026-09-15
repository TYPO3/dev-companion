<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Cli;

/**
 * Holds `src/Upkeep/Command/` and the application to each other.
 *
 * One class is one command, and `Upkeep\Cli` is the only place that switches
 * one on. That arrangement lets a class pass review and merge and never be
 * reachable. These are the other direction. Every class in the directory is on
 * the application, and the application carries no command that is not one of
 * them. Each says its name, what it does and what it takes.
 */
final class UpkeepCommandTest extends TestCase
{
    /**
     * Every command class there is, read off the directory rather than off the
     * application — which is what makes one it forgot a failure here.
     *
     * @return array<string, array{0: class-string, 1: string}>
     */
    public static function commandClasses(): array
    {
        $cases = [];
        foreach (Finder::create()->files()->in(Paths::root() . '/src/Upkeep/Command')->depth(0)->name('*.php')->sortByName() as $path) {
            /** @var class-string $class */
            $class = 'TYPO3\\DevCompanion\\Upkeep\\Command\\' . $path->getBasename('.php');
            $reflection = new \ReflectionClass($class);
            if ($reflection->isAbstract()) {
                continue;
            }

            $attributes = $reflection->getAttributes(AsCommand::class);
            $cases[$path->getBasename('.php')] = [
                $class,
                $attributes === [] ? '' : (string) $attributes[0]->newInstance()->name,
            ];
        }

        return $cases;
    }

    /**
     * A class in the directory that nothing registers is a command nobody can
     * run. It passes every check it has of its own, and the only way to notice
     * is a search for it.
     *
     * @param class-string $class
     */
    #[Decision('D-COD-002')]
    #[Test]
    #[DataProvider('commandClasses')]
    public function everyCommandClassIsOnTheApplication(string $class, string $name): void
    {
        self::assertNotSame('', $name, $class . ' carries no #[AsCommand], so nothing names it');
        self::assertTrue(Cli::application()->has($name), $class . ' declares ' . $name . ', which nothing registers');
    }

    /**
     * The other direction, as a count: a command registered from somewhere else
     * would be one nothing in this suite ever reads.
     */
    #[Test]
    public function theApplicationCarriesNoCommandThisDirectoryDoesNotHave(): void
    {
        // The console brings `help`, `list` and `completion` of its own, and
        // each of those is a Command subclass. What this repository registers
        // is an invokable class the console wraps in a plain Command.
        $registered = array_filter(
            Cli::application()->all(),
            static fn(Command $command): bool => $command::class === Command::class,
        );

        self::assertEqualsCanonicalizing(
            array_column(self::commandClasses(), 1),
            array_values(array_map(static fn(Command $command): string => (string) $command->getName(), $registered)),
            'the application and src/Upkeep/Command/ are not the same set of commands',
        );
    }

    /**
     * Every command is `<subject>:<verb>`, because that is what groups the list
     * a caller reads. The subject is what the command is about, and a command
     * without one sits loose above every group and belongs to nothing —
     * `D-FBK-041`.
     *
     * @param class-string $class
     */
    #[Decision('D-FBK-041')]
    #[Test]
    #[DataProvider('commandClasses')]
    public function everyCommandIsNamedSubjectThenVerb(string $class, string $name): void
    {
        // A hyphen inside the subject is form rather than structure.
        // `system-extensions` is what this repository calls them everywhere
        // else. A second word invented to avoid the hyphen would be a third
        // name for one thing. The verb stays one word.
        self::assertMatchesRegularExpression('/^[a-z]+(-[a-z]+)*:[a-z]+$/', $name, 'a command is named <subject>:<verb>');
    }

    /**
     * The description is the whole of what `bin/cli list` tells a caller about a
     * command, so a command without one is one nobody finds.
     *
     * @param class-string $class
     */
    #[Test]
    #[DataProvider('commandClasses')]
    public function everyCommandSaysWhatItDoes(string $class, string $name): void
    {
        self::assertNotSame('', Cli::application()->find($name)->getDescription(), $name . ' describes itself to nobody');
    }

    /**
     * A command declares what it takes on the parameters of its `__invoke`, and
     * the console reads its input definition off them. It reads them at one
     * moment only, before anything has merged the application's own definition
     * in. So a command the console no longer asks at that moment keeps every
     * argument in its signature and accepts none of them. That failure reaches
     * the caller as "too many arguments" for an argument the help still lists.
     *
     * Its own definition rather than the one it runs with. The application
     * merges its own `command` argument into every command the first time one
     * runs, and the question here is what this command declares.
     *
     * @param class-string $class
     */
    #[Decision('D-COD-002')]
    #[Test]
    #[DataProvider('commandClasses')]
    public function everyArgumentOfACommandIsOneTheConsoleBinds(string $class, string $name): void
    {
        $declared = [];
        foreach ((new \ReflectionMethod($class, '__invoke'))->getParameters() as $parameter) {
            if ($parameter->getAttributes(Argument::class) !== []) {
                $declared[] = $parameter->getName();
            }
        }

        self::assertSame(
            $declared,
            array_keys(Cli::application()->find($name)->getNativeDefinition()->getArguments()),
            $name . ' takes arguments the console does not bind',
        );
    }
}
