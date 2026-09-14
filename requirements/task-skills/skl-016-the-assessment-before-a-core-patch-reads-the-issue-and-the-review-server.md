---
id: R-SKL-016
title: 'The assessment before a core patch reads the issue and the review server'
status: held
restsOn: [D-SKL-010]
heldBy:
  - SkillTest::theAssessmentBeforeAPatchReadsTheIssueAndTheReviewServer
---

# R-SKL-016 — The assessment before a core patch reads the issue and the review server

**An assessment comes before a core patch. It asks the tracker for the issue and
the review server for whether somebody has already fixed it.**

Neither answer is in the checkout, and neither is in the report a session starts
from. The tracker adds the status and the target version as they stand today. It
adds the relations, which are one hop from the change that introduced the
behaviour under complaint, where a query on the words misses it. It adds the
notes, which is where a closure, a reassignment and a maintainer's reason live.
The review server answers "has somebody already fixed this" for one call. The
session asks before it writes any code, because the outcome that cancels the
work is worth nothing once the work ends. The read is anonymous, so an answer of
nothing says that nothing public names the issue rather than that nobody has
fixed it.

Three rungs go with the two calls, because each is a judgement the assessment
makes and the order carried none of them. The session checks a deferred decision
against what the branch has today before its blocker counts. The argument that
carries a bugfix is the same inconsistency inside one version, which is what
separates a defect from a wish. The session establishes the blast radius during
the assessment, because it decides the change type and everything downstream of
it.

This is
[R-SKL-014](skl-014-a-review-reads-what-the-project-already-says-about-the-patch.md)
on the other side of the same work. The review reads both surfaces by the
numbers the commit message carries. The session about to write the patch has
only the issue number, and it is the one the answer can still spare the work.

## From

Five sessions of one cluster, all on Forge #105403 (2026-08-02). Four ran both
lookups by hand: `feedback/2026-08-02-144511`, `144848`, `145217`, `145230`. The
fifth, `feedback/2026-08-02-145128`, filed the assessment method it had had to
rediscover, of which the three rungs are the part no order here carried. That
session treated a 2024 objection as current after the API it rested on had
arrived. It characterised the change before it found that it moved about 141
expectations across 23 files. It reached the change that introduced the
behaviour through a tracker search on the feature words rather than through the
reported issue's own relations.
