# EXT-06 — Wording, labels and translations

**Environment:** `E-SITE` with the extension under `packages/` ·
**Contract:** `held`
**Held by:** `LabelSearchTest::aQueryOfSeveralWordsAsksForAllOfThemAtOnce`,
`LabelSearchTest::anEmptyResultSaysHowFarEachWordReachesOnItsOwn`,
`CatalogTest::aDomainIsDerivedForAFileThatDoesNotExistYet`

> The new module needs its texts. Use the wording TYPO3 already uses where there
> is one, and put the rest in our own language file so the translation server can
> pick it up. Some of the core labels are wrong for our context, I want those
> overridden.

**What the agent needs from this server**

- Which labels already exist for a given wording, across everything this
  installation has active, and what their domain reference is.
- The domain the extension's own XLF file will resolve to, before the file
  exists.
- The conventions for language files: their names, where they sit, how to form a
  key.
- How an existing label is overridden for this installation.

**What has to come out of it**

- The words of the wording, in any order, find the labels that exist, and the
  agent reuses them with their real domain reference.
- New keys go into a file whose domain the answer states.
- Where nothing matches, the answer says how far each single word reached,
  rather than an empty result that reads like "no labels exist".

**How it fails**

- A multi-word query that comes back empty and reads as "no such label"
  (`R-ANS-004`).
- A console the server could not reach reported as an installation with no
  labels (`R-ANS-001`, `R-ANS-005`).
