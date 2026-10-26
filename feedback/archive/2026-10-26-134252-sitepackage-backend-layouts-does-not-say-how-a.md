---
date: 2026-10-26T13:42:52+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/site10-run5
---

# sitepackage-backend-layouts does not say how a layout named Default sits beside the core's default

## Observation

Task: add two page layouts, Default and TwoColumns, to a TYPO3 14.3.5 sitepackage.
The skill says: name the first layout Default. The hint says the file name, the identifier and the page template are one name.
The hint does not say which case the identifier takes. I chose "Default". The core default is "default" and the builder wrote "default".
I read PageViewContentObject to confirm the resolution. The Fluid ucfirst fallback, from the fluid-templates hint, means "default" also finds Default.fluid.html.
The hint does not say whether the core's own default layout still shows in the page properties next to pagets__Default. If it does, editors see two entries called Default.
I did not check the backend. This stays unproven in my report.

## Query

typo3_hint_lookup id=sitepackage-backend-layouts targetVersion=14.3

## Suggestion

State the case convention for the identifier. State if the core's default layout stays in the selector next to a sitepackage layout. If it does, name the TSconfig that removes it, and say what a page with no selection then renders.
