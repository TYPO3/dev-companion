---
id: D-FBK-019
title: A secret pasted into a feedback is taken out on the way in
date: 2026-08-02
status: open
coveredBy:
  - FeedbackTest::aLongBase64ValueIsTakenOutAndAWordIsNot
  - FeedbackTest::aPasswordInADatabaseUrlGoesWithoutTheHostGoingWithIt
  - FeedbackTest::aValueGoesAndTheNameThatSaysWhatItWasStays
---

# D-FBK-019 — A secret pasted into a feedback is taken out on the way in

**`typo3_feedback_record` copies the observation it gets into a tracked file as
it is. Nothing tells the session that a value the installation keeps secret may
not go in there.**

The feedback judged on 2026-08-02 praised a tool. With the praise, it pasted the
live encryption key of the site it had just audited into this repository.

## Evidence

- What the feedback says.
  `feedback/2026-07-31-185900-after-the-audit-i-invoked-typo3-dev-companion.md`
  reports a success. `typo3_configuration_lookup` with path `SYS/encryptionKey`
  returned the effective runtime value, which turned an inferred audit finding
  into an established one. Its observation quotes the 96-character key verbatim
  — once, and this entry said twice until 2026-08-02, which **Since then** reads
  back.
- The behaviour it praises still stands. Re-run on 2026-08-02 against the server
  as it is now, over stdio with
  `TYPO3_DEV_COMPANION_ROOT=/home/benji/projects/site-new`. `SYS/encryptionKey`
  came back `found: true`, `answeredBy: installation`, a 96-character string;
  `SYS/trustedHostsPattern` came back `.*.*`. Nothing about the observation half
  is out of date.
- The suggestion half is already delivered. It asks for a more prominent
  advertisement of the tool, and for the conformance skill in particular. That
  skill already names it. `skills/typo3-extension-conformance/SKILL.md` under
  the lookup that owns a surface's runtime facts, `skills/base.md` in "Two kinds
  of lookup". And `knowledge/server-scope.json` in the routing entry "Needing a
  configuration value as it really is at runtime". Step 2 of the ladder,
  delivery, is where this one stops.
- What actually cost that session the calls is a different report. The same
  audit, at the same minute, filed
  `feedback/2026-07-31-185900-during-an-audit-of-the-printworks-3d-site.md`. No
  tool was callable in its environment at all, and it found the binary only
  after it had written the audit. That feedback is open and has its own card.
  The suggestion here names the cause the session could see from where it stood.
- The file is in git and on the remote. `git ls-files feedback/` counts 208
  entries, this one among them, and the checkout has a GitHub remote.
- One occurrence, not a pattern. A search of `feedback/` for hexadecimal runs of
  40 characters or more returns two hits. This key, and a git revision quoted in
  an archived entry. That is why it stands here rather than as a permanent leak.
- The tool invites it. `FeedbackRecord::inputSchema()` asks the session to be
  "specific enough to act on later" in `observation`, and describes `query` as
  the arguments that produced the result. Neither says anything about values
  read out of the installation, and there is no other field for a session to put
  its evidence in.

## Decided

- The feedback is archived. Both halves have their answer. The second run
  answers the observation; the advertisement already in the skill that was
  active answers the suggestion. Nothing it asks for is open, so the *close*
  answer applies and no todo serves it.
- The secret goes to the queue as work of its own rather than closes on the
  spot. It changes a tool's declared schema and description, which a judgement
  run does not improvise.
  [`R-FBK-011`](../../requirements/feedback/fbk-011-a-recorded-feedback-carries-no-secret-out-of-the-installation.md)
  says what must hold and a todo carries the next step.
- What the guard is stays open. Wording on the fields, a refusal in
  `Channel::record()`, a redaction pass, or the wording alone. A judgement run
  has established the gap, not the fix.
- The recorded file was not rewritten. Editing the key out would leave every
  commit that carries it untouched and would alter a session's report, which the
  archive exists to keep. Rotating the key belongs to whoever owns that site,
  and it is not an action this repository takes on its own.

## Assumed

- That the session pasted the key because `observation` read to it as the place
  its evidence goes. Nothing asked it, and the session has ended.
- That one occurrence is the first rather than the first noticed. The search
  behind that was a hexadecimal-length grep. It finds a key and would walk past
  a password, a host name or a token that is shorter.
- That a sentence in a field description reaches a session that writes feedback.
  `D-AUD-003` records the opposite for tool descriptions at initialize. A
  session reads this field at call time rather than at startup, which is a
  different position and not a measured one.

## Wrong if

- A second recorded feedback carries a value out of an installation. Then the
  sentence is not enough, and the channel has to refuse or redact rather than
  ask.
- A session works around the wording with the value in `query`, which is the
  other field it would read as the place its evidence goes.
- Feedback gets vaguer after the change, observations that name no value, no
  path and no version, because the guard read as "paste nothing". Then it cost
  more than the leak it prevents.
- The suggestion half turns out to have been right after all. A later session
  reaches the conformance skill with every tool callable and still does not ask
  `typo3_configuration_lookup` about a configuration claim. Then this was step
  4, wording, and the skill names the tool and never says when it decides a
  finding.

## Since then

**The guard arrived before the first Wrong if fired**, in the commit that
carries this paragraph. The absent wait is the whole of what changed. What this
entry doubted is that a field description reaches a session at call time. The
session that pasted the key was in the middle of its proof that the tool returns
the live value. So a sentence that asks it not to would have stood against its
own work. The trade is not symmetric either: a wait means a wait for a second
live credential in a repository on a remote.

One line of the evidence was wrong: the key stood once rather than twice. Every
threshold ran over the whole corpus first, because the cost of this guard is a
rule that redacts what feedback consists of.
`FeedbackTest::theRulesTakeNothingOutOfTheCorpusButTheKeyTheyWereWrittenFor`
keeps that measure.
