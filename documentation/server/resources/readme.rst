:navigation-title: Resources

The resource surface
====================

What this server offers for a pick, and what a picker chooses by.
`Factory::resources() <../../../src/Server/Factory.php>`_ declares the list and
``Factory::skillReferences()`` the one template behind it;
`Sdk\ResourceHandler <../../../src/Sdk/ResourceHandler.php>`_ and
`Sdk\SkillReferenceHandler <../../../src/Sdk/SkillReferenceHandler.php>`_ answer
a read. Everything offered is a file this package ships, so reading one reaches
no installation and no network.

The :doc:`tool surface <../tools/index>` is the other thing a client gets. It
has a page per tool because a tool has a schema and a recorded answer. This is
one page, because what a resource obliges is the same for every entry, and
``typo3://guides`` enumerates the entries themselves live.

Picked, not called
------------------

**The host application or the user picks a resource, where the model calls a
tool in the middle of a task.** That is the protocol's own distinction, and it
decides everything else about this surface.

A tool explains itself in the answer it returns. A resource has its list entry
and nothing else. So ``description``, ``annotations.priority`` and ``size`` are
what the choice rests on rather than decoration,
`R-ANS-022 <../../../requirements/answers/ans-022-a-resource-is-picked-out-of-a-list.md>`_.

The model's route into the same prose while it works is a tool.
``typo3_rule_lookup`` searches the documents. ``typo3_task_guide`` names the
workflow that owns the task it recognized and the document that describes that
work. It names the document as the ``typo3_rule_lookup`` call that reads it
rather than as the URI a client may render nowhere.

The four shapes
---------------

* **``typo3://guides``** — the index: the purpose, coverage and routing this
  client gets, plus every document and skill with its URI, and each skill's
  references. The one to read first, and the one that enumerates the rest.
* **``typo3://guides/{documentId}``** — one document from
  ``knowledge/documents/``, which is also the corpus ``typo3_rule_lookup``
  searches. Mostly the core's own process.
* **``typo3://skill/{skillId}/SKILL.md``** — one published task workflow from
  ``skills/``. Mostly extension, sitepackage and project work.
  ``bin/typo3-dev-companion install`` writes the same file into the client's own
  skills directory, and this is the route for a client that never ran it.
  Published means the skill's own front matter does not declare it a draft. One
  that does is a directory in ``skills/`` and appears nowhere.
* **``typo3://skill/{skillId}/references/{file}``** — what a workflow hands over
  at a step. A resource template rather than one list entry each, because a
  reader follows these from the body that names them. A checklist offered beside
  its own workflow is an entry nobody can choose between.

Two families rather than one, because the documents alone serve one audience.
Most of that corpus is the core's own process, and most of the workflows are the
work outside it. Both on offer is what leaves each of the three audiences of
`R-AUD-001 <../../../requirements/audience/aud-001-core-extension-and-site-work-are-each-served.md>`_
something to pick.

Which of them an entry holds for comes off ``knowledge/server-scope.json`` and
nowhere else. A covered topic names every document and every published skill,
and that topic's ``scope`` is what the description and the priority derive from.
``ScopeTest`` holds it in both directions, so a skill no topic names fails the
suite rather than reaching an extension author as core-only.

``TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`` reaches the index and not the list.
``Coverage::offered()`` drops a topic nothing left can answer, so the index a
client reads matches the tools it has; the resources stay. Excluding a tool
takes away a call, not a document.

The skill URI
-------------

**A skill is a directory, and a pick has to deliver the whole of what the picker
chose.** The body is short routing, and every one of them opens by sending the
reader to ``references/base.md``. Served at ``typo3://skill/{id}/SKILL.md``, the
relative links that prose already carries resolve by ordinary URI rules onto the
URIs the template answers. Nothing gets a rewrite and nothing gets assembled.
The published bytes go over the wire as they stand.

Dropping the file name is the simplification not to make. Those links would
resolve one segment higher, onto URIs nothing serves, and the body would still
read complete. The first reader to follow one is how anybody finds out.

``references/base.md`` is a file in no skill in this checkout.
`skills/base.md <../../../skills/base.md>`_ is the single copy, written into
each published directory by ``Installer`` and served here from that same file
(`D-SKL-001 <../../../decisions/task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md>`_).
So the resource is the file a client would have had if it had run the install.

What a picker reads
-------------------

* **``description``** — what the entry is and who its answers oblige. A
  document's comes from the covered topic. A skill's comes from its own front
  matter. In front of it stands the sentence that says it is a workflow to
  follow rather than a page to read. Neither stands a second time, so neither
  can drift from what it describes.
* **``annotations.priority``** — the ordering, and nothing else. The index sits
  above everything it lists. What holds wherever the caller works sits above
  what stops at the core. The references sit below all of them, because a reader
  reads one at the step that sends them to it. The numbers in ``Factory`` are a
  scale a picker sorts by, and the distance between two of them carries no
  meaning.
* **``size``** — the bytes the handler really serves, so a client knows what
  reading one costs. For the index that is the encoded JSON rather than a file
  on disk, and ``ResourceSurfaceTest`` asserts every declared size against what
  a read returns.

Two fields the spec has stay absent, and the first is where the confusion is:

* **``annotations.audience``** is the protocol's ``user`` and ``assistant``, the
  SDK's ``Role`` enum. It is never the three audiences of ``R-AUD-001``, which
  are not values it takes. Everything here is for both roles, so the field says
  nothing and stays off, and ``ResourceSurfaceTest`` fails on a resource that
  sets it.
* **``annotations.lastModified``** is in the spec revision the SDK speaks, and
  no ``Mcp\Schema\Annotations`` at the mcp/sdk version in ``composer.lock``
  carries it. That class has ``audience`` and ``priority`` alone. The record
  says unavailable rather than fakes it through ``_meta``.

What is generated
-----------------

``bin/cli tools:index`` writes the tool reference because each page restates a
description and two schemas that a class declares. The installed skill catalog
at :doc:`../../usage/task-skills/index` is a generated page for the same reason.
It is where a person compares and reads the published workflows before the
install. ``bin/cli documentation:prepare`` copies the same Markdown the
installer publishes into the site source and adds one embedding page per skill.

There is no document-resource catalog in the manual. Those entries already stand
enumerated where they cannot go stale. The same functions build the
``typo3://guides`` index, and ``knowledge/server-scope.json`` names every
document with the scope it goes out under. A generated table would be a third
copy, and its size column would change on every edit to a knowledge document.

What stands here is the half no generator produces. What a resource is, why the
skill URI carries the file name, and why two fields of the spec are empty. That
changes when the shape changes, which is a commit somebody writes prose for in
any case.

What holds it
-------------

* ``ResourceSurfaceTest`` — that every resource says what it is, declares the
  size a read really costs, and sorts where its audience puts it. That it claims
  no audience the protocol does not mean. Also that every link a skill writes
  resolves onto a URI this server answers.
* ``ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope`` and
  ``ScopeTest::everyPublishedSkillIsAnnouncedByTheScope`` — that the coverage
  names each entry, which is where its description and its priority come from.
* ``StdioServerTest::theResourceListCarriesWhatAPickerChoosesBy`` and
  ``StdioServerTest::aTaskWorkflowIsServedWithWhatItSendsItsReaderTo`` — the
  list and both families as they go over the wire, including the reference a
  body sends its reader to.
