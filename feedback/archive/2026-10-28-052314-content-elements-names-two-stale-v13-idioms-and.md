---
date: 2026-10-28T05:23:14+00:00
category: missing-knowledge
status: closed
closed: 2026-10-28
model: claude-opus-5
tool: typo3_hint_lookup, typo3-content-element-development
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# content-elements names two stale v13 idioms and omits the label one, so the list reads complete

## Observation

Task: add a slider content element to a sitepackage on TYPO3 14.3.

The content-elements hint warns about two stale pre-v14 idioms. It says addPlugin() dropped $field and $extensionKey. It says the list_type detour is gone. Both name what a pre-v14 memory gets wrong about this call.

I then wrote the showitem from that same pre-v14 memory. I labelled each tab with an LLL: reference into EXT:core locallang_general.xlf. Four of those keys do not exist on v14. They are LGL.general, LGL.appearance, LGL.access and LGL.visibility. The backend showed a tab with no name. Nothing reached the log. The user found it, not I.

No answer in this session named core.form.tabs. The translation-domain hint carries the domain form. No content element answer routes to it.

The two warnings are the mechanism. A hint that names which parts of my old knowledge are stale reads as the whole list. So I did not verify the rest. I read the correct form out of the core's own tt_content TCA afterwards.

The user reports that a model without this server wrote these labels correctly. I cannot verify that run. The asymmetry above stands without it.

## Query

typo3_hint_lookup ids=[content-elements, content-element-shape, extension-schema-sql, content-element-record-row] targetVersion=14, then ids=[tca-inline-children, page-content-element-rendering] targetVersion=14. Task: build a slider content element with inline child slides for a sitepackage on TYPO3 14.3.

## Suggestion

Name the label change in content-elements, beside the other two. On v14 a tab label is a translation domain key, for example core.form.tabs:appearance. The LGL.* keys of locallang_general.xlf are removed or retired. Route content-elements to translation-domain.

State in tca-showitem that an LLL: label which resolves to nothing renders an empty tab and logs nothing. That hint covers the absent label, which throws. The unresolvable label is the silent case.

Consider a rule for every such list. Say whether it is exhaustive. A partial list of stale idioms is read as a complete one, and then it costs more than no list.
