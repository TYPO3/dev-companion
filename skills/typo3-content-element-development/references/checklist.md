# Content element checklist

Go through these checks before you write code.

## Editor workflow

Answer these four questions:

1. Where does an editor create every item of the element?
2. Can an editor order the items without a move to another page?
3. What happens when somebody copies, localizes, hides or deletes the parent?
4. Is reuse across parents a requirement, or only possible?

For repeatable content, prefer an inline child table of the element's own. Take
another model only where reuse is a stated requirement.

## Files

- Keep the generic content element setup free of code for one element.
- Give one element its own `tt_content` override.
- Give every custom child table its own TCA file.
- Give one element one TypoScript file, in the project's directory for content
  elements.
- Name and group its template and its assets the same way.

## Assets

- Use the Fluid AssetCollector for the assets of one element.
- Give each asset a stable identifier, so several instances load it once.
- Include an asset on page level only when every page needs it.
- Pass translated labels for dynamic controls from Fluid to JavaScript.

## Persistence

For inline children, call `typo3_hint_lookup` with `id=tca-inline-children`. It
says what the relation and the child table must declare:

- the parent field;
- the order;
- the page types the rows may be on;
- the workspace capability.

Then verify what the hint leaves to the element:

- the language fields and the translation behaviour;
- the enable and delete behaviour;
- a deterministic query, limited to the current parent.

## Tests

Require:

- functional tests of parent and child persistence and of the frontend order;
- localization tests when the project is multilingual;
- browser tests for interactive or accessible behaviour;
- a backend workflow test when inline editing is critical to the business and
  the project has suitable end-to-end infrastructure.

A functional frontend request verifies the response the server renders. It can
assert that the AssetCollector references are present. It does not load those
assets, execute JavaScript, apply CSS, measure layout or move focus. It does not
expose the accessibility tree of the browser. Use a real browser test before you
claim that a carousel or another interactive element works in the frontend.
