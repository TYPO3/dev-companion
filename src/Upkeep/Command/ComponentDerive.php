<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\BackendCss;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\ComponentDerivation;
use TYPO3\DevCompanion\Upkeep\Json;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Where each of a component's classes sits, and which majors it holds on.
 *
 * Both come out of the compiled `backend.css`, which the core commits on every
 * branch, so the four covered majors are read at once and reading them is the
 * verification rather than a step after it. What stays curated is which
 * components are worth answering about; every fact about a class is written
 * here — `D-CAT-008`.
 */
#[AsCommand(
    name: 'components:derive',
    description: 'write where each component class sits and the majors it holds on, from .checkouts/',
)]
final class ComponentDerive
{
    private const CLASSES = '/knowledge/catalog/component/classes.json';

    private const ELEMENTS = '/knowledge/catalog/component/elements.json';

    private const LISTING = '/knowledge/catalog/component/styleguide.json';

    public function __invoke(OutputInterface $output): int
    {
        $checkouts = [];
        $missing = [];
        foreach (Versions::covered() as $version) {
            $checkout = Checkouts::directory() . '/' . $version['branch'];
            if (BackendCss::of($checkout) === null) {
                $missing[] = $version['branch'];
                continue;
            }
            $checkouts[$version['major']] = $checkout;
        }
        if ($missing !== []) {
            Voice::problem($output, sprintf(
                'No compiled backend stylesheet below %s — run bin/cli checkouts:update.',
                implode(', ', $missing),
            ));

            return 2;
        }

        $derived = ComponentDerivation::from($checkouts);
        self::write(self::CLASSES, $derived['classes']);
        self::write(self::ELEMENTS, $derived['elements']);
        self::write(self::LISTING, $derived['listing']);

        $placed = count(array_filter($derived['classes'], static fn(array $c): bool => $c['positions'] !== []));
        Voice::ok($output, sprintf(
            '%d classes, %d of them placed, and %d custom elements, over %s',
            count($derived['classes']),
            $placed,
            count($derived['elements']),
            implode(', ', array_column(Versions::covered(), 'branch')),
        ));
        $ships = ComponentDerivation::listing($checkouts);
        Voice::row($output, sprintf(
            '%d components listed by the styleguide, which %s ships',
            count($derived['listing']),
            $ships === [] ? 'no covered major' : 'major ' . implode(', ', $ships),
        ));

        return 0;
    }

    /** @param list<array<string, mixed>> $records */
    private static function write(string $path, array $records): void
    {
        file_put_contents(
            Paths::root() . $path,
            Json::format((string) json_encode($records, JSON_THROW_ON_ERROR)),
        );
    }
}
