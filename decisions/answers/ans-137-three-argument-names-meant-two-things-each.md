---
id: D-ANS-137
title: Three argument names meant two things each
date: 2026-09-02
status: open
---

# D-ANS-137 — Three argument names meant two things each

**Every input property across the tools was read at once, and three names each
covered two concepts; the rest of what looked uneven is a rule holding.**

## Evidence

- Read on 2026-09-02 off `Registry::definitions()`, every `inputSchema()` at
  once. `query` is on twelve tools, `targetVersion` on twelve, `limit` on
  eleven; nothing else is on more than three.
- `open` was an enumeration mode on `typo3_forge_lookup` — `oldest`, `stale`,
  `newest` — and a boolean filter on `typo3_gerrit_lookup`. The same tool's own
  `status` takes `open` as a value, so one word was a way in and a status on one
  tool. `typo3_gerrit_lookup` already spelled the enumeration `backlog`.
- `changeType` was the kind of work on `typo3_task_guide`, lowercase and eleven
  values, and the TYPO3 commit message keyword on `typo3_commit_message_guide`,
  uppercase and five. The brief names the guide, so a caller carrying the value
  across is refused by the tool it was sent to. The second tool's own
  description already called it a keyword.
- `version` was a TYPO3 release prefix on `typo3_changelog_lookup` and the
  extension's own number on `typo3_ter_lookup`. Unqualified, `version` means the
  TYPO3 version everywhere else on this server, `targetVersion` included.

## Decided

- `typo3_forge_lookup` takes `backlog`, which is what the tool it is paired with
  calls the same thing. `Forge::backlog()` is renamed with it, because one
  concept is one word on both sides of the call.
- `typo3_commit_message_guide` takes `keyword`. It names its own subject, and
  the collision with the brief's kind of work is what it removes.
- `typo3_ter_lookup` takes `extensionVersion`, and echoes it back under that
  name. `version` keeps the meaning it has everywhere else.
- No alias is accepted for any of the three. `D-ANS-053` settled that: a second
  spelling that works is what makes a third one arrive, and this package is 0.x
  and says a caller pins a commit.
- `query` against `queries` is not one of these. Every plural argument on the
  surface is an array and every singular is a scalar — `paths`, `identifiers`,
  `urls`, `releases`, `sections`, `queries` against `path`, `query`, `task`. The
  plural is the rule holding rather than a second spelling.
- `task` is a third thing and stays. It is the work being described rather than
  a string to match, and it means that on all three tools that take it.
- `notes` against `messages` stays. Each is the word its own server uses for the
  comments, and each tool's description is written in that server's vocabulary
  throughout. Their defaults differ because what they cost differs, which the
  Gerrit description measures.
- The `targetVersion` defaults stay as they are. Four tools default to the
  majors a repository declares, six to the installation, and two require it: a
  tool answering for a package's declared range, one answering for what is
  installed and one reaching a host per release are three different questions,
  and each description says which it is.

## Assumed

- That no client has these three arguments written down. The package is 0.x, the
  readme says the shapes can move, and nothing here can see a caller.
- That reading the schemas at one moment says what the surface is. It is the
  whole of it and it was read whole; what it cannot say is which of these a
  caller actually reached for.

## Wrong if

- A session passes `open` to `typo3_forge_lookup` and reads the rejection as the
  tool being broken. The rename would then have moved the cost rather than
  removed it, and the answer is the rejection naming the argument it does have.
- `queries` is guessed as `query` again. `D-ANS-053` declared
  `additionalProperties` on that tool so the rejection names the property, and a
  second report would say the plural rule is invisible to a caller who reads one
  description.
- A fourth name turns out to cover two concepts. The reading above was one pass
  over the declarations, and what it catches is a collision visible in the
  schema rather than one visible only in an answer.

## Since then

The third **Wrong if** fired, and the way it said it would. The same pass over
`Registry::definitions()`, re-run on 2026-09-04, found a fourth name covering
two concepts: `path` was a file path on `typo3_gerrit_lookup` and
`typo3_translation_domain_lookup` and a path into `TYPO3_CONF_VARS` on
`typo3_configuration_lookup`, while `paths` is file paths on all three tools
that take it. It is `configurationPath` now, echoed back under that name and
with no alias — the maintainer settled it the same day.

Neither of the other two **Wrong if** has a report behind it. `query` on
`typo3_feedback_record` was looked at and left, since that tool records rather
than looks up, and `status`, `category` and `tag` are each one concept in two
vocabularies, which this entry already settled.
