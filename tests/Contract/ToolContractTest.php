<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Contract;

use Mcp\Capability\Discovery\SchemaValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Manual\CoreChangelog;
use TYPO3\DevCompanion\Result\Unsupported;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\ToolCalls;

/**
 * What every tool promises its callers. A declared input and output schema,
 * annotations, a text answer, and structured data that validates against the
 * schema, on every path, a miss included.
 *
 * That is where the unanswerable paths stand, and it is all `D-DIS-012` has.
 * Which driver left a schema question unanswerable needs a MySQL installation
 * with its server stopped, which no environment here holds.
 */
#[Requirement('R-ANS-003')]
#[Requirement('R-GUI-002')]
final class ToolContractTest extends TestCase
{
    #[Requirement('R-DOC-001')]
    #[Decision('D-ANS-017')]
    #[Test]
    public function everyToolDeclaresSchemasAndAnnotations(): void
    {
        foreach (Registry::definitions() as $definition) {
            $name = $definition['name'];

            self::assertNotSame('', $definition['description'], $name . ' has no description');
            self::assertSame('object', $definition['inputSchema']['type'], $name . ' has no object input schema');
            self::assertNotNull($definition['outputSchema'], $name . ' has no output schema');
            self::assertSame('object', $definition['outputSchema']['type']);

            self::assertSame(
                ['readOnlyHint', 'destructiveHint', 'idempotentHint', 'openWorldHint'],
                array_keys($definition['annotations']),
                $name . ' is missing annotations'
            );
            // The tools that reach a host outside this package. Every other
            // answer comes with the package or from the installation, and a
            // caller reads that difference off this flag before it calls —
            // `D-ANS-017`.
            self::assertSame(
                in_array($name, [
                    'typo3_documentation_lookup',
                    // The inventories those same manuals publish, read for the
                    // identifiers rather than for the pages — `D-ANS-119`.
                    'typo3_permalink_lookup',
                    'typo3_forge_lookup',
                    'typo3_gerrit_lookup',
                    // The changelog as the host renders it — `D-ANS-165`.
                    'typo3_changelog_lookup',
                    // What the extension registry has published, which the
                    // repository under audit cannot say — `D-FBK-051`.
                    'typo3_ter_lookup',
                ], true),
                $definition['annotations']['openWorldHint'],
                $name . ' has the wrong open-world annotation',
            );
        }
    }

    /**
     * A title is for the person a client lists the tool to, and it stays short
     * enough for a listing and a permission dialog. A title that recites the
     * name in plain words says nothing the name did not, which is the defect
     * `AGENTS.md` names for a title that repeats its statement.
     */
    #[Test]
    public function everyToolCarriesATitleAPersonReadsAtAGlance(): void
    {
        foreach (Registry::definitions() as $definition) {
            $name = $definition['name'];
            $title = $definition['title'];

            self::assertNotSame('', $title, $name . ' has no title');
            self::assertLessThanOrEqual(6, str_word_count($title), $name . ' has a title over six words');
            self::assertStringEndsNotWith('.', $title, $name . ' ends its title with a period');
            self::assertNotSame(
                str_replace('_', ' ', substr($name, strlen('typo3_'))),
                strtolower($title),
                $name . ' recites its name as the title',
            );
        }
    }

    /**
     * A client that defers these tools searches a name, a description and the
     * argument descriptions for the subject a task is about, and reads nothing
     * else this server sends. The tool with the widest corpus named two of its
     * subjects there, both to withhold them — `D-AUD-019`.
     */
    #[Decision('D-AUD-019')]
    #[Test]
    public function everyHintSubjectStandsInTheTextAClientSearchesAToolBy(): void
    {
        $searchable = '';
        foreach (Registry::definitions() as $definition) {
            $searchable .= ' ' . $definition['name'] . ' ' . $definition['description'];
            foreach ((array) ($definition['inputSchema']['properties'] ?? []) as $argument => $schema) {
                $searchable .= ' ' . $argument . ' ' . (string) ($schema['description'] ?? '');
            }
        }

        $subjects = Hints::subjects();
        self::assertNotSame([], $subjects);
        foreach ($subjects as $subject) {
            self::assertStringContainsStringIgnoringCase($subject, $searchable, 'no tool names ' . $subject . ' where a client searches');
        }
    }

    /**
     * An argument that excludes another says so in its own description.
     *
     * A `oneOf` on the way in is a rule declared in the one place nothing reads
     * out to a caller. `required` names neither branch, so a caller that
     * composes the call sees two plain optional arguments (`D-ANS-012`). The
     * answer was to keep the keyword and state the rule where the caller writes
     * the call. The descriptions are on the wire in `tools/list`, which is why
     * this holds them rather than the reference alone — `D-ANS-054`.
     */
    #[Decision('D-ANS-012')]
    #[Decision('D-ANS-054')]
    #[Test]
    public function anArgumentInAnAlternativeNamesTheOnesItExcludes(): void
    {
        $declaring = [];
        foreach (Registry::definitions() as $definition) {
            $schema = $definition['inputSchema'];
            if (!isset($schema['oneOf'])) {
                continue;
            }
            $declaring[] = $definition['name'];

            $alternatives = array_merge(...array_map(
                static fn(array $branch): array => (array) ($branch['required'] ?? []),
                (array) $schema['oneOf'],
            ));

            foreach ($alternatives as $argument) {
                $description = (string) ($schema['properties'][$argument]['description'] ?? '');
                foreach (array_diff($alternatives, [$argument]) as $excluded) {
                    self::assertStringContainsString(
                        $excluded,
                        $description,
                        $definition['name'] . ': ' . $argument . ' excludes ' . $excluded . ' and does not say so',
                    );
                }
            }
        }

        self::assertSame(
            [
                'typo3_rule_lookup',
                'typo3_documentation_lookup',
                'typo3_forge_lookup',
                'typo3_gerrit_lookup',
            ],
            $declaring,
            'the input-side alternatives this holds are not the ones that exist',
        );
    }

    /**
     * No argument declares more than one type.
     *
     * The one union the surface had is a plain string since `D-ANS-017`, and
     * this is what says the shape did not come back somewhere else. A union
     * declared in a second tool is also that entry's third **Wrong if**. So a
     * session that means to try one deletes this case and says so.
     *
     * Output schemas are not held here, because every union in them is a
     * nullable field rather than an alternative a caller has to produce.
     */
    #[Decision('D-ANS-017')]
    #[Test]
    public function noArgumentDeclaresMoreThanOneType(): void
    {
        $unions = [];
        foreach (Registry::definitions() as $definition) {
            foreach ($definition['inputSchema']['properties'] ?? [] as $argument => $schema) {
                if (is_array($schema['type'] ?? null) || isset($schema['anyOf'])) {
                    $unions[] = $definition['name'] . ' /' . $argument;
                }
            }
        }

        self::assertSame([], $unions, 'an argument a client has to produce declares two types');
    }

    /**
     * No `type` in either schema is a list, however deep it sits.
     *
     * A field that may be null is two `anyOf` branches, which is what
     * `Schema::nullable()` writes. The list is legal JSON Schema, and several
     * MCP clients read `type` as one string and refuse the tool or drop the
     * constraint over it — `D-ANS-160`.
     */
    #[Decision('D-ANS-160')]
    #[Test]
    public function noSchemaDeclaresTypeAsAList(): void
    {
        $lists = [];
        foreach (Registry::definitions() as $definition) {
            foreach (['inputSchema', 'outputSchema'] as $side) {
                array_push($lists, ...self::typeLists($definition[$side], $definition['name'] . ' ' . $side));
            }
        }

        self::assertSame([], $lists, 'a schema declares type as a list, which a client may read as one string');
    }

    /**
     * Every `type` below the schema that is a list, by its path. A property
     * named `type` is an object, and stays out.
     *
     * @param array<string, mixed> $schema
     * @return list<string>
     */
    private static function typeLists(array $schema, string $path): array
    {
        $type = $schema['type'] ?? null;
        $found = is_array($type) && array_is_list($type) ? [$path . ' => [' . implode(', ', $type) . ']'] : [];
        foreach ($schema as $key => $value) {
            if (is_array($value)) {
                array_push($found, ...self::typeLists($value, $path . '.' . $key));
            }
        }

        return $found;
    }

    /**
     * Every field of every schema says what it is.
     *
     * A field with a description and nothing else is the object spelling of
     * `true`. It accepts any value, and some clients refuse the tool over it.
     * A value that can be several things says which, `Schema::scalar()` and
     * `Schema::any()` — `D-ANS-161`.
     */
    #[Decision('D-ANS-161')]
    #[Test]
    public function everyFieldSaysWhatItIs(): void
    {
        $bare = [];
        foreach (Registry::definitions() as $definition) {
            foreach (['inputSchema', 'outputSchema'] as $side) {
                array_push($bare, ...self::bareFields($definition[$side], $definition['name'] . ' ' . $side));
            }
        }

        self::assertSame([], $bare, 'a field constrains nothing, which a client may refuse');
    }

    /**
     * Every field below the schema with no keyword but its description, by its
     * path.
     *
     * @param array<string, mixed> $schema
     * @return list<string>
     */
    private static function bareFields(array $schema, string $path): array
    {
        $found = [];
        foreach ((array) ($schema['properties'] ?? []) as $name => $field) {
            $field = (array) $field;
            if (array_diff(array_keys($field), ['description']) === []) {
                $found[] = $path . '.' . $name;
            }
            foreach ([$field, ...(array) ($field['anyOf'] ?? [])] as $branch) {
                $branch = (array) $branch;
                array_push($found, ...self::bareFields((array) ($branch['items'] ?? $branch), $path . '.' . $name));
            }
        }

        return $found;
    }

    #[Test]
    public function onlyTheFeedbackToolWrites(): void
    {
        foreach (Registry::definitions() as $definition) {
            self::assertSame(
                $definition['name'] !== 'typo3_feedback_record',
                $definition['annotations']['readOnlyHint'],
                $definition['name'] . ' is annotated with the wrong readOnlyHint'
            );
            self::assertFalse($definition['annotations']['destructiveHint'], $definition['name'] . ' must not destroy anything');
        }
    }

    /**
     * This drives the live documentation calls like every other tool. So a
     * manual that moved fails the contract rather than a page nobody reads —
     * `D-DOC-008`.
     */
    /** @param array<string, mixed> $arguments */
    #[Requirement('R-DOC-001')]
    #[Requirement('R-DOC-002')]
    #[Decision('D-DOC-008')]
    #[DataProvider('toolCalls')]
    #[Test]
    public function aToolCallAnswersWithTextAndMatchingData(string $name, array $arguments): void
    {
        $result = Registry::call($name, $arguments);

        self::assertNotSame('', trim($result->text), $name . ' answered with empty text');
        self::assertNotSame([], $result->data, $name . ' answered without data');

        $schema = $this->outputSchema($name);
        $data = json_decode((string) json_encode($result->data, JSON_THROW_ON_ERROR), true);

        $errors = (new SchemaValidator())->validateAgainstJsonSchema($data, $schema);
        self::assertSame([], $errors, $name . ' broke its output schema: ' . json_encode($errors));
    }

    /**
     * The arguments each installation-backed tool is driven with below. A tool
     * that declares answeredBy and is not named here fails the test rather than
     * skips it. The set derives from the registry, so a new one joins it by
     * existence.
     *
     * @var array<string, array<string, mixed>>
     */
    private const UNANSWERABLE_CALLS = [
        'typo3_icon_lookup' => ['query' => 'publish'],
        'typo3_label_lookup' => ['query' => 'Publish page'],
        'typo3_configuration_lookup' => ['configurationPath' => 'SYS/fluid'],
        'typo3_backend_module_lookup' => ['query' => 'page'],
        'typo3_changelog_lookup' => ['query' => 'deprecation'],
        'typo3_fluid_namespace_list' => [],
        'typo3_schema_lookup' => ['table' => 'tt_content'],
        'typo3_record_lookup' => ['table' => 'tx_acme_thing'],
        'typo3_service_lookup' => ['query' => 'PageRenderer'],
        'typo3_flexform_lookup' => ['table' => 'tt_content', 'field' => 'pi_flexform'],
        'typo3_project_describe' => [],
        'typo3_extension_describe' => ['extension' => 'news'],
    ];

    /**
     * Driven where there is nothing to ask, the answer is the unsupported one
     * and nothing besides. No count to read as a count, no flag to read as a
     * fact, and no empty list in place of a result — R-ANS-001. The reason
     * travels as data and names where discovery looked, which is R-ANS-002 and
     * what META-02 asks for where discovery failed — `D-ANS-005`.
     */
    #[Requirement('R-ANS-001')]
    #[Decision('D-ANS-005')]
    #[Test]
    public function aQuestionThatCannotBeAnsweredHereSaysOnlyThat(): void
    {
        // From a real directory with nothing above it rather than from no
        // directory at all. `searched` is what tells "nothing is here" from
        // "the server started somewhere else", and it only fills where
        // discovery walked.
        Instance::discoverFrom(sys_get_temp_dir());
        Typo3Cli::forget();
        // The changelog answers from docs.typo3.org without an installation,
        // `D-ANS-165`. Sealed, so this is the case where neither answers.
        CoreChangelog::useReader(static fn(string $url): ?string => null);

        foreach (self::installationBackedSchemas() as $name => $schema) {
            self::assertArrayHasKey($name, self::UNANSWERABLE_CALLS, $name . ' answers from the installation and is not driven here');

            $arguments = self::UNANSWERABLE_CALLS[$name];
            $data = Registry::call($name, $arguments)->data;

            self::assertArrayHasKey('unsupported', $data, $name . ' answered without an installation to ask');
            self::assertSame(
                Unsupported::NO_INSTALLATION,
                $data['unsupported']['cause'],
                $name . ' named the wrong cause where there is no installation at all'
            );
            self::assertNotSame('', $data['unsupported']['reason'], $name . ' gave no reason');
            self::assertNotSame([], $data['unsupported']['searched'], $name . ' does not say where it looked');

            // Everything the tool would have answered is absent, and what
            // remains is the caller's own arguments on the way back. That is
            // exactly what the schema still requires, because the answer fields
            // had to leave that list for this shape to be declarable at all.
            $left = array_values(array_diff(array_keys($data), ['unsupported']));
            sort($left);
            $required = $schema['required'];
            sort($required);
            self::assertSame(
                $required,
                $left,
                $name . ' states something about an installation nothing asked, or drops a field it still requires'
            );
            self::assertArrayNotHasKey('answeredBy', $data, $name . ' names a source where none answered');
        }
        CoreChangelog::useReader(null);
    }

    /**
     * The schema says the two are alternatives rather than leaves a client to
     * infer it. That is what keeps the full promise on a hit. The spec tells a
     * client to validate structuredContent. A required list relaxed to suit the
     * other branch would have withdrawn the promise without a word —
     * `D-ANS-005`.
     */
    #[Requirement('R-ANS-001')]
    #[Decision('D-ANS-005')]
    #[Test]
    public function anInstallationBackedSchemaOffersEitherShape(): void
    {
        foreach (self::installationBackedSchemas() as $name => $schema) {
            $branches = $schema['oneOf'];
            self::assertCount(2, $branches, $name . ' does not offer exactly the two');

            [$answered, $unsupported] = $branches;
            self::assertContains('answeredBy', $answered['required'], $name . ' does not promise its source on a hit');
            self::assertNotContains('unsupported', $answered['required'], $name . ' asks for both at once');
            // The echo is in both branches on purpose. What separates them is
            // that one adds the result and the other adds the reason there is
            // none.
            self::assertSame(
                [...$schema['required'], 'unsupported'],
                $unsupported['required'],
                $name . ' asks the unsupported branch for more than the reason and the echo'
            );
            self::assertNotSame(
                [],
                array_diff($answered['required'], $schema['required']),
                $name . ' promises nothing beyond the echo on a hit'
            );
        }
    }

    /**
     * One place builds the unsupported answer, so no path can reach the shape
     * without a reason to hand over. typo3_extension_describe reported every
     * miss as unanswerable, even against an installation that had just listed
     * its packages. The constant it filled from carried the value —
     * `D-ANS-005`.
     */
    #[Requirement('R-ANS-001')]
    #[Decision('D-ANS-005')]
    #[Test]
    public function onlyOneClassBuildsTheUnsupportedAnswer(): void
    {
        $sources = [];
        foreach (Finder::create()->files()->in(dirname(__DIR__, 2) . '/src')->name('*.php') as $file) {
            if (str_contains((string) file_get_contents($file->getPathname()), "'unsupported' => [")) {
                $sources[] = $file->getRelativePathname();
            }
        }

        self::assertSame(['Result/Unsupported.php'], $sources);
    }

    #[Test]
    public function anUnknownToolIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Registry::call('typo3_does_not_exist', []);
    }

    #[Test]
    public function theCommitMessageToolNeedsEitherAMessageOrASummary(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Registry::call('typo3_commit_message_guide', ['issue' => '1']);
    }

    /**
     * Every tool, on a hit and on a miss. The shape has to hold for both, since
     * a miss is where a client is most likely to look at the data.
     *
     * The table is `Upkeep\ToolCalls`, because `bin/cli tools:record` drives
     * the same calls and a second table would drift from this one. In a test
     * run nothing discovers an installation, so every installation-backed entry
     * here exercises the unanswered path. Several of the calls reach
     * docs.typo3.org, and the assertions below do not turn on the host.
     * Unreached, it answers `source-not-answering`, which validates like any
     * other answer — `D-DOC-008`.
     *
     * @return array<string, array{0: string, 1: array<string, mixed>}>
     */
    public static function toolCalls(): array
    {
        return ToolCalls::all();
    }

    /**
     * The tools whose answer belongs to an installation, which is the ones that
     * declare answeredBy. Keyed by name with the schema that says what they
     * promise.
     *
     * @return array<string, array{properties: array<string, mixed>, required: array<int, string>, oneOf: array<int, array{required: array<int, string>}>}>
     */
    private static function installationBackedSchemas(): array
    {
        $schemas = [];
        foreach (Registry::definitions() as $definition) {
            $properties = $definition['outputSchema']['properties'] ?? [];
            if (isset($properties['unsupported'])) {
                $schemas[$definition['name']] = [
                    'properties' => $properties,
                    'required' => $definition['outputSchema']['required'] ?? [],
                    'oneOf' => $definition['outputSchema']['oneOf'] ?? [],
                ];
            }
        }

        self::assertNotSame([], $schemas, 'no tool answers from the installation');

        return $schemas;
    }

    /** @return array<string, mixed> */
    private function outputSchema(string $name): array
    {
        foreach (Registry::definitions() as $definition) {
            if ($definition['name'] === $name) {
                self::assertNotNull($definition['outputSchema']);

                return $definition['outputSchema'];
            }
        }

        self::fail($name . ' is not registered');
    }
}
