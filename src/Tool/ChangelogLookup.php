<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Changelog;
use TYPO3\DevCompanion\Manual\CoreChangelog;
use TYPO3\DevCompanion\Result\Miss;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;
use TYPO3\DevCompanion\Search\LabelSearch;

/**
 * What a TYPO3 version changed, from the changelog that installation ships.
 *
 * The one question the knowledge base cannot answer from conventions: what a
 * given release broke, deprecated or added is a list, and the list is on disk in
 * every installation.
 */
final class ChangelogLookup extends ReadOnlyTool
{
    /** The versions above the installed major are read from docs.typo3.org. */
    protected const OPEN_WORLD = true;

    /**
     * What an entry that states no removal leaves to be said.
     *
     * The removal version is what an upgrade audit decides on, and an empty
     * field beside a populated one is read as "no removal planned" — the
     * silence-as-verdict failure `D-ANS-009` was built against. So the rule that
     * covers the silence travels with the answer as data and not only as text,
     * which is what `R-ANS-002` is written against. It is stated, never applied
     * per entry: a number derived from the rule would have been wrong where the
     * core kept an entry that skips a major.
     */
    private const REMOVAL_RULE = 'A deprecated API keeps working until the next major release. An entry that '
        . 'states a removal version overrides that, and some state one more than a major away. An empty removal '
        . 'is what the entry states, not a promise that no removal is planned.';

    public static function name(): string
    {
        return 'typo3_changelog_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Packages, Source::Network];
    }


    public static function description(): string
    {
        return 'Search the TYPO3 changelog: one entry per breaking change, deprecation, feature and important note, in the version it was released in. This reads the entries; writing one for a core patch of your own is the other direction and is typo3_rule_lookup with documentId "core/contribution/changelog". Answers "what did this version deprecate", "what changed about X", "which release introduced Y". This is the first stop when building on a major you have not built on recently: what separates a current answer from a two-major-old one is written down here and almost nowhere else. A deprecation carries the version it stops working in where the entry states one, and the rule that answers the rest beside it. The versions the installation ships are read from the core package on disk; the ones above its own major are read from docs.typo3.org, which is what an upgrade to a version you have not installed is asking for. Every word of the query has to be carried by an entry; narrow further with type and version. A version and a type with the query omitted list whole under a raised limit, which is the deprecation sweep of one major in a single call. A method or class you found in the code is a query of its own: an identifier reaches the entries naming it, whether or not the change was titled after it — inside the installed versions, which are the ones whose text is on disk.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Words the entry has to carry, matched against its file name and the words that name spells. Where no entry carries all of them by name, the title stated inside the file is searched as well, which reaches a method name the file name leaves out; and a class, method or constant name reaches the entries that write it in their text, so a removed API can be asked for by the identifier you have, in any spelling of it: bare, qualified by its class, or fully qualified. The issue number is among the words a file name carries, so a deprecation\'s own number reaches every entry filed under it — the Feature the replacement was announced in, with the version it was released in. When nothing carries all of them there either, the answer names the largest part of the query that does reach entries, which is what to ask again with. Omit to list a version or a type as a whole.'],
                'type' => ['type' => 'string', 'enum' => ['breaking', 'deprecation', 'feature', 'important'], 'description' => 'Restrict to one kind of change. Breaking and deprecation are what affects existing code.'],
                'version' => ['type' => 'string', 'description' => 'Restrict to a version, by prefix: "14" covers 14.0 through 14.3.x, "13.4" covers 13.4 and 13.4.x.'],
                'tag' => ['type' => 'string', 'description' => 'Restrict to entries carrying this index tag: "ext:form" for the system extension a change is in, "FullyScanned" or "NotScanned" for what the Extension Scanner has a matcher for, "PHP-API", "TCA", "Backend", "Frontend" for the surface. This bounds one question inside a version and a type. The sweep of a major does not need it: that version and type come back whole under a raised limit, and every entry carries its own tags to be read by. The changelog says nothing about which third-party extension a change affects, so an extension key of your own matches no tag.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 200, 'default' => 20, 'description' => 'Maximum number of entries. Raise it to list a version and a type whole: the largest covered major holds 128 deprecations, and that sweep is one call rather than one per tag.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'query' => Schema::string(),
            'matchCount' => Schema::integer('Entries carrying every word of the query and the tag, before the limit.'),
            'matchedIn' => Schema::string('Where the query was carried: "name" for the entry names, "body" where no name carried it and the inside of the file did — the title as it is stated, or an identifier the text writes. A body match can name the identifier without being about it, so read the title of each. Returned where the answer carries entries.'),
            'tags' => Schema::listOf(Schema::string(), 'Every index tag the entries of this version and type carry, with the ones already filtered by among them. Returned where a tag was asked for, so a tag that matched nothing can be replaced by one that exists.'),
            'entries' => Schema::listOf(Schema::object([
                'type' => ['type' => 'string', 'enum' => ['Breaking', 'Deprecation', 'Feature', 'Important']],
                'version' => Schema::string('The version directory it was released in.'),
                'issue' => Schema::string('Forge issue number.'),
                'title' => Schema::string(),
                'removal' => Schema::string('The version a Deprecation states the deprecated thing stops working in — what an upgrade decides on. Empty on the other three types, and on a deprecation whose entry states none, which is most of a major and is not "no removal planned": removalRule is what answers it there.'),
                'migration' => Schema::string('What to write instead, as the entry\'s own Migration section states it, code blocks included. Carried where the call reached one entry — an issue number, or a query that matched one — because a sweep of seventy-five is a list of titles and not seventy-five migrations. Empty on every entry of a longer answer, and on an entry whose file states no migration.'),
                'tags' => Schema::listOf(Schema::string(), 'Index tags. FullyScanned or PartiallyScanned means the extension scanner has a matcher for it.'),
                'file' => Schema::string('Where to read the description and the migration: an EXT: reference where the installation ships the entry, and a docs.typo3.org URL where it does not.'),
                'publishedIn' => Schema::string('Which side the entry came from. "installation" is the core package on disk, which is the version that installation runs. "manual" is docs.typo3.org, which is every version above the installed major — what an upgrade reads, and a moving target for a major that is not released yet.'),
            ], ['type', 'version', 'issue', 'title', 'removal', 'migration', 'tags', 'file', 'publishedIn'])),
            'termCounts' => Schema::termCounts('What each word of the query reaches on its own, inside the version and the type that were asked for. A word at 0 is the one that emptied the answer — it is misspelled, or nothing here is named after it. Returned on a miss that carried words. These are counts and not a query: termSubsets is what can be asked outright.'),
            'termCountsWithoutTheNarrowing' => Schema::termCounts('The same words counted over the whole changelog rather than inside the version and the type. Returned only where a word reaches there and nothing inside the narrowing, which makes the filter what emptied this answer rather than the words: ask again without it.'),
            'termSubsets' => Schema::listOf(Schema::object([
                'terms' => Schema::listOf(Schema::string(), 'Words of the query, as a query to ask again with.'),
                'matchCount' => Schema::integer('Entries carrying every word of this subset, inside the same version and type.'),
            ], ['terms', 'matchCount']), 'The largest parts of the query that do reach entries, narrowest first — every one of them, because the one a tie-break puts first is not always the one being looked for. Withheld where a tag was asked for: these are counted off the entry names and a tag is read inside the file, so a subset offered there would promise entries the same call does not return.'),
            'removalRule' => Schema::string('When a deprecation stops working where the entry itself does not say. Returned where the answer carries a deprecation.'),
            'versions' => Schema::listOf(Schema::string(), 'The versions this installation ships changelog entries for, newest first.'),
            'versionsFromTheManual' => Schema::listOf(Schema::string(), 'The versions above those, read from docs.typo3.org, newest first. Absent where the host did not answer, which is the one case a version is missing from this answer rather than from the changelog.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
        ], ['query', 'matchCount', 'entries', 'versions', 'answeredBy'], ['query']);
    }

    /**
     * The one system extension every match sits in, where the query never
     * named it.
     *
     * `extendToSubpages` is the TCA column and the natural word for inherited
     * frontend access restriction, and the changelog answers it with a single
     * 12.0 Breaking removing an Indexed Search option that happens to spell it.
     * The answer is arguably correct — that area was never reworked, and a
     * changelog records change events — but a session that started from the
     * column name and stopped there reads one hit as evidence about the area
     * (`feedback/2026-08-07-233553`).
     *
     * Only where the query does not name it, because a caller asking about
     * indexed search is answered by `ext:indexed_search` entries and told
     * nothing by being reminded of it — matched a word at a time, since nobody
     * types the key with its underscore.
     *
     * `ext:core` is never it. Most of what the changelog records is in there,
     * so "every one of these is in ext:core" is a statement about the corpus
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
            // says nothing about the query being answered by the wrong one.
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

        if (Changelog::directory() === null) {
            return Unsupported::because(
                'no TYPO3 installation was found whose core package ships the changelog',
                ['query' => $query],
            );
        }

        $terms = LabelSearch::terms($query);
        $installed = Changelog::versions();
        $narrowed = array_map(
            static fn(array $entry): array => $entry + ['publishedIn' => 'installation'],
            Changelog::entries($type, $version),
        );

        // What the installation ships stops at its own major, and the versions
        // above it are the ones an upgrade is asking about. They come from the
        // manual, and only where the installation has no directory of its own
        // for them — a version it ships is the version it runs, and the two
        // must never both be in one answer.
        // Not where the caller named a version this installation ships. That
        // answer is complete on disk, and the tool a session calls most should
        // not pay a round trip — or, on a machine with no network, a connect
        // timeout — for entries the narrowing has already excluded.
        $manual = new CoreChangelog();
        $asksBeyond = $version === '' || !self::ships($installed, $version);
        $published = $asksBeyond ? $manual->entries() : [];
        $ahead = [];
        if ($published !== null) {
            foreach ($published as $entry) {
                if (in_array($entry['version'], $installed, true)) {
                    continue;
                }
                if ($version !== '' && !str_starts_with($entry['version'], $version)) {
                    continue;
                }
                if ($type !== '' && $entry['type'] !== ucfirst(strtolower($type))) {
                    continue;
                }
                $ahead[] = $entry['version'];
                $narrowed[] = $entry + ['publishedIn' => 'manual'];
            }
        }
        $ahead = array_values(array_unique($ahead));
        usort($ahead, static fn(string $a, string $b): int => version_compare($b, $a));

        $matching = LabelSearch::carryingEvery($narrowed, $terms);

        // The names answer first and the file only where they answered nothing,
        // so a call that hits today pays no read and nothing that matches today
        // stops matching. Opening every file costs an order of magnitude more
        // than scanning the names, and where the names answer nothing there is
        // no answer to slow down — `D-ANS-041`, and `D-ANS-042` for what the
        // same read takes out of the body.
        //
        // Two things are taken out of that one read, because the file is open
        // either way: the title as it is stated, which the name spells
        // differently, and the identifiers the body writes, which the name
        // leaves out. The counts and subsets a miss prints run over the same
        // enriched entries, so they say what was actually searched.
        //
        // The manual's half of that read is free and its other half is not.
        // The inventory line already carries the stated title, so a title
        // search costs nothing there; the identifiers are in the body, and
        // reading 469 of them over the network is six seconds for a fallback
        // that runs on a miss. So a manual entry is searched by its title and
        // never by its identifiers, and the answer says so where that is what
        // the caller was doing.
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

        // The tags are inside the file, so narrowing by one costs a read of
        // every entry that survived the type and the version — 23 ms for the
        // deprecations of one major, six hundred for the whole changelog. That
        // read is why it is a field of its own rather than more words in the
        // query, and it bounds one question rather than a sweep: a major comes
        // back whole from the version and the type under a raised `limit`,
        // which `D-ANS-093` measured against eleven tag calls.
        $tags = [];
        if ($tag !== '') {
            $carrying = [];
            foreach ($matching as $entry) {
                $carried = self::body($entry, $manual)['tags'];
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
        // The migration is the part a session went to the file for, and it is
        // handed over where the answer is about one entry. On a sweep it is the
        // volume the titles exist to keep down — `D-ANS-139`.
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
            // Every count on this miss is taken inside the version and the
            // type, and reads as a fact about the changelog: the reported miss
            // said "preview reaches 1 entry" at `version: "15"` where all four
            // words reach without it, and the session concluded the tool could
            // not reach the entry at all. So where a word reaches outside the
            // narrowing and nothing inside it, the filter is what emptied the
            // answer and that is the first sentence — `D-ANS-016`. The second
            // scan is the whole changelog and costs 48 ms for the 3795 entries
            // of `/home/benji/projects/typo3-cms`, on a narrowed miss alone. It
            // reads the names and not the titles, because what it establishes
            // is which filter emptied the answer, and the whole-file read is
            // what the caller pays once it asks again without that filter.
            $outside = [];
            if ($narrowing !== [] && $terms !== []) {
                $inside = array_column($counts, 'matchCount', 'term');
                $reaching = array_values(array_filter(
                    LabelSearch::perTermCounts(Changelog::entries(), $terms),
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
            // the words that do reach something together. Offered where no tag
            // was asked for, because the peel reads file names while a tag is
            // inside the file — a subset counted without the tag would promise
            // entries the same call does not return. On the narrowed set, for
            // the same reason.
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
                // missing is the corpus and not the words — `D-ANS-010`, which
                // routes "does it still work" to the manual. After the offer
                // and never in place of it: the reported miss did carry the
                // entry its review turned on, one subset away, and a sentence
                // naming the manual first is what would have routed that
                // session away from it (`D-ANS-043`). Offered nowhere else,
                // because "that" is the re-query and a miss with none has
                // nothing for this sentence to follow.
                $lines[] = 'Where that comes back empty too, ask typo3_documentation_lookup with targetVersion: a '
                    . 'changelog records change events, so a mechanism nobody changed has no entry here, and '
                    . 'whether one still holds in a version is what the manual answers.';
            } elseif ($terms !== [] && $outside === [] && $tag === '') {
                // Nothing was computed to ask this corpus again with, so the
                // next call is a different corpus rather than a different query
                // — `R-ANS-018`, which the branch above held alone. Both of
                // them, because a miss says nothing about which of the two
                // shapes the question had, and a caller with no re-query left
                // cannot recover from being sent to the wrong one —
                // `D-ANS-110`.
                //
                // Not where a filter or a tag emptied the answer: those name
                // their own way back into this corpus, and routing out of it
                // ahead of a re-query that answers is what `D-ANS-043`
                // declined.
                $lines[] = 'A changelog records change events, so a miss can mean the question belongs to another '
                    . 'corpus. Whether a mechanism nobody changed still holds is typo3_documentation_lookup with '
                    . 'targetVersion; whether a core patch of your own owes an entry is typo3_rule_lookup with '
                    . 'documentId "core/contribution/changelog".';
            }
            $lines[] = self::covers($versions, $ahead, $asksBeyond, $published === null);

            // What the miss worked out is a field as well as a line. A session
            // read `matchCount: 0` and the five fields beside it, reported that
            // nothing came back to re-ask with, and settled its question by
            // grep — while the text of that same answer offered the subset that
            // returns the entry its review turned on (`D-ANS-043`, and
            // `R-ANS-002` for the client that renders `structuredContent` and
            // drops the text block).
            //
            // Each field is present where it was computed and absent where it
            // was withheld, under the two withholdings the text already makes:
            // the subsets never travel beside a `tag`, and a count says which
            // side of the narrowing it was taken on by which of the two fields
            // carries it.
            $data = [
                'query' => $query,
                'matchCount' => 0,
                'tags' => array_keys($tags),
                'entries' => [],
                'versions' => $versions,
                'answeredBy' => 'packages',
            ];
            if ($asksBeyond && $published !== null) {
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
        // silence this must not leave: entries came back, so the answer looks
        // complete, while the versions an upgrade is about were never read.
        if ($asksBeyond && $published === null) {
            $lines[] = 'docs.typo3.org did not answer, so nothing above ' . ($versions[0] ?? 'this installation')
                . ' is in this answer — those versions are missing from it rather than from the changelog.';
        }
        // Only where the answer actually carries one. A caller reading entries
        // from its own installation is reading what it runs, and the sentence
        // would be about nothing.
        if (in_array('manual', array_column($entries, 'publishedIn'), true)) {
            $lines[] = 'Entries above ' . ($versions[0] ?? 'this installation') . ' come from docs.typo3.org rather '
                . 'than from this installation: they are what the host publishes today, they are linked by URL '
                . 'instead of by EXT: path, and for a major that is not released yet they are still being written. '
                . 'An identifier search does not reach them — their text is not on disk, so they are searched by '
                . 'name and by the title the manual states.';
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
        if ($asksBeyond && $published !== null) {
            $data['versionsFromTheManual'] = $ahead;
        }
        if (in_array('Deprecation', array_column($entries, 'type'), true)) {
            $lines[] = self::REMOVAL_RULE;
            $data['removalRule'] = self::REMOVAL_RULE;
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }

    /**
     * Whether the installation has a directory of its own for what was asked.
     *
     * By prefix, the way the version filter itself narrows: "13.4" is shipped
     * where `13.4` or `13.4.x` is on disk, and "14" is not where nothing there
     * starts with it.
     *
     * @param array<int, string> $installed
     */
    private static function ships(array $installed, string $version): bool
    {
        foreach ($installed as $shipped) {
            if (str_starts_with($shipped, $version)) {
                return true;
            }
        }

        return false;
    }

    /**
     * What this answer could see, and where each half came from.
     *
     * The two are never one list. A caller acting on an entry above its own
     * major is reading what the host publishes today, and for a major that is
     * not released that is a moving target — an answer that presented both as
     * "the changelog" would hide exactly the distinction the upgrade turns on.
     *
     * @param array<int, string> $installed
     * @param array<int, string> $ahead
     */
    private static function covers(array $installed, array $ahead, bool $asked, bool $unreachable): string
    {
        $line = sprintf(
            'This installation ships %s.',
            $installed === [] ? 'no changelog at all' : implode(', ', array_slice($installed, 0, 8)) . ' and older',
        );
        if (!$asked) {
            return $line . ' The version asked for is one of them, so nothing was read from docs.typo3.org — ask '
                . 'without the version filter, or for one above the installed major, to reach what is published '
                . 'there.';
        }
        if ($unreachable) {
            return $line . ' docs.typo3.org did not answer, so the versions above its own major are missing from '
                . 'this answer rather than from the changelog — ask again, or read them at https://docs.typo3.org.';
        }
        if ($ahead === []) {
            return $line . ' Nothing above that is published yet.';
        }

        return $line . sprintf(
            ' Above that, %s %s read from docs.typo3.org — what the host publishes today, which for a major that '
            . 'is not released yet is still being written.',
            implode(', ', $ahead),
            count($ahead) === 1 ? 'is' : 'are',
        );
    }

    /**
     * The title, the tags and the stated removal of one entry, from the side
     * that publishes it.
     *
     * One parser reads both, because the host serves the same RST the package
     * ships — what differs is the delivery, and that is the whole of what this
     * decides.
     *
     * @param array<string, mixed> $entry
     * @return array{title: string, tags: array<int, string>, removal: string, migration: string}
     */
    private static function body(array $entry, CoreChangelog $manual): array
    {
        $version = (string) $entry['version'];
        $type = (string) $entry['type'];

        return $entry['publishedIn'] === 'manual'
            ? $manual->read(['path' => (string) $entry['path'], 'version' => $version, 'type' => $type])
            : Changelog::read(['file' => (string) $entry['file'], 'version' => $version, 'type' => $type]);
    }

    /**
     * The same entries, each carrying the title its own side states.
     *
     * The installation's are a file read apiece and the manual's came with the
     * inventory, so this is where the free half is taken and `Changelog` keeps
     * knowing nothing about the other side.
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
     * The axes the call was narrowed on, as a miss names them back.
     *
     * The tag is not one of them: it is read inside the file rather than off
     * the name, so the counts a miss prints never saw it, and the tags those
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
