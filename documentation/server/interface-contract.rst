:navigation-title: Interface contract

The draft RFC on an MCP interface contract for TYPO3
====================================================

**A community proposal, not an adopted standard.** It is a gist one person
published, no TYPO3 body has adopted it, and this repository takes part in no
process around it. Nothing here is filed anywhere and nothing here waits on it.

It was consulted as a reference — the yardstick this repository's own naming
questions were judged against, which is the whole of its role here. What was
judged is
`D-SCO-010 <../../decisions/scope/sco-010-all-three-typo3-namespaces-are-kept.md>`_.
This page is the reading behind it: what the draft says, what it would collide
with here, and what this server would have to change if it ever became a
contract.

Read whole on 2026-08-04 at
`RFC-XXXX, An Official MCP Interface Contract for TYPO3 <https://gist.github.com/dkd-dobberkau/1f87ba4051fc85efbb9475d96babf1d5>`_,
in its revision of 2026-08-03. It carried one revision and no comments, its
Process History dated only the row "Draft created", and its own target for v1.0
was Q1 2027. Everything below describes that revision.

What the draft says
-------------------

* **Namespace and Tool Naming** reserves ``typo3.`` for the mandatory part of
  the contract and sends extensions to a prefix of their own.
* **TCA as a Resource** requires the content model be "served as a resource
  under a uniform scheme and MUST NOT be decomposed into a large number of
  generated tools". It names no scheme, there or anywhere else.
* It names two implementations, hauptsacheNet/typo3-mcp-server and
  marekskopal/typo3-mcp-server, and does not name this package.
* Its **Open Questions** ask how the contract relates to public, read-only
  discovery surfaces — llms.txt style signals — and note that shared terminology
  would be desirable.

Its precision is uneven where being wrong is most expensive. A tool name is
local to one server's catalogue. A URI scheme is not: it is the one namespace in
MCP that two servers in the same client share whether they meant to or not. The
namespace the draft leaves unnamed is the one that actually collides.

Where it collides
-----------------

This server serves four shapes under ``typo3://``, and the first two predate the
draft:

* ``typo3://guides`` — the index: what the server covers, how it routes a
  question, and a listing of the documents below it.
* ``typo3://guides/{id}`` — one markdown document per entry in the knowledge
  base.
* ``typo3://skill/{id}/SKILL.md`` — the body of each published task skill.
* ``typo3://skill/{id}/references/{file}`` — a resource template for the files a
  skill's body links to.

The scheme is not an implementation detail this server could quietly change.
Every prose answer carries the ``typo3://guides/{id}`` of the document it
matched, the shared record shape declares that field, and two tool descriptions
send the calling model to the resource. The skills make the point stronger
rather than different: a skill body is served at a URI ending in its own file
name so that the relative links it already carries resolve onto the reference
URIs this server answers —
`ResourceHandler <../../src/Sdk/ResourceHandler.php>`_. Move the scheme and
those links resolve nowhere.

If a contract's uniform scheme turned out to be ``typo3://``, a conformant
client could not tell a TCA resource from something else by the URI alone. It
would read ``typo3://guides`` expecting a machine-readable content model and get
a documentation index. That is worse than a clean failure, because nothing
errors — the model receives the wrong kind of document and proceeds. One
sentence prevents it, and the draft already contains the template: applying to
schemes the rule it gives tool names, or fixing an authority segment such as
``typo3://tca/…`` for the mandatory part.

The tool prefix is the cheaper half. ``typo3.`` reads as reserving names that
literally begin with ``typo3.``, dot included, and therefore as not reaching a
tool named ``typo3_rule_lookup``; on that reading the 26 tools here are
unaffected. It is a reading of one sentence — the draft states the prefix once
and never gives the separator a rule.

The draft is also the first document to draw a line around what may present
itself as TYPO3's MCP surface, which is where the package name meets it. This
package declares ``typo3/dev-companion`` in its ``composer.json`` and is
published under that name on Packagist, in the ``typo3`` vendor the TYPO3
Association owns, since 2026-08-14. So the name is allocated, and a policy on
that vendor would be read against a package already carrying it rather than
against a request.

What adoption would cost
------------------------

* **``typo3://`` reserved as a whole.** Both trees move to a scheme of this
  server's own: a rename in ``ResourceHandler``, ``Factory``, ``Result\Prose``,
  ``Result\Schema``, the two tool descriptions that name the URI, and every
  published skill body, whose relative links resolve against the URI it is
  served at.
* **A fixed authority segment inside it**, such as ``typo3://tca/…``. Then
  ``typo3://guides`` and ``typo3://skill/`` stay as they are, and the cost is
  nothing.
* **A reservation catching ``typo3_``** — a rule about the string ``typo3``
  under any separator, or a conformance suite that rejects a non-contract tool
  whose name begins with it. Then all 26 tool names go.
* **A policy on the ``typo3`` Packagist vendor for MCP packages.** Then
  ``composer.json`` carries a name this package may not use, and the published
  package is renamed rather than merely declared differently.

The first three are cheap while nothing depends on this server's names and stop
being cheap afterwards, which is why ``D-SCO-010`` changes nothing yet: there is
nothing to rename against until somebody allocates a scheme, and a rename made
on a guess has to be made again. The fourth stopped being free on the day the
package was published, since a Packagist name is one other lock files carry.
