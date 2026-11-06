---
date: 2026-11-03T13:11:26+00:00
category: bug
status: closed
closed: 2026-11-06
model: claude-opus-5
---

# the initialize instructions pass the budget by 63 characters in an installed project

## Observation

The task was a `composer test` run in this checkout, before a commit on a
scenario file. One test failed:

    StdioServerTest::theServerAnnouncesItselfWithItsBoundary
    Failed asserting that 2111 is equal to 2048 or is less than 2048.

The instructions the server sends at initialize are 2111 characters.
`Coverage::INSTRUCTIONS_BUDGET` is 2048. So the string is 63 over.

`Coverage::instructions()` is 1936 characters on its own. The notice
`Installer::installedIn()` writes stands in front of it. That notice is 175
characters where the record names two skills directories.

    base                              1936
    installedIn(), two directories     175    total 2111
    installedIn(), one directory       159    total 2095
    Installer::ABSENT                  105    total 2041
    Installer::NOTICE                   85    total 2021

So one client alone passes the budget by 47. Only the notice that names the
directories, `D-DIS-035`, does not fit.

CI never sees this. A runner has no install record, because `.claude` is
gitignored. `Installer::installedIn()` returns an empty string there. So the
handshake is 1936 characters and the test passes. The failure reaches the
person who ran `install` in a checkout of this server.

`D-ANS-004` measured that the client cuts at exactly 2048. Its last note
records the largest assembly as 2033. It is 2111 today. The cut falls inside
the last sentence:

    ... read-only except typo3_feedback_record, which creates a

So a caller gets the write posture as half a sentence. `D-FBK-042` says one
word for both kinds of write reads as a hole in the posture. A cut mid-clause
is worse.

## Query

composer test, in a checkout of this server where install --agent=claude has published the skills

## Suggestion

Three ways out, and the measurement chooses between none of them.

Shorten the notice to what fits. It states one fact and one route in 175
characters.

Or give it the fallback the exclusion prefix has. `Coverage::instructions()`
measures the named form and takes a shorter one where the named form does not
fit. The directory notice has no such branch.

Or raise the budget. `D-ANS-004` fixed 2048 against a measured cut, so that way
needs a new measurement rather than a decision.

Measure the chosen way against a project with one client and against one with
several. The notice grows with each directory the record names.

The test is its own finding. It holds the base string on CI and the assembled
one on a developer's machine. So it passes where nobody installed anything.
