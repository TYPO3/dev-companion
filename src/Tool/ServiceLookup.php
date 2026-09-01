<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * What the installation's dependency injection container assembles, and what
 * each service is handed.
 *
 * The container a TYPO3 runs is compiled and has forgotten every private
 * definition, which is nearly all of them. What answers is the builder the core
 * assembles before that — `D-DIS-023`.
 */
final class ServiceLookup extends ReadOnlyTool
{
    private const MAX_SERVICES = 50;

    public static function name(): string
    {
        return 'typo3_service_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation];
    }

    public static function description(): string
    {
        return 'Find what the dependency injection container of the TYPO3 installation you are working in assembles: which class stands behind a service id or an interface, whether it is public, shared and autowired, the tags it carries, and what each of its constructor arguments is handed — as the service id that lands there, per position and after autowiring. Search by a substring of the id or the class, or ask for one exact tag to enumerate what registers into an extension point: event.listener, fluid.viewhelper, typo3.singleton, and the tags a package declares itself. It answers what this installation resolved rather than what a Services.yaml says, so a decoration, an override or an alias shows up as the class that is really injected. Nothing is instantiated to answer it.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'A case-insensitive substring matched against the service id and the class, for example "PageRenderer" or "Imaging". Omit to ask by tag alone.'],
                'tag' => ['type' => 'string', 'description' => 'One exact service tag, for example "event.listener". Omit to search by query alone.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => self::MAX_SERVICES, 'default' => 10, 'description' => 'Maximum services to return.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'query' => Schema::nullableString('The substring asked for, null where none was.'),
            'tag' => Schema::nullableString('The tag asked for, null where none was.'),
            'matchCount' => Schema::integer('Services matching before the limit. Zero is an answer: nothing this installation assembles carries that id, class or tag.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'definitionCount' => Schema::integer('Every service definition the container holds, which is what the match was made against.'),
            'aliasCount' => Schema::integer('The aliases beside them. An interface usually reaches its implementation through one.'),
            'services' => Schema::listOf(Schema::object([
                'id' => Schema::string('The service id, which is the class name for nearly all of them.'),
                'class' => Schema::string('The class the container instantiates, which a decoration or an override makes different from the id.'),
                'public' => ['type' => 'boolean', 'description' => 'True where the container hands it out by id. A private service is only ever injected.'],
                'shared' => ['type' => 'boolean', 'description' => 'True where every caller gets the same instance.'],
                'autowired' => ['type' => 'boolean'],
                'abstract' => ['type' => 'boolean'],
                'synthetic' => ['type' => 'boolean', 'description' => 'True where the instance is set into the container at boot rather than built by it.'],
                'tags' => Schema::listOf(Schema::string(), 'The tags it carries, which is what an extension point enumerates by.'),
                'arguments' => Schema::listOf(Schema::object([
                    'position' => Schema::integer('The constructor position, counted from zero.'),
                    'resolves' => Schema::string('The service id handed to it, or "value" where a configured value is passed instead of a service.'),
                ], ['position', 'resolves']), 'What the constructor is handed, after autowiring. Empty where it takes nothing.'),
            ], ['id', 'class', 'public', 'shared', 'autowired', 'abstract', 'synthetic', 'tags', 'arguments'])),
        ], ['query', 'tag', 'matchCount', 'answeredBy', 'definitionCount', 'aliasCount', 'services'], ['query', 'tag']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $tag = trim((string) ($args['tag'] ?? ''));
        $limit = is_int($args['limit'] ?? null) ? max(1, min(self::MAX_SERVICES, $args['limit'])) : 10;
        $echo = ['query' => $query === '' ? null : $query, 'tag' => $tag === '' ? null : $tag];

        if (!Instance::isAvailable()) {
            return Unsupported::because(
                'no TYPO3 installation was found from the directory this server was started in',
                $echo,
            );
        }

        $read = Typo3Runtime::services($query, $tag);
        if (!is_array($read)) {
            return Unsupported::because(Typo3Runtime::reason(), $echo);
        }
        if (isset($read['unavailable'])) {
            return Unsupported::because(
                'the installation booted and its container could not be assembled a second time: '
                . (string) $read['unavailable'],
                $echo,
            );
        }

        $matches = $read['services'];
        $shown = array_slice($matches, 0, $limit);

        return ToolResult::create(
            implode("\n", [
                $matches === []
                    ? sprintf(
                        'Nothing among the %d services this installation assembles matches%s%s.',
                        $read['definitionCount'],
                        $query === '' ? '' : ' "' . $query . '"',
                        $tag === '' ? '' : ($query === '' ? ' the tag ' : ' and the tag ') . $tag,
                    )
                    : sprintf(
                        '%d of the %d services this installation assembles match%s. %s',
                        count($matches),
                        $read['definitionCount'],
                        count($matches) > count($shown) ? sprintf(', and the first %d are here', count($shown)) : '',
                        'What a constructor is handed is the id that really lands there, after autowiring.',
                    ),
                '',
                ...array_map(static fn(array $service): string => implode("\n", [
                    sprintf(
                        '- %s%s%s%s',
                        (string) $service['id'],
                        $service['class'] !== $service['id'] ? ' → ' . (string) $service['class'] : '',
                        $service['public'] === true ? ' (public)' : '',
                        $service['tags'] === [] ? '' : ' [' . implode(', ', $service['tags']) . ']',
                    ),
                    ...array_map(static fn(array $argument): string => sprintf(
                        '    %d: %s',
                        (int) $argument['position'],
                        (string) $argument['resolves'],
                    ), $service['arguments']),
                ]), $shown),
            ]),
            $echo + [
                'matchCount' => count($matches),
                'answeredBy' => 'installation',
                'definitionCount' => $read['definitionCount'],
                'aliasCount' => $read['aliasCount'],
                'services' => $shown,
            ],
        );
    }
}
