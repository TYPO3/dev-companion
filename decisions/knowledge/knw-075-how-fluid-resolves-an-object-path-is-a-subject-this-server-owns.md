---
id: D-KNW-075
title: 'How Fluid resolves an object path is a subject this server owns'
date: 2026-08-14
status: confirmed
coveredBy:
  - HintsTest::theShadowingIsReachedFromTheClassAndFromTheTemplate
---

# D-KNW-075 — How Fluid resolves an object path is a subject this server owns

**How Fluid resolves a path segment on an object, every getter before the
property of that name, is inside this server's boundary and absent.**

The corpus states where Fluid finds a template file, what a layout renders and
where an expression goes wrong. It says nothing about what `{obj.items}` asks
the object. A session that adds a `hasItems()` beside a public `$items` gets a
boolean where the array was, and the only thing it is handed is an error naming
the ViewHelper.

## Evidence

- Re-run on 2026-08-14 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own query matches nothing and returns 25 hints as the
  index. Three narrower probes: "f:for each argument is of type boolean error"
  matches nothing either; "naming methods on a DTO rendered in a Fluid template"
  reaches `fluid-templates` alone; "hasItems method shadows the public items
  property in a Fluid template" reaches `fluid-templates` and
  `preview-record-variable`, neither of which states an accessor.
- Nothing below `knowledge/` or `skills/` contains `getByPath`,
  `StandardVariableProvider` or an object accessor. The nearest statement is
  `preview-record-variable` in `knowledge/hints/content-elements.json`. It says
  "the record is a PSR-11 container, and Fluid asks `has()` and `get()` for a
  path segment before it tries any getter". That names the branch above the
  getters, for the one variable a preview template gets, and stops there.
- The feedback's claim about Fluid holds, and it holds on every covered major.
  `StandardVariableProvider::getByPath()` tries an array key or `ArrayAccess`,
  then `getFoo()`, `isFoo()`, `hasFoo()`, and only then `property_exists()`.
  This run read it in `.checkouts/12.4` at typo3fluid/fluid 2.15.0, `13.4` at
  4.6.1, and `14.3` and `main` at 5.3.1. The `ContainerInterface` branch that
  `preview-record-variable` states arrived between 2.15.0 and 4.6.1, and it is
  the only difference across the four.
- The core writes the convention the feedback names.
  `Core\Pagination\SlidingWindowPagination` declares `getHasMorePages()` and
  `getHasLessPages()`, and `redirects/Resources/Private/Partials/Pagination`
  reads `{pagination.hasMorePages}` — read in `.checkouts/main`.
- The error string the feedback carries has a major bound, and the corpus
  already states both sides one entry away. `fluid-viewhelpers` gives "The
  argument "x" was registered with type "array", but is of type "string""
  `since: 14`. `until: 13` says a value that does not match reaches the
  ViewHelper unchanged. So the quoted message is the v14 strict argument
  processor; `ForViewHelper` on 13.4 takes its `foreach` over whatever arrived.
  What that renders on the maintained lines is the reading this entry queues.
- One report. `bin/cli feedback:list` on 2026-08-14 holds 12 open feedback, ten
  of them from `/home/benji/projects/typo3-cms` and five of those written within
  seventy-five seconds of each other. This is the only one in the corpus about
  how a path resolves on an object.

## Decided

- Step 1a, and queued. The order itself is established above rather than left to
  the card, because four checkouts carry the file and reading it cost one call.
  What the card owes is the symptom per major and where the statement goes.
- Not closed on the spot. What `<f:for>` does with a boolean on 12.4 and 13.4 is
  a claim about TYPO3 nobody here has read. It decides whether the statement
  carries a bound.
- `normal` rather than the `low` the card arrived at. One session, so not more
  than that. What the silence lets through is a template that renders nothing on
  the two LTS lines. There the message that names the ViewHelper does not exist.
- Not `high`. Nothing waits on it, and the session that reported established the
  rule from the Fluid source itself.
- Where the statement goes is the card's. `fluid-conditions-and-arrays` is what
  `fluid-templates` already routes to as "where an expression goes wrong", and
  its title would have to widen. A hint of its own beside it is the other
  candidate.
- `preview-record-variable` stays as it is. It states the container branch for
  the one variable a preview template gets. That is an instance of the order
  rather than a second statement of it.
- Not the feedback's own wording. Its author guessed about this repository as
  much as this run guesses about TYPO3. Its proposed text asserts the error
  string on every major.

## Assumed

- That a session that reaches this spells its query with the symptom rather than
  the mechanism. Nobody asks about an accessor before they suspect one, so the
  `appliesTo` carries the error string as well as `getByPath`.
- That the naming half belongs beside the resolution half. It is advice to the
  author of a PHP class and the rest of the statement is about a template, and
  no hint here is about writing a DTO.
- That one session wrote this feedback and the four beside it. They share a
  directory, a model and seventy-five seconds, and nothing in a feedback records
  a session.

## Wrong if

- The reading finds the boolean on 12.4 and 13.4 raising an error that names the
  ViewHelper as plainly as the v14 message does. The statement is then unbound
  and one sentence shorter, and the priority rested on a difference that is not
  there.
- A Fluid release inside a covered major reorders `getByPath()`. The statement
  then needs a `since` keyed on the engine each branch pins rather than on the
  TYPO3 major. That is a bound nothing in `knowledge/` expresses today.
- The statement lands and a session still writes `hasItems()` beside a public
  `$items`. The lever is then where the author writes the class rather than
  where Fluid reads the template. A hint in the Fluid domain is the wrong home.
- A session reports the same surface again as "`{obj.foo}` came back null"
  rather than as a shadowed property. The half that mattered is then the
  `hasItems()` out of reach and not the shadow, and the statement leads with the
  wrong one.

## Confirmed on 2026-08-14

The gap held and a hint fills it, and the query this feedback missed on now
reaches the hint alone.

The first **Wrong if** fired in part. The message is not the strict processor's
alone, since both maintained lines raise it from the argument check. So what the
priority rested on is wrong as written. The statement carries a bound all the
same, along a line this entry did not predict. A false value passes the lenient
check, so Fluid skips the loop and a warning is all that remains. On the oldest
major the compiled template skips the check entirely. That is why the same page
throws on its first render and renders nothing after it.

A render of the case established it rather than a read of the source. One
statement beside it was wrong and the same commit corrects it.

## Since then

The same memory arrived again on 2026-09-01, from the same directory and the
same local file, and the corpus answers all of it.

The re-run measured the third **Wrong if** from the other end. The ViewHelper's
exception reached the hint with a `.html` path in the call and reached nothing
with a `Classes/` one. `Domains` places a query that carries no Fluid word in
`php`. A `php` tag on the hint as well was the obvious lever, and a session
tried it and dropped it on 2026-09-02. It took five of twelve ordinary PHP tasks
with it, first of all for "add a getter for a new property". It displaced a PHP
hint out of the recorded deprecation brief. What moved instead is one phrase,
`was registered with type`, into the Fluid keywords. Those are the words of a
caller who does not ask about Fluid at all. The twelve stay as they were.
