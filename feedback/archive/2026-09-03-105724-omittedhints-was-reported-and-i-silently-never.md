---
date: 2026-09-03T10:57:24+00:00
category: idea
status: closed
closed: 2026-09-04
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# omittedHints was reported and I silently never fetched it; nothing downstream noticed

## Observation

Task: find an open impexp bug on forge.typo3.org and fix it.

typo3_task_guide returned four hint groups inline and reported one it had left out, as structured data: omittedHints: [system-extension-boundaries]. The base.md order this server ships is explicit that those are owed — "One that stopped short says that instead and names the ids it left, and those are what is owed: fetch them by id rather than repeating the query."

I never fetched it. Not a decision — I read the four inline hints, moved on to the code, and the id fell out of my working set. Nothing downstream flagged it: I ran typo3_task_guide a second time later with the final paths, and made 17 further MCP calls, three skills and roughly 100 tool calls without the gap resurfacing.

This matters here more than usual, because the patch crossed exactly that boundary. It began as an impexp bug and the fix landed in typo3/sysext/core/Classes/Database/ReferenceIndex.php — a core class consumed by ext:backend, ext:install, ext:linkvalidator and ext:redirects. "System Extension Boundaries" is plausibly the one hint that speaks to whether that relocation was the right call. I reasoned about it myself (the defect is in the core class; impexp is only where it is visible) and I still think that is right, but I reached it without the guidance that was offered and declined by accident.

The mechanism is sound and the reporting is honest — the server did its part by naming the id. The failure is that a named-but-unfetched hint is indistinguishable, from the server's side, from one that was fetched and found irrelevant. It sees the calls that were made and nothing else, which is why I am reporting it rather than leaving it invisible.

## Query

typo3_task_guide(task="Fix impexp export/import ignoring IRRE relations declared in TCA columnsOverrides", paths=["typo3/sysext/impexp/Classes/Export.php","typo3/sysext/impexp/Classes/Import.php","typo3/sysext/impexp/Tests/Functional"], targetVersion="15", changeType="bugfix") — answer carried omittedHints: [{id: "system-extension-boundaries", title: "System Extension Boundaries", category: "PHP"}]

## Suggestion

Two options, in order of cheapness.

Make the omission harder to drop in the answer itself: rather than a structured omittedHints array alongside a populated hints key, put a sentence at the top of the brief naming the count and the ids — "1 hint not carried: system-extension-boundaries; fetch with typo3_hint_lookup(id=...)". Structured metadata below a large payload is what gets skimmed; a line above it is not.

Better: have a second typo3_task_guide call in the same session notice that a previously omitted id was never fetched and repeat the notice more loudly. My second call had the same omission latent and said nothing about it, which was the last chance to catch it.

Worth considering separately: system-extension-boundaries looks like it should be surfaced inline, not omitted, whenever the paths span more than one sysext or the change type is a bugfix whose paths sit in one sysext but whose likely fix site is core — which is a common shape for issues filed under a consumer extension's category.
