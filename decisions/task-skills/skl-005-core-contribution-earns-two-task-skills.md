---
id: D-SKL-005
title: 'Core contribution earns two task skills'
date: 2026-08-03
status: open
---

# D-SKL-005 — Core contribution earns two task skills

**Core contribution earns two task skills, one to review a patch and one to
create one. What shows it is 35 feedback out of one checkout rather than one
run.**

`REVIEW-03` is the run that made it visible: a core patch review that called
this server nothing at all. The corpus behind it had said the same thing since
2026-08-01, with no judgement.

## Evidence

- The run. 23 calls in 256 seconds: 22 `Bash`, one `Read`, no server call, no
  skill. Session `8622fa17`, `claude-opus-5`, Claude Code 2.1.220, one prompt
  and no steer, judged `partial` in `scenarios/runs/REVIEW-03.json`. Not a
  delivery failure. The transcript's attachments carry the
  `mcp_instructions_delta` in full, first sentence
  `Start every task with typo3_project_describe`, and the `skill_listing` with
  all seven descriptions. Only the 22 tools arrived deferred, as names without
  descriptions.
- The nearest skill matches the shape and excludes the checkout.
  `typo3-extension-conformance` is "Review, audit, or improve a TYPO3 project,
  sitepackage, or extension … and report what is wrong with it in priority
  order", against a prompt reading "Review the current changes in this TYPO3
  core checkout … in priority order". It did not fire, and it was right not to.
  The other six are extension or site work by their own descriptions.
- 35 open feedback carry `directory: /home/benji/projects/typo3-cms`, in two
  clusters. Fifteen are patch **review**, from 2026-08-01, across three clients
  and four models. The GD/SVG placeholder patch, `7175fcaf7fe`, and the
  AssetCollector deprecation, each under review by a different session. Twenty
  are patch **creation**, from 2026-08-02, one session on Forge #105403. The
  worktree it worked in, the commit message, repeated runs of unit, functional,
  cgl and phpstan. Whether a patch already existed, the push to Gerrit as a
  private change, and the operation of Forge itself. No published skill names
  one of those things.
- One of them asks for it outright. `feedback/2026-08-01-115220`, GPT-5 mini:
  "Proposal: Add a dedicated MCP skill `typo3-patch-review` to support automated
  patch reviews." That is the third signal in
  [judging.md](../../documentation/records/judging.rst) — a domain reached
  independently by more than one session — and it sat in the queue while
  `REVIEW-03` reported the same gap again.
- The content is here and the order is not.
  `knowledge/documents/typo3-core-scripts.md` holds the core scripts,
  `typo3-commit-messages.md` the commit rules, and `typo3_script_lookup`,
  `typo3_test_run_guide` and `typo3_commit_message_guide` are tools this server
  ships. `bin/cli hints:probe` on the run's own prompt reaches nothing: 40 hints
  are candidates and none matches.
- Where the entry point did fire the answer was thin.
  `feedback/2026-08-02-144350` is a core session that called
  `typo3_project_describe` and got four `gerrit:setup` commands and no
  `Build/Scripts/runTests.sh`. It ran that script about thirty times and took
  its invocation syntax from elsewhere.

## Decided

- Two skills rather than one. To review a patch and to create one are two task
  shapes with two corpora. The split is the one the extension side already
  makes: `typo3-extension-conformance` reviews, the development skills build.
  Each rests on what its own cluster shows and nothing else.
- The entry comes before either skill exists, which departs from the rule that
  the commit that implements a decision writes it. What this entry settles is
  that the domain earned them and where the boundary runs. What they say is open
  and nobody can guess it from this repository.
- Not decided here: the names, the order each holds, and what each states. That
  is the reading
  [writing-a-skill.md](../../documentation/contributing/writing-a-skill.rst)
  demands before a line is written, and it is the todo this entry leaves. A name
  has to say core — `extension-conformance` for a site project is the mistake
  `D-AUD-003` spent four runs on.

## Assumed

- That the two clusters are two skills. The evidence is the task shapes. The
  review cluster never pushes anything, and most of the creation cluster is
  delivery, worktree, checks, Gerrit, Forge. A review may not touch that at all
  (`D-EVI-003`).
- That the corpus says what its titles say. This entry read four of the 35 in
  full. It read the rest as titles, models and directories. Reading them is the
  todo's first step and may move the boundary this entry draws.

## Wrong if

- The reading finds one order that covers both clusters. Then this is one skill
  and the entry split a domain by its verbs, which is what `R-SKL-010` exists to
  prevent.
- A second `REVIEW-03` run in the same client and model calls this server with
  neither skill published. Then the absent skill was not the obstacle, and what
  remains to suspect is what the tools answer a core checkout with.
- Both are out and the next core run still reads the checkout by hand. Then the
  domain did not earn them, and what a core session needs from here is smaller
  than 35 feedback made it look.

## Since then

Both clusters were read the same day, which settles the second assumption: they
contain the two orders rather than merely reporting a gap. The review order is
written down twice by sessions that arrived at it independently, one from the
chain that worked and one from finding no entry point at all, and what it has to
force — enumerate what the diff removes, and require a matcher and an `.rst` per
removal — is in two more, where two findings were under-stated for want of that
step. The creation order is one session's whole task filed in nineteen parts and
offered as a skill body.

What the reading also shows is that the two share their middle and not their
ends: both establish the change and run the same three tools, the review stops
before anything is written, and the creation half is mostly what happens after
the code is right.

## Since then

One of the four links the review order came off does not hold. Re-run in the
checkout it came from, the changelog lookup reaches that entry from the words
its title carries and from nothing the diff does. The session says so itself
four seconds earlier, since it found the entry with a grep of the checkout. So
the strength credits a tool for the finding its sibling records as that tool's
miss. That is the third corpus where the credit goes to the wrong place.

That changes the order's first step rather than the decision. A reviewer holds
what the diff removes, which is the one thing the matcher does not carry. So the
step reaches a precedent by the entry's own subject words or from the checkout,
and says which. The other three links reproduce, one credit looser than the
answer.

## Since then

Six readings held this decision and changed nothing in it, each judging one
feedback of the creation cluster. What they settled sits in the skill, the
requirement or the document each named: the precedent step that forbids reading
an empty changelog answer as "no precedent exists"; the four delivery questions,
written into the Gerrit document under `R-KNW-057` with one left partly open
because the tracker answers a fetch with 403; the assessment guidance, which
went to the patch skill rather than the guide; a rule quoted at the issue being
a claim to verify in the checkout; and the read direction of the same task,
which is routing rather than a gap.
