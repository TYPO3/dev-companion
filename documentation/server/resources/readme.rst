:navigation-title: Resources

The resource surface
====================

This page covers the resources this server offers a client to pick from, and the
fields a picker chooses by. It also says why the skill URI ends in a file name,
what is generated, and which tests guard the surface.

`Factory::resources() <../../../src/Server/Factory.php>`_ declares the list, and
``Factory::skillReferences()`` declares the one template behind it.
`Sdk\ResourceHandler <../../../src/Sdk/ResourceHandler.php>`_ and
`Sdk\SkillReferenceHandler <../../../src/Sdk/SkillReferenceHandler.php>`_ answer
a read. Every resource is a file this package ships. So a read reaches no
installation and no network.

The :doc:`tool surface <../tools/index>` is the other thing a client gets. It
has a page per tool, because a tool has a schema and a recorded answer. The
resources have one page, because the same rules hold for every entry.
``typo3://guides`` lists the entries themselves, live.

Picked, not called
------------------

**The host application or the user picks a resource. The model calls a tool in
the middle of a task.** The protocol makes that distinction, and it decides
everything else about this surface.

A tool explains itself in the answer it returns. A resource has only its list
entry. So ``description``, ``annotations.priority`` and ``size`` are what the
choice depends on, not decoration —
`R-ANS-022 <../../../requirements/answers/ans-022-a-resource-is-picked-out-of-a-list.md>`_.

While the model works, it reaches the same prose through a tool:

* ``typo3_rule_lookup`` searches the documents.

* ``typo3_task_guide`` names the workflow that the task it recognized belongs
  to, and the document that describes that work. It names the document as the
  ``typo3_rule_lookup`` call that reads it. It does not give the URI, because a
  client may render that nowhere.

The four shapes
---------------

* **``typo3://guides``** — the index. It gives the purpose, coverage and routing
  this client gets, and every document and skill with its URI. It also lists the
  references of each skill. Read it first: it lists the rest.

* **``typo3://guides/{documentId}``** — one document from
  ``knowledge/documents/``. That directory is also the corpus
  ``typo3_rule_lookup`` searches. Most documents are about the process of the
  core.

* **``typo3://skill/{skillId}/SKILL.md``** — one published task workflow from
  ``skills/``. Most workflows are about extension, sitepackage and project work.

  ``bin/typo3-dev-companion install`` writes the same file into the skills
  directory of the client. This resource is the route for a client that never
  ran it.

  A skill is published when its own front matter does not declare it a draft. A
  draft is a directory in ``skills/`` and appears nowhere.

* **``typo3://skill/{skillId}/references/{file}``** — what a workflow gives the
  reader at a step. This is a resource template, not one list entry per file. A
  reader follows these files from the body that names them. A checklist listed
  beside its own workflow is an entry nobody can choose between.

There are two families, because the documents alone serve one audience. Most of
the documents are about the process of the core. Most of the workflows are about
the work outside it. With both on offer, each of the three audiences of
`R-AUD-001 <../../../requirements/audience/aud-001-core-extension-and-site-work-are-each-served.md>`_
has something to pick.

Only ``knowledge/server-scope.json`` says which audience an entry is for. A
covered topic names every document and every published skill. The description
and the priority derive from the ``scope`` of that topic. ``ScopeTest`` checks
it in both directions. So a skill that no topic names fails the suite, and never
reaches an extension author as core-only.

``TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`` changes the index and not the list.
``Coverage::offered()`` drops a topic that no remaining tool can answer. So the
index a client reads matches the tools it has, and the resources stay. An
excluded tool takes away a call, not a document.

The skill URI
-------------

**A skill is a directory, and a pick must deliver all of what the picker
chose.** The body is short routing. Every body starts by sending the reader to
``references/base.md``.

The server serves the body at ``typo3://skill/{id}/SKILL.md``. So the relative
links in that prose resolve by ordinary URI rules onto the URIs the template
answers. Nothing gets a rewrite, and nothing gets assembled. The published bytes
go over the wire as they are.

Do not drop the file name from the URI. The links would then resolve one segment
higher, onto URIs that nothing serves. The body would still look complete, and
only the first reader who follows a link would find out.

``references/base.md`` is a file in no skill in this checkout.
`skills/base.md <../../../skills/base.md>`_ is the single copy. ``Installer``
writes it into each published directory, and the server serves it here from that
same file
(`D-SKL-001 <../../../decisions/task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md>`_).
So the resource is the file a client would have after the install.

The Skills extension
--------------------

**A host that speaks ``io.modelcontextprotocol/skills`` sees the workflows as
skills. Every other host sees them as the resources of the four shapes.** The
server sends the declaration in the ``initialize`` capabilities. A host may not
tell a skill from a page by its URI.
`Sdk\SkillsExtension <../../../src/Sdk/SkillsExtension.php>`_ declares it and
answers its two methods —
`D-ANS-163 <../../../decisions/answers/ans-163-the-skills-extension-is-served-over-the-typo3-resources.md>`_.

* **``skills/list``** — every published skill, as the URI of its body, its whole
  front matter, and a manifest. The manifest names each file with the SHA-256
  and the byte size of what a read returns. ``references/base.md`` is in it,
  with the hash of the file the read serves.

* **``skills/get``** — one entry by the URI of its body, for a host that has the
  URI without the list. A URI that names no skill gets ``-32602``.

The reads stay ``resources/read``, on the body and on each reference. The
extension adds the manifest a host checks each read against. It also adds the
identity a host binds an approval to. The scheme stays ``typo3://``. The
specification prefers ``skill://`` and permits another. The declaration, not the
scheme, makes a resource a skill.

What a picker reads
-------------------

* **``description``** — what the entry is and whom its answers are for. The
  description of a document comes from the covered topic. The description of a
  skill comes from its own front matter.

  Before it stands the sentence that says the entry is a workflow to follow, not
  a page to read. Neither text appears a second time, so neither can drift from
  what it describes.

* **``annotations.priority``** — the order, and nothing else. The index sits
  above everything it lists. What holds wherever the caller works sits above
  what stops at the core. The references sit below all of them, because a reader
  reads one at the step that sends them to it.

  The numbers in ``Factory`` are a scale a picker sorts by. The distance between
  two of them means nothing.

* **``size``** — the bytes the handler really serves, so a client knows what a
  read costs. For the index, that is the encoded JSON, not a file on disk.
  ``ResourceSurfaceTest`` checks every declared size against what a read
  returns.

Two fields of the spec stay absent. The first one is where readers get confused:

* **``annotations.audience``** is the ``user`` and ``assistant`` of the
  protocol, the ``Role`` enum of the SDK. It never means the three audiences of
  ``R-AUD-001``, and it does not take them as values.

  Everything here is for both roles, so the field says nothing and stays off.
  ``ResourceSurfaceTest`` fails on a resource that sets it.

* **``annotations.lastModified``** is in the spec revision the SDK speaks. No
  ``Mcp\Schema\Annotations`` at the mcp/sdk version in ``composer.lock`` has it.
  That class has only ``audience`` and ``priority``. The record says the field
  is unavailable, and does not fake it through ``_meta``.

What is generated
-----------------

``bin/cli tools:index`` writes the tool reference, because each page repeats a
description and two schemas that a class declares. The installed skill catalog
at :doc:`../../usage/task-skills/index` is a generated page for the same reason.
There a person compares and reads the published workflows before the install.
``bin/cli documentation:prepare`` copies the Markdown the installer publishes
into the site source. It adds one page per skill that embeds that Markdown.

The manual has no catalog of the document resources. Those entries are already
listed where they cannot go stale. The same functions build the
``typo3://guides`` index. ``knowledge/server-scope.json`` names every document
with the scope the server sends it under. A generated table would be a third
copy, and its size column would change on every edit to a knowledge document.

This page is the half that no generator produces. It says what a resource is,
why the skill URI has the file name, and why two fields of the spec are empty.
That changes only when the shape changes, and somebody writes prose for that
commit in any case.

What holds it
-------------

* ``ResourceSurfaceTest`` — every resource says what it is, declares the size a
  read really costs, and sorts where its audience puts it.

  No resource claims an audience that the protocol does not mean. Every link a
  skill writes resolves onto a URI this server answers.

* ``ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope`` and
  ``ScopeTest::everyPublishedSkillIsAnnouncedByTheScope`` — the coverage names
  each entry, and the description and the priority of the entry come from there.

* ``StdioServerTest::theResourceListCarriesWhatAPickerChoosesBy`` and
  ``StdioServerTest::aTaskWorkflowIsServedWithWhatItSendsItsReaderTo`` — the
  list and both families as they go over the wire. That includes the reference a
  body sends its reader to.

* ``ResourceSurfaceTest::theManifestOfASkillIsComputedFromTheBytesAReadReturns``
  and
  ``StdioServerTest::theSkillsExtensionListsEachWorkflowWithTheManifestAHostVerifiesAReadBy``
  — the manifest names every file of a skill once, with the digest and the size
  of what a read returns.

  The two tests also check that the declaration and the error code reach the
  wire.
