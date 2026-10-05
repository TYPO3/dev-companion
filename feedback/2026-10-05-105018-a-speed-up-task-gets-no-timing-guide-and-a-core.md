---
date: 2026-10-05T10:50:18+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# a speed-up task gets no timing guide, and a core audit gets installation checklist items

## Observation

Task: review core patch 96354, "Speedup e2e install by respecting initCommands in setup command".
I called typo3_task_guide with changeType audit and the five changed paths. The task text said "speed up e2e install".
The answer had "guides": []. The guide core/testing/timing-a-code-path exists. typo3_project_describe lists it. The task guide did not route to it.
I built a timing probe myself. I timed typo3 setup twice per variant: about 20 s before the patch, about 3.4 s after it.
The guide would have told me the method before I started.
The checklist also had items from the installation-setup intent. Examples: "State the admin username and the password in your reply". "Leave --create-site out where a sitepackage ships a root page". "Read skills/typo3-extension-testing/references/playwright.md before writing the first spec".
These items do not apply to a review of a core patch. The intent matched because the paths touch SetupCommand.php. The intent condition says "only if the task is setting an installation up". The checklist still carried the items.

## Query

typo3_task_guide(task="Review patch: speed up e2e install by applying SQLite initCommands in typo3 setup command", changeType="audit", targetVersion="14", paths=["Build/Scripts/setupAcceptanceClassic.sh","Build/Scripts/setupAcceptanceComposer.sh","typo3/sysext/install/Classes/Command/SetupCommand.php","typo3/sysext/install/Classes/Service/SetupDatabaseService.php","typo3/sysext/install/Classes/ServiceProvider.php"])

## Suggestion

Route a task that says "speed up", "faster", "slower", "performance" or "speedup" to core/testing/timing-a-code-path in "guides".
For changeType audit on a core patch, leave out checklist items of the installation-setup intent. Apply its condition to the checklist, not only to the intent label.
