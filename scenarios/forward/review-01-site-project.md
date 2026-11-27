# REVIEW-01 — Review a TYPO3 site project

**Environment:** `E-SITE` · **Status today:** `partial`

> Review this TYPO3 project and its site package. Identify the most important
> concrete problems, risks, or missing safeguards, prioritize them, and tell me
> what should happen next. Do not change files.

**What has to come out of it**

- Before it reads the checkout, the review establishes the installation, the
  project, the site package and the TYPO3 version. It also establishes the
  project's own checks and the surfaces it will cover, before the reading and
  not during or after it.
- Findings come from concrete checkout or installation evidence and name the
  affected location, consequence, confidence, and next action.
- Priorities follow user impact and failure risk rather than file count,
  stylistic preference, or the order in which files were opened.
- The review judges a subsystem against the conventions that govern it, and asks
  for them before the judgment. It does not mistake established facts about a
  subsystem for a verdict on it. The review reports a surface this kind of
  checkout could have as unassessed rather than leaving it out. That includes a
  surface with no files, whose absence the file tree cannot show.
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
