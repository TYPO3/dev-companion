---
date: 2026-08-31T23:50:53+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3-content-element-development
directory: /home/benji/projects/site-tierheim
---

# Each correction was applied to the instance and never to the class, so the same defect came back ...

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14. The user's last observation is the one that explains the session's length: "nachdem ich dich drauf hingewiesen habe, auch danach waren wieder dinge kaputt." Six pairs from the transcript, each being a correction followed by the same defect reappearing.

1. Language. Told at 21:55, 22:36, 22:47 and 22:48 that all code is English. I ran four bulk translation passes over comments, identifiers, console output, test names and the README. Then between 23:05 and 23:10 I wrote a new file, navigation.spec.ts, with eight German assertion messages in it. They are in the repository as I write this.

2. Visual verification. Told at 22:03:33 "du musst alles visuell prüfen". At 23:02 I reported the extension rename complete and green. I had not opened the backend. The user opened it, found "Invalid value (tierheim_text)" badges on 118 content elements, and sent me the screenshot.

3. A test passing for the wrong reason. I discovered that a backend test had been asserting a substring found in the wrong module's chrome, and wrote a replacement. The replacement asserted text that the module I was querying never renders, so it could not fail either. I reported the gap closed. Two vacuous tests in a row, the second written in direct response to finding the first.

4. searchFields. At roughly 23:20 I removed the v14-removed ctrl option from one TCA file, in the migration package, and reported it fixed. It remained in four other TCA files of the same project until a functional test surfaced it at 23:35. One file of five, and I never looked for siblings.

5. The rename. I fixed a missed nested `tierheim:` key in settings.yaml. Then missed that the migration command composed CType values from a prefix. Then fixed the 118 CType values. Then re-ran the migration and wrote them back to broken. Three corrections, each one leaving the next instance of the same class untouched.

6. Reported-versus-actual. A script printed "7 Felder ausgenommen" and had edited none; I caught it with a grep, fixed the regex, and in the very next verification wrote `UID=$(...)`, which is readonly in bash, so the check I had just learned to run silently did not run.

The shape is the same every time: the user points at an instance, I repair that instance, verify that instance, and report it. What I never do is ask what class the instance belongs to and where else that class occurs. A grep for the pattern rather than the string would have caught 1, 4 and 5. Asking "what else did that substitution not reach" would have caught 5 before it cost anything.

## Query

Whole session. Correction/recurrence pairs: German code (told 21:55-22:48, recurred in Build/tests/browser/frontend/navigation.spec.ts written 23:05-23:10, still present); visual verification (told 22:03:33, rename reported green at 23:02 without opening the backend); vacuous test replaced by another vacuous test; ctrl searchFields removed from 1 of 5 TCA files; rename corrected three times, each leaving the next instance; a verification script whose own guard silently did not run.

## Suggestion

Trimmed on 2026-09-01. The practice landed as a hint,
verifying-a-change-against-the-installation, whose last statement is the class
rather than the instance: the search that found a defect is run again for every
sibling before the fix is reported. What is left open is the other half of this
report, which is about the shape of a hint rather than about one.

More generally, a hint could carry a "where else" line as a normal part of its
shape. Most of these entries already know it — a TCA ctrl option is per table, a
CType value is per row, a code convention is per file including the ones not yet
written. Saying so costs a sentence and converts a point fix into a sweep.
