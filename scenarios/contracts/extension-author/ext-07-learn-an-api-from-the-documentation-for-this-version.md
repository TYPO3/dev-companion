# EXT-07 — Learn an API from the documentation for this version

**Environment:** `E-EXT` on TYPO3 13.4 · **Contract:** `held` — `R-DOC-001`
**Held by:**
`DocumentationTest::itSearchesTheRequestedVersionAndKeepsProvenanceOnEveryResult`,
`DocumentationTest::itRefusesAPageOutsideTheSelectedManualVersion`,
`DocumentationTest::anUnreachableIndexIsDifferentFromNoMatch`

> I need to add a PSR-14 listener that changes the page title. Find the official
> API and show me the registration and event shape that applies to TYPO3 13.4.

**What the agent needs from this server**

- The relevant section of the official live documentation for the stated TYPO3
  version, not a broad answer recalled from another release.
- The canonical document URL, document identifier, version and section for every
  result, so the agent can read the source before it changes code.
- Curated conventions alongside the documentation where they add a rule the
  reference does not carry.

**What has to come out of it**

- The search is explicitly bound to 13.4 and never falls through to `main` or a
  different release.
- The answer distinguishes an official documentation result, no matched section,
  and a documentation service the server could not reach.
- The calling agent can tell which sentences came from live documentation and
  which came from the bundled conventions.

**How it fails**

- A plausible event class or service tag comes from memory with no source.
- A result for another TYPO3 release passes as a 13.4 answer.
- A network failure in the shape of an authoritative empty search.
