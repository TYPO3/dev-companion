<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

/**
 * A host that answers for the changes under review of the core project, in one
 * shape.
 *
 * `ChangeTracker` asks the providers in its order — `D-ANS-198`. The interface
 * carries the three reads every provider serves: the changes of an issue, a
 * search, and the backlog. A change read by its number or by a commit goes to
 * the review server alone, because only it carries the current commit and the
 * owner's address.
 *
 * A provider that cannot serve one read, such as a filter it does not know,
 * answers `unavailable` with the cause `not-served`. The tracker then asks the
 * next one.
 *
 * A field that is null is one this read did not carry. A string field a read
 * did not carry is empty.
 *
 * @phpstan-type Answer array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, dropped: int, more: bool, cause: ?string}
 * @phpstan-type Waiting array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, dropped: int, more: bool, cause: ?string, read: int, complete: bool}
 * @phpstan-type Filter array{order: string, maxSize: int, minCodeReview: int, negativeVotes: bool, mergeable: bool, branch: string, updatedBefore: string, owner: string, reviewedBy: string, involving: string, reviewableBy: string}
 */
interface ChangeProvider
{
    /** The cause a provider answers with where it cannot serve a read. */
    public const NOT_SERVED = 'not-served';

    /** Where a caller asks the same question by hand. */
    public function host(): string;

    /** The earliest read this instance answered from, as a Unix time, and null before the first. */
    public function readAt(): ?int;

    /**
     * The changes whose commit message names the issue.
     *
     * @return Answer
     */
    public function changesForIssue(int $issue, int $limit = 10): array;

    /**
     * The changes that match words, a path, or both.
     *
     * @return Answer
     */
    public function changesMatching(string $words, string $path, bool $open = false, int $limit = 10): array;

    /**
     * The open changes that wait on a review, at the end the order names.
     *
     * @param Filter $filter
     * @return Waiting
     */
    public function changesWaiting(array $filter, int $limit): array;
}
