---
id: D-SKL-062
title: 'The workflow question is asked again on a new subject'
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aBriefNamesTheActsTheWorkflowQuestionIsAskedAgainAt
  - ScopeTest::theSecondCallIsAskedAgainAtTheCallersOwnActs
---

# D-SKL-062 — The workflow question is asked again on a new subject

**The server names `typo3_task_guide` again where the work enters a subject the
opening did not name. The moment is what this server places and the wording
already fits.**

A client chooses a skill against the request, and the work is what would have
matched it. The request is the least informative moment of the session, and it
is the only one this server points at.

## Evidence

- **The session.** `/home/benji/projects/blog` on 2026-08-18,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-18-081159`](../../feedback/archive/2026-08-18-081159-skills-are-matched-once-against-the-opening.md).
  A German symptom report about two TypoScript conditions, one file selected in
  the editor. What followed was an API removal on a major, two new test
  directories, the repository's declared check suite, and a commit on a branch.
  Those are three subjects with an owner here, none of them named in the opening
  line. No skill activated, and the same session called nothing at all
  ([`feedback/2026-08-18-080710`](../../feedback/archive/2026-08-18-080710-a-whole-session-ran-with-zero-calls-because-a.md)).
- **The brief answers the sub-step and not the opening.** Measured in this
  worktree on 2026-08-18, through `TaskGuide::answer()`. "Add unit tests for a
  TypoScript condition provider in an extension" with the test path gets
  `tests`, and names `typo3-extension-testing` and the guide
  `extension/testing/phpunit`. So does "run the declared quality checks of this
  extension: phplint, phpstan, php-cs-fixer, unit and functional tests", which
  adds `coding-standards`. The session's own opening, "fix TypoScript conditions
  broken on TYPO3 v14 while keeping them working on v13", with the provider
  path, matches no intent. It names no skill and names no guide.
- **Every channel that names the tool names it at the start.** The
  `instructions` of `knowledge/server-scope.json` open "Start every task with
  typo3_project_describe" and continue "typo3_task_guide then gives the workflow
  the task belongs to". Its `routing` entries fire on starting a core task, on
  taking a patch on, and on upgrading an installation.
  [`skills/base.md`](../../skills/base.md) carries the heading "Where every task
  starts", and its step 3 asks for the guide once per session.
- **Not step 1b.** The skill is out, the intent that routes to it matches, and
  the tool answers. The three measurements above are that answer. A
  `bin/cli hints:probe` of the feedback's own subject reaches nothing and
  returns its 98 candidates as the index, which is right. The gap is not a
  statement about TYPO3.
- **The description route is shut.** The twelve published descriptions cost 3595
  characters of the 3600 `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn`
  allows, measured the way that test measures on 2026-08-18. `D-SKL-033` decided
  against listing characters for more request shapes, and mid-task entry points
  in a description are more of them.
- **The second session of the shape.**
  [`feedback/2026-08-18-113343`](../../feedback/archive/2026-08-18-113343-no-skill-covers-reviewing-an-incoming-pull.md)
  reports it from another checkout and another task. "a skill is chosen before
  any of them are read: only the PR URL", everything else found during the work.
  Its own subject is an absent owner, and this half of it is not that.

## Decided

- **The ladder's step 2, delivery.** The answer is here, it is one call away,
  and it reaches a session only where something asks for it. Everything here
  does that at the opening. The gap is the moment rather than a tool, a skill or
  a word in a description.
- **The moments are the caller's own acts**, which this server can name without
  a look at the checkout. Those are the first file created under a test
  directory and the first run of a check the repository declares. Then the first
  branch or commit, and the first edit to the documentation the package ships.
  Nobody can name a subject the same way. The session is inside one before it
  can say which.
- **This entry weighs the placement across three channels and the shape is the
  todo's first step.** The `instructions` are the one that reaches a session
  which calls nothing, which is what this one was (`D-AUD-003`). A `routing`
  entry reaches a session that read the scope. The brief's own answer reaches
  one that called once, where `D-GUI-012` already put the guide pointer. One
  sentence in one place, because three that each say it differently are three
  things to keep true.
- **Queued rather than made on the spot.** The strongest placement is the
  brief's own answer, which is `src/`, and what the `instructions` say is what
  every client receives at initialize. Both are what
  `documentation/records/judging.rst` keeps off the spot.
- **At `normal`.** Two sessions in two checkouts report the same asymmetry, and
  the words that would have matched the work were already in the file.
- **Against a running count.** The two reports of 2026-08-31 ask for the Nth
  call and the time of the last brief in a late answer. That is session state
  and a clock in a server whose every answer is a function of its arguments and
  the installation. Nobody has measured what it buys over the sentence, and the
  maintainer declined it on 2026-09-02.
- **Against the feedback's own suggestion.** Mid-task entry points in the
  descriptions cost characters that are not there. They would say in twelve
  installed files what one sentence here says once.

## Assumed

- That a session which passed over an instruction at the opening reads one at
  the moment the act happens. Nothing here measures that, and it is the same
  assumption `D-GUI-012` records for the guide it moved into the brief.
- That the sub-step reaches the intent matcher in the session's own words. The
  three briefs above came from task texts written here, and a session mid-task
  writes its own.
- That the acts above are ones a session notices when it takes them. Creating
  the first test file is visible in the transcript and nothing in this server
  sees it.

## Wrong if

- A session reports the re-ask in its context and a test directory created
  without it. Then the moment is not the lever either, and what remains is the
  client's own activation, which `D-SKL-033` stops at.
- A brief called mid-task names a skill that does not own the sub-step. Then the
  recognition is a search rather than a placement, and the acts match words
  instead of work.
- Sessions start to call the guide at every file they open. Then the moments
  were subjects after all, and what they cost is the round trips `D-FBK-027`
  weighs a tool against.

## Since then

The first **Wrong if** fired on 2026-08-31. A session held two briefs with the
line. It went on to a harness, a rename, an icon set and a README without a
second ask. What that leaves is the activation `D-SKL-033` stops at.

The placement is the brief rather than the instructions, which are full. The
sentence is a `nextTools` entry, unconditional and in the list the commit
message pointer already sits in. Then a session took the acts that the placement
cannot reach. The brief serves one that calls once and not one that never calls
again. So the imperative in `instructions.start` carries them, in the eight
characters that were free rather than the twenty the read assumed. The fifth act
joined merged with the fourth. That is one sentence in two places against the
**Decided** bullet above, and it is deliberate: the two reach different
sessions.
