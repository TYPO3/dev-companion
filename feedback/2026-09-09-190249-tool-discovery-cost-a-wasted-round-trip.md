---
date: 2026-09-09T19:02:49+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe
directory: /home/benji/projects/typo3-cms
---

# tool discovery cost a wasted round trip: unqualified names find nothing in a client that prefixes...

## Observation

Task: rebase a Gerrit chain onto main; first action after loading the skill was to reach this server's tools.

My client (Claude Code) defers MCP tool schemas: they are listed by name only, and a ToolSearch call has to fetch each schema before the tool can be invoked. In that list every tool of this server carries the qualified name mcp__typo3-dev-companion__typo3_project_describe.

My first ToolSearch used the bare names - select:typo3_project_describe,typo3_gerrit_lookup,... - and returned "No matching deferred tools found". Repeating it with the mcp__typo3-dev-companion__ prefix returned all six. One wasted round trip, at the very start, before anything had been established.

What makes this worth filing rather than my own carelessness: references/base.md in the patch-checkout skill warns about exactly this prefix, and I had just read it. Its warning is:

  "A client may carry this server's name in each tool's - mcp__<server>__typo3_project_describe - so a search for the bare name comes back empty where the server is connected. Look for the qualified form before reading an empty result as an answer about the server."

That is aimed at one failure mode - misreading an empty result as "the server is not connected" - and it is correct and I did not fall for it. It is not aimed at the one that actually cost me: the bare name does not work as a SEARCH ARGUMENT either, so the very first call fails. The two are a sentence apart and the second is the one a session hits first.

Also worth saying: the fallback path in base.md ("No typo3_ tool in this session, or a first call that errors: stop, say this workflow needs the server and it is not there") is exactly what a less careful session would have triggered on that empty answer, and it would have abandoned a working server. The warning prevented that. Widening it by one clause would prevent the round trip too.

Suggested wording for base.md: "...comes back empty where the server is connected. The qualified form is also what a tool-schema search has to be given: a select: query on the bare names returns nothing even though the tools are there. Look for the qualified form before reading an empty result as an answer about the server."

Everything else about discovery worked. The server's own instructions block named typo3_project_describe as the first call and typo3_task_guide as the second, and I followed that order; the guides list inside typo3_project_describe is how I learned the rule documents exist, since my client showed me no resource list at all.

Two tools I read in the listing and deliberately passed over, which the server otherwise has no way of learning: typo3_configuration_lookup and typo3_service_lookup. The patch deletes SvgImageProcessor and rewires SYS/fal/processors and core's Services.yaml, and typo3_task_guide's own fal-processing hint explicitly told me typo3_configuration_lookup with SYS/fal/processors answers what is really registered. I skipped it because I judged the task to be a rebase rather than a behaviour review, and verified the deletion left no dangling references with a grep instead. That assumption held for what I was asked to deliver, but the resolved processor list after deleting a registered processor is a thing I asserted from a grep where a lookup was offered.

I also never called typo3_hint_lookup: typo3_task_guide returned hints inline with omittedHints empty, which base.md says discharges that step. That rule worked exactly as written.

I never called typo3_changelog_lookup either. base.md exempts work that produces no change, and I read a rebase as borderline. My assumption was that the changelog carries user-facing API changes and would therefore not carry the two things that actually bit me - a merged bugfix inside a rewritten class, and a php-cs-fixer rule main newly enabled. I believe that assumption held, but I never tested it, and both of those I found by git log against the checkout instead.

## Query

ToolSearch query "select:typo3_project_describe,typo3_gerrit_lookup,typo3_rule_lookup,typo3_task_guide,typo3_hint_lookup,typo3_test_run_guide" -> "No matching deferred tools found". Repeated as "select:mcp__typo3-dev-companion__typo3_project_describe,..." -> all six returned.
