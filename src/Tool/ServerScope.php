<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Server\ExcludedTools;

/**
 * What this server covers, what it deliberately does not, and which tool to
 * call when.
 */
final class ServerScope extends ReadOnlyTool
{
    /**
     * The parts of this answer a caller can ask for by name, each with what it
     * holds, in the order the payload carries them.
     *
     * The names are the payload's own field names. So a caller that asks again
     * for one of them names the field it already has rather than a word
     * invented for the parameter. What is not here never stays back: the
     * purpose, the initialize instructions and what the caller excluded,
     * `R-SCO-009`.
     *
     * @var array<string, string>
     */
    private const SECTIONS = [
        'covers' => 'what is covered, at which depth, and by which tool',
        'doesNotCover' => 'what this server deliberately does not answer, and what to do instead',
        'checkoutDiscovery' => 'what to establish in the checkout before the work, and how',
        'routing' => 'which tool to call when',
        'versions' => 'the TYPO3 versions this knowledge binds to',
        'answersFrom' => 'which source answers which tool, in the state this machine is in',
        'installation' => 'which installation is being read, and whether its console answers',
    ];

    public static function name(): string
    {
        return 'typo3_server_scope';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge, Source::Installation];
    }

    public static function description(): string
    {
        return 'Orientation for this server: what it covers and at which depth, what it deliberately does not cover, and which tool to call when. Start here when it is unclear whether this server can answer a question at all, or which of the lookups is the right one. It answers whole, which is the largest answer here. Where you know which part you need, say whether an installation and its console answer, name it in sections.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'sections' => [
                    'type' => 'array',
                    // The field names rather than a vocabulary of its own, and
                    // without a repeat of what each one holds. The output
                    // schema says that per field, and every answer says it
                    // again under withheld for the parts that are not in it.
                    'items' => ['type' => 'string', 'enum' => array_keys(self::SECTIONS)],
                    'minItems' => 1,
                    'description' => 'The parts of the answer to return, named by the fields they arrive in. '
                        . 'Omit it for all of them, which is the answer for a caller that does not yet know what '
                        . 'it can ask this server. Whatever you name, the answer keeps the purpose, the '
                        . 'instructions clients receive at initialize time, and the tools your list lacks. '
                        . 'The withheld field says what each part left out would have held.',
                ],
            ],
            'additionalProperties' => false,
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'purpose' => Schema::string('What this server is for.'),
            'instructions' => Schema::string('The boundary statement clients receive at initialize time.'),
            'covers' => Schema::listOf(Schema::object([
                'topic' => Schema::string(),
                'depth' => Schema::string('How deep the coverage of the topic goes.'),
                'tools' => Schema::listOf(Schema::string()),
                'source' => Schema::string('Knowledge file or typo3:// resource behind the topic.'),
                'scope' => ['type' => 'string', 'enum' => ['core', 'project', 'extension', 'any'], 'description' => 'Which kind of work the answers are for. core: the contribution process and the scripts of that repository. any: a convention that holds wherever somebody writes TYPO3.'],
            ], ['topic', 'depth', 'tools', 'source', 'scope'])),
            'doesNotCover' => Schema::listOf(Schema::object([
                'topic' => Schema::string(),
                'why' => Schema::string(),
                'instead' => Schema::string('What to do instead of a call to this server.'),
            ], ['topic', 'why', 'instead'])),
            'checkoutDiscovery' => Schema::listOf(Schema::object([
                'establish' => Schema::string(),
                'how' => Schema::string(),
            ], ['establish', 'how'])),
            'routing' => Schema::listOf(Schema::object([
                'when' => Schema::string(),
                'call' => Schema::string(),
            ], ['when', 'call'])),
            'versions' => Schema::listOf(Schema::object([
                'major' => Schema::integer(),
                'branch' => Schema::string('The branch this server verifies that line against.'),
                'status' => Schema::string('lts, stable, or development.'),
            ], ['major', 'branch', 'status']), 'The TYPO3 versions the knowledge binds to. The answer leaves out a statement outside a range when it knows a target version.'),
            'answersFrom' => Schema::listOf(Schema::object([
                'source' => Schema::string('installation, packages, knowledge, network or checkout.'),
                'meaning' => Schema::string('What that source is, and what it cannot answer.'),
                'tools' => Schema::listOf(Schema::string(), 'The offered tools it can answer. A tool with two sources stands under both.'),
            ], ['source', 'meaning', 'tools']), 'Which tools are worth a call in the state this machine is in. Where nothing runs, the tools answer from knowledge and packages alone. Every tool states its own sources at the foot of its description; this groups them the other way round.'),
            'excludedTools' => Schema::object([
                'names' => Schema::listOf(Schema::string(), 'The tools that are really gone, and the only reason the list is ever shorter than the documented one. Empty unless the variable has a value.'),
                'ignored' => Schema::listOf(Schema::string(), 'Names in the variable that took nothing away. No tool answers to the name, or it is one of the three this server offers whatever the variable says. Each of them is in the tool list. Absent means nothing to report, which is the ordinary case.'),
                'variable' => Schema::string('Environment variable that names them.'),
            ], ['names', 'variable']),
            'installation' => Schema::object([
                'found' => ['type' => 'boolean', 'description' => 'Whether there is an installation to read at all.'],
                'root' => Schema::nullableString('Absolute path of the installation.'),
                'kind' => Schema::nullableString('core-checkout or composer-project.'),
                'via' => Schema::nullableString('How the server found it: discovery (a walk up from the start directory) or environment (TYPO3_DEV_COMPANION_ROOT names it).'),
                'startedFrom' => Schema::nullableString('Where the search started, or the configured value.'),
                'searched' => Schema::listOf(Schema::string(), 'The directories the search walked. A failure here means a layout the server cannot read or a server started in the wrong place; this says which.'),
                'packageCount' => Schema::integer('TYPO3 packages found in it.'),
                'misconfiguration' => Schema::nullableString('Set when the server could not follow a configured value. Nothing falls back to a discovered installation.'),
                'console' => Schema::object([
                    'reachable' => ['type' => 'boolean', 'description' => 'False means every installation-backed tool answers with unsupported in place of its result.'],
                    'via' => Schema::nullableString('ddev, php, or override.'),
                    'php' => Schema::nullableString('The PHP version it runs on, where the server knows it.'),
                    'command' => Schema::nullableString('The invocation, as the server runs it.'),
                    'reason' => Schema::nullableString('Why it cannot run. Null when it can.'),
                    'caveat' => Schema::nullableString('What limits the console the server found. An interpreter of this machine answers for a project whose containers are stopped. That reaches what TYPO3 assembles from its own files and not the services the project\'s own runtime brings. Null when nothing limits it.'),
                ], ['reachable']),
                'settings' => Schema::object([
                    'root' => Schema::string('Environment variable that names the installation root.'),
                    'console' => Schema::string('Environment variable that names the console command.'),
                ], ['root', 'console']),
            ], ['found', 'searched', 'packageCount', 'console']),
            'withheld' => Schema::listOf(Schema::object([
                'section' => Schema::string(),
                'holds' => Schema::string('What that part of the answer would have carried.'),
            ], ['section', 'holds']), 'The parts this call did not ask for. A field named here is absent from this answer rather than empty, so a narrowed answer does not read as the whole one. Empty where sections was not passed, which is the whole orientation.'),
        ], ['purpose', 'excludedTools', 'withheld']);
    }

    public static function answer(array $args): ToolResult
    {
        $sections = self::selected($args);
        $coverage = Coverage::offered();

        $lines = [];
        if (ExcludedTools::all() !== []) {
            // Before the purpose rather than after it. The purpose describes
            // the whole server. A client that reads what it holds first and
            // that a tool is absent second hears a claim and then a correction.
            $lines[] = self::exclusionLine();
            $lines[] = '';
        }
        if (self::ignored() !== []) {
            $lines[] = self::ignoredLine();
            $lines[] = '';
        }

        $lines[] = $coverage['purpose'];

        if (in_array('covers', $sections, true)) {
            $lines[] = '';
            $lines[] = 'Covered, and how deeply. Each topic says which kind of work its answers are for: core is the '
                . 'contribution process and the scripts that belong to that repository, any is a convention that '
                . 'holds wherever TYPO3 is written. Where the source names the installation, the answer is read '
                . 'from the one this server was started in rather than from any snapshot.';
            foreach ($coverage['covers'] as $entry) {
                $lines[] = '## ' . $entry['topic'];
                $lines[] = $entry['depth'];
                $lines[] = 'Tools: ' . implode(', ', $entry['tools']);
                $lines[] = 'Source: ' . $entry['source'] . ' (' . $entry['scope']->value . ')';
            }
        }

        $lines[] = '';
        // Stated here as well as in the initialize instructions. This is the
        // tool an agent calls when it does not know how to use the server. A
        // client is free to show no instructions at all.
        $lines[] = 'Query this server in English, whatever language you are speaking with the user. Its '
            . 'knowledge is written in English and its matching is lexical, so a query in another language '
            . 'reaches only the words the two happen to share and otherwise comes back empty.';

        if (in_array('versions', $sections, true)) {
            $lines[] = '';
            $lines[] = 'Versions this knowledge binds to:';
            foreach (Versions::covered() as $version) {
                $lines[] = '- TYPO3 v' . $version['major'] . ' (' . $version['branch'] . ', ' . $version['status'] . ')';
            }
            $lines[] = 'A statement that does not hold on all of them carries the range it holds on. Pass targetVersion '
                . 'to have the ones that do not apply left out; without it, the version of the installation being read '
                . 'decides, and where there is none nothing is filtered.';
        }

        if (in_array('doesNotCover', $sections, true)) {
            $lines[] = '';
            // What the list is worth read from the other side. A caller cannot
            // tell a boundary from a gap by the size of an answer. The two ask
            // for opposite reactions: leave, or say what was absent.
            $lines[] = 'Deliberately not covered — and this list is the boundary: a subject that is not on it is in '
                . 'scope, so a thin answer to it is a gap in the knowledge base rather than a limit of it.'
                . (Channel::isAvailable() ? ' Record one with typo3_feedback_record instead of going elsewhere.' : '');
            foreach ($coverage['doesNotCover'] as $entry) {
                $lines[] = '## ' . $entry['topic'];
                $lines[] = $entry['why'];
                $lines[] = 'Instead: ' . $entry['instead'];
            }
        }

        if (in_array('routing', $sections, true)) {
            $lines[] = '';
            $lines[] = 'Which tool to call when:';
            foreach ($coverage['routing'] as $entry) {
                $lines[] = '- ' . $entry['when'] . ' → ' . $entry['call'];
            }
        }

        // What the installation can answer is a different question from whether
        // one turned up. The answer is actionable often enough to belong here
        // rather than in a failed tool call. Read once, and both halves of the
        // answer come from these locals. `reason()` and `caveat()` each
        // re-enter `resolve()`, which no longer remembers a failed or caveated
        // resolution (`R-DIS-009`). So every read of the console state pays a
        // `ddev describe -j` of its own while the project is down. This answer
        // made six of them, 2.648s against `.environments/e-site-13.4` with its
        // project down on 2026-08-04. Two remain, 0.869s there. A failed
        // resolution carries no caveat and a successful one carries no reason,
        // so only one of the two runs. Neither is reachable from outside
        // `Typo3Cli` without a second resolution. Nothing changes where the
        // resolution stays in memory: one describe, 0.002s on the second call.
        // No resolution at all where the caller did not ask for this section.
        // The two describes are the one part of this answer that costs seconds
        // rather than bytes.
        $wanted = in_array('installation', $sections, true);
        $console = $wanted ? Typo3Cli::resolve() : null;
        $reason = $wanted && $console === null ? Typo3Cli::reason() : '';
        $caveat = $wanted && $console !== null ? Typo3Cli::caveat() : '';
        if ($wanted) {
            $lines[] = '';
            array_push($lines, ...self::installationLines($console, $reason, $caveat));
        }

        $lines[] = '';
        $lines[] = 'Every lookup and guide is read-only. typo3_documentation_lookup reads the official, versioned '
            . 'manuals at docs.typo3.org; apart from that and the installation named above, nothing is fetched, '
            . 'executed, or looked up online.';
        if (Channel::isAvailable()) {
            // The one write stands next to the read-only claim, not after it. A
            // blanket "everything is read only" followed by a tool that creates
            // a file contradicts both the annotations and the behaviour.
            $lines[] = 'The one exception is typo3_feedback_record, this server\'s only write: '
                . 'it creates a new markdown feedback under feedback/ and touches nothing else. '
                . 'Missing something that belongs here? Leave feedback about it.';
        }

        if (in_array('answersFrom', $sections, true)) {
            $lines[] = '';
            $lines[] = 'Where the answers come from, which is what says whether a question can be asked at all right '
                . 'now. Every tool states the same thing at the foot of its own description.';
            foreach (self::answersFromReport() as $entry) {
                $lines[] = '## Answers from ' . $entry['source'];
                $lines[] = $entry['meaning'];
                $lines[] = 'Tools: ' . implode(', ', $entry['tools']);
            }
        }

        $withheld = self::withheld($sections);
        if ($withheld !== []) {
            $lines[] = '';
            $lines[] = 'Left out, because this call named sections: ' . implode(', ', array_column($withheld, 'section'))
                . '. Ask again naming those, or call this tool with no arguments for the whole orientation.';
        }

        return ToolResult::create(implode("\n", $lines), self::payload($coverage, $sections, $console, $reason, $caveat));
    }

    /**
     * Which installation the server reads, and what it can answer.
     *
     * It is the one thing a caller cannot check for itself. A read of the wrong
     * one would be worse than a read of none. So it stands here, with where the
     * search started.
     *
     * @param array{command: array<int, string>, via: string, php: string}|null $console
     * @return list<string>
     */
    private static function installationLines(?array $console, string $reason, string $caveat): array
    {
        $lines = [];
        $instance = Instance::describe();
        if ($instance === null) {
            $lines[] = 'No TYPO3 installation was found from the directory this server was started in, so every answer '
                . 'comes from the bundled knowledge base alone. Questions about what is registered in an '
                . 'installation — which icon identifiers exist, which labels — cannot be answered here.';
            if (Instance::searched() !== []) {
                // Where it looked is the difference between "this layout is
                // unreadable" and "the client started the server somewhere
                // else". The caller can check neither unaided.
                $lines[] = 'Looked in: ' . implode(', ', Instance::searched())
                    . ' — none of them declares a TYPO3 core checkout or holds Composer metadata with TYPO3 packages in it.';
            }
            $lines[] = sprintf(
                'Naming it outright is the way out: set %s to the installation root, and %s to the command that '
                . 'reaches its console where that is not a path this server would find on its own.',
                Instance::ROOT_VARIABLE,
                Typo3Cli::CONSOLE_VARIABLE,
            );
        } else {
            $lines[] = sprintf(
                'Found the TYPO3 installation at %s (%s, %s, from %s), which holds %d packages. '
                . 'If that is not the installation you are working on, this server was started in the wrong '
                . 'directory — or set %s to the one you mean.',
                $instance['root'],
                $instance['kind'],
                $instance['via'] === Instance::VIA_ENVIRONMENT
                    ? 'named by ' . Instance::ROOT_VARIABLE
                    : 'found by walking up',
                $instance['startedFrom'],
                count(Instance::packages()),
                Instance::ROOT_VARIABLE,
            );
        }
        if (Instance::misconfiguration() !== '') {
            $lines[] = 'The configuration says otherwise and could not be followed: '
                . Instance::misconfiguration() . '.';
        }

        if ($instance !== null && $console === null) {
            $lines[] = 'Its console cannot be run right now, so questions that only the installation can answer — which '
                . 'labels exist, which backend modules are registered — have no answer here: ' . $reason . '. '
                . 'Where the command that would work is known, ' . Typo3Cli::CONSOLE_VARIABLE
                . ' states it, for example "ddev exec .build/bin/typo3".';
        }
        if ($instance !== null && $console !== null && $console['via'] === Typo3Cli::VIA_OVERRIDE) {
            $lines[] = sprintf(
                'Its console is invoked as "%s", which %s states, so those answers come from the installation '
                . 'itself rather than from a bundled snapshot.',
                implode(' ', $console['command']),
                Typo3Cli::CONSOLE_VARIABLE,
            );
        }
        if ($instance !== null && $console !== null && $console['via'] !== Typo3Cli::VIA_OVERRIDE) {
            $lines[] = sprintf(
                'Its console is reachable via %s on PHP %s, so those answers come from the installation itself '
                . 'rather than from a bundled snapshot.',
                $console['via'],
                $console['php'] === '' ? 'an unreported version' : $console['php'],
            );
        }
        if ($instance !== null && $caveat !== '') {
            $lines[] = 'Reachable is not the same as ready here: ' . $caveat . '.';
        }

        return $lines;
    }

    /**
     * The answer as data, with the sections this call asked for.
     *
     * A section left out is absent rather than empty. An empty `covers` reads
     * as a server that covers nothing, which is the failure
     * `installationReport()` below stands against in its own field. What names
     * the difference is `withheld`, which every answer carries.
     *
     * @param array{purpose: string, instructions: string, covers: array<int, mixed>, doesNotCover: array<int, mixed>, checkoutDiscovery: array<int, mixed>, routing: array<int, mixed>} $coverage
     * @param list<string> $sections
     * @param array{command: array<int, string>, via: string, php: string}|null $console
     * @return array<string, mixed>
     */
    private static function payload(
        array $coverage,
        array $sections,
        ?array $console,
        string $reason,
        string $caveat,
    ): array {
        $wanted = array_flip($sections);

        return [
            'purpose' => $coverage['purpose'],
            'instructions' => $coverage['instructions'],
            ...array_intersect_key([
                'covers' => $coverage['covers'],
                'doesNotCover' => $coverage['doesNotCover'],
                'checkoutDiscovery' => $coverage['checkoutDiscovery'],
                'routing' => $coverage['routing'],
                'versions' => Versions::covered(),
            ], $wanted),
            'excludedTools' => [
                'names' => ExcludedTools::all(),
                'ignored' => self::ignored(),
                'variable' => ExcludedTools::VARIABLE,
            ],
            ...isset($wanted['answersFrom']) ? ['answersFrom' => self::answersFromReport()] : [],
            ...isset($wanted['installation'])
                ? ['installation' => self::installationReport($console, $reason, $caveat)]
                : [],
            'withheld' => self::withheld($sections),
        ];
    }

    /**
     * The sections this call asked for, in the order the answer carries them.
     *
     * No name is the whole answer. That is the default because this is the tool
     * for a caller who does not yet know what the server covers. One that
     * cannot name the part it wants chooses in its least informed moment, which
     * is `D-ANS-087`. A caller that can name one has the question this tool was
     * too large for.
     *
     * @param array<string, mixed> $args
     * @return list<string>
     */
    private static function selected(array $args): array
    {
        $named = is_array($args['sections'] ?? null) ? $args['sections'] : [];
        if ($named === []) {
            return array_keys(self::SECTIONS);
        }

        return array_values(array_filter(
            array_keys(self::SECTIONS),
            static fn(string $section): bool => in_array($section, $named, true),
        ));
    }

    /**
     * What this call did not ask for, so a narrow answer cannot pass as the
     * whole one.
     *
     * @param list<string> $sections
     * @return list<array{section: string, holds: string}>
     */
    private static function withheld(array $sections): array
    {
        $withheld = [];
        foreach (self::SECTIONS as $section => $holds) {
            if (!in_array($section, $sections, true)) {
                $withheld[] = ['section' => $section, 'holds' => $holds];
            }
        }

        return $withheld;
    }

    /**
     * The offered tools grouped by what can answer them.
     *
     * Grouped rather than listed per tool, because the question it is here for
     * is about the state of the machine and not about one tool. Nothing is up,
     * so what is still worth a call. A tool that answers from two sources
     * stands under both, which is the answer to that question for it.
     *
     * @return array<int, array{source: string, meaning: string, tools: array<int, string>}>
     */
    private static function answersFromReport(): array
    {
        $report = [];
        foreach (Source::cases() as $source) {
            $tools = [];
            foreach (Registry::definitions() as $definition) {
                if (in_array($source->value, $definition['answersFrom'], true)) {
                    $tools[] = $definition['name'];
                }
            }
            if ($tools !== []) {
                $report[] = ['source' => $source->value, 'meaning' => $source->meaning(), 'tools' => $tools];
            }
        }

        return $report;
    }

    /**
     * Which tools the caller asked to have left out.
     *
     * A shorter tool list than the documentation describes otherwise looks like
     * a broken server, and the caller has no way to check. It sees the list it
     * got and nothing else.
     */
    private static function exclusionLine(): string
    {
        return sprintf(
            '%s %s missing from the tool list, and so is every entry below that routed to one of them, because %s '
            . 'asked for that. Unset it to be offered them again. Nothing else here is withheld: what an answer is '
            . 'worth outside the core is stated per topic below, and every prose document is readable as a '
            . 'typo3://guides resource whatever the tool list holds.',
            implode(', ', ExcludedTools::all()),
            count(ExcludedTools::all()) === 1 ? 'is' : 'are',
            ExcludedTools::VARIABLE,
        );
    }

    /**
     * The names in the variable that took nothing away, in one list.
     *
     * The two reasons are one fact to a client: the tool is in the list it got.
     * Which of the two it is says what somebody has to change, and that is what
     * the sentence below carries. A startup warning on stderr says it too, and
     * a client is free to show that to nobody.
     *
     * @return array<int, string>
     */
    private static function ignored(): array
    {
        return [...ExcludedTools::unknown(), ...ExcludedTools::offeredAnyway()];
    }

    private static function ignoredLine(): string
    {
        $unknown = ExcludedTools::unknown();
        $offeredAnyway = ExcludedTools::offeredAnyway();
        $reasons = [];
        if ($unknown !== []) {
            $reasons[] = implode(', ', $unknown) . ' — no tool of this server answers to that name';
        }
        if ($offeredAnyway !== []) {
            $reasons[] = implode(', ', $offeredAnyway)
                . ' — offered whatever the variable says, because a client that lost it could not tell '
                . 'a configured server from a broken one, and because the feedback channel is how a session '
                . 'hands back what it found';
        }

        return sprintf(
            '%s names %s, which took nothing away and %s in the tool list you were handed: %s.',
            ExcludedTools::VARIABLE,
            implode(', ', self::ignored()),
            count(self::ignored()) === 1 ? 'is' : 'are',
            implode('; ', $reasons),
        );
    }

    /**
     * The installation diagnostic as data.
     *
     * It used to be in the text alone, and a client that renders
     * structuredContent and drops the text block never saw it. What the caller
     * got instead was five tools that answered {"matchCount": 0, "answeredBy":
     * "nothing"}. That looks like a registry that really is empty, and read as
     * one. An extension with forty registered icons came back as one that
     * registers none, twice.
     *
     * The console state comes in rather than reads again, which is half of the
     * cost measured above. It is also the half that could disagree. Each of the
     * two resolved for itself. So a project that came up between them left the
     * text and the data of one answer at odds.
     *
     * @param array{command: array<int, string>, via: string, php: string}|null $console
     * @return array<string, mixed>
     */
    private static function installationReport(?array $console, string $reason, string $caveat): array
    {
        $instance = Instance::describe();

        return [
            'found' => $instance !== null,
            'root' => $instance['root'] ?? null,
            'kind' => $instance['kind'] ?? null,
            'via' => $instance['via'] ?? null,
            'startedFrom' => $instance['startedFrom'] ?? null,
            'searched' => Instance::searched(),
            'packageCount' => count(Instance::packages()),
            'misconfiguration' => Instance::misconfiguration() === '' ? null : Instance::misconfiguration(),
            'console' => [
                'reachable' => $console !== null,
                'via' => $console['via'] ?? null,
                'php' => ($console['php'] ?? '') === '' ? null : $console['php'],
                'command' => $console === null ? null : implode(' ', $console['command']),
                'reason' => $console === null ? $reason : null,
                // Reachable and ready are two questions, and the second one has
                // its own answer. A console reached through an interpreter on
                // this machine while the project's containers are down runs,
                // and runs outside the runtime the project declares.
                'caveat' => $caveat === '' ? null : $caveat,
            ],
            'settings' => [
                'root' => Instance::ROOT_VARIABLE,
                'console' => Typo3Cli::CONSOLE_VARIABLE,
            ],
        ];
    }
}
