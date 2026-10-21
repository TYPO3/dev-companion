---
date: 2026-10-21T15:31:39+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# Path query for a Fluid ViewHelper returns content-element and TCA hints and a 140-entry index

## Observation

Task: review a patch to SelectViewHelper.php and SelectViewHelperTest.php. typo3_hint_lookup with these two paths returned fluid-viewhelpers, system-extension-boundaries and core-tests, which fit. It also returned content-element-shape and tca-formengine, which do not apply to a form ViewHelper. The answer also carried the full availableHints index, about 140 entries. That index was most of the answer size. bestCoverage was 0.42.

## Query

typo3_hint_lookup(paths=["typo3/sysext/fluid/Classes/ViewHelpers/Form/SelectViewHelper.php", "typo3/sysext/fluid/Tests/Functional/ViewHelpers/Form/SelectViewHelperTest.php"], task="form ViewHelper name attribute for collection property")

## Suggestion

Drop hints below a relevance threshold for the given paths. Give the availableHints index only on request, or limit it to the ids of the same categories. For ViewHelpers/Form paths, offer extbase-arguments, because form field names decide what Extbase maps.
