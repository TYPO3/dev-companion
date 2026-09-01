---
date: 2026-08-19T09:04:01+00:00
category: idea
status: closed
closed: 2026-09-01
model: claude-opus-5[1m]
tool: typo3_reference_list, typo3_script_lookup, typo3_rule_lookup, typo3_server_scope
directory: /home/benji/projects/blog
---

# Tools arrived as bare names with no schemas, and the document list was never seen at all

## Observation

Task: update the JavaScript dependencies of EXT:blog and close the Dependabot PRs. Full transcript, no summary prefix. This reports how the server presented itself to one client, which it has no other way of learning.

How I saw the tools. My client (Claude Code, VSCode extension) deferred every tool of this server. They arrived as a bare list of 27 names in a system-reminder with no descriptions and no schemas, plus the server's instruction block. Calling one directly fails; a ToolSearch has to load its schema first. So the only thing carrying meaning at decision time was the name, and the instruction block's "what to call for what" list — which names six tools out of 27. The other 21 were names alone.

The document list was never seen. typo3_reference_list is one of the 21. I never called it, so I finished the working session without learning which documents this server offers as resources, or that typo3_rule_lookup takes a documentId that some other call has to supply. I read no document whole. Where I assembled a procedure myself — the DataTables 1.x to 3.x migration — the page I wanted would have been called something like "upgrading a vendored backend JavaScript library in an extension", and I accept that is probably not this server's subject. But I could not even have checked, because I did not know the list existed as anything I could enumerate. I also never called ListMcpResourcesTool or ReadMcpResourceTool, both offered by the client, for the same reason: nothing pointed at them.

A name I read and passed over. typo3_script_lookup. From the name alone I could not tell what it covers, and my guess was TYPO3 CLI or console scripts. In a session whose entire subject was build scripts — npm scripts in package.json ("build": "webpack", "watch": "webpack --watch"), a webpack config, a Dart Sass pipeline — a tool called "script lookup" is precisely the name that should have made me look, and I passed it by. I still do not know whether it would have helped. If it covers anything about an extension's build scripts, the name is not reaching a model that had exactly that task; if it covers something else entirely, the name is actively misleading in this context. Either way it cost the server a call it might have answered.

typo3_server_scope, which the instruction block describes as the thing to call "when it is unclear whether this server can answer a question at all", was the one call that would have resolved all of the above in one round trip. I did not make it, because I never reached the state it is written for: I was not unclear whether the server could answer. I was confident it could not, and confidence does not trigger a scope check. That is the failure mode worth recording — the orientation tool is guarded by a doubt the session never had.

What I assumed, and whether it held. I assumed a server about TYPO3 core, extensions, TCA, Fluid, backend modules, labels and icons would hold nothing about npm dependency management, webpack, or a third-party DataTables upgrade. That assumption held for the third-party library itself. It did NOT hold for the TYPO3 side of the same task — a backend CSS class, the supported majors, the commit convention — all of which this server names as its subject and all of which I worked out or skipped myself. The assumption was right about the server's boundary and wrong about where my task sat relative to it.

## Query

Not asked. Calls never made, in a session that had reason to make them: typo3_server_scope (orientation, would have settled whether the server covered any part of the task), typo3_reference_list (never learned which documents exist), typo3_script_lookup (name read, guessed to mean CLI scripts, passed over while working on npm build scripts). Client: Claude Code VSCode extension, all 27 tools deferred as bare names requiring ToolSearch before invocation.

## Suggestion

Two things, both about a client that shows names and nothing else.

The tool names have to carry their scope, because for a deferred client the name is the entire description at decision time. typo3_script_lookup is the case in point: whatever it covers, add the noun to the name or get the noun into the instruction block's "what to call for what" list, which currently names only six of 27 tools. The 21 unnamed ones are invisible in practice.

And the instruction block should name typo3_reference_list among them, with one line saying the documents are enumerable. A model that does not know a list exists cannot decide it is irrelevant — it simply never forms the thought. One sentence in the opening block ("typo3_reference_list enumerates the documents; typo3_rule_lookup reads one whole") would have cost nothing and would have told me, at the only moment I read anything from this server, that there was a shelf to look at.

Lower confidence, but worth saying: typo3_server_scope is written for a doubt that a confidently-wrong session never has. If its content instead arrived unprompted in the opening block — even three lines of "covered / not covered" — it would reach the sessions that most need it, which are exactly the ones that will never call it.
