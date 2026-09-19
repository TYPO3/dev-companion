# REVIEW-02 — Review a reusable TYPO3 extension

**Environment:** `E-EXT` · **Status today:** `covered`

> Review this TYPO3 extension. Tell me the most important things that would
> prevent us maintaining and supporting it confidently, in priority order. Do
> not change files.

**What has to come out of it**

- The review establishes the extension key, supported TYPO3 and PHP range,
  repository-owned commands, and whether the checkout is a standalone extension
  or part of an installation.
- It inspects only subsystems the extension actually contains and supports each
  finding with a concrete path, behavior, or failed project-owned check.
- Compatibility, behavioral correctness, test evidence, documentation, static
  quality, and release concerns are considered only as far as repository
  evidence makes them relevant.
- Findings are prioritized by support and maintenance consequence, with
  uncertainty and missing runtime evidence stated explicitly.
- Follow-up work is split into bounded workflows; the review does not silently
  become an upgrade, testing, documentation, or release implementation.

**How it fails**

- **The corpus instead of this thing**, as
  [what every review has failed at](readme.md#what-every-review-has-failed-at)
  states it.
- It guesses compatibility from the installed major while ignoring declared
  support constraints.
- **Another context's conventions**, as the same list states it.
- **The invented requirement** and **changing files under a review-only
  request**, as the same list states them.
