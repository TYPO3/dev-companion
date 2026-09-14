---
id: D-KNW-100
title: 'How an extension extends a TypoScript condition is a subject this server owns'
date: 2026-08-18
status: confirmed
coveredBy:
  - HintsTest::whatAnExtensionMayBuildBehindAConditionProviderIsStated
---

# D-KNW-100 — How an extension extends a TypoScript condition is a subject this server owns

**Nothing below `knowledge/` says what an extension may build behind
`Configuration/ExpressionLanguage.php`.**

So a session reads the choice that decides every such fix out of core. A
variable object gives the dotted syntax and sees no condition variables, a
function provider sees them and cannot carry a dot. The feedback goes to the
queue at `normal`.

## Evidence

- Re-run on 2026-08-18 against the corpus as it is now. `bin/cli hints:probe`
  matched nothing on
  `"extension registers its own TypoScript condition ExpressionLanguage provider"`.
  It matched nothing on
  `"TypoScript condition provider ExpressionLanguage extension registers"` and
  on `"Configuration/ExpressionLanguage.php typoscript context provider"`. The
  first two routed to the `typoscript` domain and returned its 21 candidates as
  the index.
- The subject is absent rather than thin. `ExpressionLanguage` occurs nowhere
  below `knowledge/` or `skills/`, `knowledge/hints/` has no file for
  TypoScript, `knowledge/task-intents.json` carries no condition intent, and
  `knowledge/catalog/reference/entries.json` lists nothing under
  `typo3/sysext/core/Classes/ExpressionLanguage/`.
- One neighbour answers and stops short.
  `"condition provider constructor injection makeInstance public service Autoconfigure"`
  reaches `dependency-injection` and `di-service-not-found`. The second states
  the rule, an extension point resolved by class name needs a public service,
  with the page title providers as its worked case. It names neither the
  condition provider nor the attribute, and only a query that already carries
  the diagnosis reaches it.
- The two channels are exactly as reported, on `.checkouts/14.3`.
  `AbstractProvider` offers `getExpressionLanguageProviders()` and
  `getExpressionLanguageVariables()` and nothing else. `Resolver::__construct()`
  instantiates every provider through `GeneralUtility::makeInstance()` and
  merges both before a caller variable arrives.
  `Typo3ConditionFunctionsProvider` reads `$arguments['request']`,
  `$arguments['site']` and `$arguments['siteLanguage']` inside its evaluators.
- Core states the consequence and not the remedy. `AbstractProvider`'s docblock
  says runtime-related variables must be provided by the caller to the
  `Resolver` directly. That is why a variable object cannot fetch request state.
  Nothing writes down what an extension does instead.
- The public-service requirement is the `makeInstance` line.
  `GeneralUtility::makeInstance()` consults the container only where
  `$constructorArguments === [] && self::$container->has($className)`. So a
  private provider falls through to `new $class()` and fatals on its own
  constructor.
- That requirement is unbound and its form is not. `DefaultProvider` carries
  `#[Autoconfigure(public: true)]` on `.checkouts/13.4`, `14.3` and `main`, and
  on `12.4` a `Configuration/Services.yaml` entry makes the same class public
  instead.
- One task reported the subject twice.
  `feedback/2026-08-18-080532-nothing-says-what-is-reachable-at-typoscript.md`
  is the evaluation-time half, which variables the matcher hands over, where
  this one is the registration half. Both name a hint on TypoScript conditions
  as the gap.

## Decided

- Step 1a, taken on, and a todo rather than the spot. This entry read what the
  statement says about TYPO3 as evidence. The write is the curation, the version
  bindings and the test, which
  [`judging.rst`](../../documentation/records/judging.rst) puts on the todo's
  side whatever its size.
- `normal` rather than the `low` the card arrived at. The subject has no
  coverage at all, so nothing answers a caller who arrives on it.
- Not `high`. One session reported it, and the gap is statements rather than a
  capability.
- A hint curated on the condition provider rather than a sentence added to
  `di-service-not-found`. A caller who already suspects a container problem
  reaches that hint. The session that filed this arrived with
  `Configuration/ExpressionLanguage.php` and a condition that no longer matched.
- The public-service half stands there too, and `di-service-not-found` gains a
  neighbour line rather than the statement. A second hint curated on the same
  container words would compete with the one that already answers them.
- Neither archived nor trimmed. Nothing states any part of the mechanism today,
  and the one neighbour that answers covers a different extension point.
- The other feedback keeps its own card. A feedback gets one card and never a
  second, `R-FBK-014`. So a session reads what the two halves owe each other off
  the two files rather than settles it here.

## Assumed

- That the two halves make one hint rather than two. Both feedback came out of
  one task. Whether the next caller arrives on the registration of a provider or
  on what a condition can see is not something this run can know.
- That the statement holds unbound apart from the attribute. This run read both
  channels, the `Resolver` constructor and the `makeInstance` line on `14.3`
  alone, and compared only `DefaultProvider`'s registration across the four
  checkouts.
- That an event listener is the route an extension actually takes to feed a
  variable object. The feedback says so and `AbstractProvider`'s docblock agrees
  that the caller has to provide such a variable. Which event, and whether the
  core dispatches it before the condition match on every covered version, is
  unread here.

## Wrong if

- A covered version offers a provider a second way to reach the condition
  variables. A constructor argument the `Resolver` passes, a tag, an interface
  it checks. The two-channels statement would describe a subset and read as the
  whole.
- The hint lands and the next session with a condition provider reports that
  what it needed was which event feeds the variable object. The gap would have
  been the evaluation-time half, and the registration statement would not have
  closed it.
- A query that names `makeInstance` and a public service no longer reaches
  `di-service-not-found` once this hint exists. The boundary between the two
  would be wrong, and the public-service half belongs where it already is.
- The public-service requirement turns out not to bite an extension. Its own
  `Services.yaml` already registers its classes as public, or it ships none at
  all and `makeInstance` never consults the container. Half the entry would
  describe core's habit rather than a rule that holds an extension author.

## Confirmed on 2026-08-18

A hint fills the gap and the three measured queries answer with it first. The
two halves became two hints, and not because a test settled the assumption. Two
sessions worked both feedback on two branches at once. What makes that
survivable is that callers ask the two in different words.

The first **Wrong if** does not hold on any checkout. The interface declares two
getters, the resolver is byte-identical, and it instantiates each provider with
no argument at all. The third does not hold either. Where the requirement bites
answers the fourth. A provider with no dependencies never consults the
container. The moment it takes one, the default visibility is what makes the
lookup fall through, which is the case the report arrived with.

The **Assumed** about the attribute was too narrow, so the hint states the rule
and both forms with no bound.
