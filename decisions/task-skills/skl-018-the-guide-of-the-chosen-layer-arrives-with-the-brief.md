---
id: D-SKL-018
title: The guide of the chosen layer arrives with the brief
date: 2026-08-04
status: open
---

# D-SKL-018 — The guide of the chosen layer arrives with the brief

**A task whose words and paths name a test layer gets that layer's
implementation guide by name, rather than one sentence that offers three.**

A session wrote a backend Playwright project, login setup, storage state,
project dependencies, four specs. It never opened `references/playwright.md`,
which is the file that work is for.

## Evidence

- `feedback/2026-08-04-180052`. The skill says "After selecting a layer, read
  only its implementation guide" and lists the three in one sentence.
  `references/static-quality.md` was read because the check-layer half of the
  task named it first; the browser half arrived several layers into a nested
  activation — cleanup, then conformance, then testing — with the checklist
  already in hand.
- What it cost is on the record. The storage-state constant placed in the login
  setup spec, and refactored after one run. The runner rejected it with
  "Playwright Test did not expect test() to be called here". The second cost,
  locators finding nothing inside the backend module, is `D-KNW-060` and is not
  the guide's to answer.
- The brief already carries this shape. `D-GUI-007` puts a selection of hints
  into it and says whose they are, and it names the skill that owns the task
  since `b614b12`. A reference is one hop further along the same path.
- `knowledge/task-intents.json` has `tests` and no entry for a browser layer, so
  the brief has nothing to name today. The intent that matches "playwright",
  "e2e" or "browser test" does not exist.
- A reference path written into the corpus is a path this repository writes
  between its own files, which `bin/cli links:check` and `LinksTest` already
  hold. So naming a skill's file from `knowledge/` does not create a link
  nothing watches.

## Decided

- The judgement is **step 3**, routing. The guide exists, prose names it, and
  the pointer competed with two siblings at the moment of choice.
- The lever is the **tool answer**, not the skill. The next release of this
  server does not correct a published skill's copy in somebody else's project.
  The server composes the brief here on every call.
- The smaller half stands. That is the three guides as a table keyed by the
  layer chosen in the step before, so selection and pointer are one act. It
  rides along the next edit of that skill rather than earns a change of its own.
- Nothing here decides what the new intent matches or what its checklist says.
  That comes off the guide it points at.

## Assumed

- That a named file in a tool answer is harder to walk past than one of three in
  a sentence. That is the reporting session's own reading of why it skipped the
  guide, and it is one session.

## Wrong if

- The brief names the guide and the next browser-test session still works from
  the checklist. Then what competes is the reading order of a nested activation,
  and the pointer was never the lever.
- The intent matches tasks that are not browser work, and briefs start to carry
  a guide nobody needs. Then the layer is not readable off a task's words.

## Since then

The intent landed and the brief ran. It recognizes a browser task beside the
element one and returns the guide as its first checklist line. That took data
and no code, since a brief already renders an intent's checklist, so the
narrower reading — a field beside `skill` — was not built. The match list
deliberately avoids the bare word the other intent carries: a browser task
matches both, which is correct, because what the layer adds is a guide and a
served site rather than a different way of testing.

Whether a brief naming a skill should withhold the hints that skill fetches was
asked as an amendment here and is not one; `D-GUI-016` settled it.
