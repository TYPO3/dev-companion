---
id: D-KNW-029
title: 'A hint names the domains it is asked from'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::everyHintIsTaggedWithADomainSomeQuerySelects
  - HintsTest::theFileAHintSitsInDoesNotDecideWhatSelectsIt
---

# D-KNW-029 — A hint names the domains it is asked from

**Each hint declares its domains in a `domains` field, and the file it sits in
is where it lives rather than what selects it.**

The file name was three jobs at once: the shelf, the selector, and the heading
an answer prints. A hint in two domains could therefore not say so. It went into
`general.json`, whose domain every query selects, and the corpus grew a bucket
that answers everything.

## Evidence

- `bin/cli hints:coverage`: General holds 19 of 66 hints and supplies 39 of 62
  matches over the scenario prompts. 16 of 32 answers consist of it alone. That
  is what `D-KNW-001`'s **Wrong if** named, and it is a property of the shelf
  rather than of the hints. `content-elements` is PHP, Fluid and TypoScript at
  once and had no way to say it.
- The grain the file name imposed is uneven in the same direction. Backend CSS
  holds 19 hints of 115 words on average, one subject each. `php.json` holds 21
  for the whole framework at 338, and `general.json` 19 at 418.
  `datahandler-persistence` is one hint over eight statements that cover the
  datamap, relation resolution, record placement, the backend user, workspaces
  and the test obligation.
- Its `appliesTo` names `querybuilder`, `restriction`, `enablecolumns`,
  `hidden record` and `deleted record`, and not one of its statements is about a
  read of records. A grep for
  `removeAll|DefaultRestriction|enableFields|LanguageAspect| PageRepository|VersionState`
  over the whole corpus returns a single hit, and that one is about
  `excludeDoktypes` in a menu processor. The umbrella hid the gap because it
  carried the vocabulary for it.

## Decided

- The tag is the `Domains::` vocabulary — `php`, `fluid`, `typoscript`, `css`,
  `typescript`, `javascript` — and not the label an answer prints. The two are
  separate fields because they change for different reasons. A `.scss` path
  detects as `css`, while "Backend CSS" is a sentence that tells somebody whose
  conventions those are.
- Per entry, with no file-level default. A default would make the file name mean
  something again, which is the whole of what this removes.
- The first domain is the heading the hint prints under. A hint across domains
  answers from all of them and sits under one. Which one is a judgement its
  author makes when they write it first.
- `any` stays as a tag rather than dissolves into real domains in the same
  change. It is `general.json` renamed and it keeps the 63% share intact, so
  nothing about the answers moves while the mechanism does.
- This change alone is a freeze. `hints:coverage`, the domains, the scores, the
  order and the section headings for every hint title and every scenario prompt
  are byte-identical before and after. A split of a hint and a new tag for `any`
  are the next steps and are the ones that move an answer.

## Assumed

- One primary domain is enough for the heading. A hint that would want two
  headings is one hint with two jobs. That is the thing under correction rather
  than a case to support.
- The domain named per entry rather than per file survives a corpus filed by
  subject. Six files times one tag is a defensible default today; forty subject
  files with a mixed set of tags is what it is for.

## Wrong if

- The `any` share does not fall once the corpus sits by subject. General renamed
  and not dissolved would leave every query with the same nineteen hints as its
  answer, and the mechanism would have bought nothing.
- A hint ends up with every domain there is as its tags, so that a query finds
  it. That is `any` in a longer form, and it says the tag serves as a reach
  control rather than as a statement about the subject.
- Somebody re-derives the domain from the file name because the two still agree
  everywhere except `general.json`.

## Since then

The first **Wrong if** fired, and the split is what showed it. The corpus went
from 66 hints to 120 on 2026-08-03 and `general.json` grew with it, from 19
entries to 38. Every one of them still carried `any`, because a split inherits
the tag of its origin. The share does not move. General holds 38 of 120 hints
and supplies 37 of 59 matches over the scenario prompts, 63% as before. 17 of 31
answers consist of it alone.

So a corpus by subject does not dissolve the bucket on its own. What remains is
the new tags the entry's own third step names, and it is now the only thing that
moves that number. `sitepackage-layout` lost 450 words and still outranked
`project-extension-tests` for "Set up tests for our site package extension". A
hint every query selects is not a property of its length.

## Since then

The third step happened on the same day. `D-KNW-033` names the domains each of
the 38 `any` hints really answers from, and the share is 0 of 120. What kept its
deferral was a behaviour change, and it turned out to be three answers of 41.
One of them is a commit-message review that no longer gets the frontend records
hint.
