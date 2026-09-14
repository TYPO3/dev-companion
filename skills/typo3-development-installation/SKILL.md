---
name: typo3-development-installation
description: 'Bring the local development installation of a TYPO3 extension, sitepackage or project package into existence, or boot and repair the one the repository declares: the container, DDEV where it declares one, the unattended install, seeded demo content, and a site that will not come up.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Development Installation

Produce an installation the developer of this package can run. Each step decides
what the next one can be, so the order matters. Keep this skill as routing and
workflow. Never keep layout keys, environment defaults, command options or
package names. Each of those belongs to a tool that releases on its own cycle.
You cannot ask any of them again from here.

## Where this starts

Work through [references/base.md](references/base.md) first. One of its answers
is this workflow's entry condition rather than a failure. An installation lookup
that describes the repository and reports nothing installed in it is the task.
So is one that finds no repository to describe at all. Neither is the
disconnected server the base tells you to stop for. Stop for an error, continue
for either of those, and say which of the three came back.

Then, before you create anything:

- The base's `typo3_project_describe` discharges `typo3_server_scope`, whatever
  it answered. This step asks whether you can reach an installation and a
  console at all. That answer already states it. It says whether the repository
  it describes holds an installation. Where it could describe none, it gives the
  cause instead.

  The orientation tool is for a caller who does not know whether this server can
  answer at all. That is not this workflow's question.
- Ask the calls in the base that read the installation again once it exists.
  Asked before, they cannot answer. Asked after, they say whether the work
  succeeded.

The repository decides where this task starts. What decides it is the boot
procedure it declares rather than the traces an installation has left in it. A
procedure is a script its manifest declares, or a task its environment runs at
start. Or it is a sequence its own instructions write down. You boot a
repository that declares one from it. The describe answer carries what it
declares before any of it exists.

For one that declares none, you create an installation. One that declares an
environment and no procedure is both. Run what it declares, take every step
after that from the create branch, and change nothing it declares.

One that is already up is none of the three. You create nothing and boot
nothing, and you read it from **The installation that already answers** below.

## Boot what the repository already declares

- The run is a guide, and this page does not reconstruct it. `typo3_rule_lookup`
  with `documentId="project/installation/booting-a-clone"` carries the order the
  steps go in and why you start the environment twice. It says where the data
  comes from when the repository declares no import. It says what says the boot
  worked rather than that a command exited. Read it before you run the declared
  steps, not after one of them failed.
- `typo3_hint_lookup` with `id=installation-boot` owns what a clone lacks. That
  is the schema the dump owes the code, the caches inside it, and the backend
  user without a password. It owns the two things that make a booted clone
  answer nothing under the host you serve it on. That entry also holds the
  second boot's failures, and they are not the first boot's.
- Where the repository is an extension with TYPO3 installed beneath it,
  `typo3_hint_lookup` with `id=extension-repository-installation` owns that
  layout. Composer loads the root package from the Composer root itself. The
  `typo3conf/ext/` below the document root is empty on a Composer installation,
  not broken.
- Read the environment configuration whole before you run anything. The scope
  answer names the interpreter and the commands the manifests declare. The
  lifecycle the environment runs by itself lives in that file. That is the tasks
  bound to each stage, the configured data sources. Where the answer does not
  carry them, the file is the only place you can read them. A start of the
  environment runs them whether or not you read them.
- Read the versions that file pins against the release current on the day, from
  where its publisher announces it. Those are the container's own, the database,
  the Node. One behind it is a finding that carries the raise, not a raise you
  make here. A boot is not an upgrade, and what the installed TYPO3 requires
  speaks against one.
- Read the repository's own instructions beside it. A project that ships a boot
  procedure has usually written down which data it is meant to hold. You cannot
  derive that from the code.
- Run the declared steps in the declared order, and change nothing that already
  works. A boot is not a repair. A rewritten configuration that boots the same
  way is a change nobody asked for.
- Where a step fails, the finding is which declared step failed and on what. It
  is not a second procedure beside the one the repository has.

## Create one where none is declared

1. **Make the package's own manifest the Composer root package.** It has to
   install TYPO3 beneath itself, into a directory git ignores.
   `typo3_hint_lookup` owns what that takes:
   `id=extension-repository-installation` for which keys move the installation
   out of the way. It says where the console lands under them. It says which
   layout key Composer accepts and then reports rather than honours. It says
   which package the core brings that a root constraint for it cannot resolve.

   It says why the extension directory below the document root is empty rather
   than broken.

   The plugins the install has to run are Composer's own configuration, and
   Composer's documentation states them. Nothing exists yet, so the installation
   itself answers nothing at this step.
2. **Declare the container.** Its project type and its document root follow from
   the layout you decided above, not the other way round. You declare its
   interpreter here too, and nothing later asks that number again. Ask
   `typo3_hint_lookup` with `id=php-versions` for what the target version
   requires and what it resolves dependencies against. It says what the core
   runs its own suites on.

   Choose against that answer rather than against what the machine already has.
   Then verify two things rather than assume them. The environment fails its
   start when a provisioning task fails. An install that failed behind a green
   start is the expensive failure of this step. A command that rewrites the
   environment configuration has not dropped what you set by hand. That is why
   you read the file back after such a command.
3. **Install non-interactively.** The console's setup command answers its own
   questions from a fixed set of environment variables. `typo3_hint_lookup` with
   `id=environment-runtime-readers` names them. Read its option set off the
   installed console's own help, which is the binary the install runs through.
   From 14 on that help reports an option as disabled where a package it needs
   is inactive. The one it reports that way is `--distribution`, which step 4
   reaches for.

   Ask `typo3_documentation_lookup` for what an option means at the version
   installed. Check two things in what it answers. The value a connection option
   accepts is not necessarily the value written into the settings afterwards.
   The command refuses a database that already holds tables. The second decides
   whether you can run an install script twice. It needs its own guard on what a
   previous run left behind, and forced settings do not remove a schema.
4. **Seed the content the package's development runs against**, where the task
   needs one. The first question is by which mechanism this package fills an
   instance, and it has more than one answer. `typo3_hint_lookup` with
   `id=fresh-instance-seeding` says the ways a package declares one, and what
   holds when it declares none. Then only the package's own manual writes the
   procedure down. `typo3_extension_describe` reports where that manual is,
   beside the data files, the console commands and the site sets the package
   ships.

   Where the mechanism is a shipped data file, `typo3_hint_lookup` owns the
   rest. `id=sitepackage-initial-content` says which of the two setup commands
   imports it and what makes a package count as a distribution.
   `id=initial-content-import-once` says why a changed file does not arrive a
   second time. `id=initial-content-references` says what the import remaps and
   what it leaves as a pointer to a stranger.

   What this workflow adds is where you look when it lands. A seeded
   installation that answers not-found at the project root has a site base that
   is not this installation's URL. The importer did that, not the package. Read
   what landed with `typo3_configuration_lookup`. Correct it in the
   installation's own site configuration, and verify it again there.

   Content is the second question at this step and not the first. A package that
   renders into a page and defines none leaves the installation with something
   else to render. `typo3_hint_lookup` with
   `id=development-installation-page-object` owns where the page object that
   replaces it comes from. It says where it lives so nobody releases it.
5. **Decide what the install wrote into the repository.** The installation's
   configuration, its writable state and its document root land in the Composer
   root. That is the versioned repository itself. `typo3_hint_lookup` owns this:
   `id=project-configuration-files` for which of those files the project owns
   and which the environment generates.

   `id=project-build-and-scripts` for what surrounds the site rather than sits
   in it. That is where the tooling and the one-off scripts belong, how a
   colleague runs them, and what nobody commits. The ignore rules follow from
   both answers. Write them before the first commit, not after the first
   accidental one.

## The environment's settings against the installation's own

Where the local environment generates settings into the installation, two owners
share one boundary. That is the generated file and the installation's own.
`typo3_hint_lookup` with `id=project-configuration-files` owns it.

This workflow adds the case that breaks it. Such a generator knows only the
services it provides itself. So an installation you put on something else on
purpose gets the generated file merged over what the install wrote. That is a
database the environment does not run, or none. Then it can no longer connect.

To take the file over is the documented way out. It is a step of the install
rather than a repair afterwards. Establish what the merged result is with
`typo3_configuration_lookup` rather than from the files. The merge is what the
installation runs on.

## Prove it, and how far depends on what the run wrote

The site that answers is the proof in every case. That is the backend, and the
frontend on the URL the installation names for itself. Tear nothing down to
establish that, whoever wrote the sequence. An installation somebody asked for
and you then destroyed is a change nobody asked for.

**A status code is not what the site looks like, and you look at both sides.**
`typo3_rule_lookup` with `documentId="any/testing/browser-check"` carries which
installation shows a case, how a browser reaches it, and where the harness goes.
People skip the backend half. A page that answers 200 and renders unreadably
passes a frontend check. No frontend screenshot shows what an editor gets: the
element wizard, a preview, an icon, a record's own badges.

Where a side errors, read the failure from what the installation wrote down, not
from the page it rendered. `typo3_hint_lookup` with
`id=installation-exception-output` owns that. It says where TYPO3 writes an
uncaught exception, and which codes it shows and never writes at all. It says
what decides whether the page carries the message.

A fetch of the rendered error page is the detour this replaces wherever
something threw. It costs the whole document through the context and still holds
nothing where TYPO3 withheld the message. A side that answers something other
than what it should wrote nothing down at all. That is the section below rather
than this one.

Report the exact commands you ran, what each one printed, and what the
installation now is. That is the document root, the console that reaches it, the
URL that answered, and the database it is on. `typo3_task_guide` carries what a
finished setup owes its user beyond that, credentials included. Report what it
names rather than a second version of it.

What you owe past that follows from what the install wrote into the repository.
Read that off the ignore rules rather than off this session's account of itself.
Git may ignore every path it wrote: the document root, the installation's
configuration, its writable state. Then there is no sequence a clone would run
and nothing for a message to be about. Then you skip both steps below, and the
report names the two and says why. Where it left files the repository now
carries, both are part of the work:

1. Start from the state a colleague's clone is in: no installed dependencies, no
   installation, no container. Let the declared sequence run unattended.
   Anything that needed a hand is not part of the setup yet. Then start it a
   second time without a cleanup, because somebody will run a setup that is not
   idempotent twice.
2. Draft the message for what the setup added to the repository with
   `typo3_commit_message_guide` and `workflow="project"`. The manifest, the
   container declaration and the ignore rules are that repository's own files.
   That is the workflow the argument names.

## The installation that already answers

Here you build nothing. You read an installation that is up, and what it answers
is the evidence. That is why a session that arrives with one repairs it from the
same two facts that prove a build. In this order: what the installation
answered, and what it wrote down about it.

- **A log with an entry is an uncaught exception**, and you read it where
  **Prove it** above says.
- **A log that stayed empty is itself the finding.** A status code TYPO3 returns
  on purpose is a response rather than a failure. So nothing throws and nothing
  writes.

  The rendered page is then the only evidence the installation holds. Its fetch
  is right here where it was the detour above. The line it carries names the
  stage the answer came from. That separates a request that matched a site and
  failed inside it from one that matched no site at all.
- An empty log has one other cause, and you settle it before you fetch a page.
  TYPO3 never writes some exceptions down at all, and `typo3_hint_lookup` with
  `id=installation-exception-output` names them.

Where the line says the request reached a site and the page did not come, the
subject is the page. It is not the site configuration. `typo3_hint_lookup` with
`id=page-not-found-within-a-site` owns that half. It says which of those lines
is a path the router never resolved. It says which is a page it resolved and
then withheld.

It says how far up a tree a hidden or a deleted root page reaches. It says where
that line stops being readable at all. Read it before you touch the site
configuration. A page this site holds and refuses is not a base that is wrong.

Where the answer says the request reached the wrong site or none, the subject is
the installation's own site configuration. It is not the code in front of it.
Five lookups own what it can be, each a `typo3_hint_lookup` by id:

- `project-configuration-files` — which file TYPO3 reads, and why the copy a
  package ships is not that file.
- `installation-boot` — a base whose host is not the host you serve the site
  under. It matches no site, and the root answers not-found.
- `site-base-collision` — which of two sites answers where both bases fit the
  request.
- `initial-content-references` — a base an import rewrote to the identifier it
  landed under.
- `autogenerated-site-configuration` — a site nobody wrote. The core writes one
  when somebody creates a page at the root. It writes it on a sub-path of the
  URL the request that created it arrived on. Read off the identifier which of
  the last two left a site, not off the symptom. The two produce the same
  not-found.

  Correct it where TYPO3 reads it. Verify by asking the installation again
  rather than by another read of the file. `typo3_configuration_lookup` says
  which hosts the installation accepts at all and whether a page discloses its
  message. TYPO3 merges those at runtime, and the merged value is what answers.

To take over an installation somebody else built is the same reading with one
step in front of it. Establish what it runs on before what it answers means
anything. `typo3_hint_lookup` with `id=installation-boot` owns what such a
hand-over lacks. That is the schema a dump owes the code, the caches inside it,
and the backend user without a password.

The single verbs of an installation that runs have no order to keep and get none
here. Flush what a change invalidated, get into a user nobody has the password
for, add one. Each is one command, and the hint a query for it reaches carries
it.

A first boot that writes a deprecation log is a finding about the package's own
code, not about the installation. The installation is complete at that point
rather than broken. State that it is up and what it answers. Name the log and
the package whose code fills it. Change nothing in that package here. Invoke
`typo3-extension-health` with those lines as the evidence it starts from.

## When the task turns to a suite

**The moment this task grows a test, invoke `typo3-extension-testing`.** Do that
before you edit a test file or build the installation a suite boots. That is a
step, not a note about ownership. Load the skill by name and work from it. What
crosses over is the verified point this workflow reached, and the defect the
diagnosis here landed on. The point is the document root, the console that
reaches it, the URL that answered, the database it is on.

It stands as a step because the skill's name at the foot of this file did not
fire. A session read a 404 out of the log and fixed the exception behind it.
Forty minutes later it extended `Tests/Functional/` without the workflow that
owns it.

**A sentence about proof rather than about the site fires it.** "There is no
test for it", "prove it", "add a functional test": each turns the task over.
That holds whether or not the installation work here is complete. "The frontend
is still a 404", "the backend does not come up", "which site did it reach" are
this workflow's. A read of a log is not a suite.

## Where this stops

This skill owns the installation a developer works on a package in, from before
it exists until it answers. That is the Composer root package that installs
TYPO3 beneath it, and the container the repository declares. It is the
non-interactive install, and the content that seeds it. It is what the install
writes into the repository, and what a running one answers. That is which site a
request reached, what it wrote down, and the site configuration behind both.

It does not own hosting, deployment or backups. Nor the major upgrade of an
installation. That is a project of its own rather than a verb of the one
somebody develops in. `typo3_hint_lookup` with `id=installation-upgrade` carries
its order.

The installation a suite boots is not this one. The difference is what each is
for rather than how it is laid out. This workflow produces a site somebody opens
in a browser and clicks through. That is why the package's own manifest becomes
the Composer root.

A package with TYPO3 below a build directory, linked in and with no site to
visit, is a test fixture. It belongs to `typo3-extension-testing`. A repository
can have both. Which one the task needs is the first question, and it decides
the layout.

Tests and static checks are `typo3-extension-testing`'s, and the boundary runs
in both directions. On the way out, **When the task turns to a suite** above
states the verified point. It stops before you edit that owner's files. On the
way in, a suite that needs a served site and has none is this workflow first. It
runs up to that same verified point, and then back.
