# Open forward reviews

These are deliberately broad repository reviews. The prompt names the working
context and the user's intent, but no subsystem, skill, tool, expected defect,
or implementation shape. What the agent chooses to inspect and prioritize is the
evidence.

See [the scenario readme](../readme.md) for the audiences and the environments,
and [contracts/](../contracts/readme.md) for the targeted cases these are not.

| Review                                         | Working context                           |
| ---------------------------------------------- | ----------------------------------------- |
| [`REVIEW-01`](review-01-site-project.md)       | A TYPO3 site project and its site package |
| [`REVIEW-02`](review-02-reusable-extension.md) | A reusable TYPO3 extension                |
| [`REVIEW-03`](review-03-core-patch.md)         | A TYPO3 core patch                        |

## What every review has failed at

Three of the failures below **How it fails** stand in all three reviews, each in
its own wording, and a fourth in two of them. They are what a broad review goes
wrong at whatever it is reviewing, so the next review is written with its own
failures rather than these:

- **The corpus instead of this thing** — the generic TYPO3 checklist, every
  subsystem an extension might need, the whole contribution guide.
- **Another context's conventions** — core-only commands recommended to a
  project, a core process applied to an extension, a release branch's rules
  taken for the patch's.
- **Changing files under a review-only request**, amending or pushing included.
- **The invented requirement**, in two of the three: a missing feature nobody
  asked for, or a recommendation reported as a verified defect.

The three lists still carry their own wordings rather than naming these. What a
review words as its criteria is what its recorded run was judged against, so
rewriting one resets that run — measured on 2026-09-01, and the trade is three
forward runs against three sentences.

## Status of a forward review

| Mark       | Meaning                                                                        | What a run is for                                                           |
| ---------- | ------------------------------------------------------------------------------ | --------------------------------------------------------------------------- |
| `unrun`    | No session has established a result yet.                                       | Run it without predicting what the repository will reveal.                  |
| `covered`  | The server should answer this well today.                                      | A bad answer is a regression; fix it.                                       |
| `boundary` | Deliberately outside scope.                                                    | A clean decline and where to go instead. An invented answer is the failure. |
| `partial`  | Answered, but not for every audience or not to the depth the task needs.       | Record which half was missing.                                              |
| `gap`      | An accepted requirement that is not met yet, or knowledge that does not exist. | Record what the task needed, not that it fell short.                        |

`boundary` and `gap` are not the same thing. A boundary is an answer — "this
server does not cover it, the documentation does" — and it can be given
perfectly. A gap is an answer that ought to exist and does not.

Only these are run and recorded. One run is one file below [runs/](../runs/),
and it is what the **Status today** line of a review rests on: the environment,
the server it ran against, the skills that activated, the tools the session
called, and one judgment with evidence per criterion. The verdict is not written
into it — it follows from the judgments, and a review whose mark disagrees with
what its run establishes is a failing check rather than a sentence nobody
rereads.

How one is run, judged, and read when it stops without an error:
[documentation/records/forward-runs.rst](../../documentation/records/forward-runs.rst).
