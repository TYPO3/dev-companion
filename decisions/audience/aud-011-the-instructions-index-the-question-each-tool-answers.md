---
id: D-AUD-011
title: 'The instructions index the question each tool answers'
date: 2026-08-18
status: open
coveredBy:
  - ScopeTest::theIndexNamesNoToolTheCallerExcluded
  - ScopeTest::theInstructionsFitWhatAClientKeeps
  - ScopeTest::theInstructionsIndexTheQuestionEachToolAnswers
  - ScopeTest::theScopeInstructionsOrientTheClientBeforeItsFirstCall
---

# D-AUD-011 — The instructions index the question each tool answers

**The `instructions` name, per question, the tool that answers it, and the entry
is paid for out of the same 2048 characters.**

Two sessions on one day called this server nothing at all, in two projects and
at two task shapes. The client lists the tools by name and defers their schemas.

## Evidence

- `feedback/2026-08-18-113308`, a pull request review in `bootstrap_package`.
  All 27 tools arrived as bare names in a system reminder. So the tool
  `description`, the surface a caller chooses a tool on, was outside the
  session's context. Roughly 130 calls over six turns went to Bash. The
  `instructions` did arrive in full, which is what the session says must stay
  intact. It asks for five to eight lines that map a question to a tool name.
- `feedback/2026-08-18-080710`, a TypoScript condition repaired in `blog` across
  two majors. Zero calls, and it names the one line that would have caught it.
  That is the `routing` entry which sends a caller onto a major they have not
  built on recently to `typo3_changelog_lookup`, the call the session says it
  would make next time.
- Step 2 of the ladder in
  [judging.rst](../../documentation/records/judging.rst). The line exists, has
  stood in `knowledge/server-scope.json` since 2026-07-29 and describes that
  session precisely. The whole `routing` block sits behind `typo3_server_scope`,
  which is a call, which a session that calls nothing never makes.
- The channel was already established.
  [`D-AUD-003`](aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md)
  put the entry point in the `instructions` because under deferral the
  descriptions are not a channel at all.
  [`D-SKL-060`](../task-skills/skl-060-a-skill-names-a-tool-at-the-step-that-needs-it.md)
  named them as the candidate and left them as they were so this feedback would
  have its read before they moved.
- The room is what is scarce. Measured on 2026-08-18, the worst assembled case
  stood at 2038 characters of the 2048
  [`R-ANS-013`](../../requirements/answers/ans-013-the-instructions-fit-what-a-client-keeps.md)
  holds. That case is the stale-publication notice, a caller that excluded every
  tool it can, and the write sentence. So five to eight added lines are about
  six times the headroom there was.
- One line the feedback asks for is not written. Its example sends a core TCA
  palette across two majors to `typo3_schema_lookup`, which answers for the
  table as the installation's own container assembles it. The cross-major half
  is `feedback/2026-08-18-113327` and has its own card.

## Decided

- The three `Before …, call …` sentences become an index, and the index gains
  `typo3_changelog_lookup`. The stored text came out three characters shorter
  than it went in, and the worst case measures 2035.
- What paid for the entry. The repeated frame the three sentences carried,
  `against the installation` on the icon line, and `the range it does`. Also
  `its knowledge is English and`, where the rule and the mitigation both stay. A
  displacement rather than an addition is what `R-ANS-013` asks of the next line
  either way.
- Not the five to eight lines. Every further entry costs about a hundred
  characters, and a decision placed everything that remains to displace. That is
  the entry point, the three runtime lookups, the English rule, the guides
  sentence. `typo3_hint_lookup` is the largest surface left out, and neither
  feedback reports a session that lost anything for want of it.
- No line says anything about a client. An index is worth as much to a client
  that shows descriptions. A sentence about schema deferral in a file every
  client reads is the objection `D-SKL-060` raised against the fetch line.
- The commit that carries this archives both feedback. No lookup about TYPO3 ran
  and no contract moved, which is what
  [judging.rst](../../documentation/records/judging.rst) asks before a feedback
  closes on the spot. The fetch line in the skills is a different lever, and
  `feedback/2026-08-18-074627` stays open on `D-SKL-060`'s question.

## Assumed

- That a session acts on a name-only list plus an index. The measurement covers
  two sessions that had the names and the workflow frame and called nothing.
  Nobody has watched a session choose off the index.
- That 2048 still binds.
  [`D-ANS-004`](../answers/ans-004-the-instruction-budget-is-2048-characters-on-one-clients-evidence.md)
  measured one client, and a client that keeps more gets less than it would
  read.
- That the changelog entry is the most valuable line left. It is the one both
  feedback name; the rest would be speculation about task shapes nobody
  reported.

## Wrong if

- A session under a client that defers reads the index and calls nothing anyway.
  Then the channel was not the gap, and what remains to try is the fetch line
  `D-SKL-060` proposed.
- A session reaches for a tool the index names and reports that the answer does
  not fit the question. Then the line sold a tool it should not have, and
  compression is what did it.
- An entry earns its place and nothing remains to displace for it. Then the
  budget is the question rather than the wording, which is `D-ANS-004`'s **Wrong
  if** and not this one.

## Since then

The index is data in `knowledge/server-scope.json` since 2026-08-18, so
`Coverage::offered()` drops the entry of a tool the caller excluded. The room
that freed bought the commit-message line the corpus had asked for twice.


The first **Wrong if** fired twice, and neither is an absent index. One session
had decided this server could not help and called nothing. An index answers a
session on the search for a tool, not one that gave up the search. What that
session asks for instead is the boundary. The other read the entry and made no
call. The checkout's own `AGENTS.md` carries the commit rules, and a tool named
for the same question read as redundant. The entry names the branches now.

Every lever left is instruction-block characters that do not exist, twenty free
in the longest assembly. So what goes up is the budget itself, which `D-ANS-004`
measured on one client and `todo/waiting/T-260819-dcaf.md` carries.

