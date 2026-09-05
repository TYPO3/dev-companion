<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Process;

/**
 * The one place this server leaves its own process, as a seam a test can take.
 *
 * Everything here that reaches a console, a container or a checkout ends in one
 * `proc_open`, and for a long time it ended in one no test could stand between
 * (`R-COD-003`). `SystemRunner` is the real one, a test hands in a fake, and
 * nothing in the suite has to have anything running.
 */
interface CommandRunner
{
    /**
     * Runs one command and waits for it, with both its streams read.
     *
     * The child does not inherit this process's stdin unless it is asked for.
     * On the stdio server that stream is the client's JSON-RPC: a request
     * written while a console command runs would be read by the console
     * command, and the client would wait forever for an answer to a request
     * the server never saw. git is the exception and says so at its call.
     *
     * @param list<string> $command the executable and its arguments, unquoted — no shell is involved
     * @param ?string $workingDirectory where to run it, or null for this process's own
     * @param ?int $timeoutSeconds how long to wait before terminating it, or null to wait
     * @param bool $inheritStdin let the child read this process's stdin, which only git wants
     *
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    public function run(
        array $command,
        ?string $workingDirectory = null,
        ?int $timeoutSeconds = null,
        bool $inheritStdin = false,
    ): array;

    /**
     * Starts one command and leaves it running.
     *
     * What comes back is the closure that stops it, or the sentence the
     * command died with where it did not stay up — a server whose port is
     * taken says so within its first moment, and that is what is read.
     *
     * @param list<string> $command the executable and its arguments, unquoted — no shell is involved
     * @param ?string $workingDirectory where to run it, or null for this process's own
     *
     * @return (\Closure(): void)|string
     */
    public function start(array $command, ?string $workingDirectory = null): \Closure|string;

    /**
     * Where an executable of this name is, or null where the machine has none.
     *
     * On the same seam as `run()` because it is the same boundary: asking
     * whether `ddev` exists is asking the machine, and a test that has to
     * arrange for one on the `PATH` is a test that depends on the machine
     * having a writable, executable temporary directory. Answering it here is
     * what lets the whole question be stubbed.
     */
    public function locate(string $name): ?string;
}
