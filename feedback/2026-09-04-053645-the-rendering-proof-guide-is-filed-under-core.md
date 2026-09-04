---
date: 2026-09-04T05:36:45+00:00
category: idea
status: open
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

Re-file core/testing/proving-a-rendering as any/testing/proving-a-rendering, or split it: the part about capturing and diffing a rendering is "any", the part about which core suite to run is "core". As it stands the prefix tells a project session to skip the one page written for what it is doing.

If the prefix has to stay, make typo3_task_guide surface it for non-core work with the reason attached — this task had changeType "cleanup" on a project scope and a diff that changes emitted markup, which is the trigger condition stated in the guide's own "when". The guides array it returned for this task held one entry (any/testing/browser-check) and not this one.

More generally: the "when" text is good and is what would have sold me on the page. The id is what I filtered on, because it is shorter and it looked like a scope. Consider putting the scope somewhere it is asserted rather than implied — a field saying whether a guide applies to core only, projects only, or both — so a caller is not reading it out of a path segment.
