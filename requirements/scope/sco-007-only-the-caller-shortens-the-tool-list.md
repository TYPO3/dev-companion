---
id: R-SCO-007
title: 'Only the caller shortens the tool list'
status: held
restsOn: [D-AUD-004]
heldBy:
  - ExcludedToolsTest
---

# R-SCO-007 — Only the caller shortens the tool list

**The server offers every client every tool, and the only thing that takes one
away is the caller who names it.**

Which repository the server started in shapes what an answer says, never whether
the tool that says it is there. Whether a task is core work is a property of the
task, which is
[`R-AUD-002`](../audience/aud-002-the-audience-is-a-property-of-the-task.md),
and the tool list cannot vary per task. Where the caller does exclude one,
nothing the server hands out points at it. `typo3_server_scope`, which no caller
can exclude, names what went and which variable took it. A shorter list a client
cannot explain is a broken server as far as it can tell.

## From

The `project` profile withheld `typo3_test_run_guide` while a core-shaped task
asked from a site installation still got a core answer and a route to it. That
happened twice on a patch and six times on a test task (`E-SITE`, 2026-08-02).
Weighed and removed under
[`D-AUD-004`](../../decisions/audience/aud-004-every-client-is-offered-every-tool.md).

## Held by

- `ExcludedToolsTest` in full. It holds that no kind of repository shortens the
  list. It holds that a core-shaped task from a project gets its answer and the
  tool it routes to. It holds that the scope both the tool answer and the
  resource index build from routes to nothing excluded. It holds that nobody can
  exclude the tool that explains a short list
