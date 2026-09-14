---
id: D-EVI-004
title: The environment is made here, and the repository under review is not
date: 2026-08-02
status: open
coveredBy:
  - EnvironmentsTest::everyEnvironmentAScenarioNamesSaysWhereItComesFrom
  - EnvironmentsTest::everyEnvironmentThatIsNotMadeHereSaysWhereItComesFromInstead
  - EnvironmentsTest::everyStepOfTheBuildRunsInTheProject
  - EnvironmentsTest::theBuildRequiresTheExtensionsThisServerAsksFor
  - EnvironmentsTest::theInstallationIsBuiltAtTheCoveredStableVersion
  - EnvironmentsTest::theSetupStepPassesEveryOptionItCannotBeAskedFor
  - EnvironmentsTest::theSiteIsCreatedForTheAddressDdevGivesTheProject
  - EnvironmentsTest::whatIsMadeHereIsNeverCommitted
---

# D-EVI-004 — The environment is made here, and the repository under review is not

**This repository makes the working directory a run needs to reach an
installation. It goes on to declare the third-party checkouts a review is
about.**

The line is what a case needs from the directory. Where that is a property this
repository can state, `bin/cli environment:create` builds it. That is a Composer
installation under DDEV, at the covered stable version, whose console answers.
Where it is a property of somebody else's repository at a real revision, it
stays named in `todo/reference/`.

## Evidence

- All five environments in `scenarios/readme.md` sat on one machine. A forward
  run was therefore reproducible for whoever owned it, and for nobody else.
- No test exercises the console half of this server.
  [`D-DIS-007`](../discovery/dis-007-the-ddev-console-is-named-by-the-mount-not-by-the-variable.md)
  and
  [`R-DIS-018`](../../requirements/discovery/dis-018-a-console-command-never-inherits-the-clients-stdin.md)
  both came from a real run in somebody's project. The second cost two
  `REVIEW-02` attempts 24 minutes apart before anybody could name it.
- A session measured a build rather than estimated it, on 2026-08-02. Six
  commands, all `ddev`, from an empty directory to a frontend that answers 200.
  `site:list`, `language:domain:search`, `debug:backend:modules` and
  `fluid:namespaces` all answered through `Typo3Cli`. It ran in 27 seconds on a
  warm Composer cache and a few minutes on a cold one.
- The base distribution does not require `typo3/cms-lowlevel`, which carries
  `language:domain:search` and `configuration:show`. `scenarios/readme.md`
  defines `E-SITE` by that first command, so a plain base distribution is not
  one.
- `typo3/cms-install` 14.3.5 reads `--server-type`'s default through the same
  fallback as `TYPO3_SETUP_ADMIN_*` reads theirs. With `--no-interaction` and
  the option unset, `SetupCommand::getServerType()` hands its validator `false`
  and the setup dies on the type. Nothing in the option's definition says so.
- `bin/cli checkouts:update` is the precedent the cost stood against: in the
  ignore list, made by one command, re-fetchable at any time. An installation is
  not cheap in the same way. That is why the build runs on demand rather than by
  `todo:claim` the way a checkout link does.

## Decided

- `E-SITE` and `E-NONE` come from here, below `.environments/`, in the ignore
  list the way `.checkouts/` is. One command makes each, and every step of it is
  a `ddev` command printed before it runs.
- The installation is TYPO3's own base distribution at the branch
  `knowledge/versions.json` marks stable. It adds the system extensions this
  server's console path asks for and nothing else. A `composer.json` written
  here would be a second opinion on the shape of a site installation, and the
  one that goes stale.
- `E-EXT` stays declared. What a case needs from an extension repository is its
  real infrastructure at a real revision. Complete in one checkout, incomplete
  in another, a major behind in a third. A scaffold would supply this
  repository's own idea of all three.
- A made `E-SITE` is the environment and never the subject of a recorded forward
  review. Its defects would be the ones this repository wrote, which is
  [`D-EVI-001`](evi-001-forward-evidence-comes-from-a-review.md) from the other
  side. What it is for is the contract cases, and a real installation for the
  half of this server that has never had one.
- No site package scaffold, for the same reason. `scenarios/readme.md` defines
  `E-SITE` without one, and `REVIEW-01` reviews a project **and its site
  package**, so that review keeps its name of a real project.
- The admin password is a constant in the source. The environment exists for
  whoever runs a case in it to log into. It guards a throwaway site on
  `*.ddev.site`, and a generated secret would put the environment back on the
  machine that made it.
- Rejected: clones of the three extension checkouts at pinned revisions, the way
  the core has clones. It would work, and it would make this repository assert a
  revision of somebody else's repository. That includes the `--single-branch`
  constraint the `news` checkout carries, which one `git fetch` by a later
  session would end in silence.
- Rejected: one environment shared across worktrees by symlink, as `.checkouts/`
  is. That one is for reads; this one has a database two sessions would write at
  once.

## Assumed

- That a DDEV project may sit below this checkout. DDEV refuses a project nested
  inside another one, and nothing above `.environments/` is a DDEV project. But
  that is a property of where somebody clones this repository, not of the
  repository.
- That `typo3/cms-base-distribution` keeps up with the covered majors and stays
  the shape a site installation starts in.
- That the machine has a docker daemon. It is what the command refuses on, and a
  CI job without one can run everything else here.

## Wrong if

- A recorded forward run appears whose environment is the made one. The findings
  would then be this repository's own scaffold read back to it, which is the
  failure `D-EVI-001` names.
- The build stops to reproduce what it reproduced here. A step that needs a
  person, a distribution that no longer covers the stable major, a DDEV flag
  that changes meaning. `EnvironmentsTest` holds the commands and not the
  containers, so this surfaces as a build that fails rather than as a test that
  does.
- `E-EXT` turns out to be the environment cases fail in, and the three declared
  checkouts drift apart from what the reference says they play. Then the
  rejected pin is worth its cost after all.

## Since then

The second **Wrong if** arrived by a route it did not anticipate. Nothing about
DDEV had changed, and the build stopped to reproduce because the first run had
happened. A removed worktree leaves the project name registered against an
absent approot. Behind it stays a database volume named after the project, which
a second build met past `--force`. `D-EVI-005` clears both. What that says is
narrower than the **Wrong if** reads. An environment made on demand is not free,
because the part of it that is global to the machine outlives the checkout. One
installation is one per covered version since `D-EVI-006`, and everything else
here stands.
