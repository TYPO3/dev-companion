---
id: D-AUD-004
title: 'Every client is offered every tool'
date: 2026-08-02
status: open
---

# D-AUD-004 — Every client is offered every tool

**Every client gets every tool. The answer says what it is worth outside the
core, and only the caller shortens the tool list.**

Tools named in `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS` shorten the list, and nothing
else does.

[`D-AUD-002`](aud-002-a-client-is-offered-the-all-or-the-project-profile.md)
withheld three tools from a Composer project. The ground was that a repository
with no `Build/Scripts/`, no Gerrit remote and no Forge issue cannot follow the
core's contribution process. Its own **Corrected on** line of 2026-08-02 showed
what that cost. This entry is what came of the question whether the mechanism
was the right one at all.

## Evidence

- The tool list a client pays for here is 92,189 bytes across 23 tools. The
  three the `project` profile removed — `typo3_rule_lookup`,
  `typo3_script_lookup`, `typo3_test_run_guide` — are 2,223 of them, 2.4%. The
  profile passed as a cost decision and was not one.
- The knowledge was never withheld. `D-AUD-002` decided the resources are not
  filtered, so every core document stayed readable as a `typo3://core` resource
  under the `project` profile. What the profile removed was the doorway, and
  Scope::offered() then edited the map so nothing mentioned the doorway. That
  left a client with knowledge it could no longer find.
- Ten recorded sessions in `E-SITE` called `typo3_task_guide` eight times and
  not one was core-shaped. So the derivation was right every time it ran and
  gained nothing on any of them. The one shape it got wrong is the one that
  broke. A core patch got a core answer, routed to `typo3_test_run_guide` twice,
  and the client could not call it.

## Decided

- `Server\Profile` goes rather than shrinks to an explicit preset.
  `TYPO3_DEV_COMPANION_PROFILE=project` was a name for one exclude list, and its
  survival would have kept the concept that made the collision possible.
- `typo3_server_scope` cannot be excluded. It tells a client why the list is
  shorter than the documentation says. A client without it cannot tell a
  configured server from a broken one.
- The `provenance` of a topic no longer decides whether the client gets the
  topic. It says what an answer is worth outside the core, which is R-SCO-006.
  That is a statement about the answer rather than about the list.
- `excludedTools` replaces the `profile` object in the `typo3_server_scope`
  output schema, and the two do not stand side by side. A field whose concept is
  gone has no honest value, and this change breaks that schema.

## Assumed

- An agent offered a tool it cannot usefully follow does better than one offered
  nothing. `typo3_test_run_guide` in a project answers with `runTests.sh`, which
  exists in the core repository alone. The bet is that a correction beats a gap,
  and that R-SCO-001's per-path audience keeps the correction attached.
- Nobody relied on the shorter list to keep an agent away from core-only advice.
  Nothing in `feedback/` or `scenarios/runs/` shows an agent that reached for
  one of the three tools where it did not belong. But nothing shows the profile
  stop one either; nobody ever saw the profile at work.

## Wrong if

- A session in a site installation calls `typo3_test_run_guide` or
  `typo3_rule_lookup` for project work. It presents `runTests.sh` or the Gerrit
  review rules as if the caller could follow them. That is the failure the
  profile aimed at. What the answer says now has to catch it rather than the
  absence of the tool.
- The 2.4% turns out to measure the wrong thing. That is a client that pays per
  tool rather than per byte, or one whose limit is a count. Then the argument
  that the profile bought nothing does not hold.

## Since then

Both **Wrong if** have an instance and neither says the offer should shrink. The
first fired once. An extension repository got `runTests.sh` suites because every
path came back `uncertain`. So what failed is the input rather than the wording,
which `D-SCO-012` places from the root manifest. The second has its client: one
that lists tools by name and defers their schemas, where a caller pays a name
rather than a description. It does not bring the profile back. The cost is per
tool at a size the exclusion list cannot change, and what those sessions asked
for is `D-AUD-011`.
