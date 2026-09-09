---
date: 2026-09-09T18:07:41+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_changelog_lookup, typo3_commit_message_guide, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# Whether an intentional behaviour narrowing in a bugfix backport needs an Important entry

## Observation

Task: review Gerrit change 95482 and say what is missing before it can merge. The longest-running question of the session was not code — it was this changelog judgement, and I answered it from the repository's AGENTS.md plus grepping Changelog/14.x, with no authority to point at.

The situation: the change removes an intentional but inconsistent behaviour — a project root package's Initialisation/Files and ext_tables_static+adt.sql are no longer set up during installation. It targets `Releases: 14.3, main`. Its own commit message says "This may technically be breaking for installations relying on configuration resources from the project root package during setup". It ships no RST. The typo3/app package and its config/system/resources.php are documented features (Feature-107537 in 14.0, Feature-109409 in 14.2), so the surface is not undocumented.

What I did: derived from AGENTS.md that Breaking is not allowed on a release branch and that Important is therefore the vehicle; grepped Changelog/*/ for existing typo3/app entries to see how the area had been documented before; wrote a functional test proving the behaviour changed (green on the patch, red on its parent). I raised it as the one open point.

The reviewer pushed back — "aber das kann doch gewollt sein oder?" — and was largely right. I could show the behaviour changed; I could not show any real installation is affected, because in the constellation the bug is actually about (the Composer root is itself an extension) the data is still imported under the extension's own key. I withdrew it. That whole exchange, three turns, was a judgement call I had no reference for.

The gap is not the RST file format, which AGENTS.md covers well (naming, sections per type, the underline rule, the extension-scanner link). The gap is the decision: intended vs. unsupported vs. observable, and how a release-branch target constrains the type.

## Query

A bugfix with "Releases: 14.3, main" removes a behaviour its own commit message calls "technically breaking" (a Composer root package's Initialisation/Files and static SQL are no longer set up during installation), and ships no RST. Does it need a changelog entry, and of which type — given that Breaking is not permitted on a release branch?

## Suggestion

An answer for "does this change need a changelog entry, and which type" that gives criteria rather than format. It would have to cover: that an intentional narrowing still gets documented when it is observable, and does not when the affected constellation is unreachable or was never a supported pattern; that Breaking is unavailable on a release branch so a backported behaviour change lands as Important or as nothing; and two or three worked precedents — a narrowing that got an Important, a narrowing that got nothing, and what separated them.

typo3_changelog_lookup as named reads like it retrieves existing entries by version or keyword. If it does only that, the judgement call is uncovered; if it can answer "was this kind of change documented before, and how", the description should say so in those words.
