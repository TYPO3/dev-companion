---
id: R-GUI-005
title: 'The commit-message guide is also a prompt'
status: held
heldBy:
  - StdioServerTest::theCommitMessageGuideIsAvailableAsAPrompt
---

# R-GUI-005 — The commit-message guide is also a prompt

**The server exposes the commit-message guide as an MCP prompt too, so a user
can invoke it without a search for the tool first.**

The prompt delegates to the guide and keeps no second set of commit-message
rules.

## From

The SDK prompt primitive stood unused while the guide a user invokes most
readily already existed (2026-07-30).
