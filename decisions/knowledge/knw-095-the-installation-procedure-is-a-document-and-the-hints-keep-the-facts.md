---
id: D-KNW-095
title: The installation procedure is a document and the hints keep the facts
date: 2026-08-18
status: open
coveredBy:
  - KnowledgeTest::theBootRunIsOrderedInTheDocument
  - ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope
  - SkillTest::theBootStepNamesTheGuideThatCarriesTheRun
---

# D-KNW-095 — The installation procedure is a document and the hints keep the facts

**The procedure this server already carries in `installation-boot` and
`installation-setup` becomes a document below
`knowledge/documents/project/installation/`, and the hints keep their facts.**

A session that booted an extension repository read the guides list and found no
installation procedure among the eleven. It assembled one from a skill and two
hint ids.

## Evidence

- `feedback/2026-08-18-070538`, a fresh clone of `github.com/TYPO3GmbH/blog`
  brought up under DDEV. It reports three things at once. The guides list was
  absent on the fresh clone. The list is in reach only through calls this
  workflow does not make. None of the eleven documents is about installation.
  Only the third is this entry's.
- The first is `D-ANS-085`, decided against `feedback/2026-08-18-070333` and in
  hand on a branch of its own. It names `guides` among the fields the answer
  owes wherever the server finds a project root. Re-run on 2026-08-18 against
  this branch, `bin/typo3-dev-companion` over stdio from a directory with that
  repository's `composer.json` and `.ddev/config.yaml`. It answers the
  `unsupported` object, seven searched directories and no `guides` key.
- The second is `feedback/2026-08-18-074226`, which reports it on its own and
  has its own card. `D-ANS-070` already states that the call that reads a
  document hands it over rather than a resource list. `D-ANS-083` discharges
  `typo3_server_scope` with the describe answer, which is the step the skill
  names and the session followed.
- Nothing about TYPO3 is absent. `bin/cli hints:probe` with the feedback's own
  subject reaches `installation-boot`. The second `ddev start` it names is in
  `knowledge/hints/project.json` and again in `knowledge/task-intents.json`,
  `D-KNW-085`. So are the `Typo3Version.php` detection, the `additional.php`
  DDEV does not write and exception 1396795884. The unattended install, what
  `typo3 setup` refuses, `extension:setup`, `cache:flush` and
  `backend:user:create` are in those two entries as well.
- The session was not blocked by it. It says so itself: it assembled the
  procedure from the skill plus two hint ids and the installation came up.
- `installation-boot` is a procedure written as a hint. Its title is *Booting
  the Installation a Project Repository Declares* and its first statement is the
  four steps in order. Install the dependencies, start the environment, bring
  the data in, make the installation agree with the code. A hint states one
  thing, and `D-FBK-043` is where a document answers a structure instead.
- The guides list is what a caller sees of that corpus, and it holds eleven
  documents. One of them is `project/` scope and it is `project/testing/`. The
  domain the `instructions` open every task with has no entry there at all.
- The corpus: 31 open feedback out of `/home/benji/projects/blog`, all of
  2026-08-18, and the installation cluster is the largest run in it. Every other
  card in that cluster is about the tool or the skill; this is the only one
  about what the guides list holds.
- `skills/typo3-development-installation/SKILL.md` orders both branches today,
  and `D-SKL-056` re-cuts it, decided against `feedback/2026-08-18-070448` and
  in hand on a branch of its own. The fork above **Boot what the repository
  already declares** is the same fork a document would follow.

## Decided

- **Step 1b, the shape.** The answer is here and no call hands it over whole.
  The procedure is in the hint corpus, and the inventory a caller chooses a
  procedure from is the document corpus. Not step 1a, since every fact the
  feedback names already stands verified.
- **Taken on**, as a document below `knowledge/documents/project/installation/`,
  which declares what it is and when to reach for it as `D-KNW-057` requires.
- **The document orders the procedure and the hints keep the facts**, which is
  the split `D-KNW-061` made for the manual scaffold. The ordered steps move out
  of `installation-boot` rather than get a copy, because two orders that release
  together are the expensive half of both.
- **The skill routes to it by `documentId` at the step that needs it** rather
  than restates it, which is `D-SKL-045`. What the skill keeps is the fork and
  the crossings; what the document keeps is the run.
- **Not the feedback's own id.** It proposes `project/installation/ddev-boot`,
  and DDEV is one environment a repository may declare. The procedure is for the
  environment the repository declares, which is what `typo3_project_describe`
  reports and what the hint already says.
- **Ordered behind two entries rather than queued beside them.** `D-SKL-056`
  re-cuts the fork the document follows. `D-ANS-085` is what makes the list
  visible in the state a session reads the document in. A guide a fresh clone
  cannot see is half delivered.
- **Priority `normal`**, set by the empty inventory for the workflow the
  initialize instructions open every task with. One session that asks for a page
  did not set it.

## Assumed

- That a caller reading the guides list and finding no installation entry
  concludes there is none. This session did, and said so.
- That one read can judge the boot branch and the create branch. Whether they
  are one document or two is the todo's first step, and the skill's fork on them
  is the reason to expect two.
- That the move of the order out of `installation-boot` costs its callers
  nothing. The hint keeps its facts and the document names it in its own
  `hints:` front matter, which is the link `D-KNW-057` declares.

## Wrong if

- A session that read the document asks the hints for the order anyway. Then the
  split is in the wrong place and the hint was the procedure.
- The document reads as the skill's two sections rewritten. Then what earned it
  was the skill's shape rather than the corpus's, and the lever is the skill.
- The guides list grows the entry and a session on a fresh clone still assembles
  the procedure by hand. Then delivery was the whole of it and `D-ANS-085`
  carried this report already.
- Nobody can write one document without a branch per environment. Then this is a
  statement per environment rather than a procedure, and it belongs where the
  hints are.

## Since then

Written as one document rather than two, and what settled that is which branch
the corpus carries as a run. One hint opened with the four steps in order and
was the only entry that did. The other branch's order exists in a skill and
nowhere else, and a copy of it would have been the second **Wrong if**. The
third shape that skill forks on is the other half. A repository that declares an
environment and no procedure runs both, so a document per branch would leave it
with both to read.

One thing moved that nothing asked for. The matcher computes a term's weight
over the sections in front of the query. So eight more of them carried an
earlier recorded query over the coverage floor. Both halves that entry bought
are still bought, on the other call.
