<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\StyleguideListing;

final class StyleguideListingTest extends TestCase
{
    private const CONTROLLER = <<<'PHP_SOURCE'
        final class ComponentsController
        {
            private array $allowedActions = [
                'componentsOverview',
                'badges',
                'tables',
            ];

            private function renderBadgesView(): ResponseInterface
            {
                $view->assignMultiple(['variants' => ['info', 'danger']]);
            }
        }
        PHP_SOURCE;

    #[Decision('D-CAT-008')]
    #[Test]
    public function theListedComponentsAreWhatTheControllerOffers(): void
    {
        $listing = new StyleguideListing(self::CONTROLLER);

        self::assertSame(['badges', 'tables'], $listing->components());
    }

    /** The page that lists the rest is the index, and nobody builds one. */
    #[Decision('D-CAT-008')]
    #[Test]
    public function theOverviewIsNotAComponent(): void
    {
        $listing = new StyleguideListing(self::CONTROLLER);

        self::assertFalse($listing->lists('componentsOverview'));
    }

    /**
     * The boundary this reads. A component the styleguide does not list is not
     * for an extension to use. So a name that is absent has to come back absent
     * rather than fall through to something near it.
     */
    #[Decision('D-CAT-008')]
    #[Test]
    public function aComponentTheStyleguideDoesNotListIsNotListed(): void
    {
        $listing = new StyleguideListing(self::CONTROLLER);

        self::assertTrue($listing->lists('badges'));
        self::assertFalse($listing->lists('module'));
    }

    /** Only the action list, so a variant assigned further down is not read as one. */
    #[Decision('D-CAT-008')]
    #[Test]
    public function anAssignedVariantIsNotAComponent(): void
    {
        $listing = new StyleguideListing(self::CONTROLLER);

        self::assertFalse($listing->lists('variants'));
        self::assertFalse($listing->lists('info'));
    }

    /** A checkout whose styleguide is a package rather than a system extension. */
    #[Decision('D-CAT-008')]
    #[Test]
    public function aCheckoutShippingNoStyleguideAnswersNothing(): void
    {
        self::assertNull(StyleguideListing::of(__DIR__));
    }

    #[Decision('D-CAT-008')]
    #[Test]
    public function aControllerWithoutTheListAnswersNothing(): void
    {
        self::assertSame([], (new StyleguideListing('final class Nothing {}'))->components());
    }
}
