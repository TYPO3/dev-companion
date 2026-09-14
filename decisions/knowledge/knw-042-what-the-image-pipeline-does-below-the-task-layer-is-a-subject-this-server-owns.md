---
id: D-KNW-042
title: 'What the image pipeline does below the task layer is a subject this server owns'
date: 2026-08-03
status: open
---

# D-KNW-042 — What the image pipeline does below the task layer is a subject this server owns

**Where FAL stops in the image pipeline is inside this server's boundary and
absent from it. So the feedback shrinks to that half and goes to the queue at
`normal`.**

The task layer unwraps to a local path and everything below it takes path
strings, and nothing in the corpus says so. What it says about images instead
stops at a FAL boundary each time, and never says that the boundary is one. So a
session that asks whether FAL is a requirement reads the last thing it hears as
the foundation. This one did, twice, as an argument for what could not change.

## Evidence

- Re-run on 2026-08-03 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own query reaches `fal-processing` at
  `appliesTo(16) + text(211)` and `fal-basics` at `appliesTo(3) + text(294)`. It
  reaches nothing else. `fal-processing` stops at the processor by its own
  title, "Which Processor Claims a File, and in What Order". Neither hint says
  what a processor does with the file once it has claimed it.
- Neither hint existed on the feedback's day. `b1d6418` wrote `fal-processing`
  on 2026-08-03 and the feedback carries the stamp 2026-08-02T14:49:02, so the
  corpus did not mislead this session. It was silent, and after a day of work on
  exactly this subject it is still silent on the half the session got wrong.
- The words are absent. `GraphicalFunctions`, `getForLocalProcessing`,
  `imageMagickConvert`, `getImgResource` and `GIFBUILDER` occur nowhere below
  `knowledge/` or `skills/`.
- What the feedback claims about TYPO3 holds, read in `.checkouts/main` at
  `c71b2bdb2f`, 15.0.0-dev. `LocalImageProcessor::processCropScaleMask()` is the
  single line `$task->getSourceFile()->getForLocalProcessing(false)`, and
  everything under it takes `string $originalFileName`; `processPreview()` does
  the same at line 382. `GraphicalFunctions` declares
  `resize(string $sourceFile, …)`, `imageMagickConvert($imagefile, …)`,
  `mask(string $inputFile, …)` and `getImageDimensions(string $imageFile, …)`,
  all on paths. `ContentObjectRenderer::getImgResource()` is at line 3332 and
  `Frontend\Imaging\GifBuilder` at line 54 of its own file.
- One correction to the report, which changes nothing it concluded. The unwrap
  is not the first step of `processTask()`: that method delegates through
  `processTaskWithLocalFile()`, and the unwrap is in the two helpers below it.
- A second session reached for the same class from a different task.
  [`R-KNW-048`](../../requirements/knowledge/knw-048-which-processor-claims-a-file-is-answered.md)
  records a patch review of 2026-08-01 that read `GraphicalFunctions` by hand
  among seven core classes. Nothing below `knowledge/` said which of them runs
  when. That review wanted the order and this session wanted the signature, and
  neither could reach the file at all.
- The `EXT:` half of the suggestion has an answer, from two hints that also
  arrived on 2026-08-03. `fal-storages-drivers` states at `since: 14` that uid 0
  is the fallback and that `ResourceFactory::retrieveFileOrFolderObject()`
  resolves an `EXT:` path through it. It states that the source marks the route
  for removal. The two `@todo` comments are still there, at lines 195 and 212.
  `fluid-resource-uris` states at `since: 14` that `f:image` and `f:uri.image`
  are not on the System Resource API and resolve through FAL and the Extbase
  ImageService.
- Both are reachable on an image-shaped query.
  `bin/cli hints:probe "ImageViewHelper src EXT: package resource"` returns
  `fal-storages-drivers` at `text only(109)`.
  `"f:image with an EXT: path resolves through the fallback storage"` returns
  `fluid-resource-uris` at `text only(101)`. So this is not step 2: the
  statement sits where the task passes.
- It sits just below the boundary
  [`D-KNW-028`](knw-028-how-a-file-becomes-a-processed-one-is-a-subject-this-server-owns.md)
  drew. That entry took the dispatch, which processor claims a file, and left
  what the claim leads to unsaid. That is the line this feedback walks up to
  from the other side.

## Decided

- Step 1a on the pipeline half, and queued rather than closed on the spot. What
  lands is a statement about TYPO3 read across `.checkouts/`, and this run read
  one branch to check four assertions.
- Trimmed rather than archived. The `EXT:` and storage-0 part of the suggestion
  is in the corpus and in reach. So it leaves the feedback with the two hints
  that answer it named in its place. The rest stays open behind the card.
- `normal` rather than the `low` the card arrived at. Two sessions from two task
  shapes reached for `GraphicalFunctions` and found nothing. The assertions this
  one built on the silence carried what it told its user was fixable.
- Not `high`. Nothing waits on it, the subject has half its cover already, and
  the corpus states no falsehood; it stops early.
- Not the suggestion's own wording. Its author read four core classes for one
  task and guessed about this repository, as
  [`judging.md`](../../documentation/records/judging.rst) says every suggestion
  does.
- Where the statement goes is the todo's. `fal-processing` is the candidate it
  starts from. Whether the entry points beside FAL belong there or in a hint of
  their own needs the research that produces the statement.

## Assumed

- That the silence is what produced the wrong assertion, rather than the model's
  own prior. Nothing distinguishes the two from here, and the lever is the same
  either way.
- That the path-based entry points are worth a statement at all. They are old
  API a session may never touch. What makes them evidence is the inference they
  disprove rather than a caller who should use them.
- That one session wrote this feedback and the ten beside it. They share a
  directory, a model, a subject and nine minutes, and nothing in a feedback
  records a session.

## Wrong if

- The unwrap point differs across `.checkouts/12.4`, `13.4` and `14.3`. The todo
  plans one statement with a range, and it would then be one statement per line.
- `getImgResource()` or `GifBuilder` turns out to be on the way out in 15. "FAL
  is one entry point among several" would then be a statement about the past,
  and the hint would teach a route nobody should take.
- The sibling `feedback/2026-08-02-144814`, in hand on its own branch today,
  lands here as well. The two would then be one gap on two cards, and one of
  them should carry both under a `**Serves:**` line that names the other.
- A session reads the written statement and still argues from FAL necessity. The
  gap would be in the route rather than in the corpus. This entry would have
  answered the cheap rung of the ladder for a step-3 problem.

## Since then

Read across all four checkouts, and one premise the todo carried is wrong. The
two entry points said never to hold a FAL object do wrap a path into one. They
take paths, which is what the feedback saw. What is in reach without FAL is the
graphics class itself, so the hint states the correction rather than the
suggestion's wording. The conclusion the feedback drew still stands.

Half the first **Wrong if** fires. The unwrap call is the same on all four and
the class it sits in moved. The two helpers fold into one on the newer majors.
So it is one statement and two bound pairs rather than one per line. The second
does not fire. It went into the hint that exists rather than one of its own. The
query that produced the feedback already lands there first, and a session that
reads the dispatch is the one that infers necessity.
