<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Changelog;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Manual\CoreChangelog;
use TYPO3\DevCompanion\Result\Miss;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;
use TYPO3\DevCompanion\Search\LabelSearch;

/**
 * What a TYPO3 version changed, from the changelog docs.typo3.org renders.
 *
 * The one question the knowledge base cannot answer from conventions. What a
 * given release broke, deprecated or added is a list. docs.typo3.org renders
 * it after every merge, and every installation ships the source of it on
 * disk, which is where the answer comes from when docs.typo3.org does not
 * answer.
 */
final class ChangelogLookup extends ReadOnlyTool
{
    /** The entries come from docs.typo3.org, and from disk only where it did not answer. */
    protected const OPEN_WORLD = true;

    /**
     * What an entry that states no removal leaves unsaid.
     *
     * The removal version is what an upgrade audit decides on, and an empty
     * field beside a populated one reads as "no removal planned". That is the
     * silence-as-verdict failure `D-ANS-009` stands against. So the rule that
     * covers the silence travels with the answer as data and not only as text,
     * which is what `R-ANS-002` stands against. It stands as a statement and
     * never applies per entry. A number derived from the rule would have been
     * wrong where the core kept an entry that skips a major.
     */
    private const REMOVAL_RULE = 'A deprecated API keeps working until the next major release. An entry that '
        . 'states a removal version overrides that, and some state one more than a major away. An empty removal '
        . 'is what the entry states, not a promise that no removal is planned.';

    public static function name(): string
    {
        return 'typo3_changelog_lookup';
    }

    public static function title(): string
    {
        return 'Search the core changelog';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Packages, Source::Network];
    }


    public static function description(): string
    {
        return 'Search the TYPO3 changelog: one entry per breaking change, deprecation, feature and important note, in the version of its release. This reads the entries. An entry for a core patch of your own is the other direction, and it is typo3_rule_lookup with documentId "core/contribution/changelog". Answers "what did this version deprecate", "what changed about X", "which release introduced Y". This is the first stop when you build on a major you have not built on recently. What separates a current answer from a two-major-old one stands here and almost nowhere else. A deprecation carries the version it stops to work in where the entry states one, and the rule that answers the rest beside it. The tool reads the entries from docs.typo3.org, which renders them after every merge, so a version you have not installed and a change merged today are both in reach. Where docs.typo3.org does not answer, it reads the core package on disk. An entry has to carry every word of the query; narrow further with type and version. A version and a type with the query omitted list whole under a raised limit. That is the deprecation sweep of one major in a single call. A method or class you found in the code is a query of its own. An identifier reaches the entries that name it, whether or not the change has its title. That holds inside the versions the installation ships, which are the ones whose text is on disk.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Words the entry has to carry, matched against its file name and the words that name spells. Where no entry carries all of them by name, the search reads the title inside the file as well. That reaches a method name the file name leaves out. A class, method or constant name reaches the entries that write it in their text. So you ask for a removed API by the identifier you have, in any spelling: bare, qualified by its class, or fully qualified. The issue number is among the words a file name carries. So a deprecation\'s own number reaches every entry filed under it, the Feature that announced the replacement with its release version among them. When nothing carries all of them there either, the answer names the largest part of the query that does reach entries. That is what to ask again with. Omit to list a version or a type as a whole.'],
                'type' => ['type' => 'string', 'enum' => ['breaking', 'deprecation', 'feature', 'important'], 'description' => 'Restrict to one kind of change. Breaking and deprecation are what affects code you have.'],
                'version' => ['type' => 'string', 'description' => 'Restrict to a version, by prefix: "14" covers 14.0 through 14.3.x, "13.4" covers 13.4 and 13.4.x.'],
                'tag' => ['type' => 'string', 'description' => 'Restrict to entries with this index tag. "ext:form" is the system extension a change is in. "FullyScanned" or "NotScanned" is what the Extension Scanner has a matcher for. "PHP-API", "TCA", "Backend" and "Frontend" are the surface. This bounds one question inside a version and a type. The sweep of a major does not need it. That version and type come back whole under a raised limit, and every entry carries its own tags to read by. The changelog says nothing about which third-party extension a change affects, so an extension key of your own matches no tag.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 200, 'default' => 20, 'description' => 'Maximum number of entries. Raise it to list a version and a type whole. The largest covered major holds 128 deprecations, and that sweep is one call rather than one per tag.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'query' => Schema::string(),
            'matchCount' => Schema::integer('Entries that carry every word of the query and the tag, before the limit.'),
            'matchedIn' => Schema::string('Where the query matched. "name" is the entry names. "body" is where no name carried it and the inside of the file did. That is the title as the file states it, or an identifier the text writes. A body match can name the identifier and not be about it, so read the title of each. Returned where the answer carries entries.'),
            'tags' => Schema::listOf(Schema::string(), 'Every index tag the entries of this version and type carry, with the ones already filtered by among them. Returned where the call named a tag, so you replace a tag that matched nothing with one that exists.'),
            'entries' => Schema::listOf(Schema::object([
                'type' => ['type' => 'string', 'enum' => ['Breaking', 'Deprecation', 'Feature', 'Important']],
                'version' => Schema::string('The version directory of its release.'),
                'issue' => Schema::string('Forge issue number.'),
                'title' => Schema::string(),
                'removal' => Schema::string('The version a Deprecation states the deprecated thing stops to work in, which is what an upgrade decides on. Empty on the other three types, and on a deprecation whose entry states none. That is most of a major and does not mean "no removal planned"; removalRule answers it there.'),
                'migration' => Schema::string('What to write instead, as the entry\'s own Migration section states it, code blocks included. Carried where the call reached one entry, by an issue number or a query that matched one. A sweep of seventy-five is a list of titles and not seventy-five migrations. Empty on every entry of a longer answer, and on an entry whose file states no migration.'),
                'tags' => Schema::listOf(Schema::string(), 'Index tags. FullyScanned or PartiallyScanned means the extension scanner has a matcher for it.'),
                'file' => Schema::string('Where to read the description and the migration: an EXT: reference where the installation ships the entry, and the docs.typo3.org URL where it does not.'),
                'url' => Schema::string('The entry as docs.typo3.org renders it, on every entry.'),
                'publishedIn' => Schema::string('Which side the entry came from. "manual" is docs.typo3.org, which renders the changelog after every merge and is the ordinary source. "installation" is the core package on disk, read where docs.typo3.org did not answer or does not list the version. An entry for a major without a release yet moves either way.'),
            ], ['type', 'version', 'issue', 'title', 'removal', 'migration', 'tags', 'file', 'url', 'publishedIn'])),
            'termCounts' => Schema::termCounts('What each word of the query reaches on its own, inside the version and the type the call named. A word at 0 is the one that emptied the answer: a misspelt word, or nothing here carries its name. Returned on a miss that carried words. These are counts and not a query; termSubsets is what you ask outright.'),
            'termCountsWithoutTheNarrowing' => Schema::termCounts('The same words counted over the whole changelog rather than inside the version and the type. Returned only where a word reaches there and nothing inside the narrowed set. Then the filter emptied this answer rather than the words: ask again without it.'),
            'termSubsets' => Schema::listOf(Schema::object([
                'terms' => Schema::listOf(Schema::string(), 'Words of the query, as a query to ask again with.'),
                'matchCount' => Schema::integer('Entries that carry every word of this subset, inside the same version and type.'),
            ], ['terms', 'matchCount']), 'The largest parts of the query that do reach entries, narrowest first. Every one of them, because the one a tie-break puts first is not always the one you look for. Withheld where the call names a tag. The count runs off the entry names and a tag sits inside the file. So a subset offered there promises entries the same call does not return.'),
            'removalRule' => Schema::string('When a deprecation stops to work where the entry itself does not say. Returned where the answer carries a deprecation.'),
            'versions' => Schema::listOf(Schema::string(), 'The versions this installation ships changelog entries for, newest first. Empty where no installation was found.'),
            'versionsFromTheManual' => Schema::listOf(Schema::string(), 'The versions read from docs.typo3.org, newest first, inside the version and the type the call named. Absent where docs.typo3.org did not answer, which is the one case this answer lacks a version rather than the changelog.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
        ], ['query', 'matchCount', 'entries', 'versions', 'answeredBy'], ['query']);
    }

    /**
     * The one system extension every match sits in, where the query never
     * named it.
     *
     * `extendToSubpages` is the TCA column and the natural word for inherited
     * frontend access restriction. The changelog answers it with a single 12.0
     * Breaking that removes an Indexed Search option that happens to spell it.
     * The answer is arguably correct, that area never saw a rework, and a
     * changelog records change events. But a session that started from the
     * column name and stopped there reads one hit as evidence about the area
     * (`feedback/2026-08-07-233553`).
     *
     * Only where the query does not name it. A caller who asks about indexed
     * search gets `ext:indexed_search` entries and learns nothing from a
     * reminder of it. Matched a word at a time, since nobody types the key with
     * its underscore.
     *
     * `ext:core` is never it. Most of what the changelog records is in there.
     * So "every one of these is in ext:core" is a statement about the corpus
     * rather than about the query.
     *
     * @param array<int, array<string, mixed>> $entries
     */
    private static function oneSystemExtension(array $entries, string $query): ?string
    {
        if ($entries === []) {
            return null;
        }

        $only = null;
        foreach ($entries as $entry) {
            $tags = array_values(array_filter(
                array_map('strval', $entry['tags'] ?? []),
                static fn(string $tag): bool => str_starts_with($tag, 'ext:'),
            ));
            // An entry in two of them is a change across system extensions and
            // says nothing about a wrong extension in the answer.
            if (count($tags) !== 1 || ($only !== null && $only !== $tags[0])) {
                return null;
            }
            $only = $tags[0];
        }

        $key = mb_strtolower(substr((string) $only, 4));
        if ($key === '' || $key === 'core') {
            return null;
        }

        $asked = mb_strtolower($query);
        foreach (explode('_', $key) as $word) {
            if (!str_contains($asked, $word)) {
                return $only;
            }
        }

        return null;
    }

    public static function answer(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $type = trim((string) ($args['type'] ?? ''));
        $version = trim((string) ($args['version'] ?? ''));
        $tag = trim((string) ($args['tag'] ?? ''));
        $limit = (int) ($args['limit'] ?? 20);

        $terms = LabelSearch::terms($query);
        $installed = Changelog::versions();

        // One listing per covered major from docs.typo3.org, and null under a
        // major it did not answer for. A version an answered listing carries
        // is docs.typo3.org's, because it renders the changelog after every
        // merge and a package or a checkout is behind it. Every other version
        // on disk is the installation's: the ones below what the knowledge
        // covers, and the ones under a major docs.typo3.org did not answer for.
        // Offline, that is
        // the whole changelog the installation ships — `D-ANS-165`.
        $manual = new CoreChangelog();
        $listings = $manual->entries();
        $answered = array_filter($listings, static fn(?array $entries): bool => $entries !== null);
        $unread = array_keys(array_diff_key($listings, $answered));
        $all = [];
        $fromHost = [];
        foreach ($answered as $entries) {
            foreach ($entries as $entry) {
                $fromHost[$entry['version']] = true;
                $all[] = $entry + ['publishedIn' => 'manual'];
            }
        }
        foreach (Changelog::entries() as $entry) {
            if (!isset($fromHost[$entry['version']])) {
                $all[] = $entry + ['publishedIn' => 'installation', 'url' => CoreChangelog::url($entry['version'], $entry['key'])];
            }
        }
        if ($all === [] && $installed === []) {
            return Unsupported::because(
                $answered === []
                    ? 'no TYPO3 installation was found whose core package ships the changelog, and docs.typo3.org did not answer'
                    : 'no TYPO3 installation was found whose core package ships the changelog, and docs.typo3.org lists nothing',
                ['query' => $query],
            );
        }

        $narrowed = array_values(array_filter($all, static function (array $entry) use ($type, $version): bool {
            if ($version !== '' && !str_starts_with($entry['version'], $version)) {
                return false;
            }

            return $type === '' || $entry['type'] === ucfirst(strtolower($type));
        }));
        $ahead = [];
        foreach ($narrowed as $entry) {
            if ($entry['publishedIn'] === 'manual') {
                $ahead[] = $entry['version'];
            }
        }
        $ahead = array_values(array_unique($ahead));
        usort($ahead, static fn(string $a, string $b): int => version_compare($b, $a));

        $matching = LabelSearch::carryingEvery($narrowed, $terms);

        // The names answer first and the file only where they answered nothing.
        // So a call that hits today pays no read and nothing that matches today
        // stops to match. A read of every file costs an order of magnitude more
        // than a scan of the names. Where the names answer nothing there is no
        // answer to slow down. `D-ANS-041`, and `D-ANS-042` for what the same
        // read takes out of the body. Two things come out of that one read,
        // because the file is open either way. The title as the file states it,
        // which the name spells differently, and the identifiers the body
        // writes, which the name leaves out. The counts and subsets a miss
        // prints run over the same enriched entries, so they say what the
        // search covered. The manual's half of that read is free and its other
        // half is not. The listing already carries the stated title, so a title
        // search costs nothing there. The identifiers are in the body. A read
        // of thousands of them over the network is the wrong side of a minute
        // for a fallback that runs on a miss. So the search reads a manual
        // entry by its title and never by its identifiers, and an installed
        // one by both. The answer says so where that is what the caller did.
        $read = false;
        if ($matching === [] && $terms !== []) {
            $narrowed = array_map(
                static function (array $entry): array {
                    if ($entry['publishedIn'] === 'manual') {
                        return $entry + ['title' => $entry['stated']];
                    }

                    /** @var array{file: string} $entry */
                    return $entry + ['identifiers' => implode(' ', Changelog::identifiers($entry))];
                },
                self::titled($narrowed),
            );
            $matching = LabelSearch::carryingEvery($narrowed, $terms);
            $read = $matching !== [];
        }

        // An installed entry's tags are inside the file, so a filter by one
        // costs a read of every entry that survived the type and the version.
        // That is 23 ms for the deprecations of one major, six hundred for the
        // whole changelog. That read is why it is a field of its own rather
        // than more words in the query. It bounds one question rather than a
        // sweep. A major comes back whole from the version and the type under
        // a raised `limit`, which `D-ANS-093` measured against eleven tag
        // calls. A manual entry carries its tags in the listing, so its half
        // of the filter reads nothing — `D-ANS-165`.
        $tags = [];
        if ($tag !== '') {
            $carrying = [];
            foreach ($matching as $entry) {
                $carried = self::tags($entry);
                foreach ($carried as $carriedTag) {
                    $tags[$carriedTag] = true;
                }
                foreach ($carried as $carriedTag) {
                    if (strcasecmp($carriedTag, $tag) === 0) {
                        $carrying[] = $entry;
                        break;
                    }
                }
            }
            $matching = $carrying;
        }
        ksort($tags);
        usort($matching, static fn(array $a, array $b): int => version_compare($b['version'], $a['version'])
            ?: strcmp($a['key'], $b['key']));

        $shown = array_slice($matching, 0, $limit);
        // The migration is the part a session went to the file for, and it
        // comes over where the answer is about one entry. On a sweep it is the
        // volume the titles exist to keep down, `D-ANS-139`.
        $whole = count($shown) === 1;
        $entries = array_map(static function (array $entry) use ($manual, $whole): array {
            $read = self::body($entry, $manual);

            return [
                'type' => $entry['type'],
                'version' => $entry['version'],
                'issue' => $entry['issue'],
                'title' => $read['title'] === '' ? $entry['source'] : $read['title'],
                'removal' => $read['removal'],
                'migration' => $whole ? $read['migration'] : '',
                'tags' => $read['tags'],
                'file' => $entry['publishedIn'] === 'manual'
                    ? $entry['url']
                    : 'EXT:core/Documentation/Changelog/' . $entry['version'] . '/' . $entry['key'] . '.rst',
                'url' => $entry['url'],
                'publishedIn' => $entry['publishedIn'],
            ];
        }, $shown);

        $versions = $installed;
        if ($entries === []) {
            $narrowing = self::narrowing($type, $version);
            $counts = LabelSearch::perTermCounts($narrowed, $terms);
            $reached = array_values(array_filter(
                $counts,
                static fn(array $term): bool => $term['matchCount'] > 0,
            ));
            // Every count on this miss runs inside the version and the type,
            // and reads as a fact about the changelog. The reported miss said
            // "preview reaches 1 entry" at `version: "15"` where all four words
            // reach without it. The session concluded the tool could not reach
            // the entry at all. So where a word reaches outside the filter and
            // nothing inside it, the filter is what emptied the answer. That is
            // the first sentence, `D-ANS-016`. The second scan is the whole
            // changelog and costs 48 ms for the 3795 entries of
            // `/home/benji/projects/typo3-cms`, on a narrowed miss alone. It
            // reads the names and not the titles, because what it establishes
            // is which filter emptied the answer. The whole-file read is what
            // the caller pays once it asks again without that filter.
            $outside = [];
            if ($narrowing !== [] && $terms !== []) {
                $inside = array_column($counts, 'matchCount', 'term');
                $reaching = array_values(array_filter(
                    LabelSearch::perTermCounts($all, $terms),
                    static fn(array $term): bool => $term['matchCount'] > 0,
                ));
                $emptied = array_filter(
                    $reaching,
                    static fn(array $term): bool => ($inside[$term['term']] ?? 0) === 0,
                );
                $outside = $emptied === [] ? [] : $reaching;
            }

            $lines = [sprintf(
                'No changelog entry in this installation %s%s.',
                $terms === [] ? 'matched those filters' : 'carries all of ' . LabelSearch::quoted($terms),
                $tag === '' ? '' : sprintf(' and the tag "%s"', $tag),
            )];
            if ($outside !== []) {
                $lines[] = sprintf(
                    'Narrowed to %s — %s what emptied this, not the words: without %s, %s. Ask again without %s.',
                    implode(' and ', $narrowing),
                    count($narrowing) === 1 ? 'that filter is' : 'those filters are',
                    count($narrowing) === 1 ? 'it' : 'them',
                    Miss::reaching($outside, 'entry', 'entries'),
                    count($narrowing) === 1 ? 'it' : 'them',
                );
            }
            if ($tag !== '') {
                $lines[] = $tags === []
                    ? 'Nothing narrowed by that version and type carries any tag at all.'
                    : 'The tags those entries carry: ' . implode(', ', array_keys($tags)) . '.';
            }
            // What the caller can act on is a query rather than five numbers:
            // the words that do reach something together. Offered where the
            // caller asked for no tag, because the peel reads file names while
            // a tag is inside the file. A subset counted without the tag would
            // promise entries the same call does not return. On the narrowed
            // set, for the same reason.
            $subsets = $tag === '' ? LabelSearch::largestReachingSubsets($narrowed, $terms) : [];
            if (count($terms) > 1 && $reached !== []) {
                $lines[] = ($narrowing === [] ? 'On its own, ' : sprintf('Inside %s, on its own, ', implode(' and ', $narrowing)))
                    . Miss::reaching($reached, 'entry', 'entries')
                    . ($subsets === [] && $outside === [] ? ' — ask again with the one that narrows best.' : '.');
            }
            if ($subsets !== []) {
                $lines[] = Miss::largestReaching(
                    $subsets,
                    count($terms),
                    'entry',
                    'entries',
                    $narrowing === [] ? '' : 'inside ' . implode(' and ', $narrowing),
                );
                // Where the offered re-query comes back empty too, what is
                // absent is the corpus and not the words. `D-ANS-010`, which
                // routes "does it still work" to the manual. After the offer
                // and never in place of it. The reported miss did carry the
                // entry its review turned on, one subset away. A sentence that
                // names the manual first is what would have routed that session
                // away from it (`D-ANS-043`). Offered nowhere else, because
                // "that" is the re-query and a miss with none has nothing for
                // this sentence to follow.
                $lines[] = 'Where that comes back empty too, ask typo3_documentation_lookup with targetVersion: a '
                    . 'changelog records change events, so a mechanism nobody changed has no entry here, and '
                    . 'whether one still holds in a version is what the manual answers.';
            } elseif ($terms !== [] && $outside === [] && $tag === '') {
                // Nothing came out to ask this corpus again with, so the next
                // call is a different corpus rather than a different query.
                // `R-ANS-018`, which the branch above held alone. Both of them,
                // because a miss says nothing about which of the two shapes the
                // question had. A caller with no re-query left cannot recover
                // from a route to the wrong one, `D-ANS-110`. Not where a
                // filter or a tag emptied the answer. Those name their own way
                // back into this corpus, and a route out of it ahead of a
                // re-query that answers is what `D-ANS-043` declined.
                $lines[] = 'A changelog records change events, so a miss can mean the question belongs to another '
                    . 'corpus. Whether a mechanism nobody changed still holds is typo3_documentation_lookup with '
                    . 'targetVersion; whether a core patch of your own owes an entry is typo3_rule_lookup with '
                    . 'documentId "core/contribution/changelog".';
            }
            $lines[] = self::covers($versions, $ahead, $listings);

            // What the miss worked out is a field as well as a line. A session
            // read `matchCount: 0` and the five fields beside it. It reported
            // that nothing came back to ask again with, and settled its
            // question by grep. All the while the text of that same answer
            // offered the subset that returns the entry its review turned on
            // (`D-ANS-043`). `R-ANS-002` is for the client that renders
            // `structuredContent` and drops the text block. Each field is
            // present where the miss computed it and absent where it held it
            // back, under the two holds the text already makes. The subsets
            // never travel beside a `tag`. A count says which side of the
            // filter it stands on by which of the two fields carries it.
            $data = [
                'query' => $query,
                'matchCount' => 0,
                'tags' => array_keys($tags),
                'entries' => [],
                'versions' => $versions,
                'answeredBy' => 'packages',
            ];
            if ($answered !== []) {
                $data['versionsFromTheManual'] = $ahead;
            }
            if ($counts !== []) {
                $data['termCounts'] = $counts;
            }
            if ($outside !== []) {
                $data['termCountsWithoutTheNarrowing'] = $outside;
            }
            if ($subsets !== []) {
                $data['termSubsets'] = $subsets;
            }

            return ToolResult::create(implode("\n", $lines), $data);
        }

        $lines = [sprintf(
            '%d changelog entr%s%s%s:',
            count($matching),
            count($matching) === 1 ? 'y' : 'ies',
            $query === '' ? '' : sprintf(' carrying %s', LabelSearch::quoted($terms)),
            count($matching) > count($entries) ? sprintf(' — showing the first %d', count($entries)) : '',
        )];
        if ($read) {
            $lines[] = 'No entry is named after that, so these are the ones carrying it inside the file — in the '
                . 'title as it is stated, or as an identifier their text writes. Naming it is not the same as '
                . 'being about it: the title says what each one changed.';
        }
        $only = self::oneSystemExtension($entries, $query);
        if ($only !== null) {
            $lines[] = sprintf(
                'Every one of these is in %s, which the query did not name. A changelog records change events, so an '
                . 'area nobody has reworked has no entry at all — an answer that comes from one system extension is '
                . 'usually the place that happens to spell the word rather than the subject. Ask again in the words '
                . 'the changelog writes that subject in, which are not always the ones the code uses.',
                $only,
            );
        }
        if ($tag !== '') {
            $lines[0] = sprintf(
                '%d of the %d entries narrowed by version and type are tagged "%s"%s:',
                count($matching),
                count($narrowed),
                $tag,
                count($matching) > count($entries) ? sprintf(' — showing the first %d', count($entries)) : '',
            );
        }
        foreach ($entries as $entry) {
            $lines[] = sprintf(
                '- %s %s: %s (#%s)%s',
                $entry['version'],
                $entry['type'],
                $entry['title'],
                $entry['issue'],
                $entry['removal'] === '' ? '' : sprintf(' — removed in v%s', $entry['removal']),
            );
            $lines[] = '  ' . $entry['file'] . ($entry['tags'] === [] ? '' : ' — ' . implode(', ', $entry['tags']));
            if ($entry['migration'] !== '') {
                $lines[] = '';
                $lines[] = 'Migration';
                $lines[] = $entry['migration'];
            }
        }
        $lines[] = '';
        $lines[] = count($entries) === 1
            ? 'The migration above is the entry\'s own section. Read the file for the rest of the description. A '
                . 'Deprecation or Breaking entry tagged FullyScanned or PartiallyScanned has an extension scanner '
                . 'matcher behind it, so the Install Tool can find the call sites for you.'
            : 'Read the file for the description and the migration, or ask again for the one entry by its issue '
                . 'number, which carries its migration section whole. A Deprecation or Breaking entry tagged '
                . 'FullyScanned or PartiallyScanned has an extension scanner matcher behind it, so the Install Tool '
                . 'can find the call sites for you.';
        // A hit says nothing about what it could not see, and that is the one
        // silence this must not leave. Entries came back, so the answer looks
        // complete, while the versions an upgrade is about never came in.
        if ($answered === []) {
            $lines[] = 'docs.typo3.org did not answer, so this answer comes from the installation alone: nothing '
                . 'above ' . ($versions[0] ?? 'what it ships') . ' is in it, and that is missing from this answer '
                . 'rather than from the changelog.';
        } elseif ($unread !== []) {
            $lines[] = self::unanswered($unread);
        }
        if ($installed === []) {
            $lines[] = 'No TYPO3 installation was found, so this answer comes from docs.typo3.org alone and an '
                . 'identifier search reaches nothing.';
        }
        // Only where the answer actually carries one. A caller who reads
        // entries from its own installation reads what it runs, and the
        // sentence would be about nothing.
        if (in_array('manual', array_column($entries, 'publishedIn'), true)) {
            $lines[] = 'An entry marked manual is what docs.typo3.org renders today, after every merge, and it '
                . 'links by URL. For a major that is not released yet it is still being written. An identifier '
                . 'search reaches only the entries this installation ships, whose text is on disk.';
        }

        $data = [
            'query' => $query,
            'matchCount' => count($matching),
            'matchedIn' => $read ? 'body' : 'name',
            'tags' => array_keys($tags),
            'entries' => $entries,
            'versions' => $versions,
            'answeredBy' => 'packages',
        ];
        if ($answered !== []) {
            $data['versionsFromTheManual'] = $ahead;
        }
        if (in_array('Deprecation', array_column($entries, 'type'), true)) {
            $lines[] = self::REMOVAL_RULE;
            $data['removalRule'] = self::REMOVAL_RULE;
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }

    /**
     * What this answer could see, and where each half came from.
     *
     * The two are never one list. An entry from docs.typo3.org is what it
     * renders today, and for a major without a release that is a target that
     * moves. An
     * entry from disk is the source the installation ships. An answer that
     * presented both as "the changelog" would hide the distinction an upgrade
     * turns on.
     *
     * @param array<int, string> $installed
     * @param array<int, string> $ahead
     * @param array<int, array<int, mixed>|null> $listings docs.typo3.org per covered major, null where it did not answer
     */
    private static function covers(array $installed, array $ahead, array $listings): string
    {
        $unread = array_keys(array_filter($listings, static fn(?array $entries): bool => $entries === null));
        $ships = $installed === []
            ? 'No TYPO3 installation was found, so nothing comes from disk and an identifier search reaches nothing.'
            : sprintf('This installation ships %s and older.', implode(', ', array_slice($installed, 0, 8)));
        if (count($unread) === count($listings)) {
            return $ships . ' docs.typo3.org did not answer, so this answer comes from the installation alone. The '
                . 'versions above what it ships are missing from this answer rather than from the changelog — ask '
                . 'again, or read them at https://docs.typo3.org.';
        }
        $published = $ahead === []
            ? 'docs.typo3.org lists nothing inside those filters.'
            : sprintf(
                '%s %s read from docs.typo3.org — what it renders today, after every merge, which for a major '
                . 'that is not released yet is still being written.',
                implode(', ', $ahead),
                count($ahead) === 1 ? 'is' : 'are',
            );

        return $published . ' ' . $ships . ($unread === [] ? '' : ' ' . self::unanswered($unread));
    }

    /**
     * The majors docs.typo3.org did not answer for, where it answered others.
     *
     * A major read per listing can be missing on its own, and a hit that
     * carries the others looks complete without this sentence.
     *
     * @param array<int, int> $unread
     */
    private static function unanswered(array $unread): string
    {
        return sprintf(
            'docs.typo3.org did not answer for %s, so %s from this installation where it ships %s, and %s missing '
            . 'from this answer rather than from the changelog where it does not.',
            implode(' and ', $unread),
            count($unread) === 1 ? 'that major comes' : 'those majors come',
            count($unread) === 1 ? 'it' : 'them',
            count($unread) === 1 ? 'it is' : 'they are',
        );
    }

    /**
     * The tags one entry carries, from the side that publishes it.
     *
     * A manual entry brought them in its listing, and an installed one keeps
     * them inside the file — `D-ANS-165`.
     *
     * @param array<string, mixed> $entry
     * @return array<int, string>
     */
    private static function tags(array $entry): array
    {
        if ($entry['publishedIn'] === 'manual') {
            /** @var array<int, string> $tags */
            $tags = $entry['tags'];

            return $tags;
        }

        return Changelog::read([
            'file' => (string) $entry['file'],
            'version' => (string) $entry['version'],
            'type' => (string) $entry['type'],
        ])['tags'];
    }

    /**
     * The title, the tags and the stated removal of one entry, from the side
     * that publishes it.
     *
     * docs.typo3.org serves the page the build rendered from the RST, and the
     * disk has the RST. Two readers, one shape, and this decides which.
     *
     * @param array<string, mixed> $entry
     * @return array{title: string, tags: array<int, string>, removal: string, migration: string}
     */
    private static function body(array $entry, CoreChangelog $manual): array
    {
        $version = (string) $entry['version'];
        $type = (string) $entry['type'];

        if ($entry['publishedIn'] === 'manual') {
            /** @var array{path: string, stated: string, tags: list<string>} $entry */
            return $manual->read(['path' => $entry['path'], 'version' => $version, 'type' => $type, 'stated' => $entry['stated'], 'tags' => $entry['tags']]);
        }

        return Changelog::read(['file' => (string) $entry['file'], 'version' => $version, 'type' => $type]);
    }

    /**
     * The same entries, each with the title its own side states.
     *
     * The installation's are a file read apiece and the manual's came with the
     * inventory. So this is where the free half comes in, and `Changelog` stays
     * unaware of the other side.
     *
     * @param array<int, array<string, mixed>> $entries
     * @return array<int, array<string, mixed>>
     */
    private static function titled(array $entries): array
    {
        $installed = array_values(array_filter(
            $entries,
            static fn(array $entry): bool => $entry['publishedIn'] !== 'manual',
        ));
        $published = array_values(array_filter(
            $entries,
            static fn(array $entry): bool => $entry['publishedIn'] === 'manual',
        ));

        /** @var array<int, array{type: string, issue: string, version: string, key: string, source: string, file: string}> $installed */
        return [...Changelog::titled($installed), ...$published];
    }

    /**
     * The axes that narrowed the call, as a miss names them back.
     *
     * The tag is not one of them. It comes from inside the file rather than off
     * the name, so the counts a miss prints never saw it. The tags those
     * entries do carry are what the answer offers there instead.
     *
     * @return array<int, string>
     */
    private static function narrowing(string $type, string $version): array
    {
        $narrowing = [];
        if ($version !== '') {
            $narrowing[] = sprintf('version "%s"', $version);
        }
        if ($type !== '') {
            $narrowing[] = sprintf('type "%s"', $type);
        }

        return $narrowing;
    }
}
