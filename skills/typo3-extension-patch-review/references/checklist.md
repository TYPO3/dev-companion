# Incoming change review checklist

The surfaces below are the work list of the review. The report closes on the
same list, with every entry answered. Answer each entry from the diff:

- An entry the diff does not touch is not applicable. Its line says so.
- An entry the diff touches and nobody read is unassessed. Unassessed is not the
  same as clean.

## Review surfaces

- **The change itself.** What it says it does, what it does, and whether the two
  agree. Where it changes what the frontend or a backend form renders, the diff
  shows what it sets. It does not show what then comes out.

- **What it removes or renames.** The package's own public surface: PHP members,
  TCA fields and types, TypoScript paths, Fluid templates, partials and
  sections. Also ViewHelper arguments, label keys, site set settings and
  database columns. Whoever installed the package calls them.

- **The declared range.** Every TYPO3 and PHP version the package's manifest
  declares, not only the installed one. A change you verify on one version holds
  on that version.

- **The API it reaches for.** Whether the core already offers the construct, on
  every declared major. Whether the form the change uses still works on the next
  major.

- **Persisted data and editors.** Where the diff touches TCA, the schema or a
  migration: what happens to existing rows, and what the editor sees.

- **Labels and icons.** A user-facing string or an identifier the diff adds.
  Whether it resolves in the installation, not only in the file.

- **Security.** A user-controlled value the diff moves, and the sink it reaches,
  such as a query or the HTML output.

- **Coverage.** A test that fails before the change and passes after it. Where
  the package's suite has no layer for such a test, report that.

- **The commit message**, against the convention this repository writes.

- **Merge state and checks.** Whether the branch merges, what the pipeline ran,
  and which of the repository's own checks you ran.

## Severity

The levels are the merge decision, because the review exists for that decision:

- **Blocks the merge** — the change is wrong, breaks a version the package
  declares, or loses data. It opens a security boundary, or removes a public
  surface without a migration path.

- **Send it back** — the change works but is not ready. It has a defect in a
  case it covers, or no test for risky behaviour. It builds by hand what a core
  API does, or its message breaks the convention.

- **Worth a change** — a concrete cost that does not stop the merge.

- **Recommendation** — an improvement with no verified violation.

Severity follows the consequence you showed, not the size of the diff. A
one-line change that breaks a declared major blocks the merge. A large change
that only moves code does not.

## What a finding needs

A finding needs four things:

- a concrete location in the diff, and what the code there does;
- the rule, documentation or reading that says it is wrong;
- the consequence;
- what would fix it.

Without them it is a question, not a violation. A question reported as a
violation makes the author do the reading the review skipped.

Say what the finding rests on: a line you read, a command and its output, or a
mechanism you traced. A finding from a pipeline configuration is worth less than
one with a verified line. A report that does not separate the two makes them
look equal.

Say whether **this change** introduced it. A defect the diff did not cause is
pre-existing: report it with that word. A request to fix what the author did not
break is a different request, and the author may decline it.

Where nobody here can run a version, and so nobody can settle a claim about it,
the finding says so. It names the reading or the run that would settle it.
Report the finding as unverified. Do not write a confident sentence in its
place.

## What a dropped candidate needs

While you read, you suspect problems that turn out to be none. Each such
suspicion is a candidate. The report does not show a dropped candidate unless
you list it. So list each one with the reason you dropped it, in one sentence,
beside the findings. A reason can be a guard you found, a default of the core,
or a class you read.

A wrong raise and a wrong drop do not weigh the same. A candidate you raise
wrongly means one more file for you to read. A candidate you drop wrongly hides
a real finding from the maintainer, and nobody notices. So drop a candidate only
where something concretely disproves it. Report a candidate you can neither
establish nor disprove as open, and name the reading that would settle it.

Two reasons to drop a candidate are often wrong:

- **A comment or a docblock says the code behaves that way.** That is a sentence
  somebody wrote, not the behaviour.

- **The case looks unlikely.** Unlikely is not disproved.

What disproves a path makes it impossible: a guard nobody can pass, or a caller
that cannot exist. Name its line.
