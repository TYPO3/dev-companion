---
id: D-DIS-034
title: A project without skills is sent to the skill resources
date: 2026-10-27
status: open
coveredBy:
  - ScopeTest::theInstructionsFitWhatAClientKeeps
  - StdioServerTest::aProjectWithoutSkillsIsToldHowToGetThem
---

# D-DIS-034 — A project without skills is sent to the skill resources

**Where `install` never recorded a client or a skill in the project, the
instructions open with `Installer::ABSENT`. That sentence names the
`typo3://skill/<name>/SKILL.md` resource for the skill `typo3_task_guide` names,
rather than `typo3-dev-companion install`.**

The server already serves every published skill at that URI, `D-ANS-163`. A
session that reads it has the workflow without a copy in the project. So a
server that one user-scope entry starts in every project serves the skills there
as well.

## Evidence

Headless runs on 2026-10-27, Claude Code 2.1.311 and Codex 0.164.0. The project
was a TYPO3 extension with a `composer.json` and nothing else. There were two
tasks, a new content element and a unit test setup, each planned and not
written.

| Client      | Setup                                      | Skill read | Server called |
| ----------- | ------------------------------------------ | ---------- | ------------- |
| Claude Code | `install --agent=claude`                   | 2 of 2     | yes           |
| Claude Code | no skill files, the old `ABSENT`           | 0 of 2     | yes           |
| Claude Code | no skill files, a longer draft of `ABSENT` | 4 of 4     | yes           |
| Claude Code | no skill files, `ABSENT` as committed      | 4 of 4     | yes           |
| Codex       | `install --agent=codex`                    | 2 of 2     | yes           |
| Codex       | no skill files, either sentence            | 0 of 4     | no            |

- Without files, every Claude Code session called `typo3_task_guide`. It names
  the skill by name in `skills`, and no session read the resource on that alone.
- With the URI in the instructions, each Claude Code session read the `SKILL.md`
  after `typo3_task_guide`, and seven of the eight also read its references. The
  skill with files was the first call instead.
- No Codex session without skill files called a tool of this server, with either
  sentence. That is the log. Asked without a tool call, a Codex model also said
  it had no instructions from this server. That rests on what the model said,
  and `D-DIS-033` records a round that followed them. So why Codex stays away is
  open, and the outcome is not.

## Decided

- `ABSENT` names the resource and stands where the stale notice stands. The
  budget `R-ANS-013` measures now holds every assembly with `ABSENT` in it,
  rather than the length of `ABSENT` against `NOTICE`.
- The routing note says "activate the typo3-* skill that covers the task"
  without "in your listing". That holds for both ways a skill arrives, and the
  sixteen characters it frees pay for the URI.
- `install` stays. Codex in the runs above reached this server only through the
  skill files.
- Nothing is written into the project at the start. The server serves the skill,
  and the project stays as the caller left it.

## Assumed

- That a client which shows instructions and reads resources acts like Claude
  Code here. Only Claude Code was measured.

## Wrong if

- A Claude Code session in a project without skill files reports that it had no
  workflow, or reads no `typo3://skill` resource for a task a skill covers.
- A client lists the skills through the Skills extension and also gets this
  sentence. Then the sentence is redundant there, and the check has to read the
  client's capabilities.
- A Codex session without skill files calls this server's tools. Then the 0 of 4
  above is out of date and has to be measured again.

## Since then

On 2026-10-27 the skill copies in thirteen projects went away to test this route
alone. A Claude Code session in the core checkout then got neither a skill nor
the URI, for two reasons. Claude Code still listed this server as connecting at
the first prompt, which `claude -p` above never shows. And `Installer::absent()`
reads the project record, which named Antigravity only.
`feedback/2026-10-28-031023-a-session-that-starts-before-the-server.md` holds
both. On 2026-10-28 the maintainer kept the skill files and `install` as they
were. The copies went back, and this sentence stays as the fallback.
