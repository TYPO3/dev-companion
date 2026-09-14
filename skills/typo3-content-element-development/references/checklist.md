# Content element checklist

Apply these gates before you write code.

## Editor workflow

Answer:

1. Where does an editor create every owned item?
2. Can an editor order items without a move to another page?
3. What happens when somebody copies, localizes, hides or deletes the parent?
4. Is reuse across parents a requirement or merely possible?

Prefer an owned inline child table for repeatable content unless reuse is a
stated requirement.

## File ownership

- Keep the generic content-element setup free of element-specific
  implementation.
- Give one element its own `tt_content` override.
- Give every custom child table its own TCA file.
- Give one element one TypoScript file below the project's established
  content-element directory.
- Keep its template and element-only assets named and grouped consistently.

## Assets

- Use the Fluid AssetCollector for element-only assets.
- Give assets stable identifiers, so several instances load them once.
- Keep a page-level include only when every page needs the asset.
- Pass translated dynamic control labels from Fluid to JavaScript.

## Persistence

For inline children, ask `typo3_hint_lookup` with `id=tca-inline-children` for
what the relation and the child table have to declare. That is the parent field,
the order, the page types the rows may sit on and the workspace capability. Then
verify what it leaves to the element:

- language fields and translation behavior;
- enable and delete behavior;
- a deterministic query constrained to the current parent.

## Tests

Require:

- functional coverage of parent and child persistence and frontend order;
- localization coverage when the project is multilingual;
- browser coverage for interactive or accessible behavior;
- a backend workflow test when inline editing is business-critical and the
  project has suitable E2E infrastructure.

A functional frontend request verifies the server-rendered response. It can
assert that AssetCollector references are present. It does not load those
assets, execute JavaScript, apply CSS, measure layout, move focus or expose the
browser accessibility tree. Use a real browser test before you claim that a
carousel or another interactive element works in the frontend.
