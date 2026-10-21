---
date: 2026-10-21T15:31:39+00:00
category: missing-knowledge
status: closed
closed: 2026-10-21
model: claude-opus-5-5
tool: typo3_task_guide, typo3_hint_lookup, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# No hint says trusted properties decide whether a converted child is allowed to map

## Observation

Task: fix Forge #79571, a single f:form.select bound to an ObjectStorage loses its value. My first fix plan changed ObjectStorageConverter to map a non-empty string to one child. That plan was wrong. In a real request, MvcPropertyMappingConfigurationService builds the mapping configuration from the trusted properties. A single select registers its field as a scalar ("categories":1). So the property is allowed, but no child of it. The mapper then throws 1355155913 "It is not allowed to map property 0". I found this only because a throwaway test failed with that message, and then I read MvcPropertyMappingConfigurationService in the checkout. typo3_task_guide with the converter path returned system-extension-boundaries and core-tests. It did not return extbase-arguments ("What Arrives From a Request, and What Silently Does Not"). That hint showed only in the availableHints index of a later call, and I never fetched it. I do not know if it covers this rule.

## Query

typo3_task_guide(task="Map a non-empty scalar source to a one-element ObjectStorage in ObjectStorageConverter so a single select bound to an ObjectStorage keeps its selection", changeType="bugfix", paths=["typo3/sysext/extbase/Classes/Property/TypeConverter/ObjectStorageConverter.php", "typo3/sysext/extbase/Tests/Functional/Property/TypeConverter/ObjectStorageConverterTest.php"])

## Suggestion

Match a hint to paths under extbase/Classes/Property/ and fluid/Classes/ViewHelpers/Form/. The hint should say: a form field name with [] registers a list in the trusted properties, and a name without [] registers a scalar. A type converter that creates children from a scalar source fails in a real request, because the trusted properties allow no child index. A test that builds its own PropertyMappingConfiguration does not show this.
