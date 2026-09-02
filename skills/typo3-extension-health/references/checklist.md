# Conformance audit checklist

Read the relevant sections for a scoped review; read all sections for a full
extension audit. Either way the surface list below is written whole, and a
surface the request left out is reported as not requested rather than dropped.
Absence of an optional subsystem is not a defect.

## Audit surfaces

- Package: identity, Composer constraints, autoloading, extension metadata, and
  supported TYPO3/PHP range.
- Registration and runtime: services, events, middleware, plugins, content
  elements, backend modules, routes, permissions, and effective configuration.
- Persistence: TCA, schema, relations, DataHandler use, repositories, fixtures,
  and upgrade paths.
- Rendering: site sets, TypoScript, TSconfig, Fluid roots and namespaces,
  templates, translations, and public assets.
- Security: authorization boundaries, state-changing requests, output-context
  escaping, raw rendering, query construction, user-controlled attributes, URLs
  and paths, and secret exposure. Every one of them is a value and a sink, and
  the finding gate below is how one is established.
- Quality: the test suite and the supported TYPO3 versions it runs on, the check
  layer, documentation — `typo3_hint_lookup` with `id=extension-documentation`,
  which says what a manual consists of and that it ships with the package —
  deprecations, and upgrade readiness.
- Pinned versions: the Node, the actions under `.github/workflows/`, the
  container configuration and the declared dependencies, each read against the
  release current on the day rather than against the file. One behind it is a
  finding carrying the raise; what speaks against the raise is a bound the
  package declares, and the finding then names the newest release that bound
  allows.

## The check layer

The commands a repository declares are where this surface is read, never what it
is. Measure them against what a complete layer covers, each entry named by what
the check establishes rather than by the tool behind it:

- **Syntax** — every shipped PHP file parses on every PHP version the package
  declares support for.
- **Static analysis** — types, unreachable code, and calls that cannot succeed,
  over the paths the package owns and at a level the project can hold today.
- **Coding standards** — the TYPO3 coding guidelines as the project applies
  them, with the editor-configuration and declared-PHP-range checks that run
  beside them.
- **Manifests and dependencies** — the manifest validates, agrees with what the
  package declares about itself elsewhere, and carries no open advisory.
- **Shipped configuration and data** — the XLIFF, YAML and TypoScript the
  package ships are well-formed. Fluid templates have no established linter and
  are proven by the tests that render them.
- **Shipped frontend assets** — the JavaScript, TypeScript and CSS sources the
  repository maintains, never the bundle a build step produced from them.

Which of them apply is decided by what the package ships: a check whose subject
it does not ship is absent for a reason, while one whose subject it ships and no
command covers is a gap in the layer rather than an optional subsystem, and that
absence is the finding. Ask the same of where each one runs — syntax and
analysis depend on the PHP and TYPO3 combination and belong in a matrix, while a
standards, manifest or format check is version-independent and one run of it
proves as much as sixteen, so a matrix whose every cell runs only
version-independent steps establishes that the files parse and nothing more.

The checks that exist are run, and what they printed is the ceiling of what this
surface is worth rather than its verdict: a green net proves the entries it
covers and nothing about the ones it has none for, so say which of the entries
above it leaves untouched. Establishing a missing one is
`typo3-extension-testing`'s workflow, and it names the default tool per check; a
review names the gap, routes it there, and changes nothing.

For each surface, compare checkout declarations, runtime evidence when
available, applicable architecture guidance, and versioned official
documentation. When an installation parser misses a dynamic PHP registration,
report that limitation and inspect the checkout rather than treating it as
absence.

## Severity

- Critical: exploitable security issue, destructive data loss, or release-wide
  outage with no practical containment.
- High: likely security boundary failure, data corruption, or a primary feature
  unusable in a supported setup.
- Medium: concrete incompatibility, unsafe rendering pattern, broken secondary
  behavior, missing regression coverage for risky code, or misleading operator
  documentation.
- Low: limited maintainability or convention issue with a concrete future cost.
- Recommendation: beneficial improvement without a verified violation.

Severity follows demonstrated consequence, not the number of files involved.
State missing evidence instead of inflating severity.

## Finding gate

A finding needs a concrete location, observed evidence, applicable rule or
documentation, consequence, remediation, and relevant project check. Otherwise
record it as a question or unverified category, not a violation.

A finding about a user-controlled value is a claim about a **sink** rather than
about a call site, and escaping and injection are the same claim about different
sinks: the tag or attribute the value is printed into, the statement it is
executed in, the header, path or process it ends up in. It is not established
until that sink is named and the code at it is read. Everything before it is the
path, and an escaping opt-out, a quoting helper or a ViewHelper that hands its
rendered children to another component is on the path rather than at the end of
it — where the sink protects the value on its own, that opt-out is what keeps it
from being encoded or quoted twice. Ask `typo3_hint_lookup` for the sinks of the
surface in hand, follow the value into the installed package that emits or
executes it, and where the path can be rendered or run, let the repository's own
test settle it. Otherwise report the finding as unverified and say which class
went unread. A security verdict is the expensive kind to get wrong: it has to be
disproved before it can be dismissed, which costs the maintainer exactly the
reading the review skipped.

## What a dropped candidate owes

An audit drops more than it reports, and dropping is the step nothing records.
Each candidate raised while reading and then let go is named with what let it go
— the setting that turned out to be the core default, the guard that turned out
to be there, the rule that turned out not to govern this package, the class that
was actually read. One sentence each, beside the findings.

A subsystem the package does not ship never enters this list. It is answered on
the coverage list as not applicable and costs the line it costs there; what
belongs here is what was entertained as a defect and then was not one.

The two directions are not held to the same bar. Raising a candidate costs a
reading; dropping one costs the maintainer a finding, silently, and nothing
afterwards says it happened. So a candidate is dropped only where something
concretely disproves it, and one that can be neither established nor disproved
is reported as open, with the reading that would settle it named beside it —
which is the finding gate's question rather than violation, read from the other
side.

Two dismissals go wrong reliably:

- Dropped because a comment, a docblock or an annotation says the code behaves
  that way. That is a sentence somebody wrote, not the behaviour — read the
  implementation it describes, and where the two disagree the disagreement is
  the finding.
- Dropped because it looks unlikely to happen. Unlikely is not disproved. What
  disproves a path is what makes it impossible: a guard that cannot be passed or
  a caller that cannot exist, at a line.

The gate above states this bar for a security verdict, which is where it is
steepest, and the bar is not that subject's: what makes a dismissal expensive is
that its cost falls on the maintainer rather than on the audit, and it does that
on every surface here.
