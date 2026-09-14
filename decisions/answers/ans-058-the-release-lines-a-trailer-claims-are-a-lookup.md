---
id: D-ANS-058
title: 'The release lines a trailer claims are a lookup'
date: 2026-08-05
status: confirmed
coveredBy:
  - CommitMessageTest::aBranchOutOfSupportIsAnErrorNamingTheLinesThatTake
  - CommitMessageTest::aBranchTheListDoesNotCarryIsAWarningSayingWhenItWasRead
  - CommitMessageTest::theMissingTrailerNamesTheLinesThatTakeAPatch
  - ReleaseLinesTest::theLinesTakingAPatchNarrowAsTheirWindowsClose
---

# D-ANS-058 — The release lines a trailer claims are a lookup

**Which TYPO3 branches the core maintains today, and what each one is, becomes
an answer this server holds. `typo3_commit_message_guide` validates the
`releases` it gets against it.**

The `Releases:` trailer is a required part of every core commit message, and
nothing here says what may stand in it. The tool asks the caller for a fact this
server does not offer anywhere, and accepts whatever comes back.

## Evidence

- `feedback/2026-08-05-033924`: a session that wrote a core bugfix passed
  `releases: ["main", "14.3", "13.4"]` and got "No commit message readiness
  issues found". A long-dead branch would have passed the same way.
- `typo3_rule_lookup` answered the changelog half precisely. A casual bug fix
  owes no entry, and a backport goes into the `<lts>.x` directory of the oldest
  branch it reaches. "The oldest branch it reaches" is the fact it cannot
  supply.
- The session established it with a count of trailers on 40 commits of
  `origin/main`: 19 `main, 14.3`, 11 `main`, 10 `main, 14.3, 13.4`. Then it read
  the changed file on both other branches to confirm the defect was there.
- That is inference from a sample, and the checkout does not answer it either:
  `git branch -r` lists everything back to `TYPO3_3-6`. A session with less
  caution would take the branch list for the answer.
- It is the class of fact a knowledge server exists to hold. It goes stale on a
  release day, and every core change carries it.

## Decided

- **Taken on.** The judgement is step 1b, an absent shape: the answer is
  available in the world, and there is no way to get it here. What each line is
  — dev, sprint, LTS, ELTS, security-only, ended — is what makes the trailer a
  claim rather than a habit.
- The boundary is the maintained lines and what a change type owes each. It is
  not a release calendar, not a support-window product page, and not advice
  about which branch a patch should target. That last is the author's view of
  where the defect is.
- `typo3_commit_message_guide` turns the argument into a check. A branch that is
  not a maintained line is a finding. A trailer that names nothing keeps the
  placeholder it already has.
- What the entry says about TYPO3 waits for the read. Where the fact comes from,
  get.typo3.org, the core's own branch list, the release notes, is the first
  step of the todo. This judgement does not decide it from recall.
- It stays a claim the author has to have verified. The check says the core
  maintains the branch; it cannot say the defect is on it. For the reported
  patch that meant a read of the changed file on `origin/14.3` and
  `origin/13.4`.

## Assumed

- The maintained lines are readable from a source that stays current. If the
  only honest source is a page somebody edits, this becomes a statement that
  ages exactly like the one it replaces.
- A wrong `Releases:` trailer costs something. It is caught in review today, so
  what the check buys is the round trip rather than the defect.

## Wrong if

- The lookup exists and sessions still count trailers in the checkout, which
  would say the answer arrives too late in the task.
- The list goes stale between releases and a check starts to fail valid
  trailers, which is worse than the silence it replaced.
- What a change type owes each line turns out to be a judgement rather than a
  rule. Then the check can only report the branch and not the set.

## Confirmed on 2026-08-05

The read happened and the assumption held. The API answers one entry per major
with both dates, which is what the corpus now carries. It knows nothing about
the development line, so the corpus states that one rather than reads it.

Two more properties decide what a re-read finds and neither shows without a hit
on it. Its prose is stale in the direction that matters. A reader who takes it
for the state gets the current LTS backwards, so only the dates bear load. Its
release list carries strings that are not versions, so anything that sorts that
list filters first. The checkout confirms the API rather than replaces it: a
line's public branch stops when regular support ends.

## Since then

The first **Wrong if** has its event and it says less than it reads. A session
established the maintained lines from a remote list and a directory list
nineteen days after this lookup landed. It reported that the server offered no
way to check them. It reached the right lines and said itself that the inference
fails on a shallow clone or a stale remote.

What it does not show is that the answer arrives too late in the task. It called
three tools, opened no skill and never called the brief.
