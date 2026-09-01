---
date: 2026-08-31T23:41:31+00:00
category: idea
status: closed
closed: 2026-09-01
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3_hint_lookup, typo3_changelog_lookup, typo3_documentation_lookup
directory: /home/benji/projects/site-tierheim
---

# What held: fetching hints by id, the phpunit guide end to end, and an empty answer that explained...

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14. Filed so the parts that worked are not lost in a session mostly worth reporting for its failures. All measured from the full transcript.

1. typo3_rule_lookup(documentId="extension/testing/phpunit") carried the procedure end to end and needed nothing after it. Both XML files verbatim, the per-major variants with the note that which one applies is not a choice, what a copy has to correct and why (testsuite path relative to the config file; bootstrap referenced in vendor rather than copied, because the two bootstraps hold nothing an extension configures), how to run them, the database credentials by name, and what a finished run leaves behind. I copied the shipped files and adapted exactly those two attributes. The suite came up on the second try, and the one failure was mine — a missing coreExtensionsToLoad — not the guide's.

2. Fetching hints by id worked every single time. At 21:57 the user wrote "all das steht im mcp" and I fetched language-files, content-elements, content-element-preview and content-element-shape by id in sequence. content-element-preview is where `{record}` rather than `{data}` came from, which is the fact that had cost the session several turns of guessing. The availableHints index that rides along with every matched answer is what made that possible — without it those ids are unguessable. Whatever else changes, that index earns its size.

3. An empty answer that explained itself. typo3_changelog_lookup(query="searchFields TCA ctrl", version="14") returned matchCount 0 — but with termCounts showing searchfields matched 2 on its own, tca 30, ctrl 1, and termSubsets showing the pairs. I read that, dropped the extra terms, and the next call returned Breaking #106972 "TCA control option searchFields removed". Two calls, one wasted, and the wasted one told me exactly how to fix it. That is the right behaviour for an empty result and most tools do not do it.

4. typo3_hint_lookup(id=installation-boot) states that database:updateschema is not a core command and belongs to typo3-console. I had already run it and got "There are no commands defined in the database namespace"; the hint turned a confusing failure into a settled fact in one call rather than a hunt.

5. The project-extension-tests hint carried the one operational detail that had already cost me a finding: `ddev composer <name>` swallows a failing script's stdout and returns only ddev's own error, while `ddev exec composer <name>` gives both streams. I had lost a php-cs-fixer diff to exactly that an hour earlier and blamed the tool. Every declared script in the project README now says `ddev exec`.

6. typo3_documentation_lookup with page= returned the TCEFORM chapter whole, including keepItems with its type and its levels. It replaced a TCA manipulation I had written, defended in a README, and would have shipped.

7. typo3_icon_lookup(identifiers=[...]) on my seven own identifiers confirmed registration resolved, with the EXT: source path per icon — which is a different fact from "the SVG files exist" and the one that mattered.

## Query

typo3_rule_lookup(documentId="extension/testing/phpunit"); typo3_hint_lookup(id="content-element-preview"|"content-element-shape"|"content-elements"|"language-files"|"installation-boot", targetVersion="14"); typo3_changelog_lookup(query="searchFields TCA ctrl", version="14") -> 0 with termCounts, then typo3_changelog_lookup(query="searchFields", limit=6) -> Breaking 106972; typo3_documentation_lookup(page="https://docs.typo3.org/m/typo3/reference-typoscript/14.3/en-us/PageTsconfig/TceForm.html"); typo3_icon_lookup(identifiers=[seven own content-* identifiers]).

## Suggestion

Keep all seven. Specifically, do not trade away:

- The availableHints index on matched answers. It is large and it looks like overhead until the moment a caller is told "it is in the mcp" and has to find the entry by name. That is the recovery path and it is the only one.
- termCounts and termSubsets on an empty changelog answer. An empty result that says which of its terms matched what is self-repairing; a bare zero sends the caller to a different tool or to the web.
- The phpunit guide's completeness, including the parts that look redundant — the per-major XML duplication and the "what is left after a run" section. Both were load-bearing.
- The EXT: source path in icon validation.

The one change I would make to what worked: the phpunit guide assumes the package is the Composer root, so its `../Tests/Unit/` path is wrong in a project with packages/* and vendor/ at the root. I worked out `../packages/<ext>/Tests/Functional/` myself. One sentence naming the project layout as the other case would close it.
