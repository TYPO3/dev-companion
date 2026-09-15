---
date: 2026-09-15T07:37:57+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3_rule_lookup, typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# the processed-image caches, their clean-up and the composer-mode paths have no hint

## Observation

Task: review core change 95814, which moves EXT: image processing out of the FAL fallback storage.

Six questions of the review had no hint, and I answered all six from the checkout. I did not put them to the server. I assumed that no hint covers them, because the fal-processing hint ends at GraphicalFunctions::resize() and says nothing about what resize() writes. That assumption held for the hints I saw, but I did not test it with a query.

What I established from the checkout:
1. The variant name in GraphicalFunctions::resize() is md5(command . cropArea . absolute path . filemtime . frame), and file_exists() is the whole cache (lines 490-503).
2. The FAL row lookup: ProcessedFileRepository finds a row by original uid, task type and sha1(config), behind a per-request runtime cache. AbstractTask::fileNeedsProcessing() validates it against originalfilesha1, a checksum with the mtime, and exists(). GFX settings are not in that checksum (AbstractTask.php:41-52).
3. Clean-up: "Clear processed files" and cleanup:localprocessedfiles start from sys_file_processedfile. Only "Clear typo3temp/assets" removes files under typo3temp/assets/images/.
4. Composer mode: a package outside public/ gets its public URL from _assets/ plus md5 of the package path relative to the project (DefaultPublicPrefix.php:30). The processed variant goes under Environment::getPublicPath() in both modes.
5. Locking: PrepareTypoScriptFrontendRendering locks per page. ImageProcessingService locks per processed file, and only the backend's deferred route uses it. resize() writes in place, so file_exists() is true before convert finishes.
6. Deployment: the absolute path comes from TYPO3_PATH_APP, which the Composer installers write as a literal. A release directory renames every variant.

One statement in the fal-processing hint is now version-bound. It says: "ContentObjectRenderer::getImgResource() and the GIFBUILDER ... take a path or an EXT: path, but they are not a way past FAL." Change 95814, 95817 and 95818 make EXT: paths a way past FAL. If one of them lands, the sentence needs a "before" version.

The rule core/testing/proving-a-rendering came back as a search hit for "review readiness". Its sentence about echo in a functional test gave me the probe technique. I did not read the page whole. The page I wanted for the six questions does not exist. It would be a hint with the id fal-processed-file-cache or a rule core/imaging/processed-image-caching.

## Query

Questions the user asked in the session, in their words: "bitte prüfe wie das caching implementiert ist"; "kannst du auch anzeigen wie die pfade im composermode sind?"; "was passiert wenn processed files geleert werden, werden die neuen locations mit berücksichtigt?"; "wenn wir ein neues deployment der website fahren und neue assets ausrollen, werden diese erfasst und reprocessed?"; "wenn die bilder noch nicht processed sind, und mehrere hits gleichzeitig das system treffen haben wir eine placeholder / lockingstrategie?"; "wie sorgen wir dafür dass processed temporäre assets nicht bis ins unendliche wachsen? sind unsere cache identifier stabil genug?". Files open at the time: typo3/sysext/core/Classes/Imaging/GraphicalFunctions.php, typo3/sysext/core/Classes/Resource/ProcessedFileRepository.php, typo3/sysext/core/Classes/Resource/Processing/AbstractTask.php, typo3/sysext/core/Classes/SystemResource/Publishing/DefaultSystemResourcePublisher.php, typo3/sysext/install/Classes/Service/Typo3tempFileService.php.

## Suggestion

Add a hint, fal-processed-file-cache, with these statements: the key of a FAL variant and what validates it on every render; the key of a resize() output and that file_exists() is its only check; that GFX settings are in neither the row nor the checksum; which Install Tool action and which command clear which directory; that resize() writes in place and no image-level lock exists on the frontend path. Add to the system-resource hint the composer-mode path scheme: the _assets/<md5 of the relative package path> prefix, and that the variant directory is under the public path in both modes. Bind the fal-processing sentence about getImgResource() to the versions before the system-resource processor lands.
