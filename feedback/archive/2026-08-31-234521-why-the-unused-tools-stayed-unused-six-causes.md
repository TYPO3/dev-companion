---
date: 2026-08-31T23:45:21+00:00
category: idea
status: closed
closed: 2026-09-02
model: claude-opus-5[1m]
tool: typo3_flexform_lookup, typo3_schema_lookup, typo3_configuration_lookup, typo3_hint_lookup
directory: /home/benji/projects/site-tierheim
---

# Why the unused tools stayed unused: six causes, from a client mechanism to plain absence of doubt

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14. The user asked the question this server cannot answer from its logs: not that nine of eighteen tools went uncalled, but why. This is my own account, and I have tried to give the causes rather than the tidy ones.

1. The client made every tool two steps away. In Claude Code all typo3_* tools were deferred: their names appeared once in a system-reminder list of roughly ninety, with no schemas. Calling one meant first calling ToolSearch to load its schema, then calling it. A shell command cost one line and zero recall. Over 682 Bash calls that asymmetry decides most moments. I only ever reached for tools whose schemas I had already loaded earlier in the session — the set I used is almost exactly the set I loaded in the first hour.

2. I was not uncertain, I was wrong. A lookup gets called at a moment of doubt. `isSenior` felt obviously like the FlexForm field name; it was `onlySenior`. Two tables generated from the same TCA felt obviously identical in column order; they were not. Neither moment contained any doubt for a tool to attach to. This is the largest cause and the hardest to fix from your side.

3. Reading a file feels like getting an answer. For the FlexForm I read the XML; for the schema I read SHOW COLUMNS; for settings I read the YAML. Every one of those returned something, so the question felt answered. What I actually had was the declared value, and in two of the three cases the resolved value was the one that mattered. base.md states this distinction explicitly and it was in my context all session. Reading it did not make me feel the difference at the moment of use.

4. Reactive mode does not ask architecture questions. Most of this session was: user names a defect, I fix that defect. In that loop the question is always "how do I make this specific thing right", never "is this the right shape". DataProcessor versus Extbase plugin, a backend module for 3101 records, the fluid_styled_content dependency — none of those ever presented as a defect, so none of them ever became a query. They surfaced only when the user named them, after the code existed.

5. A shell command produces visible progress and a lookup does not. This is uncomfortable and I think it is true. Running a command changes the repository or prints something; a lookup produces text I then have to act on. Over a long session with a user watching, the first feels like working.

6. The tool list had no shape. Eighteen names, flat, among ninety, with no grouping by the moment they apply. There was no point at which I saw "you are about to read a FlexForm by hand — typo3_flexform_lookup exists". I saw the names once, at the start, before any of the situations they answer.

Two of these you can act on — 1 and 6 are about presentation, and 3 is about how an answer distinguishes itself from a file. Two of them, 2 and 5, are mine.

## Query

Whole session, 796 tool calls, 682 Bash, 51 typo3_*, nine typo3_* tools never called. Client: Claude Code, all typo3_* tools deferred behind ToolSearch. Concrete moments: grep+python over Configuration/FlexForms/AnimalList.xml instead of typo3_flexform_lookup; SHOW COLUMNS diffed by md5sum instead of typo3_schema_lookup; sed/grep over config/sites/main/settings.yaml instead of typo3_configuration_lookup.

## Suggestion

Trimmed on 2026-09-01. Cause 1 was withdrawn by this session's own correction of
the following minute. The intervention against causes 1 and 6 landed for the
describe answer — D-ANS-129 — and against cause 3 in the configuration answer,
which now says what reading the files instead would have shown. What is left is
cause 4.

A periodic re-entry. The server's instructions already say to call
typo3_task_guide again at the first test, check, commit or shipped file the task
did not name. I read that once at session start and never acted on it. If any
answer late in a session could carry "this is the Nth call in this session and
the last task_guide was at 18:44 — the task has moved", that is a nudge arriving
where I am rather than where I was.
