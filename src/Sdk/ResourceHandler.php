<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Mcp\Server\ClientGateway;
use Mcp\Server\Handler\ResourceHandlerInterface;
use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Knowledge\Documents;

/**
 * Serves the typo3:// resources from what this package ships.
 *
 * One instance backs every registered resource. The typo3://guides index (what
 * this server covers, plus a JSON listing of what it serves), each
 * typo3://guides/{id} knowledge document, and each typo3://skill/{id} task
 * workflow. The SDK wraps the returned string with the mime type declared on
 * the matching resource definition.
 */
final class ResourceHandler implements ResourceHandlerInterface
{
    public const INDEX_URI = 'typo3://guides';
    public const SKILL_PREFIX = 'typo3://skill/';

    /**
     * The file a skill's own body is, kept in its URI.
     *
     * A skill is a directory and its body links to the files beside it by
     * relative path, `references/base.md` in every one of them. Resolved
     * against `typo3://skill/{id}/SKILL.md` those links land on the reference
     * URIs this server serves, which is the whole reason the file name is in
     * there. The published prose goes over the wire as it stands, and nothing
     * has to change for it to point somewhere.
     */
    public const SKILL_BODY = '/SKILL.md';

    /** The reference URIs of a skill, which SKILL_REFERENCE_TEMPLATE answers. */
    public const SKILL_REFERENCES = 'references/';
    public const SKILL_REFERENCE_TEMPLATE = 'typo3://skill/{skill}/references/{reference}';

    /** Where a skill's body is on offer. */
    public static function skillUri(string $id): string
    {
        return self::SKILL_PREFIX . $id . self::SKILL_BODY;
    }

    /** Where one of its references is, at the path the body links to it by. */
    public static function skillReferenceUri(string $id, string $reference): string
    {
        return self::SKILL_PREFIX . $id . '/' . $reference;
    }

    public function read(string $uri, ClientGateway $gateway): string
    {
        if ($uri === self::INDEX_URI) {
            return self::index();
        }

        if (str_starts_with($uri, self::SKILL_PREFIX) && str_ends_with($uri, self::SKILL_BODY)) {
            return Skills::read(substr(
                $uri,
                strlen(self::SKILL_PREFIX),
                -strlen(self::SKILL_BODY),
            ));
        }

        $documentId = Documents::idOf($uri);
        if ($documentId !== null) {
            $id = $documentId;

            return Documents::read($id);
        }

        throw new \RuntimeException(sprintf('Unknown resource: %s', $uri));
    }

    /**
     * The index as the server serves it, which is also what its declared size
     * counts. The definition tells a client how many bytes a read costs, and
     * the only honest source for that number is the string that goes over.
     */
    public static function index(): string
    {
        // The profile's scope, not the stored one. The same client that gets
        // the tool list reads the index, and a topic it cannot reach is no more
        // useful here than in typo3_server_scope.
        $scope = Coverage::offered();

        $index = [
            'purpose' => $scope['purpose'],
            'covers' => $scope['covers'],
            'routing' => $scope['routing'],
            'documents' => array_map(static fn(array $document): array => [
                'id' => $document['id'],
                'title' => $document['title'],
                'uri' => Documents::uri($document['id']),
            ], Documents::documents()),
            // A skill's references stand here by name rather than wait for a
            // resolution of the links in its body. They are a resource
            // template, so no list a client reads enumerates them. One of them
            // exists as a file only once the skill goes out.
            'skills' => array_map(static fn(array $skill): array => [
                'id' => $skill['id'],
                'title' => $skill['title'],
                'uri' => self::skillUri($skill['id']),
                'references' => array_map(
                    static fn(string $reference): string => self::skillReferenceUri($skill['id'], $reference),
                    Skills::references($skill['id']),
                ),
            ], Skills::skills()),
        ];

        return (string) json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
