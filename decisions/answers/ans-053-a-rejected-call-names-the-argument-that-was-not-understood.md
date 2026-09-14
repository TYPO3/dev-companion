---
id: D-ANS-053
title: A rejected call names the argument that was not understood
date: 2026-08-04
status: open
coveredBy:
  - StdioServerTest::aCallNamingAnArgumentTheToolDoesNotHaveIsRejectedByThatName
---

# D-ANS-053 — A rejected call names the argument that was not understood

**The rejection names an argument this server does not have. So a caller that
spelled one wrong learns it in the rejection rather than in a second round
trip.**

`typo3_documentation_lookup` is the only lookup here whose search argument is
`queries`; five others spell it `query`. A session guessed from the five, and
the rejection named neither the guess nor the near miss.

## Evidence

- `feedback/2026-08-04-175819`: the call carried `query` and `targetVersion`. It
  came back "Missing required properties: `queries`.; Missing required
  properties: `page`." The validator ignored the unknown property, so both
  `oneOf` branches failed. The message described a call the session had not
  made.
- `typo3_changelog_lookup`, `typo3_icon_lookup`, `typo3_label_lookup`,
  `typo3_component_lookup` and `typo3_backend_module_lookup` take `query`;
  `typo3_hint_lookup` takes `task`. The plural is not a slip — it takes several
  alternatives at once — but it is one word against five.
- `DocumentationLookup::inputSchema()` declares no `additionalProperties`, so an
  argument nothing here knows is silently dropped before the tool runs. That is
  the whole surface, not one tool.
- A client where these tools arrive with the schema deferred pays the cost
  twice. The session guesses a name or fetches a schema before any call, so a
  wrong guess buys a failed call and a fetch. The session that reported it
  counted both.

## Decided

- The judgement is **step 4**, wording, on the message rather than on the
  description. `D-ANS-012` put the alternative into the two descriptions and
  that held: this session composed a search correctly and named it wrong.
- Two candidates, and the todo decides between them after a read of what the SDK
  does. Declare `additionalProperties: false` so the validator rejects an
  unknown argument by name, or accept `query` as a singular alias folded into
  `queries`.
- The alias is the weaker one and this entry says so. "One thing, one word" is
  the rule this repository holds names to, and a second spelling that works is
  what brings a third one.
- Not closed on the spot: both candidates move a declared input schema.

## Assumed

- That the SDK's validator reports a property it refuses by name. Nothing here
  has run that; it is the todo's first step, and if it does not, the alias
  remains.
- That no client sends arguments of its own alongside a call. A wrapper that
  adds metadata would start to fail on the day the schema declares
  `additionalProperties`.

## Wrong if

- The rejection names the unknown argument and a session still guesses the
  spelling before it reads it. Then the answer is one vocabulary across the
  lookups, which is a rename that breaks callers.
- A client turns up that sends its own extra properties. Then the strict schema
  is a break for a message, and the alias is the answer.

## Since then

A measurement of both candidates against this checkout over stdio settled on the
first. The unqualified call gets the two branches, neither of which the caller
asked about. With the property declaration the same call names the property it
refuses. So the assumption holds. This tool declares it and no other does.
