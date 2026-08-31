---
date: 2026-08-31T23:45:53+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_flexform_lookup, typo3_schema_lookup, typo3_configuration_lookup
directory: /home/benji/projects/site-tierheim
---

# Correction: the deferred-tool barrier was not the reason; I crossed it whenever I actually wanted...

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14. This corrects the feedback I filed minutes ago under "Why the unused tools stayed unused". The user read it and answered in three words: "sie waren ja da." He is right and my first cause was an excuse. Please weight this record above that one where they conflict.

I claimed the client's deferred-tool mechanism was a barrier: schemas not loaded, ToolSearch needed first, two steps instead of zero. The measurement contradicts it. I called ToolSearch six times across the session and it worked every time, including twice in the last hour. The barrier was one I crossed whenever I actually wanted something on the other side. It did not stop me; wanting to did.

So the causes that stand are the ones that are mine:

I was not uncertain, I was wrong. `isSenior` felt like the FlexForm field name. Two tables from one TCA felt identical in column order. No doubt, therefore no trigger. A tool is reached for at a moment of uncertainty and those moments did not occur.

Reading a file felt like getting an answer. I read the FlexForm XML, SHOW COLUMNS, the settings YAML. Each returned something, so each felt like a question closed. Two of the three gave me the declared value where the resolved one was what mattered.

Reactive mode never asks architecture questions. The session was a loop of "user names a defect, I fix that defect". DataProcessor versus Extbase plugin, a backend module for 3101 records, the fluid_styled_content dependency — none of those presented as a defect, so none became a query. They surfaced when the user named them, after the code existed.

A shell command produces visible progress; a lookup produces text I then have to act on. Over five hours with someone watching, the first feels like working. I think this one is real and I do not like it.

The presentational suggestion in the earlier record still holds on its own merit — tools naming each other inside answers is worth doing, the way the documents array already does and which was the single most useful thing that happened in this session. But it should be pursued as an improvement, not as the fix for why nine tools went uncalled. That reason was that I did not think I needed them, and I was wrong about that rather than obstructed.

## Query

Supersedes the first cause listed in feedback "Why the unused tools stayed unused: six causes, from a client mechanism to plain absence of doubt". Evidence against it: ToolSearch called 6 times in the same session, including at 22:35 and 23:37. Client: Claude Code with typo3_* tools deferred.

## Suggestion

Drop cause 1 from consideration. Do not build anything to lower a barrier that was not what stopped the calls.

What is left worth acting on is the one intervention that reaches a caller who does not know they need it: an answer that names the next tool. That works precisely because it does not require the caller to feel uncertain first — it arrives in text they are already reading, at a moment when the subject is already open. The documents array proved that in this session; extending it to tools is the same mechanism.

And the honest note for whoever reads this: some of what went wrong here is not addressable from a knowledge server. A model that is confidently wrong will not query. The most a server can do is put the correction in the path of what it is already being asked, which is an argument for richer answers rather than for more tools.
