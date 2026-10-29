---
date: 2026-10-29T10:50:43+00:00
category: tool-gap
status: open
model: claude-opus-5, claude-fable-5-1
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/lina/trials/jambo-1029, /home/lina/trials/jambo-fabian
---

# a statement the session read does not reach the result

## Observation

Two runs of 2026-10-29 built a sitepackage from a static design. Both started
from identical directories: an empty project, the design with a README that
names no TYPO3 concept, and the fifteen skills. Both got one sentence and
nothing else.

> I'm a PHP developer and I have never used TYPO3. There is a static HTML
> design in design/. I want to build this site in TYPO3.

**The delivery worked.** Neither session passed that sentence on. Each wrote
its own task description, and `typo3_task_guide` answered both. Opus reached
`installation-operations` and then `content-element`, `sitepackage` and a weak
`installation-setup`. Fable reached `installation-operations` and `sitepackage`
together, both strong.

Each answer named the hints it withheld, in `omittedHints` and
`omittedHintsOwed`. Both sessions then went back for them, in batches of five
and six ids. So the route this feedback was first written to blame did its
work.

What follows is what the two packages show, read against the hint each session
had in hand.

| Hint | `claude-opus-5` | `claude-fable-5-1` | What the package shows |
| ------------------------ | ----------- | ----------- | ------------------------------------------ |
| `installation-setup`     | read by id  | not read    | an account named `admin`, and an invented password on a command line |
| `translation-domain`     | not read    | read by id  | 29 labels in the long `LLL:EXT:` form, in both packages |
| `content-model-vs-markup`| read by id  | not read    | TCA record types in both, and no choice offered |
| `php-versions`           | not read    | read by id  | DDEV on 8.4, and `config.platform.php` on 8.2.0 |
| `extension-manifest`     | not read    | not read    | Opus ships `ext_emconf.php` on 14.3, Fable ships none |
| `xml-sitemap`            | read by id  | read by id  | the hint carries no doktype, and three container pages are listed |

**Two pairs settle it.** Fable fetched `translation-domain` by id and then
wrote 29 labels in the form that hint replaces. Opus fetched
`installation-setup` by id, whose own example is
`TYPO3_SETUP_ADMIN_USERNAME=admin`, and then named the account `admin` and put
an invented password on a `ddev exec` command line.

A statement nobody read and a statement somebody read produce the same result
here. `extension-manifest` reached neither session, and the two packages
disagree about `ext_emconf.php` anyway. So reading is not what decides the
outcome, and nothing in this server measures the step after it.

One mechanism is worth testing before any other. The hints arrive early and in
batches, and the work runs long: 707 tool calls for Opus and 374 for Fable. A
statement read at call 20 is not in the working set at call 400. The server
delivers once and never again.

`feedback/2026-08-25-105324-task-guide-returned-a-large-brief-that-changed.md`
proposed that mechanism in August: "a brief that returns four such groups
teaches a session to skim the next one." These two runs measure it, and they
raise it. A hint the session chose by id, rather than one a brief pushed at
it, is also contradicted by the artifact.

What the two packages show beside the table:

- **The package set.** `claude-opus-5` installed the 24 packages of
  `typo3/cms-base-distribution`. `claude-fable-5-1` assembled 7 by hand at the
  install, and two more arrived when the maintainer asked for a form and a
  search. The packages absent from the second include `beuser`, `setup`,
  `lowlevel`, `tstemplate`, `recycler`, `scheduler`, `info`, `viewpage`,
  `belog`, `impexp` and `redirects`.
- **The TCA.** The Opus package writes four columns into
  `$GLOBALS['TCA']['tt_content']['columns']` and calls neither
  `addToAllTCAtypes()` nor `addFieldsToPalette()`. The field
  `tx_jambositepackage_background`, which its own comment calls a choice an
  editor makes per element, reaches no form. Neither session read
  `tca-core-palette`.
- **The not-found page.** Neither site configures an error handler. The Opus
  config carries no `errorHandling` key, and the Fable config carries
  `errorHandling: {  }`, which is the shape the install tool writes.
  `site-error-handling` states the whole answer, and no intent routes to it.
  It appeared in no `omittedHints` list either, so neither session was told it
  existed.

**The absent packages move user administration out of the backend.** The Fable
installation has no `beuser` and no `setup`, so nobody creates a backend user
or changes their own password there. Console access answers both:
`backend:user:create` ships with `typo3/cms-backend` and takes `--admin`. So
this is not a lock-out, and it is also the command a session hands over rather
than a password it invented.

The cost is that every account change needs a shell on the server. The editor
of a county council site does not have one, and neither does the person the
site is handed to.

**What the sessions asked.** Opus made one `AskUserQuestion` call carrying two
questions, on where the demo content ships and on the vendor and extension
name. Fable asked nothing. Every other decision in both packages was settled
without the user.

## Query

User: "I'm a PHP developer and I have never used TYPO3. There is a static HTML
design in design/. I want to build this site in TYPO3." Two runs:
`claude-opus-5` in `/home/lina/trials/jambo-1029`, and `claude-fable-5-1` in
`/home/lina/trials/jambo-fabian`, each from a pristine greenfield directory.
The checkout stood at `a884f130` when both started.

Measured from the two transcripts: Opus ran 07:52 to 15:52 for 707 tool calls,
660 of them Bash, 39 to this server, at $13.29 and 86,674 output tokens. Fable
ran 09:49 to 15:50 for 374 tool calls, 172 Bash, 100 Edit, 64 Write, 20 to
this server, at $6.92 and 33,957 output tokens.

## Suggestion

Measure the step after the read before adding a statement to carry it. This
corpus is written as though delivery were the problem, and these two runs say
it is not. A hint read by id and contradicted by the result is the case to
hold a test against.

The cheapest thing to test is repetition. The guide answers once, early, and
the work runs for hundreds of calls. A checklist item that names the hint at
the moment the file is written costs one line; a statement nobody rereads
costs its whole entry. A test of that serves the August report as well, whose
suggestion is to make the brief smaller rather than to repeat it.

Two repairs stand on their own, whatever that test finds. Write a name the
caller chooses into the `installation-setup` example, because
`TYPO3_SETUP_ADMIN_USERNAME=admin` is the name every scanner tries first. Say
that the account name and the password both change before the site is public,
which no statement in that entry says today.

The checklist's first item works against its second. Item 1 says to state the
administrator name and the password in the reply, which presumes the session
chose both. Item 2 says to generate the password and to keep it out of the
shell history. Condition the first on the unattended case: where a person is
at the keyboard, the session hands over the command and never learns the
value.

Name the two ways to keep it out of the session. `ddev ssh` opens a shell in
the web container, where `backend:user:create` prompts for the username and
the password. A value that has to arrive without a prompt belongs in the
container environment rather than on the command line. `web_environment` in
`.ddev/config.local.yaml` is a place the generated `.ddev/.gitignore` already
excludes, and `.ddev/.env.web` is not.

Give `site-error-handling` a route. It reached neither session, and it
appeared in no `omittedHints` list, so neither was told that the statement
exists.

Take the package set as a default rather than a question. The newcomer cannot
weigh a system extension they have never heard of. `new-project-line` already
names `composer create-project typo3/cms-base-distribution`, and the statement
has to name `beuser` and `setup`: without them every account change needs a
shell.
