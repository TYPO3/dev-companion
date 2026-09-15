<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Server;

use Mcp\Schema\Annotations;
use Mcp\Schema\ResourceDefinition;
use Mcp\Schema\ResourceTemplate;
use Mcp\Schema\Tool;
use Mcp\Schema\ToolAnnotations;
use Mcp\Server;
use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Sdk\ResourceHandler;
use TYPO3\DevCompanion\Sdk\SkillReferenceHandler;
use TYPO3\DevCompanion\Sdk\Skills;
use TYPO3\DevCompanion\Sdk\ToolHandler;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * Builds the MCP server on the official mcp/sdk, and wires the knowledge base
 * to the SDK tool and resource handlers. Transport-agnostic, so the stdio
 * entrypoint is only one possible consumer of this definition.
 */
final class Factory
{
    public const SERVER_NAME = 'typo3-dev-companion';
    public const SERVER_VERSION = '0.3.0';

    /**
     * How much of a session's context a resource is worth, on the scale the
     * spec gives `annotations.priority`. 1 is "most important", which means
     * required in effect, and 0 is entirely optional. A picker sorts by it, so
     * what carries the meaning is the order rather than the number.
     *
     * The index says what all the others are and is the one to take where a
     * picker takes only one. Below it, what holds whatever the caller works on
     * is worth more than what holds inside a core checkout alone, `R-AUD-001`.
     * That audience is the whole of the axis, so a document and a skill that
     * both hold anywhere stand at the same height.
     */
    private const INDEX_PRIORITY = 1.0;
    private const TRANSFERABLE_PRIORITY = 0.8;
    private const CORE_ONLY_PRIORITY = 0.4;

    /**
     * Under both, because a reference is worth a read at the step that sends
     * the reader to it and not before. That is also why they are a template
     * rather than entries in the list a picker sorts.
     */
    private const REFERENCE_PRIORITY = 0.2;

    /**
     * @param string $notice what is wrong with this project before the first
     *     call, in front of the routing; empty where nothing is
     */
    public static function create(string $notice = ''): Server
    {
        $builder = Server::builder()
            ->setServerInfo(self::SERVER_NAME, self::SERVER_VERSION)
            ->setInstructions(Coverage::instructions($notice));

        foreach (Registry::definitions() as $definition) {
            // The two schemas as the SDK spells them. A tool declares them as
            // JSON Schema and `tests/Contract/` is what holds it to that. The
            // shape below is the SDK's read of the same thing, and nothing
            // between here and the tool carries it.
            /** @var array{type: 'object', properties: array<string, mixed>, required: array<string>|null} $inputSchema */
            $inputSchema = $definition['inputSchema'];
            /** @var array{type: 'object', properties?: array<string, mixed>, required?: array<string>|null, additionalProperties?: array<string, mixed>|bool, description?: string}|null $outputSchema */
            $outputSchema = $definition['outputSchema'];
            $tool = new Tool(
                $definition['name'],
                null,
                $inputSchema,
                $definition['description'],
                new ToolAnnotations(
                    readOnlyHint: $definition['annotations']['readOnlyHint'],
                    destructiveHint: $definition['annotations']['destructiveHint'],
                    idempotentHint: $definition['annotations']['idempotentHint'],
                    openWorldHint: $definition['annotations']['openWorldHint'],
                ),
                outputSchema: $outputSchema,
            );
            $builder->add($tool, new ToolHandler($definition['name']));
        }

        $builder->addPrompt(
            static function (
                string $summary,
                string $keyword = 'TASK',
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
            },
            name: 'commit_message',
            title: 'Draft a TYPO3 commit message',
            description: 'Turn a summary into the checked commit-message draft already provided by typo3_commit_message_guide.',
        );

        // Where the channel it ends in is, which is what `Registry` gates the
        // two feedback tools on. A session that cannot record a feedback has no
        // use for the questions. A prompt rather than a tool. A tool stands in
        // the model's list from the first call, and the session would learn
        // while it still works that a debrief comes, `D-FBK-048`.
        if (Channel::isAvailable()) {
            $builder->addPrompt(
                static fn(): array => ['user' => (string) file_get_contents(Paths::debrief())],
                name: 'debrief',
                title: 'Debrief the session that has just finished',
                description: 'Ask a finished session what this server did for it and what it lacked, and have it '
                    . 'record what it found with typo3_feedback_record. Run it after the work, in a message of its own.',
            );
        }

        $resourceHandler = new ResourceHandler();
        foreach (self::resources() as $resource) {
            $builder->add($resource, $resourceHandler);
        }
        $builder->add(self::skillReferences(), new SkillReferenceHandler());

        return $builder->build();
    }

    /**
     * What this server offers a picker, and what the choice rests on.
     *
     * The model calls a tool in the middle of a task and the tool can explain
     * itself in its answer. The host application or the user picks a resource
     * out of a list, so `description`, `annotations.priority` and `size` are
     * what the choice rests on, `R-ANS-022`. Two families, because the
     * knowledge documents are mostly the core's own process and the skills
     * mostly extension and site work. That leaves all three audiences of
     * `R-AUD-001` something to pick. Two fields of the spec stay absent.
     * `annotations.audience` means the client's user, and `lastModified` is in
     * no `Mcp\Schema\Annotations` of mcp/sdk v0.8.0.
     *
     * @return array<int, ResourceDefinition>
     */
    public static function resources(): array
    {
        $resources = [
            new ResourceDefinition(
                uri: ResourceHandler::INDEX_URI,
                name: 'typo3-core-knowledge-index',
                title: 'TYPO3 core knowledge index',
                description: 'What this server covers, which tool answers what, and every document and skill it '
                    . 'serves. The one to read first.',
                mimeType: 'application/json',
                annotations: new Annotations(priority: self::INDEX_PRIORITY),
                size: strlen(ResourceHandler::index()),
            ),
        ];

        foreach (Documents::documents() as $document) {
            $resources[] = new ResourceDefinition(
                uri: Documents::uri($document['id']),
                // The id is a path since `D-KNW-058` and a resource name may
                // not be. The SDK holds a name to alphanumerics, underscores
                // and hyphens, and rejects the definition outright. Only the
                // URI is free-form, so the separator flattens here and the
                // address keeps its segments.
                name: str_replace('/', '-', $document['id']),
                title: $document['title'],
                description: Documents::description($document['id']),
                mimeType: 'text/markdown',
                annotations: new Annotations(priority: Documents::isCoreOnly($document['id'])
                    ? self::CORE_ONLY_PRIORITY
                    : self::TRANSFERABLE_PRIORITY),
                size: (int) filesize($document['path']),
            );
        }

        foreach (Skills::skills() as $skill) {
            $resources[] = new ResourceDefinition(
                uri: ResourceHandler::skillUri($skill['id']),
                name: Skills::name($skill['id']),
                title: $skill['title'],
                description: Skills::description($skill['id']),
                mimeType: 'text/markdown',
                annotations: new Annotations(priority: Skills::isCoreOnly($skill['id'])
                    ? self::CORE_ONLY_PRIORITY
                    : self::TRANSFERABLE_PRIORITY),
                size: (int) filesize($skill['path']),
            );
        }

        return $resources;
    }

    /**
     * What a skill's body sends the reader to, offered as the one shape they
     * share rather than as a list entry each.
     *
     * A skill is a directory. Its body is short routing and the file it hands
     * over at a step is beside it. `references/base.md` above all, which is in
     * every one of them and a file in none of them until publication. Served
     * under `typo3://skill/{id}/SKILL.md`, those links resolve to exactly the
     * URIs this template answers. So a client that never ran the install can
     * follow the workflow it picked instead of an instruction that points at
     * nothing.
     */
    public static function skillReferences(): ResourceTemplate
    {
        return new ResourceTemplate(
            uriTemplate: ResourceHandler::SKILL_REFERENCE_TEMPLATE,
            name: 'typo3-skill-reference',
            title: 'What a TYPO3 task workflow hands over at a step',
            description: 'The files a skill under typo3://skill/ links to: the order every task starts in, the '
                . 'checklist it works through, and the implementation guide for the layer it settled on. Read the '
                . 'one its body sends you to, at the step that sends you.',
            mimeType: 'text/markdown',
            annotations: new Annotations(priority: self::REFERENCE_PRIORITY),
        );
    }
}
