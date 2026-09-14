---
id: D-SKL-058
title: 'A hint is routed by what the repository is'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::anInstallationIsBuiltInDependencyOrder
---

# D-SKL-058 — A hint is routed by what the repository is

**The boot branch of `typo3-development-installation` routes to
`typo3_hint_lookup` with `id=extension-repository-installation`. That hint's
subject is an extension repository with TYPO3 beneath it and not the act that
put it there.**

The create branch alone names the id. So a session that correctly took the boot
branch reads which question it answers and rules it out by where it stands.

## Evidence

- The feedback, read against the skill as it is now. Step 1 of
  `## Create one where none is declared` still names the id with the description
  the report quotes. It closes on "why the extension directory below the
  document root is empty rather than broken". That is the question the session
  hit, on the branch it was not in.
- The session ran `ls .build/public/typo3conf`, got no such directory, and
  settled it from what `typo3 extension:setup` had printed. The hint's fourth
  statement is the answer. TYPO3 loads the root package from the Composer root
  and publishes its `Resources/Public/` as a symlink up into it. Nothing lands
  in `typo3conf/ext/` in a Composer installation, which is not a broken install.
- **The re-cut fork does not close it.** `D-SKL-056` landed after the feedback
  and put the reported shape, an environment declared and no procedure, into
  both branches. "run what it declares, take every step after that from the
  create branch, and change nothing that is declared". Steps 1 and 2 are what
  the repository already declares. So the sentence that sends the session into
  the create branch is also the one that skips the step with the id.
- **The document the boot branch reads first does not carry it either.**
  `knowledge/documents/project/installation/booting-a-clone.md` is `D-KNW-095`'s
  and is the branch's opening bullet. Its eight steps are the environment, the
  data, the schema, the login and the two requests. `typo3conf`, `.build` and
  the root package appear nowhere in it, and its `hints:` front matter names
  `installation-boot` and `installation-setup`.
- **The corpus answers the symptom and the boot query misses it.** On
  2026-08-18,
  `bin/cli hints:probe "extension repository typo3conf/ext is empty below the document root after composer install, is the layout right"`
  returns `extension-repository-installation` first. That is at
  `appliesTo(22) + text(566)`. Asked in the task's own words, it does not come
  back at all. That query is
  `bin/cli hints:probe "boot the local DDEV development installation of an extension repository from a clean checkout"`.
  What comes back is `extension-repository-layout`, `extbase-domain-mapping`,
  `project-configuration-files`, `extension-boot-files` and `installation-boot`
  do. So the matcher closes it from the symptom and not from the task, and the
  session made neither call.
- `extension-repository-layout`, which that boot query reaches first, already
  points onward to `extension-repository-installation`. It does so for the
  Composer keys and the two that fail quietly. It does not for the layout a
  session looks at a booted repository through.
- **One id rather than a class of them.** The other create-branch ids are about
  the act. `php-versions` is the interpreter a boot finds declared.
  `environment-runtime-readers` and the seed ids are the install and the import,
  which the document covers from the boot side. The shared section on the
  environment's generated settings names `project-configuration-files` again.
  `extension-repository-installation` is the only one whose subject is the
  repository.
- The statements are settled. `D-KNW-053` read all four off a built root package
  and `R-KNW-064` is what keeps them answered, so nothing about TYPO3 is open
  here.
- **Convergence is not what carries this.** `bin/cli feedback:list` reports 29
  open on 2026-08-18, 26 of them from `/home/benji/projects/blog` and one
  debrief, so the evidence is one session series. `D-SKL-056` is the first
  finding against this fork and this is the second.

## Decided

- **Step 2 of the ladder, delivery.** The statement is here, verified, reachable
  and named in the file the session had loaded — in the half its task did not
  pass through. The gap is placement, not a statement and not a route that does
  not exist.
- **Queued rather than closed on the spot.** The change is a published skill's
  contract, which `documentation/records/judging.rst` puts on the todo side of
  that line. `SkillTest` asserts the branch headings the bullet lands under.
- **The condition is what the repository is.** The bullet reads "where the
  repository is an extension with TYPO3 installed beneath it". So it fires on
  the layout the caller looks at rather than on who created it.
- **Rejected: a statement in `booting-a-clone` instead.** That document is the
  order a session brings a project clone up in. Where the extension's own
  manifest is the Composer root is not one of its steps. The session also did
  find the id. It found it and read where it belongs, and only the file that
  told it can say otherwise.
- **Rejected: splitting the hint.** Three of its four statements are about the
  write of the manifest and the fourth is about the read of the result. They are
  one subject. `D-KNW-047` put them here rather than into
  `project-build-and-scripts`, and `D-KNW-053` verified them together. A caller
  fetches a hint whole, so a boot caller reads three statements about a manifest
  it does not write. That is cheaper than a second entry to keep true.
- **Rejected: a line above the fork.** What a skill costs is paid by every
  session that loads it — `D-SKL-052` — and the create branch already carries
  the id. Only the boot branch lacks it.
- **Priority `normal`.** The file is a copy no release of this server corrects,
  and this is the second finding against the same fork. Not `high`, because one
  session series reported it and its task finished.

## Assumed

- That the fourth statement is what the session needed. This entry reads that
  off the hint's text and the report's, not from a run that fetched the hint at
  the moment the `ls` failed.
- That a repository which is itself the extension and declares its own boot is a
  shape rather than one project's peculiarity. One report describes it.
- That a boot caller sent to the whole hint is not slowed by the three create
  statements. Nothing has measured one that reads it.

## Wrong if

- A boot session with the bullet in front of it still probes the layout by hand.
  The id would then have arrived unread, which is wording rather than placement.
  What the branch owes is the statement instead of the route.
- The bullet sends a repository that declares a full procedure and is not an
  extension to the hint anyway. The condition would then catch more than it
  exists for.
- The next report says the three create statements got in the way of the one
  that answered. The split declined here is what comes next.
- `extension-repository-layout` turns out to be what such a session reaches,
  with its closing statement widened to the layout. The route would belong in
  the corpus rather than in the skill.
