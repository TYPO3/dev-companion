# Static analysis and coding standards guidance

Read this after you choose the static-quality layer. It covers analysis, coding
standards, and the lint or normalisation steps that run beside them. The
checkout, the package's declared TYPO3 and PHP range, and versioned
documentation decide the concrete facts. Those are package versions, rule sets,
configuration contents, and commands.

## Verify what is already there

1. Inspect the package manifests, the lock file, the installed analysers and
   fixers, their configuration and rule sets, the existing baselines, the
   Composer scripts, the development environment, and CI before you change any
   of them. Half an infrastructure is the ordinary case. That is a fixer without
   an analyser, a lint step without either, a configuration nothing calls.
2. Run every check that already exists, unchanged, and record its output. You
   measure every later claim against that run. A first analyser report on a
   project that never ran one is a list of findings rather than a regression.
3. Separate a missing executable, an unreadable configuration, and a real
   finding. Only the third says anything about the code.
4. Read what the existing configuration excludes on purpose before you widen it.
   A generated directory, a vendored library, or a fixture tree of broken code
   is a decision, not an oversight.
5. For a review-only request this is the whole workflow. Report what is missing
   or unenforced and change nothing.

## What a complete surface covers

This is the expectation the checkout is measured against. It names each check by
what it establishes rather than by the tool behind it. What the package ships
decides which of them apply. A check whose subject the extension does not ship
is absent for a reason rather than missing.

- **Syntax** — every shipped PHP file parses on every PHP version the package
  declares. Run `php -l` through a lint runner such as `overtrue/phplint` or
  `php-parallel-lint/php-parallel-lint`, so one command covers the tree.
- **Static analysis** — types, unreachable code, and calls that cannot succeed.
  Run `phpstan/phpstan` against the extension's own paths, which is what the
  core runs on itself. `GeneralUtility::makeInstance()` and its neighbours carry
  `@template` annotations. So the analyser gets its types from the installed
  core rather than from a TYPO3-specific analyser extension.

  Beside it, `phpstan/extension-installer` wires up whatever extensions the
  project installed. `phpstan/phpstan-phpunit` belongs beside a PHPUnit suite.
  `bnf/phpstan-psr-container` types the container's `get()` calls, and
  `phpstan/phpstan-deprecation-rules` reports use of deprecated API. Establish
  that a TYPO3-specific analyser extension is still maintained before adding
  one. Several are not, and an abandoned extension on a current core produces
  false findings instead of types. `vimeo/psalm` is the alternative where a
  project already runs it.

  Which packages to require is the whole of what this page decides about the
  analyser. What goes into its configuration is not. `typo3_hint_lookup` with
  `id=extension-static-analysis` answers where the file belongs, which include
  it carries, and the constants an extension's analysis never sees. It answers
  which manifest you exclude rather than fix, the cache directory, the level,
  and what a baseline is for. It reads that off the packages that configure
  themselves this way. So ask it rather than recall a configuration from another
  project.
- **Coding standards** — the TYPO3 coding guidelines as the project applies
  them. Run `friendsofphp/php-cs-fixer` with the `typo3/coding-standards` rule
  set, which also owns the file header the guidelines require. Run
  `editorconfig-checker` where the repository ships an `.editorconfig`. Run
  `squizlabs/php_codesniffer` with `phpcompatibility/php-compatibility` where
  you have to prove the declared PHP range without a matrix.
- **Manifests and dependencies** — `composer validate` on the extension's own
  manifest. It also checks the manifest against what the extension declares
  about itself elsewhere. `composer audit` for advisories against what it
  requires. `ergebnis/composer-normalize` where the project keeps its manifest
  normalised.
- **Shipped configuration and data** — the files the package ships. The XLIFF
  linter `symfony/translation` ships validates against the schema rather than
  only parses the XML. A TYPO3 project usually has it installed. A YAML lint
  such as `j13k/yaml-lint` or the framework's own `lint:yaml` covers
  configuration. `helmich/typo3-typoscript-lint` covers TypoScript.

  Two of them have to learn about this project before their verdict is worth
  anything. `typo3_hint_lookup` with `id=language-files` says what the XLIFF
  linter needs before it accepts a file named the way TYPO3 names one. A linter
  ships a configuration of its own and merges yours over it. So what it reports
  and what it advises are its defaults rather than this project's conventions.
  Advice written for an older TYPO3 is still advice the tool gives today.

  Fluid templates have no established linter. The functional tests that render
  them prove them. Say so rather than invent a check for them.
- **Shipped frontend assets** — where the package ships JavaScript, TypeScript
  or CSS. Run `eslint` for the scripts, with `@typescript-eslint` where the
  sources are TypeScript. Run `stylelint` for the stylesheets, with
  `stylelint-scss` and `stylelint-order` where Sass compiles. A formatter such
  as `prettier` goes on the fix side and never into the check. Lint the sources
  the repository maintains rather than the compiled bundle below
  `Resources/Public/`. A finding in generated output is a finding about the
  build step that produced it.

  Declare each of them as a script in the package's own `package.json`. Then the
  same one command exists locally and in CI. Take the Node version from what the
  project declares rather than from the machine that happens to run it. Run each
  one over the files it guards before you report the entry as covered. A linter
  that finds nothing in the only sources the package ships is a gap dressed as
  coverage. That is the standard this page already applies to a matrix whose
  cells run only version-independent steps.

Read the names as the default per check where the checkout covers it with
nothing, never as a replacement for what it already runs. A project with another
analyser, another lint runner or its own wrapper around one has answered that
question already. A second tool for the same check is a second answer to
maintain. Versions come from the solver and from what the package's range
allows, never from this list.

`rector/rector` with `ssch/typo3-rector` reads like a check in its dry-run mode
and is not one. It proposes migrations, which belong to an upgrade task rather
than to the surface CI has to keep green.

Then say which axis each one has, because that decides where it runs. Syntax and
analysis depend on the PHP and TYPO3 combination and belong in the matrix. A
standards, manifest or format check is version-independent, and one run of it
proves as much as sixteen. A matrix whose every cell runs only
version-independent steps proves that the files parse and nothing more. Say so,
with what it costs and what it does not buy.

## Resolve the dependencies

1. Form candidates from the package's own declared TYPO3 and PHP range. Never
   form them from another project's manifest or from the core's build tooling.
2. Let Composer resolve the newest candidate that intersects those constraints
   together with what is already installed. Do not write a concrete constraint
   until the solver has accepted it.
3. Take a rule set from a package the project already requires before you add
   another one. Every added package is a further thing that has to stay current
   across the whole declared range. An abandoned one stops doing that.
4. Keep every one of them in the development requirements.
5. Where the solver refuses one, read what it refused rather than which TYPO3
   version the project runs. A check tool has to meet what this project
   resolved. A package nothing here pins can sit a major ahead of what the
   framework asks for. So the same tool goes into one project and not into the
   next.

   Where it cannot meet it, the tool gets an installation of its own: a second
   manifest below the build directory with its own vendor directory, and the
   project-owned command calls that binary. The check exists either way. What
   moves is where the tool's own dependencies live.

## Establish one command per check

1. Give each check one stable project-owned command. Declare it where the
   project already declares its commands. Name it for what it checks rather than
   for the binary behind it.
2. Keep the check and the fix apart. A check reports and fails; a fix writes.
   One command that rewrites the tree on the way to its verdict is not a check.
   CI cannot call it.
3. Keep automatic formatting inside the first-party paths the project intends it
   to touch. Vendored code, generated output, other packages' files, and
   fixtures that assert exact bytes stay outside the fixer's paths. Confirm that
   with the fixer's own dry run before the first write and with the tree's
   status afterwards.
4. Point analysis at the paths the extension owns, at the level the project can
   hold today. A level chosen for the eventual state produces a wall of findings
   nobody works off.
5. Run each command locally until it passes. Then make CI call that same
   command. A CI step assembled apart proves something the developer cannot
   reproduce.

## Work the findings off

- Fix the finding. A baseline records what was already there on the day somebody
  wrote it. It never receives an error the change in hand introduced.
- Where a baseline exists, read it as a work list with an owner and a horizon.
  The horizon is usually the release that drops the oldest supported version.
  Say which of its entries the current change retires.
- A suppression that is correct while the package supports an older version is
  evidence rather than debt. Establish what it is there for, and what would
  remove it, before you propose that it goes.
- Keep a formatting pass in its own commit, apart from a behavioural change.
  Nobody can review a diff that mixes both.
- Where you introduce a check onto a repository that does not yet pass it, the
  conformance commits come first. The commit that adds the check comes last.
  Then no commit fails the check it introduces. The obvious split does the
  opposite. Tooling first leaves the new check on a tree the conformance pass
  has not reached yet. Verify it: run the check at the new HEAD.
- Report a finding in code the task does not touch. Do not fix it quietly beside
  the requested work.

## Prove it

1. Run each check on the narrowest scope it supports, then on its full target.
2. Run the fix command, run the check again, and inspect the tree for files
   outside the intended scope.
3. Run the CI-equivalent commands after the local commands pass.
4. Report the exact commands, their results, and the files the fixer changed.
   Report every check you did not run, with the reason.
