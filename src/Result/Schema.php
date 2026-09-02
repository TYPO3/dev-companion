<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Tool\Source;

/**
 * The record shapes several tools answer with, and the builders they are
 * written in.
 *
 * A tool declares its own output schema; what lives here is what more than one
 * of them says — a knowledge match, an hint, a catalog entry, the
 * reason an installation could not be asked. Schemas stay open (no
 * additionalProperties: false) so a new field is an addition rather than a
 * break, and only fields that are always present are required.
 */
final class Schema
{
    /**
     * The output schema of a tool that answers from the installation: either
     * the result it promises, or the unsupported answer in place of it.
     *
     * The two are alternatives and the schema says so, so a client validating
     * structuredContent — which the specification tells it to — still gets the
     * full promise on a hit, and gets it as a promise rather than as a field
     * that might be there. oneOf also makes the two exclusive: an answer
     * carrying both is invalid, which is the shape this whole entry is against.
     *
     * @param array<string, mixed> $properties
     * @param array<int, string>   $answered what a result always carries
     * @param array<int, string>   $echo     what both carry: the caller's own
     *                                       arguments, which claim nothing
     * @return array<string, mixed>
     */
    public static function installationAnswer(array $properties, array $answered, array $echo = []): array
    {
        $properties['unsupported'] = self::unsupported();

        return self::object($properties, $echo) + ['oneOf' => [
            self::object([], $answered),
            self::object([], [...$echo, 'unsupported']),
        ]];
    }

    /**
     * The question could not be answered here, and this is the whole answer.
     *
     * Present instead of the result, never beside it: a tool that cannot ask
     * states this and states nothing else, so there is no count to read as a
     * count and no flag to read as a fact.
     *
     * @return array<string, mixed>
     */
    public static function unsupported(): array
    {
        return self::object([
            'cause' => [
                'type' => 'string',
                'enum' => ['no-installation', 'misconfigured', 'installation-not-answering'],
                'description' => 'no-installation: nothing to ask from here, and searched says where it looked. '
                    . 'misconfigured: an installation was named and could not be used, so nothing was searched for. '
                    . 'installation-not-answering: one was found and its console did not answer — a stopped '
                    . 'container or a database with no schema, which is a state that ends without reinstalling '
                    . 'anything.',
            ],
            'reason' => self::string('What stopped it, in the words the attempt produced.'),
            'repositoryState' => [
                'type' => ['string', 'null'],
                'enum' => ['installed', 'not-installed', 'undeclared', null],
                'description' => 'Which state the repository the caller stands in is in, which the cause does not '
                    . 'say. installed: packages are installed below the root that was found, so an install is not '
                    . 'what is missing here. not-installed: the repository declares TYPO3 and nothing is installed '
                    . 'below it yet, so this call is answerable once composer install has run. undeclared: nothing '
                    . 'in the directories walked declares TYPO3, so an install here would answer nothing. Null '
                    . 'where nothing was looked at: a named root that could not be used, or an entrypoint that '
                    . 'handed no directory in.',
            ],
            'diagnosis' => self::string('What the reason means where the message alone does not say it. A console that starts and then fails on a missing table has a database without a schema, not a broken installation. Empty where nothing beyond the reason is known.'),
            'searched' => self::listOf(self::string(), 'Every directory the discovery walked, in order. "Nothing was found" and "the server was started somewhere else" wear one sentence, and only this tells them apart. Empty where discovery never ran.'),
            'misconfiguration' => self::nullableString('What was set and could not be used. Null where nothing was set.'),
            'settings' => self::object([
                'root' => self::string('Environment variable that names the installation root.'),
                'console' => self::string('Environment variable that names the console command.'),
            ], ['root', 'console']),
        ], ['cause', 'reason', 'searched', 'settings']);
    }

    /**
     * The other shape of "not answered", and the one a source outside this
     * process takes — `D-ANS-007`.
     *
     * `unsupported()` is what a question this server cannot be asked from
     * where it stands answers with, and it replaces the result. A manual, a
     * tracker, a review server and a registry are reachable from anywhere or
     * from nowhere, so those answer with a status beside the result and this
     * object where the status is `unavailable`.
     *
     * What varies per source is which causes it can have and how each one
     * reads, which is why the caller passes them: the enum is the keys and the
     * description is the sentences, in the order they were written.
     *
     * @param array<string, string> $causes each cause this source can have, and what it means
     * @return array<string, mixed>
     */
    public static function unavailable(array $causes, string $description = ''): array
    {
        $meanings = [];
        foreach ($causes as $cause => $meaning) {
            $meanings[] = $cause . ': ' . $meaning;
        }

        return [
            'type' => ['object', 'null'],
            'description' => $description !== ''
                ? $description
                : 'Why nothing was answered, where status says unavailable. Null otherwise.',
            'properties' => [
                'cause' => [
                    'type' => 'string',
                    'enum' => array_keys($causes),
                    'description' => implode(' ', $meanings),
                ],
                'reason' => self::string(),
            ],
            'required' => ['cause', 'reason'],
        ];
    }

    /**
     * What a call to a source outside this process came back as: an answer, an
     * answer that is empty, or the source not answering at all.
     *
     * @return array<string, mixed>
     */
    public static function answerStatus(): array
    {
        return ['type' => 'string', 'enum' => ['answered', 'empty', 'unavailable']];
    }

    /**
     * Which source answered this call. An answer that came from none of them is
     * not one of its cases — that is unsupported, and it replaces the answer
     * rather than labelling it.
     *
     * The cases are the tool's own `answersFrom()`, so a tool that can only be
     * answered one way says so instead of declaring a fallback it does not
     * have. Sources that never label an answer are left out: a knowledge file
     * and a network service are what the whole tool reads, never one call.
     *
     * @param array<int, Source> $sources what the tool declares it answers from
     * @return array<string, mixed>
     */
    public static function answeredBy(array $sources): array
    {
        $labelled = array_values(array_filter(
            $sources,
            static fn(Source $source): bool => $source === Source::Installation || $source === Source::Packages,
        ));

        $meaning = [
            Source::Installation->value => 'installation: its assembled runtime state answered.',
            Source::Packages->value => 'packages: read from the files the installed packages ship, because the '
                . 'console could not be asked — overrides applied at runtime are not reflected.',
        ];

        return [
            'type' => 'string',
            'enum' => array_map(static fn(Source $source): string => $source->value, $labelled),
            'description' => implode(' ', array_map(
                static fn(Source $source): string => $meaning[$source->value],
                $labelled,
            )),
        ];
    }

    /**
     * Which kind of work an answer is for.
     *
     * `uncertain` is not a hedge but the case the other three cannot state:
     * signals that disagree with nothing left to resolve them. An answer that
     * picked a side there would be right half the time and say so never.
     *
     * @return array<string, mixed>
     */
    public static function scope(string $description = ''): array
    {
        return [
            'type' => 'string',
            'enum' => array_map(static fn(Scope $scope): string => $scope->value, Scope::ofPaths()),
            'description' => $description === ''
                ? 'Which kind of work this answer is for: core, a patch to the TYPO3 core itself; project, the '
                    . 'site repository around an installation; extension, a package in it, whether a sitepackage '
                    . 'or a third-party one; or uncertain, which means nothing in the call placed the work and '
                    . 'what came back is the core\'s own.'
                : $description,
        ];
    }

    /**
     * The same decision per path, because a call is not a path: two files of
     * different scope in one call are two questions.
     *
     * @return array<string, mixed>
     */
    public static function scopes(string $description): array
    {
        return self::listOf(self::object([
            'path' => self::string(),
            'scope' => self::scope(),
        ], ['path', 'scope']), $description);
    }

    /** @return array<string, mixed> */
    public static function knowledgeLookup(): array
    {
        return self::object([
            'query' => self::string(),
            'resource' => self::nullableString('The exact XLF resource the result was restricted to. Null means the caller did not yet provide the usage context.'),
            'matchCount' => self::integer(),
            'matches' => self::listOf(self::knowledgeMatch()),
            'documents' => self::listOf(self::object([
                'id' => self::string(),
                'title' => self::string(),
                'topics' => self::listOf(self::string()),
            ], ['id', 'title', 'topics']), 'Documents in the knowledge base with the topics they cover. Returned when nothing matched.'),
            'elsewhere' => self::listOf(self::string(), 'Documents outside the searched ones that do match the query.'),
            'alsoInHints' => self::listOf(self::object([
                'id' => self::string(),
                'title' => self::string(),
            ], ['id', 'title']), 'Hints matching the same query. They are a second corpus, searched by typo3_hint_lookup, which takes one of these ids.'),
        ], ['query', 'matchCount', 'matches']);
    }

    /** @return array<string, mixed> */
    public static function knowledgeMatch(): array
    {
        return self::object([
            'documentId' => self::string(),
            'title' => self::string('Title of the knowledge document.'),
            'uri' => self::string('typo3://guides resource holding the full document.'),
            'heading' => self::string('Heading of the matched section.'),
            'body' => self::string('The section as written, formatting included.'),
            'versions' => self::string('The TYPO3 majors this section holds for, in words. Empty means every covered major, which is what a section that declares nothing says.'),
            'coverage' => ['type' => 'number', 'description' => 'Share of the query terms the section covers, 0 to 1. '
                . 'Zero where no search ranked this record, which is a page the caller named by documentId.'],
            'score' => self::integer('Weighted match score; headings weigh more than body text. Zero where no search '
                . 'ranked this record.'),
            'truncated' => ['type' => 'boolean', 'description' => 'Whether the body was cut; read the resource for the rest.'],
        ], ['documentId', 'title', 'uri', 'heading', 'body', 'coverage', 'score', 'truncated']);
    }

    /**
     * Who is obliged by something, where that is not everyone.
     *
     * @return array<string, mixed>
     */
    public static function obliges(string $subject): array
    {
        return [
            'type' => ['string', 'null'],
            'enum' => ['core', 'project', 'extension', null],
            'description' => sprintf(
                'Which kind of work %s obliges. "core" means it is a condition of a patch to the TYPO3 core and a '
                . 'convention anywhere else — the backend\'s own design system, the changelog artifact, the paths '
                . 'of the mono repository. "project" and "extension" are the mirror: what the repository around an '
                . 'installation, or a package distributed on its own, has to do, and what is context rather than a '
                . 'condition inside the core. Null, the ordinary case, means it holds wherever TYPO3 is written: an '
                . 'API that throws throws in a sitepackage too.',
                $subject,
            ),
        ];
    }

    /** @return array<string, mixed> */
    public static function hintRecord(): array
    {
        return self::object([
            'id' => self::string(),
            'title' => self::string(),
            'category' => self::string('PHP, TypeScript, JavaScript, CSS, or General.'),
            'scope' => self::obliges('the whole hint'),
            'hints' => self::listOf(self::object([
                'text' => self::string('The statement itself. It reads the same on every version it holds for; the range is beside it, never inside it.'),
                'since' => ['type' => ['integer', 'null'], 'description' => 'First TYPO3 major this holds on. Null means as far back as this knowledge base reaches.'],
                'until' => ['type' => ['integer', 'null'], 'description' => 'Last TYPO3 major this holds on. Null means it still holds.'],
                'versions' => self::string('The same range as a sentence, empty when the statement is bound to nothing.'),
                'scope' => self::obliges('this statement'),
            ], ['text', 'since', 'until', 'versions', 'scope'])),
        ], ['id', 'title', 'category', 'scope', 'hints']);
    }

    /**
     * A hint named rather than quoted: what an answer says exists without
     * carrying it, and what typo3_hint_lookup takes as an id.
     *
     * @return array<string, mixed>
     */
    public static function hintReference(): array
    {
        return self::object([
            'id' => self::string('Ask for this hint outright by passing it as id.'),
            'title' => self::string(),
            'category' => self::string('PHP, TypeScript, JavaScript, CSS, or General.'),
        ], ['id', 'title', 'category']);
    }

    /**
     * A whole procedure named rather than handed over, the call that reads it,
     * and what the caller has to be doing for it to be worth reading.
     *
     * Two tools answer with one: the orientation call lists every page this
     * server carries, and a brief names the one the work it recognized belongs
     * to. A `typo3://guides` address reaches only a client that renders
     * resources, so what is named is the id `typo3_rule_lookup` takes —
     * `D-ANS-061`, `D-GUI-012`.
     *
     * The `when` is what makes a `documentId` a decision rather than a title:
     * six sessions read a page's name from four different surfaces and opened
     * none of them, and every account of why says the entry gave them nothing
     * to weigh. It is the document's own `whenToUse`, so the page and the
     * pointer to it cannot say different things.
     *
     * The `tool` is the other half of the same failure. The field is called
     * `guides`, the argument is a `documentId` and the call is
     * `typo3_rule_lookup`: no name joins the three, and a session that read the
     * array as data had the route only in the sentence above it — `D-GUI-012`.
     * A record beside `nextTool` says which tool to call, so this one does too.
     *
     * @return array<string, mixed>
     */
    public static function guideReference(): array
    {
        return self::object([
            'id' => self::string('What typo3_rule_lookup takes as documentId to return the whole document.'),
            'title' => self::string(),
            'when' => self::string('What the caller has to be doing for this page to be the one to read.'),
            'tool' => self::string('The tool that takes the id above and returns the page whole.'),
        ], ['id', 'title', 'when', 'tool']);
    }

    /**
     * A call to make next, and what makes it the right one.
     *
     * A brief ends on a list of them, and a commit draft carries the one that
     * owns the workflow it drafted for (`D-ANS-117`). The `when` is a fragment
     * read after the tool name, so it opens with the arguments to pass.
     *
     * @return array<string, mixed>
     */
    public static function nextTool(): array
    {
        return self::object([
            'tool' => self::string(),
            'when' => self::string('What to pass and why this call is the next one.'),
        ], ['tool', 'when']);
    }

    /**
     * A file the extension ships that core has stopped reading, or is stopping.
     *
     * Two tools answer with it: the extension answer for the extension the
     * caller named, and the orientation answer for the ones inside the
     * repository, which is what reaches a session that never makes the second
     * call — `D-ANS-009`.
     *
     * @return array<string, mixed>
     */
    public static function deprecatedFile(): array
    {
        return self::object([
            'file' => self::string('The file, relative to the extension. Not always a registration file: ext_icon.* and ext_typoscript_*.txt are read by nothing now, so they are a registration point nowhere and are checked here alone.'),
            'changelog' => self::string('The changelog entry, for typo3_changelog_lookup, which has the description and the migration whole.'),
            'predicate' => self::string('What the entry turns on, which is what holds here — shipping the file, and what stands beside it: what composer.json declares, or the file core reads before this one.'),
            'cost' => self::string('What it raises, from which version, and what the removal does instead.'),
        ], ['file', 'changelog', 'predicate', 'cost']);
    }

    /** @return array<string, mixed> */
    public static function testSuiteRecord(): array
    {
        return self::object([
            'suite' => self::string(),
            'command' => self::string('Full command, run from the core root.'),
            'runs' => [
                'type' => 'string',
                'enum' => ['check', 'change', 'git', 'unknown'],
                'description' => 'What running the command does to the checkout, read off the suite\'s body in Build/Scripts/runTests.sh rather than by running it. The values typo3_project_describe gives a declared command, plus one for the suites that run git. check: it reports and hands the files back as they were, so a task told not to change files can run it — installing its own node_modules or writing a cache is not a change. change: it rewrites files, generated or installed. git: it runs git over the working tree, so `git add *` stages what it finds, untracked files included, and a suite of this kind may discard uncommitted edits first. unknown: the body does not say, which is what a test suite is, because it runs the core\'s own code.',
            ],
            'targeted' => self::nullableString('Narrowed form for iterating on a single file or test. It can run differently from command — `-s cgl -n` reports where `-s cgl` rewrites — and runs above answers for command.'),
            'description' => self::string(),
            'whenToUse' => self::string(),
            'domains' => self::listOf(self::string()),
            'versions' => self::nullableString('The TYPO3 majors whose runTests.sh has this suite, where that is not all of them. Null means every covered version.'),
        ], ['suite', 'command', 'runs', 'targeted', 'versions']);
    }

    /**
     * What each word of a query reaches on its own, as a miss reports it.
     *
     * The same shape wherever it is answered, and which field carries it is
     * what says where it was counted: a number taken inside a filter reads as a
     * fact about the corpus otherwise, which is the miss `D-ANS-016` was
     * corrected for.
     *
     * @return array<string, mixed>
     */
    public static function termCounts(string $description): array
    {
        return self::listOf(self::object([
            'term' => self::string('The word, lowercased as it was searched for.'),
            'matchCount' => self::integer(),
        ], ['term', 'matchCount']), $description);
    }

    /**
     * The majors a catalog entry was verified on — the same since/until the
     * hints carry, so a client reads one model rather than two.
     *
     * @return array<string, mixed>
     */
    public static function verifiedOn(): array
    {
        return [
            'since' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major this entry starts holding at, or null when it holds on every covered version.'],
            'until' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major it stops holding after, or null when nothing has replaced it.'],
            'verifiedOn' => self::string('The same range as a sentence, empty when the entry holds on every covered version.'),
        ];
    }

    /** @return array<string, mixed> */
    public static function withheldComponents(): array
    {
        return self::listOf(self::object([
            'name' => self::string(),
            'title' => self::string(),
            'sassPaths' => self::listOf(self::string(), 'What to verify the entry against on the target version.'),
            'demoPath' => self::nullableString(),
        ] + self::verifiedOn(), ['name', 'title', 'verifiedOn']), 'Components this catalog has but was never verified on the target version. Left out of components rather than handed over — an empty answer here means "not verified where you are", not "does not exist".');
    }

    /** @return array<string, mixed> */
    public static function catalogProvenance(): array
    {
        return self::object([
            'repository' => self::string(),
            'branch' => self::string(),
            'version' => self::string('TYPO3 version of the snapshot.'),
            'commit' => self::string('Core revision the catalogs were taken from.'),
            'verifiedAt' => self::string(),
            'installedVersion' => self::nullableString('TYPO3 version of the installation this server was started in, where there is one. Null means there was nothing to compare the snapshot with.'),
            'skew' => self::nullableString('Set when that installation and the snapshot are different TYPO3 majors, and what to do about it. Null when they agree or nothing is known.'),
        ], ['branch', 'version', 'commit', 'verifiedAt'], 'The core revision behind catalog answers, and how it relates to the installation being read. A miss means "not in this snapshot".');
    }

    /**
     * @param array<string, mixed> $properties
     * @param array<int, string> $required
     * @return array<string, mixed>
     */
    public static function object(array $properties, array $required = [], string $description = ''): array
    {
        $schema = ['type' => 'object'];
        if ($properties !== []) {
            $schema['properties'] = $properties;
        }
        if ($required !== []) {
            $schema['required'] = $required;
        }
        if ($description !== '') {
            $schema['description'] = $description;
        }

        return $schema;
    }

    /**
     * @param array<string, mixed> $items
     * @return array<string, mixed>
     */
    public static function listOf(array $items, string $description = ''): array
    {
        $schema = ['type' => 'array', 'items' => $items];
        if ($description !== '') {
            $schema['description'] = $description;
        }

        return $schema;
    }

    /** @return array<string, mixed> */
    public static function string(string $description = ''): array
    {
        return $description === '' ? ['type' => 'string'] : ['type' => 'string', 'description' => $description];
    }

    /** @return array<string, mixed> */
    public static function nullableString(string $description = ''): array
    {
        $schema = ['type' => ['string', 'null']];
        if ($description !== '') {
            $schema['description'] = $description;
        }

        return $schema;
    }

    /** @return array<string, mixed> */
    public static function integer(string $description = ''): array
    {
        return $description === '' ? ['type' => 'integer'] : ['type' => 'integer', 'description' => $description];
    }
}
