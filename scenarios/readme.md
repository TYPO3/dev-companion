# Forward reviews and contract cases

What this server is worth is decided in a session it did not design: someone
asks an agent to review their repository, the agent decides what matters and
reaches for whatever tools it has. The answer is either useful for that person
or it is not.

Two different tests used to be called scenarios here, and the name hid the
difference:

- [Open forward reviews](forward/readme.md) ask only for a review of the current
  project, extension, or core patch. They do not name a subsystem, expected
  defect, skill, tool, or implementation. What the agent chooses to inspect and
  prioritize is the evidence, and only these receive recorded runs.
- [Targeted contract cases](contracts/readme.md) name one task shape or failure
  mode so its routing and workflow can be held still. They are intentionally
  specific and do not claim that an agent discovered their subject.

Both use a prompt a user could type and criteria a person can judge. Neither
prompt names a tool or this server.

One case is one file, and it carries its own prompt and its own criteria. That
is the unit both kinds are read, run, and edited in: a file that holds several
prompts is a file where nobody can tell which criteria were judged.

## The three audiences

- **Core contributor** — works in a TYPO3 core checkout, patches go to Gerrit.
- **Extension author** — maintains an extension repository, often developed
  inside a site installation, released for several TYPO3 versions.
- **Site developer** — builds and maintains one installation: site
  configuration, site package, content, upgrades, deployment.

The same person is routinely two of them on the same day, which is why the
audience is a property of the task rather than of the directory
([`R-AUD-002`](../requirements/audience/aud-002-the-audience-is-a-property-of-the-task.md)).

## Environments

A case is only meaningful in the working directory it names, because instance
discovery starts where the MCP client launched the server. Start the client in
that directory, then paste the prompt.

| Id          | What it is                                                     |
| ----------- | -------------------------------------------------------------- |
| `E-CORE`    | A TYPO3 core checkout (`typo3/typo3`), dependencies installed. |
| `E-SITE`    | A Composer site installation, ideally under DDEV.              |
| `E-EXT`     | A standalone extension repository, dependencies installed.     |
| `E-NONE`    | A directory with no TYPO3 installation anywhere above it.      |
| `E-STOPPED` | `E-SITE` with the DDEV project stopped.                        |

A case that needs a console command names it. `E-SITE` is the environment
`language:domain:search` is asked of, which is a version question rather than a
property of the directory.

An environment is a kind of working directory, never one particular
installation. Which checkout on this machine plays one of them belongs in
[todo/reference/](../todo/reference/), where it can go stale without taking a
case with it — a prompt that names somebody's project is a prompt only that
person can run.

Two of the five this repository makes for itself, below `.environments/`:

```bash
bin/cli environment:status             # which ones this checkout has, and which are missing
bin/cli environment:create E-SITE      # a DDEV project with TYPO3 installed in it
bin/cli environment:create E-SITE 13.4 # the same, on another covered version
```

`E-SITE` is the one a run needs and the one that costs something — containers, a
database, a docker daemon a CI job may not have — and it is made here because
everything a case needs from it is a property this repository can state. One
installation runs one version, so there is one per covered version: named none,
the covered stable one, which is what a case that says nothing about a version
is run on. `SITE-02` is the case that says otherwise, and
`bin/cli environment:create E-SITE 13.4` is the previous major it names
([`D-EVI-006`](../decisions/evidence/evi-006-one-installation-per-covered-version-kept-and-started.md)).
The development line is `bin/cli environment:create E-SITE main`, built from the
base distribution's `dev-main` on PHP 8.5 because no release of either covers
it.

Each of them carries one extension of the project's own, `acme_events`, with a
table and rows in it. A base distribution registers nothing this project owns,
and half of what this server answers is about what it does — the rows of a table
belonging to one of them could be recorded nowhere
([`D-EVI-010`](../decisions/evidence/evi-010-a-made-installation-carries-an-extension-of-the-projects-own.md)).

`E-EXT` is not made here: what a case needs from an extension repository is real
infrastructure at a real revision, which no scaffold produces. `E-CORE` is
`bin/cli checkouts:update`, and `E-STOPPED` is `E-SITE` with its project
stopped. Asking `environment:create` for one of those answers with where it
comes from instead. The reasoning is
[`D-EVI-004`](../decisions/evidence/evi-004-the-environment-is-made-here-and-the-repository-under-review-is-not.md).

A made `E-SITE` is the environment, never the subject of a review. Its
installation is TYPO3's own base distribution, so what a forward review would
find in it is what this repository put there — which is the thing
[`D-EVI-001`](../decisions/evidence/evi-001-forward-evidence-comes-from-a-review.md)
exists to prevent. A recorded forward review still runs in a real project.

## Running one

- A forward review: [forward/readme.md](forward/readme.md). `bin/cli
  scenarios:show
  <id>` prints what to paste, `record` writes the empty run, `check` holds every
  recorded run to its review.
- A contract case: [contracts/readme.md](contracts/readme.md).
  `bin/cli scenarios contract <id>` prints it for inspection; it cannot be
  recorded as a forward run.
