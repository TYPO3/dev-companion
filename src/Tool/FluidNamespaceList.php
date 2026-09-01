<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\FluidNamespaces;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * The globally registered Fluid namespaces of the installation.
 */
final class FluidNamespaceList extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_fluid_namespace_list';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation, Source::Packages];
    }

    public static function description(): string
    {
        return 'List the Fluid ViewHelper namespaces that are globally available in the TYPO3 installation you are working in, so a template knows which prefixes it may use without declaring them. Every other namespace has to be declared per template with an xmlns attribute. On TYPO3 v14 and later, the fluid:namespaces console command answers; where it cannot be reached, the Configuration/Fluid/Namespaces.php files introduced in that version are read instead. Earlier versions are booted and answered from SYS/fluid/namespaces in TYPO3_CONF_VARS.';
    }

    public static function inputSchema(): array
    {
        return self::noArguments();
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'matchCount' => Schema::integer(),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'namespaces' => Schema::listOf(Schema::object([
                'prefix' => Schema::string('The prefix usable in a template without declaring it, for example "core".'),
                'phpNamespaces' => Schema::listOf(Schema::string(), 'The PHP namespaces it resolves ViewHelpers from.'),
            ], ['prefix', 'phpNamespaces'])),
        ], ['matchCount', 'answeredBy', 'namespaces'], []);
    }

    public static function answer(array $args): ToolResult
    {
        $answeredBy = 'installation';
        $limitation = '';

        if ((Instance::typo3Major() ?? 0) >= 14) {
            $answer = Typo3Cli::json(['fluid:namespaces', '--json']);
            $declared = is_array($answer['data']) ? $answer['data'] : [];
            if (!$answer['ok'] || !is_array($answer['data'])) {
                // The declarations are files in the same packages, so a
                // console that cannot boot does not have to end the question.
                // What the files cannot say is what the container did with
                // them.
                $declared = FluidNamespaces::all();
                if ($declared === []) {
                    return Unsupported::because($answer['error']);
                }
                $answeredBy = 'packages';
                $limitation = $answer['error'];
            }
        } else {
            // Before v14 there is no fluid:namespaces command and no
            // Configuration/Fluid/Namespaces.php. The assembled registry is
            // the TYPO3_CONF_VARS value after every extension has run.
            $read = Typo3Runtime::configuration('SYS/fluid/namespaces');
            if ($read === null || isset($read['unavailable'])) {
                return Unsupported::because(
                    is_array($read) ? (string) $read['unavailable'] : Typo3Runtime::reason(),
                );
            }
            if ($read['found'] === true && !is_array($read['value'])) {
                return Unsupported::because('SYS/fluid/namespaces is not an array in this installation');
            }
            $declared = $read['found'] === true ? $read['value'] : [];
        }

        $namespaces = [];
        foreach ($declared as $prefix => $classNames) {
            $namespaces[] = [
                'prefix' => (string) $prefix,
                'phpNamespaces' => array_map('strval', (array) $classNames),
            ];
        }
        usort($namespaces, static fn(array $a, array $b): int => strcmp($a['prefix'], $b['prefix']));

        $lines = [sprintf('%d globally registered Fluid namespace(s):', count($namespaces))];
        foreach ($namespaces as $namespace) {
            $lines[] = '- ' . $namespace['prefix'] . ': ' . implode(', ', $namespace['phpNamespaces']);
        }
        $lines[] = '';
        $lines[] = 'These prefixes work in any template without being declared. Every other namespace is declared '
            . 'in the template itself — xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" on the root '
            . 'element, together with data-namespace-typo3-fluid="true" so the declaration is stripped from the output.';
        if ($limitation !== '') {
            $lines[] = '';
            $lines[] = sprintf(
                'Read from the Configuration/Fluid/Namespaces.php of the installed packages: the console could not '
                . 'be asked (%s). That is what the packages declare, not what the container assembled from them.',
                $limitation,
            );
        }

        return ToolResult::create(implode("\n", $lines), [
            'matchCount' => count($namespaces),
            'namespaces' => $namespaces,
            'answeredBy' => $answeredBy,
        ]);
    }
}
