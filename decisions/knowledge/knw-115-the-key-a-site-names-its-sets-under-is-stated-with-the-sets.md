---
id: D-KNW-115
title: The key a site names its sets under is stated with the sets
date: 2026-08-24
status: open
coveredBy:
  - HintsTest::aComposerDependencyQuestionIsAnsweredByTheRepositoryHint
  - HintsTest::theKeyASiteNamesItsSetsUnderIsAnsweredBySiteSets
---

# D-KNW-115 — The key a site names its sets under is stated with the sets

**That a site's own `config.yaml` names its sets under `dependencies` stands in
`fresh-instance-seeding`, a hint about packages that seed. It belongs in
`site-sets`, where a site-configuration query lands.**

The session that reported names that key as the one thing it could not have
guessed and read the core constructor to find it. The corpus had the sentence
and the question does not reach it. What a site-configuration query returns is
`site-sets`, which names `dependencies` for the *set's* own `config.yaml`, the
other file of that name. So a reader who gets that far is one file off.

## Evidence

- Re-run on 2026-08-24. The feedback names no call, and says so. So the re-run
  is `bin/cli hints:probe` on the question it would have asked. "What in
  config/sites/<id>/config.yaml makes Site::isTypoScriptRoot() return true, and
  which YAML key becomes Site::getSets()?" returns `initial-content-references`,
  `project-configuration-files` and `environment-placeholders`. None of them is
  about a site's `config.yaml`.
- The statement is here. The fifth of `fresh-instance-seeding` says a set
  "reaches an installation only where a site's own config.yaml names it under
  dependencies". It ranks fifth on the query above, behind
  `dependency-injection` on text alone.
  `"site config.yaml dependencies key sets Site::getSets"` returns two hints of
  which it is neither.
- Its curated phrases are what keep it there: "fills a fresh instance", "nothing
  to look at after the install", "ext_tables_static+adt.sql", "setup wizard".
  None is a site-configuration phrase, so the sentence is reachable from the
  question about a package that fills an instance and from no other.
- `site-sets` is where a site-configuration query does land, with
  `site-set-settings`, on
  `"site sets config.yaml settings.yaml site configuration"`. Its first
  statement names `dependencies` as what a set's own `config.yaml` lists other
  sets under. One word, two files, and the corpus carries it for the one the
  caller did not ask about.
- The direction the session needed is in neither hint. A search of `knowledge/`
  for `dependencies` returns those two statements and nothing that names the
  site entity's `getSets()`. So no answer here crosses from the YAML key to the
  accessor.
- The claims about TYPO3 hold.
  `$this->sets = $configuration['dependencies'] ?? []` reads the same on
  `.checkouts/13.4` and `.checkouts/main`. So does `isTypoScriptRoot()` with its
  `$this->sets !== [] || $this->typoscript !== null || $this->tsConfig !== null`.
  `.checkouts/12.4` has neither, so the statement binds from 13. The constructor
  takes
  `?SiteSettings $settings = null, ?SiteTypoScript $typoscript = null, ?SiteTSconfig $tsConfig = null`
  and falls back to
  `SiteSettings::createFromSettingsTree($configuration['settings'] ?? [])`, as
  reported. `getSets()` is public and documented; `isTypoScriptRoot()` is
  `@internal` on both lines.
- The tool reads as what it is. `typo3_configuration_lookup` names
  `TYPO3_CONF_VARS` three times in its description and takes a path into it. So
  the session read the name right and skipped the tool right.
- There is no destination for the question. The `routing` block of
  `knowledge/server-scope.json` carries no entry for a site-configuration
  question. The `doesNotCover` entry that names one, "Deciding one site's
  configuration", hands the format to https://docs.typo3.org/. A session reads
  it through `typo3_server_scope`, a call this session did not make.

## Decided

- Step 2 of the ladder with a step 1a remainder, and queued rather than closed
  on the spot. The work writes a statement about TYPO3, which
  [`judging.rst`](../../documentation/records/judging.rst) puts on the todo's
  side of the line whatever its size.
- The site's key goes into `site-sets`, curated on the site's own file rather
  than on the set's. `fresh-instance-seeding` keeps its sentence. A package that
  ships sets alone seeds nothing, and the site's key is what makes that point.
  Whether it owes a pointer at the new statement is the todo's to decide against
  `D-KNW-087`.
- The key-to-accessor direction goes with it. `dependencies` reaches the site
  entity's `getSets()` and `settings` reaches its `getSettings()`. The entity's
  own TypoScript and page TSconfig come from the files beside `config.yaml`
  rather than from a key in it. That is the half the corpus states in neither
  direction. It is what makes a lexical search on "sets" reach the file that
  spells it "dependencies".
- `isTypoScriptRoot()` is not decided here. It is `@internal` on every covered
  line. How far the corpus follows internal API is a read the todo does rather
  than a judgement this run can make.
- The description clause the feedback asks for gets a refusal in the form it
  asks for it. What the session lacked was somewhere to go, not a disclaimer in
  a tool it had already skipped. Nobody who read the name and moved on reads a
  sentence about site configuration inside a `TYPO3_CONF_VARS` tool.
- No new `routing` entry either. The `doesNotCover` entry already sends a
  site-configuration question to `typo3_hint_lookup` with `id=site-sets`. The
  placement above is what makes that pointer land on the answer instead of one
  file away from it.
- `normal` rather than the `low` the card arrived at. One session, and it
  counted what the gap cost. Four reads of the core, three of them a wider
  window on the same code, which is the measure `D-FBK-027` sets.
- Not `high`. One session and one task shape, and the gap is a placement and one
  direction rather than a subject nobody has written.
- Neither archived nor trimmed. Nothing the session asked for is reachable from
  a site-configuration query today.

## Assumed

- That the session would have found the statement had it been in `site-sets`. It
  called nothing, so what a probe would have returned to it comes off what the
  probes return now.
- That `dependencies` is not what it would have searched for. The feedback says
  it would have searched "sets" and got nothing useful. That is why the
  statement has to carry both words rather than the key alone.
- That the site's `dependencies` and the set's are worth two statements. They
  are one word in two files, and a single sentence that covers both is what the
  corpus has today.

## Wrong if

- The statement lands in `site-sets` and a site-configuration query still
  returns `fresh-instance-seeding` first. The lever would have been that hint's
  own curation, and this is step 4.
- A session with a site-YAML question reads `typo3_configuration_lookup`'s
  description and files this report anyway. The clause was the lever and the
  refusal above is wrong.
- The reading finds the site key documented in the manual under wording a
  `typo3_documentation_lookup` query reaches. Then the `doesNotCover` entry's
  "take the configuration format from docs.typo3.org" was already the right
  answer. The gap is a route to it rather than a statement.
- A resolver turns out to read the sets off a site rather than the entity the
  caller holds. The mapping would rest on an accessor read out of its call path.
- The next session needs the key while it writes a core functional test, as this
  one did. It does not reach a hint about site sets from that task at all. The
  gap would be in the core testing documents rather than in the corpus.

## Since then

The statement is in, with the direction the corpus stated in neither half. A
site names its sets in its own configuration. Its TypoScript comes from the
files beside that one, not from a key in it. The entity reads the same on both
covered lines and the oldest has neither the key nor the accessor, so the
statements carry a bound. The fourth **Wrong if** did not happen.

What the internal marker buys is the consequence rather than the method. A site
that names a set renders without a template record. One with neither errors out.

The placement alone did not bring the question in reach. The domain did. A hint
filed under one language is no candidate for a query in another unless the task
spells out a curated phrase. What that risks is a query answered with this hint
in place of the one that was about it. That is why the key is a curated phrase
rather than the bare word.
