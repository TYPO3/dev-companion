# Package audit report

Write the report in the skeleton below. Keep every heading, in this order, and
write `None.` under a heading that has nothing. Two audits in one skeleton are
two audits a reader can compare. A heading you leave out reads as a section the
audit never reached.

The skeleton is the same in the three review skills. The fields of the head, the
severity headings and one field of a finding are this skill's own.

```markdown
# Audit: <package name>

| Field    | Value                                                       |
|----------|-------------------------------------------------------------|
| Package  | <composer name>, <kind: extension, sitepackage or project>  |
| Commit   | <hash of the checkout you read>                             |
| Range    | <the TYPO3 and PHP versions the manifest declares>          |
| Ran on   | <the installation's TYPO3 version>, or `no installation`    |
| Request  | <the surfaces the request named>, or `all`                  |
| Verdict  | <the highest heading below with a finding>, or `Clean`      |

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
the whole report. The **Surfaces** table is the list step 3 wrote, one row per
entry and in its order.
