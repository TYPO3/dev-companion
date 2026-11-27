<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

use TYPO3\DevCompanion\Http\Cache;

/**
 * The changes under review of the core project, from whichever provider
 * answers first — `D-ANS-198`.
 *
 * Forger answers the lists in a tenth of a second. The review server answers
 * what Forger does not carry: a change read by its number or by a commit, and
 * a backlog filtered on a person or on a vote. One answer comes from one
 * provider, and `source()` names it.
 *
 * @phpstan-import-type Answer from ChangeProvider
 * @phpstan-import-type Waiting from ChangeProvider
 * @phpstan-import-type Filter from ChangeProvider
 */
final class ChangeTracker
{
    /**
     * The providers in the order they are asked.
     *
     * @var list<class-string<ChangeProvider>>
     */
    private const PROVIDERS = [Forger::class, Gerrit::class];

    /**
     * The provider list a test hands in, as classes. Null puts the real list
     * back.
     *
     * @var list<class-string<ChangeProvider>>|null
     */
    private static ?array $providers = null;

    /** @var list<ChangeProvider> */
    private readonly array $order;

    /**
     * The providers this instance asked, for the age of its answer.
     *
     * @var list<ChangeProvider>
     */
    private array $asked = [];

    /** The host of the provider that answered the last call. */
    private string $source = '';

    /** @param list<ChangeProvider>|null $providers */
    public function __construct(?array $providers = null)
    {
        $this->order = $providers ?? array_map(
            static fn(string $class): ChangeProvider => new $class(),
            self::$providers ?? self::PROVIDERS,
        );
    }

    /** @param list<class-string<ChangeProvider>>|null $providers */
    public static function useProviders(?array $providers): void
    {
        self::$providers = $providers;
    }

    /** The host that answered the last call, or the first one asked where none did. */
    public function source(): string
    {
        return $this->source !== '' ? $this->source : $this->order[0]->host();
    }

    /** When the answer this instance gave was read — `D-ANS-049`. */
    public function readAt(): string
    {
        $reads = array_filter(array_map(
            static fn(ChangeProvider $provider): ?int => $provider->readAt(),
            $this->asked,
        ), is_int(...));

        return Cache::timestamp($reads === [] ? null : min($reads));
    }

    /**
     * The changes that name an issue.
     *
     * An empty answer asks the next provider too. Forger can be minutes behind
     * a push, and the question right after a push is this one.
     *
     * @return Answer
     */
    public function changesForIssue(int $issue, int $limit = 10): array
    {
        return $this->first(
            static fn(ChangeProvider $provider): array => $provider->changesForIssue($issue, $limit),
            static fn(array $answer): bool => $answer['status'] === 'answered',
        );
    }

    /** @return Answer */
    public function changesMatching(string $words, string $path, bool $open = false, int $limit = 10): array
    {
        return $this->first(
            static fn(ChangeProvider $provider): array => $provider->changesMatching($words, $path, $open, $limit),
            static fn(array $answer): bool => $answer['status'] !== 'unavailable',
        );
    }

    /**
     * @param Filter $filter
     * @return Waiting
     */
    public function changesWaiting(array $filter, int $limit): array
    {
        return $this->first(
            static fn(ChangeProvider $provider): array => $provider->changesWaiting($filter, $limit),
            static fn(array $answer): bool => $answer['status'] !== 'unavailable',
        );
    }

    /**
     * One change by its number or its Change-Id, from the review server.
     *
     * @return Answer
     */
    public function change(string $change, int $limit = 1, string $messages = 'none'): array
    {
        $review = $this->review();

        return $this->ask($review, static fn(): array => $review->change($change, $limit, $messages));
    }

    /**
     * One change by a commit, from the review server.
     *
     * @return Answer
     */
    public function commit(string $commit, int $limit = 1, string $messages = 'none'): array
    {
        $review = $this->review();

        return $this->ask($review, static fn(): array => $review->commit($commit, $limit, $messages));
    }

    /**
     * The answer of the first provider whose answer counts. Where none counts,
     * the first answer that is not `unavailable`, and otherwise the last
     * provider's cause. So an empty answer outranks a host that did not answer,
     * and the review server's cause outranks a filter Forger does not serve.
     *
     * @template T of array{status: string}
     * @param \Closure(ChangeProvider): T $read
     * @param \Closure(T): bool $counts
     * @return T
     */
    private function first(\Closure $read, \Closure $counts): array
    {
        $kept = null;
        $keptFrom = '';
        foreach ($this->order as $provider) {
            $answer = $this->ask($provider, static fn(): array => $read($provider));
            if ($counts($answer)) {
                return $answer;
            }
            if ($kept === null || $kept['status'] === 'unavailable') {
                $kept = $answer;
                $keptFrom = $provider->host();
            }
        }
        $this->source = $keptFrom;

        if ($kept === null) {
            throw new \LogicException('A change tracker needs at least one provider.');
        }

        return $kept;
    }

    /** The review server among the providers, or a new one where the list has none. */
    private function review(): Gerrit
    {
        foreach ($this->order as $provider) {
            if ($provider instanceof Gerrit) {
                return $provider;
            }
        }

        return new Gerrit();
    }

    /**
     * One read, with the provider remembered as the source and as a read the
     * answer's age counts.
     *
     * @template T
     * @param \Closure(): T $read
     * @return T
     */
    private function ask(ChangeProvider $provider, \Closure $read): mixed
    {
        $answer = $read();
        if (!in_array($provider, $this->asked, true)) {
            $this->asked[] = $provider;
        }
        $this->source = $provider->host();

        return $answer;
    }
}
