---
id: D-KNW-123
title: The corpus tells a widened signature apart from a widened visibility
date: 2026-08-25
status: open
coveredBy:
  - CommitMessageTest::theAssumedClassificationBindsWideningToTheSignature
  - KnowledgeTest::aPromotedMemberIsAnsweredAsTheMoveTheCoreFilesNothingFor
---

# D-KNW-123 — The corpus tells a widened signature apart from a widened visibility

**The third breaking move is a changed signature alone, and a member promoted
from protected to public owes nothing the core files as breaking.**

`breaking-not-assessed` says "a removed, narrowed or widened public or protected
member makes the change breaking" and glosses a wider member for parameters
only. So a reviewer with a visibility change in hand reads a legitimate patch as
unsubmittable.

## Evidence

- Re-run on 2026-08-25 against the server as it is now, through
  `bin/typo3-dev-companion` over JSON-RPC. `typo3_commit_message_guide` with
  `workflow="core"` and the message of Gerrit change 91127, `isBreaking`
  omitted. `breaking-not-assessed` comes back with the text the feedback quotes,
  unchanged.
- The check is one word wider than the source it points at.
  `## Changed Signatures` of
  `knowledge/documents/core/contribution/commit-messages.md` is what
  `typo3_rule_lookup(query "breaking change")` returns. It opens "A signature
  change is the third breaking move beside removing and narrowing, and adding a
  parameter is one". It names the parameter and never the visibility. The check
  compresses that into a bare "widened … member", and the reporting session read
  the compression rather than the gloss behind it.
- Nothing states what a wider visibility does owe.
  `bin/cli hints:probe "widening a protected member to public in a core patch"`
  matches nothing and returns 105 hints as the index. The same probe worded as a
  breaking change reaches `deprecated-apis` and
  `breaking-without-a-moved-member` on `appliesTo` alone, with `text(0)` on the
  second.
- The core widens visibility in plain commits and files no entry. Read in
  `.checkouts/main` at `3cbdea24dd`, a sweep of the 1515 commits since
  2025-01-01 over the `Classes/` of `core`, `backend` and `frontend`. The sweep
  looked for a diff that drops a `protected function` and adds the same name as
  `public function`: 17 commits. The five that promote visibility and nothing
  else — `343e93a978`, `309bbf5e7a`, `a4ad6ad408`, `341826fc3c`, `f083c99e4a` —
  carry no file below `Documentation/Changelog/` at all. The three with `[!!!]`
  carry one for a different move in the same patch. `5d637429cf` introduces the
  System Resource API, `c698eab881` a new interface, `16c2bf661e` native types
  across FAL.
- Three of them reach a maintained line, which a breaking change cannot.
  `343e93a978` is `Releases: main, 14.3, 13.4`, and `309bbf5e7a` and
  `f083c99e4a` are `Releases: main, 13.4`. That the maintained lines take no
  Breaking entry is the corpus's own statement, confirmed by the sweep in
  `D-KNW-072`.
- The asymmetry is in the changelog and only in one direction. A search of the
  whole `Documentation/Changelog/` tree for a visibility move finds
  `Breaking-110277`, `Deprecation-86047` and six more, every one of them public
  to protected. `from protected to public`, `protected to public` and
  `now public` match no file.
- `D-KNW-065` established the parameter half and stopped there. Its **Confirmed
  on 2026-08-09** reads the core on a *changed parameter* — `Breaking-101133`,
  `Breaking-107777`, `Important-107342` — and on PHP's own override rule. The
  word "widened" entered `breaking-not-assessed` from that read, where it meant
  the parameter. It carries the visibility with it in a way nobody read it
  against.
- The hazard the check reaches for is real and is a different claim. The session
  verified that a subclass which overrides with the narrower visibility fatals
  with "Access level … must be public". `PageRenderer` is neither final nor
  `@internal`. What the core does about it is nothing.
- One report, and the third in this family. `bin/cli feedback:list` on
  2026-08-25 holds 49 open feedback, 45 of them from
  `/home/benji/projects/typo3-cms`. `D-KNW-065` and `D-KNW-072` took the same
  surface on from the two other directions — the signature, and the change that
  moves no member at all.

## Decided

- Step 4 on the wording of `breaking-not-assessed`, step 1a on the statement
  that nothing makes. Queued rather than closed on the spot. The wording is in
  `src/Knowledge/CommitMessage.php`, which gets a review rather than an
  improvisation.
- `normal` rather than the `low` the card arrived at, on the grounds `D-KNW-065`
  and `D-KNW-072` set. One session, so not more than that — and what this
  wording produces is the mirror of what those two produced. Theirs let a
  breaking change through in a `[BUGFIX]`. This one calls a submittable patch
  unsubmittable, and the session that caught it paid a 1405-commit sweep to do
  so.
- Not `high`. Nothing blocks, and the reviewer's own read came out right.
- The sweep is not owed again. `D-FBK-052` is why it is here rather than in the
  card. This run read the checkout and holds the evidence, and a queued read
  would send the next session to the same 1515 commits.
- The feedback's second half folds into the same wording rather than comes off.
  Half of it is already true, since the check names
  `typo3_rule_lookup(query "breaking change")` today. The rest, a check held to
  which member kinds to enumerate, is a rewrite of the one sentence this entry
  is about. `R-GUI-011` demands a named classification and demands no paragraph
  of obligations, so a shorter check holds that requirement.
- Not the feedback's own wording. Its author guessed about this repository as
  much as a judging run guesses about TYPO3. One commit named in an answer would
  date the moment the core moves on.

## Assumed

- That `core`, `backend` and `frontend` stand for the tree. They are where the
  reporting session swept and where the overridable classes are, and the other
  sysexts were not read.
- That the merged history is the practice. A patch rejected on review for a
  wider visibility leaves no commit, so a sweep of `main` cannot see one.
- That the wording produced the misreading, rather than the reviewer arriving
  with it. Nothing here separates the two, and the lever is the same either way.

## Wrong if

- A changelog entry files a protected-to-public promotion as Breaking. The
  distinction would then be the core's exception rather than its rule, and the
  check's sentence is right as it stands.
- The corrected wording lands and a reviewer still calls a widened visibility
  breaking. The lever is then where the statement sits rather than what it says,
  and the review checklist is the half that has to move.
- A widened visibility turns out to owe an `Important` entry the way an
  `@internal` signature change does. The correction is then a type rather than a
  removal, and this entry has answered half of it.
- The same sentence is misread from writing a patch rather than from reviewing
  one. The wording is then reaching both routes and the placement is what is
  wrong, as it was in `D-KNW-065`.

### 2026-08-27 — the fourth Wrong if fires from the writing route

**The same paragraph stopped a second session, and this one was writing the
patch rather than reviewing one.** `feedback/2026-08-27-145413` added an
optional `?int $language` to a protected method and read the corrected sentence
correctly: a widened signature is in scope. What it had no branch for is that
`TYPO3\CMS\Workspaces\Service\WorkspaceService` carries `@internal` on the
class. The session settled that with `sed` over the docblock and calls it "the
highest-leverage fact in the session". It concluded no `[!!!]`, no Breaking
entry and no changelog entry at all.

The last of those three is wrong by this repository's own corpus, which is what
makes it a delivery failure rather than a preference. `## Changed Signatures` of
the commit-message document says a member marked `@internal` takes an
`Important` instead. It names `Important-107342` as the precedent that reached
`13.4.x` on that ground, and says an entry is still owed with only its type
changed. `typo3_rule_lookup(query "breaking change")` returns that section
second, re-run on 2026-08-27, and the check already named that call.

So the answer is the branch in the check rather than the placement this entry's
fourth **Wrong if** predicted. What the paragraph gains is one clause that names
a classification, which is what `R-GUI-011` asks the check to do. The
obligations stay behind the call, which is what that requirement keeps out. A
caller with no reason to suspect an unstated branch does not make a call to look
for one. That is what both sessions demonstrate from opposite routes.

**The lookup the feedback asks for is not built.** Its first suggestion is a
tool that answers a core class's API classification: `@internal`, `@deprecated`,
the scanner configuration, `final`. The session got the fact from one `sed` in
the checkout it already stood in. That is the case `D-FBK-027` and `AGENTS.md`
name as one that earns no tool. What earns one is round trips, and this is a
local read of a docblock. Its third point needs nothing either.
`typo3_component_lookup` answers backend components and not PHP API
classification, checked on 2026-08-27, so the description that steered the
session away from it was right.
