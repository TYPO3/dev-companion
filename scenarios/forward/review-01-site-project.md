# REVIEW-01 — Review a TYPO3 site project

**Environment:** `E-SITE` · **Status today:** `covered`

> Review this TYPO3 project and its site package. Identify the most important
> concrete problems, risks, or missing safeguards, prioritize them, and tell me
> what should happen next. Do not change files.

**What has to come out of it**

- The review establishes the installation, project, site package, TYPO3 version,
  available project-owned checks, and the surfaces it intends to cover before it
  starts reading the checkout — not alongside the reading and not after it.
- Findings come from concrete checkout or installation evidence and name the
  affected location, consequence, confidence, and next action.
- Priorities follow user impact and failure risk rather than file count,
  stylistic preference, or the order in which files were opened.
- A subsystem is judged against the conventions that govern it, asked for before
  the judgment rather than after, and established facts about it are not
  mistaken for a verdict on it. A surface this kind of checkout could have —
  including one with no files at all, whose absence the file tree cannot show —
  is reported as unassessed rather than left out.
- Confirmed defects, recommendations, missing evidence, and subjects outside
  this server's scope remain visibly distinct.
- The answer stops at a short prioritized review and routes follow-up work to
  the applicable workflow without starting unrelated implementation.

**How it fails**

- **The corpus instead of this thing**, as
  [what every review has failed at](readme.md#what-every-review-has-failed-at)
  states it.
- **The invented requirement**, as the same list states it.
- It treats an incomplete parser or unavailable runtime source as proof that a
  feature is absent.
- **Another context's conventions**, as the same list states it.
- **Changing files under a review-only request**, as the same list states it.
