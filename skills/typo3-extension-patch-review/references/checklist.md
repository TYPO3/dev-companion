# Incoming change review checklist

The surfaces below are the work list. The coverage the report closes on is this
same list with every entry answered. You write it from the diff. An entry the
diff does not touch is not applicable and costs the line it costs here. One it
touches and nobody read has no assessment, which is not clean.

## Review surfaces

- **The change itself.** What it claims to do, what it does, and whether the two
  are the same thing. Where it changes what the frontend or the backend form
  renders, the diff says what it sets. It says nothing about what comes out.
- **What it removes or renames.** The package's own public surface: PHP members,
  TCA fields and types, TypoScript paths, Fluid templates, partials and
  sections. It is also ViewHelper arguments, label keys, site set settings,
  database columns. Whoever installed the package calls them.
- **The declared range.** Every TYPO3 and PHP version the package's manifest
  claims, not the one the installation happens to be. A change you verify on one
  point of the range holds there.
- **The API it reaches for.** Whether the core already offers the construct, on
  every declared major. Whether the form the change uses survives the next one.
- **Persisted data and editors.** Where the diff touches TCA, the schema or a
  migration. What happens to rows that already exist, and what the editor sees.
- **Labels and icons.** A user-facing string or an identifier the diff adds.
  Whether it resolves in the installation rather than only in the file.
- **Security.** A user-controlled value the diff moves, and the sink it reaches.
- **Coverage.** A test that fails before the change and passes after it. Where
  the package's suite has no layer that could carry one, that absence.
- **The commit message**, against the convention this repository writes.
- **Merge state and checks.** Whether the branch merges, what the pipeline ran,
  and which of the repository's own checks you ran here.

## Severity

The bands are the merge decision, because that is what the review is for:

- **Blocks the merge** — the change is wrong, breaks a version the package
  declares, or loses data. It opens a security boundary, or removes a public
  surface without a migration path.
- **Send it back** — the change works and is not ready. That is a defect in a
  case it covers, or a missing test for risky behaviour. It is a hand-rolled
  construct where a core API exists, or a message that does not meet the
  convention.
- **Worth a change** — a concrete cost that does not stop the merge.
- **Recommendation** — a beneficial improvement with no verified violation.

Severity follows the demonstrated consequence and not the size of the diff. A
one-line change that breaks a declared major blocks. A large change that only
moves code does not.

## What a finding owes

A concrete location in the diff, and what the code there does. The rule,
documentation or reading that says it is wrong. The consequence, and what would
remediate it. Short of those it is a question rather than a violation. A
question reported as a violation costs the author exactly the reading the review
skipped.

Say what the finding rests on. That is a line you read, a command and its
output, or a mechanism you traced into an installed package. A finding from a
pipeline configuration and one with a verified line are not worth the same. A
report that does not separate them says they are.

Say also whether **this change** introduced it. Report a defect the diff only
stands next to as pre-existing, with that word. To ask an author to fix what
they did not break is a different request. It is theirs to decline.

Where nobody here can settle a claim on a version nobody can run, the finding
says so. It names the reading or the run that would settle it. Unverified is a
result. A confident sentence in its place is not.

## What a dropped candidate owes

A review drops more than it reports, and nothing records the drop. Name each
candidate you raised while you read and then let go, with what let it go. That
is the guard that was there after all, the default that was the core's, the
class you read. One sentence each, beside the findings.

The two directions do not meet the same bar. To raise a candidate costs a
reading. To drop one costs the maintainer a finding, silently. So drop a
candidate only where something concretely disproves it. Report one you can
neither establish nor disprove as open, with the reading that would settle it
named beside it.

Two dismissals go wrong reliably. One drops a candidate because a comment or a
docblock says the code behaves that way. That is a sentence somebody wrote
rather than the behaviour. The other drops it because the case looks unlikely,
which is not disproved. What disproves a path is what makes it impossible. That
is a guard nobody can pass or a caller that cannot exist, at a line.
