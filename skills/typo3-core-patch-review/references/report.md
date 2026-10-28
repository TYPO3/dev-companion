# Core patch review report

Write the report in the skeleton below. Keep every heading, in this order, and
write `None.` under a heading that has nothing. Two reviews in one skeleton are
two reviews a reader can compare. A heading you leave out reads as a section the
review never reached.

Every entry keeps the line form the skeleton gives it. Reviews kept every
heading and broke the form below it, so no line compared with the same line of
another review. The form is four rules:

- A head field holds its value alone. An explanation beside it goes into a
  finding or under **Open**.
- A finding carries every field, in the order the skeleton gives.
- An entry under **Checked and correct**, **Open** and **Dropped** is one line:
  the claim, an em dash and its source. A bold label, a colon or a second
  sentence breaks the line.
- A **Status** cell holds one of its words alone. A surface you read in part
  takes two rows, the part you read and the rest as `unassessed`.

The answer opens with the fence, and nothing follows **Not reached** inside it.
After the fence stands at most what happens next: the finding numbers in the
order to work them, and the skill that takes each. A summary before the fence or
a recap after it repeats the report in a shape of its own.

The skeleton is the same in the three review skills. The fields of the head, the
severity headings and one field of a finding are this skill's own.

```markdown
# Review: <subject line of the commit under review>

| Field    | Value                                                     |
|----------|-----------------------------------------------------------|
| Change   | <change number>, patch set <n>, or `not pushed`           |
| Commit   | <hash of the patch set>; HEAD <same, or the other hash>   |
| Target   | <branch>; `Releases:` <branches>                          |
| Issue    | #<number> — <title of the issue>                          |
| Fetched  | <ref the checkout skill fetched>, or `not fetched`        |
| Read on  | <date of the last typo3_gerrit_lookup>                    |
| Verdict  | <the highest heading below with a finding>, or `Ready`    |

## Blocks submission

### 1. <the finding in one sentence>

- **Where:** `<path>:<line>`
- **Observed:** <what the patch does there>
- **Rule:** <the rule or behaviour it collides with> — <the lookup that answered>
- **Consequence:** <what breaks, for whom, and when somebody would notice>
- **Remediation:** <the smallest change that settles it>
- **Introduced:** <by this patch, or pre-existing>
- **Evidence:** <verified: the command, the probe or the code read> or <reasoned: the reading>

## Sent back in review

## Worth a change

## Checked and correct

- <surface or claim> — <what you read to establish it>

## Open

- <candidate> — <the reading or the run that would settle it>

## Dropped

- <candidate> — <what disproved it, at its path and line>

## Verification

- Ran: `<command>` — <what it printed>
- Not run: <every suite typo3_test_run_guide returned that nobody started> — <why; where no worktree stood, the question you asked and its answer>

## Surfaces

| Surface                        | Status                                     | Backed by        |
|--------------------------------|--------------------------------------------|------------------|
| Public API                     | assessed, unassessed or not applicable     | <what you read>  |
| Behaviour                      |                                            |                  |
| Compatibility                  |                                            |                  |
| Tests                          |                                            |                  |
| Documentation and changelog    |                                            |                  |
| Commit shape                   |                                            |                  |
| Review readiness               |                                            |                  |
| The review this patch is in    |                                            |                  |
| The chain this change sits in  |                                            |                  |
| Security                       |                                            |                  |
| The working tree               |                                            |                  |

## Not reached

- Not reached: <step> — <what stood in for it>
```

Number the findings across the severity headings, so `3` names one finding in
the whole report. A finding whose evidence is reasoned says so in its
**Evidence** line. A reader weighs a pasted error and a reading differently.
