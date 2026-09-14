---
id: D-KNW-094
title: 'How a variable reaches a console command is a subject this server owns'
date: 2026-08-18
status: confirmed
coveredBy:
  - HintsTest::theLineThatCarriesAVariableIntoTheContainerIsAnswered
---

# D-KNW-094 — How a variable reaches a console command is a subject this server owns

**The corpus names the variables `typo3 setup` reads and never says how one
reaches the command inside the container. The feedback goes to the queue.**

[`D-KNW-046`](knw-046-the-non-interactive-install-path-is-a-subject-this-server-owns.md)
is the same subject one question shallower: what the command takes and what it
refuses. The session it answered had that answer and read it as correct and
useful. Then it spent two failed round trips on the line that carries those
variables into the web container.

## Evidence

- The miss reproduces on the server as it is now. `bin/cli hints:probe` with the
  feedback's own task returns `environment-variables`, `installation-setup`,
  `environment-runtime-readers`, `project-configuration-files` and
  `installation-boot`. The first three name the `TYPO3_DB_`, `TYPO3_SETUP_` and
  `TYPO3_BE_USER_` families, and none of the five names an invocation. `bash -c`
  and `web_environment` appear nowhere below `knowledge/` or `skills/`.
- The server hands the caller the form the variables do not survive.
  `ProjectDescribe::whereTheyRun()` answers `"ddev exec <command>"` for
  everything that is not a composer script. The `operations` checklist says
  commands typed beside the lifecycle hooks are what boots the installation. A
  session following both types `ddev exec TYPO3_DB_DRIVER=… typo3 setup` and
  gets DDEV's error rather than TYPO3's.
- This repository measured the shell on that transport and keeps it where no
  caller reads it. `Typo3Cli::pastTheShell()` records against DDEV v1.25.1 on
  2026-08-02 that `ddev exec` joins its arguments and hands the line to bash
  inside the container. That is why the code escapes every argument on that
  transport and why `typo3_label_lookup` answered from the package files there
  for weeks. It is a comment in `src/`.
- Two sessions name the absent line and a third pays on the same surface.
  `feedback/2026-08-18-070423` reports three attempts.
  `ddev exec -e VAR=… typo3 setup` failed on an unknown shorthand flag.
  `ddev exec --raw=false "VAR=… typo3 setup"` stat'ed the whole string as one
  binary name. `ddev exec bash -c "VAR=… typo3 setup --no-interaction"`
  succeeded. `feedback/2026-08-18-070538` asks for a boot document and writes
  that exact `bash -c` line into the list of what it would carry.
  `feedback/2026-08-18-080743` reports a `--filter A|B` whose pipe the shell ate
  inside `ddev exec`.
- The two variable families are both written and never contrasted where a caller
  meets them. `knowledge/hints/testing.json` names `typo3DatabaseDriver` and its
  four siblings for a functional run; `environment-runtime-readers` names the
  `TYPO3_DB_` family for the setup command. Nothing states that the first family
  is what a `.ddev/config.yaml` commonly carries under `web_environment`, and
  that it configures nothing for setup.

## Decided

- Step 1a for both halves. The invocation is the one the session paid for. The
  family a `.ddev/config.yaml` carries is what makes the caller believe the
  container is already configured. A caller reads that file and the hint in one
  breath, so they are one entry and one card.
- Queued rather than closed on the spot. What holds about `ddev exec` is outside
  this repository, and this run read this repository and started no container.
- The reading settles a contradiction rather than confirming a suggestion. This
  repository records that `ddev exec` hands its line to bash, and the feedback
  records a string DDEV stat'ed as one binary name. So what `--raw` actually
  switches, and whether an assignment prefix needs `bash -c` at all, is the
  first thing to measure. The feedback's suggested line is not copied into
  `knowledge/` on its author's word.
- The environment made here is where the session measures it, not the project of
  the session that reported. `bin/cli environment:create E-SITE` is that
  environment and `D-EVI-004` is why.
- Where the statement lands is decided with the reading. `installation-setup`
  names the variables and is where a setup question arrives; `installation-boot`
  is where a clone is brought up. The boot document `feedback/2026-08-18-070538`
  asks for may take it instead, and that card has no judgement yet and stays
  where it is.
- `normal` rather than `low`. One session's suggestion is a suggestion. Two name
  this line and a third pays on the surface.
- Not step 5. `doesNotCover` excludes the operation of an installation. This is
  the transport the corpus already commits to in the `operations` checklist, in
  `project-configuration-files` and in what `typo3_project_describe` answers.

## Assumed

- That DDEV is the environment the caller is in. The corpus assumes it
  throughout, and `D-KNW-045` carries the same assumption for the document root.
- That a DDEV statement carries no date, as every DDEV statement in `knowledge/`
  today. No hint names a DDEV version, and a bound by TYPO3 major says nothing
  about a tool with its own release cycle.
- That a `web_environment` with the functional-testing family is a common habit
  rather than one repository's. The feedback saw it in the `t3g/blog`
  repository, and this run read no second `.ddev/config.yaml`.

## Wrong if

- The reading finds that an assignment prefix survives a plain `ddev exec`. Then
  the statement is about the two flags that do not do what their names suggest,
  not about a form the caller lacks.
- A boot session still types `ddev exec VAR=… typo3 setup` after the statement
  lands. Then it was never the hint but the answer `typo3_project_describe`
  gives, and `whereTheyRun()` is where the fix goes.
- No second repository puts the `typo3Database` family into `web_environment`.
  Then that half is one project's habit, and what remains is the invocation.
- DDEV changes what `ddev exec` does with its arguments. An undated statement
  about somebody else's tool goes stale while nothing fails, which is the cost
  of the second assumption above.

## Confirmed on `2026-08-18`

The gap was real and the first **Wrong if** fired, which changed what the
statement says. An assignment prefix survives a plain `ddev exec`, so the caller
never lacked a form. The wrapped form earns its line for one case only, a value
the join would quote.

What the session paid for is the raw flag, and it is stranger than one attempt
could tell. Its presence is what switches, not the value given to it. Its own
help says it defaults to the opposite of what the default does. The reading also
confirms that there is a shell on that transport and every argument is spent
through it. The third **Wrong if** does not hold, since a second project carries
the same variables. So the statement says what those lines mean rather than what
a configuration always holds.

## Since then

The reading measured the forms the feedback named, and a session has since paid
for one it did not: a command written across several lines inside the `bash -c`
string. None of the four **Wrong if** covers that. DDEV did not change and the
statement that landed is still right about what it says. So what is open is a
case rather than a correction. Its judgement is in
[`D-KNW-119`](knw-119-the-corpus-tells-apart-the-failures-one-usage-synopsis-presents-alike.md),
which takes the same subject one form further and carries the card.
