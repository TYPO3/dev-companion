---
date: 2026-11-06T06:44:46+00:00
category: bug
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3_task_guide
directory: /home/lina/projects/typo3/tools/dev-companion
---

# the install tool password the setup sets reaches no answer about changing a password

## Observation

A maintainer installed TYPO3 by hand with an agent on 2026-11-06, then asked how to change the administrator password. The answer named the backend and the console command. It did not say that the install tool password holds the same value, that the value is now unsafe, or that a deploy of the project's own files carries it.

The fact is in this corpus. `knowledge/task-intents.json` carries it as the third checklist item of `installation-setup`: the command sets `BE/installToolPassword` in `config/system/settings.php` to the value it was handed, and the two diverge silently the first time somebody changes the backend password.

That item reaches one caller. `typo3_task_guide` delivers it where the task is an installation setup. A question about changing a password is a different task.

The hint that question reaches says nothing about it. `bin/cli hints:probe "how can I change the admin password"` ranks `installation-setup` first. That hint stands in `knowledge/hints/configuration.json`, and it carries neither `installToolPassword` nor the words install tool anywhere in its text. So the statement and the question never meet.

The deploy half is worse than the report assumed, and this session measured it. A fresh TYPO3 14.3.10 from `typo3/cms-base-distribution` ships a `.gitignore` of `/var/*`, `/vendor` and `/public/*`. `git check-ignore config/system/settings.php` reports the file is not ignored. So the install tool password hash is committed with the project and travels to every environment the repository reaches.

What ships is the hash rather than the value, and the checklist's fourth item says neither password is stored reversibly. The exposure is the pair. The agent printed the plaintext into a file or a reply, and the hash of that same plaintext is in the repository. Anybody who holds the printed value reaches the install tool of every deployed environment.

Nothing warns at the moment the value is created. The setup intent states the mechanics of the install tool password beside its other items. It does not say that an automatically generated value has to change before the project is deployed or public.

## Query

A manual installation run with agent support, then the question how to change the administrator password. Checked afterwards with `bin/cli hints:probe "how can I change the admin password"`, `bin/cli hints:probe "install tool password"`, a grep of `knowledge/` for `installToolPassword`, and `git check-ignore config/system/settings.php` in a fresh 14.3.10 project.

## Suggestion

Two repairs, and the first is the one the reporter asks for.

Say it where a password question lands. The `installation-setup` hint answers "how can I change the admin password" and owes one statement: the setup command set the install tool password to the same value, so changing the backend password leaves the install tool on the old one. Name `install:password:set`, and `BE/installToolPassword` in `config/system/settings.php` where the installation has no such command.

Warn where the value is created. The third checklist item states the mechanism and reads as a note. An automatically generated value is a secret the agent chose, printed, and committed as a hash, and it has to change before the project is deployed or public. That sentence belongs beside the item that tells the session to report the password.

Name the deploy, because no statement here does. `config/system/settings.php` is not in the distribution's `.gitignore`, and the install tool password hash is in it. A reader who deploys by pushing the repository ships it.

`feedback/2026-10-29-163000-the-installation-setup-entry-hands-the-session-a.md` reports the account the same command creates, and a reader judges the two together. That report says the entry covers the install tool password. This one says where that coverage does not reach.

A probe of the password question that asserts the answer names the install tool measures the first repair.
