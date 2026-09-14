---
id: D-EVI-006
title: One installation per covered version, kept and started
date: 2026-08-03
status: open
coveredBy:
  - EnvironmentsTest::aDatabaseNothingIsMadeOnIsRefusedWithTheOnesThereAre
  - EnvironmentsTest::aVersionNoInstallationIsMadeOfSaysWhy
  - EnvironmentsTest::anInstallationOnASecondDatabaseIsItsOwnProject
  - EnvironmentsTest::anInstallationThatIsThereIsStarted
  - EnvironmentsTest::eachCoveredLineIsItsOwnProjectAndItsOwnDirectory
  - EnvironmentsTest::eachDriverPassesTheValuesItsOwnToolsTake
  - EnvironmentsTest::everyCoveredLineIsOneAnInstallationIsMadeOf
  - EnvironmentsTest::theDevelopmentLineIsBuiltFromDevMainOnThePhpItsCoreDeclares
  - EnvironmentsTest::theInstallationIsBuiltAtTheCoveredStableVersion
---

# D-EVI-006 — One installation per covered version, kept and started

**`bin/cli environment:create E-SITE <version>` makes one installation per
covered version, each its own DDEV project. It starts one that is already there
rather than builds it again.**

One installation runs one version, and there was one installation. So every case
that names `E-SITE` ran on the covered stable major. Nothing showed what a
client on another covered line would get.

## Evidence

- Twenty contract cases name `E-SITE`, and one of them names a version.
  `SITE-02` is `E-SITE` on the previous major. No case names 12.4 or the
  development line, so the demand the scenarios carry today is two installations
  rather than four.
- What one costs, measured on 2026-08-03 with the 14.3 and the 13.4 built. 258
  MB of files for the two, and a 133 MB database volume each. The per-project
  DDEV images, `ddev/ddev-webserver:v1.25.1-typo3-mcp-e-site-14-3-built` and its
  dbserver, report `UNIQUE SIZE` 0 B in `docker system df -v`. They are tags on
  layers every DDEV project on the machine already shares. So a covered line is
  about 260 MB, and every released one is about 780 MB.
- Both builds were six `ddev` commands on a warm Composer cache, 36 and 34
  seconds. They ran from an empty directory to `site:list` and
  `language:domain:search` that answer through `ddev exec`. That is the
  measurement `D-EVI-004` made of one installation, taken again per version.
- A start of a stopped one instead was 14 seconds, against the 34 the build
  takes and the database it would meet.
- The recording no longer runs on any of them. `D-DOC-012` moved the second root
  to the installation this repository writes below `.fixtures/`, and rejected a
  third. So the number of installed instances is a question about `scenarios/`
  alone, not about `documentation/server/tools/`.
- `typo3/cms-base-distribution` publishes no release above the newest stable.
  `v14.3.0` is its top tag, and the only thing that tracks the development core
  is its `dev-main`. That core declares PHP `^8.5` where the containers here pin
  8.4. Read on 2026-08-03 from packagist and from
  `.checkouts/main/composer.json`.
- One PHP covers every released covered line. Each branch's own
  `Build/Scripts/runTests.sh` takes 8.4: 12.4 accepts `8.1` to `8.5`, 13.4 and
  14.3 accept `8.2` to `8.5`, read in `.checkouts/` on 2026-08-03.

## Decided

- The version is the second argument of `environment:create`, not a command of
  its own. `create` already takes the environment id, and the build is the same
  six commands with one constraint changed. A second command would carry the
  whole of it a second time.
- With no version named it is the covered stable one, which is what a case that
  says nothing about a version runs on. That keeps
  `bin/cli environment:create E-SITE` at what it meant.
- One DDEV project name and one directory per version, both with the branch in
  them. The name is global to the machine, so one name for all of them is one
  installation for all of them. That is the state this entry is about.
- An installation that is there starts, and never builds again. `ddev start`
  runs where the containers are in any state but `running`, the pause DDEV puts
  an idle project into included. The build is minutes and the containers are
  seconds. An environment is only worth a keep if a second request for it costs
  the seconds.
- `environment:status` is a row per covered version: which exist here, which are
  absent, and which the command does not make at all. "Which of these do I have"
  is the whole of what that command is for.
- ~~The development line is not made here, and says so with the reason. It is a
  different build rather than a version argument: another package constraint,
  another stability, another PHP.~~ Reversed on 2026-08-03, see **Since then**.
  It is all three of those and the command makes it anyway.
- Rejected: one command that makes all of them. Which covered lines a machine
  holds is a property of that machine and of what somebody is about to run.
  `status` already names the absent ones with the command that makes each.

## Assumed

- That two installations are what the scenarios need, and the other released
  lines come on the day a case asks for one. Nothing holds that. It is what the
  case files say today. 260 MB per line is cheap enough that a session can be
  wrong about it for the price of a `create`.
- That the covered stable line stays the version a case with no version runs on.
  `branch()` reads that off `knowledge/versions.json`, so a new stable major
  moves it and the old installation stays behind under its own name.

## Wrong if

- A case runs on the wrong version because the call to `create` named none and
  the caller wanted another. The default is silent, and the directory name is
  the only place the version is visible.
- The installations come once and never start again, so nobody exercises the
  `ddev start` path and people rebuild the environments by hand anyway.
- The disk cost turns out to be the thing that stops somebody from more than
  one. That is a machine where 260 MB per line is not the number. The DDEV
  images stop as shared layers, or the database grows with what a run writes
  into it.
- Nobody refreshes the development line's installation, so what a case run in it
  measures is a `main` from whenever somebody last built it. Nothing here dates
  it and `status` reports it like any other row.
- A case that turns on what the database does runs in an environment where no
  line has one. Every covered line is on sqlite since `c27f8bd`. An installation
  on sqlite answers what a console question asks and says nothing about what
  MariaDB does under the same schema.

## Since then

The development line comes after all, by decision of the person who queued the
card. These environments exist to develop this server, so an installation that
moves daily is a development cost. The build unsets the platform PHP the
distribution pinned against its own requirement, which no file said and only a
build found.

The build found the other thing a read could not. `setup` could not finish
against MySQL or MariaDB on three branches. This entry first wrote that up as a
DBAL bump; it was Forge #110258's introspection change, filed by somebody else
and fixed.

sqlite is the default on every line for reasons of its own, and the database is
the third argument of `environment:create` since.
