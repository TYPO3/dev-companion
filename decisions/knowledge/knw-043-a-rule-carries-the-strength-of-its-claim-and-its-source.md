---
id: D-KNW-043
title: 'A rule carries the strength of its claim and its source'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aFluidResourceUriTaskIsAnsweredWithWhoAppliesCacheBusting
---

# D-KNW-043 — A rule carries the strength of its claim and its source

**Where the corpus states what an API is for, it names the source the rule came
from and how strongly that source puts it.**

In force in code, a caveat in a docblock and advice in prose are three different
claims, and two neighbour APIs regularly make different ones. The session that
reported accepted a maintainer's "you *must not* use `f:image` for anything but
FAL resources". Then it found the checkout in contradiction of it. The corpus
was not the innocent party. The one sentence it held on the subject flattened
`f:image` and `f:uri.image` into a single documented rule, and only
`f:uri.image` carries it.

## Evidence

- The feedback. `feedback/2026-08-02-144814`, `claude-opus-5[1m]`, from
  `/home/benji/projects/typo3-cms`,
  `tool: typo3_task_guide, typo3_documentation_lookup`. A maintainer's tracker
  comment quoted as a rule and called correct in the session's first assessment.
  The checkout disproved it only because the user asked what the session made of
  the statement.
- The corpus carried the same over-strong claim. `fluid-resource-uris` in
  `knowledge/hints/fluid.json` read this. "f:image and f:uri.image are not on it
  — they resolve through FAL and the Extbase ImageService, and **their** own
  class documentation sends an extension resource to f:uri.resource instead".
  Read in `.checkouts/` on 12.4, 13.4, 14.3 and `main` alike, that is true of
  one of the two:
  - `Uri/ImageViewHelper`: "This ViewHelper should only be used for images
    within FAL storages, or where graphical operations shall be performed". And
    "For extension resource files, use `<f:uri.resource>` instead".
  - `ImageViewHelper` — neither sentence, and its own first example is
    `<f:image src="EXT:myext/Resources/Public/typo3_logo.png" width="100c" />`,
    on all four.
- What both docblocks do say is weaker than a prohibition and is about
  stability. Image operations on non-FAL files "may be changed in future TYPO3
  versions", because each creates a "fake" FAL record.
- The core's own suite covers the form.
  `typo3/sysext/fluid/Tests/Functional/ViewHelpers/SvgImageViewHelperTest.php`
  renders `<f:image src="EXT:svg_image_test/…">` in six data-provider cases,
  width, height, `crop`, `fileExtension="png"`. That is on 13.4, 14.3 and
  `main`; the file does not exist on 12.4.
- The route behind it is already stated correctly one file away.
  `fal-storages-drivers` in `knowledge/hints/fal.json` says uid 0 is the
  fallback and that `ResourceFactory::retrieveFileOrFolderObject()` still
  resolves an `EXT:` path through it. It says the source marks the route for
  removal. That is `@todo` on `ResourceFactory` lines 195 and 212 of `main`. So
  the *reason* for the discouragement was here and the *strength* of it was not.
- Nothing reached the session's question.
  `bin/cli hints:probe "must not use f:image for anything but FAL resources"`
  matched no hint, 23 back as the index. The feedback's own `Query` line reached
  `fluid-viewhelpers`, `system-extension-boundaries`, `core-tests` and
  `fal-basics`, and neither of the two hints that bear on it.
- The instructions already draw the line this feedback found, and draw it around
  three artifacts. `knowledge/server-scope.json` sends the caller to the
  checkout for "what changed, which branch you are on, and whether a path still
  exists". `skills/base.md` extends it to identifiers: "Verify each identifier
  that comes back in the checkout". A behavioural rule is on neither list.

## Decided

- The corpus states the strength and the source when it states such a rule, and
  the two image ViewHelpers stand apart. `fluid-resource-uris` loses the
  flattened clause and gains a version-independent statement. An `EXT:` path in
  `f:image` works and has a test, `f:uri.image`'s docblock is what carries the
  FAL-only sentence. The shared caveat is about stability rather than support,
  and the fallback-storage route marked for removal is what the advice against
  it is about.
- It stands as a statement about TYPO3, not as a rule about how a session reads
  a tracker, the boundary
  [`D-FBK-024`](../feedback/fbk-024-a-feedback-about-the-callers-conduct-toward-its-user-names-no-surface.md)
  draws. The claim is what the code, the tests and the docblocks say, and the
  reader draws the conclusion about the comment.
- The statement names what the over-strong version gets wrong, rather than
  quietly states the right thing beside it,
  [`D-KNW-011`](knw-011-a-rule-that-names-a-defect-names-its-correction.md). A
  session arrives at this hint with the "must not" in hand, and a hint that does
  not contradict it leaves it in place.
- `appliesTo` gains `f:image`, `extension resource` and
  `EXT: path in a template`. The question comes in the words of the rule rather
  than in the words of the API, `R-KNW-002`. The probe that matched nothing now
  reaches `fluid-resource-uris` first.
- No decision here on the feedback's third suggestion. That is that guidance
  tell a session a rule quoted from a tracker or from prose docs is a claim to
  verify against the checkout. That orders a task rather than states a fact. So
  it is a step of the core-contribution creation order
  ([`D-SKL-005`](../task-skills/skl-005-core-contribution-earns-two-task-skills.md)),
  whose assessment half already comes from this cluster. The feedback stays open
  behind it, cut to that half. It gains no card that would carry a quarter of an
  order somebody else writes.
- Nothing on another branch changed.

## Assumed

- That the flat clause is what carried the strength, rather than a hint that was
  merely silent. Nothing measured a session that reads the corrected sentence.
  What stands known is that the wrong clause and the reported mistake say the
  same thing.
- That the statement holds on 12.4 as written. The docblock example and the
  fallback route are there, the functional test is not. So "the core covers that
  form" comes off 13.4 and later. The statement names the test rather than a
  version, which is what keeps it honest if the file moves again.
- That `f:uri.resource` is the right thing to send a template to for a resource
  that needs no graphical operation. That is `f:uri.image`'s own docblock and
  the System Resource API's shape, not a preference formed here.

## Wrong if

- A session reads the statement and still reports "must not" as the rule. The
  contradiction was then not the gap, and what remains to suspect is that a
  tracker comment outweighs a hint whatever the hint says. That is the skill
  step this entry declined to write.
- The `EXT:` branch of `ResourceFactory::retrieveFileOrFolderObject()` goes, or
  `SvgImageViewHelperTest` no longer covers `EXT:` sources. The statement then
  describes a version boundary and needs a `since`/`until` rather than none. The
  two `@todo` markers say this is the likelier of the failures.
- `ImageViewHelper` gains the sentence `Uri/ImageViewHelper` carries. The flat
  clause would have been early rather than wrong, and the statement becomes one
  about a boundary that closed.
- Another rule of this shape turns up at full strength in the corpus with no
  source on it. One correction is then a fix and not a rule. The need is a check
  over the corpus rather than a sentence in one hint.

## Since then

The half deferred above landed on 2026-08-03. "Verify in the checkout every rule
the issue quotes" is the fourth step of `typo3-core-patch-development`'s
assessment, before the reproduction. It names the three strengths this entry
separates. `R-SKL-015` holds it, and `feedback/2026-08-02-144814` sits in the
archive on both halves. What remains here is the first **Wrong if**. A session
that reads the corrected statement and still reports "must not" says the hint
was not the gap.
