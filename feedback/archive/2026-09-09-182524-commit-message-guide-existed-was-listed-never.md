---
date: 2026-09-09T18:25:24+00:00
category: idea
status: closed
closed: 2026-09-09
model: claude-opus-5[1m]
tool: typo3_commit_message_guide, typo3_rule_lookup, typo3_project_describe
directory: /home/benji/projects/typo3-cms
---

# Commit message guide existed, was listed, never opened - five user-driven amends

## Observation

Task: review and finish TYPO3 core Gerrit change 93620 ("[WIP] Toplevel access"). The single most expensive part of the session was the commit message, and this server had the answer the whole time.

I wrote a message from AGENTS.md rules alone (tag, imperative subject, sub-72-char lines, Resolves/Releases/Signed-off-by, keep the Change-Id). It passed every mechanical check I knew about - max line 66 characters. The user then rejected it four times in a row, each time on style, not on rules:

1. "ich mag die commit message nicht. es sollte eher sowas wie 'Unify the top level window access' sein" - subject was too indirect.
2. "die commit message selbst ist auch zu lang"
3. "sie sollte sich auf das wesentliche konzentrieren"
4. "bitte kuerze soweit wie moeglich, nicht pauschal 3 absaetze"
5. "wir wollen auch nicht zaehlen, das machen wir nicht" - I had written "in 68 files" and "in four spellings". Counts are apparently not done in TYPO3 core commit messages.

Five amends. The final body is three lines. None of that is in AGENTS.md, which covers only the mechanical constraints.

typo3_commit_message_guide was in my tool list from the start, described as "the commit message, yours as much as the core's, and its branches". I never called it - not once, across a session that produced six versions of a commit message. typo3_rule_lookup with documentId core/contribution/commit-messages ("TYPO3 Core Commit Message Rules") was also offered, in the guides array of typo3_project_describe's answer, which I read at the very start of the session and did not come back to.

So this is not missing knowledge. It is knowledge that was named twice in my context and stayed shut, because nothing at the moment of writing pointed at it. I was in a Bash heredoc writing the message; the guides list had scrolled past roughly 80 tool calls earlier, buried in a ~6KB JSON payload about PHP constraints and npm scripts.

I cannot say whether the guide would have caught "no counts" and "as short as possible" specifically - I did not read it, and it would be dishonest to claim it would have. That is worth checking against the guide's actual content.

## Query

Not a query that failed - a tool never called. typo3_commit_message_guide: 0 calls. typo3_rule_lookup with documentId core/contribution/commit-messages: 0 calls. Both were visible: the first in the tool list, the second in the guides array returned by typo3_project_describe at the start of the session. Six commit message versions were written; the user rejected five on style grounds not covered by the repository's AGENTS.md. Final accepted body: "The top level window was reached by hand across the JavaScript stack, in different spellings and mostly unguarded. @typo3/core/utility/top-window now provides topWindow() and topDocument() for this."

## Suggestion

Two things, both about timing rather than content.

First: if the commit message rules do not already say "no counts of files or occurrences" and "as short as the change allows, one paragraph is normal", they should. Those were the two corrections the user actually made, and they are house style a model cannot derive from AGENTS.md.

Second, and more useful: the guides list arrives as a field of typo3_project_describe, at the start of a session, alongside PHP constraints and npm scripts. That is the wrong moment for a commit-message guide - the caller is orienting, not writing. Something needs to surface it at write time. typo3_gerrit_lookup answering about a change the caller owns and is amending would be one place; the answer already knows the change is NEW, WIP and owned by the person asking.
