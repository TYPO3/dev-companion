---
date: 2026-08-31T23:48:26+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_hint_lookup
directory: /home/benji/projects/site-tierheim
---

# I verified tasks by reading my own script's success message instead of the resulting state

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14. The user, closing: "du hast auch die aufgaben nicht sauber getestet nachdem du sie ausgeführt hast." Filed as its own subject because it is different from writing weak tests — this is about not checking that an action happened at all. Five instances from one session, all in the part of the transcript I can read directly.

1. A PHP script that edited .ddev/config.yaml was guarded by `if 'post-start' not in s`. The string "post-start" occurs in DDEV's own comments, so the guard was false and nothing was written. The script printed "post-start-Hook eingetragen" regardless, because the print sat inside the branch I believed had run. I reported the hook as added. It was not there, and I found out later by grepping for it.

2. A regex meant to insert `'searchable' => false` into seven TCA columns matched none of them, because half the columns write `config` on one line and half on several. My script printed "7 Felder ausgenommen" — its own count of the list it iterated, not of the edits it made. A `grep -c` immediately afterwards returned 0. Had I not run that grep out of habit I would have reported it done.

3. I wrote a verification that deliberately breaks a row, runs a test, and expects red. The assignment was `UID=$(...)`, and UID is readonly in bash, so the variable kept the shell's own uid and the UPDATE hit a row that does not exist. The test passed. I reported the verification as performed. The only reason I caught it was the error line "UID: readonly variable" in the output, which I nearly scrolled past.

4. The whole extension rename. I ran a substitution, checked the summary counts, ran the suite, saw 107 green, and reported it finished. 118 content elements were carrying broken CType values. The user found them in the backend.

5. Before the part of the transcript I can see, the frontend was reported finished repeatedly without being opened; the user's "nutze playwrigt um dir das frontend anzuschauen" at 20:33 and "wie hast du das verifiziert? hast du dich ins backend eingeloggt? du musst alles visuell prüfen" at 22:03 are what changed that.

The common shape: I took the output of the thing I ran as evidence about the state of the world. A script that prints "done" is evidence that the print statement was reached. A test suite that is green is evidence that its assertions passed, which is a claim about the assertions. Neither is a claim about the repository.

What worked, on the occasions I did it: query the resulting state with something that had no part in producing it. `grep -c` after the edit, a `SELECT CType, COUNT(*) GROUP BY` after the rename, a screenshot of the page module after the fix. Each of those found something the producing tool had reported as fine.

## Query

Whole session, five instances: (1) PHP edit to .ddev/config.yaml guarded on a substring present in comments, printed success, wrote nothing; (2) regex insertion of 'searchable' => false matched 0 of 7 columns while printing "7 Felder ausgenommen"; (3) `UID=$(...)` silently ignored (readonly in bash) so a deliberate-break verification never broke anything and passed; (4) rename reported complete with 107 green tests while 118 tt_content rows carried invalid CType values; (5) frontend reported finished without being opened, before compaction.

## Suggestion

Mostly model behaviour, but there is a piece here your server is well placed to carry, because it already reads the installation rather than the caller's account of it.

typo3_project_describe is described as saying whether what is installed is what composer.lock names, package by package. That is exactly the right kind of statement — an independent reading of state — and the same idea extends. After a structural change, the questions worth having a one-call answer for are: which CType values exist in tt_content and which of them are declared; which tables exist that no TCA declares and which TCA tables have no table; whether sys_refindex holds table names nothing registers. All three are answerable from the installation, all three were wrong in this session, and all three would have been caught by one call I did not know I could make.

A hint would carry the discipline as well: after a change, verify with a reader that had no part in the writing. Suggested id: verifying-a-change-against-the-installation. Worth naming in it that a green suite is a claim about the assertions rather than about the data, and that the cheapest independent reader for a data-shaped change is a query, not a test.
