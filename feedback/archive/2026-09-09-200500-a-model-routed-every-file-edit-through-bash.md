---
date: 2026-09-09T20:05:00+00:00
category: idea
status: closed
closed: 2026-09-09
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_task_guide, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# A model routed every file edit through Bash+Python rather than the dedicated edit tool

## Observation

Task: review Gerrit change 95482 against a local core worktree, write tests for the open points, implement one production fix. This feedback is adjacent to the one titled "A core patch review ran end to end with zero calls to this server" and reports the mechanism behind it, which only became visible when the person running the session asked why so much Python was in the transcript.

Counted off the transcript: 96 Bash calls, 0 calls to the client's own Read, Edit or Write tools, 0 MCP calls, 0 skill activations. Of those Bash calls, nine were python3 heredocs that performed exact string replacement in PHP source files — inserting test methods before a closing brace, adding use statements, replacing a method body. Four more parsed JSON from the Gerrit REST API, which is a fair use.

The nine edits are the finding. The client offers a purpose-built Edit tool whose schema was loaded and available at zero marginal cost, and which guarantees what my Python blocks had to assert by hand: I wrote `assert old in src` and `assert src.count(old) == 2` into those heredocs, reimplementing the match-uniqueness check that Edit performs itself. The approach cost three levels of quoting (PHP inside Python inside Bash) and produced one real defect: writing a new test file via heredoc, I emitted a `use` statement for a class that does not exist (VirtualAppPackageTestAccessor) and needed a second call to strip it back out.

Why it happened: this session carried a harness instruction to do work through Bash wherever Bash can accomplish it — read with cat and sed, search with grep, "make file changes with sed, heredocs, or short scripts, rather than using the dedicated Read, Edit, or Write tools". I followed it. Once inside Bash, Python is the only reliable way to do exact multi-line replacement in PHP, because sed cannot handle the braces, slashes and quotes safely.

Why this is reportable here rather than only to the client: it is the control case for the routing question. Edit is a first-party tool, already loaded, cheaper and safer than what I replaced it with, and I bypassed it anyway because an instruction made Bash the default. An MCP tool on this server sits strictly further out — it needs a ToolSearch round trip before it can even be called. If a model in this environment routes past a loaded, purpose-built, first-party tool, the probability that it reaches a deferred third-party one is close to zero. That is a harder constraint on this server's reach than any description or naming problem, and it is invisible from the server side, which sees only the calls that were made.

## Query

Session task: review the final patch set of Gerrit change 95482 against a local review worktree, substantiate the open points with tests, implement one fix. Follow-up question from the person running the session: "warum nutzt du soviel python? brauchen wir das?" — which prompted counting the actual tool routing off the transcript.

## Suggestion

Nothing to change in the knowledge base for this one; it is filed because the server cannot observe it and because it bounds what better descriptions can achieve.

Two things worth drawing from it. First, when measuring adoption, treat "no calls from a session" as ambiguous between "the tools were not useful" and "the model never entered a mode where any tool call was cheap" — this session was the second, and the two look identical in a call log. Second, the tools most likely to survive a Bash-first routing are the ones answering something Bash genuinely cannot: a convention, a judgement call, a cross-version fact, the state of a review. Anything a grep over the checkout can answer will lose, reliably, and is worth deprioritising in favour of the rest.

For the people running such sessions, the concrete recommendation is to scope a Bash-first instruction to reads and searches and leave writes to the dedicated edit tools, which fail loudly on an ambiguous or missing match instead of silently writing wrong code.
