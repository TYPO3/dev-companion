---
id: D-SKL-046
title: 'A precondition is restated where the file it guards is written'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder
---

# D-SKL-046 — A precondition is restated where the file it guards is written

**A precondition the instructions state stands again in the skill whose workflow
writes the file it guards. The instructions arrive once and the file arrives
hundreds of calls later.**

The instructions name three lookups whose guess fails only at runtime. One
session had all three calls' worth of mistakes open to it and made one of them.
The difference between the call it made and the two it skipped was where the
rule stood.

## Evidence

- `feedback/2026-08-17-210001` reports one build session — a sitepackage and a
  distribution extension — against the three preconditions the `instructions`
  carry. The session called `typo3_icon_lookup` three times, and one call caught
  three unregistered identifiers out of twelve proposed, before any of them
  reached a TCA file. It never called `typo3_component_lookup`. It wrote four
  backend design tokens into a preview stylesheet from memory, each behind a CSS
  fallback, and shipped them unvalidated. It never called `typo3_label_lookup`
  across roughly fifty new trans-units.
- Exactly one published skill named `typo3_component_lookup`,
  `typo3-backend-module-development`, which routes it *before writing buttons,
  status markers, cards, tables, or other backend markup*.
  `typo3-content-element-development` asks for *a useful backend preview for a
  custom CType* and for element-only CSS loaded through the AssetCollector. Its
  evidence step routed documentation, labels and icons and not that.
- The icon and label preconditions were both in that skill, in one bullet. The
  icon rule fired and the label rule did not. So a rule stated again where the
  task passes is what carried the icon call, and is not on its own what carries
  a call.
- The session states what it did instead. It read *reuse is scoped to the XLF
  resource used at the consuming code* as a statement that a new extension has
  nothing to ask about. That scope is the whole of what either channel says
  about labels. The `instructions` sentence ends *a match in another resource is
  not reusable there*. `LabelLookup::description()` opens *Reuse is local to the
  translation resource already used at the consuming code*. Neither covers the
  case that failed. That is a core label reference lifted out of another
  extension into the session's own TCA, which its code consumes rather than
  reuses.
- The `instructions` are not the channel with room in it. Assembled in this
  checkout they are 1830 characters of the 2048 `D-ANS-004` fixed as the budget.
  The exclusion prefix and the installer notice both go in front of them and are
  the caller's size rather than ours.
- The `routing` block already carries all three preconditions, and it sits
  behind `typo3_server_scope`. That is `D-AUD-003`'s own finding, that a session
  has to call a tool to learn that it should call tools.
- `feedback/2026-08-17-212218`, from the same session and still unjudged,
  reports the same shape from another symptom. A prescription phrased as a
  section in the middle of a document supplies a caller no reason to stop. A
  guide delivered at session start is not what a session reads at the end.

## Decided

- **Step 2, delivery, closed on the spot** for the component half. The rule
  exists and the workflow that writes backend preview markup and CSS did not
  carry it. The bullet is placement, and its wording is the sibling skill's
  because the two guard the same surface. `ROUTING_SKILLS` records it, which is
  what holds it.
- **Step 4, wording** for the label half. The rule arrived in the same bullet as
  the one that fired, so what remains is what it says. The bullet now states
  that the code that copies a label reference out of another extension consumes
  it. An extension with nothing of its own to reuse still has those to check.
- The `instructions` stay as they are. There is no room for a fourth clause. A
  longer sentence at session start is the channel a measure has already shown
  does not reach the moment the session writes the file.
- `LabelLookup::description()` stays too. It is accurate about reuse, and the
  gap was a step in a workflow rather than a property of the search. A workflow
  in a description a client caches is `D-SKL-043`'s mistake, one file further
  out.
- Nothing here looked up anything about TYPO3 and no contract moved. The skill's
  `description` and the ownership boundary it closes on stand as they were. That
  is the test `D-SKL-043` set for a skill edit closed in the judgement run.

## Assumed

- That a bullet in a skill's evidence step is still in reach when the session
  writes the preview stylesheet sixty files later. The icon call is the only
  evidence for it, and it sat in the same list as the label rule that did not
  fire.
- That the backend preview is the only surface in this workflow the component
  lookup owns. A content element's frontend markup is the project's own and the
  catalog says nothing about it.
- That the two skills now routing the lookup are the two that consume the
  component contract. `typo3_component_lookup` appears in no other skill. The
  two core-patch workflows name backend markup as a layer that decides which
  suites run. A patch that changes a component is where the contract comes from
  rather than a caller of it.

## Wrong if

- A session under `typo3-content-element-development` reports that it skipped
  the component lookup and wrote preview CSS from memory anyway. Then the
  evidence step is as far from the file as the instructions were. What remains
  is a step where the session writes the stylesheet.
- A session calls the component lookup for a backend preview and gets nothing it
  can use. Then the placement was right and the curated catalog is what does not
  carry the surface. That is a catalog question and not a skill one.
- A session follows the label bullet and a copied reference still goes
  unchecked. Then the trigger is the act of the copy rather than the wording. It
  belongs where the session writes the TCA rather than in the evidence step.
- A third published skill turns out to write backend markup without routing the
  lookup. Then this is not per-skill placement but something `skills/base.md`
  owes every workflow, and it stands once there instead.
