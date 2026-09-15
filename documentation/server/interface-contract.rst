:navigation-title: Interface contract

The draft RFC on an MCP interface contract for TYPO3
====================================================

**A community proposal, not an adopted standard.** It is a gist one person
published, no TYPO3 body has adopted it, and this repository takes part in no
process around it. Nothing here has a filed form anywhere and nothing here waits
on it.

It served as a reference, the yardstick this repository's own name questions
stood against, which is the whole of its role here. The judgement is
`D-SCO-010 <../../decisions/scope/sco-010-all-three-typo3-namespaces-are-kept.md>`_.
This page is the read behind it. What the draft says, what it would collide with
here, and what this server would have to change if it ever became a contract.

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
  discovery surfaces, llms.txt style signals. They note that shared terminology
  would be desirable.

Its precision is uneven where being wrong is most expensive. A tool name is
local to one server's catalogue. A URI scheme is not. It is the one namespace in
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
matched, and the shared record shape declares that field. Two tool descriptions
send the model that calls to the resource. The skills make the point stronger
rather than different. A skill body sits at a URI that ends in its own file
name. So the relative links it already carries resolve onto the reference URIs
this server answers, see `ResourceHandler <../../src/Sdk/ResourceHandler.php>`_.
Move the scheme and those links resolve nowhere.

If a contract's uniform scheme turned out to be ``typo3://``, a conformant
client could not tell a TCA resource from something else. The URI alone would
not say. It would read ``typo3://guides`` expecting a machine-readable content
model and get a documentation index. That is worse than a clean failure, because
nothing errors — the model receives the wrong kind of document and proceeds. One
sentence prevents it, and the draft already contains the template. Apply to
schemes the rule it gives tool names, or fix an authority segment such as
``typo3://tca/…`` for the mandatory part.

The tool prefix is the cheaper half. ``typo3.`` reads as a reservation of names
that literally begin with ``typo3.``, dot included. So it does not reach a tool
named ``typo3_rule_lookup``. On that read the 26 tools here stay as they are. It
is a reading of one sentence — the draft states the prefix once and never gives
the separator a rule.

The draft is also the first document to draw a line around what may present
itself as TYPO3's MCP surface. That is where the package name meets it. This
package declares ``typo3/dev-companion`` in its ``composer.json`` and stands
under that name on Packagist since 2026-08-14, in the ``typo3`` vendor the TYPO3
Association owns. So the name has an owner, and a policy on that vendor would
stand against a package that already carries it rather than against a request.

What adoption would cost
------------------------

* **``typo3://`` reserved as a whole.** Both trees move to a scheme of this
  server's own. That is a rename in ``ResourceHandler``, ``Factory``,
  ``Result\Prose``, ``Result\Schema``, and the two tool descriptions that name
  the URI. Also every published skill body, whose relative links resolve against
  the URI it sits at.
* **A fixed authority segment inside it**, such as ``typo3://tca/…``. Then
  ``typo3://guides`` and ``typo3://skill/`` stay as they are, and the cost is
  nothing.
* **A reservation that catches ``typo3_``**, a rule about the string ``typo3``
  under any separator. Or a conformance suite that rejects a non-contract tool
  whose name begins with it. Then all 26 tool names go.
* **A policy on the ``typo3`` Packagist vendor for MCP packages.** Then
  ``composer.json`` carries a name this package may not use, and the published
  package needs a rename rather than a different declaration.

The first three are cheap while nothing depends on this server's names and stop
to be cheap afterwards. That is why ``D-SCO-010`` changes nothing yet. There is
nothing to rename against until somebody allocates a scheme, and a rename made
on a guess needs a second one. The fourth stopped to be free on the day the
package went to Packagist, since a Packagist name is one other lock files carry.
