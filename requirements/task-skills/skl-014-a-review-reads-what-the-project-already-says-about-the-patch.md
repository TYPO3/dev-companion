---
id: R-SKL-014
title: 'A review reads what the project already says about the patch'
status: held
restsOn: [D-SKL-008]
heldBy:
  - SkillTest::aReviewReadsTheReviewThePatchIsAlreadyIn
---

# R-SKL-014 — A review reads what the project already says about the patch

**A review reads the issue the message resolves and the change on the review
server, each by its own number.**

It reads both before it reads the code a second time, and neither is in the
checkout. The issue carries what the change is *for*, which a commit message can
only report from the author's side. It is where a series announces itself. An
issue that calls itself a part decides every finding about what the patch lacks.
The review server carries the patch set that exists and any comment nobody
answered. That is the commonest reason a change sits unmerged and the finding a
second review makes twice.

The Forge issue and the Gerrit change are different numbers, and a swap answers
rather than fails. Both lookups return a real issue and a real change under the
wrong number, and neither payload says so. The subject is the check that catches
it. The change that comes back carries the subject of the commit under review,
or the number was wrong.

An answer of nothing is a result the review states. Where the commit in the
checkout and the change on the server differ, the review names which of the two
it read.

## From

The third recorded `REVIEW-03` run reviewed change 95070 without a request for
either. It closed its own report with the note that it had not fetched Forge
#110359. Both calls answered at once when the judgement made them. The issue is
"Avoid calling ImageService methods - part 2" with an empty description, and its
part 1 is already in `origin/main`. The run judged a series as a patch on its
own. Until then both tools existed and no skill routed to either.
