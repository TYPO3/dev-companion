---
id: D-ANS-166
title: Initialize declares the capabilities the server honours
date: 2026-09-19
status: open
coveredBy:
  - StdioServerTest::theCapabilitiesSayWhatTheServerDoes
---

# D-ANS-166 — Initialize declares the capabilities the server honours

**`Server\Factory` hands the SDK the capabilities itself: tools, resources
without `subscribe`, prompts, and completions the `commit_message` prompt
answers. Logging is gone.**

The SDK's own detection had declared `logging`, `completions` and
`resources.subscribe: true` for every server it builds. A client that reads a
capability takes it for what the server does, and three of them were not.

## Evidence

- **Read on 2026-09-19** against the server over stdio, with the specification
  at revisions 2025-11-25 and 2026-07-28. The server sends no
  `notifications/message` and no `notifications/resources/updated`, and
  `completion/complete` answered `values: []` for every argument. The 2026-07-28
  revision deprecates the logging feature, and a new implementation does not add
  it. Its lifecycle page says both parties use only the capabilities they
  negotiated.
- `Mcp\Server\Builder` builds `ServerCapabilities` with `logging: true`,
  `completions: true` and `resourcesSubscribe` on for every server that has a
  resource. `setCapabilities()` is its override, and the builder folds the
  extensions of `D-ANS-163` into a caller's capabilities too.
- The `keyword` and `workflow` arguments of the `commit_message` prompt take the
  sets `CommitMessage::PROJECT_KEYWORDS` and `CommitMessage::WORKFLOWS`. The SDK
  reads a `#[CompletionProvider]` on a handler's parameter, which is its main
  path for a completion. With it the two answer `TASK` for `T` and both
  workflows for an empty value.

## Decided

- `Factory::capabilities()` declares tools, resources, prompts and completions,
  and neither `logging` nor `resources.subscribe`. The maintainer chose this on
  2026-09-19 over declaring tools, resources and prompts alone, so a
  slash-command client completes the two closed arguments.
- The two sets stand once, in `CommitMessage`, and the tool's schema and the
  prompt's providers read them from there.
- `documentation/server/initialize.rst` prints the capabilities as the server
  sends them.

## Wrong if

- A tool or a resource starts to send a log message or a resource update. Then
  the capability comes back, and the test that holds this entry moves with it.
- A client refuses a server whose capabilities carry no `logging`. Then the
  declaration returns and this entry says why.
