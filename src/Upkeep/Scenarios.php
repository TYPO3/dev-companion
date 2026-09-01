<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * Reads scenarios/ back as data, and holds a recorded forward run to the
 * scenario it says it is a run of.
 *
 * Two kinds live there and only one of them is run forward. An open forward
 * review below scenarios/forward/ asks for a review of the repository and
 * nothing more, carries a `Status today`, and is what a recorded run answers. A
 * targeted contract case below scenarios/contracts/ names one task shape so its
 * routing can be held still, carries a `Contract` state instead, and is read for
 * inspection only. Both are the same file shape, which is why they parse
 * through the same code and differ in one label.
 *
 * A scenario is prose because it is written for a person: a prompt in the words
 * a user would use, and the criteria somebody has to judge an answer against.
 * Nothing here changes that. The prose stays the only copy of the prompt and of
 * the criteria — this class parses it rather than restating it, so a criterion
 * cannot be edited in one place and stay true in another.
 *
 * What the prose cannot hold is the run. A run happens in a client, against one
 * state of this server, and what it establishes is a judgment per criterion.
 * That judgment used to live in the head of whoever ran it, which is why a
 * recorded status drifts: the scenario says `covered` long after the run that
 * earned it stopped being true, and nothing says so out loud.
 *
 * So a run is one file below scenarios/runs/, and it holds only what the run
 * adds: where it ran, against which server, which skills the session activated
 * and which tools it called with what, and one judgment with evidence per
 * criterion. Two things follow from that:
 *
 * - The verdict is derived, never written down. All criteria met and no failure
 *   condition hit is `covered`; some is `partial`; none is `gap`. A verdict a
 *   run could state for itself would be the one field nobody could check.
 * - The run carries a digest of the criteria it was judged against. Edit the
 *   prompt or a criterion and the digest stops matching, which is the run
 *   saying it predates the thing it claims to answer — rather than silently
 *   answering the old question.
 */
final class Scenarios
{
    /**
     * The vocabularies a scenario is written in, read out of the tables that
     * define them: the environments in scenarios/readme.md, the marks a forward
     * review carries in scenarios/forward/readme.md, and the contract states in
     * scenarios/contracts/readme.md.
     *
     * Each is defined where somebody writing that kind of case reads it. A copy
     * here would be the second definition, and the one that goes stale is always
     * the one nobody is reading — so this looks through every readme below
     * scenarios/ for the column rather than naming one file.
     *
     * @return array<int, string>
     */
    public static function vocabulary(string $column): array
    {
        $codes = [];
        foreach (self::documents(self::directory()) as $path) {
            if (basename($path) !== 'readme.md') {
                continue;
            }

            $inTable = false;
            foreach (preg_split('/\R/', (string) file_get_contents($path)) ?: [] as $line) {
                if (!str_starts_with(trim($line), '|')) {
                    if ($inTable) {
                        break;
                    }

                    continue;
                }

                // Read as cells rather than as the text of a row: a table is
                // padded to the width of its widest cell, so where the column
                // stands is not where its name ends — `D-DOC-001`.
                $first = Wrap::cells($line)[0];
                if (!$inTable) {
                    $inTable = $first === $column;

                    continue;
                }
                if (preg_match('/^`([^`]+)`$/', $first, $matches) === 1) {
                    $codes[] = $matches[1];
                }
            }
        }

        return $codes;
    }

    /**
     * Every open forward review, keyed and sorted by id. These are the ones a
     * recorded run answers.
     *
     * @return array<string, array{id: string, title: string, file: string, environment: string, status: string, requirements: array<int, string>, heldBy: string, prompt: string, needs: array<int, string>, outcomes: array<int, string>, failures: array<int, string>, criteria: string}>
     */
    public static function load(): array
    {
        return self::read(self::directory() . '/forward', 'Status today');
    }

    /**
     * Every targeted contract case, keyed and sorted by id. Printed for
     * inspection; never recorded as a forward run.
     *
     * @return array<string, array{id: string, title: string, file: string, environment: string, status: string, requirements: array<int, string>, heldBy: string, prompt: string, needs: array<int, string>, outcomes: array<int, string>, failures: array<int, string>, criteria: string}>
     */
    public static function contracts(): array
    {
        return self::read(self::directory() . '/contracts', 'Contract');
    }

    /**
     * @return array<string, array{id: string, title: string, file: string, environment: string, status: string, requirements: array<int, string>, heldBy: string, prompt: string, needs: array<int, string>, outcomes: array<int, string>, failures: array<int, string>, criteria: string}>
     */
    private static function read(string $directory, string $label): array
    {
        $scenarios = [];
        foreach (self::documents($directory) as $path) {
            if (basename($path) === 'readme.md') {
                continue;
            }

            $contents = (string) file_get_contents($path);
            // Split on the headings rather than scanning line by line: every
            // field of a scenario is inside its own section, so the section is
            // the unit both this and the criteria digest are built from. One
            // file holds one case, and a second heading in it is what the tests
            // catch rather than something to parse around.
            $parts = preg_split('/^#{1,2} (?=[A-Z]+-\d+\b)/m', $contents) ?: [];
            foreach (array_slice($parts, 1) as $section) {
                $scenario = self::parse($path, $label, $section);
                $scenarios[$scenario['id']] = $scenario;
            }
        }

        ksort($scenarios);

        return $scenarios;
    }

    /**
     * Every markdown file below a directory, at whatever depth it sits.
     *
     * @return array<int, string>
     */
    private static function documents(string $directory): array
    {
        if (!is_dir($directory)) {
            return [];
        }

        $paths = [];
        foreach (Finder::create()->files()->in($directory)->name('*.md')->sortByName() as $file) {
            $paths[] = $file->getPathname();
        }

        return $paths;
    }

    /**
     * @return array{id: string, title: string, file: string, environment: string, status: string, requirements: array<int, string>, heldBy: string, prompt: string, needs: array<int, string>, outcomes: array<int, string>, failures: array<int, string>, criteria: string}
     */
    private static function parse(string $path, string $label, string $section): array
    {
        $heading = trim((string) strtok($section, "\n"));
        [$id, $title] = array_pad(preg_split('/\s+—\s+/', $heading, 2) ?: [], 2, '');

        $prompt = [];
        foreach (preg_split('/\R/', $section) ?: [] as $line) {
            if (str_starts_with($line, '> ')) {
                // The line breaks are the markdown's wrapping, not the user's.
                $prompt[] = substr($line, 2);
            }
        }

        $outcomes = self::bullets($section, 'What has to come out of it');
        $failures = self::bullets($section, 'How it fails');
        $promptText = implode(' ', $prompt);

        return [
            'id' => $id,
            'title' => $title,
            'file' => substr($path, strlen(Paths::root()) + 1),
            'environment' => self::field($section, 'Environment'),
            'status' => self::field($section, $label),
            'requirements' => self::requirements($section, $label),
            'heldBy' => self::statement($section, 'Held by'),
            'prompt' => $promptText,
            'needs' => self::bullets($section, 'What the agent needs from this server'),
            'outcomes' => $outcomes,
            'failures' => $failures,
            'criteria' => self::digest($promptText, $outcomes, $failures),
        ];
    }

    /**
     * The whole of a labelled statement, wrapped lines folded back together.
     *
     * A contract case cannot be answered by a run, so what holds it has to be
     * named beside it: the tests that do, or that nothing does. The state line
     * above it is a claim either way, and this is the only thing that makes the
     * claim checkable.
     */
    private static function statement(string $section, string $label): string
    {
        $marker = '**' . $label . ':**';
        $start = strpos($section, $marker);
        if ($start === false) {
            return '';
        }

        $paragraph = Entry::firstParagraph(substr($section, $start + strlen($marker)));

        return trim((string) preg_replace('/\s+/', ' ', $paragraph));
    }

    /** The first code span after a bold label, which is where both vocabularies sit. */
    private static function field(string $section, string $label): string
    {
        return preg_match('/\*\*' . preg_quote($label, '/') . ':\*\*\s*`([^`]+)`/', $section, $matches) === 1
            ? $matches[1]
            : '';
    }

    /**
     * The requirements a state line names, which is where a `partial`, `gap` or
     * `open` says what is already written down and what a run should not
     * re-file.
     *
     * @return array<int, string>
     */
    private static function requirements(string $section, string $label): array
    {
        $start = strpos($section, '**' . $label . ':**');
        if ($start === false) {
            return [];
        }

        // Only that paragraph: the same code appears in prose further down, and
        // what is wanted here is what this scenario holds itself to.
        $paragraph = Entry::firstParagraph(substr($section, $start));
        preg_match_all('/`(R-[A-Z]+-\d+)`/', $paragraph, $matches);

        return $matches[1];
    }

    /**
     * The list under one bold heading, with wrapped lines folded back together.
     *
     * @return array<int, string>
     */
    private static function bullets(string $section, string $heading): array
    {
        $start = strpos($section, '**' . $heading . '**');
        if ($start === false) {
            return [];
        }

        $items = [];
        foreach (array_slice(preg_split('/\R/', substr($section, $start)) ?: [], 1) as $line) {
            if (str_starts_with($line, '**') || str_starts_with($line, '## ') || trim($line) === '---') {
                break;
            }
            if (str_starts_with($line, '- ')) {
                $items[] = substr($line, 2);
            } elseif ($items !== [] && trim($line) !== '') {
                $items[count($items) - 1] .= ' ' . trim($line);
            }
        }

        return $items;
    }

    /**
     * What a run was judged against, short enough to read in a diff.
     *
     * @param array<int, string> $outcomes
     * @param array<int, string> $failures
     */
    private static function digest(string $prompt, array $outcomes, array $failures): string
    {
        return substr(hash('sha256', implode("\n", [$prompt, ...$outcomes, ...$failures])), 0, 12);
    }

    /**
     * Every recorded run, with the scenario it belongs to, the verdict its
     * judgments derive, and what is wrong with it.
     *
     * A run whose problems are empty is one that can be believed: it judged
     * every criterion of the scenario as the scenario words them today, and the
     * verdict that follows is the status the scenario claims.
     *
     * @param string|null $directory where the runs are, for a test that needs
     *                               runs this repository does not have
     * @return array<string, array{file: string, run: array<string, mixed>, verdict: string, problems: array<int, string>}>
     */
    public static function runs(?string $directory = null): array
    {
        $scenarios = self::load();

        $runs = [];
        foreach (Finder::create()->files()->in($directory ?? self::runsDirectory())->depth(0)->name('*.json')->sortByName() as $entry) {
            $name = $entry->getBasename('.json');
            $file = 'scenarios/runs/' . $entry->getFilename();
            $run = json_decode((string) file_get_contents($entry->getPathname()), true);
            if (!is_array($run)) {
                $runs[$name] = [
                    'file' => $file,
                    'run' => [],
                    'verdict' => '',
                    'problems' => [$file . ' is not readable as JSON'],
                ];
                continue;
            }

            $id = is_string($run['scenario'] ?? null) ? $run['scenario'] : $name;
            $scenario = $scenarios[$id] ?? null;
            $runs[$id] = [
                'file' => $file,
                'run' => $run,
                'verdict' => $scenario === null ? '' : self::verdict($run),
                'problems' => self::problems($file, $run, $scenario),
            ];
        }

        ksort($runs);

        return $runs;
    }

    /**
     * What the judgments add up to. Not a field of the run: a run that could
     * state its own verdict would be the one thing in it nobody could check.
     *
     * @param array<string, mixed> $run
     */
    private static function verdict(array $run): string
    {
        $outcomes = self::judgments($run, 'outcomes', 'met');
        $failures = self::judgments($run, 'failures', 'avoided');

        if (in_array(null, [...$outcomes, ...$failures], true)) {
            return '';
        }
        if (!in_array(false, [...$outcomes, ...$failures], true)) {
            return 'covered';
        }

        return in_array(true, $outcomes, true) ? 'partial' : 'gap';
    }

    /**
     * A run nobody has judged yet: written, and waiting for its session.
     *
     * @param array<string, mixed> $run
     */
    public static function isOpen(array $run): bool
    {
        $judgments = [
            ...self::judgments($run, 'outcomes', 'met'),
            ...self::judgments($run, 'failures', 'avoided'),
        ];

        return $judgments !== [] && array_filter($judgments, static fn(?bool $j): bool => $j !== null) === [];
    }

    /**
     * The tools a run's evidence quotes that its own trace does not carry.
     *
     * A run holds the claim and what backs it in one file, and nothing read the
     * two against each other: REVIEW-03 quotes two hints as
     * `typo3_hint_lookup` where `typo3_task_guide` returned them and no such
     * call was made, which a judge found by hand. It reports rather than fails,
     * because a judgment naming a tool in order to say it was never called
     * reads the same from here — `D-EVI-009`.
     *
     * @param array<string, mixed> $run
     * @return array<int, string>
     */
    public static function unbackedTools(array $run): array
    {
        $called = [];
        foreach (is_array($run['toolTrace'] ?? null) ? $run['toolTrace'] : [] as $call) {
            $tool = is_array($call) ? ($call['tool'] ?? null) : null;
            if (is_string($tool)) {
                $called[] = $tool;
            }
        }

        $named = [];
        foreach (['outcomes', 'failures'] as $section) {
            foreach (is_array($run[$section] ?? null) ? $run[$section] : [] as $entry) {
                $evidence = is_array($entry) ? ($entry['evidence'] ?? null) : null;
                preg_match_all('/\btypo3_[a-z_]+/', is_string($evidence) ? $evidence : '', $matches);
                $named = [...$named, ...$matches[0]];
            }
        }

        $unbacked = array_values(array_unique(array_diff($named, $called)));
        sort($unbacked);

        return $unbacked;
    }

    /**
     * @param array<string, mixed> $run
     * @return array<int, bool|null>
     */
    private static function judgments(array $run, string $section, string $key): array
    {
        $judgments = [];
        foreach (is_array($run[$section] ?? null) ? $run[$section] : [] as $entry) {
            $judgment = is_array($entry) ? ($entry[$key] ?? null) : null;
            $judgments[] = is_bool($judgment) ? $judgment : null;
        }

        return $judgments;
    }

    /**
     * @param array<string, mixed> $run
     * @param array{id: string, title: string, file: string, environment: string, status: string, requirements: array<int, string>, heldBy: string, prompt: string, needs: array<int, string>, outcomes: array<int, string>, failures: array<int, string>, criteria: string}|null $scenario
     * @return array<int, string>
     */
    private static function problems(string $file, array $run, ?array $scenario): array
    {
        if ($scenario === null) {
            return [$file . ' records a run of no forward review in scenarios/forward/'];
        }

        $problems = [];
        foreach (['date', 'server', 'client'] as $field) {
            if (!is_string($run[$field] ?? null) || trim((string) $run[$field]) === '') {
                $problems[] = $file . ' does not say its ' . $field;
            }
        }
        if (($run['environment'] ?? null) !== $scenario['environment']) {
            $problems[] = sprintf(
                '%s ran in %s, and %s is a scenario for %s',
                $file,
                is_string($run['environment'] ?? null) ? $run['environment'] : '(nothing)',
                $scenario['id'],
                $scenario['environment'],
            );
        }
        // Both empty is a legitimate and damning result: an agent that reached
        // for neither is the run this suite exists to catch. Both absent is
        // not a result at all — nobody looked.
        foreach (['skills' => 'which skills activated', 'toolTrace' => 'which tools were called'] as $field => $what) {
            if (!is_array($run[$field] ?? null)) {
                $problems[] = $file . ' does not say ' . $what . ', not even that it was nothing';
            }
        }
        // A tool name says a lookup happened, never what was asked of it, and
        // most of what a run is judged on is the difference: one query per
        // surface or one broad one, the version a lookup was given, whether a
        // returned id was followed. So each call carries the arguments it was
        // made with, `{}` where it takes none, read out of the transcript with
        // the name.
        foreach (is_array($run['toolTrace'] ?? null) ? $run['toolTrace'] : [] as $index => $call) {
            $position = $file . ' tool call ' . ((int) $index + 1);
            if (!is_array($call) || !is_string($call['tool'] ?? null) || trim((string) $call['tool']) === '') {
                $problems[] = $position . ' does not name the tool it called';
                continue;
            }
            if (!is_array($call['arguments'] ?? null)) {
                $problems[] = $position . ', ' . $call['tool'] . ', does not say what it was called with';
            }
        }
        if (($run['criteria'] ?? null) !== $scenario['criteria']) {
            // The scenario was rewritten after the run, so the judgments below
            // answer criteria that no longer exist. Run it again; there is no
            // way to tell from here which of them survived the edit.
            $problems[] = sprintf(
                '%s was judged against criteria %s, and %s now words them as %s',
                $file,
                is_string($run['criteria'] ?? null) ? $run['criteria'] : '(nothing)',
                $scenario['id'],
                $scenario['criteria'],
            );

            return $problems;
        }

        // The empty run `record` writes, before the session it belongs to has
        // happened. It claims nothing, so there is nothing to hold it to — and
        // a checker that fails on it stops the repository for as long as a run
        // is open, which is the one time it has to stay usable.
        if (self::isOpen($run)) {
            return $problems;
        }

        foreach ([['outcomes', 'met'], ['failures', 'avoided']] as [$section, $key]) {
            $judgments = self::judgments($run, $section, $key);
            if (count($judgments) !== count($scenario[$section])) {
                $problems[] = sprintf(
                    '%s judges %d of the %d %s in %s',
                    $file,
                    count($judgments),
                    count($scenario[$section]),
                    $section,
                    $scenario['id'],
                );
                continue;
            }
            foreach ($judgments as $index => $judgment) {
                if ($judgment === null) {
                    $problems[] = sprintf('%s leaves %s %d unjudged', $file, $section, $index + 1);
                }
            }
            // Every judgment, not only the ones that went badly: a criterion
            // met without evidence is the same sentence in the same place as
            // the status line that drifted, and it is worth exactly as much.
            foreach (is_array($run[$section] ?? null) ? $run[$section] : [] as $index => $entry) {
                $evidence = is_array($entry) ? ($entry['evidence'] ?? null) : null;
                if (!is_string($evidence) || trim($evidence) === '') {
                    $problems[] = sprintf('%s gives no evidence for %s %d', $file, $section, (int) $index + 1);
                }
            }
        }

        $verdict = self::verdict($run);
        // `boundary` is a scenario that is answered by declining well, so its
        // criteria are met like any other scenario's.
        $expected = $scenario['status'] === 'boundary' ? 'covered' : $scenario['status'];
        if ($verdict !== '' && $verdict !== $expected) {
            $problems[] = sprintf(
                '%s says %s is `%s`, and %s stands at `%s`',
                $file,
                $scenario['id'],
                $verdict,
                $scenario['file'],
                $scenario['status'],
            );
        }

        return $problems;
    }

    /**
     * The empty run of one scenario: every criterion listed as unjudged, and
     * the digest of the criteria as they read now.
     *
     * @return array<string, mixed>
     */
    public static function skeleton(string $id, string $server, string $client, string $date): array
    {
        $scenario = self::load()[$id] ?? null;
        if ($scenario === null) {
            throw new \InvalidArgumentException(sprintf('There is no forward review %s.', $id));
        }

        return [
            'scenario' => $scenario['id'],
            'date' => $date,
            'server' => $server,
            'client' => $client,
            'environment' => $scenario['environment'],
            'criteria' => $scenario['criteria'],
            'skills' => [],
            'toolTrace' => [],
            'outcomes' => array_map(
                static fn(): array => ['met' => null, 'evidence' => ''],
                $scenario['outcomes'],
            ),
            'failures' => array_map(
                static fn(): array => ['avoided' => null, 'evidence' => ''],
                $scenario['failures'],
            ),
            'notes' => '',
        ];
    }

    public static function directory(): string
    {
        return Paths::root() . '/scenarios';
    }

    public static function runsDirectory(): string
    {
        return self::directory() . '/runs';
    }
}
