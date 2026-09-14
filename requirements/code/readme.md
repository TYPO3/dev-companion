# Code — what must hold of the source itself

Not what an answer has to be right about, but what `src/` has to stay. That is how a file maps to a class, what stays reachable, and what a check goes through to count as one. Every other group here is about a caller; this one is about the tree the callers reach through. It exists because a repository can be wrong in a way no answer is wrong yet.

Its counterpart is [`decisions/code/`](../../decisions/code/readme.md), which
holds what a change to that shape rested on.

See [the requirements readme](../readme.md) for how a session writes an entry and when it adds one.

- [`R-COD-001`][R-COD-001] — Every entrypoint is driven by a test that goes through it · held
- [`R-COD-002`][R-COD-002] — What the server ships is held to the prose rule · held
- [`R-COD-003`][R-COD-003] — A unit test holds a small part and stubs what is outside it · not guarded
- [`R-COD-004`][R-COD-004] — The versions this repository pins are checked against the day's release · not guarded

[R-COD-001]: cod-001-every-entrypoint-is-driven-by-a-test-that-goes-through-it.md
[R-COD-002]: cod-002-what-the-server-ships-is-held-to-the-prose-rule.md
[R-COD-003]: cod-003-a-unit-test-holds-a-small-part-and-stubs-what-is-outside-it.md
[R-COD-004]: cod-004-the-versions-this-repository-pins-are-checked-against-the-days-release.md
