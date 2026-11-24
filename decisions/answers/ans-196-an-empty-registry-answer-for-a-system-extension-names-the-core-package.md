---
id: D-ANS-196
title: An empty registry answer for a system extension names the core package
date: 2026-11-24
status: open
coveredBy: []
---

# D-ANS-196 — An empty registry answer for a system extension names the core package

**Where `typo3_ter_lookup` finds nothing under a key that the core ships as a
system extension, the answer says so. It names the Composer package and
`typo3_system_extension_lookup`. The tool description says that the registry
carries no price and no licence.**

Two feedback from the T3CA talk recordings of 2026-11-24 reported the gap.

## Evidence

- `typo3_ter_lookup` with `indexed_search`, re-run on 2026-11-24, answered
  `empty` and nothing else. A caller can read that as an abandoned extension.
- The system extension catalog already knows every key the core ships, with its
  package. `typo3_system_extension_lookup` answers from it.
- The registry API for `ke_search`, read on 2026-11-24, carries the owner, the
  downloads, the package, the repository and a PayPal field. It carries no
  licence, no price and no paid edition. The session that asked left the server
  for a web search.

## Decided

- An empty answer looks the key up in the catalog by exact key. A match replaces
  the generic sentence and adds one `owed` sentence. A substring match would
  name a core package for an extension that only shares a word.
- The cost question gets a sentence in the description, not a field. No registry
  this server reads carries it, so a field would be empty on every answer.

## Wrong if

- A system extension key has a published version in the registry. Then the empty
  branch is the wrong place, and an answered key needs the note too.
- The registry API gains a licence or a commercial field. Then the answer can
  carry it, and the description sentence turns false.
