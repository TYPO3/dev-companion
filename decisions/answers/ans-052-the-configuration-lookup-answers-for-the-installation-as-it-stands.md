---
id: D-ANS-052
title: The configuration lookup answers for the installation as it stands
date: 2026-08-04
status: open
---

# D-ANS-052 — The configuration lookup answers for the installation as it stands

**`typo3_configuration_lookup` reads the installation in the environment it is
in, and its description says so. Whether a caller may ask it under a stated
environment is a question for the maintainer.**

A session that audited a documented environment contract called it zero times
and used the console six, and was right to.

## Evidence

- `feedback/2026-08-04-180112`. What the session had to prove was conditional.
  `SYS/displayErrors` falls back to `-1` with `IS_DDEV_PROJECT` unset.
  `SYS/trustedHostsPattern` resolves through `settings.php`, the DDEV block,
  `.env`, `.env.local` and a real environment variable in that order.
  `DB/Connections/Default` picks up `TYPO3_DB_HOST`. Each run needed its own
  environment.
- None of that is expressible as "read path X from the installation", which is
  what the tool answers. The session reached for
  `vendor/bin/typo3 configuration:show` and reported the boundary rather than a
  defect.
- The session's own account makes this worth an entry: from the call log the run
  looks like one that ignored the tool. Nothing in the description says what a
  caller cannot ask it, so the next session spends the call to find out.

## Decided

- The description states the boundary. The tool reads the value in the
  environment the installation is in, and a value that has to resolve under
  another one is the console's. **Queued**, because it moves a declared tool
  surface.
- The `env` map the feedback proposes is **not decided here**. It would make
  "prove this variable reaches this path" one call for an audit shape that
  recurs. It would also make a lookup run the installation under an environment
  the caller composed. That would be the first thing this server does that is
  neither a read nor the caller's own console.
- That half goes to the maintainer rather than gets built or dropped quietly.
  One session, one task shape: the corpus does not carry the weight to decide it
  either way. `D-FBK-027`'s measure, what it takes off the caller, is six
  console runs in one session against a capability nothing else here has.

## Assumed

- That the boundary sentence is what the session lacked. It says so itself, and
  it is one session.

## Wrong if

- A session reads the sentence and files the same report anyway. Then the gap is
  the capability rather than the description, and the corpus answers the
  question above.
- The `env` map exists and the six-run shape does not recur. Then this was one
  audit's convenience.

## Since then

On 2026-08-04, the maintainer answered the half this entry put up: no `env` map.
The tool reports the instance that runs and what is in it. A value that only
exists under another environment is not a configuration anybody asked this
server about.

So the description states that positively rather than as a limitation, and the
console keeps the conditional half because it is the project's own. That settles
the second **Wrong if** above. The shape does not have to recur for the map to
be wrong, since what it would answer is not what this lookup is for.

## Since then

A read of the source against the covered lines showed it answers on two of the
four. The command arrived inside the newest major, so both LTS lines got the
console's own "command is not defined" as unsupported. That is the answer an
entry next to this one rules out in as many words, and this follows its view.
The booted container answers on every covered line, so the tool has one source
rather than a version-bound pair.
