---
id: D-ANS-088
title: 'The orientation answer is asked for by section'
date: 2026-08-19
status: open
coveredBy:
  - ScopeTest::aCallThatNamesOneSectionIsAnsweredWithThatSectionAlone
  - ScopeTest::namingNoSectionAnswersEverythingTheToolHas
  - ScopeTest::noSelectionHidesWhatTheCallerExcludedOrWhatThisServerIsFor
---

# D-ANS-088 — The orientation answer is asked for by section

**`typo3_server_scope` takes a `sections` argument, answers only the parts a
caller names, and says which parts it left out.**

It answers whole where the caller names nothing, which is the call this tool
exists for. The argument is for the other caller: one that already knows which
part of the orientation it lacks.

## Evidence

- `feedback/2026-08-17-205904` is the filed session the card said did not exist.
  It called this tool from an empty directory and measured the answer at roughly
  11,000 tokens. That was the largest single result of a build that cost $29.59,
  about $0.88 amortised over the 148 requests it stayed in context for. It
  reports that it changed no decision the session took. What it needed was
  whether an installation and a console were in reach at all.
- `feedback/2026-08-18-080743` is the second session on record that called it.
  It reports the answer as accurate and names nothing it decided. Both sessions
  read the whole catalogue for a question that is a part of it.
- Measured with `bin/cli tools:measure` on 2026-08-19: 93,994 characters over
  one call, the heaviest single call this server has. The recorded pair against
  `.checkouts/14.3` is 94,198 characters whole and 7,734 for
  `sections: ["installation"]`, twelve times less.
- `D-ANS-083` measured the part the session was after at 389 characters, under
  1% of the answer it arrived in. Its third **Wrong if** is this entry.
- [`D-FBK-020`](../feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md)
  prices calls rather than tokens. That is what makes the default the whole
  answer and the argument a narrower answer rather than a paged one. A caller
  that has to ask twice has paid more than it saved.

## Decided

- **The sections are the payload's own field names**: `covers`, `doesNotCover`,
  `checkoutDiscovery`, `routing`, `versions`, `answersFrom`, `installation`. A
  caller who asks again names the field it already has in hand, so there is no
  second vocabulary to guess at.
  [`D-GUI-015`](../guides/gui-015-a-cases-own-prompt-reaches-less-than-the-brief.md)
  measured what that costs.
- **Naming none is the whole answer.**
  [`D-ANS-087`](ans-087-the-project-answer-stays-whole-because-a-call-is-what-costs.md)
  holds for the caller this tool serves. One that does not know what the server
  covers cannot name the part it wants, and would choose in its least informed
  moment. A caller that can name one is the caller `D-ANS-083` measured.
- **No selection reaches what
  [`R-SCO-009`](../../requirements/scope/sco-009-individual-tools-can-be-excluded.md)
  protects.** The purpose, the initialize instructions and the exclusion report
  are outside the section list and travel with every answer. So the argument
  cannot reopen through a parameter the hole the exclusion list stays away from.
  So does the sentence that tells the agent to query in English, which is the
  whole mitigation for a lexical matcher over an English corpus.
- **A withheld section is absent rather than empty, and `withheld` names it.**
  An empty `covers` reads as a server that covers nothing, which is the failure
  the installation diagnostic moved into the payload for. `withheld` carries
  what each absent part would have held, so nobody can read a narrowed answer as
  the whole one.
- **The console is not resolved where `installation` was not asked for.** It is
  the one part of this answer that costs seconds rather than bytes: two
  `ddev describe` calls, 0.869s against a stopped project on 2026-08-04.
- **The output schema requires three fields where it required eight.** A whole
  answer still carries every field it did. So only a caller that passes
  `sections` meets the difference, and it met it because it asked.
- **Rejected: the name `topics` for the argument.** Every `covers` and
  `doesNotCover` entry already carries a `topic`. A caller who passes one of
  those as a section name would make a different call than it thought.
- **`typo3-backend-module-development` is the first caller**, and it was one
  before this. Its step asked for the knowledge depth available and any tool the
  caller excluded, which is `covers` and what no selection can take away. It now
  names `sections: ["covers"]` and reads about a twelfth of what it did.
- **Nothing changes in `typo3-development-installation`.**
  `typo3_project_describe` discharges its `typo3_server_scope` step, and
  `D-ANS-083` records why that survives a cheap form. The answer arrives in the
  first call's own payload, so the second one fetches what the caller holds.

## Assumed

- That a caller who needs one part names it. Nobody has watched a session do it;
  the measurement covers two sessions that took the whole answer for a part of
  it. `D-AUD-011` carries the same assumption about its index.
- That the purpose is worth its 1,318 characters in a narrowed answer. It is the
  largest thing this entry keeps always on. What it buys is a text half that
  stands on its own whatever the caller named.
- That the whole answer stays the same. A test asserts it rather than assumes it
  for the text, which is byte-identical, and the data half gained `withheld`
  alone.

## Wrong if

- A session names a section, gets its answer, and reports it needed a part it
  did not name. Then the cuts in the answer fall in the wrong places, and what
  it names is the evidence this entry lacks.
- A session narrows and then reasons about the boundary from what came back.
  Then `withheld` does not do the work and the whole answer is the only safe
  shape.
- Nobody but the skill passes `sections` over the sessions that follow. Then the
  argument is a surface nobody chooses off. `D-ANS-087`'s reasons covered this
  tool too, and what a skill prescribes is the whole of what it bought.
