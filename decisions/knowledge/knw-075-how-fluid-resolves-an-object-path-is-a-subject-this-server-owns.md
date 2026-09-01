---
id: D-KNW-075
title: 'How Fluid resolves an object path is a subject this server owns'
date: 2026-08-14
status: confirmed
coveredBy:
  - HintsTest::theShadowingIsReachedFromTheClassAndFromTheTemplate
---

# D-KNW-075 — How Fluid resolves an object path is a subject this server owns

**How Fluid resolves a path segment on an object — every getter before the
property of that name — is inside this server's boundary and missing from it.**

The corpus states where a template file is found, what a layout renders and
where an expression goes wrong, and says nothing about what `{obj.items}` asks
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
  `preview-record-variable` in `knowledge/hints/content-elements.json`: "the
  record is a PSR-11 container, and Fluid asks `has()` and `get()` for a path
  segment before it tries any getter". That names the branch above the getters,
  for the one variable a preview template is handed, and stops there.
- The feedback's claim about Fluid holds, and it holds on every covered major.
  `StandardVariableProvider::getByPath()` tries an array key or `ArrayAccess`,
  then `getFoo()`, `isFoo()`, `hasFoo()`, and only then `property_exists()` —
  read in `.checkouts/12.4` at typo3fluid/fluid 2.15.0, `13.4` at 4.6.1, and
  `14.3` and `main` at 5.3.1. The `ContainerInterface` branch that
  `preview-record-variable` states arrived between 2.15.0 and 4.6.1, and it is
  the only difference across the four.
- The core writes the convention the feedback names.
  `Core\Pagination\SlidingWindowPagination` declares `getHasMorePages()` and
  `getHasLessPages()`, and `redirects/Resources/Private/Partials/Pagination`
  reads `{pagination.hasMorePages}` — read in `.checkouts/main`.
- The error string the feedback carries is bound to a major, and both sides are
  already stated one entry away. `fluid-viewhelpers` gives "The argument "x" was
  registered with type "array", but is of type "string"" `since: 14`, and
  `until: 13` says a value that does not match is passed to the ViewHelper
  unchanged. So the quoted message is the v14 strict argument processor;
  `ForViewHelper` on 13.4 takes its `foreach` over whatever arrived. What that
  renders on the maintained lines is the reading this entry queues.
- One report. `bin/cli feedback:list` on 2026-08-14 holds 12 open feedback, ten
  of them from `/home/benji/projects/typo3-cms` and five of those written within
  seventy-five seconds of each other. This is the only one in the corpus about
  how a path resolves on an object.

## Decided

- Step 1a, and queued. The order itself is established above rather than left to
  the card, because four checkouts carry the file and reading it cost one call.
  What the card owes is the symptom per major and where the statement goes.
- Not closed on the spot. What `<f:for>` does with a boolean on 12.4 and 13.4 is
  a claim about TYPO3 nobody here has read, and it decides whether the statement
  carries a binding.
- `normal` rather than the `low` the card arrived at. One session, so not more
  than that; what the silence lets through is a template that renders nothing on
  the two LTS lines, where the message that names the ViewHelper does not exist.
- Not `high`. Nothing is blocked on it, and the reporting session established
  the rule from the Fluid source itself.
- Where the statement goes is the card's. `fluid-conditions-and-arrays` is what
  `fluid-templates` already routes to as "where an expression goes wrong", and
  its title would have to widen; a hint of its own beside it is the other
  candidate.
- `preview-record-variable` stays as it is. It states the container branch for
  the one variable a preview template is handed, which is an instance of the
  order rather than a second statement of it.
- Not the feedback's own wording. Its author was guessing about this repository
  as much as this run guesses about TYPO3, and its proposed text asserts the
  error string on every major.

## Assumed

- That a session reaching this spells its query with the symptom rather than the
  mechanism. Nobody asks about an accessor before suspecting one, so the
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
  and one sentence shorter, and the priority was set on a difference that is not
  there.
- A Fluid release inside a covered major reorders `getByPath()`. The statement
  then needs a `since` keyed on the engine each branch pins rather than on the
  TYPO3 major, which is a binding nothing in `knowledge/` expresses today.
- The statement lands and a session still writes `hasItems()` beside a public
  `$items`. The lever is then where the class is written rather than where the
  template is read, and a hint in the Fluid domain is the wrong home.
- The same surface is reported again as "`{obj.foo}` came back null" rather than
  as a shadowed property. The half that mattered is then the unreachable
  `hasItems()` and not the shadowing, and the statement leads with the wrong
  one.

## Confirmed on 2026-08-14

The gap held and is filled, and the query this feedback missed on now reaches
the hint alone.

The first **Wrong if** fired in part: the message is not the strict processor's
alone, both maintained lines raising it from the argument check, so what the
priority rested on is wrong as written. The statement carries a binding all the
same, along a line this entry did not predict — a false value passes the lenient
check, so the loop is skipped and a warning is all that is left, and on the
oldest major the compiled template skips the check entirely, which is why the
same page throws on its first render and renders nothing after it.

Read by rendering the case rather than off the source. One statement beside it
was wrong and is corrected in the same commit.

## Since then

The same memory was filed again on 2026-09-01, from the same directory and the
same local file, and the corpus answers all of it.

The re-run measured the third **Wrong if** from the other end: the ViewHelper's
exception reached the hint with a `.html` path in the call and reached nothing
with a `Classes/` one, because `Domains` places a query carrying no Fluid word
in `php`. Tagging the hint `php` as well was the obvious lever and was tried and
dropped on 2026-09-02: it took five of twelve ordinary PHP tasks with it, first
of all for "add a getter for a new property", and it displaced a PHP hint out of
the recorded deprecation brief. What moved instead is one phrasing —
`was registered with type` — into the Fluid keywords, which is the words of a
caller who is not asking about Fluid at all. The twelve are untouched by it.
