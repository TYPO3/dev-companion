---
date: 2026-09-04T05:36:18+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_record_lookup
directory: /home/benji/projects/site-tierheim
---

# Six ddev mysql queries decided the markup and typo3_record_lookup was never tried

## Observation

Task: remove fluid_styled_content from a sitepackage and rebuild what it provided, on an installation with real content.

typo3_task_guide named typo3_record_lookup in nextTools. I read its stated purpose — row counts, which page, what they are, "the count that decides whether the record list is still the editing surface" — decided it answered a record-volume question and not mine, and went to the database directly. Six queries, every one of which changed what I wrote:

1. SELECT CType, COUNT(*) FROM tt_content GROUP BY CType — eight CTypes in use, 137 rows, none of them a core element. This is what made it safe not to rebuild text, textmedia, header, table, bullets, uploads, div, html and the menus at all. Without it I would have rebuilt all of them defensively.
2. A UNION of GROUP BY over header_layout, header_position, frame_class, layout, space_before_class, space_after_class, subheader, date, linkToTop, sectionIndex. This decided the whole markup question: every record carries frame_class='default', layout=0 and empty spacing, so the frame-default / frame-layout-0 / frame-space-* classes fluid_styled_content emitted are dead on this site and I dropped them from the replacement layout.
3. The single exception the same query exposed: one record with header_layout=1. I then looked it up by uid — it is the stage text on the home page, an h1. Had I trimmed the header partial to a fixed h2 (which the forms alone would have justified, since no CType here puts header_layout in its showitem) I would have demoted the site's only h1 and neither the CSS nor the browser suite would have caught it.
4. header_link per CType — set only on the teasers, which suppress the header section. That is why the replacement header partial has no typolink.
5. pages list and 6. a CType-to-slug map, to choose which twelve URLs to capture as a before/after rendering baseline.

So the questions that mattered were not "how many rows" but "what values does this column actually hold across this table, and which rows are the exceptions". I never put that to the server because nothing in the tool's description suggested it would answer it. I did not test the assumption.

I cannot say whether typo3_record_lookup would have answered any of these; the finding is that its description sent me elsewhere before I found out.

## Query

Not a call — the finding is that no call was made. typo3_task_guide listed typo3_record_lookup under nextTools with the reason "how many rows the table has, on which page and what they are — the count that decides whether the record list is still the editing surface". I read that description and went to `ddev mysql` instead, six times, on a TYPO3 14.3.6 project.

## Suggestion

Either widen typo3_record_lookup to answer field-value distribution over a table — "what values does tt_content.header_layout hold, with counts, and which uids carry a value other than the default" — or say plainly in its description that it does not, so a caller stops guessing.

The distribution question is the one that decides whether markup, a TCA default or a template branch can be dropped, and it is asked at exactly the moment a cleanup is being planned. A shape like: per column, the distinct values with counts, the TCA default beside them, and the uid+pid of every row that departs from it, capped at a handful. That last part is what turns a distribution into a decision — the one h1 on this site was a single row out of 137.

If the tool is deliberately scoped to counts and locations, then typo3_task_guide's nextTools entry for a "cleanup" changeType should say which questions about existing content the server does not answer, so the caller goes to the database once with a plan instead of six times.
