---
date: 2026-10-23T00:26:13+00:00
category: missing-knowledge
status: closed
closed: 2026-10-23
model: claude-opus-5-5
tool: typo3_task_guide, typo3-core-patch-development, typo3-backend-module-development
directory: /home/benji/projects/typo3-cms
---

# Workflows do not tell the agent to flush the DI cache after a DI change

## Observation

Task: build a backend Lit component and a styleguide page for it in a TYPO3 core worktree.
The agent added two constructor dependencies to a backend controller (PackageManager, SetRegistry).
The compiled DI container of the running instance kept the old constructor signature.
The agent did not flush the DI cache on its own initiative. The user had to remind it.
The user wants this step every time a change touches dependency injection.
Changes of this kind: constructor arguments, Configuration/Services.yaml, Configuration/Services.php, and attributes like #[AsEventListener] or #[AsController].
A general cache:flush also flushes the DI cache. An agent does not know that it must run one after a PHP-only change.

## Query

Session task: "nimm dir einen worktree und schaue mal ob wir sowas irgendwo verwenden könnten" followed by "wir wollen eine lit component daraus machen, fange mit einer seite in der styleguide extension an". The change added constructor dependencies to typo3/sysext/styleguide/Classes/Controller/ComponentsController.php.

## Suggestion

Add a step to the workflows that change PHP services (core patch, backend module, extension work): "When you change DI (constructor arguments, Services.yaml/.php, DI attributes), flush the DI cache of the running instance before you test in the browser: `typo3 cache:flush --group di`."
Give the step again when the task guide is called for a test or a browser check after such a change.
Name the symptom too: the old constructor is still used, or the service is not found, until the DI cache is flushed.
