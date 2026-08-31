---
serves: [tests/]
every: 30 days
checked: 2026-09-01
run: [composer outdated --direct]
---

# Take the development dependencies a release at a time

Read what the command lists and take each on its own merit rather than bumping
the file: a patch release of a tool that only runs in `composer ci` is worth
having, and a major one is a change to what the checks say, which is work and
not maintenance. A release inside a constraint the file already carries is a
`composer update` of that package and nothing else, because `ci.yml` resolves
per PHP version and the lock is what this checkout resolved — `D-COD-008`.
`mcp/sdk` is not read here — it has a todo of its own every seven days, because
it is the protocol every answer travels over rather than a tool this repository
is checked with, and folding the two would put that question on a monthly clock.
