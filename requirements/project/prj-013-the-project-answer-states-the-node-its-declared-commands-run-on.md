---
id: R-PRJ-013
title: 'The project answer states the Node its declared commands run on'
status: held
restsOn: [D-SCO-013, D-SCO-014]
heldBy:
  - ProjectTest::aNodeAWorkflowDecidesElsewhereIsStatedBack
  - ProjectTest::aRepositoryThatDeclaresNoNodeIsSaidToDeclareNone
  - ProjectTest::aRepositoryWithNoNpmSurfaceIsToldNothingAboutNode
  - ProjectTest::theManifestBelowBuildIsReadWhereTheRepositoryKeepsItThere
  - ProjectTest::theNodeThoseNpmCommandsRunOnIsStatedBesideThem
---

# R-PRJ-013 — The project answer states the Node its declared commands run on

**Where a repository declares npm commands, the answer states the Node each file
names and how they relate.**

Those files are `engines.node`, an `.nvmrc`, an `actions/setup-node` step, a
DDEV `nodejs_version`.

The composer half of that command list has carried its interpreter since
`R-PRJ-008` and the relation between the numbers since `R-PRJ-010`. The npm half
beside it carried none. The difference between the Node on the machine and the
Node in CI is what a build breaks on.

The answer reads it from the files as they stand, so `R-PRJ-001` still holds and
the answer arrives on a fresh clone. Nothing runs, which is the whole of what it
may not claim. It says what the repository declares, never that any of it ran.
The Node the caller's own shell has is in none of these files.

The answer reads a version where a file names one outright and states the file
back where it does not. A `node-version` that is a matrix entry, an expression,
an `lts` alias or a range comes back as the workflow writes it. The workflow is
one file for the caller to open, and a resolved wrong number carries this
answer's authority.

The comparison takes only the segments both sides spell. An `.nvmrc` with a
major and a workflow with a patch level agree wherever the major does. The
release difference inside one major is a thing no file here states.

The answer says it even where nothing declares one, because that silence is the
finding. The npm commands are in the list above and whatever node is on the path
runs them. It withholds it only where the repository has no npm surface at all.

## From

`feedback/2026-08-18-113501` (2026-08-18), a sitepackage maintenance session in
`bootstrap_package`. Five sixths of it were Node, npm and GitHub Actions work.
Its defect was that fantasticon wrote 65536 bytes instead of 5828 because Node
24.19 raised `Buffer.poolSize`. It reproduced only in a container, because the
machine ran 24.16 and CI ran 24.19.

## Held by

The answer reads every `package.json` `Project::commands()` reads its npm
scripts from. That is the root one, and the `Build/package.json` beside it that
a core checkout has instead of a root one. It answers each number with the file
it came from. The same field means the root's manifest in one repository and the
one below it in the next (`D-SCO-014`).
