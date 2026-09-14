---
id: D-SKL-027
title: A draft declares itself under this server's own metadata key
date: 2026-08-08
status: revoked
revokedBy: D-SKL-087
coveredBy: []
---

# D-SKL-027 — A draft declares itself under this server's own metadata key

**A skill that must stay out of a publication declares it as
`metadata: {typo3-dev-companion-status: draft}`. Installer::draft() reads that
with a YAML parser.**

It was a top-level `status: draft`, which is not a field the standard defines.
So the one file that must stay out was the one file a conformant reader refuses.
The line that held it back was the reason.

## Evidence

- The specification defines six frontmatter fields and no others — `name` and
  `description` required, `license`, `compatibility`, `metadata` and
  `allowed-tools` optional. Read on agentskills.io on 2026-08-08.
- The reference validator treats that set as closed. `ALLOWED_FIELDS` in
  `skills-ref/src/skills_ref/validator.py` is exactly those six, and
  `_validate_metadata_fields()` returns "Unexpected fields in frontmatter" for
  anything else. It is an error rather than a warning, so a run over a draft
  fails rather than reporting.
- `metadata` is where the standard puts what it does not define. "A map from
  string keys to string values", "Clients can use this to store additional
  properties not defined by the Agent Skills spec". The validator does not
  police the keys inside it.
- The same paragraph asks for key names "reasonably unique to avoid accidental
  conflicts", which the card that proposed this move did not carry. A plain
  `status` under `metadata` is what that sentence is about.
- The pattern this replaced read `^status:[ \t]*draft[ \t]*$`, which agrees with
  a parser on the one line it exists for. Measured against the reader now in
  place. A quoted value and an inline map are the same declaration to every
  client and were not to that pattern.

## Decided

- The key is `typo3-dev-companion-status`, this server's own name, because the
  file travels. A draft published under `--drafts` sits in somebody else's
  project where other tools write the same map. A generic `status` there is the
  conflict the standard names.
- The reader is `Yaml::parse` over the block rather than a second pattern, so
  what the installer sees and what a client sees are one read. `symfony/yaml` is
  already a production dependency.
- Front matter no parser can read is not a declaration. `draft()` returns false
  rather than throws, because the publication decision is not the place to raise
  a parse error, and `everyFrontMatterFieldIsOneTheStandardDefines` is.
- The same move holds the field set closed. `draft` was the only key ever
  outside it. It got in beside an assertion that read one field out of the block
  and let every other one through.

## Assumed

- That the reference validator is what a client validates with, or that clients
  implement the same closed set. Nothing here measured an actual client's
  loader. This entry read the specification and the validator the specification
  names.
- That `metadata` stays a free map in later revisions of the standard. It is the
  field defined as the client's, so a revision closing it would be a different
  standard.

## Wrong if

- A client refuses or ignores a skill over the `metadata` key, which would show
  as a draft that installs under `--drafts` and never loads.
- Another tool writes `typo3-dev-companion-status` into a published skill's
  metadata. That is the collision the namespaced key exists to prevent, and it
  would make a published skill read as a draft on the next `update`.
- A later revision of the standard defines `status` itself, which would make the
  short form correct and this one a private duplicate of it.

## Revoked on 2026-09-01

No file ever declared the key after the two drafts of 2026-08-05 went out a
fortnight later. The installer, the entrypoint, the record and the digest read
the declaration for the whole of that time. `D-SKL-087` publishes the directory
and holds the closed field set this decided in the same move.
