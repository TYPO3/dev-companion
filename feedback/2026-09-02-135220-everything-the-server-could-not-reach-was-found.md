---
date: 2026-09-02T13:52:20+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/benji/projects/site-tierheim
---

# Everything the server could not reach was found by a person looking at the screen

## Observation

Task: same session, reported because the debrief asked what else I would say.

The session ran long and its shape was: I built, the person looked at a screenshot, they wrote one line, I fixed it. Their lines, in order:

- "das klingt gut, wir wollen neue backendmodule schaffen"
- "die interfaces sind immer noch nicht gut z.b. sind die namen nicht klickbar"
- "aber auch die übersicht fehlt ein bisschen"
- "auch müssen wir davon ausgehen was die personen machen. sie verwalten aktive nicht adoptierte tiere und müssen neue erstellen"
- "adopted ist mehr oder weniger eine archiv funktion"
- "auch haben wir noch fehler in den daten wie z.b. &quot"
- "ich denke wir sollten beide module auch zu einem machen"
- "der filter sitzt komisch" / "es wird auch nicht angezeit das ein filter aktiv ist"
- "das sorting icon fehlt auch bei aktiven sortierungen"
- "hier sieht man nicht wirklich das es eine zahl ist ich glaube wir haben dafür css tools"
- "das folder icon für animals ist kaum vom normalen unterscheidbar"
- "das platzhalterbild mit der pfote ist aber unsauber die kleben zusammen die tatzen"
- "was für ein tier soll das denn sein?"
- "die location fehlt in der liste wenn mehere storage folder existieren"

Every one of those was right and none of them was reachable by any call I could have made. Two of them were defects in code I had written that all my tests passed over: the sort icon never appeared because I wrote the Fluid condition as `{demand.orderField} === '{field}'` with the field name quoted, which never matches (core writes it unquoted); and the counts in a dropdown were plain text where the backend has dropdown-item-column-value for exactly that.

I record this not as a complaint about the server — it is not a rendering oracle — but because the debrief asks what a session cost and where. The expensive part of this session was not knowledge. It was that between writing markup and knowing whether it reads correctly there is only a person, and I burned roughly fifteen throwaway Playwright specs plus screenshot reads getting a partial substitute.

One knowledge-shaped thing does fall out of it: the two defects above were both "the core has a documented way to do this and I did it by hand". The quoted-comparison one I would not expect any server to catch. The dropdown-item-column-value one is in typo3_component_lookup's answer for "dropdown" — I had that answer in the session, earlier, for a different reason, and did not connect it. That is on me, and it argues for the component answers being reachable at the moment of writing rather than at the moment of planning.

## Query

Whole session; no single call. The two code defects: Fluid `<f:if condition="{demand.orderField} === '{field}'">` (never matches; core writes `=== {field}`) and counts rendered as `<span class="text-body-secondary">` instead of `dropdown-item-column-value`.

## Suggestion

Nothing to change in a tool. If there is an appetite for it, a hint id like "fluid-conditions-and-arrays" (which typo3_hint_lookup's fluid-templates entry already names as a neighbour and I never fetched) carrying "a field name compared in a condition is written unquoted; a quoted one is a literal and never matches" would have caught the first defect. I did not fetch it because the task_guide answer listed it only as a see-also inside another hint's text, not as a hint I was owed.
