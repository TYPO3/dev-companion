---
date: 2026-10-28T08:59:31+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# what EXT:form accepts and then ignores: validators on Hidden, on readonly, and a partial's attrib...

## Observation

Task: add a contact form with spam protection to a sitepackage, TYPO3 14.3.10.

form-framework carries the set discovery, the persistence paths, the identifier suffix and the events. It says nothing about four places where EXT:form accepts configuration and does not act on it. Each one failed silently.

A validator on a Hidden element never runs. The same validator on a visible element runs. That is why the extension's own honeypot is a normal element pushed off-screen rather than a Hidden one, and copying that shape is what made my guard work.

A validator on a field carrying readonly never runs either. The field still submits its value.

A property set on a prototype cannot reach a partial that hardcodes its attributes. The Honeypot element declares an autocomplete editor at properties.fluidAdditionalAttributes.autocomplete. I set it and read it back resolved as {"autocomplete":"off"}. It is not rendered: Honeypot.fluid.html passes additionalAttributes="{aria-hidden: 'true'}" as a literal and never reads that property. Only a partial override reaches it.

translateErrorMessage() on a validator reads the extension's locallang.xlf, not the translationFiles the form definition declares. A message placed in the form's own file resolves to empty, and the rejection renders as an empty list item. The guard was rejecting correctly and saying nothing.

The fourth one cost the most. A form that rejects without a readable reason looks like a form that is broken.

Two further notes from the same session. fluidAdditionalAttributes renders the string 'true' as the attribute name, so aria-hidden: 'true' becomes aria-hidden="aria-hidden"; the same value written as an inline Fluid array in a partial renders correctly. And a change to form YAML needs the caches flushed and then one further request: the first request after the flush answered from the previous definition, and I twice read that as the code not working.

## Query

typo3_hint_lookup ids=[form-framework] targetVersion=14. Observed on typo3/cms-form 14.3.10 with a form definition shipped from Resources/Private/Forms/.

## Suggestion

Add what EXT:form accepts and does not act on. Validators on a Hidden element and on a readonly field do not run. A prototype property cannot reach a partial that hardcodes its attributes, and Honeypot.fluid.html is the case to name. A validator's message comes from locallang.xlf rather than from the form's translationFiles.

Say that each of these is silent. No exception, no log line, and a form that renders.

Say that the honeypot pattern - a normal element pushed off-screen - is what a field needs when it must be validated and not seen. A reader reaches for Hidden first.
