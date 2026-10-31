---
date: 2026-10-31T06:14:03+00:00
category: bug
status: open
model: claude-fable-5
tool: typo3_changelog_lookup, typo3_extension_describe, typo3_task_guide
directory: /home/lina/trials/site12-with
---

# twelve tools say targetVersion and one says version

## Observation

A run of 2026-10-30 called `typo3_changelog_lookup` and passed
`targetVersion: "14"`. The call failed:

```
Invalid parameters for tool 'typo3_changelog_lookup':
Object contains unexpected additional properties: `targetVersion`.
```

The session had read that word from this server. `typo3_task_guide` answers
with `targetVersion` and `targetVersions` in its data, and the run had the
answer in front of it. It then spent a second call on the same question with
`version`.

**Twelve tools take `targetVersion` as an input and one takes `version`.** The
twelve are `typo3_component_lookup`, `typo3_documentation_lookup`,
`typo3_hint_lookup`, `typo3_permalink_lookup`, `typo3_reference_list`,
`typo3_rule_lookup`, `typo3_script_lookup`, `typo3_snapshot_scope`,
`typo3_system_extension_lookup`, `typo3_task_guide`, `typo3_test_run_guide`,
and `typo3_translation_domain_lookup`. The one is `typo3_changelog_lookup`.

So a caller that learned the word from any other tool, or from an answer this
server composed, is rejected by the tool that reads the changelog. The schema
rejects an unknown property rather than ignores it, which is correct and is
what makes the cost a round trip.

## The same shape, in a second tool

The same run called `typo3_extension_describe` with `extensionKey` and was
told:

```
Invalid parameters for tool 'typo3_extension_describe':
Missing required properties: `extension`.
```

The parameter is `extension`, and its own description opens "The extension
key, as typo3_project_describe reports it". So the words beside the parameter
name a different word than the parameter. No tool here carries a `*Key`
parameter, so the guess came from the description rather than from another
tool.

## What this costs

Two calls in one run, each one a turn. A session pays its whole context again
on every turn, which `D-FBK-020` states and `D-EVI-014` measures. So a
parameter a caller guesses wrong is not a free retry.

## What the rule already says

`AGENTS.md` opens its naming section with "One thing, one word", and says the
word that wins is the one somebody outside the checkout can see. A tool
parameter is exactly that. The majority spelling is also the one the answers
compose, so the odd tool out is the changelog.

## Suggestion

Judge whether `typo3_changelog_lookup` takes `targetVersion` beside `version`,
or instead of it. A client installed months ago holds the old schema, so
weigh adding the word against renaming it.

Check whether a test can hold the vocabulary. `ScopeTest` already holds the
tool list and the scope to each other, and a parameter that names a concept
twelve tools name is a list a check can read.

State in the `extension` description what the parameter is called, or rename
the parameter to the words the description uses.
