---
id: D-AUD-003
title: The instructions carry the entry point, because the tool descriptions never arrive
date: 2026-07-31
status: confirmed
coveredBy:
  - ScopeTest::bothCallsOfTheEntryPointAreToldInTheImperative
---

# D-AUD-003 — The instructions carry the entry point, because the tool descriptions never arrive

**The `instructions` state the entry point into this server, because under
deferral the tool descriptions are not a channel at all.**

`REVIEW-01` ran in `E-SITE` and this server took no part in it: no tool called,
no skill activated, all thirty-five calls through Bash. Reading the client's own
attachments back showed why, and the three channels behaved differently enough
that one fix would have been the wrong fix.

## Evidence

- The eighteen tools arrived as a `deferred_tools_delta`: names only, no schemas
  and no descriptions. So every tool `description` in `src/Tool/` was outside
  the session's context, and only a `ToolSearch` call first would have shown
  one. The `instructions` did arrive, in full, from the first turn. They opened
  with a profile caveat, then "not a patch assistant", then "it does not read
  your working tree". They named no entry point for the commonest request there
  is. The twenty `routing` entries, which do name one, sit behind
  `typo3_server_scope`. A session has to call a tool to learn that it should
  call tools. The skills arrived with their full descriptions.
  `typo3-extension-conformance` did not activate. Its description reads "Audit
  or improve a TYPO3 project, sitepackage, or extension … Use for extension
  reviews, … quality or readiness assessments". The prompt read "Review this
  TYPO3 project and its site package. Identify the most important concrete
  problems, risks, or missing safeguards". Its body would have met both criteria
  the run failed. Step 1 is `typo3_project_describe`, and it hands repairs to
  the test and documentation skills.

## Decided

- The `instructions` open with `typo3_project_describe` as the first call of any
  task and name a review beside an upgrade and new code. The working-tree
  sentence turns from a disclaimer into a division of labour, and the review
  shape gets a `routing` entry of its own. The skill description leads with the
  open request, review a repository and say what is wrong with it, in priority
  order. It no longer promises compatibility, which described one of the seven
  things that run found. The tool descriptions stay as they are. They are not
  the channel that failed, because under deferral they are not a channel at all.

## Wrong if

- The second `REVIEW-01` run still reaches for Bash alone. Then the wording was
  never the obstacle, and what remains to suspect is the skill's name,
  `extension-conformance` for a site project. Past that, a repository review may
  need nothing this server has except the installed version, the icon and label
  registries and the component contract. That answer would be worth its keep; it
  is much smaller than the current surface implies.

## Confirmed on 2026-07-31

The second `REVIEW-01` run did not reach for Bash alone. It ran against the
commit that applied this entry, activated the skill first and called
`typo3_project_describe` second. So both channels carried and the wording was
part of the obstacle. What it did not do is follow the skill past step 2;
thirty-eight of 45 calls were still Bash. That is the order rather than the
entry point, and `D-SKL-001` owns it. The second suspicion falls with the first:
run 4 called eight tools fifteen times and found the two things three runs had
missed.

## Since then

The same channels failed on a task that builds rather than reviews, in another
client and a much smaller model. So it is not the **Wrong if**. What the
instance adds is a cause this entry did not name. A skill description that names
one side of a domain it owns both sides of reads as somebody else's work. A read
of all seven for that shape found two, and both changed, with `R-SKL-010` to
hold the pair. Its own author later withdrew half the instance, because the
session may have activated a skill after all. The finding does not rest on it,
because the descriptions came off the files here.

`REVIEW-03` settled the delivery on 2026-08-03. The instructions and all seven
descriptions are in the transcript's own attachments, against 23 calls that are
22 `Bash` and one `Read`. An entry point carries a task where a skill is there
to receive it, and in `E-CORE` there is none. `D-SKL-005` carries that half.

## Confirmed on 2026-09-17

The cause of the deferral has a source. The API's tool search documentation
tells a client to defer from ten tools or 10k tokens of definitions. This server
sends 31 tools and about 20k tokens before any `outputSchema`. The same page
says the search reads the description. So the description is a channel after
all, for the session that knows a subject and no tool name. `D-AUD-019` takes
that half.
