---
id: D-SKL-056
title: 'The installation workflow branches on the declared procedure'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::anInstallationIsBuiltInDependencyOrder
---

# D-SKL-056 — The installation workflow branches on the declared procedure

**The installation skill branches on whether the repository declares a boot
procedure, and its closing obligations follow what the run wrote into the
repository.**

Both discriminants name something else today: the traces an installation leaves
in a repository, and who authored the sequence. A repository that declares an
environment and nothing else falls on the wrong side of each.

## Evidence

- The feedback, read whole. `t3g/blog` declares `.ddev/config.yaml` and a
  document root. Its hooks are an empty array, its manifest has no install
  script, and its lock file and `config/` are in the ignore rules. The session
  took the boot branch and ran out of declared steps after the environment
  start. It composed everything from the install onward out of the create
  branch.
- The fork as it stands is a disjunction of traces: "an environment
  configuration, a document root, a site configuration, a lock file". Every
  bullet under it presupposes a sequence — "Run the declared steps in the
  declared order", "the finding is which declared step failed". One trace is
  enough to enter a branch that then has nothing to run.
- The closing section splits on authorship and asks a session that wrote the
  sequence for three more things: a re-run from a clone with "no installed
  dependencies, no installation, no container", a second start, and a commit
  message drafted with `typo3_commit_message_guide`. The reporting session wrote
  the sequence and committed nothing — `git status --short` was empty, because
  `.build/`, `config/` and `var/` are ignored — so the message had no subject
  and the re-run would have destroyed the installation that had just been asked
  for. The boot branch states that case two paragraphs above: "an installation
  that was asked for and then destroyed is a change nobody asked for".
- The rung is not the corpus. `bin/cli hints:probe` on 2026-08-18, asked in the
  reported repository's own terms, returns `installation-setup`,
  `project-build-and-scripts`, `project-configuration-files`,
  `installation-boot` and `environment-variables`. The session reached the skill
  on its description alone, and every hint id it names inline was fetchable and
  correct, which the feedback states first. Delivery and routing worked; the
  wording is what is left.
- `bin/cli feedback:list` on 2026-08-18 reports 35 open in two directories, 32
  of them in `/home/benji/projects/blog`. `074606` is a second task shape out of
  that directory that reaches the same edge from the other end. That is an
  installation that is up and answers wrong, whose nearest section is this same
  closing one.
- `D-SKL-012`'s third **Assumed** reads a boot and a create as two that share
  the install sequence and differ "in the first step". This is the first report
  of one repository in both.
- `SkillTest::anInstallationIsBuiltInDependencyOrder` asserts both branch
  headings and `## Prove it, and how far depends on who wrote the sequence`
  verbatim, so the wording and its guard move together.

## Decided

- **Step 4 of the ladder, and queued rather than closed on the spot.** The
  change is the structure of a published skill and the assertions that hold it,
  which gets a review rather than an improvisation.
- **The fork asks which procedure the repository declares.** A session boots a
  repository that declares one from it. One that declares an environment and no
  procedure runs what it declares and takes the rest from the create branch. It
  changes nothing the repository declares.
- **The closing obligations follow what the run wrote into the repository.**
  Where every path the install wrote is in the ignore rules, the unattended
  re-run and the commit message have no subject. The site that answers is the
  whole proof, and the report says why the run owed the other two nothing.
- **No third section.** A third branch restates the create branch's steps, and
  what a skill costs is paid by every session that loads it — `D-SKL-052`.
- **Priority `normal`, set by two task shapes in one directory that reach the
  same fork.** The file is also a copy no release of this server corrects. Not
  `high`: one session series, one repository, and the task finished.
- **`074606` keeps its own card.** It asks whether an installation that is up
  and answers wrong has an owner at all, which is rung 1b. A fold into this card
  would hide that question behind a rewrite.

## Assumed

- That the repository is as reported. Nothing here reads that checkout, so its
  shape rests on the account and only the skill's own wording was read.
- That a declared environment and no procedure is a shape rather than one
  repository's peculiarity. One report says so.

## Wrong if

- A session in a repository that declares only an environment reads the re-cut
  fork and still reconciles two branches. The pair would be wrong rather than
  the discriminant, and the third shape is a branch of its own after all.
- A session that committed its setup skips the second start because this run
  wrote nothing. The session would then read the discriminant as what happened
  today rather than as what the repository now carries.
- The fork sends a repository with a full boot procedure through the create
  branch's steps. The condition would then catch more than it exists for.

## Since then

The third shape carries a second cost. What sends a session into the create
branch is also what skips its first two steps. "change nothing that is declared"
passes over the steps the repository already declares. Step 1 is where the
layout hint stands, which the session probed by hand. `D-SKL-058` routes the
boot branch to that hint, which leaves the fork as this entry cut it. The last
**Decided** turned the same day. The card folds into the one that answers its
question rather than into a rewrite, which is the reason it stayed.
