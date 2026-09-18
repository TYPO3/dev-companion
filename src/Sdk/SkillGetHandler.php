<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Sdk;

use Mcp\Exception\ResourceNotFoundException;
use Mcp\Schema\JsonRpc\Error;
use Mcp\Schema\JsonRpc\Request;
use Mcp\Schema\JsonRpc\Response;
use Mcp\Server\Handler\Request\RequestHandlerInterface;
use Mcp\Server\Session\SessionInterface;

/** @implements RequestHandlerInterface<SkillsResult> */
final class SkillGetHandler implements RequestHandlerInterface
{
    public function supports(Request $request): bool
    {
        return $request instanceof SkillGetRequest;
    }

    /** @return Response<SkillsResult>|Error */
    public function handle(Request $request, SessionInterface $session): Response|Error
    {
        \assert($request instanceof SkillGetRequest);

        // The spec fixes the code: a URI that names no skill is -32602 on
        // every revision, where a resource read moved there with 2026-07-28.
        if (!str_starts_with($request->uri, ResourceHandler::SKILL_PREFIX)
            || !str_ends_with($request->uri, ResourceHandler::SKILL_BODY)
        ) {
            return Error::forInvalidParams('Unknown skill: ' . $request->uri, $request->getId(), ['uri' => $request->uri]);
        }

        try {
            $manifest = Skills::manifest(substr(
                $request->uri,
                strlen(ResourceHandler::SKILL_PREFIX),
                -strlen(ResourceHandler::SKILL_BODY),
            ));
        } catch (ResourceNotFoundException $exception) {
            return Error::forInvalidParams($exception->getMessage(), $request->getId(), ['uri' => $request->uri]);
        }

        return new Response($request->getId(), new SkillsResult(['skill' => $manifest]));
    }
}
