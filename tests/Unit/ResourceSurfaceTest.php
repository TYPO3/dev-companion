<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use Mcp\Capability\Registry\ResourceTemplateReference;
use Mcp\Exception\ResourceNotFoundException;
use Mcp\Schema\ResourceDefinition;
use Mcp\Server\ClientGateway;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Sdk\ResourceHandler;
use TYPO3\DevCompanion\Sdk\SkillReferenceHandler;
use TYPO3\DevCompanion\Sdk\Skills;
use TYPO3\DevCompanion\Server\Factory;
use TYPO3\DevCompanion\Server\Installer;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;

/**
 * The typo3:// resources as a host reads them, which is before any call to this
 * server.
 *
 * The model calls a tool mid-task and the tool explains itself in its answer.
 * The application or the user picks a resource out of a list, and they have the
 * list and nothing else. `R-ANS-022` is what follows from that, and this holds
 * the fields the choice turns on.
 */
#[Requirement('R-ANS-022')]
final class ResourceSurfaceTest extends TestCase
{
    #[Test]
    public function everyResourceSaysWhatItIsAndHowMuchReadingItCosts(): void
    {
        foreach (Factory::resources() as $resource) {
            self::assertNotNull(
                $resource->description,
                $resource->uri . ' is offered as a name and a title, which is not enough to pick it by',
            );
            self::assertNotNull($resource->mimeType, $resource->uri . ' does not say what it is served as');
            self::assertSame(
                strlen(self::served($resource)),
                $resource->size,
                $resource->uri . ' declares a size that is not what a client reads',
            );
        }
    }

    /**
     * The order is the whole of what a priority says, so it is what this holds.
     * The index above everything it lists, and what holds wherever the caller
     * works above what stops at the core. It runs over both families, because a
     * picker sorts one list.
     */
    #[Test]
    public function whatAResourceIsWorthOutsideTheCoreDecidesWhereAPickerPutsIt(): void
    {
        $priorities = [];
        foreach (Factory::resources() as $resource) {
            $priority = $resource->annotations?->priority;
            self::assertNotNull($priority, $resource->uri . ' is offered with nothing to sort it by');
            $priorities[$resource->uri] = $priority;
        }

        $coreOnly = [];
        $transferable = [];
        foreach (self::offeredBelowTheIndex() as $uri => $stopsAtTheCore) {
            self::assertLessThan(
                $priorities[ResourceHandler::INDEX_URI],
                $priorities[$uri],
                $uri . ' is offered above the index that says what it is',
            );
            if ($stopsAtTheCore) {
                $coreOnly[] = $priorities[$uri];
            } else {
                $transferable[] = $priorities[$uri];
            }
        }

        if ($coreOnly === [] || $transferable === []) {
            self::fail('the corpus has no resource of one of the two kinds, so nothing is being compared');
        }
        self::assertLessThan(
            min($transferable),
            max($coreOnly),
            'a resource that stops at the core is offered as high as one that holds anywhere',
        );
    }

    #[Test]
    public function aDocumentThatStopsAtTheCoreSaysSoWhereItIsPicked(): void
    {
        foreach (Documents::documents() as $document) {
            $description = (string) Documents::description($document['id']);
            if (Documents::isCoreOnly($document['id'])) {
                self::assertStringContainsString('does not transfer', $description, $document['id']);
                continue;
            }

            // Everything outside the core used to be one case and is now
            // several. A document may hold everywhere, or answer for a package
            // or for the repository around an installation and for neither of
            // the others. All of them still owe the card the same thing, which
            // is a sentence that names who the answers oblige.
            self::assertMatchesRegularExpression(
                '/(Holds for|Answers for) /',
                $description,
                $document['id'] . ' is offered without saying who its answers oblige',
            );
        }
    }

    /**
     * The same for the second family, plus the difference between the two. A
     * document is prose to read and a skill is an order to follow. The one
     * place a host can learn which of the two it picks up is the card it picks
     * from. The card is all it has.
     */
    #[Test]
    public function aSkillSaysThatItIsAWorkflowAndWhoItsStepsOblige(): void
    {
        foreach (Skills::skills() as $skill) {
            $description = (string) Skills::description($skill['id']);

            self::assertStringContainsString(
                'not a page to read',
                $description,
                $skill['id'] . ' is offered as though it were a page a host serves for selection',
            );
            self::assertStringContainsString(
                Skills::isCoreOnly($skill['id']) ? 'does not transfer' : "Not the core's own workflow",
                $description,
                $skill['id'] . ' is offered without saying who its steps oblige',
            );
        }
    }

    /**
     * What the skill says about itself is what it already said, so the two
     * cannot come apart. A description written a second time here would get its
     * correction in the front matter and stay wrong in the resource list.
     */
    #[Test]
    public function whatASkillIsOfferedAsIsWhatItsOwnFrontMatterSays(): void
    {
        foreach (Skills::skills() as $skill) {
            self::assertStringContainsString(
                Skills::frontMatter($skill['id'])['description'],
                (string) Skills::description($skill['id']),
                $skill['id'] . ' is described twice, and the two can disagree',
            );
            self::assertSame($skill['id'], Skills::name($skill['id']), 'the name is not the id it is published as');
        }
    }

    /**
     * A skill is on offer because it is in the published list, which
     * `Server\Installer` writes into a client. A draft is a file in `skills/`
     * that nobody may load yet — `typo3-development-installation`, `D-SKL-013`.
     * An offer of it as a resource would publish it by the back door.
     */
    #[Requirement('R-ANS-022')]
    #[Test]
    public function onlyAPublishedSkillIsOffered(): void
    {
        $offered = array_column(Factory::resources(), 'uri');

        foreach (Installer::skills() as $skill) {
            self::assertContains(ResourceHandler::skillUri($skill), $offered);
        }

        $published = Finder::create()->directories()->in(Paths::root() . '/skills')->depth(0);
        foreach ($published as $directory) {
            if (in_array($directory->getFilename(), Installer::skills(), true)) {
                continue;
            }
            self::assertNotContains(
                ResourceHandler::skillUri($directory->getFilename()),
                $offered,
                $directory->getFilename() . ' is not published to any client and is offered here',
            );
        }
    }

    /**
     * What a workflow hands over at a step is addressable and stays out of the
     * list a picker sorts. A checklist on offer beside the workflow that owns
     * it is an entry nobody can choose between. The reader reads it at the step
     * that sends them to it. That is what the priority under all of them says
     * for the client that does sort templates.
     */
    #[Test]
    public function whatASkillHandsOverAtAStepIsOfferedAsATemplate(): void
    {
        $template = Factory::skillReferences();
        self::assertNotNull($template->description, 'the reference template says nothing about what it answers');
        self::assertNotNull($template->mimeType);

        $priority = $template->annotations?->priority;
        self::assertNotNull($priority);

        $offered = array_column(Factory::resources(), 'uri');
        foreach (Skills::skills() as $skill) {
            foreach (Skills::references($skill['id']) as $reference) {
                self::assertNotContains(
                    ResourceHandler::skillReferenceUri($skill['id'], $reference),
                    $offered,
                    $reference . ' of ' . $skill['id'] . ' is in the list a host offers for selection',
                );
            }
        }

        $priorities = array_map(
            static fn(ResourceDefinition $resource): float => (float) $resource->annotations?->priority,
            Factory::resources(),
        );
        if ($priorities === []) {
            self::fail('the server offers no resource at all');
        }
        self::assertLessThan(min($priorities), $priority);
    }

    /**
     * Every resource that is not the index, and whether it stops at the core.
     *
     * @return array<string, bool>
     */
    private static function offeredBelowTheIndex(): array
    {
        $offered = [];
        foreach (Documents::documents() as $document) {
            $offered[Documents::uri($document['id'])] = Documents::isCoreOnly($document['id']);
        }
        foreach (Skills::skills() as $skill) {
            $offered[ResourceHandler::skillUri($skill['id'])] = Skills::isCoreOnly($skill['id']);
        }

        return $offered;
    }

    /**
     * The protocol's `audience` is the user of the client or the model that
     * reads the resource. It is none of the three audiences `R-AUD-001` names —
     * those are not values it takes. Everything served here is for both roles,
     * so the field says nothing and stays off.
     */
    #[Test]
    public function noResourceClaimsTheProtocolsAudienceForTheOneThisServerMeans(): void
    {
        foreach (Factory::resources() as $resource) {
            self::assertNull($resource->annotations?->audience, $resource->uri);
        }
    }

    /**
     * The defect a body served on its own has, and the one nothing about a
     * description can catch. Every skill opens with a pointer to
     * `references/base.md`, which is a file in no skill in this checkout —
     * `Installer` writes it at publication. A client that never ran that
     * install is exactly the client this family exists for. So the link has to
     * land on something it can read.
     *
     * The case asserts the resolution a reader performs, not a promise that it
     * works. The target resolves against the URI of the body, the way a
     * relative reference resolves against any other URI. The result has to be a
     * URI this server answers.
     */
    #[Requirement('R-ANS-022')]
    #[Test]
    public function everyLinkASkillWritesResolvesToAResourceThisServerServes(): void
    {
        foreach (Skills::skills() as $skill) {
            $uri = ResourceHandler::skillUri($skill['id']);
            preg_match_all('/\]\(\s*([^)\s#][^)\s]*)\s*\)/', Skills::read($skill['id']), $links);

            $written = array_values(array_filter(
                array_unique($links[1]),
                static fn(string $target): bool => preg_match('#^[a-z][a-z0-9+.-]*:|^//#i', $target) !== 1,
            ));
            self::assertNotSame([], $written, $skill['id'] . ' links to nothing, and every skill links to its base');

            foreach ($written as $target) {
                self::assertNotSame(
                    '',
                    self::read(dirname($uri) . '/' . $target),
                    $skill['id'] . ' sends its reader to ' . $target . ', which resolves to nothing served',
                );
            }
        }
    }

    /**
     * What the Skills extension lists is what a host verifies a read against.
     * Every file a skill consists of, once, with the digest and the size of
     * the bytes the read returns. The front matter whole, because the host
     * compares it field by field with the body it reads. And the name equal
     * to the segment above `SKILL.md`, which is what the spec binds a skill's
     * identity to.
     */
    #[Decision('D-ANS-163')]
    #[Test]
    public function theManifestOfASkillIsComputedFromTheBytesAReadReturns(): void
    {
        foreach (Skills::skills() as $skill) {
            $manifest = Skills::manifest($skill['id']);
            self::assertSame(ResourceHandler::skillUri($skill['id']), $manifest['uri']);
            self::assertSame(basename(dirname($manifest['uri'])), $manifest['frontmatter']['name']);

            $listed = array_column($manifest['resources'], 'uri');
            self::assertSame($listed, array_unique($listed), $skill['id'] . ' lists a file twice');
            self::assertSame(
                [$manifest['uri'], ...array_map(
                    static fn(string $reference): string => ResourceHandler::skillReferenceUri($skill['id'], $reference),
                    Skills::references($skill['id']),
                )],
                $listed,
                $skill['id'] . ' lists other files than it serves',
            );

            foreach ($manifest['resources'] as $resource) {
                $served = self::read($resource['uri']);
                self::assertSame('sha256:' . hash('sha256', $served), $resource['digest'], $resource['uri']);
                self::assertSame(strlen($served), $resource['size'], $resource['uri']);
            }
        }
    }

    #[Test]
    public function whatNoSkillLinksToIsNotServedUnderItsUri(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        (new SkillReferenceHandler())->read(
            ResourceHandler::skillReferenceUri('typo3-extension-testing', 'references/invented.md'),
            ['skill' => 'typo3-extension-testing', 'reference' => 'invented.md'],
            self::createStub(ClientGateway::class),
        );
    }

    /** What a client gets when it reads the resource, which is what its size counts. */
    private static function served(ResourceDefinition $resource): string
    {
        return self::read($resource->uri);
    }

    /**
     * One URI read the way the server reads it: the registered resources first,
     * then the template. The SDK's own compiled template matches it rather than
     * a pattern written a second time here.
     */
    private static function read(string $uri): string
    {
        $gateway = self::createStub(ClientGateway::class);

        foreach (Factory::resources() as $resource) {
            if ($resource->uri === $uri) {
                return (new ResourceHandler())->read($uri, $gateway);
            }
        }

        $template = new ResourceTemplateReference(Factory::skillReferences(), static fn(): string => '');
        if ($template->matches($uri)) {
            return (new SkillReferenceHandler())->read(
                $uri,
                array_map('strval', $template->extractVariables($uri)),
                $gateway,
            );
        }

        self::fail($uri . ' is served by nothing this server registers');
    }
}
