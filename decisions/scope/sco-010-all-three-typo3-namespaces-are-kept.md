---
id: D-SCO-010
title: 'All three `typo3` namespaces are kept'
date: 2026-08-04
status: open
coveredBy:
  - StdioServerTest::theKnowledgeIndexIsServedWithTheScope
  - ToolNamingTest::everyToolIsNamedSubjectThenVerb
---

# D-SCO-010 — All three `typo3` namespaces are kept

**This server keeps the `typo3_` tool prefix, the `typo3://` resource scheme and
the `typo3/dev-companion` package name. It reads the draft RFC on an MCP
interface contract for TYPO3 as a reference, not as a process this repository
takes part in.**

The draft reserves `typo3.` for the mandatory part of the contract. It requires
a resource under a uniform scheme for the content model, and it does not name
the scheme. Two of the three namespaces here are outside that reservation; the
third is inside the sentence that names no scheme.

Nothing in `decisions/` had ever said why the prefix is `typo3_`. So this entry
records a choice nobody made rather than one somebody defends, which is the
difference between a decision and a rationalisation.

Its first form, written earlier the same day, made the decision turn on a
comment to the draft's authors. The draft is a gist one person published rather
than a standard anybody has adopted. Nobody has published this server, it can
still change any of the three names, and nothing is at stake in either
direction. This entry records what the session read, and what it would cost if
the draft ever became a contract.

## Evidence

- The session read the draft whole on 2026-08-04 at
  <https://gist.github.com/dkd-dobberkau/1f87ba4051fc85efbb9475d96babf1d5>. It
  carries one revision, created 2026-08-03, and no comments. Its **Process
  History** table dates a single row, "Draft created"; every later row has no
  date. Its own target for v1.0 is Q1 2027.
- It is a gist published under one person's account. It names no TYPO3 body that
  adopted it, and it names this repository not at all.
- Its **Namespace and Tool Naming** section reserves one prefix and puts a dot
  in it. It says "The prefix `typo3.` is reserved for the mandatory part of the
  contract. Extensions register their tools under their own prefix." None of the
  26 tool names here contains a dot. They are `typo3_<subject>_<verb>`, which
  `ToolNamingTest` holds against
  [`Registry`](../../src/Tool/Registry.php).
- Its **TCA as a Resource** section says: "The content model MUST be served as a
  resource under a uniform scheme and MUST NOT be decomposed into a large number
  of generated tools". The draft names no scheme, there or anywhere else in it.
  So the document does not take `typo3://`. It leaves the scheme open, and
  `typo3://` is the scheme a contract about TYPO3 reaches for first.
- This server serves four shapes under `typo3://`, in
  [`ResourceHandler`](../../src/Sdk/ResourceHandler.php), and
  [`Factory`](../../src/Server/Factory.php) registers them. They are
  `typo3://core`, `typo3://core/{id}` per knowledge document,
  `typo3://skill/{id}/SKILL.md` per published skill, and the template
  `typo3://skill/{id}/references/{file}`. None of it is an internal detail.
  Every prose answer prints the URI of the document it matched
  ([`Result\Prose`](../../src/Result/Prose.php)), and the shared record shape
  declares it ([`Result\Schema`](../../src/Result/Schema.php)).
  [`RuleLookup`](../../src/Tool/RuleLookup.php) and
  [`ServerScope`](../../src/Tool/ServerScope.php) tell the caller to read it. A
  skill body's own relative links resolve against the URI the server serves it
  at.
- The draft names two implementations, hauptsacheNet/typo3-mcp-server and
  marekskopal/typo3-mcp-server. It does not name this package. Both of those
  publish under a vendor of their own — `hn/typo3-mcp-server` and
  `marekskopal/typo3-mcp-server` on Packagist, read 2026-08-04.
- `composer.json` declares `typo3/dev-companion`, and nobody has published it.
  Packagist's search answers `{"results":[],"total":0}` for it and
  `repo.packagist.org/p2/typo3/dev-companion.json` answers 404, both read
  2026-08-04.
- Packagist protects a vendor once one package exists under it. It says that a
  publication under a vendor that exists requires a maintainer of at least one
  package already in it. `typo3/cms-core` lists the maintainers `typo3`, `bmack`
  and `ohader`, read 2026-08-04. Nobody here is one of them.

## Decided

- All three namespaces stay, unchanged: the 26 `typo3_` tool names, the
  `typo3://` scheme, and `typo3/dev-companion` in `composer.json`.
- The tool prefix stays because the reservation is `typo3.` and an underscore
  name does not literally collide with it. That is the first reason this
  repository has ever written down for the prefix. It is an interpretation of
  somebody else's draft rather than the reason somebody chose the prefix; nobody
  knows what that was.
- The scheme stays and nobody renames anything ahead of anything. A scheme
  nobody has allocated is a sentence in one person's gist. A rename against it
  pays the whole cost of a rename to settle nothing. Four classes print the URI,
  two tool descriptions send it to the caller, and every published skill's own
  links resolve against it.
- [documentation/server/interface-contract.rst](../../documentation/server/interface-contract.rst)
  writes down what the draft says, what it would collide with here and what
  would have to change under it. Nobody argues with it. Nobody files anything
  anywhere, and nothing in this repository waits on the draft.
- Whether this package may publish as `typo3/dev-companion` is **unresolved**.
  The vendor belongs to the TYPO3 Association and Packagist would refuse the
  submission. This entry decides only that the name stays in `composer.json`
  while nobody publishes, because a name that resolves nothing costs nothing.
- Rejected: a rename of the scheme now, to `typo3-dev-companion://` or similar.
  This server is pre-release and can change any of the three names on the day
  there is something to change them against. So a rename today buys a guess and
  loses the four URI shapes that already work.
- Rejected: a rename of the tool prefix. The reservation does not reach it, and
  a tool name is what clients installed months ago call. That is the outward
  name `AGENTS.md` says wins where two spellings compete.

## Assumed

- The dot in `typo3.` is deliberate and the contract's own tool names will carry
  it. The draft states the prefix once and never gives the separator a rule, so
  this interpretation rests on one sentence.
- A client resolves a URI scheme this server hands it against no registry. So
  `typo3://core` and a contract's `typo3://` can coexist in one client until
  something has to tell them apart.
- Nothing outside this checkout depends on any of the three names. Nobody has
  published the package, and a client that has this server points at a checkout,
  so all three are still free to move.

## Wrong if

- TYPO3 adopts the draft, or something descended from it, as a standard, and the
  standard allocates `typo3://` to the content model. The signal is a published
  contract that names a scheme. Then a conformant client that reads
  `typo3://core` gets a knowledge index where it expects a schema, which is
  ambiguity rather than a clean failure. The fix is a rename in
  `ResourceHandler`, `Factory`, `Result\Prose` and `Result\Schema`. It reaches
  the two tool descriptions that name it, and every skill body whose relative
  links resolve against its own URI.
- Another MCP server claims `typo3://` in a client that also loads this one.
  That needs no RFC and is visible in the client's own resource list: two
  `typo3://` trees, where the URI no longer says which server answered. Then the
  scheme has to carry something this server owns, whatever any contract says.
- A published contract or its conformance suite rejects a tool whose name begins
  with `typo3` under any separator, rather than only with the dot. Then all 26
  names go, and the only reason this entry gives for the prefix goes with them.
- Somebody publishes this package and Packagist refuses the `typo3` vendor, or
  the Association asks for the name back. Then `composer.json` carries a name
  this package may not use, and the rename is cheap only for as long as nothing
  has installed it.
