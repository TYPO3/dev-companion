---
id: D-CAT-010
title: The catalog scope is kept and the miss carries its own re-check
date: 2026-09-02
status: open
coveredBy:
  - CatalogTest::aCatalogMissCarriesItsOwnRecheck
---

# D-CAT-010 — The catalog scope is kept and the miss carries its own re-check

**The catalog scope answers for two catalogs and the retirement measured one. So
the tool stays and the miss gains the one string it lacked.**

## Evidence

- A component miss already carries `componentSource`, and `catalog` with the
  repository, the branch, the version, the commit, `verifiedAt`,
  `installedVersion` and `skew`. Its text carries `CatalogMeta::line()`. That
  says what the snapshot is, that a miss on it means "not in this snapshot" and
  that the checkout is what settles it.
- A note sat in that same text block. It said "Call `typo3_snapshot_scope` for
  what this snapshot covers." Of what that call returns, the miss held
  everything but `verifyCommand`, `scope` and `counts`.
- `Meta::read()` has two catalogs: `components` and `systemExtensions`. No
  component answer reports on the second one, and
  `typo3_system_extension_lookup` has no provenance block of its own.
- `feedback/archive/2026-08-31-234323` lists the tool among nine never called in
  a five-hour session. It lists it under "unused with no particular regret",
  beside eight others, and reports no question it went unanswered on.

## Decided

- The tool stays. The retirement proposal stood against the component miss
  alone, and the read that produced it did not reach the second catalog.
  `D-SCO-011` settled its verb, and a name clients know does not go to save one
  string.
- `verifyCommand` moves into `Provenance::catalogRecord()`, so every answer with
  the pin carries the command that checks it again. `Schema` declares it beside
  the rest of the provenance.
- That note goes. On the bundled branch the miss now says nothing extra. The
  snapshot line under it already says what a miss means and what settles it. The
  round trip was for a string that is now in the record.
- The `doesNotCover` entry stops to send the caller to the tool for which source
  answered, which the miss states. It names the read that remains: the installed
  backend CSS, or the target checkout, for a class outside the index.
- The routing entry stays as it is. "A catalog lookup found nothing that should
  exist" is the report path rather than a provenance question. It covers the
  system extension catalog, whose answers carry no provenance block.
- The pair row in `ScopeTest` stays, because both tools do.
- No skill routes to it, and none gets one. `T-260902-9e2c` read the absence as
  a gap. What it was a gap in was the component miss, which the section above
  closes. `R-DOC-001` puts every tool in the scope rather than in a skill. The
  two skills that route the component lookup already say what to do with a miss.
  The backend module workflow reports the gap and its diagnosis, and neither
  needs a second call to do it. What remains of the tool is coverage per
  catalog, which is a question a caller brings rather than a step in a build.

## Assumed

- That `scope` and `counts` are worth a call. They are what the two catalogs
  hold and how many entries each has, and nothing else answers either. No
  session has asked for them, and no session has reported a wish for them.
- That one debrief with a tool among the never-called says nothing about the
  tool. It named six it regretted and eleven it did not, and this was in the
  second list.

## Wrong if

- A session reports that it reached for `typo3_snapshot_scope` and found only
  what its last answer already held. The move above would then have been the
  half measure. `scope` and `counts` folded into the two lookups is the change.
- The system extension catalog grows a provenance block of its own. The second
  reason to keep the tool goes with it, and what remains is one call for two
  fields.
- A client validates against the old `catalogProvenance` and refuses the added
  field. Nothing here can see that, and the schema grew rather than moved.
