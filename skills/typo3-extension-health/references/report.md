# Package audit report

Write the report in the skeleton below. Keep every heading, in this order, and
write `None.` under a heading that has nothing. Two audits in one skeleton are
two audits a reader can compare. A heading you leave out reads as a section the
audit never reached.

Every entry keeps the line form the skeleton gives it. Audits kept every heading
and broke the form below it, so no line compared with the same line of another
audit. The form is five rules:

- A head field holds its value alone. An explanation beside it goes into a
  finding or under **Open**.
- A finding carries every field, in the order the skeleton gives.
- An entry under **Checked and correct**, **Open** and **Dropped** is one line:
  the claim, an em dash and its source. A bold label, a colon or a second
  sentence breaks the line.
- A **Status** cell holds one of its words alone. A surface you read in part
  takes two rows, the part you read and the rest as `unassessed`.
- A **Rule** line ends on the call that returned the rule, as the trace holds
  it: the tool and the id or query it took. A rule the brief carried names
  `typo3_task_guide`, and a rule you read in the code names its `<path>:<line>`.

The answer opens with the fence, and nothing follows **Not reached** inside it.
After the fence stands at most what happens next: the finding numbers in the
order to work them, and the skill that takes each. A summary before the fence or
a recap after it repeats the report in a shape of its own.

The skeleton is the same in the three review skills. The fields of the head, the
severity headings and one field of a finding are this skill's own.

```markdown
# Audit: <package name>

| Field    | Value                                                                 |
|----------|-----------------------------------------------------------------------|
| Package  | <composer name>                                                       |
| Key      | <extension key>, or `none` for a project                              |
| Kind     | `extension`, `sitepackage` or `project`                               |
| Checkout | `standalone`, `inside an installation` or `the installation`          |
| Commit   | <hash of the checkout you read>                                       |
| TYPO3    | <the TYPO3 constraint the manifest declares>                          |
| PHP      | <the PHP constraint the manifest declares>                            |
| Ran on   | <the installation's TYPO3 version>, or `no installation`              |
| Request  | <the surfaces the request named>, or `all`                            |
| Verdict  | <the highest heading below with a finding>, or `Clean`                |

## Critical

### 1. <the finding in one sentence>

- **Where:** `<path>:<line>` or <the runtime registration>
- **Observed:** <the behaviour or the configuration>
- **Rule:** <the rule or documentation it collides with> — <the lookup that answered>
- **Consequence:** <what breaks, for whom, and when somebody would notice>
- **Remediation:** <the scoped change>; check: `<the project check that proves it>`
- **Owner:** <the skill that owns the change, or `unassigned`>
- **Evidence:** <verified: the command, the sink read or the test run> or <reasoned: the reading>

## High

## Medium

## Low

## Recommendation

## Checked and correct

- <surface or claim> — <what you read to establish it>

## Open

- <candidate> — <the reading or the run that would settle it>
- <candidate> — unverified: <the class you did not read>

## Dropped

- <candidate> — <what disproved it, at its path and line>

## Verification

- Ran: `<command>` — <what it printed>
- Not run: <every command typo3_project_describe marks a check that you did not run>

## Surfaces

| Surface   | Status                                                  | Backed by        |
|-----------|---------------------------------------------------------|------------------|
| <surface> | assessed, unassessed, not requested or not applicable   | <what you read>  |

## Not reached

- Not reached: <step> — <what stood in for it>
```

Number the findings across the severity headings, so `3` names one finding in
the whole report. A finding the checklist's finding gate leaves unverified
stands under **Open** and under no severity. Its entry names the class you did
not read. The **Surfaces** table is the list step 3 wrote, one row per entry and
in its order.

A **not requested** row names in **Backed by** what a review of it would read:
the lookups and the files. That is the cost of the rest, and the reader asks for
it off that row.
