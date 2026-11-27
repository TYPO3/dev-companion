<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

/**
 * A host that answers for the issues of the core project, in one shape.
 *
 * A provider reads and normalises. `IssueTracker` asks the providers in its
 * order and builds every answer a caller sees from what they return. So a new
 * provider is one class and one entry in `IssueTracker::PROVIDERS` —
 * `D-ANS-197`.
 *
 * A list a provider could not read is empty, and the tracker reads an empty
 * list as "leave what came back alone". Only the three reads a caller asked
 * for carry a status, because only they decide which provider answers.
 *
 * A row field that is null is one this read did not carry, which is a different
 * thing from an issue that has none.
 *
 * @phpstan-type Comment array{author: string, on: string, note: string, bot: bool}
 * @phpstan-type Relation array{issue: int, relation: string}
 * @phpstan-type Attachment array{filename: string, contentType: string, size: int, on: string, url: string}
 * @phpstan-type Change array{change: int, status: string, url: string}
 * @phpstan-type Row array{issue: int, subject: string, tracker: string, status: string, priority: string, category: string, targetVersion: string, typo3Version: string, phpVersion: string, reportedBy: string, assignedTo: string, createdOn: string, updatedOn: string, url: string, description: ?string, relations: ?list<Relation>, attachments: ?list<Attachment>, comments: ?list<Comment>, changes: ?list<Change>}
 * @phpstan-type Page array{status: 'answered'|'empty'|'unavailable', url: string, total: int, rows: list<Row>, complete: bool, scanned: int, cause: ?string}
 * @phpstan-type Filter array{status: string, order: string, tracker: string, areas: list<string>, createdSince: string, createdBefore: string, updatedBefore: string, reportedBy: int, assignedTo: int, involving: int, words: string}
 */
interface IssueProvider
{
    /** Where a caller asks the same question by hand. */
    public function host(): string;

    /** The earliest read this instance answered from, as a Unix time, and null before the first. */
    public function readAt(): ?int;

    /**
     * One issue whole: its description, comments, relations, files and changes.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, row: ?Row, cause: ?string}
     */
    public function issue(int $number): array;

    /**
     * The comments of one issue, oldest first, and null where the host did not
     * answer. The stale end of a backlog reads them for every row.
     *
     * @return list<Comment>|null
     */
    public function comments(int $number): ?array;

    /**
     * The fields of these issues, in as few reads as the host allows.
     *
     * @param list<mixed> $numbers
     * @return array<int, Row>
     */
    public function fields(array $numbers): array;

    /**
     * These issues with their description, relations, files and changes, in
     * the order asked.
     *
     * @param list<int> $numbers
     * @return list<Row>
     */
    public function rows(array $numbers): array;

    /**
     * The issues of any status whose text matches the words, fields filled.
     *
     * @return Page
     */
    public function search(string $words, int $limit): array;

    /** How many issues one word matches on its own, and null where the host did not answer. */
    public function matchCount(string $word): ?int;

    /**
     * One page of the backlog, or the whole set up to the provider's bound.
     *
     * A page carries what `rows()` carries. The whole set carries the fields
     * alone, because a caller reads it for its shape. `total` and `scanned`
     * count after the words, where the filter names any.
     *
     * @param Filter $filter
     * @return Page
     */
    public function backlog(array $filter, int $offset, int $limit, bool $whole): array;

    /**
     * The areas the core project files its issues under, in its own spelling.
     *
     * @return list<string>
     */
    public function areas(): array;

    /**
     * The people this host knows whose name carries every word of the name,
     * name to the tracker's user id.
     *
     * @return array<string, int>
     */
    public function people(string $name): array;
}
