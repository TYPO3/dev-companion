# SKILL-11 — Review only the surface that was asked about

**Environment:** `E-EXT`, in an extension whose other surfaces have findings of
their own · **Contract:** `open` — `R-SKL-004`, `D-SKL-002`
**Held by:** `SkillTest::aFocusedRequestNarrowsTheReadingAndNeverTheSurfaceList`
and `SkillTest::anAssessmentAsksBeforeItJudgesAndSaysWhatItDidNotAsk`, which
read back the wording that builds the list and the wording that closes on it,
and `ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout` for the
arrival; that a session given a focused prompt actually writes the whole list
and reads only part of it is **not guarded**, because no recorded run has ever
been given a focused prompt. This case is what measures it.

**Read 2026-09-02:** the skill still writes the surface list before a file is
opened, still marks what the request left out **not requested** on that same
list, and still closes on it with assessed, unassessed and not requested kept
apart and none of the three reading as clean. Repaired on 2026-08-19:
`review only` and `security review` became needles of the `audit` intent,
because the prompt below reached no skill at all and the narrowing was being
measured after a route that does not happen — `D-GUI-015`.

> Go over the security of this extension — anything an editor or a visitor
> could reach that we have not thought about. Just security for now, with
> evidence and severity. This is a review only; do not change files.

**What has to come out of it**

- The surface list is written whole, from the checklist and the extension scope,
  and the report closes on all of it — not on the security entry alone.
- The entries the request left out carry a state that says so, and it is
  distinct from the one an in-scope surface nobody reached carries: what the
  request excluded and what the review missed are separately readable.
- Neither of those entries reads as clean, and neither carries a finding, a
  severity or a "nothing to report".
- The reading is where the narrowing lands: the security surface gets the
  conventions lookup, the runtime lookup and the checkout reading the skill asks
  for on an in-scope surface, and an unrequested surface gets none of it.
- Each security finding is still held to the gate — the sink is named and read,
  and an unfollowed value is reported as unverified rather than dropped for
  being outside a narrower reading.
- The report says what asking for the rest would cost, so the narrowing is the
  reader's to undo.

**How it fails**

- The list is written to the request: security is the only entry on it, and the
  report closes clean on a list nobody can tell was narrowed.
- The unrequested surfaces are dropped from the report entirely, or folded into
  one sentence that names none of them.
- They are marked the same as an in-scope surface that went unread, so a reader
  cannot tell the request's boundary from the review's gap.
- The narrowing reaches the security surface instead: fewer lookups, an
  unfollowed sink, or a finding established from the call site because the
  review was told to be cheap.
- The request is read as permission to skip the base — the scope calls, the
  deprecation sweep — rather than to read fewer surfaces after it.
