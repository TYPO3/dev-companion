---
date: 2026-11-25T09:19:45+00:00
category: idea
status: closed
closed: 2026-11-25
model: claude-opus-5-5
tool: typo3-core-patch-development, typo3_issue_report_guide, typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# core-patch-development tells the agent the user files the issue and never names typo3_issue_repor...

## Observation

Task: fix the review findings of my own change 92384, then create a Forge issue for it. I did not plan to use typo3_issue_report_guide for the issue. My plan was to fill in the forge.typo3.org form myself in the browser panel. The user had to tell me to use the dev-companion. There are four reasons. First, the typo3-core-patch-development skill says: "The user files it, because that takes an account and this server holds none." I read that sentence as "this server has no issue capability". Second, that skill points only to typo3_rule_lookup with documentId core/contribution/reporting-an-issue. It does not name typo3_issue_report_guide anywhere. Third, the tool was deferred in my session. I saw only its name, and "issue_report_guide" reads like a prose guide, not like a drafter that pre-fills the form. Fourth, the base order and typo3_task_guide did not route to it. Only the typo3_forge_lookup description names it, in its last sentences. I had not loaded that tool when I made the plan.

## Query

Task: "fix bitte alle findings, dann lege mir ein issue an" after a review of Gerrit change 92384. Skill text read: typo3-core-patch-development, step 3, last bullet ("The user files it, because that takes an account and this server holds none").

## Suggestion

Name typo3_issue_report_guide in typo3-core-patch-development where the skill says that the user files the issue. Example: "typo3_issue_report_guide drafts the issue, searches for duplicates and returns a pre-filled form link. The user submits it." Also add the tool to the nextTools of typo3_task_guide when the task text says "issue", "Forge" or "report". Consider a tool name that says what it does, for example typo3_issue_draft. The current name sounds like a static guide.
