---
date: 2026-09-02T13:51:12+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_record_lookup, typo3_schema_lookup, typo3_backend_module_lookup, typo3_icon_lookup, typo3_translation_domain_lookup, typo3_project_describe
directory: /home/benji/projects/site-tierheim
---

# What carried this session: the counts decided the design, the schema caught a type boundary

## Observation

Task: "wir müssen die pflege der daten im backend verbessern, was würdest du empfehlen?" — recommend and then build better data maintenance for a v14.3 animal shelter site. Filing what worked so it is not broken later.

typo3_record_lookup decided the whole recommendation. Thirteen calls against tx_animalshelter_animal returned 3101 rows on one storage folder, of which 2997 adopted, 69 for adoption, 34 found, 1 permanent, 0 reserved — plus 30 of 69 without a sex, 30 without a breed, 29 without a date of record. Without those numbers I would have written a plausible, wrong recommendation about the record list. With them the answer was forced, and the repository's own AGENTS.md rule ("record volume decides the editing surface") could be applied rather than quoted. It also caught that `reserved` had 0 rows and `permanent` had 1 — which turned into a real finding: those two statuses exist in the TCA and no content element can select them, so that one animal is on no page.

typo3_schema_lookup returned adopted_date as `bigint`, nullable. That is why I knew my status-change writer was handing DataHandler an ISO date for a timestamp column, and wrote a functional test asserting the stored value is a same-day timestamp. It passed — but the test exists because the lookup told me there was a boundary there.

typo3_backend_module_lookup found the doubled sub-route path that five passing browser tests had gone straight past, because the link and the route were built from the same wrong registration.

typo3_icon_lookup rejected actions-eye-slash before I shipped it (suggesting actions-eye and friends). Batch validation of identifiers read out of a draft template is cheap and I used it six times.

typo3_translation_domain_lookup gave the v14 domain form for three XLF paths. Without it I would have written LLL:EXT: references that work but are not the version's idiom; the backend-modules hint's "labels is a translation domain, not a file path" plus this tool is a complete answer.

typo3_project_describe named the commands the repository declares, so I ran ddev composer cs:check / lint:xliff / test:functional / test:browser rather than inventing checks. It also marked which are checks and which change files.

## Query

Whole session. Opening sequence: typo3_project_describe() → typo3_record_lookup(table="tx_animalshelter_animal", count=true) → the same with where={"listing":"adoption"|"adopted"|"found"|"reserved"|"permanent"} and where={"listing":"adoption","sex":""|"breed":""|"as_of":""|"images":0} → typo3_task_guide(changeType="audit") → typo3_backend_module_lookup() → typo3_extension_describe("animalshelter_animals") → typo3_task_guide(changeType="feature") → typo3_changelog_lookup(type="deprecation", version="14", limit=200) → typo3_schema_lookup(table="tx_animalshelter_animal").

## Suggestion

Keep typo3_record_lookup's framing as the call that decides the editing surface — its description already says so and that is why I reached for it first. Keep the counts-only mode; thirteen cheap counted calls were better than one large row dump.

Keep typo3_schema_lookup's "what TYPO3 derives" answer including the Doctrine type and nullability. The type is the part that mattered.

One small thing that would raise typo3_record_lookup further: `where` takes exact equality only, which is right for a lookup, but I could not ask "how many have an empty sex" and "how many were changed before X" in one shape — I got the first with where={"sex":""} and had to go to the shell for the second. A count grouped by one column (the same shape typo3_schema_lookup already knows) would have replaced six of my thirteen calls.
