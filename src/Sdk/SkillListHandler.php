<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Mcp\Schema\JsonRpc\Request;
use Mcp\Schema\JsonRpc\Response;
use Mcp\Server\Handler\Request\RequestHandlerInterface;
use Mcp\Server\Session\SessionInterface;

/** @implements RequestHandlerInterface<SkillsResult> */
final class SkillListHandler implements RequestHandlerInterface
{
    public function supports(Request $request): bool
    {
        return $request instanceof SkillListRequest;
    }

    /** @return Response<SkillsResult> */
    public function handle(Request $request, SessionInterface $session): Response
    {
        return new Response($request->getId(), new SkillsResult([
            'skills' => array_map(
                static fn(array $skill): array => Skills::manifest($skill['id']),
                Skills::skills(),
            ),
        ]));
    }
}
