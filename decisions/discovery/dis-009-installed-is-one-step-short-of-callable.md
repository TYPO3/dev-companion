---
id: D-DIS-009
title: 'Installed is one step short of callable'
date: 2026-08-02
status: confirmed
---

# D-DIS-009 — Installed is one step short of callable

**`install` reports success once it writes `.mcp.json`, which is one step short
of a callable tool, and it says nothing about the step that remains.**

Two sessions in the same project, three days apart, activated a published skill
with a correct entry beside it. Neither found a tool it could call.

## Evidence

- Nothing was absent on this server's side. Re-run on 2026-08-02 over stdio
  against `bin/typo3-dev-companion` from this checkout: `tools/list` returned 24
  tools. Every one of the five the feedback names is among them:
  `typo3_project_describe`, `typo3_extension_describe`, `typo3_task_guide`,
  `typo3_hint_lookup`, `typo3_changelog_lookup`.
- The entry in the reported project is correct and this installer wrote it.
  `/home/benji/projects/site-new/.mcp.json` names `php` with the absolute path
  to the binary above, and `typo3-dev-companion.json` records `claude`,
  `generic` and `opencode` as the clients set up there.
- Reported twice, from that same directory, and only one of the two was ever
  read as a report about this server.
  `feedback/archive/2026-07-29-105130-overall-verdict-from-using-this-server-as-the.md`
  says the tools "were not registered in the Claude Code session despite a valid
  .mcp.json in the working directory, so every call in this evaluation went
  through a hand-written stdio JSON-RPC wrapper". It filed that as a process
  note, went to the archive on the strength of its other half, and nobody judged
  it.
  `feedback/archive/2026-07-31-185900-during-an-audit-of-the-printworks-3d-site.md`
  is the same failure on 2026-07-31. It found the binary the same way, and found
  it only after the audit stood written. The commit that answered it archived
  it.
- What the command says when it finishes. Run into an empty directory on
  2026-08-02 it prints nine lines: one for `.mcp.json`, seven for the published
  skills, one for the `.gitignore` block. Every one of them is a past-tense
  success. Nothing in the output tells a file on disk from a file the client has
  read.
- This repository already knows the failure shape, for one client and one
  artefact. `documentation/usage/installing.rst` carries "VS Code reads the
  skills only once it is told to". `chat.useAgentSkills` is off by default, so
  "nothing reports that six skills are sitting in the repository unread; a
  session there answers from the checkout as if none had been installed"
  (measured on VS Code 1.131.0, 2026-07-31). That is this finding one artefact
  over. There is no equivalent sentence for the `.mcp.json` entry, which every
  client install writes.
- Where it stands written is not where a reader needs it. The VS Code note is in
  a manual the person who runs `install` has no reason to open. The run of
  2026-07-31 is what that costs.

## Decided

- The gap is step 2 of the ladder, delivery. The answer is here and it never
  reached the session. The piece of this server that owns the delivery is the
  install rather than a skill or a hint.
- Queued rather than closed on the spot. The change is a line of installer
  output, which is `src/`, and what remains to do differs per client. A judging
  run has established the gap and not the fix.
  [`R-DIS-023`](../../requirements/discovery/dis-023-an-install-says-what-is-left-before-a-tool-can-be-called.md)
  says what must hold and a todo carries the next step.
- Against a server that reaches further at runtime. Whether a client registers a
  project-scoped server is the client's to decide. The feedback's own
  suggestion, to expose the tools in the agent environment, names something out
  of reach. No process here can do that from inside a session that never started
  it.
- The precondition in `skills/base.md` stands unchanged, and the base does not
  learn the workaround. That is the question
  [`D-SKL-001`](../task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md)
  handed forward, and that entry answers it rather than this one.

## Assumed

- That the two reports have one cause. Both name a valid `.mcp.json` and a
  session with no tool in it, and both are from `site-new` with `claude`
  recorded as an installed client. Neither session could see the client's side,
  so what the client did with the file is an inference from what it did not do.
- That the person who runs `install` is the one who can finish it. The approval
  of a project-scoped server, or the restart of a session that was already open
  when the file arrived, is theirs. Nothing establishes that they read the
  output rather than scroll past nine success lines.
- That a name for the step is worth the line. `D-AUD-003` records a message at
  initialize that failed to reach its reader. This is a different position: a
  terminal, a person, at the moment they asked for something.

## Wrong if

- A third session reports the same thing after the install says what remains.
  Then the sentence is not the lever, and the gap is a command that checks
  rather than tells. It asks the client, or refuses to call the install complete
  until something has connected.
- What remains turns out to be nothing on the clients that matter, and the two
  sessions were a stale process or a file written mid-session. Then this is one
  sentence about a restart, not a per-client property, and `R-DIS-023` is larger
  than the gap.
- The line lands and installs read as failed: somebody sees a step that remains
  and concludes the command did not work. Then it cost more clarity than it
  bought.
- A session reaches a skill with the entry present, no tool callable, and drives
  the binary by hand anyway rather than stops. Then the gap was never in the
  install: the session does not follow the precondition, and that is
  `D-SKL-001`.

## Confirmed on 2026-08-02

Of the four branches the second was settleable without another session and did
not fire. That client documents both mechanisms this entry inferred, so the two
sessions were not a stale process. Nor is it one sentence about a restart.
Eleven clients give three different answers: two gate a workspace server behind
an approval, two need nothing, and five document it nowhere. The record says
unestablished rather than a guess. Per client is what the requirement had to be,
and `R-DIS-023` holds it. The other three branches need a session nobody has
had.
