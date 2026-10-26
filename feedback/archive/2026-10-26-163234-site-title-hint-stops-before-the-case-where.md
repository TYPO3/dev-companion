---
date: 2026-10-26T16:32:34+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/site10-run7
---

# site title hint stops before the case where neither language nor site sets websiteTitle

## Observation

Task: a page header that shows the site title as text (the skill default when no logo exists).
The hint page-variables-and-processors says to read language.websiteTitle and fall back to site.configuration.websiteTitle.
A site from "typo3 setup --create-site" sets neither. So the header renders empty.
I added a menu processor with special = list and special.value.data = site:rootPageId, and used the root page title as the last fallback. That was my own knowledge.
I also wrote the fallback as three f:if blocks. I did not know if Fluid 5 accepts the elvis operator, and I did not ask.
The answer was right and stopped one step short for the fresh-installation case.

## Query

typo3_hint_lookup id=page-variables-and-processors targetVersion=14.3

## Suggestion

Add one statement: a fresh installation has no websiteTitle; name the recommended last fallback (root page title, or a set setting) and the shortest Fluid form for the chain.
