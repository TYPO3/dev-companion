<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

/**
 * Where a tool's answer can come from, as the tool itself declares it.
 *
 * A caller who chooses a tool reads what it is about in the description. What
 * is not in there is whether the answer will exist at all when nothing is up.
 * That decides how a caller plans a task rather than only how it reads an
 * answer. A tool that answers from the knowledge base answers on a fresh clone.
 * One that answers from the installation alone is the reason to start the
 * containers before the task instead of in the middle of it.
 *
 * What a tool declares is the set of sources that can answer it, first one
 * first. Which one did answer is `answeredBy` in the output schema, and it is a
 * different statement. This one is about the tool, that one about the call.
 */
enum Source: string
{
    /**
     * The installation this server started in: booted, or asked through its
     * console.
     */
    case Installation = 'installation';

    /** The files the installed packages ship, read rather than executed. */
    case Packages = 'packages';

    /** The knowledge base inside this package, which needs nothing up. */
    case Knowledge = 'knowledge';

    /** A service read over the network, named in the tool's own description. */
    case Network = 'network';

    /** This server's own checkout, where the feedback files live. */
    case Checkout = 'checkout';

    /** What this source is, for the one answer that lists them all. */
    public function meaning(): string
    {
        return match ($this) {
            self::Installation => 'The installation this server started in, booted or asked through its '
                . 'console. Its assembled state after every extension has had its say, and nothing at all where '
                . 'it is out of reach.',
            self::Packages => 'The files the installed packages ship, read rather than executed. Answers on a '
                . 'fresh clone and with the containers down. What a package registers at runtime is not in it.',
            self::Knowledge => 'The knowledge base inside this package. Needs nothing up, and binds to TYPO3 '
                . 'versions rather than to an installation.',
            self::Network => 'A service outside this machine. An unreachable one says so out loud rather than '
                . 'answers as empty.',
            self::Checkout => "This server's own checkout, which is why the tool offering it exists only in a "
                . 'standalone one.',
        };
    }

    /**
     * The sentence appended to every tool description.
     *
     * Uniform and short on purpose. Every client reads it in every session and
     * pays for it in tokens each time. So it carries the names and leaves what
     * they mean to typo3_server_scope, which a caller calls once and by choice.
     *
     * @param array<int, self> $sources
     * @return non-empty-string
     */
    public static function clause(array $sources): string
    {
        return 'Answers from: ' . implode(', ', array_map(
            static fn(self $source): string => $source->value,
            $sources,
        )) . '.';
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(static fn(self $source): string => $source->value, self::cases());
    }
}
