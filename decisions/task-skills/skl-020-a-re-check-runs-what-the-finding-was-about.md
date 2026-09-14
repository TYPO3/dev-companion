---
id: D-SKL-020
title: A re-check runs what the finding was about
date: 2026-08-04
status: open
---

# D-SKL-020 — A re-check runs what the finding was about

**The re-check that closes a cleanup finding re-runs the thing the finding was
about; re-reading the changed file is what let a reverting fix be reported as
closed.**

The report names the step that hands the worked list back as the half that
worked. The one line it lacked is what separated a closed finding from a shipped
regression.

## Evidence

- `feedback/2026-08-04-180010`. The finding was that
  `config/system/additional.php` was owned by DDEV, which decides ownership by
  searching the whole file for its generated-file signature. The fix removed the
  marker and quoted the literal signature in the comment that explains why. So
  DDEV still owned the file. The suite was green, the commit was made, the
  finding was reported closed.
- Reading the file showed a correct 131-line file. What showed the defect was
  `ddev restart` and a checksum comparison. The file came back as the 45-line
  stock template with the environment contract gone, no error and no prompt.
- The same pass caught a second regression with a run rather than a read. A
  Playwright project whose dependency skipped left backend specs red instead of
  skipped. So `npm run test:e2e` was red on a fresh clone for something that is
  not a defect.
- The report says the rest of the step paid for itself, and it stays as it is.
  "What a dropped candidate owes" made the session write down 13 candidates it
  had raised and let go. Several of them would have been false findings, the
  `<f:layout name="Default"/>` its own `settings.yaml` documents as deliberate
  among them.

## Decided

- The strength is evidence about a **boundary** rather than about a decision
  ([`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)).
  What a re-read establishes is that the file says what the fix wrote, and what
  a re-run establishes is that it still says it. Only the second closes a
  finding about ownership, generation or an environment.
- One sentence, in step 12 where the skill prescribes the re-check, and nothing
  else. The report says the step otherwise works, and a rewrite would be a
  second answer to a question that has one.
- **Closed on the spot.** It is the wording of a rule that is already there,
  with nothing about TYPO3 looked up and no contract moved.

## Assumed

- That a session which re-runs the environment sees the difference. Here it took
  a checksum comparison. A session that restarts and re-reads with no comparison
  would read the stock template as the file it wrote.

## Wrong if

- A re-check re-runs everything and the cost is a cleanup nobody finishes. Then
  what a finding was "about" has to be narrowed to the environment that owns the
  file rather than left to the reader.
- A finding of this shape is missed again with the sentence in place. Then the
  lever is the audit's evidence rule rather than the re-check's wording.

## Since then

The sentence survived the merge and moved into `typo3-extension-health`, where
it answers half of the first **Wrong if** before it can fire. The re-run binds
to the worked list, and the sentence names the environment that owns the file
rather than leaves it to the reader. What it does not bound is what that re-run
costs, and no session has reported it. Nothing reports the second one either.
