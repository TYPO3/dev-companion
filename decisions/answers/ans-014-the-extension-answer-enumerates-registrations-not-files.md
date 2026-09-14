---
id: D-ANS-014
title: 'The extension answer enumerates registrations, not files'
date: 2026-08-02
status: open
coveredBy:
  - ProjectTest::whatAnExtensionDoesNotShipIsAnswered
---

# D-ANS-014 — The extension answer enumerates registrations, not files

**`typo3_extension_describe` lists what an extension registers and never the
files it ships. A registration belongs in that list whichever file declares
it.**

A conformance audit asks for a ships section that names test files by path,
FlexForms, form definitions, route enhancers and the Configuration
subdirectories. Half of that is a file tree, which `glob` answers and the skill
already says to answer that way. The other half is three registration kinds this
answer omits while listing fifteen others, and their omission is not a boundary
anybody chose.

## Evidence

- `feedback/2026-07-31-194510`, re-run on 2026-08-02 through
  `bin/typo3-dev-companion` from this worktree, in
  `/home/benji/projects/site-new`. That is the directory it came from, whose
  `opencode.json` and `.mcp.json` both name this repository's entrypoint.
  `typo3_extension_describe` with `printworks_sitepackage` answers from the
  booted installation.
- The XLF half is in that answer: "Ships: manual none, readme none, tests
  Functional+Unit". Then `Resources/Private/Language/backend_fields.xlf`,
  `backend_layouts.xlf` and `messages.xlf`, each with "source-language de, no
  translations beside it". `artifacts` landed in `fc80db8` (2026-07-31 02:08
  +0200), an ancestor of `420b0ac`. `main` stood there when the report arrived
  nineteen and a half hours later at 21:45 local.
- `feedback/2026-07-31-194825` is the same call three minutes later from the
  same directory, by a different model. It reports that section as the thing
  that let it tell "missing" from "not yet read". The two are the same property
  read from both sides, which is the pair
  [judging.md](../../documentation/records/judging.rst) names.
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
  is why the strength counts as the boundary rather than as a confirmation.
- The file-listing half is somebody else's step, stated.
  `skills/typo3-extension-conformance/SKILL.md` says a surface is in scope
  because the checklist names it and not because the file tree shows it. A file
  list first inverts that. The skill derives the list from the checklist and
  `typo3_extension_describe` and then lets a read answer it. "I had to use glob
  and read to discover the full file tree" is that read.
  `Extension::artifacts()` states the same line from this side. Everything above
  it is what a caller can find more of with a further read. The four below it
  are the ones whose absence has no file to stumble over.
- Three of the six asked for are not that. Each is a registration, each stands
  in a file that does not move, and none is in the answer:
  - `Configuration/FlexForms/Catalogue.xml` and `Teaser.xml`, bound by
    `addPiFlexFormValue()` with a `FILE:EXT:` argument in
    `Configuration/TCA/Overrides/tt_content.php`. `Extension::overrides()`
    already tokenises that file. The comment above
    `Extension::TABLE_FIRST_METHODS` already names that method. It keeps it out
    of the *table* list for the correct reason that its first argument is not a
    table. Nothing else picks it up, so the bound file leaves the answer
    entirely.
  - `Configuration/Sets/Printworks/route-enhancers.yaml`. Core reads that name
    exactly, in
    `.checkouts/14.3/typo3/sysext/core/Classes/Site/Set/YamlSetDefinitionProvider.php:123`,
    beside `settings.definitions.yaml`, `settings.yaml` and `labels.xlf`. The
    answer names the set as `bk2k/printworks (Configuration/Sets/Printworks/)`
    and says nothing about what the directory carries.
  - `Configuration/Form/Printworks/config.yaml`, which registers the form
    storage the extension's `Resources/Private/Forms/ProductRequest.form.yaml`
    lives in.
- The sharpest instance is inside the answer already. The two content elements
  it reports as "no templateName in this extension's TypoScript",
  `printworkssitepackage_catalogue` and `printworkssitepackage_teaser`, are
  exactly the two with a FlexForm. The answer is least informative at the two
  entries whose unread file is what describes them.

## Decided

- The feedback shrinks rather than closes. The XLF half has its answer and goes.
  The three registration kinds stay open and a todo serves them, which is what
  [`D-FBK-017`](../feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
  requires of a judgement that does not archive.
- This entry declines the file-enumeration half and records the reason here
  rather than in the feedback, because it is a boundary and not a gap. Test
  files by path and "Configuration subdirectories not already covered" are a
  tree, and `glob` answers a tree. This answer's job is the part a tree walk
  cannot give: what the files mean, and which of them are not there.
- The three kinds are step 1b of the ladder. The shape is absent, and the answer
  is available from files nobody can get it out of. They touch the tool's
  declared schema, so they go to the queue rather than close on the spot.
  [`R-ANS-014`](../../requirements/answers/ans-014-a-registration-is-answered-wherever-it-is-declared.md)
  records what must hold.
- That todo settles what each of the three is in TYPO3. The session read only
  `route-enhancers.yaml` in core here, and neither the form-set mechanism nor
  the general shape of a FlexForm bound to a plugin. A fix named from this
  position is the copy-down
  [judging.md](../../documentation/records/judging.rst) warns about.

## Assumed

- That the session which reported it called the server this checkout builds.
  Both client entries in that project name it, and `fc80db8` predates the
  report; nothing records what that working tree held at 21:45.
- That one sitepackage is enough to show the gap. One extension showed three
  kinds. Whether the list is three or ten is the todo's first step rather than
  something this run established.
- That a registration kind is worth an entry even where another lookup reaches
  it from the other side. `typo3_configuration_lookup` answers effective
  configuration, which is not the same question as what this extension declares.

## Wrong if

- The three turn out to move in the general case. A FlexForm bound through a
  path the file assembles, a form set that registers at run time. The line above
  would then select nothing reliable, and all the answer can add is what
  [`R-ANS-012`](../../requirements/answers/ans-012-an-answer-that-cannot-read-something-says-so.md)
  says.
- A session with an answer that carries all three still walks the tree with
  `glob` for the same surfaces. The declined half would then be the one the
  session wanted, and the decline here the error.
- A feedback reports the enumeration as noise — an extension whose answer is
  longer than the tree it describes. The boundary would then be in the right
  place and drawn too far out.

## Since then

The strength this entry reads as the other side of the same property is not the
confirmation it looks like. The four artifacts are the exception this entry
names, the ones whose absence has no file to stumble over. The exception holds
for three of them. The answer renders three present or absent in one line, and
the fourth only where there is something to render.
