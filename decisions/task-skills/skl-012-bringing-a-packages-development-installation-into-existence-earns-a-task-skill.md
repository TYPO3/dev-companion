---
id: D-SKL-012
title: Bringing a package's development installation into existence earns a task skill
date: 2026-08-03
status: open
coveredBy:
  - ScopeTest::theDeclaredInterpreterIsNotDeclined
---

# D-SKL-012 — Bringing a package's development installation into existence earns a task skill

**Bringing a TYPO3 package's development installation into existence earns a
task skill of its own.** It owns the Composer root package, the container, the
non-interactive install and the content that seeds it. Its boundary runs at the
installation a repository declares, and stops at the server that serves it.

Seven sessions in two projects reached the same order on one day, and the guide
answers the task with a core patch checklist.

## Evidence

- **Seven open feedback, two directories, one day.** `bin/cli feedback:list` on
  2026-08-03 reports 29 open across four directories. Five are this domain in
  `/home/benji/projects/ext-guidedtour`. `162745` is the whole task and asks for
  the skill, `162759` the Composer root package, `162826` the `typo3 setup`
  semantics. `162836` the site base an imported distribution lands on, `162858`
  DDEV's settings management against SQLite. Two are in
  `/home/benji/projects/site-demo-typo3-org`, `154501` and `154508`. All seven
  are `claude-opus-5[1m]`, and this entry read all seven whole.
- **The two directories are opposite task shapes.** `ext-guidedtour` had no
  installation and had to produce one, from a repository with `Classes/`,
  `Configuration/`, `Resources/` and a `composer.json`. `site-demo-typo3-org`
  had a Composer project that declares its own DDEV hooks and a pull provider,
  and had to boot it. Neither found an order, and both ended at the same two
  findings: nothing owns the task, and the server answers it as patch work.
- **The re-run reproduces it.** This run called `typo3_task_guide` through
  `bin/typo3-dev-companion` from this worktree on 2026-08-03, with `162745`'s
  own query in English and the repository described, at `changeType: unknown`.
  The answer opens `Recognized as: Test coverage`. `installation-setup` appears
  only under `Possibly also`, at weak confidence. The hints are
  `How a Distributed Extension Repository Is Laid Out` and
  `Models, Repositories and the Table Behind Them`, and the checks are
  `Build/Scripts/runTests.sh` suites. The checklist asks to confirm the target
  core branch and issue context, keep the patch focused, and write the commit
  message. That is `154508`'s diagnosis reproduced from the other project:
  `unknown` falls back to the patch shape.
- **The intent that does match holds none of this.** The five
  `installation-setup` items in `knowledge/task-intents.json` are the admin
  password, the install tool password it becomes, and how to reset each. And to
  leave `--create-site` out where a sitepackage seeds content. Every one of them
  is what to report once a setup has run. None is how the installation the setup
  runs against comes to exist.
- **The corpus is not reached by the query that produced the feedback.**
  `bin/cli hints:probe` on `162745`'s German query returns nothing, with 78
  hints as candidates. That is `R-AUD-006` at work rather than a miss, so the
  English call above is what carries the evidence.
- **No skill owns it.** This entry read the nine descriptions under `skills/` on
  2026-08-03. `typo3-extension-testing` is the nearest and names the harness —
  "when a project has no working test harness yet" — which is what runs against
  an installation. The other eight are patch work, patch review, conformance,
  documentation, release, upgrade, backend modules and content elements.
- **All three signals of an absent skill are present.** A session invented the
  order itself: `162745` numbers five steps in the sequence their dependencies
  force. A session went in an order that cost it the task: four debug cycles,
  one feedback each. And the domain was reached independently from a second
  project, which is the signal
  [judging.md](../../documentation/records/judging.rst) calls the strongest and
  which `D-SKL-005` was decided on.
- **An earlier entry read the boundary once already.** `doesNotCover` excludes
  "Running an installation: server and container setup, deployment, backups, the
  editorial use of the backend".
  [`D-KNW-027`](../knowledge/knw-027-which-caches-a-change-invalidates-is-a-subject-this-server-owns.md)
  read that line as a limit "at the container and the webserver", and placed the
  console commands of `installation-upgrade` inside it. The DDEV file this
  cluster turns on is one this server already reads. `D-DIS-007` names the
  console by its mount, `R-DIS-015` holds the DDEV client entry, and
  `typo3_project_describe` reports DDEV as the declared environment. That is
  what `154501` asks to widen rather than introduce.

## Decided

- **Step 1b, and taken on.** The answers are available here and nothing says in
  which order to ask for them, which is the half of that rung a skill fills.
- **A skill rather than the `installation-setup` intent.** Both feedback ends
  ask for a hand-over: `162745` wants `typo3-extension-testing` to point here
  when no installation exists yet, and to take over once one does.
  `src/Tool/TaskGuide.php` carries no skill name at all, which `D-SKL-001` and
  `D-SKL-006` both record. So an intent cannot state that hand-over today and a
  skill can. The intent keeps its five items, which are about a setup that has
  already run.
- **Where the boundary runs.** Inside: the repository's own `composer.json` read
  as a root package, the console sequence that installs and seeds, what the
  import does to a site's base. What belongs in `.gitignore` because the install
  writes it into the Composer root, and the environment file the repository
  declares. Outside, as before: a server in operation, deployment, backups, and
  DDEV as a product with a life of its own.
- **No card folds in and none goes.** The four obstacle cards are 1a facts, each
  judged on its own card and each in hand on another branch. This card carries
  the order, which is a different rung. `154508`'s change-type gap is a tool's
  schema and stays on its own card.
- **Priority `normal`, and what sets it is order rather than weight.** Seven
  feedback from two projects is not `low`. It is not `high` either: a skill
  routing to lookups that answer nothing ships a promise into somebody else's
  project, which is the `%private` case recorded in `D-SKL-005`. The four
  knowledge cards land first.
- **`feedback/2026-08-03-162745` stays open** behind the card, which is what
  [`D-FBK-017`](../feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
  asks of a judgement that turns a feedback into work.

## Assumed

- **That the domain clears the bar without a recorded run.**
  [writing-a-skill.md](../../documentation/contributing/writing-a-skill.rst)
  settles a domain with a scenario case or a run in `scenarios/runs/`, and this
  cluster has seven accounts plus the one re-run made here. `D-SKL-005` had
  `REVIEW-03` and this has nothing of that kind, so the accounts do work a
  transcript would otherwise do.
- **That the `site-demo-typo3-org` pair is this domain rather than one beside
  it.** The boot of a declared environment and the creation of one share the
  install sequence and the DDEV file, and differ in the first step. Read as two
  domains, the independent-arrival signal falls away and five feedback out of
  one project is what remains.
- **That the four sibling judgements land the facts this skill would route to.**
  All four are in hand elsewhere and none of them has landed yet.

## Wrong if

- The reading finds the order is five checklist items rather than a session's
  working order. Then it belongs on the `installation-setup` intent, and this
  entry built a skill for a checklist.
- `154508`'s change-type value lands, `typo3_task_guide` is re-run with it, and
  the answer already orders this task. Then the route was the obstacle and
  `D-SKL-006`'s answer was the right one here too.
- The draft turns out to be mostly DDEV and Composer rather than TYPO3. Then the
  boundary above sits in the wrong place, and `doesNotCover` was right as it
  stood.
- The skill is out and the next session in a repository with no installation
  still derives the Composer root layout by hand. Then the four facts were the
  gap and the order was never absent.

## Since then

The boundary reached `doesNotCover` on 2026-08-18. The interpreter a covered
line requires landed in a hint inside the environment file this entry placed on
the covered side. The excluded topic still sent that question to the TYPO3
documentation. The topic stays as it was otherwise, and what the entry names now
is the one part with an answer here.

The third **Assumed** took its first counter-report the same day. The boot and
the creation are one domain, which is what it concluded, but they do not differ
in the first step. A repository that declares an environment and no procedure
had the session on both branches at once. `D-SKL-056` re-cuts the discriminant.
