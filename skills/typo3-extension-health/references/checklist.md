# Conformance audit checklist

Read the relevant sections for a scoped review. Read all sections for a full
extension audit. Either way, write the surface list below whole. Report a
surface the request left out as not requested rather than drop it. The absence
of an optional subsystem is not a defect.

## Audit surfaces

- Package: identity, Composer constraints, autoloading, extension metadata, and
  the supported TYPO3 and PHP range.
- Registration and runtime: services, events, middleware, plugins, content
  elements, backend modules, routes, permissions, and effective configuration.
- Persistence: TCA, schema, relations, DataHandler use, repositories, fixtures,
  and upgrade paths.
- Rendering: site sets, TypoScript, TSconfig, Fluid roots and namespaces,
  templates, translations, and public assets.
- Security: authorization boundaries, state-changing requests, output-context
  escaping, raw rendering, query construction, user-controlled attributes, URLs
  and paths, and secret exposure. Every one of them is a value and a sink. The
  finding gate below says how you establish one.
- Quality: the test suite and the supported TYPO3 versions it runs on, the check
  layer, documentation, deprecations, and upgrade readiness. For documentation,
  `typo3_hint_lookup` with `id=extension-documentation` says what a manual
  consists of and that it ships with the package.
- Pinned versions: the Node, the actions under `.github/workflows/`, the
  container configuration and the declared dependencies. Read each against the
  release current on the day rather than against the file. One behind it is a
  finding that carries the raise. What speaks against the raise is a bound the
  package declares. The finding then names the newest release that bound allows.

## The check layer

The commands a repository declares are where you read this surface, never what
it is. Measure them against what a complete layer covers. Name each entry by
what the check establishes rather than by the tool behind it:

- **Syntax** — every shipped PHP file parses on every PHP version the package
  declares.
- **Static analysis** — types, unreachable code, and calls that cannot succeed.
  It runs over the paths the package owns, at a level the project can hold
  today.
- **Coding standards** — the TYPO3 coding guidelines as the project applies
  them. The editor-configuration and declared-PHP-range checks run beside them.
- **Manifests and dependencies** — the manifest validates and carries no open
  advisory. It agrees with what the package declares about itself elsewhere.
- **Shipped configuration and data** — the XLIFF, YAML and TypoScript the
  package ships are well-formed. Fluid templates have no established linter.

  The tests that render them prove them.
- **Shipped frontend assets** — the JavaScript, TypeScript and CSS sources the
  repository maintains. Never the bundle a build step produced from them.

What the package ships decides which of them apply. A check whose subject it
does not ship is absent for a reason. One whose subject it ships and no command
covers is a gap in the layer rather than an optional subsystem. That absence is
the finding. Ask the same of where each one runs. Syntax and analysis depend on
the PHP and TYPO3 combination and belong in a matrix.

A standards, manifest or format check is version-independent, and one run of it
proves as much as sixteen. So a matrix whose every cell runs only
version-independent steps establishes that the files parse and nothing more.

Run the checks that exist. What they printed is the ceiling of what this surface
is worth rather than its verdict. A green net proves the entries it covers and
nothing about the ones it has none for.

So say which of the entries above it leaves untouched. To establish a missing
one is `typo3-extension-testing`'s workflow, and it names the default tool per
check. A review names the gap, routes it there, and changes nothing.

For each surface, compare four sources. Those are the checkout declarations, the
runtime evidence when available, the architecture guidance, and the versioned
official documentation. When an installation parser misses a dynamic PHP
registration, report that limitation and inspect the checkout. Do not treat the
miss as absence.

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

Severity follows the demonstrated consequence, not the number of files involved.
State missing evidence instead of inflated severity.

## Finding gate

A finding needs a concrete location, observed evidence, and the applicable rule
or documentation. It needs the consequence, the remediation, and the relevant
project check. Otherwise record it as a question or an unverified category, not
a violation.

A finding about a user-controlled value is a claim about a **sink** rather than
about a call site. Escaping and injection are the same claim about different
sinks. A sink is the tag or attribute that prints the value, or the statement
that executes it. It is the header, path or process the value ends up in.

The claim stands only once you name that sink and read the code at it.
Everything before it is the path. An escaping opt-out or a quoting helper is on
the path, not at its end. So is a ViewHelper that hands its children to another
component. Where the sink protects the value on its own, that opt-out keeps the
value from a second encoding or quoting.

Ask `typo3_hint_lookup` for the sinks of the surface in hand. Follow the value
into the installed package that emits or executes it. Where you can render or
run the path, let the repository's own test settle it. Otherwise report the
finding as unverified and say which class you did not read.

A security verdict is the expensive kind to get wrong. Disprove it before you
dismiss it. A wrong dismissal costs the maintainer exactly the reading the
review skipped.

## What a dropped candidate owes

An audit drops more than it reports, and nothing records the drop. Name each
candidate you raised while you read and then let go, with what let it go. That
is the setting that was the core default after all, or the guard that was there.
It is the rule that does not govern this package, or the class you read. One
sentence each, beside the findings.

A subsystem the package does not ship never enters this list. Answer it on the
coverage list as not applicable, where it costs one line. What belongs here is
what you entertained as a defect and then was not one.

The two directions do not meet the same bar. To raise a candidate costs a
reading. To drop one costs the maintainer a finding, silently, and nothing
afterwards says it happened. So drop a candidate only where something concretely
disproves it. Report one you can neither establish nor disprove as open, with
the reading that would settle it named beside it. That is the finding gate's
question rather than violation, read from the other side.

Two dismissals go wrong reliably:

- Dropped because a comment, a docblock or an annotation says the code behaves
  that way. That is a sentence somebody wrote, not the behaviour. Read the
  implementation it describes. Where the two disagree, the disagreement is the
  finding.
- Dropped because it looks unlikely to happen. Unlikely is not disproved. What
  disproves a path is what makes it impossible. That is a guard nobody can pass
  or a caller that cannot exist, at a line.

The gate above states this bar for a security verdict, which is where it is
steepest. The bar is not that subject's. What makes a dismissal expensive is
that its cost falls on the maintainer rather than on the audit. It does that on
every surface here.
