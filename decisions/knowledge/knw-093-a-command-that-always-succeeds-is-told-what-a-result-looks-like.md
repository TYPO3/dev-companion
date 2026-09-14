---
id: D-KNW-093
title: 'A command that always succeeds is told what a result looks like'
date: 2026-08-18
status: confirmed
coveredBy:
  - HintsTest::aCommandThatAlwaysSucceedsCarriesItsDiscriminator
---

# D-KNW-093 — A command that always succeeds is told what a result looks like

**Where a hint prescribes a command whose success message is unconditional, the
hint says what a correct result looks like. A sweep holds the corpus to that
rule.**

Three of the four sightings the feedback reports have their correction, one hint
at a time, and each correction deferred the rule to this card. What no entry
says is the property they share. In this domain a success message is not
evidence, and the hint that prescribes the command is where the discriminator
belongs.

## Evidence

- Re-run on 2026-08-18 through `bin/typo3-dev-companion` over JSON-RPC.
  `typo3_hint_lookup` with `id=impexp-artifact` and `targetVersion=14` closes on
  "impexp:export answers [OK] whatever it left out, so the artifact is what says
  whether it worked". `id=extension-schema-sql` closes on "its success is not
  evidence that anything was created". `id=datahandler-relations` states that
  the parent's column holds the number of children and that the run finishes
  clean with the counter at 0. The three sightings the feedback names by id have
  the answer it asked for.
- Each got its answer from its own feedback and card, after this one arrived.
  `D-KNW-080` for both impexp sentences, `D-KNW-081` for the `NEW` placeholder,
  `D-KNW-089` for the warm TCA cache.
- Both of those entries deferred the general rule here by name. "the
  discriminator is left to the todo to word", "this is that proposal's worked
  case, and judging it stays its own card". So two entries passed the rule over
  by design and it is what remains of the feedback.
- Nothing below `requirements/` states it. `R-KNW-005` says a mechanism that
  fails in silence names its failure, and `R-KNW-018` says where a caller can
  verify an artifact. `R-KNW-049` holds one check that passes after it read
  nothing, and `R-KNW-073` holds one step that reads from a cache. Those are
  four rules for four mechanisms, none of them about what a caller may conclude
  from a success message.
- The shape arrives from outside this session and outside TYPO3's console.
  `knowledge/task-intents.json` carries "a post-start hook that installs the
  instance can fail while ddev start reports success" from a feedback of
  2026-08-03. `R-KNW-049` carries `cglGit` with SUCCESS after it inspected
  nothing, from one of 2026-08-02. Both stand where a session found them.
- The corpus carries two discriminator sentences today, both from 2026-08-18. It
  carries 24 statements that name a `typo3 <subject>:<verb>` command over 11
  distinct commands, counted over `knowledge/hints/` on 2026-08-18. That is the
  whole of what a sweep reads.
- The session that reported paid between two and twelve round trips per
  sighting, four times. It says the four are most of its nine debug cycles.

## Decided

- Queued rather than closed on the spot. Whether a given command answers success
  unconditionally comes off that command's class in `.checkouts/`.
  [`judging.rst`](../../documentation/records/judging.rst) puts anything that
  needs a lookup about TYPO3 on the todo's side of the line.
- `normal` rather than the `low` the card arrived at. One session reported it,
  but the same failure now has a correction in four subsystems one at a time. A
  rule is what stops the fifth from the same route.
- Not `high`. The two expensive sightings have their correction, the rest is
  prevention, and nothing waits on it.
- The rule holds for a command whose success message is unconditional rather
  than for every procedural hint as the feedback asks. A discriminator on a
  command that reports its own failure warns about a trap the reader does not
  walk into. That is what `D-KNW-087` says a pointer may not do. Every sentence
  costs in the answer that carries the hint.
- What the discriminator names sits outside the command's own output: the
  artifact, the database, the parent's column. Where a lookup on this server can
  perform the check the statement names it, with what it does not settle.
  `extension-schema-sql` names `typo3_schema_lookup` and says it answers what
  TYPO3 would create, which is the worked example of both halves.
- The requirement is about the command a hint prescribes, whoever ships it. The
  sweep starts at the console commands because that is where the evidence is.
  `ddev` and `runTests.sh` produced two of the sightings on record, and a rule
  that excluded them would follow the corpus rather than the failure.
- Trimmed rather than archived. The three sightings have their answer and the
  rule does not, so the feedback keeps the half nothing here has taken on.
- The card goes and one card titled after the work takes it over, with this
  feedback in its `**Serves:**` line, `R-FBK-014`.
- `datahandler-seeding` takes nothing here, though the feedback names it. The
  check it would carry is the parent's counter, which is
  `datahandler-relations`' subject since `D-KNW-030` and stands there.
  `installation-boot` takes nothing either, which `D-KNW-089` settled on its own
  evidence.

## Assumed

- That the two corrected sentences are right about TYPO3. `D-KNW-080` confirmed
  its pair against seven runs of the command on a 14.3.6 installation. The
  schema one comes off a read of `SetupExtensionsCommand` on four checkouts and
  not a run.
- That the sweep's reading is cheap. A command's success path is one class, and
  the 11 commands the corpus prescribes are the whole list. But a command whose
  success depends on what a service returned is a longer read than one whose
  `$io->success()` is unconditional.
- That one session wrote this feedback and the sixteen beside it. They share a
  directory, a model, a subject and three quarters of an hour, and nothing in a
  feedback records a session.

## Wrong if

- The sweep finds no command besides the two already corrected whose success is
  unconditional. The rule would be two instances rather than a property of the
  domain, and the requirement would hold nothing that is not already held.
- A session reports the same trap for a command the sweep passed over as one
  that reports its failures. Then "unconditional success" is the wrong test, and
  what decides is a command that acts on stale input. That is what `R-KNW-073`
  covers for one case and would then be the general rule instead.
- A session follows a discriminator and it is wrong. A check read off the corpus
  rather than off a run is the guess `judging.rst` warns about. The sweep would
  have produced sentences with a verified entry's authority.
- The discriminators land and the answers grow enough that a caller stops
  reading them. The cost is in payload, which `bin/cli hints:coverage` reports
  as body lengths. The rule would be right about the sentence and wrong about
  where it goes.

## Confirmed on 2026-08-18

The sweep ran and the first **Wrong if** did not hold. Three more commands
answer success after they did nothing. So the rule is a property of the domain
rather than two instances, and `R-KNW-074` states it. Of eleven prescribed
commands read on every covered checkout, five report their own failures. One
needs no sentence because its hint already ends on the rendered page, and five
are unconditional.

The three the sweep found are each one class deep. A language update that moves
its status only under a flag, so a download the server answered with nothing
hides behind the progress bar. A backend user creation that reads neither the
error log nor the result of the write. And an upgrade run that marks a wizard
done where its own check returned false. The `ddev`, `composer` and
`runTests.sh` commands are outside what the checkouts settle.
