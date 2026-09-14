---
id: D-AUD-008
title: 'The server is called dev-companion'
date: 2026-08-06
status: confirmed
readings:
  - 2026-09-04
---

# D-AUD-008 — The server is called dev-companion

**This server takes the name `typo3/dev-companion`, and every name a caller sees
moves with it in one cut. That is the binary, the namespace, the environment
variables and the state directory.**

`typo3/cms-mcp` claimed two things that are not true of it. `cms-` is the
namespace the core's system extensions live under, so the name reads as part of
the core's delivery. And "MCP" names a category in TYPO3 that four other
projects already fill, all of which write into the installation this one only
reads.

## Evidence

- Four TYPO3 MCP servers exist and all of them write.
  `hauptsacheNet/typo3-mcp-server` changes pages and records through workspaces.
  `marekskopal/typo3-mcp-server` offers 60+ tools over pages, content elements,
  files and backend users. `autodudesde/ai-suite-mcp` serves `EXT:ai_suite`, and
  `netresearch/t3x-nr-mcp-agent` is a backend chat assistant. TYPO3 News carries
  the category under the headline "AI Integration in TYPO3 Via MCP: The End of
  Backend Fumbling". A reader who knows the term expects the opposite of what
  this server does.
- `typo3/` without `cms-` is where the official tools sit that are not a system
  extension: `typo3/tailor`, `typo3/coding-standards`,
  `typo3/testing-framework`.
- `typo3/dev-companion` answers 404 on Packagist, read on 2026-08-06. The only
  package on the term is `webregulate/dev-companion`, a vendor away and
  unrelated, and GitHub carries no project of that name with a TYPO3 subject.
- "Companion" is a common noun in TYPO3's developer tools rather than a taken
  brand. `ide_companion` is an extension key paired with a VS Code LSP client,
  and `praetorius/fluid-companion` backports Fluid's IDE integration. Both are
  the same architecture as this server — a subprocess an editor starts for
  project knowledge. The word therefore distinguishes little, which is a cost
  taken with open eyes rather than a collision nobody saw.

## Decided

- The name is `dev-companion` and the vendor is `typo3/`. `dev` separates this
  from the editorial and operational halves of TYPO3. `companion` says it
  accompanies the work rather than performs it, and it is the word
  `knowledge/server-scope.json` already opens with.
- "MCP" leaves the name and stays in the description. It names the transport,
  and the transport is the part another is most likely to join.
- The cut is hard: no alias binary, no transitional second name. A client
  configuration written against `bin/typo3-cms-mcp` no longer works and needs a
  rewrite.
- What does not move: the `typo3_` tool prefix, the `typo3-*` skill names, and
  the `typo3://` resource URIs. Those name the domain rather than this product,
  and a skill lives in somebody else's project, where the next release corrects
  no rename.
- Rejected: `agent-navigator`, which names the API consumer rather than the
  reader it serves and takes a word the component catalog itself answers queries
  on. Rejected: `mentor` and `compass`, which distinguish better and say less.
  Rejected: anything with `guide`, `scope`, `lookup`, `describe`, `list`,
  `record` or `forge` in it. The first six are the tool verbs and the last is
  the core's issue tracker, which `typo3_forge_lookup` reads.

## Assumed

- That this package may live under the `typo3/` vendor. Nobody has had the
  question, and the vendor is the half of the name that carries the claim.
- That the TER key `dev_companion` is free. Nobody could read it on 2026-08-06;
  extensions.typo3.org blocked both the page and the API behind its bot
  protection. The package is a library and claims no extension key, so this only
  matters if an extension side is ever shipped.
- That the install base is small enough for a hard cut to be retold rather than
  discovered.

## Wrong if

- The `typo3/` vendor refuses, or grants only through a review this has not been
  through. Then the name is right and the vendor is not, and the package moves
  again — which is the second rename people notice.
- Somebody reports an issue against this server that is about `ide_companion` or
  about one of the four MCP servers. That is the confusion the name accepts
  rather than avoids, and one report is evidence it costs more than the
  judgement said.
- `dev` is no longer true: the server grows an answer for editors, or for the
  operation of an installation rather than its build.
- Somebody who never heard finds a client configuration from before the cut
  broken, rather than somebody who did.

## Confirmed on 2026-08-14

The vendor was granted. The repository is `TYPO3/dev-companion` on GitHub, and
the `typo3/` vendor came with it as a yes. That is what the first **Wrong if**
watched for and what the assumption above rested on. Every address that named
the old one moved the same day. That is the `support` block in `composer.json`,
where `Upkeep\Site` reads the repository the rendered site links into, the
GitHub button and the footer link. And the `compatibility` line each published
skill carries into somebody else's project. The site is
`typo3.github.io/dev-companion`.

The other three **Wrong if** are unread. The package went to Packagist under
that name the same day, with `dev-main` and no tagged release. So the install
base a further rename would break starts there rather than still ahead.
