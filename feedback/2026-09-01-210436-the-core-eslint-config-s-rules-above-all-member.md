---
date: 2026-09-01T21:04:36+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3_script_lookup
directory: /home/benji/projects/typo3-cms
---

# The core ESLint config's rules, above all member-ordering putting the constructor before static m...

## Observation

Migrating a local memory into the server: the rules that decide whether backend TypeScript passes lintTypescript. The memory was a copy of Build/eslint.config.mjs written out as prose, and that copy is the part of it that does not survive — the config is a file in the checkout the session is already in, and it moves when the core moves.

What is left after dropping the copy is the reason a session needs the config read at all, and it is one rule: @typescript-eslint/member-ordering requires the CONSTRUCTOR BEFORE all static methods — static fields, then constructor, then static methods, then instance methods. Nothing about the code looks wrong, so this is the most common lint failure when adding a static method to an existing class.

Two rules are OFF and are worth naming for the same reason, since a session assumes them on and writes around a restriction that is not there: no-explicit-any and no-inferrable-types.

Everything else the memory listed — indent, quotes, curly, the naming convention, the lit and wc rules — is read from Build/eslint.config.mjs and settled by CI=true ./Build/Scripts/runTests.sh -s lintTypescript.

## Query

Migrated from a local project memory file (eslint.md), summarising Build/eslint.config.mjs in the core checkout.

## Suggestion

The backend-typescript hint already answers this and should not be given the rule list: it names Build/eslint.config.mjs as the source, says that file is the list, gives the command that settles a question about it, and already calls out member-ordering and the two disabled rules. A copied rule list beside it would be a second source that drifts against the config on the next core commit, and nothing here would fail when it did.

Three rules from the memory were not in any hint and are now transferred, because each is invisible while reading the file rather than mechanical: the jQuery import into backend-typescript, bound because the core drops the dependency, and wc/no-constructor-params plus wc/require-listener-teardown into backend-ui. The rest of the memory was deleted with it.

So what is left is delivery rather than content: whether the hint reaches a session at the moment it touches a TypeScript file. Its appliesTo names Build/Sources/TypeScript/ and Resources/Public/JavaScript/, so check that a bare path to a .ts file under those matches, and add the extension where it does not. Judge this against the hint as it stands before adding anything to it.
