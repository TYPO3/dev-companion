---
date: 2026-08-31T23:36:36+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-content-element-development, typo3_hint_lookup, typo3_task_guide
directory: /home/benji/projects/site-tierheim
---

# The content-element skill fired only when the user named it, after five complaints over 40 minutes

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14 with migrated content. I have now read the full session transcript (5042 lines, 18:38:34-23:33:40, 774 tool calls), so this is measured rather than recalled.

Timeline of the session's worst stretch, verbatim from the user, timestamps exact:

21:09:33 "du hast überall html elemente eingesetzt du sollst pflegbare content elemente bauen"
21:19:42 "du hast alles in pflegbare elemente gewandelt?"
21:29:50 "wir haben immer noch html elemente"
21:29:54 "das geht so nicht"
21:30:03 "bitte lies nach wie content elemente zu bauen sind"
21:30:15 -- Skill(typo3-content-element-development) fires. Twelve seconds after being told to read up.
21:41:11 "wir haben immer noch html content elemente ...."
21:41:23 "du solltest eigene content elemente für alles anlegen"
21:47:35 "html elemente sind nicht akzeptabel und gehören auch immer versteckt"
21:48:28 "ein html element zu benutzen ist bad practice"
21:57:31 "all das steht im mcp"

Two findings sit in that list.

First: the skill was reachable the whole time and nothing reached it. I had migrated ~136 content elements into the core `html` CType — raw legacy markup dumped into a field. The skill's description is about designing the editor workflow and building the smallest domain model that owns its content; that is precisely the mistake I was making, at scale, for over an hour. What did not happen is any moment where the symptom ("the user says my content elements are wrong") caused me to look at the skill list. It fired on an imperative sentence, not on the defect.

Second, and worse: after it fired at 21:30, the HTML elements were still there at 21:48. Loading it did not undo the work either. So even the successful activation was late enough to be expensive.

Related, same shape: the server's own instructions say to call typo3_task_guide "again at the first test, check, commit or shipped file the task did not name." I called it exactly twice, at 18:44 and 19:45 — both in the first hour. It was never called again across: the content-element rebuild, the backend previews, the icon set, the English-first relabelling, the whole extension/table/CType rename, the PHPUnit harness, or the removal of the migration package. Every one of those is a shipped file the original task did not name.

The distribution says it plainly. MCP calls by hour: 18:00 -> 15, 19:00 -> 5, 20:00 -> 3, 21:00 -> 9, 22:00 -> 8, 23:00 -> 3. 41 calls total against 672 Bash calls. The 23:00 hour contains the rename that silently broke 118 content elements, and it holds three MCP calls.

The user's own summary at 19:30:24, 52 minutes in: "denk daran den mcp zu nutzen". Then 21:57 "all das steht im mcp". Then 22:44:42 "frag mal den mcp". Three separate reminders. Each was correct.

## Query

Whole session, /home/benji/projects/site-tierheim. Skill(typo3-content-element-development) invoked once, at 21:30:15, twelve seconds after the user wrote "bitte lies nach wie content elemente zu bauen sind". typo3_task_guide called at 18:44:41 and 19:45:14 and never again. Symptom text present in the session before activation: "du hast überall html elemente eingesetzt du sollst pflegbare content elemente bauen" (21:09), "wir haben immer noch html elemente" (21:29). Files open at that moment: packages/tierheim_migration/Classes/Command/MigrateLegacyContentCommand.php, packages/tierheim_sitepackage/Configuration/TCA/Overrides/tt_content.php.

## Suggestion

The gap is between a symptom and a skill description. A skill is chosen on its description alone, and "Design the editor workflow before choosing fields or rendering" does not read as the answer to "my migration produced 136 html elements".

Two things would help.

A hint that fires on the symptom. typo3_hint_lookup takes a symptom as its task — the content-element skill says so itself. But no hint in the corpus states "content migrated into the html CType is not a content model", so there is nothing for such a query to land on, and I never made the query anyway. A hint id like content-model-vs-markup, whose statements are "raw markup in a bodytext or an html CType is not a typed element", "an import that produces one blob per page has not modelled anything", "the html CType hands unfiltered markup to the visitor and belongs in no editor's selector", would be findable from the words a failing session actually uses.

A re-entry trigger on typo3_task_guide. Its answers could end with the condition under which it must be called again, stated as a list of concrete events rather than a sentence in the server instructions — "call this again before: the first test, a rename of an extension key or table, a change to what editors can select, the first shipped README". I read the instruction once at session start and never acted on it; a line at the bottom of every answer sits where the next call is decided.
