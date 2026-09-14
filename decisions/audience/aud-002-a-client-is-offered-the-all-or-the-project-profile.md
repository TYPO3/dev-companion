---
id: D-AUD-002
title: A client is offered the `all` or the `project` profile
date: 2026-07-29
status: revoked
revokedBy: D-AUD-004
---

# D-AUD-002 — A client is offered the `all` or the `project` profile

**A client gets one of two profiles, `all` and `project`. The list names the
tools left out per tool rather than derives them from the `provenance` of what
they answer.**

The feedback that asked for this described one package: the installation-backed
tools and the transferable domains, without the core contribution surface. The
item written from it named three profiles — `core`, `project`, `all`.

## Decided

- Two, `all` and `project`. A `core` profile would have had to leave the
  installation-backed tools out to differ from `all`, and it must not. A core
  checkout is an installation, and a lookup of an icon identifier or a label in
  it is exactly what a patch needs. Everything else made `core` and `all` the
  same set under two names.
- The list names the tools left out per tool rather than derives them from the
  `provenance` of the topics they answer. `typo3_rule_lookup` answers two
  core-only topics and two transferable ones, so no formula over the field gets
  it right. The field is the input to the decision, not the decision.
- The resources are not filtered, only the tools and the maps that route to
  them. A session reads a document when it asks for it by name; every session
  gets a tool whether it fits or not.

## Assumed

- What the three omitted tools also carried and what does transfer is reachable
  without them. The commit conventions come through `typo3_commit_message_guide`
  with `workflow="project"`, the backend CSS and subsystem conventions through
  `typo3_hint_lookup`. Every prose document comes verbatim through its
  `typo3://core` resource.
- The profile derived from the kind of installation is right more often than it
  is wrong, and a wrong one costs one environment variable. It does sit against
  R-AUD-002, where the audience is a property of the task, not of the directory.
  The tool list cannot vary per task, so this is the one place the directory
  decides.

## Wrong if

- Someone contributes to the core from a session started in a site installation
  and then has to set `TYPO3_DEV_COMPANION_PROFILE=all` to get the rules back.
  Or a deployment has no installation to read at all, which is where a profile
  that leaves out the installation-backed half would earn its name.

## Revoked on 2026-08-02

The rules do arrive and the **Assumed** about what a wrong profile costs does
not hold. Driven over stdio from an `E-SITE`, the server derives `project` and
offers 20 tools. A core-shaped task got a core answer all the same, so every
route the **Assumed** named held. What costs more than one environment variable
is the answer itself. It routes to `typo3_test_run_guide`, which that client
cannot call, because `Coverage::offered()` filters those routes and `TaskGuide`
filters by the audience of the task instead. That is `R-SCO-007` failing. Nobody
has met it in the wild, and what no protocol-level run can show is what an agent
does with a route it cannot follow.

## Since then

The mechanism is gone rather than repaired. Profiles met what they bought, 2,223
bytes of a 92,189-byte tool list, and went on 2026-08-02. So no client gets a
shorter list unless it asked for one. What replaces them is
[`D-AUD-004`](aud-004-every-client-is-offered-every-tool.md), and nothing can
meet the second half of the **Wrong if** any more. No profile remains for a
deployment without an installation to earn.

## Since then

The second half stands read. The profile it stood against is gone (`D-AUD-004`),
so what remains is what a deployment with no installation gets, and that is all
23 tools. Every layer says where it stands. The scope answer reports the search
and the console out of reach. The three installation-backed tools answer with
the `unsupported` shape and route back to it. The one thing that does not know
where it is, is the `instructions`, which open on a tool that answers
`unsupported` here. Left as it is: the string is the same for every session, and
a client that follows it learns in one call what it has.
