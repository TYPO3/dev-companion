---
description: >-
  How you settle whether a class, a member or a signature is there on a TYPO3 major the package declares and the installation does not have.
whenToUse: >-
  When the code has to run on more than one declared major and one of them is installed — before you write against an API the installed copy happens to have. It hands you the invocation per symbol: one git call against the branch that is not installed, or that major's released package where no checkout is at hand. Nothing here bundles a per-version list of identifiers, because the branch is what carries the shape.
hints:
  - extension-repository-layout
---

# Settling an API Question on a Declared Major That Is Not Installed

A package states the majors it runs on, and the installation around it supplies
one of them. Whether the API you write against is there on another one is a
question about that branch. You settle it when you read the branch. Nothing here
bundles a signature. A signature written down today is wrong at the next
release. A reader believes a wrong signature as readily as a right one.

## Which Majors the Question Is About

`typo3_project_describe` reports `coreConstraint`, which is what the package
requires of `typo3/cms-core`. The code has to hold on that range, and the
installed version is one point in it. A compatibility judgement against the
installed version alone judges one of the declared majors.

The knowledge answers already cover all of them. `typo3_hint_lookup` and
`typo3_task_guide` keep a statement that holds on any declared major and name
which those are. `targetVersion` narrows to one. None of them covers the core's
own source, which is where the shape of a class stands.

The question is per symbol rather than per package. A class that stands on both
branches can have gained a method on the newer one. A return type can be
nullable on one side. A constant can hold a different set of entries.

## What the Changelog Settles and What It Does Not

`typo3_changelog_lookup` answers with change events: what a version added,
deprecated, changed or removed. The entries of the older lines are on disk. A
core package ships the changelog of every line it has. So the changelog covers
the direction below the installed major.

An entry that says an API arrived before the older declared major settles that
the API is there. It settles nothing past that. Nobody writes an entry for what
did not change. So a member added later to a class that was already there leaves
no trace. Neither does a widened signature. The changelog narrows the question,
and the branch closes it.

## Reading the Branch

The core repository keeps every maintained line as a branch. So a checkout of it
answers for a major nobody has installed. It is one call per symbol:

```bash
git cat-file -e <branch>:typo3/sysext/<key>/Classes/<Path>.php
git grep -n "function <name>" <branch> -- typo3/sysext/<key>/Classes
git show <branch>:typo3/sysext/<key>/Classes/<Path>.php
```

Where you write against a whole subsystem, one diff reads it:
`git diff <installed> <declared> -- typo3/sysext/<key>/Classes/<Subtree>/`.

Those paths are the repository's, and the installed copy spells them the other
way. `typo3/sysext/<key>/` is `vendor/typo3/cms-<key>/` in an installation, with
the underscores of the extension key as dashes. So
`typo3/sysext/indexed_search/` is `vendor/typo3/cms-indexed-search/`. Everything
below `Classes/` is the same path on both sides.

A branch is the tip of its line and not the release this project resolved. The
tip is what the next update brings. So you read a constraint on that major
against the tip. Where one exact release is the question, read its tag.

Where no checkout of the branch is at hand, require that major's released
package into a directory of its own. Read the same file there. Where two or
three symbols are the whole question, read those files alone. To assemble the
branch for them costs more than they do.

## What Reading Proves, and What It Does Not

A read settles the shape and not the behaviour. What a member does with its
arguments is the run. The package's own suite, resolved against the other major,
says so. That costs the download of that major, and a change worth a proof gets
its proof there.
`typo3://guides/extension/compatibility/running-on-a-declared-major-that-is-not-installed`
says how you stand that major up beside the installation.

Where you can run nothing, the compatibility claim is an argument, and you write
it as one. It owes what you read: the symbols, the branch, and the revision that
branch was at. It owes what it left uncovered, and it says outright that it is
unproven. The next session cannot recheck an argument that names none of those,
and the next session pays for it.

What you do with a difference once you establish it is not this page. The
`extension-repository-layout` hint carries it. One policy in two places is the
pair that disagrees.
