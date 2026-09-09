---
date: 2026-09-09T18:26:15+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_test_run_guide, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# On a core checkout, commands lists the npm invocations AGENTS.md forbids and omits runTests.sh

## Observation

Task: review and finish TYPO3 core Gerrit change 93620, in the typo3/cms core monorepo. typo3_project_describe correctly identified kind "core-checkout", typo3Version 15.0.0-dev.

Its commands array listed, among others:

  {"command":"npm --prefix Build run build","invocation":"ddev exec npm --prefix Build run build","declares":"./node_modules/.bin/grunt","runs":"change"}
  {"command":"npm --prefix Build run lint","invocation":"ddev exec npm --prefix Build run lint",...}
  {"command":"npm --prefix Build run test","invocation":"ddev exec npm --prefix Build run test","declares":"wtr",...}

The repository's own AGENTS.md, which every coding agent working here is told to follow, says the opposite in as many words: "Everything runs through one container-based dispatcher, ./Build/Scripts/runTests.sh (podman by default, -b docker to switch). Do not invoke phpunit, phpstan, php-cs-fixer, npm or grunt directly - the wrapper supplies the PHP version, database service and bootstrap."

runTests.sh does not appear in the commands array at all. Not one suite. The five I actually ran and that CI enforces - build, lintTypescript, unitJavascript, checkGruntClean, cglGit - are absent.

The server is not making things up: those npm scripts are declared in Build/package.json, and reporting them is defensible in general. But for kind "core-checkout" specifically it inverts the repository's contract. A caller who trusts commands, as the initialize instruction tells it to ("a check you recommend that the repository does not declare is a wrong answer however sensible it sounds"), would run the exact invocations the repo forbids and would never learn the dispatcher exists.

I did not fall into this because AGENTS.md is loaded into my system prompt in this checkout and I read it before the server answered. A caller without that would have.

The server does know about runTests.sh - the guides array carries core/testing/scripts, "TYPO3 Core Script Help", "When running a suite inside a core checkout". So the right answer is present, one indirection away, while the commands field states the wrong one directly.

## Query

mcp__typo3-dev-companion__typo3_project_describe, no arguments, in /home/benji/projects/typo3-cms (TYPO3 core monorepo, main, 15.0.0-dev). Answer: kind "core-checkout"; commands array containing "npm --prefix Build run build", "npm --prefix Build run lint", "npm --prefix Build run test", "npm --prefix Build run watch:build" and the composer gerrit:setup family; no entry for Build/Scripts/runTests.sh. Suites actually run in the session, all via the dispatcher: -s build, -s lintTypescript, -s unitJavascript, -s checkGruntClean, -s cglGit.

## Suggestion

When kind is "core-checkout", either put the runTests.sh suites in commands as the declared checks, or mark the npm entries as not-the-supported-route and name the dispatcher in the same field. A line on each npm entry saying "the core drives this through Build/Scripts/runTests.sh -s <suite>; invoking npm directly skips the PHP version and database service" would fix it without dropping accurate information.

The suite list is discoverable at runtime (runTests.sh -h), so it does not have to be maintained by hand - but the pointer does have to be in commands, because that is the field a caller reads when deciding what to run.
