---
id: D-ANS-178
title: A ViewHelper page answers with the arguments its reference lists
date: 2026-10-11
status: open
coveredBy:
  - DocumentationTest::aViewHelperPageAnswersWithTheArgumentsTheReferenceLists
---

# D-ANS-178 — A ViewHelper page answers with the arguments its reference lists

**A page of the ViewHelper reference answers from the reference's
`viewhelpers.json`. The answer carries the tag, the class, the XML namespace and
every argument, as `viewhelper` and in the excerpt.**

The excerpt of such a page was its first paragraph, which names what the
ViewHelper does and none of what a template writes into it.

## Evidence

- **render-guides 0.45.0** writes `viewhelpers.json` for the ViewHelper
  reference, which publishes no `confvals.json`. Read on 2026-10-11, it answers
  at `main`, 14.3 and 13.4, keyed by tag. Each entry carries `class`,
  `namespace`, `xmlNamespace`, `summary`, `documentation`, `docTags`,
  `allowsArbitraryArguments`, `source`, `arguments`, `path`, `anchor` and
  `permalink`. Each argument carries `type`, `required`, `default`,
  `description` and its own anchor.
- **No tool answered a ViewHelper's arguments.** `typo3_fluid_namespace_list`
  answers which prefixes are global, and nothing else here reads a ViewHelper's
  signature.
- **Read over stdio on 2026-10-11.** `f:if` at 14.3 answered its three arguments
  and `f:form.textarea` at 13.4 its eight, without a page read.

## Decided

- The maintainer gave this its own todo on 2026-10-11, and this session decided
  to build it, because the arguments are what a template needs and the page
  excerpt never carried them.
- The entry is found by the page it documents: `path` plus `.html` against the
  result URL. A page the file lists nothing for answers from the page as before.
- `description` of an argument and `documentation` stay out of the data. The
  page carries them, and the result URL is one call away.

## Assumed

- That `path` names the page the inventory lists for the ViewHelper.

## Wrong if

- A ViewHelper page answers from the page while the file lists it. Then the path
  and the URL differ.
