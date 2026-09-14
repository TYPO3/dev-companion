---
id: R-ANS-016
title: 'A content-element task is offered the Extbase fork'
status: held
restsOn: [D-ANS-039]
heldBy:
  - HintsTest::aContentElementTaskIsOfferedTheExtbaseForkWithoutNamingIt
---

# R-ANS-016 — A content-element task is offered the Extbase fork

**A task that chooses how to build a new content element gets the Extbase-or-not
fork, without Extbase, a plugin or a repository named first.**

The fork itself is on the `extbase` and the `frontend-records` hint and this
does not restate it. What this demands is that it arrive. Both paths to it open
on a word the caller has to supply, and the word is the option they have not
considered. A session that describes its work as the work, a new content element
with a repeatable list, describes the decision. It does so at the one point
where it is still free to make it.

It also demands that the fork be right about the versions it arrives on. A
plugin is a `CType` like any other since v14, registered into the same selector
as any other element. So words that offer "a content element or an Extbase
plugin" offer two categories that are one.

The other half is what the extension already does. `typo3_extension_describe`
reports each content element with a `kind` of `element` or `plugin`.
`skills/base.md` orders that call before the read of the checkout, so the
convention is in the session's hands already. What this requires is that it read
as evidence about the architecture rather than only about where an element
renders.

## From

`feedback/2026-08-01-003925` (2026-08-01), a TYPO3 14 testimonials session in
`/home/benji/projects/site-new`. It built the element on TCA and a
`DatabaseQueryProcessor` without a check that the extension's other plugins are
Extbase. `feedback/2026-08-01-003313` is the same debrief's other delivery
report, queued as `R-ANS-015`.

Measured on 2026-08-02:
`bin/cli hints:probe "new content element for testimonials with a repeatable list of entries, TCA and Fluid rendering"`
reaches `content-elements` and `tca-formengine`. The word records is what brings
`frontend-records` back. Extbase, a plugin, a repository or pagination by name
is what brings `extbase` back.

## Held by

It holds this the way the fork arrives. The two hints stay unreachable from the
query that reported it, and the `content-element` checklist is what carries the
fork to it instead. What no test holds is whether a session acts on a checklist
item it received.
