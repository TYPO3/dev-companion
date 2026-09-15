<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Tool\Source;

/**
 * The record shapes several tools answer with, and the builders that write
 * them.
 *
 * A tool declares its own output schema. What lives here is what more than one
 * of them says. A knowledge match, a hint, a catalog entry, the reason an
 * installation did not answer. Schemas stay open (no additionalProperties:
 * false) so a new field is an addition rather than a break. Only fields that
 * are always present stand as required.
 */
final class Schema
{
    /**
     * The output schema of a tool that answers from the installation: either
     * the result it promises, or the unsupported answer in place of it.
     *
     * The two are alternatives and the schema says so. So a client that
     * validates structuredContent, which the specification tells it to, still
     * gets the full promise on a hit. As a promise rather than as a field that
     * might be there. oneOf also makes the two exclusive. An answer with both
     * is invalid, which is the shape this whole entry stands against.
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
     * The question had no answer here, and this is the whole answer.
     *
     * Present instead of the result, never beside it. A tool that cannot ask
     * states this and states nothing else. So there is no count to read as a
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
                'description' => 'no-installation: nothing to ask from here, and searched says where the discovery looked. '
                    . 'misconfigured: the caller named an installation the server could not use, so the discovery '
                    . 'searched nothing. installation-not-answering: the discovery found one and its console did '
                    . 'not answer. A stopped container or a database with no schema is that state, and it ends '
                    . 'without a reinstall.',
            ],
            'reason' => self::string('What stopped it, in the words the attempt produced.'),
            'repositoryState' => [
                'type' => ['string', 'null'],
                'enum' => ['installed', 'not-installed', 'undeclared', null],
                'description' => 'The state of the repository the caller stands in, which the cause does not say. '
                    . 'installed: packages sit below the root the discovery found, so the caller lacks no '
                    . 'install. not-installed: the repository declares TYPO3 and has no packages below it yet, so '
                    . 'this call answers once composer install has run. undeclared: nothing in the directories '
                    . 'the discovery walked declares TYPO3, so an install here answers nothing. Null where the '
                    . 'discovery looked at nothing: a named root the server could not use, or an entrypoint that '
                    . 'handed no directory in.',
            ],
            'diagnosis' => self::string('What the reason means where the message alone does not say it. A console that starts and then fails on a missing table has a database without a schema, not a broken installation. Empty where the server knows nothing beyond the reason.'),
            'searched' => self::listOf(self::string(), 'Every directory the discovery walked, in order. "Nothing found" and "the server started somewhere else" read the same, and only this list tells them apart. Empty where the discovery never ran.'),
            'misconfiguration' => self::nullableString('The setting the server could not use. Null where the caller set nothing.'),
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
     * `unsupported()` is what a question this server cannot take from where it
     * stands answers with, and it replaces the result. A manual, a tracker, a
     * review server and a registry are reachable from anywhere or from nowhere.
     * So those answer with a status beside the result and this object where the
     * status is `unavailable`.
     *
     * What varies per source is which causes it can have and how each one
     * reads, which is why the caller passes them. The enum is the keys and the
     * description is the sentences, in the caller's order.
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
                : 'Why the source answered nothing, where status says unavailable. Null otherwise.',
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
     * What a call to a source outside this process came back as. An answer, an
     * answer that is empty, or a source with no answer at all.
     *
     * @return array<string, mixed>
     */
    public static function answerStatus(): array
    {
        return ['type' => 'string', 'enum' => ['answered', 'empty', 'unavailable']];
    }

    /**
     * Which source answered this call. An answer that came from none of them is
     * not one of its cases. That is the unsupported case, and it replaces the
     * answer rather than labels it.
     *
     * The cases are the tool's own `answersFrom()`. So a tool with only one way
     * to answer says so instead of declares a fallback it does not have.
     * Sources that never label an answer stay out. A knowledge file and a
     * network service are what the whole tool reads, never one call.
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
            Source::Packages->value => 'packages: the server read the files the installed packages ship, because it '
                . 'could not ask the console. The answer misses the overrides that apply at runtime.',
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
                ? 'Which kind of work this answer is for. core: a patch to the TYPO3 core itself. project: the '
                    . 'site repository around an installation. extension: a package in it, a sitepackage or a '
                    . 'third-party one. uncertain: nothing in the call placed the work, and the answer is the '
                    . 'core\'s own.'
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
            'resource' => self::nullableString('The exact XLF resource the lookup restricted the result to. Null means the caller gave no usage context.'),
            'matchCount' => self::integer(),
            'matches' => self::listOf(self::knowledgeMatch()),
            'documents' => self::listOf(self::object([
                'id' => self::string(),
                'title' => self::string(),
                'topics' => self::listOf(self::string()),
            ], ['id', 'title', 'topics']), 'Documents in the knowledge base with the topics they cover. The lookup returns them when nothing matched.'),
            'elsewhere' => self::listOf(self::string(), 'Documents outside the searched ones that do match the query.'),
            'alsoInHints' => self::listOf(self::object([
                'id' => self::string(),
                'title' => self::string(),
            ], ['id', 'title']), 'Hints that match the same query. They are a second corpus, which typo3_hint_lookup searches, and it takes one of these ids.'),
        ], ['query', 'matchCount', 'matches']);
    }

    /** @return array<string, mixed> */
    public static function knowledgeMatch(): array
    {
        return self::object([
            'documentId' => self::string(),
            'title' => self::string('Title of the knowledge document.'),
            'uri' => self::string('The typo3://guides resource that holds the full document.'),
            'heading' => self::string('Heading of the matched section.'),
            'body' => self::string('The section as the file has it, format included.'),
            'versions' => self::string('The TYPO3 majors this section holds for, in words. Empty means every covered major, which is what a section that declares nothing says.'),
            'coverage' => ['type' => 'number', 'description' => 'Share of the query terms the section covers, 0 to 1. '
                . 'Zero where no search ranked this record, which is a page the caller named by documentId.'],
            'score' => self::integer('Weighted match score; headings weigh more than body text. Zero where no search '
                . 'ranked this record.'),
            'truncated' => ['type' => 'boolean', 'description' => 'Whether the lookup cut the body. Read the resource for the rest.'],
        ], ['documentId', 'title', 'uri', 'heading', 'body', 'coverage', 'score', 'truncated']);
    }

    /**
     * Whom something obliges, where that is not everyone.
     *
     * @return array<string, mixed>
     */
    public static function obliges(string $subject): array
    {
        return [
            'type' => ['string', 'null'],
            'enum' => ['core', 'project', 'extension', null],
            'description' => sprintf(
                'Which kind of work %s obliges. "core" means a condition of a patch to the TYPO3 core and a '
                . 'convention anywhere else. The backend\'s own design system, the changelog artifact and the paths '
                . 'of the mono repository are that case. "project" and "extension" are the mirror. They say what '
                . 'the repository around an installation, or a package on its own, has to do, and what is context '
                . 'inside the core. Null, the ordinary case, means it holds wherever somebody writes TYPO3: an '
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
                'versions' => self::string('The same range as a sentence, empty when the statement binds to no version.'),
                'scope' => self::obliges('this statement'),
            ], ['text', 'since', 'until', 'versions', 'scope'])),
        ], ['id', 'title', 'category', 'scope', 'hints']);
    }

    /**
     * A hint by name rather than in quotes. What an answer says exists without
     * a copy of it, and what typo3_hint_lookup takes as an id.
     *
     * @return array<string, mixed>
     */
    public static function hintReference(): array
    {
        return self::object([
            'id' => self::string('Pass this as id to ask for the hint outright.'),
            'title' => self::string(),
            'category' => self::string('PHP, TypeScript, JavaScript, CSS, or General.'),
        ], ['id', 'title', 'category']);
    }

    /**
     * A whole procedure by name rather than in a handover. The call that reads
     * it, and what the caller has to do for it to be worth a read.
     *
     * Two tools answer with one, the orientation call and a brief. A
     * `typo3://guides` address reaches only a client that renders resources, so
     * the name is the id `typo3_rule_lookup` takes, `D-ANS-061`. The `when` is
     * what makes a `documentId` a decision rather than a title, because six
     * sessions read a page's name and opened none of them. It is the document's
     * own `whenToUse`. The `tool` is the other half, because no name joins the
     * field, the argument and the call, `D-GUI-012`.
     *
     * @return array<string, mixed>
     */
    public static function guideReference(): array
    {
        return self::object([
            'id' => self::string('What typo3_rule_lookup takes as documentId to return the whole document.'),
            'title' => self::string(),
            'when' => self::string('What the caller has to do for this page to be the one to read.'),
            'scope' => [
                'type' => 'string',
                'enum' => array_map(static fn(Scope $scope): string => $scope->value, Scope::ofKnowledge()),
                'description' => 'Which kind of work this page serves. core: a patch to the TYPO3 core repository. '
                    . 'project: the site repository around an installation. extension: a package in it. any: all '
                    . 'three. It stands here because it decides whether to open the page at all. A caller '
                    . 'that reads it out of the id parses a path segment — D-ANS-150.',
            ],
            'tool' => self::string('The tool that takes the id above and returns the page whole.'),
        ], ['id', 'title', 'when', 'scope', 'tool']);
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
     * A file the extension ships that core has stopped to read, or stops soon.
     *
     * Two tools answer with it. The extension answer for the extension the
     * caller named, and the orientation answer for the ones inside the
     * repository. The second is what reaches a session that never makes the
     * second call, `D-ANS-009`.
     *
     * @return array<string, mixed>
     */
    public static function deprecatedFile(): array
    {
        return self::object([
            'file' => self::string('The file, relative to the extension. Not always a registration file: nothing reads ext_icon.* and ext_typoscript_*.txt now, so they are a registration point nowhere and this check alone covers them.'),
            'changelog' => self::string('The changelog entry, for typo3_changelog_lookup, which has the description and the migration whole.'),
            'predicate' => self::string('What the entry turns on, which is what holds here. That is the shipped file and what stands beside it: what composer.json declares, or the file the core reads before this one.'),
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
                'description' => 'What the command does to the checkout, read off the suite\'s body in Build/Scripts/runTests.sh rather than measured by a run. The values are what typo3_project_describe gives a declared command, plus one for the suites that run git. check: it reports and hands the files back as they were, so a task told not to change files can run it. An install of its own node_modules or a written cache is not a change. change: it rewrites files, generated or installed. git: it runs git over the working tree, so `git add *` stages what it finds, untracked files included. A suite of this kind may discard uncommitted edits first. unknown: the body does not say. A test suite is that case, because it runs the core\'s own code.',
            ],
            'targeted' => self::nullableString('The narrowed form for one file or one test. It can run differently from command: `-s cgl -n` reports where `-s cgl` rewrites, and runs answers for command.'),
            'description' => self::string(),
            'whenToUse' => self::string(),
            'domains' => self::listOf(self::string()),
            'versions' => self::nullableString('The TYPO3 majors whose runTests.sh has this suite, where that is not all of them. Null means every covered version.'),
        ], ['suite', 'command', 'runs', 'targeted', 'versions']);
    }

    /**
     * What each word of a query reaches on its own, as a miss reports it.
     *
     * The same shape wherever it answers, and which field carries it is what
     * says where the count ran. A number from inside a filter reads as a fact
     * about the corpus otherwise, which is the miss `D-ANS-016` corrected.
     *
     * @return array<string, mixed>
     */
    public static function termCounts(string $description): array
    {
        return self::listOf(self::object([
            'term' => self::string('The word, lowercased as the search used it.'),
            'matchCount' => self::integer(),
        ], ['term', 'matchCount']), $description);
    }

    /**
     * A reference to a Forge issue, in the one shape every answer carries it in.
     *
     * Four sites named the same five fields: a relation, an issue the prose
     * cites, and the issues a change's trailers name. A caller that reads two
     * shapes for one thing reads the second one wrong.
     *
     * The two that take an argument are the two that differ, which issue this
     * is and what its state says here. The three that do not are the same
     * sentence at every site.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function issueReference(string $issue, string $status): array
    {
        return [
            'issue' => self::integer($issue),
            'subject' => self::string('What the issue is about, so a caller judges it without a read. Empty where the tracker did not answer the one call that fills the whole set.'),
            'tracker' => self::string('Bug, Feature, Task.'),
            'status' => self::string($status),
            'url' => self::string('Where a person reads it.'),
        ];
    }

    /**
     * A reference to a change on the review server, in one shape.
     *
     * `typo3_gerrit_lookup`'s cherry-pick provenance is not this: it carries a
     * patch set where this carries a state, which is a different claim about a
     * different thing.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function changeReference(string $status): array
    {
        return [
            'change' => self::integer('The change number on review.typo3.org, which is what typo3_gerrit_lookup takes as change.'),
            'status' => self::string($status),
            'url' => self::string('Where a person reads the change.'),
        ];
    }

    /**
     * The majors a catalog entry has a check on, the same since/until the hints
     * carry, so a client reads one model rather than two.
     *
     * @return array<string, mixed>
     */
    public static function verifiedOn(): array
    {
        return [
            'since' => ['type' => ['integer', 'null'], 'description' => 'The first TYPO3 major this entry holds on, or null when it holds on every covered version.'],
            'until' => ['type' => ['integer', 'null'], 'description' => 'The last TYPO3 major this entry holds on, or null when nothing has replaced it.'],
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
        ] + self::verifiedOn(), ['name', 'title', 'verifiedOn']), 'Components this catalog has and nobody verified on the target version. The answer leaves them out of components. An empty answer here means "not verified where you are", not "does not exist".');
    }

    /** @return array<string, mixed> */
    public static function catalogProvenance(): array
    {
        return self::object([
            'repository' => self::string(),
            'branch' => self::string(),
            'version' => self::string('TYPO3 version of the snapshot.'),
            'commit' => self::string('The core revision the catalogs come from.'),
            'verifiedAt' => self::string(),
            'verifyCommand' => self::string('The command that re-checks the snapshot against a core checkout.'),
            'installedVersion' => self::nullableString('TYPO3 version of the installation this server started in, where there is one. Null means nothing to compare the snapshot with.'),
            'skew' => self::nullableString('What to do when that installation and the snapshot are different TYPO3 majors. Null when they agree or the server knows nothing.'),
        ], ['branch', 'version', 'commit', 'verifiedAt'], 'The core revision behind catalog answers, and how it relates to the installation the server reads. A miss means "not in this snapshot".');
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
