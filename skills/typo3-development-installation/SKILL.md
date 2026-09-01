---
name: typo3-development-installation
description: 'Bring the local development installation of a TYPO3 extension, sitepackage or project package into existence, or boot and repair the one the repository declares: the container, DDEV where it declares one, the unattended install, seeded demo content, and a site that will not come up.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Development Installation

Produce an installation the developer of this package can run, in an order where
each step decides what the next one can be. Keep this skill as routing and
workflow; never retain layout keys, environment defaults, command options or
package names — each of those belongs to a tool that releases on its own cycle
and none of them can be re-asked from here.

## Where this starts

Work through [references/base.md](references/base.md) first. One of its answers
is this workflow's entry condition rather than a failure: an installation lookup
that describes the repository and reports nothing installed in it is the task,
and so is one that finds no repository to describe at all. Neither is the
disconnected server the base tells you to stop for. Stop for an error, continue
for either of those, and say which of the three came back.

Then, before anything is created:

- `typo3_server_scope` is discharged by the base's `typo3_project_describe`,
  whatever it answered. What this step asks — whether an installation and a
  console can be reached at all — is what that answer already states: whether
  anything is installed below the repository it describes, and where it could
  describe none, the cause it gives instead. The orientation tool is for a
  caller who does not know whether this server can answer at all, which is not
  this workflow's question.
- The calls in the base that read the installation are asked again once it
  exists. Asked before, they are unanswerable; asked after, they are what says
  the work succeeded.

The repository decides where this task starts, and what decides it is the boot
procedure it declares rather than the traces an installation has left in it: a
script its manifest declares, a task its environment runs at start, a sequence
its own instructions write down. A repository that declares one is booted from
it, and the describe answer carries what it declares before any of it is
installed. One that declares none has an installation created for it. One that
declares an environment and no procedure is both: run what it declares, take
every step after that from the create branch, and change nothing that is
declared. One that is already up is none of the three — nothing is created and
nothing is booted — and it is read from **The installation that already
answers** below.

## Boot what the repository already declares

- The run is a guide and is not reconstructed here: `typo3_rule_lookup` with
  `documentId="project/installation/booting-a-clone"` carries the order the
  steps go in, why the environment is started twice, where the data comes from
  when the repository declares no import, and what says the boot worked rather
  than that a command exited. Read it before the declared steps are run, not
  after one of them has failed.
- `typo3_hint_lookup` with `id=installation-boot` owns what a clone is missing:
  the schema the imported dump owes the code, the caches that arrived inside
  that dump, the backend user whose password did not, and the two things that
  make a booted clone answer nothing under the host it is served on. That entry
  is also where the second boot's failures are, and they are not the first
  boot's.
- Where the repository is an extension with TYPO3 installed beneath it,
  `typo3_hint_lookup` with `id=extension-repository-installation` owns how that
  is laid out. The root package is loaded from the Composer root itself, and the
  `typo3conf/ext/` below the document root is empty on a Composer installation
  rather than broken.
- Read the environment configuration whole before running anything. The scope
  answer names the interpreter and the commands the manifests declare; the
  lifecycle the environment runs by itself — the tasks bound to each stage, the
  configured data sources — lives in that file, and where the answer does not
  carry them, the file is the only place they are readable. Starting the
  environment runs them whether or not they were read.
- The versions that file pins — the container's own, the database, the Node —
  are read against the release current on the day, from where it is published,
  and one behind it is a finding carrying the raise rather than a raise made
  here. Booting is not upgrading, and what the installed TYPO3 requires is what
  speaks against one.
- Read the repository's own instructions beside it. A project that ships a boot
  procedure has usually written down which data it is meant to be filled with,
  and that is not derivable from the code.
- Run the declared steps in the declared order, and change nothing that already
  works. Booting is not repairing, and a rewritten configuration that boots the
  same way is a change nobody asked for.
- Where a step fails, the finding is which declared step failed and on what —
  not a second procedure written beside the one the repository has.

## Create one where none is declared

1. **Make the package's own manifest the Composer root package.** It has to be
   able to install TYPO3 beneath itself, into a directory that is ignored.
   `typo3_hint_lookup` owns what that takes:
   `id=extension-repository-installation` for which keys move the installation
   out of the way and where the console lands under them, which layout key is
   accepted and then reported rather than honoured, which package the core
   brings that a root constraint for it cannot resolve, and why the extension
   directory below the document root is empty rather than broken. The plugins
   the install has to be allowed to run are Composer's own configuration, and
   Composer's documentation is where they are stated. Nothing is installed yet,
   so the installation itself answers nothing at this step.
2. **Declare the container.** Its project type and its document root follow from
   the layout decided above, not the other way round. Its interpreter is
   declared here too, and it is the number nothing later re-asks:
   `typo3_hint_lookup` with `id=php-versions` for what the target version
   requires, what it resolves dependencies against and what the core runs its
   own suites on, and the choice is made against that answer rather than against
   what the machine already has. Two things then have to be verified rather than
   assumed: that the environment fails its start when a provisioning task fails,
   because an install that failed behind a green start is the expensive failure
   of this step; and that a command which rewrites the environment configuration
   has not dropped what was set by hand, which is what reading the file back
   after such a command is for.
3. **Install non-interactively.** The console's setup command answers its own
   questions from a fixed set of environment variables, and `typo3_hint_lookup`
   with `id=environment-runtime-readers` names them. Its option set is read off
   the installed console's own help, which is the binary the install runs
   through. From 14 on that help reports an option as disabled where a package
   it needs is inactive, and the one it reports that way is `--distribution`,
   which step 4 reaches for. Ask `typo3_documentation_lookup` for what an option
   means at the version installed, and check two things in what it answers: the
   value a connection option accepts is not necessarily the value written into
   the settings afterwards, and the command refuses a database that already
   holds tables. The second is what makes an install script re-runnable or not —
   it needs its own guard on what a previous run left behind, and forcing the
   settings does not remove a schema.
4. **Seed the content the package is to be developed against**, where the task
   needs one. By which mechanism this package fills an instance is the first
   question and it has more than one answer: `typo3_hint_lookup` with
   `id=fresh-instance-seeding` for the ways a package declares one, and for what
   holds when it declares none — then the procedure is written down only in the
   package's own manual, and `typo3_extension_describe` reports where that
   manual is beside the data files, the console commands and the site sets it
   ships. Where the mechanism is a shipped data file, `typo3_hint_lookup` owns
   the rest: `id=sitepackage-initial-content` for which of the two setup
   commands imports it and what makes a package count as a distribution,
   `id=initial-content-import-once` for why a changed file does not arrive a
   second time, `id=initial-content-references` for what the import remaps and
   what it leaves pointing at a stranger. What this workflow adds is where to
   look when it lands: a seeded installation answering not-found at the project
   root is a site configuration whose base is not this installation's URL, which
   is the importer's doing and not the package's. Read what actually landed with
   `typo3_configuration_lookup`, correct it in the installation's own site
   configuration, and verify it again there. Content is the second question at
   this step and not the first: a package that renders into a page and defines
   none leaves the installation rendering something else, and
   `typo3_hint_lookup` with `id=development-installation-page-object` owns where
   the page object that replaces it comes from and where it lives so it is not
   released.
5. **Decide what the install wrote into the repository.** The installation's
   configuration, its writable state and its document root land in the Composer
   root, which is the versioned repository itself. `typo3_hint_lookup` owns
   this: `id=project-configuration-files` for which of those files the project
   owns and which the environment generates, `id=project-build-and-scripts` for
   what surrounds the site rather than sitting in it — where the tooling and the
   one-off scripts belong, how a colleague runs any of it, and what is never
   committed. The ignore rules follow from both answers and are written before
   the first commit, not after the first accidental one.

## The environment's settings against the installation's own

Where the local environment generates settings into the installation, the
generated file and the installation's own are one boundary with two owners, and
`typo3_hint_lookup` with `id=project-configuration-files` is what owns it. This
workflow adds the case that breaks it: such a generator knows only the services
it provides itself, so an installation deliberately put on something else — a
database the environment does not run, or none — has the generated file merged
over what the install wrote, and it can no longer connect. Taking the file over
is the documented way out, and it is a step of the install rather than a repair
afterwards. Establish what the merged result actually is with
`typo3_configuration_lookup` rather than from the files, because the merge is
what the installation runs on.

## Prove it, and how far depends on what the run wrote

The site answering is the proof in every case: the backend, and the frontend on
the URL the installation is configured for. Nothing is torn down to establish
that, whoever wrote the sequence — an installation that was asked for and then
destroyed is a change nobody asked for.

**A status code is not what the site looks like, and both sides are looked at.**
`typo3_rule_lookup` with `documentId="any/testing/browser-check"` carries which
installation shows a case, how a browser reaches it, and where the harness goes.
The backend is the half that is skipped: a page that answers 200 and renders
unreadably is what a frontend check passes, and what an editor is handed — the
element wizard, a preview, an icon, a record's own badges — is visible from no
frontend screenshot at all.

Where a side errors, the failure is read from what the installation wrote down
rather than out of the page it rendered. `typo3_hint_lookup` with
`id=installation-exception-output` owns that: where an uncaught exception is
written, which codes are shown and never written at all, and what decides
whether the page carries the message. Fetching the rendered error page is the
detour this replaces wherever something was thrown — it costs the whole document
through the context and still holds nothing where the message was withheld. A
side that answers something other than what it should wrote nothing down at all,
and that is the section below rather than this one.

Report the exact commands run, what each one printed, and what the installation
now is: the document root, the console that reaches it, the URL that answered,
and the database it is on. `typo3_task_guide` carries what a finished setup owes
its user beyond that, credentials included; report what it names rather than a
second version of it.

What is owed past that follows from what the install wrote into the repository,
which is read off the ignore rules rather than off this session's account of
itself. Where every path it wrote is ignored — the document root, the
installation's configuration, its writable state — there is no sequence a clone
would run and nothing for a message to be about, so both steps below are excused
and the report names the two and says why. Where it left files the repository
now carries, both are part of the work:

1. Start from the state a colleague's clone is in — no installed dependencies,
   no installation, no container — and let the declared sequence run unattended.
   Anything that needed a hand is not part of the setup yet. Then start it a
   second time without cleaning up, because a setup that is not idempotent is a
   setup somebody will run twice.
2. Draft the message for what the setup added to the repository with
   `typo3_commit_message_guide` and `workflow="project"`. The manifest, the
   container declaration and the ignore rules are that repository's own files,
   which is the workflow that argument names.

## The installation that already answers

Nothing here is built. An installation that is up is read, and what it answers
is the evidence — which is why a session that arrives with one repairs it from
the same two facts a build is proved by, in this order: what the installation
answered, and what it wrote down about it.

- **A log with an entry is an uncaught exception**, and it is read where **Prove
  it** above says.
- **A log that stayed empty is itself the finding.** A status code TYPO3 returns
  on purpose is a response rather than a failure, so nothing is thrown and
  nothing is written. The rendered page is then the only evidence the
  installation holds, and fetching it is right here where it was the detour
  above: the line it carries names the stage the answer came from, which
  separates a request that matched a site and failed inside it from one that
  matched no site at all.
- An empty log has one other cause, and it is settled before a page is fetched:
  some exceptions are never written down at all, and `typo3_hint_lookup` with
  `id=installation-exception-output` is what names them.

Where the line says a site was reached and the page did not come, the subject is
the page rather than the site configuration. `typo3_hint_lookup` with
`id=page-not-found-within-a-site` owns that half: which of those lines is a path
the router never resolved and which is a page it resolved and then withheld, how
far up a tree a hidden or a deleted root page reaches, and where that line stops
being readable at all. Read it before touching the site configuration, because a
page this site holds and refuses is not a base that is wrong.

Where the answer says the request reached the wrong site or none, the subject is
the installation's own site configuration rather than the code in front of it.
Five lookups own what it can be: `typo3_hint_lookup` with
`id=project-configuration-files` for which file TYPO3 reads and why the copy a
package ships is not that file, `id=installation-boot` for a base whose host is
not the host the site is served under, which matches no site and answers the
root with a not-found, `id=site-base-collision` for which of two sites answers
where both bases fit the request, `id=initial-content-references` for a base an
import rewrote to the identifier it landed under, and
`id=autogenerated-site-configuration` for a site nobody wrote, which the core
writes when a page is created at the root and puts on a sub-path of the URL the
request that created it arrived on. Which of the last two left a site is read
off its identifier rather than off the symptom, because the two produce the same
not-found. Correct it where TYPO3 reads it, and verify by asking the
installation again rather than by rereading the file.
`typo3_configuration_lookup` is what says which hosts are accepted at all and
whether a page discloses its message, because those are merged at runtime and
the merged value is what answers.

Taking over an installation somebody else built is the same reading with one
step in front of it: what it is running on is established before what it answers
means anything. `typo3_hint_lookup` with `id=installation-boot` owns what such a
hand-over is missing — the schema an imported dump owes the code, the caches
that rode in with that dump, and the backend user whose password did not arrive.
The single verbs of a running installation have no order to keep and get none
here: flushing what a change invalidated, getting into a user nobody has the
password for, adding one. Each is one command, and the hint a query for it
reaches is what carries it.

A first boot that writes a deprecation log is a finding about the package's own
code rather than about the installation, and the installation is finished at
that point rather than broken. State that it is up and what it answers, name the
log and the package whose code fills it, change nothing in that package here,
and invoke `typo3-extension-health` with those lines as the evidence it starts
from.

## When the task turns to a suite

**The moment this task grows a test, invoke `typo3-extension-testing` before
editing a test file or building the installation a suite boots.** That is a
step, not a note about ownership: load the skill by name and work from it. What
crosses over is the verified point this workflow reached — the document root,
the console that reaches it, the URL that answered, the database it is on — and
the defect the diagnosis here landed on. It is written as a step because naming
that skill at the end of this file did not fire as one: a session read a 404 out
of the log, fixed the exception behind it, and forty minutes later extended
`Tests/Functional/` without loading the workflow that owns it.

**What fires it is a sentence about proof rather than about the site.** "There
is no test for it", "prove it", "add a functional test" — each turns the task
over, whether or not the installation work here is finished. "The frontend is
still a 404", "the backend does not come up", "which site did the request reach"
are this workflow's own, and reading a log is not a suite.

## Where this stops

This skill owns the installation a package is developed in, from before it
exists until it is answering: the Composer root package that installs TYPO3
beneath it, the container the repository declares, the non-interactive install,
the content it is seeded with, what the install writes into the repository, and
what a running one answers — which site a request reached, what it wrote down,
and the site configuration behind both. It does not own hosting, deployment or
backups. Nor the major upgrade of an installation, which is a project of its own
rather than a verb of the one somebody develops in, and which
`typo3_hint_lookup` with `id=installation-upgrade` carries the order of.

The installation a suite boots is not this one, and the difference is what each
is for rather than how it is laid out. This workflow produces a site somebody
opens in a browser and clicks through, which is why the package's own manifest
becomes the Composer root. A package whose TYPO3 is installed below a build
directory, with the package linked in and no site to visit, is a test fixture
and belongs to `typo3-extension-testing` — a repository can have both, and
asking which one the task needs is the first thing that decides the layout.

Tests and static checks are `typo3-extension-testing`'s, and the boundary runs
in both directions. Going out is **When the task turns to a suite** above, which
states the verified point and stops before editing that owner's files. Coming
in: a suite that needs a served site and has none is this workflow first, up to
that same verified point, and then back.
