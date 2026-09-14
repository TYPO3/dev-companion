---
id: D-SCO-003
title: What is core-only is decided per line, by what it names
date: 2026-07-29
status: confirmed
---

# D-SCO-003 — What is core-only is decided per line, by what it names

**Whether a line is core-only is a mechanical check over the rendered text
rather than a flag on each entry.**

`typo3_task_guide` now drops core-only material outside the core. A check on the
text of each entry, not a flag on it, says what counts as core-only. The check
asks whether the line names something that exists in the core repository alone.
Those are `typo3/sysext/`, `Build/Scripts/`, Gerrit, a Change-Id and the core
branch policy.

## Decided

- A mechanical check over the rendered line, in `Scope`, applied to the
  checklist, the checkout discovery steps and the follow-up tools. The
  alternative is a mark on every checklist item, every intent item and every
  scope entry in the knowledge files. That is a flag on a hundred strings, which
  every writer has to set correctly on each new one, and a forgotten flag fails
  silently.

## Assumed

- A line that names a core artefact is, reliably, one nobody can use outside the
  core. The two error directions cost differently. A transferable line the check
  drops because it names a core path as an example is the smaller loss. A
  command that cannot run, handed over as a step, is the larger one.

## Wrong if

- A checklist item has to survive although it names a core path. Advice to read
  the core as a reference rather than change it would be exactly that. It would
  then need the flag after all.

## Confirmed on 2026-08-02

The **Wrong if** has not happened. Of the three corpora the check runs over in
`TaskGuide`, six lines drop. All six instruct the caller to write into the core
or push to it. So the shape that would need a flag is not in the corpus.

## Confirmed on 2026-08-28

The **Assumed** failed in the other direction, which this entry named as the
costlier one. The brief handed a line that is core-only and names no core
artefact to an extension session. `D-SCO-015` has the measurement and the
repair, and the mechanical check keeps its case.
