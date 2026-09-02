# Content element checklist

Apply these gates before writing code.

## Editor workflow

Answer:

1. Where does an editor create every owned item?
2. Can items be ordered without navigating away?
3. What happens when the parent is copied, localized, hidden or deleted?
4. Is reuse across parents required or merely possible?

Prefer an owned inline child table for repeatable content unless reuse is a
stated requirement.

## File ownership

- Keep generic content-element setup free of element-specific implementation.
- Give one element its own `tt_content` override.
- Give every custom child table its own TCA file.
- Give one element one TypoScript file below the project's established
  content-element directory.
- Keep its template and element-only assets named and grouped consistently.

## Assets

- Use the Fluid AssetCollector for element-only assets.
- Give assets stable identifiers so multiple instances load them once.
- Keep a page-level include only when every page needs the asset.
- Pass translated dynamic control labels from Fluid to JavaScript.

## Persistence

For inline children, ask `typo3_hint_lookup` with `id=tca-inline-children` for
what the relation and the child table have to declare — the parent field, the
ordering, the page types the rows may sit on and the workspace capability. Then
verify what it leaves to the element:

- language fields and translation behavior;
- enable/delete behavior;
- deterministic query constrained to the current parent.

## Tests

Require:

- functional coverage of parent/child persistence and frontend ordering;
- localization coverage when the project is multilingual;
- browser coverage for interactive or accessible behavior;
- a backend workflow test when inline editing is business-critical and the
  project has suitable E2E infrastructure.

A functional frontend request verifies the server-rendered response and can
assert that AssetCollector references are present. It does not load those
assets, execute JavaScript, apply CSS, measure layout, move focus or expose the
browser accessibility tree. Use a real browser test before claiming that a
carousel or another interactive element works in the frontend.
