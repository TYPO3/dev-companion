<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Mcp\Schema\Extension\ExtensionIdentifier;
use Mcp\Schema\Extension\ExtensionInterface;

/**
 * The Skills extension, `io.modelcontextprotocol/skills`, over the resources
 * this server already serves.
 *
 * The declaration is what makes `typo3://skill/{id}/SKILL.md` a skill to a
 * host, which may not read that off the URI. The reads stay `resources/read`;
 * the manifest a host verifies them against is what the extension adds.
 * `D-ANS-163` holds the scheme and the index beside it.
 */
final class SkillsExtension implements ExtensionInterface
{
    public const ID = 'io.modelcontextprotocol/skills';

    public function getId(): ExtensionIdentifier
    {
        return new ExtensionIdentifier(self::ID);
    }

    public function getCapabilities(): array
    {
        return [];
    }

    public function getMessages(): array
    {
        return [SkillListRequest::class, SkillGetRequest::class];
    }

    public function getRequestHandlers(): iterable
    {
        return [new SkillListHandler(), new SkillGetHandler()];
    }
}
