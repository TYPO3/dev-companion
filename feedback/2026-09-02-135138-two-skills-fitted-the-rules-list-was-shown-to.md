---
date: 2026-09-02T13:51:38+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-backend-module-development, typo3-extension-documentation, typo3_rule_lookup, typo3_label_lookup, typo3_flexform_lookup, typo3_documentation_lookup, typo3_server_scope
directory: /home/benji/projects/site-tierheim
---

# Two skills fitted; the rules list was shown to me and I read none of it

## Observation

Task: same session. Reporting which skills activated, and — more usefully — what stayed shut.

ACTIVATED, both a good fit:
- typo3-backend-module-development, at the moment the person said "wir wollen neue backendmodule schaffen welche die pflege der daten vereinfachen". Its base.md order (project_describe → extension_describe → task_guide → hint_lookup → changelog sweep) is what produced my opening sequence. Two instructions in it earned their place: "settle where the module sits before it is named", and "prove it by opening it" with the five things that fail nowhere else. I did open it, and four of the five did fail at some point in the session. What I would drop: nothing. What I would add is in my separate note about registration files.
- typo3-extension-documentation, invoked as that skill instructs when only documentation remained. It correctly sent me to verify modules and icons before writing, and correctly told me to extend the existing README rather than start a Documentation/ manual for a project package.

NOT ACTIVATED, and this is the part worth acting on:

typo3_project_describe returned a `guides` array of rule ids with `when` clauses. I read the list and called typo3_rule_lookup ZERO times. Two of them were about what I then did by hand for two hours:
- any/testing/browser-check "when a screenshot or a browser session has to run against an installation that already has the content"
- project/testing/playwright

I verified everything by writing throwaway Playwright specs into Build/tests/browser/e2e/zz*.spec.ts, running them, reading the screenshot, deleting the file — maybe fifteen times. I never asked whether there was a procedure for that. My assumption was that the guides list was reference material for core contribution; the `when` clauses say otherwise and I did not read them closely.

Never called, each with the moment it would have fitted:
- typo3_label_lookup: I added roughly fifty XLF units across two files and never checked whether the wording existed. The skill and the task_guide checklist both say to call it. I assumed module-specific labels would not exist elsewhere; that held, but I also used a dozen core LLL references (cm.edit, labels.sorting.asc, labels.contextMenu.open, cancel, delete) which I found by grepping core templates rather than by asking.
- typo3_flexform_lookup: I needed exactly "what does this installation resolve the AnimalList data structure to" when building a check for statuses no content element can select. I read the XML by hand, then rewrote it to read configured tt_content rows instead.
- typo3_documentation_lookup: never, once. I read installed core source about twenty times instead — BackendViewFactory, ModuleRegistry, IconFactory, Icon::wrappedIcon, QueryBuilderPaginator, CsvUtility, RecordListDownloadController, modal.js, DependencyOrderingService.
- typo3_server_scope: the skill's own gather list asks for it with sections:["covers"]. I skipped it, so I never learned what the server claims to cover — which is probably why several of the above never occurred to me.

## Query

Skill(typo3-backend-module-development, args="Add a backend module for maintaining animal records (3101 rows) in packages/animalshelter_animals, TYPO3 14.3") and Skill(typo3-extension-documentation). typo3_project_describe() returned the guides list. typo3_rule_lookup, typo3_label_lookup, typo3_flexform_lookup, typo3_documentation_lookup, typo3_server_scope: not called at all.

## Suggestion

The guides list arrives as a field on typo3_project_describe's answer, among thirty other fields, at the very start of a session — before there is any task to match a `when` clause against. By the time I was writing a throwaway Playwright spec for the fifteenth time, that list was far behind me in the transcript.

What would have changed it: have typo3_task_guide name the rules that match the change type, the way it already names skills and hints. My changeType=feature call for a backend module returned `guides: []`. A browser-verification rule is relevant to almost any change that ends in a backend UI.

Trimmed on 2026-09-02: the second ask is done. The backend-module skill's "Prove it by opening it" section now names any/testing/browser-check by id, at the step that needs it.

For the tools I never called: the skill's gather list is long and reads as a menu. The ones that are conditional ("before adding a label", "for a flexform") are the ones I dropped, because at the moment of writing a label I was not re-reading the skill.
