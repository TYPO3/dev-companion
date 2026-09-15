<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Knowledge\Catalog\TranslationDomain;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * The translation domain an XLF file resolves to, computed from its path.
 *
 * Nothing registers a domain. It follows from the path by the core's own
 * path-to-domain rules, and the class with them has been both
 * TranslationDomainMapper and TranslationDomainResolver. A computation rather
 * than a lookup is what makes it answerable at all. For a file in any
 * extension, in any instance, and for one a patch is about to add. That is
 * exactly when no lookup anywhere can find it.
 */
final class TranslationDomainLookup extends ReadOnlyTool
{
    /**
     * The first TYPO3 major that resolves translation domains.
     *
     * Verified against the core: 13.4 has no TranslationDomain* class at all,
     * 14 ships the mapper. A domain in a label below this renders nothing,
     * silently and at runtime. That is why this is the one version fact the
     * code carries rather than the knowledge base. Public because it is one
     * number in one place (`D-DIS-004`), held to what it claims by
     * `VersionsTest` and by `bin/cli versions:check`.
     */
    public const SINCE = 14;

    public static function name(): string
    {
        return 'typo3_translation_domain_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Compute the translation domain an XLF file resolves to, from its path. The domain is the canonical way to reference a label (backend.alt_doc:key) in TCA, LanguageService::sL() and f:translate, and nothing registers it. It follows from the path by the rules the core itself applies, in TranslationDomainMapper on one branch and TranslationDomainResolver on the next. Because the tool computes it, it also answers for a file outside the core and for one a patch is about to add. On a version older than translation domains it answers with the full LLL:EXT: reference instead. The domain form renders nothing there and fails at runtime rather than at build time. That version is targetVersion, or the installation this server started in where the call states none. State one when the work is on another branch than the installed one. It computes a reference from a path and reads no label: whether the installation already registers one to reuse, and under which id, is typo3_label_lookup. The answer also carries the specifier a backend JavaScript module imports that domain under, which is the same value in the form that module needs.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'path' => ['type' => 'string', 'minLength' => 1, 'description' => 'The XLF file path, either as an EXT: reference ("EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf") or relative to a core checkout ("typo3/sysext/backend/Resources/Private/Language/locallang_alt_doc.xlf").'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the label is for, for example "13.4" or "14". It decides one thing here and it decides it entirely. Below the version that resolves domains the domain form renders nothing, so the answer is the LLL:EXT: reference instead. Defaults to the installation this server started in, which is the wrong answer for a backport branch or a second checkout; state it there.'],
            ],
            'required' => ['path'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'path' => Schema::string('The XLF path the domain comes from.'),
            'targetVersion' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major the answer is for, stated by the caller or read from the installation. Null means neither said, and the domain comes back unqualified: it is the form from ' . self::SINCE . ' onwards, and nothing placed this call on a version.'],
            'domain' => Schema::nullableString('The translation domain it resolves to. Null when the path names no extension, and also when the version the answer is for is too old to resolve domains at all. There the full LLL:EXT: reference is the answer.'),
            'domainOnNewerVersions' => Schema::nullableString('Set only in that second case: what the domain would be on a version that has them. It is not usable on this installation.'),
            'moduleImport' => Schema::nullableString('The specifier a backend JavaScript module imports the same domain under: import labels from \'~labels/<domain>\', read with labels.get(). Returned where the answer carries a domain, and absent where it carries none. The import map prefix arrived with the domains themselves, so there is nothing to write on a version below them.'),
        ], ['path', 'domain']);
    }

    public static function answer(array $args): ToolResult
    {
        $path = trim((string) ($args['path'] ?? ''));
        $stated = isset($args['targetVersion']) ? trim((string) $args['targetVersion']) : '';
        // One major, never the several a repository may declare. The whole
        // answer is one string that either works on a version or renders
        // nothing there. "It depends which of your two majors" is no answer to
        // write a label from (D-DIS-004).
        $target = Versions::target($stated === '' ? null : $stated);
        $domain = TranslationDomain::fromPath($path);

        if ($domain === null) {
            return ToolResult::create(
                sprintf(
                    "\"%s\" names no extension, so no translation domain follows from it.\n"
                    . 'Pass either an EXT: reference ("EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf") '
                    . 'or a checkout path ("typo3/sysext/backend/Resources/Private/Language/locallang_alt_doc.xlf").',
                    $path
                ),
                ['path' => $path, 'targetVersion' => $target, 'domain' => null],
            );
        }

        // The domain form is younger than the versions this answers from. On a
        // version that has no resolver for it, the domain string is
        // syntactically fine and resolves to nothing at runtime. Every label
        // with it in silently renders empty. That is the one answer here that
        // has to stay back rather than carry a qualification.
        if ($target !== null && $target < self::SINCE) {
            $reference = str_starts_with($path, 'EXT:') ? $path : 'EXT:<key>/' . ltrim($path, '/');

            return ToolResult::create(
                implode("\n", [
                    sprintf(
                        '%s has no translation domains: the API that resolves them arrived after it. Reference the '
                        . 'file itself instead:',
                        $stated === ''
                            ? 'The installation here is TYPO3 ' . Instance::typo3Version() . ', which'
                            : 'TYPO3 ' . $target . ', which you asked about,',
                    ),
                    '',
                    '  LLL:' . $reference . ':<trans-unit id>',
                    '',
                    sprintf(
                        'For the record, the domain this path would resolve to on a version that has them is "%s". '
                        . 'Writing it into a label there renders nothing, and fails at runtime rather than at build '
                        . 'time.',
                        $domain,
                    ),
                ]),
                ['path' => $path, 'targetVersion' => $target, 'domain' => null, 'domainOnNewerVersions' => $domain],
            );
        }

        return ToolResult::create(
            implode("\n", [
                sprintf('%s resolves to the translation domain:', $path),
                '',
                '  ' . $domain,
                '',
                'Reference a label in it as "' . $domain . ':<trans-unit id>" — in TCA, in LanguageService::sL(), '
                    . 'and in f:translate as separate domain and key attributes.',
                'A backend JavaScript module writes the same value as an import: import labels from '
                    . '"~labels/' . $domain . '", then labels.get("<trans-unit id>").',
                self::composedFor($stated, $target),
                'Which trans-units the file actually holds is a property of your checkout: read the file, and remember '
                    . 'that an installation can override it through LANG/resourceOverrides.',
            ]),
            [
                'path' => $path,
                'targetVersion' => $target,
                'domain' => $domain,
                'domainOnNewerVersions' => null,
                'moduleImport' => '~labels/' . $domain,
            ],
        );
    }

    /**
     * Which version the domain answers for.
     *
     * The answer that stays back has always named the version it stays back
     * for. The one that hands a domain over said nothing. So a caller on a
     * backport branch could not see that the answer was for the installation
     * instead. Where nothing placed the call at all, that is what it says. This
     * form is the newer one, and a caller on an older branch has to be the one
     * to know it.
     */
    private static function composedFor(string $stated, ?int $target): string
    {
        if ($stated !== '') {
            return sprintf('Composed for TYPO3 %d, which resolves domains.', $target);
        }
        if ($target !== null) {
            return sprintf(
                'Composed for the installation here, TYPO3 %s. State targetVersion where the label is being written '
                . 'for another branch.',
                Instance::typo3Version(),
            );
        }

        return sprintf(
            'Nothing here says which TYPO3 this is for: no installation was found and no targetVersion was stated. '
            . 'Domains resolve from %d onwards — on anything older this path is referenced as LLL:EXT:, and stating '
            . 'targetVersion is what gets that answer.',
            self::SINCE,
        );
    }
}
