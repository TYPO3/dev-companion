---
id: D-GUI-030
title: A request for a new issue reaches the issue report guide in loose words
date: 2026-10-15
status: open
coveredBy:
  - KnowledgeTest::aRequestForANewIssueReachesTheIssueReportGuide
---

# D-GUI-030 — A request for a new issue reaches the issue report guide in loose words

**`reporting` matches a new issue in the verbs a caller asks for one with. That
is write, draft, prepare, write up, file and report, each with an issue, a bug
report or a ticket. `triage` matches "bug report", "issue report" and "issue
tracker" only weakly, because a new issue and an old one share them.**

The maintainer asked on 2026-10-15 whether "schreib mir ein issue" finds
`typo3_issue_report_guide`. A probe sent loose English requests through
`typo3_task_guide` and `typo3_rule_lookup`.

## Evidence

- Before the change, `typo3_task_guide` named the guide for 4 of 21 requests.
  "write an issue", "draft an issue" and "prepare an issue" matched no intent.
- "write a bug report" matched `triage` and not `reporting`. "report this on
  forge" matched `triage` strongly and `reporting` not at all. So the brief
  offered the check of an old issue for the write of a new one.
- `typo3_rule_lookup` put `any/security/reporting-a-vulnerability` first for
  "prepare an issue" and "prepare a bug report". The search weighs heading,
  title and body, and only that page carried "prepare". Its `whenToUse` takes no
  part in the score.
- After the change, every request in
  `KnowledgeTest::aRequestForANewIssueReachesTheIssueReportGuide` matches `reporting`, and the
  page on a core bug report ranks first. "report a security vulnerability" still
  ranks the security page first.

## Decided

- Phrases rather than a verb and a noun matched apart. The intents match
  phrases throughout, and a second kind of needle is a concept for one intent.
- "bug report", "issue report" and "issue tracker" move to `triage`'s
  `matchWeak`. A triage still matches strongly on its own work: "triage",
  "still happens", "already fixed".
- "on forge", "in forge", "from forge", "off forge" and "forge.typo3.org" stay
  strong for `triage`, for the reason `D-SKL-081` gives. So "report this on
  forge" matches both intents strongly, and the brief names both.
- The opening of the page on a core bug report says "write a new issue" and
  "prepare one", and that a ticket is an issue on Forge. That is where the
  search reads.
- A request in German stays unmatched. The knowledge is English, and the server
  tells the agent to translate.

## Wrong if

- A triage request that names no work of its own, such as "look at this bug
  report on forge", comes back without the triage skill. Then the subject words
  go back to `match`, and `reporting` wins by another rule.
- A forward run shows that an agent picks `typo3_issue_report_guide` from its
  description alone. Then the phrases cost upkeep and buy no call.
