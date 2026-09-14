---
id: R-SKL-028
title: A widened request re-establishes what the patch is and what it owes
status: held
restsOn: [D-SKL-079]
heldBy:
  - SkillTest::aWidenedRequestReEstablishesWhatThePatchIsAndWhatItOwes
---

# R-SKL-028 — A widened request re-establishes what the patch is and what it owes

**Where the request widens after the patch is under way, the session
re-establishes the kind of change, the branches it reaches and what it owes.**

The session settled each of the three against the narrower request, and each can
flip on a widened one. A change that gains a second subsystem gains that
subsystem's build, its checks and its backport constraint with it. None of that
derives again from a continuation.

To say which of the three moved is what separates the re-establishment from a
repetition. A widened request that moves none of them is an answer too, and it
is the one that lets the work continue.

This is [R-SKL-027](skl-027-a-core-patch-covers-every-point-its-issue-lists.md)
in the other direction. That one holds the patch to the list the issue already
carries. This one is for the list the request grows after the assessment is
over.

## From

`feedback/2026-08-24-225243` (2026-08-24), a session on Forge #93177 whose task
grew eight times, twelve of its twenty user turns mid-tool-call. It derived its
own scope again four times. One patch or two, which release lines, whether the
client was in it, whether it owed an entry. It threw away two rounds of
client-side work and a changelog entry it had written and matched against
precedent. Both rules that would have carried it were in the skill and both
stood for the assessment.
