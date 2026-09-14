---
id: D-KNW-028
title: 'How a file becomes a processed one is a subject this server owns'
date: 2026-08-02
status: open
---

# D-KNW-028 — How a file becomes a processed one is a subject this server owns

**Which processor claims a file, and in what order, is inside this server's
boundary and absent from it. So the feedback shrinks to that half and goes to
the queue.**

The feedback asks for the answer in `typo3_project_describe`, which is the one
place it cannot go. The value assembles at runtime and that answer reads files.
The actual gap is a statement in `knowledge/`.

## Evidence

- The strength reproduces, except the half it credits. Run again on 2026-08-02
  over stdio through `bin/typo3-dev-companion` from
  `/home/benji/projects/typo3-cms`, the directory it came from.
  `typo3_project_describe` opens with "core-checkout, TYPO3 15.0.0-dev, PHP ^8.5
  declared and 8.5 in DDEV". Then no extensions, no sites, and four
  `gerrit:setup` commands that all answer `runs: unknown`. Neither the text nor
  the data names a processor or a task type.
- The feedback contradicts itself on exactly that point. It credits the tool
  with a return of "the declared processors and processingTaskTypes", lists the
  processing classes it read by hand two paragraphs later, and then asks for the
  map. This is the third corpus in which a strength's credit lands in the wrong
  place;
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
  records two. It is the first where the credit goes to no tool at all.
- The sibling from the same debrief asks for the same thing, more precisely.
  `feedback/2026-08-01-114807` arrived two minutes and forty-one seconds later,
  same model and same directory. Its suggestion reads "Consider adding
  `processingTaskTypes` and `SYS.fal.processors` to the `typo3_project_describe`
  output". Its transcript names the seven files it opened instead —
  `GraphicalFunctions`, `LocalImageProcessor`, `SvgImageProcessor`,
  `ThumbnailViewHelper`, `PreviewNotAvailable.svg`,
  `DeferredBackendImageProcessor` and `PreviewProcessing` — over about ten
  `read_file` calls. That feedback has a card of its own, in hand elsewhere.
- The value is not a project fact, and no manifest declares it. Both keys live
  under `TYPO3_CONF_VARS`, assembled from `DefaultConfiguration.php` and every
  extension's `ext_localconf.php`. `typo3_project_describe` reads
  `composer.json`, `package.json`, `config/sites/` and `.ddev/config.yaml`, and
  its own description says it reads files only, so it answers on a fresh clone.
- It is also a constant of the core. Both keys are identical on
  `.checkouts/12.4`, `13.4`, `14.3` and `main`. Four processors,
  `SvgImageProcessor`, `DeferredBackendImageProcessor`, the OnlineMedia
  `PreviewProcessing` and `LocalImageProcessor`, in `before` and `after` order.
  And two task types, `Image.Preview` and `Image.CropScaleMask`. A constant of
  the core is knowledge rather than a property of the project in front of the
  caller.
- Where the question really is about the installation, a tool answers it and the
  route already points there. `typo3_configuration_lookup` reads an effective
  `TYPO3_CONF_VARS` path. `knowledge/server-scope.json` routes "Needing a
  configuration value as it really is at runtime" to it, and `skills/base.md`
  names it under "Two kinds of lookup". Its answer is more than the suggestion
  asks for, because a files-only reader could only report the shipped default.
- That tool needs the installation, and a core checkout may not offer one.
  Called from the same directory on 2026-08-02 with `SYS/fal/processors` and
  with `SYS/fal/processingTaskTypes`, it answers unsupported. The cause is
  `installation-not-answering`: "the DDEV project is paused". A patch review
  does not boot a checkout, so the runtime route is not always open.
- The knowledge half is empty. `bin/cli hints:probe` on the feedback's own query
  reaches nothing. Six processor-shaped queries reach nothing relevant. "image
  processing", "thumbnail generation", "ProcessedFile processing task",
  "GraphicalFunctions ImageMagick", "online media preview YouTube thumbnail",
  "register a custom image processor". `ProcessedFile`, `Image.Preview`,
  `LocalImageProcessor`, `processingTaskTypes`, `SvgImageProcessor`,
  `GraphicalFunctions` and `_processed_` occur nowhere below `knowledge/` or
  `skills/`.
- The one hint the subject does reach stops short of it.
  `file-abstraction-layer` in `knowledge/architecture-hints/php.json` carries
  five statements, about storages, drivers, `ResourceFactory`, the
  `MetaDataAspect` and functional coverage. None of them is about a processor.
  The only "processor" the corpus holds is `frontend-dataprocessors`, which is a
  different subsystem a lexical query can hand back instead.
- It is inside the boundary rather than outside it. `doesNotCover` in
  `knowledge/server-scope.json` excludes "the core source itself: API
  signatures, TCA of a table, existing implementations". That is the line-level
  read this session also did and does not ask for. The architecture topic beside
  it covers "conventions per subsystem", from "what a change to the subsystem
  has to satisfy" and "how the mechanism is used". The processor chain is the
  second of those.

## Decided

- Step 1a of the ladder on the processor half, and in the queue rather than
  closed on the spot. What lands is a corpus statement about TYPO3, read across
  the checkouts, and this run established none of it.
- The suggestion as it stands meets a refusal, on the boundary
  [`D-ANS-011`](../answers/ans-011-a-scope-answer-states-what-a-manifest-declares.md)
  draws. A scope answer states what a manifest declares, and no manifest
  declares `SYS/fal/processors`. A files-only reader that copies
  `DefaultConfiguration.php` would report the shipped default as the project's
  configuration. That is wrong on the one installation where the question
  matters: the one that registers a processor of its own.
- Not step 2 and not step 3 for the runtime half. The routing entry exists, the
  base skill names the tool, and nothing here records what this session saw.
  Walking that again would give one gap a second entry.
- The feedback shrinks rather than goes to the archive. Everything the strength
  names except the processors reproduces, a keep is not work, and the todo is
  what remains open behind it.
- Where the statement goes is the todo's and not this entry's.
  `file-abstraction-layer` is the candidate the todo starts from. The choice
  between an extension of it and a hint of its own needs the research that
  produces the statement.

## Assumed

- That the two keys are the whole of what the session wanted. It names them and
  nothing else in either file, and the classes it read by hand are the ones
  those keys point at.
- That one session wrote both feedback. They share a directory, a model, a
  subject and two minutes, and nothing in a feedback records a session.
- That a core checkout is commonly worked unbooted. The DDEV project stands
  paused on this machine today, and nothing records whether it did on
  2026-08-01.

## Wrong if

- A read of `canProcessTask()` across the four checkouts finds that the dispatch
  differs by major. The todo plans one statement with a range, and it would then
  be one statement per line.
- A session asks `typo3_configuration_lookup` for `SYS/fal/processors` against a
  booted installation and still reads the classes by hand. The list would then
  not be the gap, and the hint would aim at the wrong half.
- The refused suggestion turns out to have been right. A caller in an unbooted
  core checkout needs the shipped default and has nowhere else to get it. The
  hint written instead does not state it. That would leave that caller exactly
  where this session was.
- `feedback/2026-08-01-114807` gets its judgement and lands somewhere other than
  here. The pair above would then be a view of two files rather than one
  debrief's report.

## Since then

Written the same day, and one sentence of the evidence is off. The processor
configuration is not identical on all four majors; one branch names a second
processor in its order. The order that results is the same either way, so the
hint carries no range. What was wrong was the read of the declaration rather
than the conclusion.

The boundary had a visit from below the same day. A session that had asserted
twice that image processing requires a FAL object filed the case the new hint
answers up to the processor and not after it. `D-KNW-042` takes that half on.
