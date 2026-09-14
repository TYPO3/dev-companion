---
id: D-FBK-050
title: A package's release policy is asked rather than derived
date: 2026-08-19
status: open
---

# D-FBK-050 — A package's release policy is asked rather than derived

**A package's release, merge and backport policy is a question for the
maintainer. The skill that lands a change delivers the rule that says so.**

`feedback/2026-08-18-113441` asks `typo3_project_describe` for it instead, out
of a branch and tag pattern where nothing stands written. The session that filed
it had already run those git commands, and what they returned is what nearly
sent a fix to an unsupported line.

## Evidence

- The cost is in the feedback and it came after the fact. The rules arrived from
  the user halfway through, by which time a branch stood pushed and a pull
  request open. Five maintenance branches needed a survey to work out which the
  second fix reached.
- The session's own account refutes the shape it asks for. It saw `BP_6-2`
  through `BP_16_0` in the remote refs and read `git log --merges`. It wrote
  that "which of these branches is still alive", "master first" and "never fix
  directly on a maintenance branch" are policy rather than history. Git answered
  and the answer did not carry the policy.
- What a derived answer would have said is the wrong thing. The session nearly
  proposed a cherry-pick into `BP_15_0` and below, which have no support at all.
  It names what stopped it: the Node version each branch pins in CI, not any
  knowledge of the support policy. A `BP_*`-and-tags pattern reported by this
  server is the same inference with this server's confidence on it.
- Git state is already declared outside the boundary. It is the first
  `doesNotCover` entry in `knowledge/server-scope.json`, "Your git state:
  changed files, branch, working tree, commit message, history". `D-FBK-037`
  declined a tool that reads git for a reason that holds here too: git is local,
  documented, and answers the first time. `D-FBK-038` carries that half forward
  unchanged.
- The rule the session needed exists nowhere.
  `bin/cli hints:probe "release branch and backport policy for a project package"`
  returns three PHP hints. They are about breaking changes, public API surface
  and a TER release, and none is about it.
- Every occurrence of "backport" in `knowledge/` is the core's own process. The
  changelog directory a backport goes into, the Change-Id a backport on a
  release branch keeps, the browser baseline an older line carries. For a
  package that is not the core the word appears nowhere, and the core's rule is
  the one an agent carries in by default.
- No skill owns the phase where the cost was paid. `typo3-extension-cleanup` is
  the skill that carries results through to committed changes and names no
  branch, no push and no release line. The draft `typo3-extension-patch-review`
  stops at the verdict and routes there for the change itself.
- `skills/base.md` stops one step short in the paragraph that owns this. "What
  this server does not know" says which branch you are on is yours to establish.
  It does not say that which branch a fix belongs on is the repository's policy
  and derivable from nothing here.
- `D-ANS-085` is where the tool's boundary stands: what the repository's own
  files declare, wherever the search finds a project root. The commands, the
  patches and the DDEV lifecycle are all of that kind — declared,
  machine-readable, one right answer. A release policy is none of the three in
  the repository this feedback came from.
- The corpus is 8 open across 3 directories. Five are this checkout and all five
  are one session. So the two that name this ground are one session twice rather
  than two sessions. Those are this one, and `feedback/2026-08-18-113425`, which
  names the document it wanted, "Release branches and backporting in a project
  package".

## Decided

- Step 1a, the knowledge, and the absent statement is a negative one: that no
  source here carries a package's release policy. Its ground is this repository
  rather than TYPO3, so it owes nothing to `.checkouts/` or the manual.
- The half that asks `typo3_project_describe` to report a policy inferred from
  branches and tags stays out. It is the inference the session made, and a
  coincidence saved it. A field with it would make the same mistake with this
  server's authority behind it.
- Queued rather than closed on the spot. A `SKILL.md` lands in somebody else's
  project, where the next release of this server corrects no wrong sentence.
  `documentation/records/judging.rst` puts a contract beyond a run that has read
  only this repository.
- Priority `normal`, above the `low` a card arrives at. This is the half of the
  feedback that would have prevented the cost. The change is a rule on a surface
  that already exists, and nothing blocks it.
- The rule goes into `skills/typo3-extension-cleanup/SKILL.md`, at the commit
  step, because that is where the branch choice happens. Not into
  `skills/base.md`: it would cost every task a paragraph for a question only a
  task that lands something asks.
- Not a document. `D-FBK-043` answers a structure with one, and the gap here is
  a statement rather than an order of steps. There is no TYPO3-wide convention
  for a package's release lines to write down.
- The feedback stays open until the commit that writes the rule archives it. The
  card that served it carries the next step instead of the judgement it asked
  for.

## Assumed

- That a package's supported lines are not reliably declared in a file this
  server already reads. What exists is narrower: a Composer `branch-alias` maps
  a development branch to a version, and a workflow's branch triggers say what
  CI runs on. Neither says which lines still have support, and no repository had
  a read to check how often either is present.
- That the session which lands a change is the one that needs the rule. The cost
  was paid at the push and the pull request, both of which are inside that task.
- That one session with two reports of this is one report. The judgement rests
  on what the session had to do instead, which the feedback states, rather than
  on a count.

## Wrong if

- A session reads a release policy out of a file this server already opens, a
  `CONTRIBUTING.md`, a `.github` config, and pays for the read. Then the
  declined half was real, and what was wrong was the inference from refs rather
  than a report of what a file declares.
- The rule arrives and the session pushes to a maintenance branch anyway. Then
  it is step 4, wording, and a rule stated where the task passes was not enough.
- A session gets the rule, asks, and the maintainer answers that it stands in
  the repository. Then the question is a step this server made necessary.
- A task that lands a change in a package turns out not to run under
  `typo3-extension-cleanup`. Then the placement is wrong and the rule reaches
  nobody, which is step 2 arrived at in advance.

## Since then

The fourth **Wrong if** happened in the harmless direction and the rule moved
with the work. The skill it named folded into another, and step 12 of that one
is where the rule now stands. The policy is a question for the maintainer before
a push, and a branch listing is not that answer. So the placement changed name
and the rule reaches the same task. The other three wait on a session, and
nothing since reports any of them.
