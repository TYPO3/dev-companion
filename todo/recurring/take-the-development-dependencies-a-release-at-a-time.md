---
serves: [tests/]
every: 30 days
checked: 2026-09-01
run: [composer outdated --direct]
---

# Take the development dependencies a release at a time

Read what the command lists and take each on its own merit rather than bump the
file. A patch release of a tool that only runs in `composer ci` is worth it. A
major one is a change to what the checks say, which is work and not maintenance.
A release inside a constraint the file already carries is a `composer update` of
that package and nothing else. `ci.yml` resolves per PHP version and the lock is
what this checkout resolved (`D-COD-008`). `mcp/sdk` is not part of this. It has
a todo of its own every seven days. It is the protocol every answer travels over
rather than a tool that checks this repository. A fold of the two would put that
question on a monthly clock.
