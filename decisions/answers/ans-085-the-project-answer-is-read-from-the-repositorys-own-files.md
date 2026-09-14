---
id: D-ANS-085
title: The project answer is read from the repository's own files
date: 2026-08-18
status: open
coveredBy:
  - InstanceTest::aProjectRootIsRecognisedByWhatItsOwnManifestDeclares
  - ProjectTest::theRepositoryIsDescribedBeforeAnythingIsInstalledInIt
---

# D-ANS-085 — The project answer is read from the repository's own files

**`typo3_project_describe` answers what the repository's own files declare
wherever it finds a project root. It withholds only the three fields that come
out of the installed tree.**

Its description promises an answer on a fresh clone. On a clone with no vendor
directory it returns `unsupported` instead, because a discovery that looks for
installed packages gates the whole answer.

## Evidence

- `feedback/2026-08-18-070333` reports it from a fresh clone of
  `github.com/TYPO3GmbH/blog`. One call, `cause: no-installation`, and four Bash
  calls over `.ddev/config.yaml`, `composer.json`, `package.json` and the rest
  to read by hand what the answer would have carried. The identical call after
  the install returned all of it, the environment block included. It came out of
  a file that had not changed between the two calls.
- Re-run on 2026-08-18 against this branch. `bin/typo3-dev-companion` over stdio
  from a directory with that repository's `composer.json` and
  `.ddev/config.yaml` and nothing else answers the `unsupported` object. It
  answers seven searched directories, and no other key.
- The gate is one call. `Project::describe()` returns null where
  `Instance::describe()` does, and `Instance::locate()` recognises a Composer
  project by `vendor/composer/installed.json` — the file a clone does not have.
  The `type` in its own `composer.json` identifies a core checkout, so the
  promise holds there and nowhere else.
- Three fields read the installed tree and the rest read the root.
  `typo3Version` and `corePhpConstraint` come out of the core package,
  `extensions` out of `Instance::packages()`. `phpConstraint`, `coreConstraint`,
  `environment`, `sites`, `commands`, `patches` and `guides` come from
  `composer.json`, `package.json`, `.ddev/` and `config/sites/` below the root.
- The empty state is the one the workflow starts in.
  `skills/typo3-development-installation/SKILL.md` calls an answer that reports
  no installation "the task, not the disconnected server". It decides between a
  boot and a creation on what the repository declares: an environment
  configuration, a document root, a site configuration, a lock file. Those are
  what the answer withholds exactly there.
- That skill already sends the caller to the file by hand. It says "where the
  answer does not carry them, the file is the only place they are readable",
  about the DDEV lifecycle. The four Bash calls are that sentence in action.
- Two places prescribe the call. `skills/base.md` opens its order with it, and
  so do the `instructions` returned at initialize.
- `R-PRJ-001` states the same promise, "No console, no database, so it answers
  on a fresh clone", and carries the mark `held`. Both tests it names stand on
  an installed tree. `theProjectIsDescribedFromItsFilesAlone` builds one through
  `composerProject('vendor', '13.4.33')`, and
  `withoutAnInstallationThereIsNoProjectToDescribe` hands in no start directory
  at all. Nothing guards the sentence for a clone that was never installed.
- The corpus: 32 open feedback out of `/home/benji/projects/blog`, all recorded
  on 2026-08-18. `feedback/2026-08-18-074305` is the same call reported as a
  strength from the other side, "worth more than any other single answer". That
  was in a project where the installation existed. One boundary reported from
  both sides out of one build, which is what `D-FBK-018` reads.

## Decided

- Step 1b of the ladder, the shape. The answer is available here and no call
  returns it. Every field named above comes from files this server already
  opens, and the session read them out of the checkout instead. Not step 2,
  since no other tool carries them either.
- Queued rather than closed on the spot. It changes `Project::describe()`, the
  discovery that finds a root, and the tool's declared `outputSchema`.
  `documentation/records/judging.rst` puts a schema beyond a run that has read
  only this repository.
- **That** the file-only half answers wherever the tool finds a project root,
  and the boundary is the three fields above. What comes out of the installed
  tree stays withheld; the answer owes what comes from the root whether or not
  the tree holds anything.
- Not the feedback's own shape. It asks for the packages half beside an
  `unsupported` block that covers the keys derived from the installation.
  `D-ANS-005` decided that the unsupported answer replaces the result rather
  than labels it. `Schema::installationAnswer()` declares the two as `oneOf`, so
  an answer with both is invalid against the schema this server publishes.
- The answer says the installation is absent rather than implies it.
  `extensions: []` is what a project with no extensions answers and
  `typo3Version: null` is what a root with no readable core answers. So neither
  tells a caller that the tree holds nothing yet. Which field says it is the
  todo's; that one exists is not.
- What identifies a project root without an installed tree is the todo's first
  step, and it is where this can go wrong. `Instance::locate()` walks up twelve
  directories, so a rule that admits any `composer.json` reports a TYPO3 project
  for every PHP repository above the caller.
- This reaches `typo3_project_describe` and no other tool. `typo3_icon_lookup`,
  `typo3_label_lookup`, `typo3_schema_lookup` and the rest answer for the
  installation they name, and `cause: no-installation` stays their whole answer.
- `R-PRJ-001` keeps its `held` status. What it names exists and has its guard
  for the installed case, and the state vocabulary has no word for a sentence
  half kept. The commit that does the work widens the guard.

## Assumed

- That `D-ANS-083` survives it. That entry discharges the skill's
  `typo3_server_scope` step with any describe answer, because `cause` carries
  the distinction between nothing found and nothing that runs. So whatever
  states the absent installation in the new shape has to carry it as well. The
  todo reads that entry rather than the work after it.
- That a clone nobody has installed is an ordinary state rather than an exotic
  one. One session reports it, and the workflow that declares it as its entry
  condition is what makes it ordinary.
- That the session read the description sentence as a promise. The feedback says
  so of itself, because it is what made the session call the tool first and
  trust it. Nothing else records a read of that sentence.

## Wrong if

- A session on a fresh clone reads the same files by hand after the answer
  carries them. Then the fields arrived and were not taken, which is step 4 and
  a rewrite rather than a seam.
- The answer starts to report a project in a repository that has nothing to do
  with TYPO3. Then the root rule admits too much, and the gate did that work as
  well as the one it took the blame for.
- A caller reads the installation-less answer as an installed one — recommends a
  version, or reports the extension list as empty. Then the state is not said
  loudly enough, and the `unsupported` shape was what carried it.
- The three withheld fields turn out to be what the workflow needed, and a
  session on a fresh clone stops on the absent `typo3Version` anyway. Then the
  seam is in the wrong place and the answer owed there is a different one.

## Since then

Built on 2026-08-18 at the seam this entry drew. What identifies a root without
an installed tree is another entry's. A field of its own says the state rather
than the kind. So a caller can still tell a site project from an extension
repository during its install. The skill states both entry conditions now.

No session on a real clone has read any of this back, which is what the four
**Wrong if** wait for.
