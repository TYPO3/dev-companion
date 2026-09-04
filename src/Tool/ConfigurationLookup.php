<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * An effective TYPO3_CONF_VARS value: what it is at runtime after every
 * extension has had its say, which is rarely what the shipped default says.
 */
final class ConfigurationLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_configuration_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation];
    }

    public static function description(): string
    {
        return 'Read an effective TYPO3_CONF_VARS value from the installation you are working in — the value as it is at runtime after every extension has had its say, not the shipped default. Use it for configuration whose assembled shape matters, such as SYS/formEngine/formDataGroup, SYS/caching/cacheConfigurations, or SYS/fluid. Ask it for one form data group — SYS/formEngine/formDataGroup/tcaDatabaseRecord — and the answer also carries the order the providers actually run in, resolved by the installation from the depends and before each declares, which is what decides whether one provider sees what another wrote. It answers for the installation as it stands, in the environment it is in: a value that has to be shown resolving under another environment — a variable set, a development-environment marker absent — is the project\'s own console, one run per environment.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'configurationPath' => ['type' => 'string', 'minLength' => 1, 'description' => 'Slash-separated path into TYPO3_CONF_VARS, for example "SYS/fluid" or "SYS/formEngine/formDataGroup". Named apart from path, which is a file everywhere else on this server — D-ANS-137.'],
            ],
            'required' => ['configurationPath'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'configurationPath' => Schema::string('The TYPO3_CONF_VARS path that was read.'),
            'found' => ['type' => 'boolean', 'description' => 'Whether the installation has a value at that path. Present only where one was asked: false is a statement about an installation, and where there was none to ask, unsupported stands in place of this answer.'],
            'value' => ['description' => 'The effective runtime value, of whatever shape the configuration has.'],
            'resolvedOrder' => Schema::listOf(Schema::object([
                'index' => ['type' => 'integer', 'description' => 'Position in the run, counting from zero.'],
                'provider' => Schema::string('Fully qualified class name of the form data provider.'),
                'depends' => Schema::listOf(Schema::string(), 'What it declares it runs after.'),
                'before' => Schema::listOf(Schema::string(), 'What it declares it runs before.'),
            ], ['index', 'provider', 'depends', 'before']), 'The order the providers actually run in, present only '
                . 'where the path names one form data group and the installation answered. The registry under it is '
                . 'a dependency graph, so this is what it resolves to rather than what it is written as.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
        ], ['configurationPath', 'found', 'answeredBy'], ['configurationPath']);
    }

    public static function answer(array $args): ToolResult
    {
        $path = trim((string) ($args['configurationPath'] ?? ''), " \t/");

        if ($path === '') {
            return Unsupported::because('no path into TYPO3_CONF_VARS was named', ['configurationPath' => $path]);
        }

        // The booted container rather than `configuration:show`, which arrived
        // in TYPO3 14.2 and would leave the two LTS lines this server covers
        // holding the console's "command is not defined" — D-ANS-077 is the
        // same case decided for the backend modules, and its reading is why
        // this has one source rather than a version-bound pair of them.
        $read = Typo3Runtime::configuration($path);
        if ($read === null || isset($read['unavailable'])) {
            // found is absent rather than false: false is a statement about the
            // installation — "it has no value at that path" — and nothing was
            // consulted to make it.
            return Unsupported::because(
                is_array($read) ? (string) $read['unavailable'] : Typo3Runtime::reason(),
                ['configurationPath' => $path],
            );
        }

        // A path that does not exist is a legitimate answer, not a breakage.
        if ($read['found'] !== true) {
            return ToolResult::create(
                sprintf('The installation has no configuration at "%s".', $path),
                ['configurationPath' => $path, 'found' => false, 'value' => null, 'answeredBy' => 'installation'],
            );
        }

        $text = sprintf(
            "Effective value of TYPO3_CONF_VARS/%s in this installation:\n\n```json\n%s\n```\n\n"
            . 'This is the assembled runtime value, not the default the core ships. Reading the files '
            . 'instead answers something narrower: config/system/settings.php carries the layer that file '
            . 'sets, config/system/additional.php the code that runs over it, and every extension changes it '
            . 'again from ext_localconf.php — none of which the file you opened shows.',
            $path,
            json_encode($read['value'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
        );

        $data = ['configurationPath' => $path, 'found' => true, 'value' => $read['value'], 'answeredBy' => 'installation'];

        $order = self::resolvedOrder($path);
        if ($order !== null) {
            $data['resolvedOrder'] = $order;
            $text .= sprintf(
                "\n\nThe providers run in this order, resolved by this installation's own "
                . "DependencyOrderingService from the graph above:\n\n%s\n\n"
                . 'What each one declares it runs after and before is in the data beside this. '
                . 'Whether one provider sees what another wrote is decided here and not by the order '
                . 'the entries are written in.',
                implode("\n", array_map(
                    static fn(array $entry): string => sprintf('%3d  %s', $entry['index'], $entry['provider']),
                    $order,
                )),
            );
        }

        return ToolResult::create($text, $data);
    }

    /**
     * The execution order behind a form data group, or null where the path
     * names none and where the installation could not be asked.
     *
     * The registry is one of the few configuration values whose assembled shape
     * is not what anybody wants: a caller asking about it is asking whether one
     * provider runs before another, and the map answers with 64 entries whose
     * edges sit far apart. A session settled it by writing a throwaway script
     * into its own checkout and running the resolver by hand — the feedback of
     * 2026-08-05.
     *
     * Ordered on the other side of the process boundary by the core's own
     * service, so this cannot drift from what the FormEngine actually does.
     *
     * @return array<int, array{index: int, provider: string, depends: array<int, string>, before: array<int, string>}>|null
     */
    private static function resolvedOrder(string $path): ?array
    {
        if (preg_match('#^SYS/formEngine/formDataGroup/([^/]+)$#', $path, $matches) !== 1) {
            return null;
        }

        $topic = Typo3Runtime::topic('formDataGroups');
        $groups = is_array($topic) && is_array($topic['groups'] ?? null) ? $topic['groups'] : [];
        if (!is_array($groups[$matches[1]] ?? null)) {
            return null;
        }

        $order = [];
        foreach ($groups[$matches[1]] as $index => $entry) {
            $order[] = [
                'index' => (int) $index,
                'provider' => (string) $entry['provider'],
                'depends' => array_values(array_map('strval', (array) ($entry['depends'] ?? []))),
                'before' => array_values(array_map('strval', (array) ($entry['before'] ?? []))),
            ];
        }

        return $order;
    }
}
