---
date: 2026-10-29T16:30:00+00:00
category: bug
status: open
model: claude-opus-5, claude-fable-5-1
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/lina/trials/jambo-1029, /home/lina/trials/jambo-fabian
---

# the installation-setup entry hands the session a weak administrator account

## Observation

`installation-setup` covers the administrator account that `typo3 setup`
creates. The hint stands in `knowledge/hints/configuration.json` and the
checklist in `knowledge/task-intents.json`. Three things in them work against
that account.

**The worked example names the account `admin`.** Hint [14] demonstrates the
DDEV invocation as `ddev exec TYPO3_DB_DRIVER=mysqli
TYPO3_SETUP_ADMIN_USERNAME=admin vendor/bin/typo3 setup --no-interaction`. A
session that reads the hint takes the name from the example. `admin` is the
first name an automated attempt tries.

**The checklist's first item contradicts its second.** Item 1 says to state
the administrator name and the password in the reply, which presumes the
session chose both. Item 2 says to generate the password rather than invent
one, and to keep it out of the shell history, because
`TYPO3_SETUP_ADMIN_PASSWORD` reaches it. Item 1 stands first and reads as the
stronger instruction.

**Item 1 rests on a claim that is false.** It justifies itself: "a password
nobody reports locks everybody out of the installation that was just created".
Console access always remains. `backend:user:create` ships with
`typo3/cms-backend`, takes `--admin`, and prompts for the username and the
password where no option supplies them. So nobody is locked out, and the
argument for printing the password does not hold.

**No statement says the account changes before the site is public.** The entry
covers the password policy, the install tool password, and recovery. It says
nothing about the handover.

The two runs of 2026-10-29 show the result. `claude-opus-5` fetched
`installation-setup` by id, then named the account `admin` and passed an
invented password on the command line of a `ddev exec bash -c` call. There it
reaches the shell history, the container's process list, and the record of the
session. `claude-fable-5-1` did the same without reading the hint. Neither
warned the user.

The `claude-fable-5-1` installation compounds it. That installation has no
`beuser` and no `setup`, so nobody creates a backend user or changes their own
password from the backend. The account stays as the session left it until
somebody opens a shell on the server.

## Query

User: "I'm a PHP developer and I have never used TYPO3. There is a static HTML
design in design/. I want to build this site in TYPO3." Two runs:
`claude-opus-5` in `/home/lina/trials/jambo-1029`, and `claude-fable-5-1` in
`/home/lina/trials/jambo-fabian`. The maintainer read both command lines in
the session records.

`feedback/2026-10-29-105043-a-statement-the-session-read-does-not-reach-the.md`
reports the same two runs, and carries this case as one row of its evidence.

## Suggestion

Write a name the caller chooses into the example. The hint can show
`TYPO3_SETUP_ADMIN_USERNAME="<the name you choose>"` and lose nothing it
demonstrates about how DDEV carries a variable.

Condition item 1 on the unattended case. Where a person is at the keyboard,
the session hands over the command and never learns the value. Item 1 then
holds for a run nobody watches, such as a seeded test instance.

Replace the lock-out justification with what is true. A password nobody
reports costs a reset rather than the installation, and
`backend:user:create --admin` is that reset.

Name the two ways to keep the value out of the session. `ddev ssh` opens a
shell in the web container, where `typo3 setup` and `backend:user:create` both
prompt for what no variable supplies. A value that has to arrive without a
prompt belongs in the container environment rather than on a command line.
`web_environment` in `.ddev/config.local.yaml` is a place the generated
`.ddev/.gitignore` already excludes, and `.ddev/.env.web` is not.

Say that the account name and the password both change before the site is
public. That sentence is the one a newcomer cannot supply for themselves.
