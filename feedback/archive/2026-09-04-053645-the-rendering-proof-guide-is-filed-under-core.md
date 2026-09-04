---
date: 2026-09-04T05:36:45+00:00
category: idea
status: closed
closed: 2026-09-04
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3_project_describe
directory: /home/benji/projects/site-tierheim
---

# The rendering-proof guide is filed under core/ and I skipped it while doing exactly what it descr...

## Observation

Task: remove fluid_styled_content from a project sitepackage and rebuild its content element frame, then show that the frontend still renders what it rendered before.

typo3_project_describe listed 21 guides. My client did show me that list — it came back inside the tool result, so this is not a case of a resource list never reaching the session. I read it, and I called typo3_rule_lookup zero times.

The guide I should have opened is core/testing/proving-a-rendering, whose "when" reads: "When a finding turns on what a rendering contains and nothing in the checkout produces it, so the value is the unknown rather than the expectation." That is precisely the session. What I did instead, worked out from scratch: curl twelve representative URLs plus an animal detail page into a before/ directory, chosen by querying which page slugs carry which CType so every element type was covered; make the change; re-run extension:setup and cache:flush; re-curl into after/; diff each pair after inserting a newline between adjacent tags so the diff is readable; then grep the union of all diffs for any line not explained by the three changes I intended, which is what proved nothing else moved.

I skipped the guide on its id prefix. I was working in a Composer project — typo3_project_describe had just told me kind "composer-project", origin "project" for both extensions — and read "core/" as "this is for patches to the TYPO3 core repository", the same way I correctly skipped core/contribution/changelog and core/contribution/gerrit-workflow. The other testing guides in the same list are prefixed "any/" (any/testing/browser-check, any/testing/proving-a-condition), which reinforced the reading: if this one were for me too, it would have said any/.

So the prefix is doing classification work it cannot carry. A procedure for proving what a rendering contains is not core-specific in any way I can see; nothing about curling a page and diffing it depends on being in the core repository.

Two smaller notes from the same list. any/testing/browser-check was named by typo3_task_guide as the one guide matching this task, and I did not read it either — I ran the project's declared Playwright suite from a note in my own memory that says it must run on the host rather than through ddev. And typo3_task_guide's own answer already carries a "guides" array, so the routing is there; what stopped me was the name, not the absence of a pointer.

## Query

typo3_project_describe on a Composer project (kind: composer-project) returned a guides array of 21 entries including {"id":"core/testing/proving-a-rendering","title":"Proving What a Rendering Change Renders","tool":"typo3_rule_lookup"}. I called typo3_rule_lookup zero times in the whole session.

## Suggestion

*Trimmed on 2026-09-04. The re-filing was refused and the scope field was built
— `D-ANS-150` carries both, and `todo/open/T-260904-f190.md` carries what is
left.*

What is still open is the procedure this session worked out from scratch, which
nothing here carries: proving a rendering in an installation you can request
over HTTP. Choose the URLs from the content that exists rather than from what
the templates allow, capture them before the change, make the change, capture
them again, and read the diff — the session inserted a newline between adjacent
tags first and then grepped the union of every diff for a line none of the
intended changes explains, which is what proved nothing else moved.
