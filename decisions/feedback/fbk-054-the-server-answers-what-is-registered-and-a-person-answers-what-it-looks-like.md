---
id: D-FBK-054
title: The server answers what is registered and a person answers what it looks like
date: 2026-09-02
status: open
---

# D-FBK-054 — The server answers what is registered and a person answers what it looks like

**Every answer this server got right was about what a declaration resolves to,
and every correction it could not reach was about how the result reads.**

## Evidence

- `feedback/archive/2026-09-02-135112` lists what carried the session, and each
  entry is a registration or a stored shape. `typo3_record_lookup` returned
  3,101 rows on one folder split by status, which decided the editing surface
  and turned two statuses no content element can select into a finding.
  `typo3_schema_lookup` returned a date column as a nullable bigint, which is
  why a writer handing DataHandler an ISO string was caught by a test written
  for that boundary. `typo3_backend_module_lookup` found a doubled route path
  that five passing browser tests had gone past. `typo3_icon_lookup` refused an
  identifier before it shipped.
- `feedback/2026-09-02-135220` lists fourteen corrections the person the session
  worked for made, in their own words, and none is reachable by a call: the
  names are not clickable, the filter sits oddly, nothing shows a filter is
  active, the sort icon is missing, a number does not read as a number, the
  folder icon is barely distinguishable, the paws stick together. Two of them
  were defects in code the session wrote that its own tests passed over.
- The same session wrote roughly fifteen throwaway browser specs and read
  screenshots as a partial substitute for that person, and says so.
- It reports one of the fourteen as knowledge-shaped after all: a count rendered
  as plain text where the backend has a class for exactly that, and the class
  was in a `typo3_component_lookup` answer the session already held, fetched
  earlier for another reason.

## Decided

- The boundary is written down here rather than argued each time a report of
  this shape arrives. This server answers what an installation registers, what a
  column stores and what a convention requires. Whether a rendered result reads
  correctly is answered by opening it, and the reports above are the two sides
  of that line rather than a gap on either.
- What follows for the corpus is not a rendering answer but the step that gets a
  session in front of the thing: `any/testing/browser-check` is that procedure,
  and the session that wrote its own spec fifteen times never fetched it. The
  routing for it is `T-260902-e7c1` and the backend-module skill's own step,
  written the same day.
- The component answer arriving at planning time and being needed at writing
  time is a delivery observation about one tool rather than about the boundary.
  It is not queued on this entry: one session connecting an answer late is not
  yet evidence that the answer arrives wrong.
- The strengths are recorded and nothing is built from them. `D-FBK-018` is why:
  an account of a run confirms no decision, and what these two carry is where
  the line runs.

## Assumed

- That one session says where the line is. It is a long one against a real site
  with a real reader, which is the widest evidence of this shape the corpus
  holds, and it is still one.
- That the fourteen corrections are representative of what a person catches.
  They are what one person wrote in one project, and a different reader would
  have written a different fourteen.

## Wrong if

- A session reports a correction of that kind that a call would have caught, and
  names the call. The line would then run somewhere else, and the entry moves
  with it.
- The browser-check routing lands and a session still substitutes its own specs
  for it. The gap would then be the procedure rather than its delivery, which is
  the opposite diagnosis from the one written above.
