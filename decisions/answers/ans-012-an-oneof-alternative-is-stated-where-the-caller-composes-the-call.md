---
id: D-ANS-012
title: An `oneOf` alternative is stated where the caller composes the call
date: 2026-08-02
status: open
coveredBy:
  - StdioServerTest::aCallCarryingNeitherOfTwoAlternativeArgumentsNamesBoth
  - ToolContractTest::anArgumentInAnAlternativeNamesTheOnesItExcludes
---

# D-ANS-012 — An `oneOf` alternative is stated where the caller composes the call

**An alternative between two arguments declared only as `oneOf` reaches no
caller. The reference renders none of it, and the rejection names one branch at
a time.**

`typo3_documentation_lookup` takes `queries` or `page`, never both, and the
schema says so in the one keyword nothing here reads out. A caller sees
`targetVersion` required and two optional arguments. A caller that then sends
only `targetVersion` gets two sentences back, each demands a different property,
and neither states the rule.

## Evidence

- `feedback/2026-07-31-185900`, re-run on 2026-08-02 against the server as it is
  now — `bin/typo3-dev-companion` over stdio from this worktree.
  `{"queries": ["encryption key environment variable TYPO3_ENCRYPTION_KEY"], "targetVersion": "14.3"}`,
  no `page`, gets an answer. That is six results from docs.typo3.org, the first
  the coreapi page on environment variables in site handling at 14.3. The
  chicken-and-egg the feedback reports does not exist and did not exist on the
  feedback's date.
- The message it quotes has one producer. Arguments of
  `{"targetVersion": "14.3"}` alone fail with
  `Missing required properties: queries.; Missing required properties: page.` —
  one message per `oneOf` branch, joined. The SDK's `SchemaValidator` collects
  the leaves of a failed `oneOf` and formats each on its own. A caller that acts
  on the last half sends `page: ""`, which is the second thing the feedback
  reports. The validator rejects it correctly:
  `Minimum string length is 1, found 0`.
- The session called this checkout. `/home/benji/projects/site-new/.mcp.json`
  runs `/home/benji/projects/typo3-dev-companion/bin/typo3-dev-companion`, and
  `9ced27c` — 2026-07-30, the day before the report — is where `required` became
  `['targetVersion']` with the `oneOf` beside it. So the schema the session read
  and the server it called are the ones above, and the session read the schema
  right.
- The keyword is on the wire and nowhere else. `tools/list` carries the `oneOf`
  whole, while `documentation/clients/tools.md` lists `queries` and `page` as
  plain optional arguments. `ToolSurface::alternatives()` runs on the output
  schema only (`src/Upkeep/ToolSurface.php:75`), where it renders "the answer
  carries exactly one of these sets of fields" for nine tools.
- Nothing else here has this shape. `typo3_documentation_lookup` is the only
  tool that declares an input-side `oneOf`. That is why one tool's callers hit
  it and the rest of the surface never did.
- The tool already says the rule in one sentence and never gets to.
  `src/Tool/DocumentationLookup.php:113` throws "Pass targetVersion and exactly
  one of queries or page", and the validator rejects the call before the tool
  runs.

## Decided

- The judgement is **step 4 of the ladder**, wording. The tool lacks no verb and
  answered in one call. What it lacked is the rule stated where a caller
  composes the call, and the message that could have corrected it.
- The search half is **answered** and the feedback **trimmed** to what remains.
  The tool's own suggestion — document a two-step workflow, or pass a
  placeholder page URL — describes a server that never existed.
- The rest is **queued**, not closed on the spot. Both candidates touch the
  declared schema, the argument descriptions or the reference generator.
  [judging.md](../../documentation/records/judging.rst) puts a tool's contract
  on the reviewed side of that line.
- This entry names the two candidates and chooses neither. One renders the input
  `oneOf` in the reference and puts the rule in the two descriptions. The other
  drops the root `oneOf` so the tool's own message answers instead of the
  validator's. The second buys a legible rejection and gives up the
  machine-readable exclusivity a client that does read `oneOf` gets today.

## Assumed

- That the call which reached the server carried no `queries`. The message has
  no other producer in this checkout, and nothing recorded the call itself. The
  session reports that it left out `page`, which cannot be what the validator
  rejected.
- That a caller who composes a call reads `required` and the argument
  descriptions rather than the `oneOf` beside them. That is what the session
  did, and it is one session.

## Wrong if

- The rule lands in the descriptions and the reference, and a session still
  calls with `targetVersion` alone. Then the shape misleads, not the wording,
  and the answer drops the keyword rather than explains it.
- A feedback reports that the validator rejected a call with `queries` in it for
  an absent `page`. Then the validator is at fault and the delivery diagnosis
  here is wrong.
- Another tool grows an input-side alternative and its callers compose the call
  correctly. Then `oneOf` does reach a caller, and this entry took one session's
  mistake for a property of the keyword.

## Since then

On 2026-08-04, a session hit the same rejection and the **Wrong if** did not
hold. `feedback/2026-08-04-175819` composed a search and spelled its argument
`query`, which this tool does not have. The validator ignored the unknown
property, the `oneOf` failed on both branches, and the message named `queries`
and `page` exactly as this entry describes. The session read it and called
correctly on the second attempt.

## Since then

The first of the two candidates landed: the keyword stays, the reference renders
it, and the rule stands in the descriptions. Three readings decided it rather
than a preference. The listing carries the alternative whole and the SDK leaves
it as it is. So the exclusivity is already on the wire in a form a client can
validate against. A drop would take that away in exchange for a message this
repository does not own. The SDK's own request handler builds the two sentences.
An entry next to this one made the same bet deliberately on the other side.
