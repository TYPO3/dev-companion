---
id: D-AUD-013
title: A competing route is corrected where it is written
date: 2026-08-25
status: revoked
revokedBy: D-AUD-014
coveredBy: []
---

# D-AUD-013 — A competing route is corrected where it is written

**A session corrects a route the caller's own repository names in that
repository. This server's own surfaces state its routes and say nothing about
another file.**

The core checkout ships an instruction file that names a `curl` recipe for
Forge. This server has a tool that answers the same question better.

## Evidence

- [`feedback/2026-08-24-163321`](../../feedback/archive/2026-08-24-163321-the-repository-s-own-agents-md-routes-agents.md)
  worked a core patch with that file in its context and called
  `typo3_forge_lookup` anyway. It names what it got that the recipe could not
  give. That is the `open` enumeration with its filters, and `notes=people`,
  which dropped 12 of the 14 comments on issue #104460. The session had the
  worse route and did not take it, so nobody observed a failure. What the
  feedback reports is what a more obedient session would have done.
- `AGENTS.md` at the root of the core checkout, read on 2026-08-25, still
  carries the paragraph verbatim. No line of the file names an MCP server.
- The core repository tracks that file; `git log` names
  `781c8525870 [TASK] Add AGENTS.md with repository guidelines for coding agents`.
  So a patch through Gerrit changes it like any other core file, and not an edit
  on somebody's machine.
- The same feedback's second point is the other side of the boundary. The
  session wrote its commit message from that `AGENTS.md` and never called
  `typo3_commit_message_guide`. The review accepted the message and it survived
  an amend. Where the other file is right, nothing reaches the tool and nothing
  is lost.
- Two open feedback report that file in disagreement with the guide rather than
  in agreement: `2026-08-24-183512` on `Signed-off-by` and `2026-08-24-205132`
  on the 72-character body wrap. Both carry their own cards and this entry
  judges neither.

## Decided

- The judgement is
  [`documentation/records/judging.rst`](../../documentation/records/judging.rst)
  step 3, routing, against a routing table this repository does not own. The
  feedback is **trimmed**: its changelog half has its answer under
  [`D-KNW-111`](../knowledge/knw-111-the-changelog-procedure-is-a-guide-of-its-own.md)
  and its Forge half stays open.
- The three surfaces this server states a route on are the `instructions` of
  `knowledge/server-scope.json`, each tool `description`, and the skills the
  installer publishes
  ([`D-DIS-017`](../discovery/dis-017-the-skills-reach-a-project-through-the-installer.md)).
  None of them changes for this, because none of them failed.
- Rejected: a skill that says what another repository's `AGENTS.md` names. A
  skill lands in somebody else's project, where a sentence about a third
  repository's file goes stale and no release of this server reaches it.
- Whether to propose the core patch waits for the maintainer. Naming an
  experimental server in the core's own instructions is an outward claim, and
  the ladder says an outward claim is never made quietly.
- The priority is `normal`, set by what the file is rather than by how many
  sessions reported it. Every session in a core checkout reads it into its
  context.
- Nothing holds this. The lever is a patch in another repository, and the two
  surfaces it bears on stay as they are. So there is no assertion to write.

## Assumed

- That a session which holds both the recipe and the tool takes the tool. This
  one did and said why, and no session has reported the other way.

## Wrong if

- A feedback reports a session that queried Forge with `curl` while it had
  `typo3_forge_lookup` on offer. Then the tool list does not win against the
  file, and a statement on this server's own surfaces is the lever after all.
- The core's `AGENTS.md` comes to name an MCP server. Then this server's absence
  from it is a placement rather than a boundary. The paragraph is a patch to
  write rather than a question to ask.
- The maintainer answers that nobody proposes a core patch that names this
  server. Then nobody corrects the route anywhere, and what remains is the
  statement on this server's own surfaces that this entry declined to make.

## Since then

A session in the same checkout bounds the commit-message bullet. The account
holds for the conventions that file states in full. It does not hold for the
branch set, which the file states as a rule without the value. That bounds the
evidence rather than the decision.

The first **Wrong if** has its case. A session searched Forge with three `curl`
calls while it had the tool on offer. It names the documented recipe as why it
never considered one. Judged on 2026-08-27 as `D-AUD-014`: the ladder's step 4,
and the lever is the tool description's first sentence. So the bullet which says
none of the three surfaces changes holds for the day of this entry.

## Revoked on 2026-08-27

**The maintainer answered that nobody proposes a core patch that names this
server. A change to another repository's instruction file is out of scope for
this one.** That is the third **Wrong if** verbatim, and it retires the entry
rather than qualifies it. A correction of a rival route where it stands is what
will not happen. The patch existed and passed its checks; what stopped it was
neither its text nor Gerrit. What remains is `D-AUD-014`, the statement on this
server's own surfaces. A session that reads the other file and never calls the
tool is a cost this server carries.
