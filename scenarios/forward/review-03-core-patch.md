# REVIEW-03 — Review a TYPO3 core patch

**Environment:** `E-CORE` · **Status today:** `covered`

> Review the current changes in this TYPO3 core checkout. Tell me what is wrong,
> missing, or not ready for review, in priority order. Do not change files.

**What has to come out of it**

- The review reads the actual diff, target branch, affected subsystem, and
  nearby tests before selecting rules or checks.
- Each finding ties a concrete changed path or behavior to a core contribution
  requirement and its consequence.
- The proposed verification is the narrowest applicable project command before
  broader suites, and commands that were not run remain labeled as such.
- Public behavior, compatibility, documentation or changelog work, commit shape,
  and review readiness are raised only when the diff makes them relevant.
- The result is prioritized and bounded; it does not rewrite the patch during a
  review-only request.

**How it fails**

- It reviews TYPO3 from memory without reading the diff.
- **The corpus instead of this thing**, as
  [what every review has failed at](readme.md#what-every-review-has-failed-at)
  states it.
- **Another context's conventions**, as the same list states it.
- **Changing files under a review-only request**, as the same list states it.
