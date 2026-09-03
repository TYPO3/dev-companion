---
date: 2026-09-03T23:55:57+00:00
category: idea
status: open
model: claude-opus-5[1m]
directory: /home/benji/projects/site-tierheim
---

# changeType cleanup returns the repository-audit checklist even for one named file

## Observation

Task: the user asked, in one sentence, to group the fields of one TCA file better. I called typo3_task_guide with changeType="cleanup" and that single file as paths.

The intents came back as "cleanup" (strong) and "tca-field" (strong), and the tca-field items were exactly right - name the table and the type before the field, a column appears in no form until a type's showitem puts it there, settle the column with typo3_schema_lookup, pass the XLF resource to typo3_label_lookup. I followed all of them.

The cleanup half was for a different task. Five of the fifteen checklist items describe an audit of a whole repository: run the audit before writing the list and let it own the findings; show the list whole and let the maintainer cut items before a single file is changed; keep the list in the reply rather than committing it; work an item in the workflow that owns it; re-run the audit on the worked list. For "regroup the fields in this file" that is a procedure with no subject - there is no list to agree, the maintainer already named the one file, and stopping to propose a list would have been the wrong answer to what was asked.

The two cases are easy to tell apart from the call itself: paths named one file and the task text named a concrete edit. A cleanup that means a repository review arrives with no paths, or with a directory, and its task text asks for a review rather than for a change.

The same guide also, correctly, told me to write the commit message with typo3_commit_message_guide, which I did and which worked well - including its warning that my subject line was 64 characters where 52 is preferred, with the summary length spelled out rather than just flagged.

What would help: let the cleanup intent's audit items depend on the shape of the call - withhold them where paths name concrete files and the task text describes an edit, keep them where the call has no paths or asks for a review. Or split the intent into a review variant and an edit variant, so the checklist that arrives is the one for the work in front of the caller. The audit brief is good where it belongs; it just should not arrive attached to a one-file edit.
