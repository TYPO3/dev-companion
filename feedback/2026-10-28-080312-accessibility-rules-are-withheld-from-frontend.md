---
date: 2026-10-28T08:03:12+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3-sitepackage-development
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# accessibility rules are withheld from frontend work, and none states the focus ring's own contrast

## Observation

Task: judge a sitepackage site on TYPO3 14.3 for disabled users, after building it.

The site declared one focus token, #1a1a1a, and used it in a :focus-visible rule. Against the surfaces that ring lands on it reaches 1.10 on the utility bar, 1.10 on the footer, 1.57 on the hero and 2.28 on the main navigation. WCAG 2.2 asks for 3:1. The ring is invisible where most of the links are.

Two things in the corpus bear on that, and neither reached me.

css-accessibility-states says that colour contrast must satisfy WCAG, and that keyboard focus must be visible with :focus-visible. It carries scope core and sits in backend-css.json. A task naming the frontend has it withheld, and the server says why: Backend CSS describes the backend interface and would be inverted advice for a website. That is right for the --typo3-* properties and the backend colour schemes. It is not right for those two statements, which are WCAG and hold identically on both sides.

The withheld rules would not have caught this anyway. "Focus must be visible with :focus-visible" is satisfied by a rule that renders an invisible ring. No statement says the focus indicator carries a contrast requirement of its own against the surface it sits on.

browser-test-accessibility points contrast at a browser suite. This case needed no browser: both sides are hardcoded hex values, and the ratios come out of a few lines of arithmetic.

A model without this server found it, by computing the ring's contrast against each declared background. I did not.

A frontend query about focus and contrast answers at 40 percent.

## Query

typo3_hint_lookup task='make the frontend of a sitepackage accessible: visible keyboard focus and sufficient colour contrast on the site design' targetVersion=14 limit=4. Then ids=[css-accessibility-states, browser-test-accessibility] targetVersion=14.

## Suggestion

Separate the statements that are WCAG from the ones that are the backend's. Contrast and visible focus hold on both sides, and the frontend withholding removes them with the rest.

Add the focus indicator's own contrast as a statement. WCAG 2.2 asks for 3:1 against what the ring sits on, so one token cannot serve a light surface and a dark one. Name the fix: redefine the token on the dark components, so the shared rule picks it up.

Say that this one is computed rather than observed. The focus colour and every surface colour are declared values, so the ratio is arithmetic and a browser suite is not what finds it.
