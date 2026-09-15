<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge\Catalog;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Paths;

/**
 * Provenance of the static catalogs.
 *
 * The catalogs are snapshots of a moving target: identifiers, label keys and
 * component classes change with every core release. Without the branch and
 * commit they came from, a miss looks like "the catalog is older than your
 * checkout". That is exactly the case where a confident answer does the most
 * damage.
 */
final class Meta
{
    /**
     * @return array{
     *     source: array{repository: string, branch: string, version: string, commit: string},
     *     verifiedAt: string,
     *     verifyCommand: string,
     *     scope: array<string, string>,
     *     counts: array<string, int>
     * }
     */
    public static function read(): array
    {
        $decoded = json_decode((string) file_get_contents(Paths::catalogFile('meta.json')), true);
        if (!is_array($decoded) || !isset($decoded['source'])) {
            throw new \RuntimeException('Invalid catalog/meta.json');
        }

        /** @var array{source: array{repository: string, branch: string, version: string, commit: string}, verifiedAt: string, verifyCommand: string, scope: array<string, string>, counts: array<string, int>} $decoded */
        return $decoded;
    }

    /** One-line provenance, appended to every catalog answer. */
    public static function line(): string
    {
        $meta = self::read();

        $line = sprintf(
            'Catalog snapshot: TYPO3 %s (%s) @ %s, verified %s. A miss means "not in this snapshot", not "does not exist" — verify against the checkout before concluding a class does not exist.',
            $meta['source']['version'],
            $meta['source']['branch'],
            substr($meta['source']['commit'], 0, 12),
            $meta['verifiedAt'],
        );

        return self::skew() === '' ? $line : $line . ' ' . self::skew();
    }

    /**
     * What the caller has to know when the catalogs and the installation are
     * not the same TYPO3.
     *
     * The server knows both numbers and used to contrast them nowhere, so the
     * skew stayed invisible unless the caller thought to ask for the pin. v15
     * markup and a v15 custom-property contract went to a v13 backend as fact.
     * Empty where the majors agree, or where there is no installation to
     * compare with.
     */
    public static function skew(): string
    {
        $installed = Instance::typo3Version();
        if ($installed === null) {
            return '';
        }

        $catalog = self::read()['source']['version'];
        if ((int) $installed === (int) $catalog) {
            return '';
        }

        return sprintf(
            'The installation here is TYPO3 %s, the catalogs are from %s: verify class names, markup and custom '
            . 'properties against that installation rather than pasting them.',
            $installed,
            $catalog,
        );
    }
}
