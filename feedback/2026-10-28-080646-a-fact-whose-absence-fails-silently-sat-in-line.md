---
date: 2026-10-28T08:06:46+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# a fact whose absence fails silently sat in line 41 of a 51 line answer, and the reader stopped at 30

## Observation

Task: translate part of a sitepackage site into Swahili, TYPO3 14.3.

I called typo3_hint_lookup for language-files, translation-domain and tca-showitem. The answer was 51 lines. I displayed the first 30 and worked from those.

Three facts I needed sat between line 31 and line 51. The locale-prefixed file convention, de.locallang.xlf beside locallang.xlf. LANG/resourceOverrides, which is how another extension's labels are translated. And that a locale-prefixed file without target-language is read as a default-language template, so every translation in it is discarded with nothing logged.

So I wrote the chrome without labels, left the strings of indexed_search in English, and reached the override mechanism only after the user named each failure.

The third of those is the one that matters. Its absence fails silently: the file is valid XLIFF, nothing is raised or logged, and the symptom is a maintained translation rendering in English. It sits in the middle of a long list, in a hint whose headline subject is naming and reusing label identifiers.

The model cannot be changed, and more models will be run against this server. A reader that stops early is a property of the readers rather than a defect of one of them.

## Query

typo3_hint_lookup ids=[tca-showitem, translation-domain, language-files] targetVersion=14. The response was 51 lines; the session read lines 1 to 30.

## Suggestion

Put a fact whose absence fails silently where a reader who stops early still meets it. The target-language trap is the case: first statement of its hint, or a subject of its own.

Consider what a long answer owes. language-files returned 51 lines across three ids, and its load-bearing statements were at the end. An answer could name its own silent-failure statements at the top, before the list that explains them.

Reports carry the model that filed them. A hint several models each read past is too long or badly ordered, and that signal is in the queue already.
