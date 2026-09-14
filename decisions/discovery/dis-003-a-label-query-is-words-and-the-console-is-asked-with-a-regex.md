---
id: D-DIS-003
title: 'A label query is words and the console is asked with a regex'
date: 2026-07-29
status: revoked
---

# D-DIS-003 — A label query is words and the console is asked with a regex

**The words of a label query go to the console as one case-insensitive regex
union, and this server takes the intersection.**

`language:domain:search --search=` matches one literal string, so the words of a
query had to become something the console can answer at once.

## Decided

- The words go over as `--regex=/(one|two)/i`, the union, and this server takes
  the intersection. One call per query rather than one per word, because a
  console call boots TYPO3. The union is also what answers "save alone matches
  65 labels" without a second call.

## Assumed

- `--regex` is available wherever the command itself is. It arrived in the same
  commit as `--search`, `--json` and the command ([TASK] Add CLI command and
  service to search labels, on 14.0 and later). So an installation that answers
  the one answers the other.
- The match is a plain case-insensitive substring on both sides, not a word
  boundary as elsewhere in this server. A trans-unit id is
  `labels.save_document` and an underscore is a word character, so an anchor
  would drop exactly the ids a caller searches by.
- A console that exits 0 without a JSON payload found nothing. That is what this
  command does: it prints `[WARNING] No language resource files found.` and
  returns SUCCESS. No other command this server calls answers `--json` with
  anything but JSON.

## Wrong if

- A command exits 0 and prints nothing usable for a reason other than an empty
  result. The answer would then be a confident "none" where nothing stands
  established. The exit code is the only signal the server reads.

## Revoked on 2026-08-01

Not because somebody caught the "Wrong if" in the act. It was not. The measure
is the half that does not depend on a catch. Four different stdouts went to
`typo3_label_lookup` through a fixture console. They were the real
`[WARNING] No language resource files found.`, an empty one, and two with a
bracket ahead of an intact payload. All four came back as one answer,
`answeredBy: installation`, `matchCount: 0`, "No label in … carries all of". In
the two with a payload the installation held the very label the query named. The
tool had one shape for four inputs and no way to tell them apart. That is enough
to correct without a name for who produces the input.

## Revoked on 2026-08-01

The mechanism first written here was wrong. `--json` does share stdout with the
title the command prints ahead of it. But neither named carrier that could put
something onto that stream survived a check. Xdebug's connection message goes to
stderr, which the server reads on its own pipe. A deprecation reaches stdout
only with `display_errors=On`, and not even then inside a booted command. **No
producer of stdout noise ahead of the payload stands established.**

## Since then

The exit code is not the signal, on the narrower ground that it never certified
anything. Only the warning reads as "none", and every other exit-0 without a
payload takes the route an unreachable console takes. A read of the warning
fails in the safe direction: a moved wording costs a fallback rather than a
wrong answer. The transport does not fold the two streams either, read from
DDEV's own source rather than from a run. `ddev exec` appends `-T` where stdin
is not a terminal, so Docker allocates no pseudo-TTY and keeps the streams
apart. What a run would still add is what a container that misbehaves puts on
them.
