---
date: 2026-08-31T23:47:45+00:00
category: idea
status: closed
closed: 2026-09-02
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_project_describe
directory: /home/benji/projects/site-tierheim
---

# Told four times that code is English, I translated everything, then wrote the next new file in Ge...

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14. The project convention, stated by the user and never in doubt: all code is English — identifiers, comments, labels, XLF source. Filed at his request because this is the failure that repeated most times in one session, and the last repetition is still in the repository as I write this.

He said it at 21:55:10 ("ich habe auch schon wieder deutsche labels im backend obwohl alles english first sein muss"), 22:36:44 ("alle code kommentare müssen auch immer englisch sein"), 22:47:24 ("alles ist im code auf englsich"), 22:48:17 ("alles muss auf englsich sein, auch alle felder") and 23:15:09 ("die readme ist auch noch auf deutsch"). Note "wieder" in the first one — it had already happened before the part of the session I can see.

I ran four bulk translation passes in response: comments across 35 files, PHP identifiers and constants, console output and command descriptions, test names, then the whole README rewritten in English.

Then, between 23:05 and 23:10 — roughly fifteen minutes after the last of those passes — I wrote a new file, Build/tests/browser/frontend/navigation.spec.ts, and put German in it. Still there now:

  line 61   `${via}: nicht genau eine H1`
  line 72   'Die Hauptnavigation ist leer'
  line 74   'Ein Menüpunkt ohne Beschriftung'
  line 98   `"${entry.name}" führt woandershin`
  line 135  `"${via}" führt woandershin`
  line 143  'Kein einziger Untermenüpunkt geprüft'
  line 174  'Der erste Abschnitt hat keine Unterseiten.'
  line 226  'Im Ausklappmenü stehen die Unterpunkte ohnehin offen.'

Plus survivors my identifier pass missed because they were not in my substitution list: `werte` in e2e/backend.spec.ts with two German assertion messages beside it, and German skip reasons in interaction.spec.ts and readability.spec.ts.

And while those files sat in the tree I wrote a memory note for myself titled "All code is English."

The lesson is about the shape of the correction, not the correction itself. I treated a convention as a cleanup task: run a substitution over what exists, verify the substitution, report it done. A cleanup does not change what the next file starts from. The next file started from my default, which is to write explanatory strings in the language of the conversation. Nothing in a bulk pass touches that, and the pass reporting success is what made it feel settled.

Honest scope note for whoever reads this: most of this is model behaviour and not a gap in your knowledge base. I am recording it here because it is the pattern that cost this session the most rework, and because there is a small piece that is yours.

## Query

Whole session. Convention stated at 21:55, 22:36, 22:47, 22:48, 23:15. Bulk translation passes over packages/**, Build/tests/**, README.md. New file written afterwards with German strings: Build/tests/browser/frontend/navigation.spec.ts (lines 61, 72, 74, 98, 135, 143, 174, 226). Survivors of the identifier pass: `werte` and two German assertion messages in Build/tests/browser/e2e/backend.spec.ts:98,101; German skip reasons in interaction.spec.ts:4 and readability.spec.ts:48.

## Suggestion

Trimmed on 2026-09-01. The framing at the end of this report landed as a
statement in verifying-a-change-against-the-installation: a convention is a
precondition on writing rather than a pass over what is written, and what the
existing files of a kind already do is one search away. What is left is the
piece this report calls yours, and it is open rather than answered.

The piece that is yours is small and cheap: let typo3_project_describe report
the project's own code language where the repository states it, and say so in
the answer. An .editorconfig, a CONTRIBUTING, a README line, the source language
declared in the XLF files it already reads — this project's unprefixed XLF files
declare source-language="en" and the describe answer already reports that per
language file. Turning that into one sentence at the top — "this project's XLF
sources declare English; treat English as the code language unless the
repository says otherwise" — puts the convention in front of a caller at the
moment they are being oriented, rather than leaving it to be discovered through
corrections.
