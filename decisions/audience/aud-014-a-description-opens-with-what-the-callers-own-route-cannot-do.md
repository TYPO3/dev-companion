---
id: D-AUD-014
title: A description opens with what the caller's own route cannot do
date: 2026-08-27
status: open
coveredBy:
  - SkillTest::theBriefOpensWithWhatTheCheckoutsOwnConventionsCannotSay
---

# D-AUD-014 — A description opens with what the caller's own route cannot do

**Where the caller's own repository hands out a route to the same source, the
tool's description opens with what that route cannot do.**

[`D-AUD-013`](aud-013-a-competing-route-is-corrected-where-it-is-written.md)
changed no surface of this server, because the one session on record had held
both routes and taken the tool. Its first **Wrong if** has since happened.

## Evidence

- [`feedback/2026-08-25-114714`](../../feedback/archive/2026-08-25-114714-typo3-forge-lookup-lost-to-the-raw-curl-recipe.md)
  queried Forge with three `curl` calls and two inline Python parsers while
  `typo3_forge_lookup` sat in its tool list. It names the recipe in the core's
  `AGENTS.md` as the reason it never considered the tool. One of those calls
  bought nothing but the knowledge that the endpoint answered at all.
- The description that session saw is the one `f1e047d1` left. It opens "Read
  the TYPO3 issue tracker at forge.typo3.org before writing a patch", which is
  what the recipe already promises.
- The clause the feedback asks for was in it. The last sentence of that
  description stands past every parameter the tool takes. It reads "An issue
  that does not exist is answered as such, and so is a tracker that could not be
  reached". So this is placement and not absence.
- The bot protection appears nowhere in the description.
  `ForgeLookup::UNREACHABLE` names it, which a caller reads once the tracker has
  already refused them.
- `.checkouts/main/AGENTS.md` lines 11 to 15, read on 2026-08-27, still carry
  the recipe and its Anubis caveat verbatim. Forge is the only entry of that
  Context list which hands out a route that works; Gerrit stands there as two
  URLs.

## Decided

- The judgement is
  [`documentation/records/judging.rst`](../../documentation/records/judging.rst)
  step 4, wording. The tool arrived, and its description did not take against a
  recipe that promises the same thing.
- The feedback is **queued** rather than closed on the spot, because the change
  is in `src/`.
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md)
  keeps that half of the line whatever the judging run holds.
- The surface is the description. Rejected: the feedback's other option, a
  `doesNotCover` entry that declares the `curl` route the intended one. The tool
  passes the bot protection and tells a miss from a refusal, so a boundary drawn
  there would be a false one.
- The statement goes at the front. A caller decides whether to call from the
  first sentence, and everything this tool does that `curl` cannot was already
  written further down.
- The priority is `normal`, which is what `D-AUD-013` set the other half at on
  what the file is. This is the same file with a measured failure behind it
  rather than a predicted one.
- Nothing holds it, so no requirement exists. The assertion would have to be
  that one sentence of one description says what a file in another repository
  leaves out. A check that reads for that is a keyword the next rewrite moves.

## Assumed

- That the first sentence is what a caller weighs a tool against a route it
  already has. The session says the tool's name alone did not beat the recipe,
  and it does not say how far into the description it read.

## Wrong if

- A session reports that it went around `typo3_forge_lookup` again once the
  first sentence says what `curl` cannot do. Then what wins is the file that
  arrives in the context rather than anything the tool list carries, and no
  lever remains on this side. The maintainer declined the core patch that would
  have corrected it at its source on 2026-08-27 as out of scope, which is what
  revoked
  [`D-AUD-013`](aud-013-a-competing-route-is-corrected-where-it-is-written.md).
- A feedback reports the first sentence as noise: a caller with no rival recipe
  reads a caveat where the tool's subject belongs.
- The core's `AGENTS.md` paragraph comes to name this tool. Then the first
  sentence answers something no caller hears the opposite of any more, and it is
  a sentence to withdraw.

## Since then

A second tool lost to the same file, and it is the one the `instructions` name
second. A session held the core's `AGENTS.md` in context and never called
`typo3_task_guide`, whose schema it had loaded. So this is not a single case,
and what the two share is a file read into every session in a core checkout.

The second first sentence stands. It names the two things that file does not
carry and the session guessed at. Those are whether the fix owes a changelog
entry, and the branches a `Releases:` trailer takes.
`typo3_commit_message_guide` already answered the second. The first stood in a
document no bugfix brief reached. So the `bugfix` change type gained the item
that states the rule and names the page. A first sentence that claims the answer
without it would have been the lie this repository's rule about outward surfaces
names.
