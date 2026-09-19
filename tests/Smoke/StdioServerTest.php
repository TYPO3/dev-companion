<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Server\Entrypoint;
use TYPO3\DevCompanion\Server\Installer;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Directory;
use TYPO3\DevCompanion\Tests\Support\Requirement;

/**
 * Drives the real entrypoint the way a client does: a subprocess that speaks
 * JSON-RPC over stdin and stdout. Only this reaches everything in between: the
 * SDK setup, the schema validation, the error mapping.
 */
final class StdioServerTest extends TestCase
{
    /**
     * The newest revision reachable through the `initialize` handshake, which
     * is the only era a stdio transport serves.
     */
    private const PROTOCOL_VERSION = '2025-11-25';

    /**
     * What a feedback recorded from here says about itself, so tearDown finds
     * the file it became. The server writes into its own checkout whatever
     * directory the subprocess starts in, which is the whole point of
     * `Paths::root()`. So a case that records leaves a real file in
     * `feedback/`, and this is what tearDown removes it by.
     */
    private const MARKER = 'phpunit-stdio-fixture';

    private ?string $temporaryRoot = null;

    private ?string $feedbackDirectory = null;

    protected function tearDown(): void
    {
        if ($this->temporaryRoot !== null) {
            Directory::remove($this->temporaryRoot);
        }
        $this->temporaryRoot = null;

        if ($this->feedbackDirectory !== null) {
            Directory::remove(dirname($this->feedbackDirectory));
            $this->feedbackDirectory = null;
        }
    }

    /**
     * Where a server started here records what a caller asks it to.
     *
     * The server runs as a subprocess, so the static redirect a unit test uses
     * cannot reach it. The environment carries the directory instead. Without
     * it a recorded feedback lands in the corpus this repository keeps, where
     * the next run reads it as a report somebody left — `R-COD-003`.
     */
    private function ownFeedbackDirectory(): string
    {
        if ($this->feedbackDirectory === null) {
            // Named `feedback` below a root of its own, so the path the
            // server answers with keeps the shape it has in a real checkout.
            $root = sys_get_temp_dir() . '/' . self::MARKER . '-' . getmypid() . '-' . bin2hex(random_bytes(6));
            $this->feedbackDirectory = $root . '/feedback';
            mkdir($this->feedbackDirectory . '/archive', 0o777, true);
        }

        return $this->feedbackDirectory;
    }

    #[Requirement('R-ANS-013')]
    #[Requirement('R-COD-001')]
    #[Test]
    public function theServerAnnouncesItselfWithItsBoundary(): void
    {
        $result = $this->call([$this->request(1, 'initialize', [
            'protocolVersion' => self::PROTOCOL_VERSION,
            'capabilities' => new \stdClass(),
            'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
        ])])[1];

        self::assertSame('typo3-dev-companion', $result['result']['serverInfo']['name']);
        self::assertSame(self::PROTOCOL_VERSION, $result['result']['protocolVersion']);
        // What a client shows a person beside the name: the title, the site,
        // and the mark as a `data:` URI, since a stdio server has no origin.
        self::assertSame('TYPO3 Dev Companion', $result['result']['serverInfo']['title']);
        self::assertSame('https://typo3.github.io/dev-companion/', $result['result']['serverInfo']['websiteUrl']);
        self::assertSame(['16x16'], $result['result']['serverInfo']['icons'][0]['sizes']);
        self::assertStringStartsWith('data:image/svg+xml;base64,', $result['result']['serverInfo']['icons'][0]['src']);
        self::assertStringContainsString('checkout', $result['result']['instructions']);
        // Held here as well as in ScopeTest, because this is the string a
        // client gets. What the SDK puts on the wire is what a client
        // truncates, and it truncates without a word to either side.
        self::assertLessThanOrEqual(
            Coverage::INSTRUCTIONS_BUDGET,
            mb_strlen($result['result']['instructions']),
        );
    }

    /**
     * What the capabilities say is what the server does. The SDK's detection
     * had declared logging, completions and a subscription for every server it
     * builds, and this one sent no log message and no resource update. The
     * completion it keeps is the one it answers — `D-ANS-166`.
     */
    #[Decision('D-ANS-166')]
    #[Test]
    public function theCapabilitiesSayWhatTheServerDoes(): void
    {
        $capabilities = $this->call([$this->request(1, 'initialize', [
            'protocolVersion' => self::PROTOCOL_VERSION,
            'capabilities' => new \stdClass(),
            'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
        ])])[1]['result']['capabilities'];

        self::assertSame(
            ['completions', 'prompts', 'resources', 'tools', 'extensions'],
            array_keys($capabilities),
        );
        self::assertSame([], $capabilities['resources']);

        $completion = $this->session([$this->request(2, 'completion/complete', [
            'ref' => ['type' => 'ref/prompt', 'name' => 'commit_message'],
            'argument' => ['name' => 'keyword', 'value' => 'T'],
        ])])[2]['result']['completion'];

        self::assertSame(['TASK'], $completion['values']);
    }

    /**
     * A client that has moved on to the revision this transport cannot speak
     * gets the newest one it can, rather than a refusal.
     *
     * `2026-07-28` replaced `initialize` with per-request metadata and
     * `server/discover`, which mcp/sdk serves from `StreamableHttpTransport`
     * alone. So the negotiation it gained with that revision is the whole of
     * what keeps such a client in contact with this server. It is the one thing
     * every answer here travels over.
     */
    #[Test]
    public function aClientOfferingARevisionThisTransportCannotSpeakIsAnsweredWithOneItCan(): void
    {
        $result = $this->call([$this->request(1, 'initialize', [
            'protocolVersion' => '2026-07-28',
            'capabilities' => new \stdClass(),
            'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
        ])])[1];

        self::assertSame(self::PROTOCOL_VERSION, $result['result']['protocolVersion']);
        self::assertArrayNotHasKey('error', $result);
    }

    /**
     * A project whose task skills nobody has updated gets them back before its
     * first call. The server says so on both channels it has at start.
     *
     * To say it was all this did, and a report needs somebody to hear it. On
     * the machine behind `D-DIS-021`, twelve projects had drifted and none of
     * them had a reader. So the server puts the copies back, and both channels
     * still speak. stderr with what differed, for whoever is at the terminal.
     * The instructions with the one sentence the budget has room for. The
     * client read that directory when the session opened rather than now —
     * `R-DIS-025`, `D-DIS-013`.
     */
    #[Requirement('R-DIS-025')]
    #[Decision('D-DIS-013')]
    #[Decision('D-DIS-021')]
    #[Test]
    public function aProjectWhoseSkillsNobodyHasUpdatedHasThemPutBack(): void
    {
        $this->temporaryRoot = sys_get_temp_dir() . '/typo3-dev-companion-stale-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($this->temporaryRoot));
        self::assertSame(0, $this->install($this->temporaryRoot));

        $stderr = null;
        $current = $this->call([$this->request(1, 'initialize', [
            'protocolVersion' => self::PROTOCOL_VERSION,
            'capabilities' => new \stdClass(),
            'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
        ])], $this->temporaryRoot, $stderr)[1];
        self::assertStringNotContainsString('stale', $current['result']['instructions']);
        self::assertStringNotContainsString('typo3-dev-companion update', (string) $stderr);

        // What every release of this package does to a project that installed
        // an earlier one, and what nothing said until now.
        Directory::remove($this->temporaryRoot . '/.agents/skills');

        // Asked not to, the server says it and changes nothing. The read is the
        // same either way, and only what follows it moves.
        $reported = $this->call([$this->request(1, 'initialize', [
            'protocolVersion' => self::PROTOCOL_VERSION,
            'capabilities' => new \stdClass(),
            'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
        ])], $this->temporaryRoot, $stderr, [Entrypoint::REFRESH => 'off'])[1];
        self::assertStringStartsWith(Installer::NOTICE, $reported['result']['instructions']);
        self::assertDirectoryDoesNotExist($this->temporaryRoot . '/.agents/skills');

        $result = $this->call([$this->request(1, 'initialize', [
            'protocolVersion' => self::PROTOCOL_VERSION,
            'capabilities' => new \stdClass(),
            'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
        ])], $this->temporaryRoot, $stderr)[1];

        self::assertStringStartsWith(Installer::REFRESHED, $result['result']['instructions']);
        self::assertStringContainsString('checkout', $result['result']['instructions'], 'the routing is still there');
        self::assertLessThanOrEqual(
            Coverage::INSTRUCTIONS_BUDGET,
            mb_strlen($result['result']['instructions']),
        );
        self::assertStringContainsString('nothing is published at .agents/skills', (string) $stderr);
        self::assertFileEquals(
            Paths::root() . '/skills/typo3-extension-health/SKILL.md',
            $this->temporaryRoot . '/.agents/skills/typo3-extension-health/SKILL.md',
        );

        // The next start has nothing to say, which is what says the record went
        // out too and not only the files.
        $settled = $this->call([$this->request(1, 'initialize', [
            'protocolVersion' => self::PROTOCOL_VERSION,
            'capabilities' => new \stdClass(),
            'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
        ])], $this->temporaryRoot, $stderr)[1];
        self::assertStringNotContainsString('stale', $settled['result']['instructions']);
        self::assertStringNotContainsString('refreshed', $settled['result']['instructions']);
    }

    private function install(string $directory): int
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-dev-companion', 'install'],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $directory,
        );
        self::assertIsResource($process);
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }

        return proc_close($process);
    }

    #[Test]
    public function everyToolIsListedWithItsSchemasAndAnnotations(): void
    {
        $tools = $this->session([$this->request(2, 'tools/list')])[2]['result']['tools'];

        self::assertNotSame([], $tools);
        foreach ($tools as $tool) {
            self::assertArrayHasKey('inputSchema', $tool);
            self::assertArrayHasKey('outputSchema', $tool, $tool['name'] . ' has no output schema');
            self::assertArrayHasKey('annotations', $tool, $tool['name'] . ' has no annotations');
        }

        self::assertContains('typo3_server_scope', array_column($tools, 'name'));
    }

    #[Requirement('R-GUI-005')]
    #[Test]
    public function theCommitMessageGuideIsAvailableAsAPrompt(): void
    {
        $prompts = $this->session([$this->request(2, 'prompts/list')])[2]['result']['prompts'];
        self::assertContains('commit_message', array_column($prompts, 'name'));
        // Every argument says what it is, which the SDK reads off the
        // handler's `@param` tags and a slash-command client shows.
        foreach ($prompts[0]['arguments'] as $argument) {
            self::assertNotSame('', $argument['description'] ?? '', $argument['name'] . ' says nothing');
        }

        $result = $this->session([$this->request(2, 'prompts/get', [
            'name' => 'commit_message',
            'arguments' => [
                'summary' => 'Explain the prompt primitive',
                'workflow' => 'project',
            ],
        ])])[2]['result'];

        self::assertSame('user', $result['messages'][0]['role']);
        self::assertStringContainsString(
            '[TASK] Explain the prompt primitive',
            $result['messages'][0]['content']['text']
        );
    }

    /**
     * The other prompt, offered because this checkout is where the feedback
     * channel is — `D-FBK-048`. What it hands over is the file the page
     * includes, which is what holds the two to one text.
     */
    #[Decision('D-FBK-048')]
    #[Test]
    public function theDebriefIsAvailableAsAPromptTakingNoArguments(): void
    {
        $prompts = $this->session([$this->request(2, 'prompts/list')])[2]['result']['prompts'];
        $debrief = array_values(array_filter(
            $prompts,
            static fn(array $prompt): bool => $prompt['name'] === 'debrief',
        ));

        self::assertCount(1, $debrief);
        self::assertSame([], $debrief[0]['arguments'] ?? []);

        $result = $this->session([$this->request(2, 'prompts/get', ['name' => 'debrief'])])[2]['result'];

        self::assertSame('user', $result['messages'][0]['role']);
        self::assertSame(
            (string) file_get_contents(Paths::debrief()),
            $result['messages'][0]['content']['text'],
        );
    }

    /**
     * The text block is the answer as prose and not the data again. The
     * specification's SHOULD asks for the serialized JSON there, and
     * `D-ANS-167` says why this server answers a text-reading client with
     * prose instead.
     */
    #[Decision('D-ANS-167')]
    #[Test]
    public function aToolCallReturnsTextAndStructuredContent(): void
    {
        $result = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_component_lookup',
            'arguments' => ['query' => 'badge'],
        ])])[2]['result'];

        self::assertFalse($result['isError']);
        self::assertSame('text', $result['content'][0]['type']);
        self::assertNotSame('', $result['content'][0]['text']);
        self::assertNull(json_decode($result['content'][0]['text']), 'the text block is prose, not the data');
        self::assertGreaterThan(0, $result['structuredContent']['matchCount']);
        self::assertNotSame([], $result['structuredContent']['components']);
    }

    /**
     * A question about an installation, asked where there is none, over the
     * wire a client actually reads. Nothing failed, so nothing is an error —
     * `D-ANS-005`. What comes back is the unsupported answer and the caller's
     * own query. No count, no flag and no empty list to read as a result.
     */
    #[Requirement('R-ANS-001')]
    #[Decision('D-ANS-005')]
    #[Test]
    public function aQuestionThatCannotBeAnsweredHereIsStillAnAnswer(): void
    {
        $this->temporaryRoot = sys_get_temp_dir() . '/typo3-dev-companion-nothing-' . bin2hex(random_bytes(6));
        mkdir($this->temporaryRoot, 0o777, true);

        $result = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_icon_lookup',
            'arguments' => ['query' => 'publish'],
        ])], $this->temporaryRoot)[2]['result'];

        self::assertFalse($result['isError'], 'an unmet precondition is not a tool error');
        self::assertSame(['query', 'unsupported'], self::sorted($result['structuredContent']));
        self::assertSame('no-installation', $result['structuredContent']['unsupported']['cause']);
        self::assertContains(
            $this->temporaryRoot,
            $result['structuredContent']['unsupported']['searched'],
            'the answer does not name where it looked'
        );
        self::assertStringContainsString('not answerable here', $result['content'][0]['text']);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<int, string>
     */
    private static function sorted(array $data): array
    {
        $keys = array_keys($data);
        sort($keys);

        return $keys;
    }

    /**
     * The `typo3://` scheme is what a client addresses this corpus by. The
     * server serves the index under it over the wire rather than only in a unit
     * — `D-SCO-010`.
     */
    #[Decision('D-SCO-010')]
    #[Test]
    public function theKnowledgeIndexIsServedWithTheScope(): void
    {
        $result = $this->session([$this->request(2, 'resources/read', ['uri' => 'typo3://guides'])])[2]['result'];

        $index = json_decode($result['contents'][0]['text'], true);
        self::assertIsArray($index);
        self::assertArrayHasKey('purpose', $index);
        self::assertArrayHasKey('documents', $index);
        self::assertContains('core/contribution/rules', array_column($index['documents'], 'id'));
    }

    /**
     * The list a host picks from, on the wire that carries it. What the
     * definitions say is `ResourceSurfaceTest`; this is that the SDK passes the
     * fields on rather than drops the ones it does not use itself.
     */
    #[Requirement('R-ANS-022')]
    #[Test]
    public function theResourceListCarriesWhatAPickerChoosesBy(): void
    {
        $result = $this->session([$this->request(2, 'resources/list')])[2]['result'];

        $offered = array_column($result['resources'], null, 'uri');
        $coreOnly = $offered['typo3://guides/core/contribution/rules'];
        self::assertStringContainsString('does not transfer', $coreOnly['description']);
        self::assertGreaterThan(0, $coreOnly['size']);
        self::assertLessThan(
            $offered['typo3://guides/extension/testing/phpunit']['annotations']['priority'],
            $coreOnly['annotations']['priority'],
        );
    }

    #[Test]
    public function aKnowledgeDocumentIsServedAsMarkdown(): void
    {
        $result = $this->session([$this->request(2, 'resources/read', [
            'uri' => 'typo3://guides/core/contribution/rules',
        ])])[2]['result'];

        self::assertStringContainsString('# TYPO3 Core Contribution Rules', $result['contents'][0]['text']);
    }

    /**
     * The second family on the same wire: a task workflow, and the file it
     * sends its reader to.
     *
     * That second read is what the URI shape exists for. `references/base.md`
     * is a file in no skill in this checkout; `Installer` writes it when it
     * publishes one. The client this family is for is precisely the one that
     * never ran that install. Resolved against the URI of the body, the link
     * the body writes is the URI this reads. The SDK matches it against the
     * registered template.
     */
    #[Requirement('R-ANS-022')]
    #[Test]
    public function aTaskWorkflowIsServedWithWhatItSendsItsReaderTo(): void
    {
        $body = 'typo3://skill/typo3-extension-testing/SKILL.md';
        $responses = $this->session([
            $this->request(2, 'resources/list'),
            $this->request(3, 'resources/read', ['uri' => $body]),
            $this->request(4, 'resources/read', ['uri' => dirname($body) . '/references/base.md']),
        ]);

        $offered = array_column($responses[2]['result']['resources'], null, 'uri');
        self::assertStringContainsString('not a page to read', $offered[$body]['description']);
        self::assertGreaterThan(0, $offered[$body]['size']);
        self::assertLessThan(
            $offered[$body]['annotations']['priority'],
            $offered['typo3://skill/typo3-core-patch-review/SKILL.md']['annotations']['priority'],
        );

        self::assertStringContainsString(
            '](references/base.md)',
            $responses[3]['result']['contents'][0]['text'],
        );
        self::assertStringContainsString(
            'typo3_project_describe',
            $responses[4]['result']['contents'][0]['text'],
        );
    }

    /**
     * The same family as the Skills extension declares it, on the wire a host
     * that speaks the extension reads. The host issues `skills/list` only after
     * the declaration, verifies every file it then reads against the manifest,
     * and stops on a URI that names no skill. So the case is the declaration,
     * the digest against the read, and the error code the spec fixes.
     */
    #[Decision('D-ANS-163')]
    #[Test]
    public function theSkillsExtensionListsEachWorkflowWithTheManifestAHostVerifiesAReadBy(): void
    {
        $body = 'typo3://skill/typo3-extension-testing/SKILL.md';
        $responses = $this->session([
            $this->request(2, 'skills/list', []),
            $this->request(3, 'skills/get', ['uri' => $body]),
            $this->request(4, 'resources/read', ['uri' => $body]),
            $this->request(5, 'skills/get', ['uri' => 'typo3://skill/nobody-published-this/SKILL.md']),
        ]);

        self::assertSame(['io.modelcontextprotocol/skills' => []], $responses[1]['result']['capabilities']['extensions']);

        $list = $responses[2]['result'];
        self::assertSame('complete', $list['resultType']);
        self::assertArrayHasKey('ttlMs', $list);
        self::assertArrayHasKey('cacheScope', $list);
        self::assertSame(Installer::skills(), array_map(
            static fn(array $skill): string => $skill['frontmatter']['name'],
            $list['skills'],
        ));

        $entry = $responses[3]['result']['skill'];
        self::assertContains($entry, $list['skills'], 'skills/get answers with another entry than the list');
        $read = $responses[4]['result']['contents'][0]['text'];
        self::assertSame($body, $entry['resources'][0]['uri']);
        self::assertSame('sha256:' . hash('sha256', $read), $entry['resources'][0]['digest']);
        self::assertSame(strlen($read), $entry['resources'][0]['size']);
        self::assertContains(dirname($body) . '/references/base.md', array_column($entry['resources'], 'uri'));

        self::assertSame(-32602, $responses[5]['error']['code']);
    }

    #[Test]
    public function invalidArgumentsAreRejectedBeforeTheToolRuns(): void
    {
        $response = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_rule_lookup',
            'arguments' => new \stdClass(),
        ])])[2];

        self::assertSame(-32602, $response['error']['code']);
        self::assertStringContainsString('query', $response['error']['message']);
    }

    /**
     * What a tool refuses, over the wire that decides whether the caller ever
     * reads it.
     *
     * A session that filed five feedback got "Error while executing tool" and
     * nothing else. It spent its remaining calls on a bisection of which
     * parameter had done it — `D-ANS-143`. The refusal it hit says what to send
     * instead, and the SDK dropped every word of it.
     */
    #[Decision('D-ANS-143')]
    #[Test]
    public function whatAToolRefusesIsSaidToTheCallerThatSentIt(): void
    {
        $answers = $this->session([
            $this->request(2, 'tools/call', ['name' => 'typo3_feedback_record', 'arguments' => [
                'observation' => '   ',
                'model' => 'phpunit',
            ]]),
            $this->request(3, 'tools/call', ['name' => 'typo3_feedback_record', 'arguments' => [
                'observation' => self::MARKER . ' a report whose suggestion carried the call',
                'model' => 'phpunit',
                'suggestion' => "what to do instead\n<parameter name=\"query\">the rest of the call</invoke>",
            ]]),
        ]);

        self::assertArrayNotHasKey('error', $answers[2]);
        self::assertTrue($answers[2]['result']['isError']);
        self::assertSame('An observation is required.', $answers[2]['result']['content'][0]['text']);

        // The refusal that names the parameter, which is the one behind this
        // test. Nothing goes to disk, and the caller learns why.
        self::assertTrue($answers[3]['result']['isError']);
        self::assertStringContainsString('The suggestion carries the frame', $answers[3]['result']['content'][0]['text']);
    }

    /**
     * The one case `read()` cannot get a handle in, over the wire where a lost
     * answer costs the most.
     *
     * `composer.json` requires ext-curl, and two ways in pass it: an install
     * with the platform check off, and an entry that starts a PHP other than
     * the one Composer resolved against. PHP's own error named a function and
     * no extension — `D-ANS-155`. `disable_functions` takes the function away
     * from a PHP that has it, so the case runs on every PHP the suite runs on.
     */
    #[Decision('D-ANS-155')]
    #[Test]
    public function aPhpWithoutCurlIsRefusedWithTheExtensionNamed(): void
    {
        $answers = $this->session([
            $this->request(2, 'tools/call', ['name' => 'typo3_forge_lookup', 'arguments' => ['issue' => '110348']]),
            $this->request(3, 'tools/call', ['name' => 'typo3_server_scope', 'arguments' => new \stdClass()]),
        ], php: ['-d', 'disable_functions=curl_init']);

        self::assertArrayNotHasKey('error', $answers[2]);
        self::assertTrue($answers[2]['result']['isError']);
        $said = $answers[2]['result']['content'][0]['text'];
        self::assertStringContainsString('ext-curl', $said);
        self::assertStringContainsString(PHP_BINARY, $said);
        self::assertStringNotContainsString('undefined function', $said);

        // The read is the one answer lost. The server stands, and the next
        // request is answered.
        self::assertArrayNotHasKey('error', $answers[3]);
        self::assertFalse($answers[3]['result']['isError'] ?? false);
    }

    /**
     * The one argument that ever declared two types, over the wire that decides
     * whether a client can compose the call at all.
     *
     * `tool` is a plain string since `D-ANS-017`, and several travel separated
     * by commas. A list a client sends instead gets a refusal that names the
     * type it should have used. Both halves belong over the wire, because
     * `FeedbackTest` calls `Channel::record` directly and the recorder still
     * takes a list.
     */
    #[Requirement('R-FBK-001')]
    #[Decision('D-ANS-017')]
    #[Test]
    public function aListOfToolNamesIsRefusedWithTheTypeItWanted(): void
    {
        $answers = $this->session([
            $this->request(2, 'tools/call', ['name' => 'typo3_feedback_record', 'arguments' => [
                'observation' => self::MARKER . ' both lookups went quiet',
                'model' => 'phpunit',
                'tool' => 'typo3_label_lookup, typo3_icon_lookup',
            ]]),
            $this->request(3, 'tools/call', ['name' => 'typo3_feedback_record', 'arguments' => [
                'observation' => self::MARKER . ' recorded with a list',
                'model' => 'phpunit',
                'tool' => ['typo3_label_lookup', 'typo3_icon_lookup'],
            ]]),
        ]);

        $recorded = (string) file_get_contents(
            dirname($this->ownFeedbackDirectory()) . '/' . $answers[2]['result']['structuredContent']['file']
        );
        self::assertStringContainsString('tool: typo3_label_lookup, typo3_icon_lookup', $recorded);

        self::assertSame(-32602, $answers[3]['error']['code'], 'the schema still declares more than one type');
        self::assertStringContainsString('/tool', $answers[3]['error']['message']);
        self::assertStringContainsString('string', $answers[3]['error']['message']);
        self::assertStringContainsString('array', $answers[3]['error']['message']);
    }

    /**
     * The one input-side alternative on the surface, over the wire that
     * produced the complaint. A call that carries neither branch gets a refusal
     * for both at once, one sentence per branch. The SDK formats the leaves of
     * a failed `oneOf` one by one. A session read the last half as "page is
     * required" and reported the tool as unusable for search. The keyword stays
     * and the rule stands where the caller composes the call, so this holds
     * what a client still validates against. `D-ANS-012` says what would show
     * that the fix hit the wrong half.
     */
    #[Decision('D-ANS-012')]
    #[Test]
    public function aCallCarryingNeitherOfTwoAlternativeArgumentsNamesBoth(): void
    {
        $response = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_documentation_lookup',
            'arguments' => ['targetVersion' => '14.3'],
        ])])[2];

        self::assertSame(-32602, $response['error']['code']);
        self::assertStringContainsString('queries', $response['error']['message']);
        self::assertStringContainsString('page', $response['error']['message']);

        $documentation = null;
        foreach ($this->session([$this->request(2, 'tools/list')])[2]['result']['tools'] as $tool) {
            if ($tool['name'] === 'typo3_documentation_lookup') {
                $documentation = $tool;
            }
        }

        self::assertNotNull($documentation, 'the tool that carries the alternative is not offered');
        self::assertSame(['targetVersion'], $documentation['inputSchema']['required']);
        self::assertSame(
            [['queries'], ['page']],
            array_column($documentation['inputSchema']['oneOf'], 'required'),
            'the alternative no longer reaches a client that reads oneOf',
        );
    }

    /**
     * The session of `feedback/2026-08-04-175819` spelled the argument `query`,
     * the way five other lookups here spell theirs. The validator dropped the
     * unknown property, both `oneOf` branches failed, and the message named two
     * arguments the call had not been about — `D-ANS-053`.
     */
    #[Decision('D-ANS-053')]
    #[Test]
    public function aCallNamingAnArgumentTheToolDoesNotHaveIsRejectedByThatName(): void
    {
        $response = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_documentation_lookup',
            'arguments' => ['query' => 'extension documentation', 'targetVersion' => '14.3'],
        ])])[2];

        self::assertSame(-32602, $response['error']['code']);
        self::assertStringContainsString('query', $response['error']['message']);
        self::assertStringNotContainsString(
            'Missing required properties',
            $response['error']['message'],
            'the rejection describes a call the caller did not make',
        );
    }

    #[Test]
    public function anUnknownToolIsAnError(): void
    {
        $response = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_does_not_exist',
            'arguments' => new \stdClass(),
        ])])[2];

        self::assertArrayHasKey('error', $response);
    }

    #[Test]
    public function anUnknownResourceIsAnError(): void
    {
        $response = $this->session([$this->request(2, 'resources/read', ['uri' => 'typo3://guides/nope'])])[2];

        self::assertArrayHasKey('error', $response);
    }

    /**
     * A client writes its next request while the server still works on the last
     * one. Where that work is a console command, the command inherits the
     * server's stdin unless it gets one of its own. `ddev exec` reads stdin to
     * the end, so it eats the queued request and the session hangs on an answer
     * that can never come. Both runs of `REVIEW-02` in an extension checkout
     * died here, 24 minutes apart, with no error on either side.
     */
    #[Requirement('R-DIS-018')]
    #[Test]
    public function aRequestBehindOneThatRunsTheConsoleIsStillAnswered(): void
    {
        $root = $this->installationWithADrainingConsole();
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-dev-companion'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $root,
            getenv() + ['TYPO3_DEV_COMPANION_CONSOLE' => PHP_BINARY . ' ' . $root . '/console.php', Paths::FEEDBACK_VARIABLE => $this->ownFeedbackDirectory()]
        );
        self::assertIsResource($process);

        fwrite($pipes[0], implode("\n", [
            $this->request(1, 'initialize', [
                'protocolVersion' => self::PROTOCOL_VERSION,
                'capabilities' => new \stdClass(),
                'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
            ]),
            (string) json_encode(['jsonrpc' => '2.0', 'method' => 'notifications/initialized']),
            $this->request(2, 'tools/call', ['name' => 'typo3_fluid_namespace_list', 'arguments' => new \stdClass()]),
        ]) . "\n");
        // Late enough that the console command runs, which is the only moment
        // this can go wrong. Written earlier, the line sits in the server's own
        // read buffer, where no child can reach it.
        usleep(200_000);
        fwrite($pipes[0], $this->request(3, 'tools/call', ['name' => 'typo3_server_scope', 'arguments' => new \stdClass()]) . "\n");
        fclose($pipes[0]);

        $stdout = (string) stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        $answered = [];
        foreach (explode("\n", trim($stdout)) as $line) {
            $decoded = json_decode(trim($line), true);
            if (is_array($decoded) && isset($decoded['id'])) {
                $answered[] = $decoded['id'];
            }
        }

        self::assertContains(2, $answered, 'the console command itself went unanswered');
        self::assertContains(3, $answered, 'the console command swallowed the request queued behind it');
    }

    /**
     * The one thing a client never tells this server and it has to work out for
     * itself: which installation the session is in.
     *
     * Thirteen of the twenty tools answer differently once discovery finds one,
     * and three of them are not even on offer. So a server that reads nothing
     * answers about TYPO3 in general where the question was about a checkout,
     * and says so nowhere. Every test that hands `Instance` a directory itself
     * covers the mechanism a class at a time. What only this can cover is that
     * something hands one in at all. The line that does it is in the
     * entrypoint, and with it deleted the rest of the suite stays green.
     */
    #[Requirement('R-DIS-022')]
    #[Test]
    public function theServerWorksOutWhichInstallationItWasStartedIn(): void
    {
        $root = $this->installation();

        $scope = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_server_scope',
            'arguments' => new \stdClass(),
        ])], $root)[2]['result']['structuredContent']['installation'];

        self::assertTrue($scope['found'], 'the server was started in an installation and did not find it');
        self::assertSame($root, $scope['root']);
        self::assertSame(Instance::KIND_COMPOSER_PROJECT, $scope['kind']);
        self::assertSame(Instance::VIA_DISCOVERY, $scope['via']);
    }

    /**
     * A client starts the server in the directory the session is in, and that
     * is rarely the project root. It is wherever the file under edit lives.
     * Finding the installation from there is the walk-up, and it is the half
     * that makes the answer right in practice rather than in a fixture.
     */
    #[Requirement('R-DIS-022')]
    #[Test]
    public function itWalksUpToTheInstallationFromInsideIt(): void
    {
        $root = $this->installation();
        $inside = $root . '/packages/my_sitepackage/Classes/Controller';
        mkdir($inside, 0o777, true);

        $scope = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_server_scope',
            'arguments' => new \stdClass(),
        ])], $inside)[2]['result']['structuredContent']['installation'];

        self::assertTrue($scope['found'], 'started four directories in, the walk-up gave up');
        self::assertSame($root, $scope['root']);
        self::assertSame($inside, $scope['startedFrom']);
    }

    /** A Composer project with TYPO3 in it, which is what most callers stand in. */
    private function installation(): string
    {
        $root = sys_get_temp_dir() . '/typo3-dev-companion-discovery-' . bin2hex(random_bytes(6));
        $this->temporaryRoot = $root;
        mkdir($root . '/vendor/composer', 0o777, true);
        mkdir($root . '/vendor/typo3/cms-core', 0o777, true);
        file_put_contents($root . '/composer.json', (string) json_encode([
            'name' => 'acme/site',
            'require' => ['typo3/cms-core' => '^13.4'],
        ], JSON_THROW_ON_ERROR));
        file_put_contents($root . '/vendor/composer/installed.json', (string) json_encode([
            'packages' => [[
                'name' => 'typo3/cms-core',
                'version' => '13.4.33',
                'type' => 'typo3-cms-framework',
                'install-path' => '../typo3/cms-core',
            ]],
        ], JSON_THROW_ON_ERROR));

        return $root;
    }

    /**
     * An installation whose console takes a moment and then reads its stdin to
     * the end, the way `ddev exec` does.
     */
    private function installationWithADrainingConsole(): string
    {
        $root = sys_get_temp_dir() . '/typo3-dev-companion-stdio-' . bin2hex(random_bytes(6));
        $this->temporaryRoot = $root;
        mkdir($root . '/typo3/sysext/core', 0o777, true);
        file_put_contents($root . '/composer.json', (string) json_encode([
            'name' => 'typo3/cms',
            'type' => 'typo3-cms-core',
        ], JSON_THROW_ON_ERROR));
        file_put_contents($root . '/typo3/sysext/core/composer.json', (string) json_encode([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ], JSON_THROW_ON_ERROR));
        file_put_contents($root . '/console.php', "<?php\nusleep(400000);\nstream_get_contents(STDIN);\necho '[]';\n");

        return $root;
    }

    /**
     * @param array<int, string> $requests
     * @param array<int, string> $php options for the interpreter that runs the server
     * @return array<int, array<string, mixed>> responses by request id
     */
    private function session(array $requests, ?string $cwd = null, array $php = []): array
    {
        return $this->call(array_merge([
            $this->request(1, 'initialize', [
                'protocolVersion' => self::PROTOCOL_VERSION,
                'capabilities' => new \stdClass(),
                'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
            ]),
            (string) json_encode(['jsonrpc' => '2.0', 'method' => 'notifications/initialized']),
        ], $requests), $cwd, php: $php);
    }

    /**
     * @param array<int, string> $lines
     * @param string|null $stderr what the server said beside the protocol
     * @param array<string, string> $environment
     * @param array<int, string> $php options for the interpreter that runs the server
     * @param-out string $stderr
     * @return array<int, array<string, mixed>>
     */
    private function call(
        array $lines,
        ?string $cwd = null,
        ?string &$stderr = null,
        array $environment = [],
        array $php = [],
    ): array {
        // The working directory is the whole of what a client tells this server
        // about where it is. So a test about discovery is a test about this
        // argument.
        $process = proc_open(
            [PHP_BINARY, ...$php, Paths::root() . '/bin/typo3-dev-companion'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $cwd,
            $environment + getenv() + [Paths::FEEDBACK_VARIABLE => $this->ownFeedbackDirectory()],
        );
        self::assertIsResource($process);

        fwrite($pipes[0], implode("\n", $lines) . "\n");
        fclose($pipes[0]);

        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($process);

        self::assertSame(0, $status, 'the server exited with ' . $status . ': ' . $stderr);

        $responses = [];
        foreach (explode("\n", trim($stdout)) as $line) {
            if (trim($line) === '') {
                continue;
            }
            $decoded = json_decode($line, true);
            self::assertIsArray($decoded, 'the server wrote a non-JSON line: ' . $line);
            $responses[$decoded['id'] ?? 0] = $decoded;
        }

        return $responses;
    }

    /** @param array<string, mixed>|null $params */
    private function request(int $id, string $method, ?array $params = null): string
    {
        $request = ['jsonrpc' => '2.0', 'id' => $id, 'method' => $method];
        if ($params !== null) {
            $request['params'] = $params;
        }

        return (string) json_encode($request);
    }
}
