---
date: 2026-10-28T16:28:00+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_project_describe, typo3_server_scope
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# a new project gets no version and no install method from this server

## Observation

A maintainer session compared four runs. Each run had to install TYPO3 and build a sitepackage.
The projects are jambo-chep (Claude Opus 5.5 with this server), jambo-plain (the same model without it), jambo-open and jambo-pickle (opencode).
No run chose TYPO3 14 on its own.
The two opencode runs did not know that 14 exists. One installed 13.4.37. The other began a Classic installation from a zip archive. It took a Composer project only after somebody named the Getting Started tutorial.
The two Claude runs offered a choice and advised 13.
Without this server, the run said that only 13 is stable. That is false.
With this server, the run knew that 14.3 is stable. It advised 13 because more extensions are available for that line. The finished project requires no third-party extension at all.
So the server corrected the fact and left the decision alone.
knowledge/versions.json records 13.4 as lts and 14.3 as stable. Nothing here says which line a new project takes. Nothing says that a new project is a Composer installation.
typo3_project_describe is the first call the instructions demand. On an empty directory it answers installed: false and typo3Version: null. That is correct, and it answers nothing a new project asks.

Asked afterwards why it took that version, the opencode run answered that 13.4.37 "is a stable LTS release", that it suits the PHP 8.2 of the DDEV container, and that it "aligns with the patterns and setup instructions referenced from the dev-companion knowledge base used for the installation".
This corpus does not say that. The hints carry 131 statements bound since 14 and 131 bound since 13. The literal 14.3 stands in knowledge/ twelve times and 13.4 seven times. PHP 8.2 rules nothing out either, because 13.4 and 14.3 both require ^8.2.
So the server was named as the warrant for a choice it never made.

## Query

typo3_project_describe in an empty directory, and typo3_server_scope with the versions section.

## Suggestion

Two changes.
Have ProjectDescribe name the current line from Versions::covered() where it finds no core installed. That call is already the mandated first one, and the number stays data.
Add a statement about what a new project starts on: which line, and that it is a Composer installation. An agent that weighs "lts" against "stable" takes the LTS. That reading is sound until something here says otherwise.
A statement also gives the next session something to check a claim against. An agent that cites this server for a version it chose on its own is harder to catch while no answer here names one.
