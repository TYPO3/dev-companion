---
id: R-SKL-004
title: 'An assessment establishes its base before opening the checkout'
status: held
restsOn: [D-SKL-002]
heldBy:
  - HintsTest::bothSidesOfAnInjectionQuestionReachTheSinkMethod
  - SkillTest::aFocusedRequestNarrowsTheReadingAndNeverTheSurfaceList
  - SkillTest::aSecurityFindingIsNotEstablishedUntilItsSinkIs
  - SkillTest::anAssessmentAsksBeforeItJudgesAndSaysWhatItDidNotAsk
  - SkillTest::theBaseIsEstablishedBeforeTheCheckoutIsOpened
  - SkillTest::theCheckLayerIsMeasuredAgainstACompleteOne
  - SkillTest::theDeprecationSweepRunsFromTheExtensionsSurface
---

# R-SKL-004 — An assessment establishes its base before opening the checkout

**An assessment establishes its base before it opens the checkout: the scope,
the owner tools, and the list of surfaces it will cover.**

That list comes from the audit surfaces rather than from the file tree. A
surface with no files is invisible to a listing, and its absence is usually the
finding.

A surface stated as what the repository declares is the file tree in another
form and fails the same way. So a surface that is a layer of checks names what a
complete one covers, and the assessment answers it entry by entry.

It asks the owner of a surface's conventions before it forms a view of it, not
afterwards to confirm one. It does not mistake a runtime lookup for that
question. What the registry holds and what a path resolves to are facts about
the installation, never a verdict on it. It reads what comes back against the
checkout in both directions. A file that has settled into the opposite of a rule
is a finding rather than a local style. It reports a surface it never asked
about as unassessed. A defect nobody looked for and a defect that is not there
look the same in a report that does not separate them. That is why it reports
the deprecation sweep when it comes back empty as well, with the majors it
covered.

A request that names the surfaces it is about narrows what the assessment reads,
never the list it closes on. The surfaces it left out stay on the list as not
requested. That is a different state from the unassessed one above, because
different people answer for the review's own gap and the reader's narrowed
request.

A finding about a user-controlled value holds to the same distinction one
surface further in. It is a claim about the sink. So it stands only once the
assessment names and reads the tag, attribute, header, statement or process the
value ends up in. An opt-out or a quoting helper on the way there is part of the
path rather than the end of it. Escape and injection are that same claim about
different sinks, so the gate stands once for both and the hints answer for the
sinks themselves.

## From

The second `REVIEW-01` run (2026-07-31), which followed two of the conformance
skill's seven evidence steps. It read the site package's three XLF files without
a question about what governs them. So it missed the German `source-language`
that
[`R-KNW-033`](../knowledge/knw-033-a-new-label-names-its-source-language.md)
already covers and a run on 2026-07-30 had already found in the same checkout.
The third run the same day sharpened it. That run read the checklist, then
listed the file tree and spent five minutes on it before it called
`typo3_task_guide` or any conventions lookup. It confirmed the translation
domain with `typo3_translation_domain_lookup` and filed translations under
"assessed and clean" with the German `source-language` header on screen. The
extension's absent `Documentation/` appeared neither as a finding nor as
unassessed. Two `REVIEW-02` runs in the same extension checkout on 2026-07-31,
at 12:21 and at 13:32, extended it. Neither produced a finding about static
analysis in a repository with no analyser, no analysis step and no baseline. The
second had run both declared checks and reported their ceiling instead. The
quality surface asked what the repository declares rather than what a complete
check layer covers. The `REVIEW-02` run in an extension a major behind the world
(2026-07-31) extended it again. Its one finding with an active security
consequence was an escape opt-out in a template. Every citation under it was
correct, and the output escaped anyway. The six call sites sit in a ViewHelper
that emits nothing, and the core wraps the resolved title in
`htmlspecialchars()` two classes further on. The run opened neither of them, and
did open the core ViewHelper that confirmed what it already believed. The
feedback of 2026-07-31 18:36 extended it a last time. It asked for a mode that
skips the surface list for a focused review, judged in
[`D-SKL-002`](../../decisions/task-skills/skl-002-a-focused-audit-narrows-what-is-assessed-not-the-list-it-closes-on.md).
There the skip is what the judgement refused and the read is what it cut.

## Held by

- `SkillTest::theCheckLayerIsMeasuredAgainstACompleteOne`,
- `REVIEW-01`, `REVIEW-02`, `SKILL-09`, `SKILL-11`. The last two measure the
  escape half and the focused half, because the test beside each of them reads a
  sentence rather than a review.
