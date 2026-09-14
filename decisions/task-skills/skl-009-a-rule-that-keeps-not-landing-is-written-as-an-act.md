---
id: D-SKL-009
title: 'A rule that keeps not landing is written as an act'
date: 2026-08-03
status: confirmed
coveredBy:
  - SkillTest::aReviewNamesTheSuitesItDidNotRun
---

# D-SKL-009 — A rule that keeps not landing is written as an act

**A rule three runs read and none followed becomes an act with a named argument.
What the skill gained without a run behind it comes back out.**

The skill stated the names of the suites a run left out as a property the report
should have. It was present at every length the skill has had, and no run has
ever produced it.

## Evidence

- Three recorded `REVIEW-03` runs report green suites and name none of the ones
  `typo3_test_run_guide` returned beside them. The rule was in `SKILL.md` for
  all three: `7553cb3` at 1009 words, `e6c1067` at 1165, `8c01355` at 1562. A
  failure at the shortest length is what rules out a skill too long as the cause
  of this one.
- What the sentence said was "Anything it did not run stays labelled as
  available rather than passing". The example after it was that "The tests would
  presumably still pass" is not a review sentence. Both are about a **claim**
  that an unrun suite passed. A report that simply omits the suites makes no
  such claim, so all three runs were compliant with the sentence and none with
  the demand.
- The fourth run did drop a call, and not the one under watch. It never called
  `typo3_hint_lookup`, which the run before it did. Between the two, `SKILL.md`
  grew from 1165 to 1562 words. The paragraph added directly beside that call,
  that the diff's content routes as well as its paths, had no run behind it. It
  came out of a reading of somebody else's review pipeline, not out of a session
  here.

## Decided

- The rule becomes an act with an object. The review writes out, by name, the
  suites on the guide's own list that it did not run. The consequence stands as
  what the omission does to a reader. The example is the omission itself rather
  than the false claim, since an unnamed suite is that sentence with the words
  taken out.
- The checklist's `Tests` surface says both halves answer it. So the demand is
  where the review closes the report against the surfaces and not only where it
  chooses the suites.
- The content-routing paragraph is removed. It was written from a reading rather
  than from a run, it sits beside the one call the next run stopped making, and
  nothing here can say it ever helped.
- The identifier paragraph in the Gerrit step shrinks from ten lines to seven.
  Every part of it that bears load survives. What goes is the restatement.
- What stays is what a run used: the two lookups and the `Change-Id`, the series
  reading, the dropped-candidate section, the three dispositions and the surface
  the review server answers. Each of those is visible in the fourth run's
  answer.

## Assumed

- That the wording is what failed rather than the placement. The sentence is the
  last one of the last paragraph of its section, and this change does not move
  it, so a fifth run that still omits the suites leaves placement as the reading
  that was not tried.
- That one dropped call is a signal rather than variance between two sessions of
  the same model.

## Wrong if

- The next run still reports its suites without naming the rest. The wording
  would then have been the wrong hypothesis twice, and what is left is where the
  sentence sits and how much stands in front of it.
- The next run no longer makes a call the fourth one made. That would say the
  section is over its budget whatever the sentences say, and the answer is
  subtraction rather than another rewrite.
- A run starts to list every suite the guide returned as unrun with no thought
  about any of them. That is the demand satisfied as a formality.

## Confirmed on 2026-08-03

The fifth recorded run wrote the list out where three before it read the old
sentence and produced nothing. So neither of the first two **Wrong if** holds
and the placement kept in reserve is not what the run needed. The second has a
better answer than its question. The hint call stayed uncalled after the removal
of the paragraph beside it, and the run's own answer says why. The hints came
back inside the brief, so the call is redundant on this task rather than lost.

What it leaves open is smaller. The run cites those hints as the lookup it never
called. The quotes of the rules are correct and came from this server, so it is
a mislabelled source. Nothing says the guide carries hints of its own.

## Since then

That last question has its answer. A run of the two payloads against each other
on this run's own call shows what the brief carries. It carries the four
strongest of `typo3_hint_lookup`'s hints for those paths, quoted statement for
statement. The lookup answers with three more. So the citation was right and the
gap is a sentence in the brief that says whose the hints are, which
[`D-GUI-007`](../guides/gui-007-the-brief-carries-a-selection-of-the-hints-and-says-whose-they-are.md)
adds. The reading above holds for this task and does not generalise: the four
that came back were the ones the findings rested on, and the three the call
would have added are the reason it stays in the skill.
