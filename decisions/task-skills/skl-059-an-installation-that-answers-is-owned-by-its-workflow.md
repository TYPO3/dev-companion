---
id: D-SKL-059
title: 'An installation that answers is owned by its workflow'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::anInstallationIsBuiltInDependencyOrder
---

# D-SKL-059 — An installation that answers is owned by its workflow

**`typo3-development-installation` owns an installation that is up, its run and
the repair of what it answers, rather than a skill of its own.**

The guide names the domain in an intent, routes it to that skill, and the skill
gives half of it away in its closing sentence.

## Evidence

- **The re-run reproduces the substance and not the number.** This entry called
  `TaskGuide::answer()` from this worktree on 2026-08-18 with the feedback's
  query rebuilt. That is
  `task="Boot the local DDEV development installation for the blog extension repository"`,
  `changeType="operations"`, the quoted call minus the ellipsis nobody can
  restore. It answers `installation-operations` strong, `installation-setup`
  weak rather than the second strong match the report quotes, and one skill:
  `typo3-development-installation`.
- **Two intents naming one skill is the construction rather than a miss.**
  `knowledge/task-intents.json` declares
  `"skill": "typo3-development-installation"` on `installation-setup` and on
  `installation-operations`, and `TaskIntents::skills()` answers with the set.
  What the report read as one intent with no skill is both intents that point at
  the same file.
- **The skill claims the domain in its description and disclaims it in its
  closing.** The description offers to "boot and repair the one the repository
  declares … and a site that will not come up". **Where this stops** ends "it
  does not own what runs against the installation once it answers". What runs
  between those two sentences is whether the site answers at all.
- **The second arrival is a different task shape.** `feedback/2026-08-18-074606`
  is a frontend 404 on an installation that existed, booted and served both
  sides. "typo3-extension-conformance audits code,
  typo3-development-installation creates and boots, and the space between them —
  an installation that is up and misconfigured — has no skill." `D-SKL-056` read
  it as "a second task shape out of that directory reaching the same edge from
  the other end" and left its card in place for this question.
- **The answers are here and nothing orders them.** `bin/cli hints:probe` on the
  verbs this feedback lists returns `installation-boot`, `page-cache-flushing`
  and `caching` on 2026-08-18. Those verbs are a cache flush after a code
  change, a reset of the backend password, an import of a colleague's dump.
  `installation-boot` and `installation-exception-output` are the two the skill
  already routes to, both from its create-and-boot half.
- **A fourteenth description has 25 characters to fit into.** The twelve
  published descriptions cost 3575 of the 3600
  `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn` allows, counted from
  `Installer::skills()` on 2026-08-18. The thirteenth,
  `typo3-distribution-content`, is a draft and its publication card sits in
  `todo/waiting/` with the room undecided.
- **Half of what the feedback lists has no order to write down.** Resetting a
  password, adding an editor user and flushing a cache after a code change are
  single commands that `installation-boot` and `page-cache-flushing` already
  carry. What has a work order is the diagnosis and the takeover of one somebody
  else built. The diagnosis is what the installation answers, which site matched
  the request, what it wrote down.

## Decided

- **Step 1b, and taken on.** The answers are available here and nothing says in
  which order to ask for them, which is the half of that rung a workflow fills.
- **A section in the skill that exists rather than a fourteenth skill.** The
  discriminant a second one would need is whether the site answers at all, and
  nobody types that. `074606`'s user reported a served 404 as "das frontend
  zeigt immer noch 404". That reads as a site that will not come up and is a
  site that answers. A skill chosen on that line is chosen wrong at the moment
  it decides anything. The budget above is the second reason and the weaker one.
- **What the section also avoids stands here as a consequence rather than as a
  reason.** A new skill buys a baseline run and a review before publication, and
  an edit stays on the author's word (`D-SKL-035`). So the cheap answer and the
  right one coincide here. That is the coincidence `D-SKL-052`'s fourth **Wrong
  if** watches for, and this entry records it rather than uses it.
- **Where the boundary runs.** Inside: what the installation answers and which
  site matched. Also the log that is empty because nothing threw, and the site
  configuration of an installation that runs. Also inside: the takeover of one
  somebody else built, the dump, the schema behind it, the password nobody has.
  Outside, unchanged: the hosting, the deployment and the backups. Also outside:
  the major upgrade, which `installation-upgrade` already carries a checklist
  for. That is a project undertaking rather than a verb of the installation
  somebody develops in.
- **`074606` folds into this card and its own goes.** It asks whether the domain
  has an owner, which is the question decided above, and two cards would each
  carry half of one gap (`R-FBK-014`). `D-SKL-056` kept it so that a rewrite
  would not hide the question; this card answers it. Its **Prove it** half rides
  along rather than stays behind. An empty log that separates a deliberate
  status code from an uncaught exception is the first step of the diagnosis the
  section owns.
- **The hand-off the feedback asks for mostly stops to be one.** It wants an
  explicit hand-over at the end of `typo3-development-installation` into the
  operational verbs and into `typo3-extension-conformance` for a first-boot
  deprecation log. Once the run half is that skill's own, only the conformance
  crossing is a hand-over. What form a crossing takes is
  `feedback/2026-08-18-074245`'s card.
- **The 1a facts keep their own cards.** This section would route to `074200`
  and `074545`. The first is which of two site bases that collide wins, the
  second the site the core auto-creates for a new root page. Each gets its
  judgement on its own card.
- **Priority `normal`, set by arrival rather than by weight.** Two task shapes
  in one directory reached the domain, which is not `low`. One repository, one
  session series and two finished tasks are not `high`.
- **Both feedback stay open behind the card**, which is what `D-FBK-017` asks of
  a judgement that turns a feedback into work.

## Assumed

- That the two reports are two sessions rather than one that filed twice. Both
  are `/home/benji/projects/blog` half an hour apart, and
  `feedback/2026-08-18-071603` describes the first as a boot session debriefed
  at its end.
- That the discriminant is unusable rather than merely awkward. One report says
  a user called a served 404 a frontend that does not come up.
- That the diagnosis has an order at all. This entry reads it off `074606`'s
  account of its own session and off no run made here.

## Wrong if

- The read finds the run half is the hints the probe already returns plus a
  routing line. Then it belongs on the `installation-operations` intent, and
  this entry grew a workflow for a checklist.
- The section lands and a session whose installation answers still finds nothing
  past "the site answered". Then the domain wanted a file of its own, and a
  budget decided a boundary the work should have decided.
- Sessions stop to read `typo3-development-installation` whole once it carries
  both halves. That is `D-SKL-052`'s first **Wrong if** in the file this entry
  grows.
- The listing turns out not to bind, with a client with more room, or a trim
  that frees a description. The domain then splits cleanly in two. This entry
  would have read a wall as a boundary.

## Since then

The section landed as **The installation that already answers**, and the fork
above it names the shape that enters there. The description was not touched: it
already offered to boot and repair, and the characters the listing has left
would not have paid for more. Two things it does not carry, because nothing here
answers them yet. What a request that matched a site and then answered not-found
means, and what form one crossing takes beyond its name. The second **Wrong if**
is what a session that arrives with an installation that runs will answer, and
nothing has yet.
