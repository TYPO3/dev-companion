---
id: D-SCO-006
title: Every surface says who this server answers for
date: 2026-07-29
status: confirmed
coveredBy:
  - ScopeTest::everyDescriptionOfTheServerNamesAllThreeAudiences
  - ScopeTest::noSurfaceClaimsTheCoreAlone
---

# D-SCO-006 — Every surface says who this server answers for

**All three prose sources now say who this server answers for, and a test rather
than a wording holds the contradiction shut.**

Several entries settled that the extension author and the site developer are
audiences of this server. The claim that they are not came back each time. A
session read every place that states a boundary and found where it came from.

## Evidence

- The four places that state one.
  - `knowledge/server-scope.json` carried it as a `doesNotCover` entry — "The
    knowledge base is scoped to contributing to the core". That entry is the
    canonical text anyone who writes about scope copies from.
  - `knowledge/typo3-core-architecture.md` opened its TypoScript section with
    "Configuring an installation with TypoScript is out of this server's scope".
    It is a knowledge document, so the server also handed the sentence to
    callers.
  - `Scope::OUTSIDE_CORE_NOTICE` said the server "only knows the core's own
    conventions". Every tool opens with it, so the answers themselves repeated
    the frame.
  - `requirements/` says the opposite in R-AUD-001 and R-AUD-002 and flags the
    conflict — but R-AUD-002 is **open**, and nothing decided which side wins.

## Decided

- All three prose sources now say what is true, and a test guards the
  contradiction. `ScopeTest::aTopicWithAHintIsNotDeclined` fails if the declared
  scope excludes a subject that has a hint.

## Assumed

- The pull that remains is the name `outsideCore`. It is a scope word for what
  is really a payload rule: "leave out what only the core repository has". Each
  session that touches it derives a scope sentence from the name again.

## Wrong if

- The claim reappears anyway, in a place the guard does not read — a tool
  description, the readme, a hint. Then the flag needs the name of what it
  decides, `coreRepositoryOnly` or the audience of R-AUD-002. A correction of
  the sentences one at a time is not enough.

## Since then

`ScopeTest::noSurfaceClaimsTheCoreAlone` reads the three places **Wrong if**
names too. Its corpus is every tool description, `readme.md`, every architecture
hint, and the scope's own purpose, routing and instructions. It matches the
claim in both wordings the session found it in. One names who it turns away —
"out of this server's scope" beside an extension, a project, an installation.
The other names nobody and confines the server instead. "Scoped to contributing
to the core" and "only knows the core's own conventions" were that. All three
surfaces were already clean on 2026-08-01, so the guard holds a boundary rather
than reports a breach.

What it matches is wording, and that is the weakest thing there is to match. So
the test runs the three sentences the evidence records through the matcher
first. A matcher that no longer recognises them fails with them rather than
passes everywhere.

What stays out of reach is narrower than **Wrong if** was. A wording neither
form catches is still unguarded. So are the two surfaces the test leaves out on
purpose, the knowledge documents and the skills. There "this holds for the core
repository" is the true sentence, and a guard could only make it harder to
write. An occurrence in either asks for the flag rename, which stays the next
step.

## Since then

The **Assumed** was right and the pull was the name. On 2026-08-02 a session
removed `outsideCore` rather than renamed it. One enum, `Knowledge\Scope`, says
which kind of work an answer is for. No field remains whose only content is what
the work is not
([`D-KNW-005`](../knowledge/knw-005-scope-is-the-one-word-for-which-work-a-statement-is-for.md)).

## Confirmed on 2026-08-22

The guard is there and reads what it claims.
`ScopeTest::noSurfaceClaimsTheCoreAlone` reads the tool descriptions, the
readme, the hints and the scope's own text, with `aTopicWithAHintIsNotDeclined`
beside it. The claim has reappeared in none of them.

Its own remedy has overtaken **Wrong if**. It prescribed a rename of
`outsideCore`, and the section above records that a session removed the flag
instead on 2026-08-02. So only a reappearance would show this wrong. The places
it can still happen are the two the guard leaves out on purpose: the knowledge
documents and the skills.
