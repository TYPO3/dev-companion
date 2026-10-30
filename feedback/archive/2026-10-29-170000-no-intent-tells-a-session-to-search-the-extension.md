---
date: 2026-10-29T17:00:00+00:00
category: tool-gap
status: closed
closed: 2026-10-30
model: claude-opus-5, claude-fable-5-1
tool: typo3_task_guide, typo3_ter_lookup
directory: /home/lina/trials/jambo-1029, /home/lina/trials/jambo-fabian
---

# no intent tells a session to search the extension registries

## Observation

`extension-reuse` settles whether a feature takes a published extension or a
build of its own. It is balanced. Statement [0] says to name the published
extension before the first file, and to get that name from
`typo3_ter_lookup` rather than from recall. Statement [3] gives the
counterweight: a dependency couples the site's upgrade to somebody else's
release schedule, and a feature that uses a small part of a large extension
is the case for a build of its own.

**Nothing routes to it.** `knowledge/task-intents.json` names
`extension-reuse` zero times and `typo3_ter_lookup` zero times. No intent
carries the decision, and no checklist item names the tool.

The two runs of 2026-10-29 show what follows. Each added a site search and a
contact form to a greenfield sitepackage. Both are features where a published
extension is the first question. Neither run called `typo3_ter_lookup` once.
The string appears 20 times across the two transcripts, every time in a tool
description or in hint text passing by.

Both runs still chose well, and that is the point. They took
`typo3/cms-indexed-search` and `typo3/cms-form`, which are core. They chose
them from their own training rather than from the registries, so the choice
was right by luck of the subject. A council feature with no core answer, such
as a tender list filtered by ward, reaches the same silence.

**Extbase is the other half of the same decision, and it is routed once.** The
`content-element` intent carries one checklist item for it: "Settle whether
this element needs Extbase before its rendering is designed." The `extbase`
hint it names is titled "What Extbase Is For, and When It Is Not Needed", and
its first statement gives the trigger list: pagination, a search with
arguments, validation and forms.

Both runs received that hint. It stood in the hints of each session's second
`typo3_task_guide` call. The maintainer then asked each for a search and for a
form, which is two of the four triggers. Neither run reached Extbase, because
each delegated the feature to a core extension first.

So the two halves of one decision are routed unequally. Whether to build is
reachable from one intent, as a gate phrased for the answer "no". Whether
somebody has already built it is reachable from none.

## Query

User: "I'm a PHP developer and I have never used TYPO3. There is a static HTML
design in design/. I want to build this site in TYPO3." Two runs:
`claude-opus-5` in `/home/lina/trials/jambo-1029`, and `claude-fable-5-1` in
`/home/lina/trials/jambo-fabian`. The maintainer then asked each run for a
site search and for a contact form.

The maintainer's own observation started this: no task she wrote made the
server suggest Extbase.

## Suggestion

Route `extension-reuse` from the intents where a feature starts. `sitepackage`
and `content-element` are the two these runs reached. The checklist item is
one sentence: name the published extension before the first file, with
`typo3_ter_lookup`.

Say when a core extension settles it without a search. `typo3/cms-form` and
`typo3/cms-indexed-search` are the answer to two common features, and a
session that knows this spends no call on the registries. The statement that
names them is cheaper than the search it replaces, and it is the statement
both runs supplied from training.

Give the `extbase` entry a positive case to match its trigger list. Its title
and its first statement both lead with the negative, and the one checklist
item that reaches it is a gate. A statement that reads "a list the visitor
filters and pages through is what Extbase is for, and the core ships no
answer for it" would settle the same question from the other side.

Test the gate before changing the prose. One prompt does it: a tender list
filtered by ward and paged ten at a time, over a table the sitepackage owns.
Whether that reaches Extbase says whether the framing or the route is what
needs the repair.
