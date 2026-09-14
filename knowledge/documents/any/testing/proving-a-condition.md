---
description: >-
  How you prove a TypoScript condition verdict against an installation that runs: the marker only the guarded branch produces, the negative control that makes one result evidence, and what stands between two runs.
whenToUse: >-
  When you have to show that a TypoScript condition matched in the frontend, or stopped matching — a repair judged before and after, or a template swap that may never have fired. What a condition gets at evaluation time and how an extension registers one are hints instead.
hints:
  - typoscript-conditions
---

# Proving a TypoScript Condition Verdict

A verdict is a boolean nobody prints. The condition matcher records it. The
include tree keeps or drops the branch it guards. The response says nothing
about either. So you prove the verdict from what that branch did to the rendered
page. The proof is a marker you derive rather than guess.

## What Does Not Answer It

The backend's TypoScript module lists the conditions of a tree and evaluates
none of them. `IncludeTreeConditionAggregatorVisitor` collects the condition
tokens. `IncludeTreeConditionEnforcerVisitor` then applies the ones the editor
ticked, which the module data remembers per backend user. So the tree the module
renders is the one those ticks describe. A condition nobody ticked is inactive
there, whatever the frontend makes of it.

No other core surface prints a verdict. The request builds the list of them,
puts it into the page cache identifier, and renders it nowhere.

## The Marker Only the Branch Produces

A marker proves the verdict when the guarded branch is the only thing that can
produce it. So you derive one from a diff of the two branches. What the two
share is what a marker may not rest on.

Where the branch swaps a Fluid template (`page.10.templateName` from one name to
another), the two usually share the wrapper markup. Two templates under one
`templateRootPaths` normally use the same layout and the same partials. So
everything those render is identical on both sides, and only what the templates
themselves add differs. A container class from the template the condition
selects is in the output either way, and a find proves nothing. That is the
trap, because the wrapper is what the markup offers first.

Where the branch assigns a value in place, the marker is that value in the
output, under the same rule. Take the one the other branch cannot render.

## A Marker You Put There on Purpose

Where you may edit the TypoScript, stop the derivation. Copy the condition line
verbatim below the block under test, into the same setup file. Have it render
something the installation has nowhere else:

```typoscript
[blog.isPost()]
page.headerData.9999 = TEXT
page.headerData.9999.value = <meta name="x-verdict" content="isPost">
[END]
```

TYPO3 renders `page.headerData` into the `<head>`. So one request and one string
comparison settle the verdict, with nothing to discriminate. Copy the line
rather than retype it. TYPO3 evaluates an expression with a constant after
substitution, and a constant spelled differently is a different condition.
Delete the block when you have the answer. Check with `git status` that the file
is back as it was.

## Which URL You Request

`typo3 site:list` prints each site's identifier, its root page id and its base
URL. The base URL is what turns a page id into a request. The rest of the path
is the page's own `slug` column. So the pages table answers it in one query
through the installation's own client. That is `ddev mysql` where DDEV runs it.

Request it without a backend session. A preview with a login disables the page
cache and renders hidden records. So a browser tab with a session and a `curl`
are two different requests. Only one of them is what a visitor gets.

## The Negative Control

A page that carries the marker is also what a condition that matches everything
would produce. The evidence is the pair. The page the condition must match
carries the marker, and a page it must not match does not. Where the question is
a repair rather than a rule, the pair is the same URL before and after. The
marker is first absent and then present. One green run on its own establishes
nothing.

## What Stands Between Two Runs

The verdicts are part of the page cache identifier, as `constantConditionList`
and `setupConditionList`. Those map each condition expression to what it
evaluated to. TYPO3 computes them on every request, a fully cached one included,
because it cannot build the identifier without them. So a verdict that flips
lands on another identifier, and you need no flush to stop the old page.

What does go stale is the parsed TypoScript, and only where it comes from a
file. TYPO3 keys an `@import` target or an `include_static_file` set on the file
name. So an edited `.typoscript` file keeps its include tree. The probe above is
exactly that case. Two ways out:

- `&no_cache=1` on the request. It disables the page cache and the TypoScript
  cache together. TYPO3 hands the cache to the TypoScript factory only while it
  allows a cache. So TYPO3 rebuilds the include tree from the files. Such a
  request also writes no page cache entry, so it cannot poison the next one.
  TYPO3 ignores it where `FE/disableNoCacheParameter` is on. It is off as
  shipped, and `config/system/` is where an installation turns it on.
- `typo3 cache:flush --group=pages`. That is the step where that parameter is
  on. It is also the step where the run has to be the one a visitor gets.
