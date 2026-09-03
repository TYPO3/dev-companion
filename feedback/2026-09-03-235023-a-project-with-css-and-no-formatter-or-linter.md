---
date: 2026-09-03T23:50:23+00:00
category: missing-knowledge
status: open
model: claude-fable-5-1
tool: typo3_task_guide, typo3_project_describe
directory: /home/benji/projects/site-tierheim
---

# A project with CSS and no formatter or linter for it is not named as a gap in the checks

## Observation

Task: polish the visitor-facing frontend of a TYPO3 v14 project sitepackage (one hand-written stylesheet under Resources/Public/Css, no build step). typo3_project_describe listed the declared commands (php-cs-fixer, xliff-lint, phpunit, playwright) and typo3_task_guide with changeType feature on the CSS and Fluid paths returned a checklist with "Run targeted tests first; broaden to CGL, functional, or npm checks when relevant" and the extension-asset-build hint - but nothing said that the repository declares no formatter or linter for its stylesheets while it declares one for PHP and for XLIFF. The project owner then asked "does a CSS formatter not belong to project standards?", and the answer was yes: Prettier plus stylelint-config-standard were added afterwards, wired as composer lint:css beside cs:check and lint:xliff. The gap was visible from what typo3_project_describe already reads (composer.json scripts, Build/package.json devDependencies and scripts, the .editorconfig carrying a stanza for .stylelintrc that no file matched) and would have been a one-line finding.

## Query

typo3_task_guide task="Polish the visitor-facing frontend of a sitepackage: refresh CSS design tokens, typography, layout, components in Fluid page templates and the shipped stylesheet" changeType=feature paths=[packages/animalshelter_sitepackage/Resources/Public/Css/animalshelter.css, .../PageView/Layouts/Default.fluid.html, .../Partials/Page/Header.fluid.html, packages/animalshelter_animals/Resources/Private/Partials/AnimalShelter/AnimalCard.fluid.html] targetVersion=14.3; typo3_project_describe with no arguments

## Suggestion

When typo3_project_describe sees a package shipping files of a kind (CSS, JavaScript, TypeScript, Sass) that no declared command checks, say so in the commands section: "the packages ship .css under Resources/Public and no declared command lints or formats it". typo3_task_guide for a change touching such a file could carry a checklist item of the same shape, and the extension-asset-build hint could name the conventional pair for a project package - Prettier for layout, stylelint with stylelint-config-standard for the rules, run from the directory the package.json sits in, wired as a composer script beside the checks the repository already declares. The TYPO3 core's own Build uses stylelint for its Sass, which is the precedent an extension can follow; naming it would let an agent add the check with the change instead of after the owner asks.
