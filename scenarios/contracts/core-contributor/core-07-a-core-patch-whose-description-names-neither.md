# CORE-07 — A core patch whose description names neither the path nor Gerrit

**Environment:** `E-CORE` · **Contract:** `held` — `R-SCO-003`
**Held by:** `ScopeTest::aCoreTaskNamingNoPathKeepsTheSubmissionRules`,
`ScopeTest::theBriefNamesWhatWouldTurnTheConditionIntoFact`,
`ScopeTest::aCorePathStillMakesTheSameWordAPatchSubmission`

> The page tree loses drag and drop as soon as a mount point is set. Fix that,
> then take me through pushing it for review.

This is the case `D-SCO-002` names as its own cost. The words that select the
submission intent, "push", "review", describe maintenance anywhere. So the
intent needs positive evidence of core work and this text carries none. The
contributor is in a core checkout all the same, and the submission rules reach
them as a conditional match rather than as fact. What the test holds here is
that the condition stays cheap to answer.

**What the agent needs from this server**

- The core's own checklist and checks, stated as applying, because the
  installation places the work even where the text does not.
- The Gerrit steps for a first push, under whatever condition the server
  attaches to them.
- The question the server could not answer for itself, and what answering it
  costs.

**What has to come out of it**

- Both submission steps arrive whole — one commit amended rather than a second
  one, and the `refs/for/` refspec with the `Change-Id` kept. Neither goes for
  want of evidence.
- The condition each carries is one the contributor settles from their own
  intent, without a lookup and without reading the rest of the answer.
- The rest of the brief is the core's own: the target branch, the issue context,
  the `runTests.sh` suites.
- Naming a `typo3/sysext/` path in the same session turns the condition into a
  stated match rather than repeating it.

**How it fails**

- The submission steps withheld, so the contributor has to ask a second time for
  a workflow the server already knows.
- A condition phrased as a question about the repository rather than about the
  patch, which the contributor answers by guessing what the server meant.
- The core checklist demoted along with the intent, which turns one unproven
  signal into a hedge over the whole answer.
