<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Command\CheckoutStatus;
use TYPO3\DevCompanion\Upkeep\Command\CheckoutUpdate;
use TYPO3\DevCompanion\Upkeep\Command\CheckoutVerify;
use TYPO3\DevCompanion\Upkeep\Command\ComponentCheck;
use TYPO3\DevCompanion\Upkeep\Command\ComponentDerive;
use TYPO3\DevCompanion\Upkeep\Command\ComponentPaths;
use TYPO3\DevCompanion\Upkeep\Command\DecisionCheck;
use TYPO3\DevCompanion\Upkeep\Command\DecisionCover;
use TYPO3\DevCompanion\Upkeep\Command\DecisionIndex;
use TYPO3\DevCompanion\Upkeep\Command\DecisionList;
use TYPO3\DevCompanion\Upkeep\Command\DecisionRename;
use TYPO3\DevCompanion\Upkeep\Command\DecisionRenumber;
use TYPO3\DevCompanion\Upkeep\Command\DocumentationPrepare;
use TYPO3\DevCompanion\Upkeep\Command\DocumentationPreview;
use TYPO3\DevCompanion\Upkeep\Command\EntryLookup;
use TYPO3\DevCompanion\Upkeep\Command\EnvironmentCreate;
use TYPO3\DevCompanion\Upkeep\Command\EnvironmentStatus;
use TYPO3\DevCompanion\Upkeep\Command\FeedbackArchive;
use TYPO3\DevCompanion\Upkeep\Command\FeedbackList;
use TYPO3\DevCompanion\Upkeep\Command\ForgeCategoryCheck;
use TYPO3\DevCompanion\Upkeep\Command\HintCoverage;
use TYPO3\DevCompanion\Upkeep\Command\HintProbe;
use TYPO3\DevCompanion\Upkeep\Command\KnowledgeFormat;
use TYPO3\DevCompanion\Upkeep\Command\LinkCheck;
use TYPO3\DevCompanion\Upkeep\Command\LinkRepair;
use TYPO3\DevCompanion\Upkeep\Command\ManualCheck;
use TYPO3\DevCompanion\Upkeep\Command\ProseCheck;
use TYPO3\DevCompanion\Upkeep\Command\ProseFormat;
use TYPO3\DevCompanion\Upkeep\Command\ReferenceCheck;
use TYPO3\DevCompanion\Upkeep\Command\RepositoryCheck;
use TYPO3\DevCompanion\Upkeep\Command\RequirementCheck;
use TYPO3\DevCompanion\Upkeep\Command\RequirementCover;
use TYPO3\DevCompanion\Upkeep\Command\RequirementIndex;
use TYPO3\DevCompanion\Upkeep\Command\RequirementList;
use TYPO3\DevCompanion\Upkeep\Command\RequirementRename;
use TYPO3\DevCompanion\Upkeep\Command\ScenarioCheck;
use TYPO3\DevCompanion\Upkeep\Command\ScenarioContract;
use TYPO3\DevCompanion\Upkeep\Command\ScenarioRecord;
use TYPO3\DevCompanion\Upkeep\Command\ScenarioShow;
use TYPO3\DevCompanion\Upkeep\Command\SystemExtensionCheck;
use TYPO3\DevCompanion\Upkeep\Command\TodoCheck;
use TYPO3\DevCompanion\Upkeep\Command\TodoClaim;
use TYPO3\DevCompanion\Upkeep\Command\TodoDrop;
use TYPO3\DevCompanion\Upkeep\Command\TodoHome;
use TYPO3\DevCompanion\Upkeep\Command\TodoList;
use TYPO3\DevCompanion\Upkeep\Command\TodoNext;
use TYPO3\DevCompanion\Upkeep\Command\TodoPark;
use TYPO3\DevCompanion\Upkeep\Command\TodoWaiting;
use TYPO3\DevCompanion\Upkeep\Command\ToolCheck;
use TYPO3\DevCompanion\Upkeep\Command\ToolIndex;
use TYPO3\DevCompanion\Upkeep\Command\ToolMeasure;
use TYPO3\DevCompanion\Upkeep\Command\ToolRecord;
use TYPO3\DevCompanion\Upkeep\Command\UnresolvedList;
use TYPO3\DevCompanion\Upkeep\Command\VersionCheck;

/**
 * Everything this repository is kept in order by, as one console application.
 *
 * What this replaces is six scripts below bin/, each with a usage line of its
 * own. Nothing said what the set of them was: a command existed for whoever had
 * listed the directory, and the only overview was a block in AGENTS.md that no
 * code read and nothing held to the truth. The list below is that set, and it is
 * the only place a command is switched on — everything a caller is shown, from
 * `bin/cli list` down to the arguments of one command, the console reads off the
 * classes registered here.
 *
 * The console is `symfony/console`, and it is a dev dependency: `bin/cli` is the
 * upkeep of this checkout and Composer exports it as no `bin`, so what it needs
 * is not what an installation of this package needs.
 *
 * `bin/typo3-dev-companion` is deliberately not here. That one is the product — the
 * client launches it, Composer exports it as a `bin`, and it has no business
 * carrying the upkeep of the repository it happens to live in.
 */
final class Cli
{
    /**
     * The sentence at the head of the command list. It is the application's
     * name because that is where the console prints it, and there is no second
     * place to say what this command is for.
     */
    private const ABOUT = 'The upkeep of this repository. bin/typo3-dev-companion is the server itself.';

    private static ?Application $application = null;

    /**
     * Every command there is, on the application `bin/cli` runs and that a
     * todo's own `Run:` lines are dispatched through.
     */
    public static function application(): Application
    {
        if (self::$application instanceof Application) {
            return self::$application;
        }

        $application = new Application(self::ABOUT);
        // The exit code belongs to the caller: `bin/cli` hands it to exit(),
        // and `todo:next` reads it to decide whether a todo still has work.
        $application->setAutoExit(false);

        $application->addCommand(new RequirementList());
        $application->addCommand(new RequirementCheck());
        $application->addCommand(new RequirementCover());
        $application->addCommand(new RequirementIndex());
        $application->addCommand(new RequirementRename());
        $application->addCommand(new DecisionList());
        $application->addCommand(new DecisionCheck());
        $application->addCommand(new DecisionCover());
        $application->addCommand(new DecisionIndex());
        $application->addCommand(new DecisionRename());
        $application->addCommand(new DecisionRenumber());
        $application->addCommand(new ToolCheck());
        $application->addCommand(new ToolIndex());
        $application->addCommand(new ToolMeasure());
        $application->addCommand(new ToolRecord());
        $application->addCommand(new ScenarioShow());
        $application->addCommand(new ScenarioContract());
        $application->addCommand(new ScenarioRecord());
        $application->addCommand(new ScenarioCheck());
        $application->addCommand(new TodoNext());
        $application->addCommand(new TodoList());
        $application->addCommand(new TodoWaiting());
        $application->addCommand(new TodoClaim());
        $application->addCommand(new TodoHome());
        $application->addCommand(new TodoDrop());
        $application->addCommand(new TodoPark());
        $application->addCommand(new TodoCheck());
        $application->addCommand(new ProseCheck());
        $application->addCommand(new ProseFormat());
        $application->addCommand(new LinkCheck());
        $application->addCommand(new LinkRepair());
        $application->addCommand(new DocumentationPrepare());
        $application->addCommand(new DocumentationPreview());
        $application->addCommand(new FeedbackList());
        $application->addCommand(new FeedbackArchive());
        $application->addCommand(new UnresolvedList());
        $application->addCommand(new HintProbe());
        $application->addCommand(new HintCoverage());
        $application->addCommand(new ComponentCheck());
        $application->addCommand(new ComponentDerive());
        $application->addCommand(new ComponentPaths());
        $application->addCommand(new ManualCheck());
        $application->addCommand(new ForgeCategoryCheck());
        $application->addCommand(new ReferenceCheck());
        $application->addCommand(new SystemExtensionCheck());
        $application->addCommand(new VersionCheck());
        $application->addCommand(new KnowledgeFormat());
        $application->addCommand(new CheckoutUpdate());
        $application->addCommand(new CheckoutVerify());
        $application->addCommand(new CheckoutStatus());
        $application->addCommand(new EntryLookup());
        $application->addCommand(new EnvironmentCreate());
        $application->addCommand(new EnvironmentStatus());
        $application->addCommand(new RepositoryCheck());

        return self::$application = $application;
    }

    /**
     * Whether `bin/cli …` names something this command can do, for the todos,
     * whose `Run:` lines are commands nobody runs until the day they are due.
     */
    public static function knows(string $command): bool
    {
        $words = explode(' ', $command);
        if (array_shift($words) !== 'bin/cli') {
            return true;
        }

        return $words !== [] && self::application()->has($words[0]);
    }

    /**
     * Where a command writes what went wrong, which is not where it writes its
     * answer. A check prints its problems here and its count on stdout, so the
     * count survives a pipe that the problems are read out of.
     */
    public static function errors(OutputInterface $output): OutputInterface
    {
        return $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
    }
}
