<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Process;

/**
 * The runner that actually starts something, which is the only one outside a
 * test.
 *
 * What is in here was in `Typo3Cli::execute()` and in `Environments::run()`,
 * twice, with the same three things got right in both and the reasons written
 * down in one. It is one class now because it is one behaviour, and because a
 * seam with two implementations of the real side is not a seam.
 */
final class SystemRunner implements CommandRunner
{
    /**
     * @param list<string> $command
     *
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    public function run(
        array $command,
        ?string $workingDirectory = null,
        ?int $timeoutSeconds = null,
        bool $inheritStdin = false,
    ): array {
        // Read from /dev/null rather than left out of the descriptors: left out
        // it would be inherited, and what that costs is in `CommandRunner`.
        // Asked for, it is left out, which is how a child inherits it.
        $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        if (!$inheritStdin) {
            $descriptors[0] = ['file', '/dev/null', 'r'];
        }
        $process = @proc_open($command, $descriptors, $pipes, $workingDirectory, null);
        if (!is_resource($process)) {
            return ['ok' => false, 'exitCode' => -1, 'output' => '', 'error' => 'could not start ' . ($command[0] ?? '')];
        }

        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $output = '';
        $error = '';
        // Taken from the status rather than from proc_close: before PHP 8.3 the
        // status call reaps the child itself, and proc_close then has nothing
        // left to wait for and answers -1. Every command would read as failed.
        $exitCode = -1;
        $deadline = $timeoutSeconds === null ? null : time() + $timeoutSeconds;
        while (true) {
            $output .= (string) stream_get_contents($pipes[1]);
            $error .= (string) stream_get_contents($pipes[2]);

            $status = proc_get_status($process);
            if (!$status['running']) {
                $exitCode = $status['exitcode'];
                break;
            }
            if ($deadline !== null && time() >= $deadline) {
                proc_terminate($process);
                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($process);

                return [
                    'ok' => false,
                    'exitCode' => -1,
                    'output' => $output,
                    'error' => sprintf('timed out after %d seconds', $timeoutSeconds),
                ];
            }
            usleep(20_000);
        }

        $output .= (string) stream_get_contents($pipes[1]);
        $error .= (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $closed = proc_close($process);
        if ($exitCode < 0) {
            $exitCode = $closed;
        }

        return ['ok' => $exitCode === 0, 'exitCode' => $exitCode, 'output' => $output, 'error' => $error];
    }

    /**
     * The child is given a moment to die before it is called started: a server
     * refusing its port exits at once, and what it said is the answer.
     *
     * @param list<string> $command
     *
     * @return (\Closure(): void)|string
     */
    public function start(array $command, ?string $workingDirectory = null): \Closure|string
    {
        $descriptors = [0 => ['file', '/dev/null', 'r'], 1 => ['file', '/dev/null', 'w'], 2 => ['pipe', 'w']];
        $process = @proc_open($command, $descriptors, $pipes, $workingDirectory, null);
        if (!is_resource($process)) {
            return 'could not start ' . ($command[0] ?? '');
        }
        stream_set_blocking($pipes[2], false);
        usleep(300_000);
        if (!proc_get_status($process)['running']) {
            $said = trim((string) stream_get_contents($pipes[2]));
            fclose($pipes[2]);
            proc_close($process);

            return $said === '' ? ($command[0] ?? '') . ' ended at once' : $said;
        }

        return static function () use ($process, $pipes): void {
            proc_terminate($process);
            fclose($pipes[2]);
            proc_close($process);
        };
    }

    /**
     * Walks PATH rather than shelling out: "command -v" is a shell builtin and
     * `proc_open` runs no shell, so asking for it would always come back empty.
     */
    public function locate(string $name): ?string
    {
        foreach (explode(PATH_SEPARATOR, (string) getenv('PATH')) as $directory) {
            $candidate = rtrim($directory, '/') . '/' . $name;
            if (is_file($candidate) && is_executable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
