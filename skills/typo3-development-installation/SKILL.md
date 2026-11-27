---
name: typo3-development-installation
description: 'Bring the local development installation of a TYPO3 extension, sitepackage or project package into existence, or boot and repair the one the repository declares: the container, DDEV where it declares one, the unattended install, seeded demo content, and a site that will not come up.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Development Installation

This skill covers the installation a developer works on a package in, from
before it exists until it answers. It boots the installation a repository
declares, or creates one where the repository declares none. It also repairs an
installation that is up, from what the installation answers.

It stops at hosting, deployment, backups and a major upgrade. It also stops at
the installation a test suite boots, which `typo3-extension-testing` covers.

**Each step decides what the next one can be, so keep the order.** A container
declared before the layout gets the wrong document root. An install run before
the container has no database.

Keep this skill as routing and order: it says which lookup to ask, and when. It
holds no layout keys, environment defaults, command options or package names.
Those come from the lookups, which release on their own cycle. A copy here would
go out of date in somebody else's project.

## Steps at a glance

1. Work through the base, and read its answer as the entry condition.
2. Decide which of four cases the repository is in.
3. Boot what the repository declares.
4. Create an installation where the repository declares none.
5. Prove that the site answers, and finish what the install wrote.
6. Diagnose an installation that is already up.

Step 2 sends you to step 3, step 4 or both, and then to step 5. An installation
that is already up goes straight to step 6. When the task needs a test, follow
"When the task turns to a test suite".

## 1. Start with the base

1. Work through [references/base.md](references/base.md) first.

2. Read the answer of `typo3_project_describe` as the entry condition. Two of
   its answers are the task, not a failure:

   - It describes the repository and reports nothing installed in it.
   - It finds no repository to describe at all.

   Neither is the disconnected server the base tells you to stop for. Stop for
   an error, and continue for either of these two. Say which of the three came
   back.

3. Skip the call for the server's scope. The base's `typo3_project_describe`
   discharges `typo3_server_scope`, whatever it answered. It already says
   whether the repository holds an installation, or why it could describe none.

4. Ask the base's lookups that read the installation again once it exists.
   Before that they cannot answer. After it they say whether the work succeeded.

## 2. Decide which case the repository is in

Decide from the boot procedure it declares rather than from the traces an
installation has left in it. A boot procedure is one of these:

- a script its manifest declares;
- a task its environment runs at start;
- a sequence its own instructions write down.

The answer of `typo3_project_describe` lists what the repository declares,
before any of it exists. Then choose the case:

- **It declares a boot procedure.** Boot from it with step 3.

- **It declares nothing.** Create an installation with step 4.

- **One that declares an environment and no procedure is both cases.** Run what
  it declares with step 3. Take every step after that from step 4. Change
  nothing it declares.

- **One that is already up is none of the three.** Create nothing and boot
  nothing. Go to step 6.

## 3. Boot what the repository declares

1. **Read the guide before you run anything.** Call `typo3_rule_lookup` with
   `documentId="project/installation/booting-a-clone"`. It carries the order the
   steps go in, and why you start the environment twice. It says where the data
   comes from when the repository declares no import. It says what shows that
   the boot worked, beyond a command that exited. Read it first, not after a
   step failed.

2. **Read what a clone lacks.** `typo3_hint_lookup` with `id=installation-boot`
   lists what a clone lacks:

   - the schema the database dump must match;
   - the caches inside that dump;
   - a backend user without a password;
   - two reasons a booted clone answers nothing under the host you serve it on.

   The same entry lists the failures of the second start. They differ from those
   of the first start.

3. **Read the layout, where the repository is an extension.** Some extensions
   have TYPO3 installed beneath them. For that layout, `typo3_hint_lookup` with
   `id=extension-repository-installation` lists how Composer loads the root
   package. On a Composer installation, `typo3conf/ext/` below the document root
   is empty. That is correct, not broken.

4. **Read the environment configuration whole.** The describe answer names the
   interpreter and the commands in the manifests. The tasks the environment runs
   at each stage are only in that file, and so are its data sources. A start
   runs them whether or not you read them.

5. **Check the versions that file pins** against the release current on the day.
   Those are the versions of the container, the database and Node. Take the
   release from where its publisher announces it. Report one that is behind as a
   finding that carries the raise. Name the newer version, and do not raise it
   here. A boot is not an upgrade, and what the installed TYPO3 requires often
   speaks against one.

6. **Read the repository's own instructions.** A project with a boot procedure
   usually says which data the installation must hold. The code does not show
   that.

7. **Run the declared steps in the declared order.** Change nothing that already
   works. A boot is not a repair. A rewritten configuration that boots the same
   way is a change nobody asked for.

8. **Where a step fails, report which declared step failed and on what.** Do not
   write a second procedure beside the one the repository has.

## 4. Create an installation where none is declared

First decide the TYPO3 version, where the repository declares none yet. Every
later step installs against it. `typo3_hint_lookup` with `id=new-project-line`
says which version TYPO3 recommends, and what speaks for an older one. It also
says why the installation is a Composer installation. Name the version and the
reason before you install.

Then follow these five steps in this order. Some environments generate settings
into the installation. Where the installation then runs on a database the
environment does not provide, read
[references/generated-settings.md](references/generated-settings.md) before step
3.

1. **Make the package's own manifest the Composer root package.** It installs
   TYPO3 beneath itself, into a directory git ignores. Ask `typo3_hint_lookup`
   with `id=extension-repository-installation` for these:

   - which keys move the installation out of the way;
   - where the console lands under those keys;
   - which layout key Composer accepts and then ignores;
   - which core dependency a root constraint cannot resolve;
   - why `typo3conf/ext/` below the document root is empty, not broken.

   The plugins the install must run are Composer configuration. Composer's own
   documentation states them. Nothing exists yet, so the installation itself
   answers nothing at this step.

2. **Declare the container.** Its project type and its document root follow from
   the layout of step 1. Declare its interpreter here too, because nothing later
   asks again. Ask `typo3_hint_lookup` with `id=php-versions` for the PHP
   versions the target version requires. It also says which version the core
   resolves its dependencies against, and runs its own suites on. Choose from
   that answer, not from what the machine already has.

   Then check two things:

   - **The environment fails its start when a provisioning task fails.** An
     install that failed behind a successful start is the expensive failure of
     this step.

   - **A command that rewrites the environment configuration kept your manual
     settings.** Read the file again after such a command.

3. **Install non-interactively.** The console's setup command takes its answers
   from a fixed set of environment variables. `typo3_hint_lookup` with
   `id=environment-runtime-readers` names them.

   Read the option set off the installed console's own help. That is the binary
   the install runs through. From 14 on, that help reports an option as disabled
   where a package it needs is inactive. The seeding step needs one such option,
   `--distribution`.

   Ask `typo3_documentation_lookup` what an option means in the installed
   version. Check two points in its answer:

   - The value a connection option accepts is not necessarily the value written
     into the settings afterwards.

   - The command refuses a database that already holds tables. So a script that
     runs the install twice needs a guard against what the previous run left.
     Forced settings do not remove a schema.

4. **Seed the content the package's development runs against**, where the task
   needs content. First find out how this package fills a new installation.
   There is more than one way. `typo3_hint_lookup` with
   `id=fresh-instance-seeding` lists the ways a package can declare one. It also
   says what applies when it declares none. Then only the package's own manual
   describes the procedure. `typo3_extension_describe` says where that manual
   is. It also lists the data files, console commands and site sets the package
   ships.

   Where the package ships a data file, ask `typo3_hint_lookup` for these ids:

   - `id=sitepackage-initial-content` says which of the two setup commands
     imports the file, and what makes a package a distribution.
   - `id=initial-content-import-once` says why a changed file is not imported a
     second time.
   - `id=initial-content-references` says what the import remaps, and which
     references it leaves pointing at the wrong record.

   Then check what the import wrote. An installation that answers not-found at
   its root has a site base that is not its own URL. The importer wrote that
   base, not the package. Read the site with `typo3_configuration_lookup`.
   Correct the base in the installation's own site configuration, and read it
   again there.

   A package that renders into a page and defines no page object needs one from
   somewhere else. `typo3_hint_lookup` with
   `id=development-installation-page-object` says where that page object comes
   from. It also says where to keep it, so that no release ships it.

5. **Decide what the install wrote into the repository.** The installation's
   configuration, its writable state and its document root land in the Composer
   root. That is the versioned repository itself. Ask `typo3_hint_lookup` for
   both ids:

   - `id=project-configuration-files` for which of those files the project keeps
     in version control, and which the environment generates.

   - `id=project-build-and-scripts` for the tooling and one-off scripts around
     the site. It says where they belong, how a colleague runs them, and what
     nobody commits.

   The ignore rules follow from both answers. Write them before the first
   commit, not after the first accidental one.

## 5. Prove that the site answers

**The site that answers is the proof.** That means the backend, and the frontend
on the URL the installation names for itself. Do not tear anything down to prove
it, whoever wrote the sequence. An installation somebody asked for and you then
destroyed is a change nobody asked for.

**Look at both sides, because a status code does not show what the site looks
like.** `typo3_rule_lookup` with `documentId="any/testing/browser-check"` says
which installation shows a case, and how a browser reaches it. It also says
where the test harness goes. People skip the backend. A page can answer 200,
render unreadably, and still pass a frontend check. No frontend screenshot shows
what an editor gets: the element wizard, a preview, an icon, a record's badges.

**Where a side shows an error, read what the installation logged.** Do not read
the error page it rendered. `typo3_hint_lookup` with
`id=installation-exception-output` says where TYPO3 writes an uncaught
exception. It names the codes it shows and never writes at all. It says what
decides whether the page shows the message.

The fetch of a rendered error page is the detour this replaces wherever
something threw. It puts the whole page into the context. It still holds nothing
where TYPO3 hid the message. A side that answers wrong without an error logged
nothing. Step 6 covers that case.

**Report the result.** Give the exact commands you ran and what each printed.
Then say what the installation is now:

- the document root;
- the console that reaches it;
- the URL that answered;
- the database it runs on.

`typo3_task_guide` lists what else a finished setup must tell its user,
credentials included. Report what it lists, not a version of your own.

What else the work needs follows from what the install wrote into the
repository. Read that from the ignore rules, not from your memory of the
session. Git may ignore every path it wrote: the document root, the
configuration, the writable state. Then a clone has nothing to run, and a commit
has nothing to describe. Skip both steps below, and the report names the two and
says why.

Where the repository now holds new files, do both:

1. **Run the setup as a colleague's clone would.** Start with no installed
   dependencies, no installation and no container. Let the declared sequence run
   unattended. A step that needed your hand is not part of the setup yet. Then
   run it a second time without a cleanup. Somebody will run a setup twice, so
   it must survive that.

2. **Draft the commit message** with `typo3_commit_message_guide` and
   `workflow="project"`. The manifest, the container declaration and the ignore
   rules are files of that repository, so `project` is the right workflow.

## 6. Diagnose an installation that is already up

Here you build nothing. You read an installation that is up, and what it answers
is the evidence. Use the same two facts that prove a new installation. First
read what the installation answered, then what it logged about it.

1. **Read the log.**

   - **A log with an entry is an uncaught exception.** Read it as step 5 says.

   - **A log that stayed empty is itself the finding.** TYPO3 returns some
     status codes on purpose. Such a response is not a failure, so nothing
     throws and nothing is logged.

   - **Rule out the other cause of an empty log first.** TYPO3 never logs some
     exceptions. `typo3_hint_lookup` with `id=installation-exception-output`
     names them.

2. **Fetch the page.** With an empty log, the rendered page is the only evidence
   the installation holds. Its fetch is right here where it was the detour
   above. The page names the stage the answer came from. That shows whether the
   request matched a site and failed inside it, or matched no site at all.

3. **Where the request reached a site and the page did not come, look at the
   page.** Do not look at the site configuration yet. `typo3_hint_lookup` with
   `id=page-not-found-within-a-site` covers that half:

   - which messages mean the router never resolved the path;
   - which mean it resolved a page and then refused it;
   - how far a hidden or deleted root page reaches down the tree;
   - where that message stops being readable.

   Read it before you touch the site configuration. A page this site holds and
   refuses is not a wrong base.

4. **Where the request reached the wrong site or none, look at the site
   configuration.** Do not look at the code in front of it. Ask
   `typo3_hint_lookup` with one of these five ids:

   - `project-configuration-files` — which file TYPO3 reads, and why the copy a
     package ships is not that file.
   - `installation-boot` — a base whose host differs from the host you serve the
     site under. It matches no site, and the root answers not-found.
   - `site-base-collision` — which of two sites answers where both bases match
     the request.
   - `initial-content-references` — a base an import rewrote to the identifier
     the site landed under.
   - `autogenerated-site-configuration` — a site nobody wrote. The core writes
     one when somebody creates a page at the root. It puts it on a sub-path of
     the URL that request came in on.

   The last two produce the same not-found. Tell them apart by the site
   identifier, not by the symptom.

5. **Correct the configuration where TYPO3 reads it.** To verify, ask the
   installation again rather than read the file again.
   `typo3_configuration_lookup` says which hosts the installation accepts, and
   whether a page shows its error message. TYPO3 merges those values at runtime,
   and the merged value is what answers.

**An installation somebody else built** needs one step first. Find out what it
runs on before you read what it answers. `typo3_hint_lookup` with
`id=installation-boot` lists what such a handover lacks. That is the schema the
dump must match, the caches inside it, and a backend user without a password.

**A single command on a running installation** needs no order. That is a cache
flush, a login for a user nobody has the password for, or a new user. The hint
that a query for it finds gives the command.

**A first start that writes a deprecation log** is a finding about the package's
code, not about the installation. The installation is complete, not broken. Say
that it is up and what it answers. Name the log, and the package whose code
fills it. Change nothing in that package here. Invoke `typo3-extension-health`
with those log lines as its evidence.

## When the task turns to a test suite

**The moment this task grows a test, invoke `typo3-extension-testing`.** Do that
before you edit a test file or build the installation a suite boots. Load the
skill by name and work from it. Hand over the point this skill verified: the
document root, the console, the URL that answered, and the database. Hand over
the defect the diagnosis found, too. `R-SKL-018` says why this is a step and not
a note at the end.

These sentences are about proof, and they turn the task over: "There is no test
for it", "prove it", "add a functional test". That is true whether or not the
installation work is complete. These stay here: "the frontend is still a 404",
"the backend does not come up", "which site did it reach". A read of a log is
not a test suite.

## Where this skill stops

- **The installation a test suite boots** is `typo3-extension-testing`. Its
  purpose differs from this one. This skill produces a site somebody opens in a
  browser and clicks through. That is why the package's own manifest becomes the
  Composer root. A package with TYPO3 in a build directory and no site to visit
  is a test fixture. A repository can have both, and the task decides which one
  it needs first. That choice decides the layout.

- **Tests and static checks** are `typo3-extension-testing`. The boundary holds
  in both directions. On the way out, this skill stops before you edit the files
  that skill covers. On the way in, a suite that needs a served site and has
  none is this workflow first. It runs up to the verified point of step 5, and
  then back.

- **A major upgrade** is a project of its own. `typo3_hint_lookup` with
  `id=installation-upgrade` gives its order.

- **Hosting, deployment and backups** are outside this skill.
