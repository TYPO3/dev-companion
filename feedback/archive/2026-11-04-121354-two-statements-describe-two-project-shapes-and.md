---
date: 2026-11-04T12:13:54+00:00
category: missing-knowledge
status: closed
closed: 2026-11-06
model: claude-opus-5
tool: typo3_hint_lookup, typo3_task_guide, typo3-sitepackage-development
directory: /home/lina/projects/typo3/tools/dev-companion
---

# two statements describe two project shapes, and neither says which a site takes

## Observation

The task was a trial on 2026-11-03. A cursor-agent run on grok-4.7-high built a TYPO3 site from the static design in design/. It had this server and the 15 skills, and it installed TYPO3 14.3.10.

The run made the sitepackage the Composer root package. Its composer.json carries `type: typo3-cms-extension`, the core packages in `require`, and `extra.typo3/cms.web-dir`. No project package requires the extension. So no file carries the dependency that SITE-10 asks a session to name.

The run received two statements, and each describes a different shape.

`sitepackage-start` says a package below packages/ renders nothing until the project's root composer.json requires it. That sentence reached the session 12 times.

The extension hints carry a `root package` statement. It says an extension's own composer.json can install TYPO3 beneath itself. That shape keeps `type: typo3-cms-extension` and moves the installation with `config.vendor-dir`. That sentence reached the session 6 times.

The second statement holds for a standalone extension repository. The session applied it to a site project. Neither statement says which shape a site project takes.

The control arm of the same pair had no server and no skills. It wrote a project package plus packages/jambo_sitepackage over a path repository, and required it as ^1.0. So the arm with the statements took the worse shape.

feedback/2026-10-29-105043 reports the same pattern for other statements. A reader judges the two together.

## Query

The prompt was: "I'm a PHP developer and I have never used TYPO3. There is a static HTML design in design/. I want to build this site in TYPO3." The session reached `typo3_task_guide` from it, and then `typo3_hint_lookup` for the ids the guide withheld, `sitepackage-start` among them.

## Suggestion

The sitepackage statement says which shape a site project takes, before the first file. That is a project package at the root, the sitepackage below packages/, a path repository on packages/*, and `composer require`.

The `root package` statement says where it holds. It holds for an extension repository with a development installation, and not for a site project.

A test holds the route, so a sitepackage task reaches the first statement rather than the second.

A run of SITE-11 from a pristine directory measures the repair, because the artifact shows the shape.
