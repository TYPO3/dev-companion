---
id: D-KNW-127
title: 'How a backend web component surfaces a failed load is a subject this server owns'
date: 2026-08-27
status: open
readings:
  - 2026-09-01
coveredBy: []
---

# D-KNW-127 — How a backend web component surfaces a failed load is a subject this server owns

**The `error` branch a backend component declares renders only where its `Task`
rejects, and `knowledge/hints/backend-ui.json` states that as
`backend-lit-task`.**

Step 1a of the ladder, closed on the spot. The corpus had four sentences about
custom elements and none about how one of them loads data, so a session about to
write an error path had nothing to read.

## Evidence

- The miss reproduces. `bin/cli hints:probe` on the feedback's own subject — a
  backend Lit step that renders a `Task` and declares an error render branch —
  reached `javascript-unit-tests` alone, which is about covering a module rather
  than writing one. No file below `knowledge/` or `skills/` named `@lit/task`
  before this change.
- The TYPO3 claim holds, read in `.checkouts/main`. All five steps of the
  localization wizard declare an `error:` branch calling
  `this.context.wizard.renderError()` with a label of its own, and all five task
  functions close on `catch (error) { console.warn(...); return []; }`. The
  branch cannot fire, and the caller is shown the empty-result markup of the
  `complete` branch instead.
- The working shape stands in the same tree.
  `Build/Sources/TypeScript/backend/element/icon-element.ts` and
  `thumbnail-element.ts` let `Icons.getIcon()` reject and render the not-found
  icon out of `error:`. So the rule is read off the core rather than argued from
  the library.
- No binding is owed. `@lit/task` is `^1.0.1` in `Build/package.json` on every
  covered line, and the number of modules importing it grows from two on the
  oldest to fifteen on the development line.
- The reporting session paid for the gap twice: it built a wizard teardown, then
  a faked Close button, and removed both once the screenshots showed the wizard
  chrome disappearing. What it wrote was a second error surface beside one that
  was declared and switched off.

## Decided

- A hint of its own rather than a statement on `backend-ui`. That one answers
  what a custom element should be kept to; this answers how one loads data and
  what a failed load looks like, and `D-KNW-030` puts one question in one hint.
- Closed on the spot rather than queued. The checkout reading was made in this
  run and the change touches no code, no schema and no skill — `D-FBK-052`.
- The feedback's own wording is not copied. It names the localization wizard,
  which is the file its session was changing; the hint states the mechanism and
  names no path, so it survives the fix that report was written on the way to.
- The reachable phrasings were measured rather than assumed.
  `write an error path in backend typescript` answers `backend-lit-task` alone,
  and the symptom phrasing — the error branch never renders because the task
  returns an empty list — answers it first. A query about a wizard step reaches
  nothing, and `wizard` is left out of `appliesTo` deliberately: routing a rule
  about `Task` off one wizard's vocabulary is what `D-KNW-030` measured as
  crowding.
- `coveredBy: []`. What is settled is that a statement exists and what it says,
  and no assertion tells that from the same words in another file.

## Assumed

- That the caller arrives while writing or debugging the error path, which is
  the phrasing measured above and the moment the reporting session was in. One
  arriving from the rendered symptom — an empty list where a request failed —
  writes about the wizard, and nothing here reaches that.
- That the core keeps rendering asynchronous backend data through `@lit/task`.
  Fifteen modules on the development line against two on the oldest covered one
  is the trend the hint is written against.

## Wrong if

- A core `Task` returns an empty value on failure on purpose and renders an
  empty state for it. Then catching is a choice rather than a defect, and the
  hint's second statement claims more than the tree supports.
- A session reports the same cost with the hint in place, having searched by the
  wizard rather than by the component. Then `appliesTo` is written in the wrong
  vocabulary and the assumption above is what failed.
- The backend adopts another shape for asynchronous rendering. Then the hint
  describes an API the tree no longer writes, and what replaces it is a new
  statement rather than a rewrite of this one.
