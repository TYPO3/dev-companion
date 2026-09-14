---
id: D-KNW-016
title: 'What an `f:else` does to the branch beside it is a subject this server owns'
date: 2026-08-02
status: open
---

# D-KNW-016 — What an `f:else` does to the branch beside it is a subject this server owns

**An `f:else` turns the inline body of its `f:if` into no branch at all, and
neither this server nor the manuals it serves says so.**

The failure is silent in both directions. The condition is true, the data is in
the template context, the else branch is visibly the wrong one to have rendered.
The then branch produces nothing, with no error and no log line. A session that
has read the reference page still writes the markup that fails. The page says
the opposite in the sentence it reads first.

## Evidence

- The miss reproduces. `bin/cli hints:probe` with the feedback's own query
  matches nothing, and returns 40 candidate hints as the index. The query is
  *f:if with f:else but no explicit f:then swallows the inline then-branch /
  f:link.typolink output*. `f:if f:then f:else branch renders empty` matches
  nothing either.
- Routing is not what failed. `fluid template conditional link` reaches
  `fluid-templates` at `appliesTo(14) + text(99)`. So a statement on that entry
  would arrive at a session that writes a conditional into a template. There is
  nothing here to route and nothing to move.
- The corpus names `f:then` once, on `fluid-templates`, in the `until: 12`
  statement about an array-typed argument. That statement is about what an
  inline `f:if()` evaluates to as a value; which child renders is a different
  subject. Nothing below `skills/` names `f:if`, `f:then` or `f:else` at all.
- The manual still answers with the wrong book. Called over stdio with
  `f:if f:then f:else condition ViewHelper` at `targetVersion: "14"`,
  `typo3_documentation_lookup` returns Developing a custom ViewHelper and the
  Fluid Translate ViewHelper. It returns JavaScript form helpers and three
  TypoScript *Conditions* pages. That is
  [`D-ANS-023`](../answers/ans-023-a-viewhelper-question-is-answered-by-widening-the-manual-index.md)
  reproduced.
- Indexing the ViewHelper reference would not close this, which is what keeps
  the two levers apart. The If page is at
  `https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/Global/If.html`;
  the `typo3fluid/fluid/latest/If.html` path `D-ANS-023` names answers 404. Its
  Basic usage reads "Everything inside the <f:if> tag is being displayed if the
  condition evaluates to true". The `f:then` / `f:else` form stands beside it as
  a second example. Nothing on the page says the first no longer holds once an
  `f:else` is there.
- The claim about the engine holds, and `D-ANS-023` already read it in
  `typo3fluid/fluid` 5.3.1. Uncached,
  `AbstractConditionViewHelper::renderThenChild()` returns `null` where it finds
  an `ElseViewHelper` and no `ThenViewHelper`. Compiled, `convert()` wraps the
  body as `__then` only where it saw no `f:then`, `f:else` or `f:else if` child.
  It is not read again here.
- What that research does not settle is the range. It came from one engine, and
  [`D-VER-003`](../versions/ver-003-the-fluid-engine-gets-no-version-axis-of-its-own.md)
  says each covered branch pins its own: 12.4 on `^2.15.0`, 13.4 on `^4.6.1`,
  14.3 on `^5.3.1`. A statement from it has a measure on 14 and a guess on the
  other two.

## Decided

- Step 1a of the ladder, and queued. The answer is in neither `knowledge/` nor
  `skills/`, the manuals this server serves do not carry it, and there is
  nothing to reword or place.
- Not closed on the spot, and the range is why. What lands is a statement about
  engine behaviour across three majors with one of them measured. `D-VER-003`
  says how to settle the other two. One throwaway directory per major with the
  engine required into it, and the behaviour rendered through a probe
  ViewHelper. That is the todo's first step rather than this run's.
- The statement belongs on `fluid-templates`. That is the entry the query
  already reaches. A session that hits this writes a template rather than a
  ViewHelper class, which is what `fluid-viewhelpers` is for.
- The feedback's suggestion goes as far as its author could see and no further.
  Its markup that works is the right canonical example. What it cannot say,
  since it never asked, is which majors the trap exists on. A statement bound to
  the wrong range is the failure the ladder's step 1a names.
- This is not the lever `D-ANS-023` already has. That entry indexes a book; this
  one writes a sentence the book does not contain. Neither closes the other, and
  the research above is what says so rather than an assumption that two Fluid
  feedback want one fix.

## Assumed

- That one statement covers `f:else if` as well. The compiled path names all
  three children, so the assumption is that a body beside an `f:else if`
  disappears the same way. Nothing here measured it, and the todo can settle it
  in the same directory it settles the range in.
- That a session reads the statement before it writes the template rather than
  after the link comes back empty. Nothing about the failure points at the
  condition, so a session that debugs it has no phrase to search with. That is
  the argument for a statement that names the symptom and not only the rule.

## Wrong if

- The behaviour differs on Fluid 2.15 or 4.6. It is then two statements with a
  boundary between them. A single unbound one, the shape this queues, is wrong
  on whichever branch it is not about.
- The engine starts to report it. A parse error, a caveat, or a log line on an
  `f:if` that has both a body and an `f:else`. The statement then describes a
  version nobody is on, and it needs an `until` it was not written with.
- The reference page gains the note upstream. The corpus then carries a
  restatement, and what this server owns is a pointer rather than a statement,
  the same test `D-KNW-015` sets itself.

## Since then

The range had its measure and there is no boundary. Three engines, ten markup
cases, each rendered uncached and off a filled cache, and all three answer
identically.

What the measurement says is stronger than what this entry assumed. Beside an
`f:else` the body does not render and disappear; the engine never evaluates it,
on every engine and both paths. Plain text disappears the same way, so the
feedback's account that ViewHelper output in particular is lost is narrower than
the behaviour.

The **Evidence** bullet that says the route is not what failed is wrong, and its
own first bullet shows it. The feedback's query carries no word that reaches the
Fluid domain, so the category was gone before any score. `D-KNW-024` is the
second lever this entry said there was no need for.
