<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * A scenario handed to whoever runs it: what has to be pasted, and what the
 * session is judged against.
 *
 * Two commands print this and differ in one word — `scenarios:show` for an open
 * forward review, `scenarios:contract` for a targeted case that is read rather
 * than run — so what they have in common is stated once here. That word is the
 * label above the status line, because the two claim their state on different
 * evidence: a forward review on a run somebody recorded, a contract case on the
 * test that holds it.
 */
abstract class ScenarioReport
{
    /**
     * @param array{id: string, title: string, file: string, environment: string, status: string, requirements: array<int, string>, heldBy: string, prompt: string, needs: array<int, string>, outcomes: array<int, string>, failures: array<int, string>, criteria: string} $scenario
     */
    protected function report(OutputInterface $output, array $scenario, string $label): void
    {
        Voice::heading($output, sprintf('%s — %s', $scenario['id'], $scenario['title']));
        Voice::note($output, $scenario['file']);
        Voice::row($output, sprintf('%s %s', Voice::key('Environment', 12), $scenario['environment']));
        Voice::row($output, sprintf(
            '%s %s%s',
            Voice::key($label, 12),
            $scenario['status'],
            $scenario['requirements'] === [] ? '' : ' — ' . implode(', ', $scenario['requirements']),
        ));
        if ($scenario['heldBy'] !== '') {
            // A case nobody runs claims its state on the strength of this line.
            Voice::row($output, sprintf('%s %s', Voice::key('Held by', 12), str_replace('`', '', $scenario['heldBy'])));
        }
        Voice::row($output, sprintf('%s %s', Voice::key('Criteria', 12), $scenario['criteria']));

        // Verbatim, on its own, with nothing around it: a prompt read off a screen
        // that also explains what it is testing is no longer the prompt.
        Voice::heading($output, 'Paste this and add nothing');
        $output->writeln('');
        $output->writeln($scenario['prompt']);

        if ($scenario['needs'] !== []) {
            Voice::heading($output, 'What the agent needs from this server');
            foreach ($scenario['needs'] as $need) {
                Voice::row($output, sprintf('- %s', $need));
            }
        }

        foreach ([['outcomes', 'What has to come out of it'], ['failures', 'How it fails']] as [$section, $heading]) {
            Voice::heading($output, $heading);
            foreach ($scenario[$section] as $index => $criterion) {
                Voice::row($output, sprintf('%s %s', Voice::key(sprintf('%s %d', $section === 'outcomes' ? 'met' : 'avoided', $index + 1), 9), $criterion));
            }
        }
    }
}
