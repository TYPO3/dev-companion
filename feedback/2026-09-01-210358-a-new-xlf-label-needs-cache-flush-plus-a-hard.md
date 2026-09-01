---
date: 2026-09-01T21:03:58+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_label_lookup, typo3_translation_domain_lookup, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# A new XLF label needs cache:flush plus a hard reload before the JS ~labels bundle serves it

## Observation

Migrating a local memory into the server: this is knowledge the server does not answer today, and it is adjacent to what typo3_label_lookup and typo3_translation_domain_lookup already cover.

When a new <trans-unit> is added to an XLF that backs a JS label bundle (~labels/<domain>, for example ~labels/core.core backed by locallang_core.xlf), the grunt build only regenerates the TYPE declarations under Build/types/labels/*.d.ts. So TypeScript compiles and every check passes, but at runtime LabelProvider.get('new.key') throws "Error: Label is not defined: new.key".

Reason: the ~labels/* module is generated server-side from the XLF by JavaScriptLanguageDomainProvider::createLanguageDomainResponse() and served with a Cache-Control max-age of one year, and the parsed XLF additionally sits in TYPO3's language cache.

Fix after adding any label consumed from JavaScript: run the installation's cache flush (bin/typo3 cache:flush, via ddev exec where the repository declares DDEV) to invalidate the language cache so the bundle regenerates, AND do a browser hard reload (Ctrl+Shift+R) to get past the one-year HTTP cache on the already-fetched label module. Doing only one of the two leaves the error in place, which is what makes it look like the label was never added.

## Query

Migrated from a local project memory file (reference_xlf_label_cache.md). Originally hit while adding a trans-unit to locallang_core.xlf that backend TypeScript then read.

## Suggestion

Attach this to typo3_label_lookup's answer whenever the label is being ADDED rather than looked up, and to the language-files hint. The searchable phrasing should carry the runtime symptom — "Label is not defined", "label added but JS does not see it", "~labels missing key" — because the reader arrives from the thrown error while the build was green. State both steps together and say explicitly that either one alone is not enough.
