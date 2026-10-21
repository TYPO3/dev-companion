<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Http\Recent;

/**
 * The pipelines core-ci runs for every patch set on review.typo3.org, read
 * from the GitLab on git.typo3.org that runs them.
 *
 * A Verified-1 by core-ci names a pipeline in its review message and nothing
 * else. Which job failed took a session thirteen REST calls and a GitLab read
 * per pipeline (`D-ANS-182`). The pipeline's ref is `<branch>.<change>.<patch
 * set>`, so the patch set alone finds it, without the review log. Read only,
 * and no credential. A job's log is public at its `raw` address while the API
 * answers 401 for the same trace.
 *
 * @phpstan-type Pipeline array{pipeline: string, status: string, failedJobs: list<array{name: string, stage: string, url: string, log: string}>}
 */
final class CoreCi
{
    public const HOST = 'https://git.typo3.org';

    /** The project core-ci runs in, as the API takes it. */
    private const PROJECT = 'typo3%2FCI%2Fcms';

    private readonly Fetch $fetch;

    /** @param (\Closure(string): ?string)|null $transport */
    public function __construct(?\Closure $transport = null)
    {
        $this->fetch = new Fetch($transport);
    }

    /**
     * The latest pipeline of one patch set and the jobs in it that failed.
     *
     * An empty pipeline is a patch set core-ci ran nothing for. Null is a
     * GitLab that did not answer, which says nothing about the patch set.
     *
     * @return Pipeline|null
     */
    public function failed(string $branch, int $change, int $patchSet): ?array
    {
        $api = self::HOST . '/api/v4/projects/' . self::PROJECT;
        $url = $api . '/pipelines?' . http_build_query(['ref' => $branch . '.' . $change . '.' . $patchSet, 'per_page' => '1']);
        /** @var Pipeline|null $held */
        $held = Recent::held($url, Gerrit::HELD_FOR);
        if ($held !== null) {
            return $held;
        }

        $pipelines = Fetch::decode($this->fetch->get($url, ['Accept: application/json']));
        if (!is_array($pipelines)) {
            return null;
        }
        $pipeline = is_array($pipelines[0] ?? null) ? $pipelines[0] : null;
        if ($pipeline === null || !is_numeric($pipeline['id'] ?? null)) {
            return ['pipeline' => '', 'status' => '', 'failedJobs' => []];
        }

        $jobs = Fetch::decode($this->fetch->get(
            $api . '/pipelines/' . (int) $pipeline['id'] . '/jobs?scope%5B%5D=failed&per_page=100',
            ['Accept: application/json'],
        ));
        if (!is_array($jobs)) {
            return null;
        }

        $failed = [];
        foreach ($jobs as $job) {
            if (!is_array($job)) {
                continue;
            }
            $at = is_string($job['web_url'] ?? null) ? $job['web_url'] : '';
            $failed[] = [
                'name' => is_string($job['name'] ?? null) ? $job['name'] : '',
                'stage' => is_string($job['stage'] ?? null) ? $job['stage'] : '',
                'url' => $at,
                'log' => $at === '' ? '' : $at . '/raw',
            ];
        }
        $answer = [
            'pipeline' => is_string($pipeline['web_url'] ?? null) ? $pipeline['web_url'] : '',
            'status' => is_string($pipeline['status'] ?? null) ? $pipeline['status'] : '',
            'failedJobs' => $failed,
        ];
        Recent::hold($url, $answer);

        return $answer;
    }
}
