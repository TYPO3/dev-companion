---
id: R-AUD-006
title: 'The query language is English'
status: held
heldBy:
  - ScopeTest::theQueryLanguageIsStatedWhereTheCallingAgentReadsIt
---

# R-AUD-006 — The query language is English

**The query language is English, and the server says so to the agent rather than
to the user.**

The corpus is English and the match is lexical. So a query in another language
reaches only the words the two share, the technical loanwords, and otherwise
comes back empty. Support for a second language means a translated corpus, not a
translated query. The agent translates the subject before the call and the
answer back afterwards. That instruction is the entire mitigation for a limit
nothing else covers, so it stands where an agent reads it. That is a length as
well as a place, and
[`R-ANS-013`](../answers/ans-013-the-instructions-fit-what-a-client-keeps.md)
holds that half. The places are the `instructions` sent at initialize and
`typo3_server_scope` for a client that does not surface them. The third is the
free-text parameters of the tools that match against prose. This binds what may
enter `knowledge/` too: a statement in another language is one nothing can find.

## From

A German-phrased task that reached four of twelve hints by loanword accident,
six clean misses, and one confidently wrong answer (2026-07-30).
