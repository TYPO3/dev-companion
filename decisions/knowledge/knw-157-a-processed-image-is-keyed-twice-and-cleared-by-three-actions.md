---
id: D-KNW-157
title: A processed image is keyed twice and cleared by three actions
date: 2026-09-15
status: open
coveredBy:
  - HintsTest::whatKeysAProcessedImageAndWhatClearsItIsAnswered
---

# D-KNW-157 — A processed image is keyed twice and cleared by three actions

**`fal-processed-file-cache` states the two keys a processed image has, what
makes each stale, which of three clean-ups reaches which, and where the one lock
sits.**

A session that reviewed a core change had six questions about the caches below
image processing and answered all six from the checkout. Its account was right
in five parts and guessed in one, and nothing below `knowledge/` said any of it.

## Evidence

- **The report.**
  [`feedback/2026-09-15-073757`](../../feedback/archive/2026-09-15-073757-the-processed-image-caches-their-clean-up-and.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5`. The six questions are
  quoted in it in the user's words.
- **Probed on 2026-09-15.** `bin/cli hints:probe` with the feedback's questions
  reached `caching`, `fal-processing` and `public-assets`. None states a key, a
  validation, a clean-up or a lock. Step 1a.
- **Read in `.checkouts/` on 2026-09-15, on `12.4`, `13.4`, `14.3` and `main`.**
  `GraphicalFunctions` names its output `md5` over the command, the crop area,
  the absolute source path, `filemtime` and the frame, on every branch.
  `file_exists()` is its only check. `AbstractTask::getChecksumData()` is the
  uid, the type and name with the modification time, and the serialised
  configuration. No subclass adds `GFX`, on any branch, while the docblock asks
  for it. `Typo3tempFileService::clearProcessedFiles()` is
  `ProcessedFileRepository::removeAll()`, and `clearAssetsFolder()` is the only
  code that empties `typo3temp/assets/images/`.
  `CleanUpLocalProcessedFilesService` walks the processing folders of the
  `Local` storages. `ImageProcessingService` locks per processed-file uid, and
  `ImageProcessController` is its only caller on every branch.
- **What moved between the branches.** The reprocess decision sits in
  `FileProcessingService::process()` with `ProcessedFile::isOutdated()` on
  `12.4`, and in `AbstractTask::fileNeedsProcessing()` from `13.4`. On `12.4`
  the tasks' own `fileNeedsProcessing()` returns `false` under a todo. The
  runtime cache in `ProcessedFileRepository` and `--all` on
  `cleanup:localprocessedfiles` exist from `13.4` and `14.3` respectively.
- **The `_assets` hash.** `PackageArtifactBuilder` on `12.4` and `13.4` and
  `DefaultPublicPrefix` on `14.3` and `main` agree. Each takes `md5` of the
  package path relative to the project root, with the slash on both ends.
  `DefaultPublicFolderPrefix` appends the declared folder for anything beside
  `Resources/Public`.
- **The one guess.** The report says the Composer installer writes
  `TYPO3_PATH_APP` as a literal. `vendor/typo3/autoload-include.php` in every
  environment below `.environments/` writes `dirname(dirname(__DIR__))`. The
  consequence it drew still holds, because the absolute source path is in the
  key either way. The hint states the key and the consequence, and nothing about
  the installer.
- **The change under review had not landed.** No commit on `main` at
  `bb8a770990` names change 95814, 95817 or 95818.
  `ContentObjectRenderer::getImgResource()` still resolves through
  `ResourceFactory::retrieveFileOrFolderObject()`. So the `fal-processing`
  sentence the report wants bound holds on every branch today.

## Decided

- **Closed on the spot under `D-FBK-052`.** The run held the evidence from all
  four checkouts, and the change touches `knowledge/` and a test alone.
- **One hint, beside `fal-processing`, rather than a paragraph inside it.**
  `fal-processing` says how a task reaches a processor. What keys the result,
  what makes it stale and what clears it is a second subject with its own
  queries. The two name each other.
- **The `fal-processing` reprocess statement is bound**, `since 13` and
  `until 12`, because it named `fileNeedsProcessing()` for a branch where that
  method returns `false`.
- **The `_assets` derivation goes into `public-assets`**, unbound, because the
  code differs per major and the result does not.
- **Against binding the `getImgResource()` sentence now.** A bound is written
  when a checkout shows both sides of it. The session that finds an `EXT:` path
  reach the pipeline without `ResourceFactory` binds it then.
- **Against the report's sixth statement as written.** The installer detail was
  a guess, and the hint carries what the checkouts show.

## Assumed

- That `ImageProcessingService` has no caller outside the core. A search of the
  four checkouts finds one, and an extension may add another.
- That ImageMagick writes to the output path it is given. The code hands it the
  final name and renames nothing, which is what the hint states.

## Wrong if

- A session reports a stale variant that survived one of the four conditions the
  hint names. Then the checksum or the row lookup carries something the read
  missed.
- One of the three changes lands and an `EXT:` path reaches `GraphicalFunctions`
  without a FAL object. Then the `fal-processing` sentence about
  `getImgResource()` needs an `until`, and `fal-storages-drivers` and
  `fluid-resource-uris` say the same thing about the fallback storage.
- A session in a Composer installation reports a hash that is not the md5 of the
  relative package path. Then a publisher other than the default is in play and
  the hint has to say which.
