:navigation-title: Interface contract

The draft RFC on an MCP interface contract for TYPO3
====================================================

This page covers a draft RFC for an MCP interface contract for TYPO3. It says
what the draft says and where it would collide with this server. It also says
what this server would have to change if the draft became a contract.

**The draft is a community proposal, not an adopted standard.** One person
published it as a gist, and no TYPO3 body has adopted it. This repository takes
part in no process around it. Nothing here has a filed form anywhere, and
nothing here waits on it.

The draft is a reference, and that is its whole role here. This repository
measured its own names against it. The judgement is
`D-SCO-010 <../../decisions/scope/sco-010-all-three-typo3-namespaces-are-kept.md>`_,
and this page gives the reasons for that judgement.

The session behind ``D-SCO-010`` read the draft whole on 2026-08-04 at
`RFC-XXXX, An Official MCP Interface Contract for TYPO3 <https://gist.github.com/dkd-dobberkau/1f87ba4051fc85efbb9475d96babf1d5>`_,
in its revision of 2026-08-03. The gist had one revision and no comments. Its
Process History gave a date only for the row "Draft created". Its own target for
v1.0 was Q1 2027. Everything on this page describes that revision.

What the draft says
-------------------

* **Namespace and Tool Naming** reserves ``typo3.`` for the mandatory part of
  the contract. It sends extensions to a prefix of their own.

* **TCA as a Resource** demands the content model "served as a resource under a
  uniform scheme". It "MUST NOT be decomposed into a large number of generated
  tools". It names no scheme, there or anywhere else.

* It names two implementations, hauptsacheNet/typo3-mcp-server and
  marekskopal/typo3-mcp-server. It does not name this package.

* Its **Open Questions** ask how the contract relates to public, read-only
  discovery surfaces, such as llms.txt style signals. They say that shared
  terminology would be desirable.

The draft is least precise where a wrong answer costs most. A tool name is local
to the catalogue of one server, but a URI scheme is not. Two servers in the same
client share the URI scheme, whether they mean to or not. So the namespace the
draft leaves unnamed is the one that collides.

Where it collides
-----------------

This server serves four shapes under ``typo3://``. The first two are older than
the draft:

* ``typo3://guides`` — the index: what the server covers, how it routes a
  question, and a list of the documents below it.

* ``typo3://guides/{id}`` — one markdown document per entry in the knowledge
  base.

* ``typo3://skill/{id}/SKILL.md`` — the body of each published task skill.

* ``typo3://skill/{id}/references/{file}`` — a resource template for the files
  the body of a skill links to.

This server cannot change the scheme quietly, because callers depend on it:

* Every prose answer gives the ``typo3://guides/{id}`` of the document it
  matched, and the shared record shape declares that field.

* The output schemas of several tools send the model that calls them to the
  resource.

* A skill body sits at a URI that ends in its own file name. So its relative
  links resolve onto the reference URIs this server answers, see
  `ResourceHandler <../../src/Sdk/ResourceHandler.php>`_. If the scheme moves,
  those links resolve nowhere.

A contract could choose ``typo3://`` as its uniform scheme. Then a conformant
client could not tell a TCA resource from something else by the URI. It would
read ``typo3://guides``, expect a machine-readable content model and get a
documentation index.

That is worse than a clean failure, because nothing reports an error. The model
receives the wrong kind of document and continues. One sentence in the draft
prevents it, and the draft already has the template for it. The draft can apply
its rule for tool names to schemes too. Or it can fix an authority segment such
as ``typo3://tca/…`` for the mandatory part.

The tool prefix is the cheaper half. ``typo3.`` reads as a reservation of names
that begin with ``typo3.``, with the dot. So it does not reach a tool named
``typo3_rule_lookup``, and every tool here stays as it is. This interpretation
rests on one sentence. The draft states the prefix once and gives no rule for
the separator.

The draft is also the first document to define what may present itself as the
MCP surface of TYPO3. The package name meets the draft there. This package
declares ``typo3/dev-companion`` in its ``composer.json``. It has had that name
on Packagist since 2026-08-14, in the ``typo3`` vendor of the TYPO3 Association.
So the name has an owner. A policy on that vendor would apply to a package that
already has the name, not to a request for it.

What adoption would cost
------------------------

* ``typo3://`` **reserved as a whole.** Both trees move to a scheme of this
  server's own. That is a rename in ``ResourceHandler`` and ``Documents``, which
  hold the two prefixes. It is also a rename in every sentence that names the
  URI, in ``Factory``, ``Installer``, ``Result\Schema`` and several tool
  schemas.

  It is also a rename in every published skill body, because its relative links
  resolve against the URI the body sits at.

* **A fixed authority segment inside it**, such as ``typo3://tca/…``. Then
  ``typo3://guides`` and ``typo3://skill/`` stay as they are, and the cost is
  nothing.

* **A reservation that catches** ``typo3_``, a rule about the string ``typo3``
  under any separator. Or a conformance suite that rejects a non-contract tool
  whose name begins with it. Then every tool name here goes.

* **A policy on the** ``typo3`` **Packagist vendor for MCP packages.** Then
  ``composer.json`` has a name this package may not use. The published package
  then needs a rename, not a different declaration.

The first three are cheap while nothing depends on the names of this server.
Afterwards they are not cheap. That is why ``D-SCO-010`` changes nothing yet.
There is nothing to rename against until somebody allocates a scheme, and a
rename on a guess needs a second rename.

The fourth cost something from the day the package went to Packagist. Other lock
files record a Packagist name.
