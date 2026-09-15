# SKILL-14 — Ship the site's content with the package

**Environment:** `E-SITE`, with the site package in the repository beside it and
a second installation of the same version that has never had the package ·
**Contract:** `held` — `D-SKL-050`, `D-SKL-035`, `D-SKL-064`
**Held by:** `SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`
and `SkillTest::everySkillStatesWhatItOwns`, which read back the order the
workflow asks its three lookups in and that it says where it stops, and
`SkillTest::theCommitStepIsNamedWhereASkillsWorkflowEndsInAChange`, because the
artifact and the site configuration are that repository's own files. That the
task reaches the workflow at all is **not guarded** and cannot be. No assertion
can hold that a request in somebody's own words reaches a workflow, and only a
run measures it. The skill went out on 2026-08-19. The draft declaration is
gone, `distribution-content` in `knowledge/task-intents.json` routes to it, and
`SkillTest::everyPublishedSkillIsNamedByAnIntent` holds that route.

> The pages and the content on them are built in my development installation,
> and the site package sits in the repository beside it. I want that content to
> ship with the package, so a colleague installing it on an empty TYPO3 gets the
> same pages, the same content on them and the same images, and the site comes
> up rendering.

**What has to come out of it**

- A script writes content that exists nowhere yet rather than somebody clicks it
  together. Where what landed is wrong the session corrects the script rather
  than the records. The script is what the next version of the artifact comes
  out of.
- The session reads what landed back out of the installation before it exports
  anything. A record takes the default its configuration gives it and not the
  one its column carries. A page that arrives hidden reaches nobody while every
  command involved reports success.
- The session judges the export on the artifact and not on the command's
  message. Every table the tree holds is part of it, and the files it references
  are in it as files rather than as rows.
- The artifact and its files directory go into the package under the names the
  import looks for. Neither arrives there on request.
- The whole directory the site keeps its configuration in travels with the
  package. Only the root page changes, to the record the import wrote.
- The proof runs on an installation that has never had this package, with three
  checks there. The records arrived, and the site answers on that installation's
  address and renders. Every page the artifact carries answers rather than only
  the one at the root.
- Where the second installation cannot be had, the answer says the proof was not
  run.

**How it fails**

- Somebody builds the content by hand in the backend, so the artifact has no
  source to regenerate from. The next export loses the correction of a wrong
  record.
- An export command that reports success passes as an export that worked, with
  an artifact that shipped no images or left a table out.
- The session verifies the artifact with a read of it, or with a second import
  on the installation it came from. That installation remembers the import and
  does nothing.
- The site configuration ships inside the export, or as the one file in its
  directory that is obviously configuration. The installation that receives it
  then resolves the site, finds every page, and renders nothing.
- A relation lands that points at nothing and nothing logs it, so the package
  ships content whose images attach to no record.
- The session reports the proof as done after it opened the root page, or after
  a console check that a page tree exists.
