<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Yaml\Yaml;

/**
 * The head a requirement and a decision share: front matter, a heading with the
 * id and the title, and the sentence under it.
 *
 * One format read in one place. `Requirements` and `Decisions` had the same six
 * lines and the same `frontMatterValue()` each. So the two could have drifted
 * on what a heading looks like while every check stayed green. A todo opens the
 * same way and reads its front matter through `opening()`, where its heading is
 * the title alone, `D-DOC-062`.
 *
 * An entry rather than a record, because in TYPO3 a record is a row in the
 * database and the corpus names `Record::get()` as that. A class called
 * `Record` here answers a backticked name that belongs to somebody else.
 */
final class Entry
{
    /**
     * @return array{matter: array<string, mixed>, heading: string, written: string, statement: string}
     */
    public static function head(string $contents): array
    {
        $opening = self::opening($contents);
        preg_match('/^(\S+) — (.*)$/', $opening['heading'], $named);

        return [
            'matter' => $opening['matter'],
            'heading' => $named[1] ?? '',
            // What the heading says the title is. The front matter is the
            // title; this is what a check holds it to, so the two cannot drift.
            'written' => $named[2] ?? '',
            // Everything under the first paragraph is what the entry was
            // written from, so the statement ends where that paragraph does.
            'statement' => trim(str_replace('**', '', self::firstParagraph($opening['body']))),
        ];
    }

    /**
     * A markdown file in the three parts every file here opens with: the front
     * matter as data, the heading, and what stands under it.
     *
     * The one place those are told apart. A requirement, a decision and a todo
     * are the same shape and differ in what the heading says. So a second
     * reader would be the same two patterns once more, `D-DOC-062`.
     *
     * @return array{matter: array<string, mixed>, heading: string, body: string}
     */
    public static function opening(string $contents): array
    {
        preg_match('/^---\R(.*?)\R---\R/s', $contents, $matter);
        $below = ltrim(substr($contents, strlen($matter[0] ?? '')));
        preg_match('/^# ([^\n]*)\R?/', $below, $heading);

        return [
            'matter' => self::matter($matter[1] ?? ''),
            'heading' => trim($heading[1] ?? ''),
            'body' => ltrim(substr($below, strlen($heading[0] ?? ''))),
        ];
    }

    /**
     * The front matter as data, which is what it is.
     *
     * Read with a YAML parser rather than a line at a time. It carries a list
     * since the tests moved into it. A hand-rolled reader for one is a third
     * parser over a format somebody else already implements.
     *
     * @return array<string, mixed>
     */
    public static function matter(string $frontMatter): array
    {
        if (trim($frontMatter) === '') {
            return [];
        }

        try {
            // A date comes back as a date rather than becomes the Unix
            // timestamp the parser answers by default, so `2026-08-23` comes
            // back as the day it stands as.
            $parsed = Yaml::parse($frontMatter, Yaml::PARSE_DATETIME);
        } catch (\Throwable) {
            return [];
        }

        return is_array($parsed) ? $parsed : [];
    }

    /**
     * The file name an entry takes, which is its id and its title.
     *
     * One implementation, because the check that holds a name and whatever
     * writes one have to agree on every character of it. An apostrophe or a
     * backtick goes rather than turns into a separator, so "a package's build"
     * reads `a-packages-build`. Every other run of what is neither a letter nor
     * a figure is one dash.
     */
    public static function fileName(string $id, string $title): string
    {
        $slug = strtolower(str_replace(["\u{2019}", "\u{2018}", '`', "'"], '', $title));
        $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', $slug), '-');

        return strtolower(substr($id, 2)) . '-' . $slug . '.md';
    }

    /**
     * The entries a test holds, which is what a red one prints.
     *
     * Read from the generated list, which the test's own `#[Decision]` or
     * `#[Requirement]` attributes wrote. So this answers with the entries that
     * test declared, and a session in a red test goes to them.
     *
     * A whole class counts for every method in it: an entry that names
     * `VersionsTest` rests on all of it.
     *
     * @param array<string, array{id: string, title: string, group: string, file: string, tests: list<string>}> $entries
     * @return list<array{id: string, title: string, file: string}>
     */
    public static function restingOn(array $entries, string $corpus, string $class, string $method): array
    {
        $held = [];
        foreach ($entries as $entry) {
            if (!in_array($class . '::' . $method, $entry['tests'], true) && !in_array($class, $entry['tests'], true)) {
                continue;
            }
            $held[] = [
                'id' => $entry['id'],
                'title' => $entry['title'],
                'file' => $corpus . '/' . $entry['group'] . '/' . $entry['file'],
            ];
        }

        return $held;
    }

    /**
     * The entry's file as the generated list of names would write it.
     *
     * The `#[Decision]` and `#[Requirement]` attributes are the source and the
     * front matter is the copy. That is why the whole file comes back rather
     * than the list. `decisions:cover` and `requirements:cover` write what this
     * returns and the checks compare against it, so one implementation decides
     * what the front matter says.
     *
     * An entry no test names keeps what it came with: an empty list, or the
     * `not guarded` a person wrote to say nothing holds it. Either says nothing
     * holds the entry, where the absence of the key says nobody has asked.
     *
     * @param list<string> $names
     */
    public static function withNames(string $contents, string $key, array $names): string
    {
        $written = $names === []
            ? $key . ": []\n"
            : $key . ":\n" . implode('', array_map(static fn(string $name): string => '  - ' . $name . "\n", $names));

        // Whatever value the key came with, so that one written as a word gives
        // way rather than stays for a second key to follow under it. Matching
        // only the empty and the `[]` forms put two `coveredBy:` into every
        // entry somebody had written `not guarded` on.
        $carries = '/^' . $key . ': *(.*)\R(?:  - .*\R)*/m';
        if (preg_match($carries, $contents, $already) === 1) {
            // What a person wrote to say nothing holds it stays in their words.
            // Rewriting `not guarded` to `[]` would say the same thing and lose
            // that somebody heard the question and answered.
            if ($names === [] && !in_array(trim($already[1]), ['', '[]'], true)) {
                return $contents;
            }

            // A callback rather than a replacement string: a `$` in one would
            // count as a group reference, and what stands here is a name.
            return (string) preg_replace_callback($carries, static fn(): string => $written, $contents, 1);
        }
        if ($names === []) {
            return $contents;
        }

        // The key is new, and goes at the foot of the front matter: the first
        // `---` on a line of its own, the first one at the very start of the
        // file.
        return (string) preg_replace_callback('/\R---\R/', static fn(): string => "\n" . $written . "---\n", $contents, 1);
    }

    /**
     * One key of the front matter as a string, or nothing where it is not
     * written.
     *
     * @param array<string, mixed> $matter
     */
    public static function value(array $matter, string $key): string
    {
        return self::text($matter[$key] ?? '');
    }

    /**
     * One entry of the front matter as the file has it.
     *
     * A bare date is a `DateTimeImmutable` once the parser has finished with
     * it, and the value the file carries is the day. So it renders back rather
     * than drops, which is what a scalar test alone did to every `readings:`
     * date (`D-DOC-066`).
     */
    private static function text(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return is_scalar($value) ? trim((string) $value) : '';
    }

    /**
     * One key as a list of names, whatever shape the file has it in.
     *
     * @param array<string, mixed> $matter
     * @return list<string>
     */
    public static function names(array $matter, string $key): array
    {
        $value = $matter[$key] ?? [];
        if (is_string($value)) {
            $value = [$value];
        }

        if (!is_array($value)) {
            return [];
        }

        $names = array_map(self::text(...), $value);

        return array_values(array_filter($names, static fn(string $name): bool => $name !== ''));
    }

    /**
     * The first paragraph of a body, which is where a statement ends.
     *
     * A pattern that will not compile is the one way `preg_split()` answers
     * `false`, and these are literals. So the empty string is what a body with
     * nothing in it gives, and there is no second case to read.
     */
    public static function firstParagraph(string $body): string
    {
        return (preg_split('/\R\R/', $body, 2) ?: [''])[0];
    }
}
