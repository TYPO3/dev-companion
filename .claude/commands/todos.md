---
description: Work the todo queue — one todo here, or several at once in worktrees
argument-hint: [how many sessions at once]
---

Work the queue. `$ARGUMENTS` says how many sessions should work at once; where
it is empty, it is one.

**Run the command before reading anything.** Each of the three below carries its
step out and prints what it did — none of them needs a document read first, and
none of them needs its own source read to find out what it does.

- **One todo, here:** `bin/cli todo:next` prints the one todo that is due,
  whole, with its own `Run:` command already run. Work it as
  [documentation/records/working-a-todo.rst](../../documentation/records/working-a-todo.rst)
  says: read what it serves, settle what the step turns on rather than recalling
  it, `composer ci` before the commit.
- **Several at once:** `bin/cli todo:claim <n>` is the whole setup — the claims
  out of the queue, the commit on `main` that carries them, a worktree and a
  branch apiece with its own `composer install`, and a session started in each
  from `.session-command`. Read its output, not the page behind it.
- **A session that has reported:** `bin/cli todo:home <worktree>` brings that one
  branch home — rebase onto `main`, `composer ci` in the worktree, `--ff-only`
  merge, worktree and branch down, the group listings rewritten, and a claim
  left behind released. One branch at a time, whenever its session ends; nothing
  waits for the others.

Where a command refuses, that refusal is the thing to read and report — it names
what is not what the procedure assumes. The pages behind the commands
([todo/readme.md](../../todo/readme.md),
[working-todos-in-parallel.rst](../../documentation/records/working-todos-in-parallel.rst))
are for that case and for changing the procedure, not for running it.
