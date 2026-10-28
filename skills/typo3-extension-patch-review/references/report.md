# Incoming change review report

Write the report in the skeleton below. Keep every heading, in this order, and
write `None.` under a heading that has nothing. Two reviews in one skeleton are
two reviews a reader can compare. A heading you leave out reads as a section the
review never reached.

Every entry keeps the line form the skeleton gives it. Reviews kept every
heading and broke the form below it, so no line compared with the same line of
another review. The form is six rules:

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
  An id the brief or the skill names without its text is no source yet. Fetch it
  with `typo3_hint_lookup` or `typo3_rule_lookup` and cite that call.
- A `<path>:<line>` comes from a read of that one file. Output that prints
  several files numbers its own lines, and those are no file's lines.

The answer opens with the fence, and nothing follows **Not reached** inside it.
After the fence stands at most what happens next: the finding numbers in the
order to work them, and the skill that takes each. A summary before the fence or
a recap after it repeats the report in a shape of its own.

The skeleton is the same in the three review skills. The fields of the head, the
severity headings and one field of a finding are this skill's own.

```markdown
# Review: <title of the change>

| Field    | Value                                                       |
|----------|-------------------------------------------------------------|
| Change   | <pull request, patch or branch, with its link or ref>       |
| Head     | <hash of the head commit you read>                          |
| Target   | <branch>                                                    |
| Merges   | `yes`, or the conflicting paths                             |
| TYPO3    | <the TYPO3 constraint the manifest declares>                |
| PHP      | <the PHP constraint the manifest declares>                  |
| Ran on   | <the installation's TYPO3 version>, or `no installation`    |
| Pipeline | <what the forge reported, as read>                          |
| Verdict  | <the highest heading below with a finding>, or `Ready`      |

## Blocks the merge

### 1. <the finding in one sentence>

- **Where:** `<path>:<line>`
- **Observed:** <what the change does there>
- **Rule:** <the rule, documentation or reading it collides with> — <the lookup that answered>
- **Consequence:** <what breaks, for whom, and on which declared version>
- **Remediation:** <the smallest change that settles it>
- **Introduced:** <by this change, or pre-existing>
- **Evidence:** <verified: the command, the line or the mechanism traced> or <reasoned: the reading>

## Send it back

## Worth a change

## Recommendation

## Checked and correct

- <surface or claim> — <what you read to establish it>

## Open

- <candidate> — <the reading or the run that would settle it>

## Dropped

- <candidate> — <what disproved it, at its path and line>

## Verification

- Ran: `<command>` — <what it printed>
- Not run: <every check the repository declares that you did not run>

## Surfaces

| Surface                    | Status                                     | Backed by        |
|----------------------------|--------------------------------------------|------------------|
| The change itself          | assessed, unassessed or not applicable     | <what you read>  |
| What it removes or renames |                                            |                  |
| The declared range         |                                            |                  |
| The API it reaches for     |                                            |                  |
| Persisted data and editors |                                            |                  |
| Labels and icons           |                                            |                  |
| Security                   |                                            |                  |
| Coverage                   |                                            |                  |
| The commit message         |                                            |                  |
| Merge state and checks     |                                            |                  |

## Not reached

- Not reached: <step> — <what stood in for it>
```

Number the findings across the severity headings, so `3` names one finding in
the whole report. A finding about a declared version nobody ran says so in its
**Evidence** line, with the reading that would settle it.
