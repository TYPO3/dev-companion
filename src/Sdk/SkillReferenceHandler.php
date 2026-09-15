<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Mcp\Server\ClientGateway;
use Mcp\Server\Handler\ResourceTemplateHandlerInterface;

/**
 * Serves what a skill's body links to, at the URIs those links resolve to.
 *
 * A template rather than a resource each, because a reader follows these and
 * does not pick them. `R-ANS-022` is about the list a host offers for
 * selection. A checklist on offer beside the workflow that owns it is an entry
 * nobody can choose between. They stay addressable. `resources/templates/list`
 * says the shape, and the body says which of them it sends the reader to.
 */
final class SkillReferenceHandler implements ResourceTemplateHandlerInterface
{
    /** @param array<string, string> $variables */
    public function read(string $uri, array $variables, ClientGateway $gateway): string
    {
        return Skills::reference(
            $variables['skill'] ?? '',
            ResourceHandler::SKILL_REFERENCES . ($variables['reference'] ?? ''),
        );
    }
}
