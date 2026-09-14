---
id: D-CAT-005
title: 'A reference entry names a form to imitate'
date: 2026-08-18
status: open
---

# D-CAT-005 — A reference entry names a form to imitate

**A directory earns an entry in `knowledge/catalog/reference/entries.json` where
a caller reads it before it writes one of the same shape. A fact that happens to
sit in it earns nothing.**

A session that had read the frontend's middleware registration on two branches
asked for that file in the list. The next caller would then find it. What the
next caller needs is the order, and a path does not carry one.

## Evidence

- `feedback/2026-08-18-081228` asks for
  `typo3/sysext/frontend/Configuration/RequestMiddlewares.php` and
  `typo3/sysext/frontend/Classes/Event/` beside the document it proposes,
  because "neither is discoverable as a reference today". Neither is in the
  seven entries of `knowledge/catalog/reference/entries.json`.
- The corpus already names both, from the hint that also answers the question
  behind them. `routing-request-handling` carries
  `Configuration/RequestMiddlewares.php` in its `appliesTo`. It states that
  middleware registers there per request scope with `before` and `after` that
  name other middleware. `events-extension-points` states that event classes
  live in `Classes/Event/` of the extension that dispatches them. That holds for
  every extension where an entry would name one directory.
- The path is not what the session lacked. It reports a read of
  `RequestMiddlewares.php` on both branches along with both request handlers. It
  reports the cross-branch git read as the efficient part of the session, one
  `git show origin/13.4:<path>` against the same path on `main`. What it paid
  for was the order across majors.
- That order is now in the corpus as an answer rather than as a path.
  `typoscript-conditions`, written the same day in `899dbdbb` and `3b7a0939`,
  states per major which globals exist when a condition matches.
  `bin/cli hints:probe` on the session's own three questions reaches it:
  `appliesTo(20) + text(193)`, `appliesTo(33) + text(240)`,
  `appliesTo(20) + text(133)`.
- `typo3_reference_list` said what an entry is for in its own description. "Read
  one of these before inventing a layout or a test harness — they are the
  version-correct, currently-passing form of what a convention describes". All
  seven are that. A theme, the styleguide, an Extbase fixture extension, the
  content element render, a browser suite, and two build setups.

## Decided

- The two entries stay unwritten. A reference answers *what do I read before
  writing one of these*. The feedback asks *where is this declared*, which is a
  lookup's question, and the lookup already names both files.
- What earns an entry is the write it saves, not that a session opened the file.
  A path in an index is one call spent to learn where to spend the next four.
  That is the trade `D-FBK-027` measures the other way round.
- The subject does not go out with the entries. What the session wanted from
  those two directories has an answer, and it has it where an answer is.

## Assumed

- That a caller who asks about middleware order asks in words
  `routing-request-handling` matches. Probed here on 2026-08-18: "frontend
  middleware order per major, what each stage has assembled" reaches it at
  `appliesTo(10) + text(146)`.
- That the hint rather than the core's file serves an extension author who
  writes their own `Configuration/RequestMiddlewares.php`. Nothing has measured
  it, and it is the reading under which the entry would be due after all.

## Wrong if

- A session reports that it reached the middleware hint and still did not know
  what to write in its own registration. The core's file is then a worked
  example under the tool's own reading, and the entry is due.
- A second session pays round trips to find where the frontend declares its
  middleware order while the hint is reachable. That is the ladder's step 2 and
  a placement question, not a catalog one. This entry would have sent it to the
  wrong lever.
- Somebody writes an entry for a directory nobody imitates and a session reports
  it as the answer to a fact question. The line then runs in the wrong place.

## Since then

The two entries are still unwritten and nothing asks for them again.
`knowledge/catalog/reference/entries.json` holds the same seven, and the probe
this entry rests on returns the same numbers on 2026-08-23. "frontend middleware
order per major, what each stage has assembled" reaches
`routing-request-handling` at `appliesTo(10) + text(146)`, alone.

No second session has paid for it. The feedback that asked, `2026-08-18-081228`,
is in the archive. No report since names the frontend's middleware declaration
as something it went to look for. So all three **Wrong if** stand undisturbed.
The one that would fire first is a session that reaches the hint and still does
not know what to write. That is the case the **Assumed** says nothing has
measured.
