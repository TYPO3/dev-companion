---
description: >-
  How a temporary functional test measures what one code path costs per call, how you get the same number for the patch and for its parent, and what a difference between the two leaves out.
whenToUse: >-
  When a finding turns on whether a change is slower or faster than the code it replaces, and nothing in the checkout measures it. A review that asks about performance is the case. To find out what a rendering contains rather than what it costs, use the rendering probe instead.
hints:
  - core-tests
---

# Timing a Code Path Between Two Revisions

What a change costs at runtime is not in the diff. A review that has to address
performance asks whether the patch is slower or faster than its parent. The
checkout has no number, and a guess from the code carries no weight. What
settles it is a temporary functional test that calls the path many times and
prints what one call cost. `core/testing/proving-a-rendering` is the same
harness for what a rendering contains. This page is for how long a call takes.

## The Probe

The file goes below `Tests/Functional/` of the extension the path lives in. A
file the runner does not collect measures nothing. It boots what a functional
test boots and nothing else. So the fixture is whatever the path needs to run at
all.

```php
#[Test]
public function probe(): void
{
    $subject = $this->get(ImageService::class);
    $subject->getImage('EXT:core/Resources/Public/Icons/Extension.svg', null, false);

    $calls = 500;
    $start = hrtime(true);
    for ($i = 0; $i < $calls; $i++) {
        $subject->getImage('EXT:core/Resources/Public/Icons/Extension.svg', null, false);
    }
    $perCall = (hrtime(true) - $start) / $calls / 1_000_000;

    echo "\n===TIMING=== " . round($perCall, 3) . " ms per call\n";
    self::assertTrue(true);
}
```

`hrtime(true)` counts nanoseconds and never moves backwards, which `microtime()`
can. The call before the loop is the warm-up. The first call pays for the
container, the TCA and every cache the path fills. None of that is the path. The
division gives one number a reader can compare. The count is high enough that
the loop outweighs the clock.

`echo` from the test body prints it, and the core's functional PHPUnit
configuration lets it through. `core/testing/proving-a-rendering` says why, and
what to do where something swallows the output.

## The Same Number Twice

One number says nothing. The finding is the ratio between the patch and its
parent, measured by the same probe in the same instance.

Run the probe on the patch. Then move the checkout to the parent and run it
again. For a change fetched from the review server the parent is `FETCH_HEAD~1`.
`git stash` holds the probe while `git switch` moves the tree under it. Run each
side more than once, because a single run carries the noise of the machine. A
difference smaller than the spread between two runs of one side is no
difference.

```bash
CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- typo3/sysext/core/Tests/Functional/Imaging/TimingProbeTest.php
```

Name the file after `--`. You run a probe again after every change to it and
after every move of the tree.

## A Claim in the Commit Message

A message that states a before and an after names a cause as well. That is an
expensive call the patch makes less often. Check the cause before the clock,
because a time is one machine's view of it.

- **Count the expensive call on both sides.** The count is the claim. Count from
  the probe rather than with a counter in the class under review, which would be
  a second change in the diff you review. A side effect the call leaves counts
  as well: distinct salted hashes in the database are hash calls. A probe that
  counts rows takes the query restrictions off first, as the `core-tests` hint
  says.
- **Measure where the author measured.** The message names the environment of
  its numbers. Where it names none, measure on SQLite and on MariaDB and report
  both. A number from another database does not refute the claim.
- **Give each cause its share.** A patch that changes two things gets each one
  turned off once. The report then says what each part bought.

## A Command the Test Setup Runs

A change to a console command that an installation runs once, such as setup or
generated demo data, has a harness the probe cannot give. The `core-tests` hint
names it where the branch has it: the instance a browser suite installs, with a
time on every line the setup prints. The gap between two lines is what the
command between them cost.

## What the Number Leaves Out

The number is one process on one machine against one database. Each of those is
a caveat the report carries.

- **The database is SQLite unless `-d` says otherwise.** A query costs something
  else there than on MariaDB. Where the path's cost is a query, measure with
  `-d mariadb` and say so.
- **The runtime cache is per process.** `cache.runtime` is a
  `TransientMemoryBackend`. Every call after the first reads what the first one
  wrote, so a loop measures the cached path. Where the uncached one is the
  question, flush that cache inside the loop and measure the flush beside it. Or
  time the first call alone.
- **The test instance hashes as production does.** The testing framework sets no
  `passwordHashing`, so a password goes through the hasher the core's default
  configuration names. `configurationToUseInTestInstance` sets another one.
- **One process, no concurrency.** Nothing here says what the path costs under
  load, with a warm opcache, or with a lock in the way.

## Removing the Probe

Delete the test when you have both numbers. Confirm with `git status` that the
checkout is clean and on the revision you started from. What the probe
established goes into the review, with the count, the database and the ratio.
The probe itself is evidence of nothing once somebody commits it.
