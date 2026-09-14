---
id: D-FBK-039
title: A mangled name is rewritten once, and the comparison carries the rest
date: 2026-08-03
status: open
coveredBy:
  - FeedbackTest::aNameIsFoundHoweverItsSeparatorsAreSpelled
  - FeedbackTest::everyNameTheCorpusCarriesIsSpelledTheWayThisProjectSpellsIt
---

# D-FBK-039 — A mangled name is rewritten once, and the comparison carries the rest

**The 43 mangled names get one rewrite to the form this project uses, and the
comparison of two names still ignores their separators.**

Both, in the commit that ends the strip. The given form kept makes the stored
form heterogeneous, so the filter has to reconcile forms whatever happens to the
corpus. What the rewrite buys is the other half of the report: a name a grep
finds.

## Evidence

- `Channel::toolNames()` stripped everything outside `[a-z0-9_]`, so a hyphen
  went and an underscore stayed. 43 names in the corpus resolved to a tool this
  server registers or a skill this repository ships once the separators fell
  away. None of them stood that way in the file.
- One tool sat under two identifiers. `typo3_documentation_lookup` in the front
  matter of twelve feedback and `typo3documentationlookup` in five, both from
  sessions that named the same tool.
- The mangled names are not guessed back. Each one's separatorless form matches
  exactly one name from `Registry::definitions()` or one directory under
  `skills/`, which is the same comparison the filter makes.
- What that comparison does not resolve stays as it arrived. A name for a tool
  that has since had a rename, and a client's wrapper
  (`mcp_typo3cmsmcp_typo3_feedback_record`). And the run-together names an
  earlier version of the same strip left behind.

## Decided

- Both, rather than one of them. A match on the separatorless form is what
  `D-ANS-006` already does for an identifier a caller looks up. It is the cost
  of a store of what arrived. The rewrite is what the report actually asked for,
  and it is a one-off edit rather than a rule anything carries.
- No migration command. It resolves names against a registry and a directory
  that move, so a second run a month from now would be a different rewrite.
  `FeedbackTest::everyNameTheCorpusCarriesIsSpelledTheWayThisProjectSpellsIt`
  reads the corpus instead and fails on a name that arrives mangled again.
- The front matter only. The prose of a feedback is a session's report and
  nobody edits it. The one file that names a skill in its body already carries
  the hyphens the session typed.

## Assumed

- That a stored name which resolves to exactly one tool or skill is that tool or
  skill. Two names that collapse onto one separatorless form would make the
  resolution wrong and the filter ambiguous with it. None of the names this
  server registers or this repository ships does.
- That the archive tolerates this edit. Nothing goes and no report changes; what
  changes is a name back to what the session passed in.

## Wrong if

- A feedback filtered by tool comes back with another tool, because two names
  met on their separatorless form.
- The next damage is a different one and the corpus needs a second sweep. Then
  the data rewrite was the cheaper half of a problem that lives in the writer.

## Since then

The second **Wrong if** happened. A session recorded a tool name from outside
this server and read it back folded. The writer kept the separators and still
lowercased what remained. That is what this entry named as the sign that the
data rewrite had been the cheaper half. The comparison carried the other half of
it. It stripped everything outside the alphabet, so a stored form would lose its
capitals rather than fold them. Both were one repair and it landed the same day,
with exactly one damaged name in the corpus.

What made this cheap to miss is worth a name. Everything this server registers
is lower case, so nothing in its own vocabulary could show the fold. The one
kind of name that could is a name from outside it. That is the kind a session
writes down when it reached for something else instead.
