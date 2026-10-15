---
id: D-GUI-029
title: A commit body is the decision in one or two sentences
date: 2026-10-15
status: open
coveredBy:
  - CommitMessageTest::aMessageWithoutABodyIsToldToCarryOne
---

# D-GUI-029 — A commit body is the decision in one or two sentences

**Every commit message has a body of one or two sentences. It states the
decision behind the change and what matters that the code does not show. What
the diff shows stays out, and `typo3_commit_message_guide` warns on a message
without a body.**

The guide said "Keep the body brief. Say what changed and why." The maintainer
found the bodies agents wrote with it too long on 2026-10-15. They carried what
the diff shows, such as a class that now sits on an element, beside the
decision that put it there.

## Evidence

- The official appendix asks for a body that is "brief, but yet descriptive so
  people reviewing your change get an idea what they need to look out for". It
  says "Do not repeat the code change itself in the body text". It treats the
  body as optional.
- This repository writes by the same guide, `D-DOC-013`. Over the 300 commits on
  `main` before 2026-10-15, 95 bodies run five lines or more, and 45 commits
  have no body.

## Decided

- A body on every message, in both workflows. The maintainer chose that over the
  optional body the appendix allows. So the rule is stricter than the core's,
  and the check is a warning rather than an error.
- One or two sentences. The maintainer asked for a body that is really short.
- What matters and the code does not show belongs in the body, because the
  appendix asks for it as what a reviewer has to look out for.

## Assumed

- That one or two sentences hold the decision of a single-purpose change.

## Wrong if

- A core reviewer asks for a longer body on a patch that kept to the rule. Then
  the length is wrong for the core workflow, and the two workflows part.
- The warning fires on core commits that merge without a body and nobody missed
  one. Then the body is the core's choice to leave out, and the check holds for
  `workflow="project"` alone.
