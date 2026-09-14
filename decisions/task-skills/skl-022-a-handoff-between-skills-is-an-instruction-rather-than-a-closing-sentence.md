---
id: D-SKL-022
title: A handoff between skills is an instruction rather than a closing sentence
date: 2026-08-07
status: confirmed
coveredBy:
  - SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor
---

# D-SKL-022 — A handoff between skills is an instruction rather than a closing sentence

**Three sessions crossed from one core workflow into the next and none of them
opened the skill that owns the second. Two had read the sentence that names
it.**

## Evidence

- `feedback/2026-08-07-065244`. `typo3-core-issue-triage` activated and carried
  the verification. Its closing paragraph names `typo3-core-patch-development`,
  says what crosses over, and the session states that it read it. The user then
  asked for the patch. The session wrote it over roughly forty more turns and
  never invoked the skill. It decided for itself the changelog obligation, the
  suites and databases, the trailers, the release branches and the Gerrit hook.
- `feedback/2026-08-07-132559`. `typo3-core-patch-review` activated and fitted.
  Its closing paragraph names `typo3-core-patch-development` and says a review
  does not change the patch. The user accepted the findings and asked for the
  change. The session edited `ColumnMap.php`, wrote a functional test, ran seven
  suites and amended the commit, still under review rules. It says there was no
  moment at which anything marked the crossing.
- `feedback/2026-08-07-130022` is the same failure from the other side. No skill
  activated at all for "bitte rebasen". `typo3-core-patch-checkout` covers it,
  since its description carries "rebase it where the branch has moved under it".
  But every noun around that phrase is about a change fetched from
  review.typo3.org. The session read it as somebody else's patch and did not
  open it, and on that description that read is correct.
- The three are two different failures with one consequence. Two skills name
  their successor in prose that a model reads and does not act on. One skill's
  description does not describe the task that reached it.
- `D-SKL-021` already settled that a triage and a fetch of a patch end before
  somebody else's act. This is the crossing that follows, and nothing carries
  it.

## Decided

- A sentence in a skill body is documentation of a boundary, not a transition.
  The client selects a skill on its description, and prose inside an active
  skill competes with everything else in the window. Two sessions with exactly
  the handoff it describes in hand is what says so.
- So the crossing stands as an instruction the session performs, invoke the
  named skill, at the point the trigger occurs. It is no closing paragraph about
  ownership. The closing paragraph stays; it is what tells a reader where the
  boundary is.
- `typo3-core-patch-checkout` is a description problem and not a handoff one.
  Whether it widens to cover a commit the session wrote itself, or
  `typo3-core-patch-development` gains the rebase-before-push step, is the
  question the todo carries. The session offered both and this entry decides
  neither.
- The cost is not hypothetical. What the first session improvised includes the
  changelog obligation, which it answered from its own knowledge. `D-ANS-061` is
  the same obligation that did not arrive by another route in a different
  session.

## Assumed

- What did not fire would have helped. Neither session reports a wrong outcome
  from the improvisation, and the first says everything it decided held. The
  claim is that the session reconstructed the order, not that it reconstructed
  it wrong.
- An explicit instruction fires where prose did not. That is the shape
  `D-AUD-003` already argued for the entry point, and no test covers it for a
  crossing between two skills.

## Wrong if

- A session reports that it invoked the successor and found the two skills'
  instructions in conflict while it held both. That would say the crossing needs
  the first to close rather than the second to open.
- A session reports that a wider `typo3-core-patch-checkout` pulled it into
  sessions that wanted the review-server workflow. That would say the two tasks
  are one description apart for a reason.
- A session with no client-side skill invocation at all reports the same
  crossing. That would say the lever is in the tools rather than in the skills.
- A session reports that it switched on a sentence that reaffirmed a finding,
  after the counter-case is in the skill. That would say nobody can draw the
  boundary in prose at all. The crossing has to be a question the session asks
  rather than a trigger it recognises. Written on 2026-08-11, from the read
  below.

## Confirmed on 2026-08-09

One session held both forms twenty turns apart and each behaved as predicted. It
invoked the successor at the moment the verdict turned into a patch. Then it
finished a push-ready patch and never invoked the review, whose crossing stands
as ownership. That settles the second **Assumed** — the two forms were
distinguishable in one session, on one task.

The crossing it did not fire on is the one the requirement never covered. Across
the skills it is the only sentence of its kind. It also narrows the proxy
`D-EVI-002` accepts. A named successor is not what holds a crossing, since a
paragraph the session held named this one. What an assertion has to read is the
imperative.

## Since then

The review crossing then fired on a sentence that commissioned nothing. The
reader meant that an absent test was reason to reject the patch. The session
switched, backed out and re-ranked the finding, one turn under the wrong skill's
rules. That is the price of this entry rather than a case against it, and it
shows which half an imperative leaves open. That crossing described its trigger
as "a sentence in a conversation" and named nothing that tells two of them
apart. The triage crossing names the instruction and has never fired early.

So the crossing names the instruction that fires it and the remark that does
not. Where the sentence could be either, the session asks rather than switches.
Nothing holds that half, because which sentences a trigger excludes is a read of
the workflow.

## Since then

The rule the crossing guards held under the pressure it exists for. A review
names four things it could have fixed on the way past. It reports that what
stopped it is the sentence that says a session across that line looks like
nothing from the inside. Nobody asked it for the change, so it is evidence about
where the boundary runs rather than a confirmation. What it settles is which
half of the paragraph does the work: the concrete failure mode, not the
ownership sentence above it.

## Since then

A session read the imperative and nothing crossed. It followed a skill to
completion and quoted its closing sentence that names three successors, and none
of the three fired. No test written, three READMEs by hand, and ten defects the
user listed himself. That is what the 2026-08-09 read got wrong. It counted
`Activate <skill>` as already an act, so the requirement covered three core
crossings and none of the extension ones. What the two that fired carry beside
the imperative is the moment. The sentence that failed names three successors,
no moment, and sits where a session leaves a workflow. `D-SKL-053` is the
judgement.

## Confirmed on 2026-08-22

Two visits held this decision and changed nothing, both of them a build of the
crossing. The two skills that end at the patch say to invoke it at the point the
crossing happens. The moment has a name and the ownership paragraph stays. The
question the third **Decided** bullet left open got the answer *both, with a
pointer*. That is where the rebase-before-push step and its two parts came from.
And the crossing in the other direction landed. That is why the test reads a
successor per skill rather than one name for all of them.

## Since then

An extension-side crossing fired, the first since the three that did not. A
session loaded the testing skill before it wrote a functional test. It named the
section that opens with the imperative and the moment beside it. That is what
the three-successor closing sentence lacked. Nobody asked it about the crossing,
so it is evidence about the boundary rather than a confirmation. What it places
is which of the two ways into a skill worked. The crossing inside an active one
fired. The client's own listing reached the same session with four descriptions
that match and opened none.

## Since then

A session met the crossing *into* the checkout skill three times and crossed it
never. It had one of the other two core skills in hand for two of them. So the
tool was in reach and what stayed shut was the third. Neither of the two tells a
session to open it. One names it in a sentence about who owns what, the other
not at all. The session quotes that sentence as read and attributes it to the
wrong skill.

So the 2026-08-09 sweep missed an edge by direction. It read the crossings that
run *out of* the three core skills and never the one that runs *into* this one.
What that costs is a fetch that arrives as a step inside two other workflows and
never as the task. That is the only shape a client could choose a description
on.
