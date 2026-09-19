<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Mcp\Capability\Attribute\CompletionProvider;
use TYPO3\DevCompanion\Knowledge\CommitMessage;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * The two prompts, as methods the SDK reflects.
 *
 * Methods rather than closures, because the SDK reads an argument's
 * description off a method's `@param` tag and off nothing on a closure. A
 * slash-command client shows that description beside the argument, and without
 * it a person sees the name alone. `Server\Factory` still says which of the
 * two a client gets.
 */
final class Prompts
{
    /**
     * @param string $summary  What the commit did, without the keyword in front
     * @param string $keyword  TYPO3 commit message keyword; [SECURITY] belongs to the Security Team
     * @param string $workflow core for a patch against the TYPO3 core, project for any other repository
     * @param string $issue    The issue the commit resolves, with or without # in front
     *
     * @return array{user: string}
     */
    public static function commitMessage(
        string $summary,
        #[CompletionProvider(values: CommitMessage::PROJECT_KEYWORDS)]
        string $keyword = 'TASK',
        #[CompletionProvider(values: CommitMessage::WORKFLOWS)]
        string $workflow = 'core',
        string $issue = '',
    ): array {
        $arguments = [
            'summary' => $summary,
            'keyword' => $keyword,
            'workflow' => $workflow,
        ];
        if ($issue !== '') {
            $arguments['issue'] = $issue;
        }

        return ['user' => Registry::call('typo3_commit_message_guide', $arguments)->text];
    }

    /** @return array{user: string} */
    public static function debrief(): array
    {
        return ['user' => (string) file_get_contents(Paths::debrief())];
    }
}
