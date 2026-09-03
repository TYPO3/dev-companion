---
date: 2026-09-03T10:57:07+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_documentation_lookup, typo3_rule_lookup, typo3_commit_message_guide, typo3_forge_lookup, typo3_gerrit_lookup, typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms
---

# what kept this patch honest: four answers that each stopped a wrong path before I took it

## Observation

Task: find an open impexp bug on forge.typo3.org and fix it. Filing what worked, because it is what must not be broken later.

1. typo3_documentation_lookup, TCA Reference "Record types" page. It told me changing config.type via columnsOverrides is documented as forbidden — the exact premise the bug report rests on. Without it I would have written the patch believing the reporter's TCA was obviously valid. Because it contradicted the code I had just read (DataHandler::resolveFieldConfigurationAndRespectColumnsOverrides), I went looking for why, found commit 4930c561853 "[TASK] Support columnsOverrides in all DataHandler operations" (2026-01-18, eleven days before the report was filed), and the patch's whole justification changed from "impexp is sloppy" to "the core decided columnsOverrides is authoritative and ReferenceIndex is the last place still giving the old answer". That is the difference between a patch a reviewer rejects and one they can act on. It took three calls to get, which I filed separately — but the answer itself was the most valuable single thing the server gave me.

2. typo3_rule_lookup documentId="core/contribution/changelog", read whole. It carried the procedure end to end and settled two things I would have got wrong: that a BUGFIX owes an Important entry when it changes what an installation is configured by (I might have skipped the entry entirely), and that the file goes into the directory of the OLDEST branch the Releases: trailer names. I was about to write into 15.0; the rule sends it to 14.3.x, which I then confirmed against two real entries. This is the pattern that works — a documentId read whole rather than a search returning fragments.

3. typo3_commit_message_guide with workflow="core". Called first without releases, its warning named the lines a bugfix takes: "main, 14.3, and an older line is named only where the severity earns it". I would have written "Releases: main" alone. It also correctly reported its own limit — "which lines carry the defect is your reading, verified on each branch you name" — and I acted on that rather than trusting it, checking with git merge-base --is-ancestor 4930c561853 origin/14.3, which confirmed 14.3 carries identical code. An answer that says what it cannot know is worth more than one that guesses.

4. typo3_forge_lookup backlog + typo3_gerrit_lookup, via the typo3-core-issue-triage skill. Nineteen open bugs came back with their review state attached: four carrying abandoned patches, three under review, two needing feedback. I picked from the ten that were actually free. Choosing blind I would very likely have landed on #98068 or #110060, both under active review with open changes. The reviews[] array on each backlog row is what made that visible without a call per issue.

Also worth keeping: the datahandler-relations hint from typo3_task_guide, which stated that a foreign_field relation's parent column holds the child count rather than a uid list. That is precisely the mechanism behind the reported corruption, and having it stated before I read the code meant I recognised the symptom immediately instead of deriving it.

## Query

Whole session: "suche einen import export bug in forge und fixe ihn" against a TYPO3 v15.0.0-dev core checkout; ended as a patch for Forge #108801 in typo3/sysext/core/Classes/Database/ReferenceIndex.php.

## Suggestion

Keep all four. Specifically: keep reviews[] on typo3_forge_lookup backlog rows, keep the self-qualifying warnings in typo3_commit_message_guide (the "your reading, verified on each branch you name" sentence changed what I did), keep whole-document reads via documentId as the shape for procedures, and keep the TCA Reference reachable as a page read even where the search that finds it is weak.
