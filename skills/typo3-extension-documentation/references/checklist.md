# Documentation decision checklist

Read this before you choose where the documentation goes, and again before you
finish.

## Reader and file

- Name the reader: an evaluator, an integrator or administrator, an editor, an
  extension developer, or a maintainer.

- Put the purpose and the essential setup in the README. Put configuration,
  workflows, API and migration material in the existing `Documentation/` manual,
  because it lasts longer.

- Keep installation and administration apart from what editors do and from the
  extension points for developers.

- Extend the structure and the markup the repository has. Do not create a second
  main manual.

## Which source decides

- **The checkout** decides what this package implements: registration, keys,
  defaults, paths, examples and supported behaviour.

- **The installation lookups** decide what is registered and active at runtime.
  A parser that finds nothing does not prove that nothing is there. Where the
  PHP code builds something dynamically and the parsed output disagrees, say
  that the parser has this limit. Then verify the code path.

- **The Composer constraints and the versioned official documentation** decide
  compatibility and every claim about a TYPO3 API.

- **Tests** can show behaviour. They do not make an internal detail a public API
  the package promises.

## Secrets and personal data

- Never copy passwords, tokens, private keys, personal addresses, internal hosts
  or machine-specific paths into an example.

- Replace a credential the example needs with a named placeholder. Say how the
  reader supplies it safely.

- Where the documentation already contains a plausible credential or personal
  data, say so in the answer. Do not repeat it.

## Before you finish

Stop the search for evidence when every reader and every file you were asked for
has:

1. a source in the code for what the page says;
2. version evidence for every claim about TYPO3;
3. an example you verified, or a clear mark that it is not verified;
4. paths, identifiers and links you checked; and
5. no open contradiction that changes what the reader must do.

Report what is still unknown instead of a wider search. Run only the checks the
project declares. End with the changed files, the checks you ran and the
behaviour you could not verify.
