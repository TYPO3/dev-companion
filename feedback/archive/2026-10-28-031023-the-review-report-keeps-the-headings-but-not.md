---
date: 2026-10-28T03:10:23+00:00
category: wrong-answer
status: closed
closed: 2026-10-28
model: claude-opus-5-5
tool: typo3-core-patch-review
directory: /home/benji/projects/typo3-cms-mcp
---

# the review report keeps the headings but not the entry forms or a copyable block

## Observation

Task: review Gerrit change 97177 in the typo3-cms core checkout, Claude Code session typo3-cms-f6 on 2026-10-28. The maintainer said the report did not keep the template.

The report kept every heading of references/report.md in its order. The entries below the headings did not keep their form:
- The report stood as rendered markdown in the answer, not in a code block. In the Claude Code terminal a copy gives the rendered text, not the markdown. D-SKL-042 assumes that a client hands over the source.
- Commit field: prose after the hashes ("main, an older state. The patch set commit was read").
- Verdict field: the heading plus a sentence. The template allows the heading or Ready.
- Rule field: finding 1 named the lookup in parentheses, and finding 3 named "project convention" and no lookup. The template is "<rule> — <lookup that answered>".
- Checked and correct: bold labels with whole paragraphs. The template is one line, "<claim> — <what you read>".
- Open: a paragraph instead of "<candidate> — <what would settle it>".
- Dropped: quoted claims, mostly without path and line. The template asks for "what disproved it, at its path and line".
- A "Fazit" paragraph followed Not reached. The template ends with Not reached.

The installed copy of the skill is identical to skills/typo3-core-patch-review. SKILL.md says "every heading in its order". It does not say that each entry keeps its line form, and it does not name a code block.

## Query

"bitte review mir https://review.typo3.org/c/Packages/TYPO3.CMS/+/97177" in /home/benji/projects/typo3-cms, skill typo3-core-patch-review

## Suggestion

State in the Report section of the three review skills:
- Put the whole report in one fenced markdown block, so a copy from a rendering client keeps the source.
- Every entry keeps the line form of the skeleton. A head field holds the value and nothing else.
- Nothing follows Not reached.

Re-read D-SKL-042: its first Assumed does not hold for the Claude Code terminal.
