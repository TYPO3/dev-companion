---
id: D-KNW-077
title: 'The TypeScript style hint carries what cannot be guessed'
date: 2026-08-14
status: open
---

# D-KNW-077 — The TypeScript style hint carries what cannot be guessed

**`backend-typescript` names `Build/eslint.config.mjs` as what decides the
core's TypeScript style and `-s lintTypescript` as what settles a question about
it.** It carries three rules a reader cannot guess, and the file stays the list.

The hint currently ends at "Prefer existing TYPO3 backend imports, event
patterns, and custom element conventions used nearby". That is an instruction to
imitate the neighbours in a codebase where two rules are off on purpose.

## Evidence

- `feedback/2026-08-13-215716`. Its query re-run on 2026-08-14,
  `bin/cli hints:probe "backend TypeScript ESLint code style conventions building JavaScript modules"`,
  still reaches `backend-typescript` and `backend-ui`. Neither names a rule, a
  configuration file or a check. Step 1a of the ladder: a search for `eslint`
  over `knowledge/` and `skills/` reaches one file,
  `skills/typo3-extension-testing/references/static-quality.md`, which answers
  the extension audience about an extension's own setup.
- The server delivers half of it already. `knowledge/test-suite-hints.json`
  carries `lintTypescript` and `typo3_test_run_guide` hands it over. The session
  that reported ran it and it passed. What is nowhere in the corpus is where the
  style is *decided*, so a question about a single rule has no cheaper answer
  than reading the config.
- The corpus already names a checker where the work passes rather than only in
  the test guide: `css-source-build-boundaries` says to use `lintScss`. The
  TypeScript hint beside it says neither.
- Verified in `.checkouts` on 2026-08-14. `Build/eslint.config.mjs` exists on
  `12.4`, `13.4`, `14.3` and `main`, and all four carry
  `"@typescript-eslint/member-ordering": "error"` with no options object,
  `"@typescript-eslint/no-explicit-any": "off"` and
  `"@typescript-eslint/no-inferrable-types": "off"`. The statement is unbound
  over the covered majors.
- The cost the session counted was a review rather than a build. To decide
  whether absent spaces after commas and two blank lines at the end were lint
  failures, it had to read the config. There `comma-spacing` and
  `no-multiple-empty-lines` turn out to be unset, and the finding moved from
  blocker to cosmetic.

## Decided

- Taken on, as sentences in `backend-typescript` rather than as a rule corpus.
  The list is the file. A copy of it is wrong on the next config commit and the
  checkout is always right, which is
  [`D-KNW-064`](knw-064-the-disabled-assertions-a-core-checkout-carries-are-a-grep.md)'s
  shape. The step goes where the work is, and what it names is the file to read.
- The hint carries three rules, because they are what a reader gets wrong on
  their own. The config enforces `member-ordering`, and `no-explicit-any` and
  `no-inferrable-types` are off on purpose. The last two are what makes the
  imitation of the neighbours dangerous. Code the config permits reads as code
  another TypeScript project would fix, and the fix is a diff nobody asked for.
- No tool. The caller reads one file from its own checkout, which is what
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
  excludes. The round trips are one, and the pointer is what makes it one.
- No entry in `knowledge/catalog/reference/entries.json` beside
  `Build/php-cs-fixer`. That catalog holds what somebody reads to imitate, and
  the PHP one is there because a project adopts the core's rule set. Nobody
  adopts the core's ESLint configuration outside the core. So this is a pointer
  for a core patch and belongs in the hint that a core patch already reaches.

## Assumed

- That the three rules are the ones that cost sessions time. One session said
  so, from two situations in one run — writing a static method and reviewing
  somebody else's fixture. No second feedback names TypeScript style.
- That naming the config file is enough for the questions the list would have
  answered. The session's own suggestion says so, and it is the one that paid
  the cost.

## Wrong if

- A session reports reading `Build/eslint.config.mjs` by hand after the sentence
  lands, for a rule that is on. Then three carried rules were the wrong three,
  and the answer is the list or a lookup over it after all.
- One of the three moves on a maintained branch while the hint states it for all
  of them. Then the sentence owes a `since`, and this entry is the reason nobody
  looked.
- A session reports that it took "conventions used nearby" as the authority with
  the pointer already in the same hint. Then this is delivery rather than
  wording, and the rule belongs where the task passes instead.

## Since then

The sentence and the three rules are in `backend-typescript`, and a session read
the second **Wrong if** rather than waited on it. `Build/eslint.config.mjs` on
all four covered branches carries the same three: `no-inferrable-types` off,
`no-explicit-any` off, `member-ordering` at `error`. That is 12.4 at lines 53,
57 and 60, and 13.4, 14.3 and `main` at 51, 55 and 58. So the hint states them
unbound and that is what the branches say. Nobody owes a `since` today.

The other two need a session. Nothing since 2026-08-14 reports a read of the
config by hand for a rule that is on. Nothing reports the neighbour file taken
as the authority with the pointer in the same hint.
