---
date: 2026-09-03T10:55:49+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# changelog rule says Important has no Impact section; shipped Important entries all have one

## Observation

Task: find an open impexp bug on forge.typo3.org and fix it; I ended up writing an Important changelog entry for Forge #108801.

The document core/contribution/changelog states, under "What a Changelog File Carries":

  "Every type has a Description section, every type except `Important` has an
  Impact section, and `Deprecation` and `Breaking` additionally have an Affected
  installations and a Migration section."

That is wrong for Important, and there are three mutually inconsistent accounts of what an Important entry contains:

1. The rule document: Description, no Impact.
2. Build/rstTemplates/rstTemplateImportant.rst (which the same document tells you to copy): Description, "Affected installations", "Migration" — no Impact.
3. What the core actually ships. Every Important entry I looked at in typo3/sysext/core/Documentation/Changelog/14.3.x/ uses Description + Impact. Verified in Important-109672-AllowDomainSyntaxInResourceOverrides.rst (Description, Impact, index) and consistent with the neighbouring Important-107032, Important-101699.

I noticed the contradiction only because I opened a neighbouring file to copy the headline-underline convention. Had I followed the rule document I would have written an entry with no Impact section; had I followed the shipped template I would have written "Affected installations" and "Migration". Neither matches the corpus. Build/Scripts/validateRstFiles.php does not catch it — it only checks the include, the reference label and the headline block, which the document also says ("Which of the four types the change owes is what neither reports"), so the wrong shape would have reached review.

I resolved it by following the neighbouring files and wrote Description + Impact.

## Query

typo3_rule_lookup documentId="core/contribution/changelog", then read typo3/sysext/core/Documentation/Changelog/14.3.x/Important-109672-AllowDomainSyntaxInResourceOverrides.rst and Build/rstTemplates/rstTemplateImportant.rst in a TYPO3 v15.0.0-dev core checkout

## Suggestion

Correct the sentence so Important is not the exception: in practice Description + Impact is what Important entries carry, and Deprecation/Breaking additionally carry "Affected installations" and "Migration". Better still, say explicitly that the three sources disagree and which one wins — the shipped corpus does, since that is what a reviewer compares against — and note that Build/rstTemplates/rstTemplateImportant.rst is stale relative to it, so a session that copies the template as the document advises does not silently produce the wrong shape. A one-line "read a neighbouring entry in the target directory for the section layout" would have settled it for me in the same call.
