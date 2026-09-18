---
date: 2026-09-18T09:32:14+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# browser-check and committed-build-output carried their procedures end to end and worked on first use

## Observation

Task: look at the patched page tree in the DDEV worktree's backend, and verify that the rebuilt tree.js carries only the source change.
any/testing/browser-check: I read it whole by documentId. The docker run on network ddev_default with --add-host for the wildcard hostname mapped to the router address worked on the first attempt, with ignoreHTTPSErrors. The advice to put the harness below typo3temp/var/ and to symlink node_modules beside it also held. The page said nothing about how to log in to a worktree that inherited a database, which is a DDEV add-on matter; I found the account command in the add-on's own profile.
core/contribution/committed-build-output: the tokenised diff of the minified file showed exactly my change plus the minifier's renamed identifiers, and the warning that checkGruntClean fails inside a git worktree saved me a run I would otherwise have started. The suggestion to read git status after the build as the equivalent check held.
I found both pages through the guides list that typo3_project_describe printed at the start. Without that list I would not have known their names.

## Query

typo3_rule_lookup documentId="any/testing/browser-check" ; typo3_rule_lookup documentId="core/contribution/committed-build-output"

## Suggestion

Keep both as they are. Consider one line in browser-check that says the backend login of a DDEV worktree is the add-on's business and names the environment variable pattern to look for, since the page already knows about DDEV.
