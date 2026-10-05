---
date: 2026-09-28T11:22:43+00:00
category: tool-gap
status: closed
closed: 2026-10-05
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_backend_module_lookup
directory: /home/lina/projects/typo3/manuals/coreapi
---

# project_describe does not say whether the installation can answer at runtime

## Observation

Task: verify a claim in a documentation PR about how backend module route identifiers are composed, working in the coreapi manual's checkout at /home/lina/projects/typo3/manuals/coreapi.

I followed the server instruction to call typo3_project_describe first. It answered fully from packages: typo3Version 15.0.0-dev, corePhpConstraint ^8.5, environment {via: ddev, php: 8.5, source: .ddev/config.yaml, project: reference-coreapi, entered: false}. Nothing in that answer said whether the installation was reachable, so I had no reason to expect the next call to fail.

typo3_backend_module_lookup {"query": "beuser"} then returned unsupported {cause: installation-not-answering, reason: "the DDEV project is stopped - start it with ddev start"}. That reason was precise and correct, and is the part that must not be broken. But it arrived only at the first runtime call, and cost three round trips of the same call: the person running me said they had started the companion, I called again and got the same refusal, and only after I ran ddev describe -j and ddev list in the checkout myself did it become clear that this root resolves to the DDEV project reference-coreapi, which was stopped, and that the project another session had started was a different one, so no shared instance existed.

After ddev start in this root the same call answered completely: 18 sub-routes for EXT:beuser, each named backend_user_management.BackendUser_<action>, plus a non-Extbase module for contrast whose only route is _default. That single answer settled the review claim better than reading source could, because it showed the live registry rather than the code that builds it.

The gap is not the refusal but the first answer: project_describe describes the environment it would use without saying whether that environment is up.

## Query

typo3_project_describe (no arguments), then typo3_backend_module_lookup {"query": "beuser"} three times. Task: reviewing docs PR 7131 in TYPO3CMS-Reference-CoreApi, which claims an Extbase backend module registers sub-routes named "<module identifier>.<Controller without suffix>_<action>", example my_extension_conferences.Conference_show.

## Suggestion

Have typo3_project_describe report the installation's runtime state beside the environment it names - whether the DDEV project (here reference-coreapi) is running, and which console it would use - so a session knows before its first lookup whether runtime answers are available, and can either start the project or stay with package-level answers. The answer already carries "entered: false"; a sibling such as "running: false" or "answers: packages-only" would have removed all three wasted calls.

Two details worth keeping in the wording: the note "answers from: installation" on a tool does not tell a session that the installation may be down; and on a machine where several sessions each run their own server, "the installation" is ambiguous, so naming the concrete DDEV project in both project_describe and the refusal (which it does) is what finally resolved the confusion.
