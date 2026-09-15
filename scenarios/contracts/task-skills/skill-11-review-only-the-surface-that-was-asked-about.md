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

**Read 2026-09-02:** the skill still writes the surface list before it opens a file. It still marks what the request left out **not requested** on that same list. It still closes on it with assessed, unassessed and not requested kept apart and none of the three read as clean. Repaired on 2026-08-19: `review only` and `security review` became needles of the `audit` intent. The prompt below reached no skill at all, so the measure of the narrow review came after a route that does not happen (`D-GUI-015`).

> Go over the security of this extension — anything an editor or a visitor
> could reach that we have not thought about. Just security for now, with
> evidence and severity. This is a review only; do not change files.

**What has to come out of it**

- The surface list comes whole, from the checklist and the extension scope. The
  report closes on all of it, not on the security entry alone.
- The entries the request left out carry a state that says so, and it differs
  from the one an in-scope surface nobody reached carries. A reader can tell
  what the request excluded from what the review missed.
- Neither of those entries reads as clean, and neither carries a finding, a
  severity or a "nothing to report".
- The read is where the narrow review lands. The security surface gets the
  conventions lookup, the runtime lookup and the checkout read the skill asks
  for on an in-scope surface. An unrequested surface gets none of it.
- The gate still holds each security finding. The review names and reads the
  sink, and reports an unfollowed value as unverified rather than drops it as
  outside a narrower read.
- The report says what asking for the rest would cost, so the narrowing is the
  reader's to undo.

**How it fails**

- The list follows the request. Security is the only entry on it, and the report
  closes clean on a list nobody can tell was narrow.
- The unrequested surfaces vanish from the report entirely, or fold into one
  sentence that names none of them.
- They carry the same mark as an in-scope surface that went unread, so a reader
  cannot tell the request's boundary from the review's gap.
- The narrow review reaches the security surface instead. Fewer lookups, an
  unfollowed sink, or a finding established from the call site because the user
  told the review to be cheap.
- The review reads the request as permission to skip the base, the scope calls,
  the deprecation sweep, rather than to read fewer surfaces after it.
