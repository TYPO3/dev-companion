---
id: D-SKL-033
title: Whether a skill is activated is the client's and the model's
date: 2026-08-11
status: open
readings:
  - 2026-09-09
coveredBy:
  - SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn
---

# D-SKL-033 — Whether a skill is activated is the client's and the model's

**A skill's description serves the listing a client reads it in. Nothing here
forces an activation the client and the model decide.**

A description that opens with the request's own words arrived whole and did not
fire, so the wording is not what remains to suspect.

## Evidence

- **The session.** `/home/benji/projects/typo3-cms` on 2026-08-10,
  `claude-opus-5` in Claude Code, transcript
  `d7b5b468-5ef2-4e9e-81df-27a4afbdce07`. The whole brief was "bitte review mir
  den patch [TASK] Keep the docheader navigation row sticky". Thirteen `Bash`
  calls followed: `git show`, four repository greps, a branch listing, four file
  windows and a failed CSS build. `typo3-core-patch-review` activated at call
  14, after the user interrupted with "warum fragst du das review tool nicht
  an?".
  [`feedback/archive/2026-08-10-182404`](../../feedback/archive/2026-08-10-182404-a-review-request-quoting-the-skill-s-own.md)
  is that session's own report of it.
- **The listing arrived, described.** The `skill_listing` attachment carried
  9500 characters with a description on every entry, the twelve published here
  among them.
  [`typo3-core-patch-review`](../../skills/typo3-core-patch-review/SKILL.md)'s
  is byte-identical to the one in this checkout today, all 256 characters of it,
  and opens "Review a TYPO3 core patch". The German sentence's only content
  words are `review` and `patch`, which are two of those first four.
- **The instructions arrived, whole.** The `mcp_instructions_delta` opens with
  `Installer::NOTICE` — the project's published skills were stale — and then
  with "Start every task with typo3_project_describe", naming `typo3_task_guide`
  as what "hands the parts that have their own workflow to the skill that owns
  them". The session called neither tool before call 14.
- **What the twelve cost today.** 3537 characters of listing against the 3600
  `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn` allows, measured on
  2026-08-11.

## Decided

- The description stays as it is. Its first four words are the request's own
  two, and no rewrite of it matches this brief harder.
- Against trigger words in another language, here and in the other eleven.
  Everything this server publishes is English and it says so three times: in the
  `instructions`, in `typo3_server_scope` and in the last section of
  [`skills/base.md`](../../skills/base.md). So a German word in a description is
  a second vocabulary to keep true in twelve installed files. It buys a request
  whose matched words were English already.
- Against more request shapes. The two sides a review arrives from are in the
  description already, and `R-SKL-010` is what put them there. 63 characters is
  what the ratchet has left.
- What this run queues instead is step 3 of the order, which the same transcript
  shows skipped after the skill was active — `D-SKL-015`.
- **The channel that was not tried, named so nobody rediscovers it.** The
  installer writes `.mcp.json` and the published skills, and nothing into the
  project's own agent instruction file. That file is the one place left that
  reaches a session before its first turn without a call. This entry does not
  take it on this evidence. The `instructions` reach a session that way already,
  they were in this one's context from the first turn, and they bought thirteen
  calls of nothing.

## Assumed

- That the listing this session received is the one other sessions receive. This
  entry read it in one client on one day. 9500 characters is above the 6000
  `D-SKL-026` computes for a 200k session on this model generation. So either
  the window was larger or that arithmetic has moved, and nothing established
  which.
- That a model reading `- typo3-core-patch-review: Review a TYPO3 core patch …`
  against "bitte review mir den patch" is choosing rather than failing to match.
  Nothing here can see the choice, which is why this entry stops at what
  arrived.

## Wrong if

- A transcript shows the same request shape arrive where the listing dropped or
  truncated the description. Then the budget was the obstacle after all, and
  `D-SKL-026`'s arithmetic is what to act on rather than the wording.
- A description rewritten for another reason starts to activate on a shape it
  did not carry before. Then the wording carries more than this entry allows it,
  and the shapes are worth their characters.
- A third session reports a skill that stood in the listing, described in the
  request's own words, and stayed shut. Then the project's own instruction file
  is the channel to weigh, and this entry is what recorded it as untried.

## Since then

Ten sessions of the counted shape are on record and none of them moves
**Decided**. What the readings weighed and did not take is the project's own
agent instruction file as a channel. What moved instead is the mood of one
sentence, which `D-AUD-012` carries, because the position is the base's own
order. A benchmark of eighty-two runs, every skill listed beside the connected
server and none used, is the third **Wrong if**.

The rows since then bound it rather than widen it. The shape arrives outside a
core checkout too, and it arrives in German, where one word of the brief is the
whole of the match. One row names its own cause, read off the session's account
rather than off a silence. What it names is outside anything this server
publishes. A description competes for the model's attention with everything else
in the context, and nothing here reaches that.
