---
date: 2026-09-09T19:03:18+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# nothing knows the checkout provisions worktrees through a ddev add-on, though the whole session r...

## Observation

Task: rebase a Gerrit chain onto main "in a ddev branchery worktree" - the user named the mechanism in the request.

Filed because the server cannot learn this from the calls I made: the entire session ran inside a git worktree that a ddev add-on provisions, and nothing this server answered knew such worktrees existed here, while several answers were shaped by the fact that I was in one.

What typo3_project_describe reported was accurate as far as it went: environment.via "ddev", the config source, the project name, and the wildcard hostname *.typo3-cms.ddev.site. That wildcard is the tell - it is there precisely because each worktree gets its own subdomain - but nothing named the mechanism. hooks and providers came back empty, which was true of .ddev/config.yaml itself; the worktree provisioning lives in .ddev/branchery.yaml, and .ddev/ is gitignored in this checkout, so none of it would ever appear in a patch or in a tracked file.

Concretely, this checkout had ten worktrees under .worktrees/ when I started, each with its own PHP pool, its own database and its own address. The one I made got branchery_review_93628 and https://review-93628.typo3-cms.ddev.site/. Creating it is a long-running job with a detach flag and a jobs:show poller, and it takes about 80 seconds through nine named steps. All of that I had from the user's own CLAUDE.local.md, not from here, and that is the right place for it - the add-on is that user's own project and machine-specific.

Where it stops being purely local, and why this is worth a note:

1. typo3_test_run_guide's preconditions say a git worktree starts without vendor/ and bin/ because both are gitignored, and that composerInstall is therefore owed before any PHP suite. That is correct for a plain git worktree and was WRONG for this one - the provisioning copies the gitignored files across, so vendor/ and bin/ were already there and every suite ran without a composerInstall. The guide's own wording allows for it ("a checkout that already has vendor/ needs it again only after composer.json or composer.lock changed"), so nothing broke. But a session that trusted the precondition literally would have run a redundant composerInstall in a container. The reliable test is "does vendor/ exist in the directory the suite will mount", not "is this a worktree", and the guide could say it that way round.

2. The worktree warnings that DID fire were the most valuable thing in that answer (cglGit reporting SUCCESS having read nothing, checkGruntClean staging the whole tree). Those hold for any worktree however it was made. Keep them exactly as they are; only the vendor precondition is the one stated on the wrong axis.

I am not asking the server to learn one person's add-on. The transferable version is: state the worktree preconditions as checks against the directory rather than as properties of being a worktree, since tooling that provisions worktrees is common enough that "worktree implies no vendor" is not safe.

## Query

typo3_project_describe on /home/benji/projects/typo3-cms. Answer carried environment: {via: "ddev", php: "8.5", source: ".ddev/config.yaml", project: "typo3-cms", hostnames: ["typo3-cms.ddev.site", "*.typo3-cms.ddev.site"], entered: false, hooks: [], providers: []}. The work then happened in /home/benji/projects/typo3-cms/.worktrees/review-93628.
