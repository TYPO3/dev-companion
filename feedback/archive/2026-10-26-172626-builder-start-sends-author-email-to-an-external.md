---
date: 2026-10-26T17:26:26+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3-sitepackage-development
directory: /home/benji/site10-run8
---

# Builder start sends author email to an external service; skill is silent

## Observation

Task: create a sitepackage with the get.typo3.org builder, the skill's default start.
The SitePackageType schema requires author.name, author.email, author.company and author.homepage.
The skill says to take the author from composer.json or the git configuration. That gives the user's personal email. The POST then sends it to get.typo3.org.
I sent a placeholder email and wrote the real email only into the local composer.json.
The builder also writes the email into every XLF header. I rewrote those files, so it did not matter here.

## Query

POST https://get.typo3.org/api/v1/sitepackage/ base_package=fluid_styled_content typo3_version=14.3

## Suggestion

Say in the skill: the builder requires an email; send a placeholder unless the user agrees, and set the real author in the local files after generation.
