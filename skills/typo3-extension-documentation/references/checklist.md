# Documentation decision checklist

Read this before you choose the documentation surface, and again before you
finish.

## Audience and surface

- Identify the reader: evaluator, integrator or administrator, editor, extension
  developer, or maintainer.
- Put the purpose and the essential setup in the README. Use the existing
  Documentation/ manual for durable configuration, workflows, API, and migration
  material.
- Separate installation and administration from editor workflows and developer
  extension points.
- Extend the repository's existing structure and markup. Do not create a second
  canonical manual.

## Evidence and conflicts

- The checkout is the authority for what this package implements: registration,
  keys, defaults, paths, examples, and supported behavior.
- The installation lookups are the authority for effective registrations and
  runtime state. A parser miss is not proof of absence. If dynamic PHP code and
  parsed output disagree, describe the parser limitation and verify the code
  path.
- Composer constraints and versioned official documentation own compatibility
  and external TYPO3 API claims.
- Tests may demonstrate behavior. They do not turn internal implementation
  details into promised public API.

## Secret hygiene

- Never copy passwords, tokens, private keys, personal addresses, internal
  hosts, or machine-specific paths into examples.
- Replace required credentials with named placeholders. Explain how the reader
  supplies them securely.
- When existing documentation contains plausible credentials or personal data,
  flag it explicitly and do not reproduce it.

## Completion gate

Stop the evidence search when every requested audience and surface has:

1. an authoritative implementation source;
2. version evidence for external TYPO3 claims;
3. a verified example or an explicit unverified marker;
4. validated paths, identifiers, and links; and
5. no unresolved contradiction that changes user instructions.

Report the unknowns that remain instead of a broad search. Run only declared
validation commands. Finish with the changed files, the checks, and the
unverified behavior.
