---
date: 2026-09-01T21:01:10+00:00
category: missing-knowledge
status: closed
closed: 2026-09-01
model: claude-opus-5
tool: typo3-core-patch-review, typo3-core-patch-checkout, typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# Reviewing a Gerrit change must read its chain before calling structure a defect

## Observation

Answered on 2026-09-02, in the two places the session actually stood.

`typo3_gerrit_lookup` says what a chain entry is evidence for now, beside its
paragraph on what a chain is: a namespace holding one class, a class left
non-final or a service with no caller in this patch is what the next change in
the stack uses, and those entries are read before any of it is reported. That is
where this session already was — the chain was in its first answer.

The review checklist carries the step, as a surface of its own and in the line
where a finding is held against the author's case. The maintainer settled the
shape of it on the same day: the patch is rated on its own, and the chain says
whether something in it is preparation rather than excusing it, so what the
follow-up explains is reported as a question. The `Tests` surface says what the
follow-up decides there too.

One part is not answered. Whether a review reports that the change is the bottom
of a chain, because rebasing it obliges everything above, was put to the
maintainer and left unsure, so nothing states it — `D-SKL-090` carries that as
open.
