---
description: >-
  Who a core commit message is written for, and the subject line, the body and the trailers it carries, Gerrit's own among them.
whenToUse: >-
  When writing or amending the message of a patch to the core, which is the only repository these rules describe.
hints: []
---

# TYPO3 Core Commit Message Rules

Source:
https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html

TYPO3 core commit messages are part of the contribution workflow and are checked
by tooling. Keep this document aligned with the official TYPO3 Core Contribution
Guide.

## Who Reads It

- A commit message is read by a person who wants to know what the commit did —
  in `git log`, in a blame, in a review.
- Write it in plain English, and only as long as that answer needs.
- The diff carries the detail, so the message does not repeat it. Nothing here
  asks for a full account of the change.

## Summary Line

- Start with one of `[BUGFIX]`, `[FEATURE]`, `[TASK]`, or `[DOCS]`.
- Add `[!!!]` before the keyword for breaking changes.
- `[!!!]` is the only prefix a merge-ready subject carries.
- Do not use `[SECURITY]` unless this is handled by the TYPO3 Security Team.
- Keep the whole subject line below 52 characters if possible. The keyword
  prefix is counted, so `[BUGFIX]` spends nine of them and leaves the summary
  43.
- Use imperative present tense, for example `Fix`, `Add`, `Improve`, or
  `Remove`.
- Describe what the patch changes, not what used to be broken.
- Start the summary text after the keyword with a capital letter.
- Avoid `EXT:some_extension` in the subject when the changed files already make
  the extension context clear.

## Work in Progress

- `[WIP]` and `[POC]` go before the keyword, where `[!!!]` goes:
  `[WIP][BUGFIX] Parse User TSConfig for user settings`. They mark a state, not
  a kind of change — work in progress, and proof of concept.
- A change carrying one is not offered for merge. Both come off before it is
  merged, and no merged commit carries either.
- `[POC]` is written `[PoC]` as often as not, and the two are the same marker.
- Gerrit says the same thing to the review server rather than to a reader:
  pushing with `%wip` opens the change as work in progress.

## Body

- Separate the summary and body with a blank line.
- Keep the body brief and focused on what changed and why.
- Do not repeat full reproduction instructions from the Forge issue.
- Wrap body lines manually at 72 characters.
- The body is prose. A list in it enumerates items the change touched — the
  classes it moved, the rules it dropped — and the argument is written as
  sentences around it. Of the last thousand merged commits on `main` carrying a
  body, read on 2026-09-02, 115 hold a list line and one is written as a list.

## The Longest Line The Hook Accepts

A line of 72 characters passes and a line of 73 is refused.
`Build/git-hooks/commit-msg` draws that boundary in `checkForLineLength()`,
which is `grep -q -E '^[^#].{72}'`: one character for `[^#]` and 72 after it, so
73 is the shortest line that matches.

- Every line is measured, not only the body. The subject, the trailers and the
  `Change-Id:` the hook writes are read the same way, and a line beginning with
  `#` is the only exemption.
- The checkout's own `AGENTS.md` puts it as "no line of the message may reach 72
  characters", one character stricter than the hook it cites. Where the two are
  read together, the hook is what refuses a commit.
- `typo3_commit_message_guide` wraps to that width, so a draft it returns with
  no `body-line-too-long` check is one the hook takes as it stands.
- The check is the same on every maintained branch, so which one a patch targets
  does not change it.

## Relationships

- `Resolves: #12345` is required.
- Multiple resolved issues need one `Resolves:` line per issue.
- `Related: #12345` is optional and cannot replace `Resolves:`.
- `Releases: main, 13.4` lists target versions.
- Do not create or change `Change-Id:` manually. The commit hook creates it.
  Keep it when amending an existing Gerrit patch set.

## Release Targets

- `Releases:` names branches: `main` and the maintained release lines, comma
  separated.
- Which lines those are changes with every LTS release and every support window
  that closes, so it is a lookup and not a rule to remember.
  `typo3_commit_message_guide` names them where the trailer is left out, and
  reports a branch that is out of regular support as an error.
- A line out of regular support still has releases, and the ELTS partners make
  them. A patch pushed to Gerrit is not one of them.
- The branch list in a checkout does not answer this. `git branch -r` reaches
  back to `TYPO3_3-6`, and counting `Releases:` trailers on recent commits
  samples what other changes needed rather than what this one does.
- Which of the maintained lines a change reaches is your reading of where the
  defect is, and the trailer is the claim you verified it there — by reading the
  changed file on each branch you name.
- A feature, a deprecation and a breaking change go to `main`. A backport of one
  happens and is the release managers' call: `origin/main..origin/13.4` carries
  three `[FEATURE]` commits against 969 `[BUGFIX]` ones, and
  `origin/main..origin/14.3` carries none at all.
- A bug fix and a task go to `main` and to the one release line back from it.
  That the defect is present on an older maintained line does not put that line
  in the trailer: the older lines take priority bug fixes and grave or
  security-relevant defects, and naming one for an ordinary fix asks a merger to
  cherry-pick onto a line the change was never meant for.
- So the trailer is two readings rather than one. Where the defect is, on each
  line, is the first; whether its severity earns an older line is the second,
  and it is a judgement you state rather than something that follows from the
  first.
- What a release branch carries since it was cut is `origin/main..origin/14.3`.
  A plain log on that branch, or a `--since` window over it, answers about the
  history shared with `main` and reports every change made before the branch
  existed as if the branch had taken it: the same count that is 0 one way is 188
  the other. The two differ by one operator and give opposite answers about
  whether features reach a release line.

## The Trailers A Core Commit Carries

A core commit message carries `Resolves:`, `Related:`, `Releases:`,
`Signed-off-by:` and the `Change-Id:` the hook writes, and no trailer beyond
those five.

- `Signed-off-by:` is set on every TYPO3 core patch. `git commit -s` writes it
  from your git identity, `git config format.signOff true` makes that the
  default, and `git commit --amend -s` adds it to a patch set that went out
  without one.
- The line is the Developer Certificate of Origin rather than a second author
  field. Signing it says the contribution may be published under GPL v2 and
  violates nobody else's rights.
- That warranty is yours whatever wrote the code. An AI tool does not divide it
  and does not diminish it, and a contribution nobody will stand behind is one
  that is not merged.
- The rule comes from the TYPO3 Association board's statement on GPL and
  AI-generated code of 2026-07-20, which recommends the certificate as what
  makes a contributor's provenance representation explicit and auditable. The
  board put it as a recommendation to consider; this project requires it.
- `Co-Authored-By:` is not set, and neither is a trailer naming the agent or the
  session a patch was written in. Who held the keyboard is the author field and
  the review, not a line in the message.
- Changing any of this is the maintainer's call. A session that believes a
  trailer is owed asks before writing one, rather than reading the answer out of
  whichever file it happens to be holding.
- The checkout asks for the trailer in one place and checks it nowhere. The
  core's `AGENTS.md` says to sign off every commit and names the certificate,
  the one line in `Build/git-hooks/commit-msg` naming `Signed-off-by:` deletes
  it, the official Contribution Guide's appendix lists the trailers and stops
  before it, and `CONTRIBUTING.md` is silent.
- The merged history is the practice the rule replaces: it carries the sign-off
  on about one commit in a hundred on `main`, which
  `git log -500 --format=%b | grep -c '^Signed-off-by:'` counts. So a patch
  without one is struck by a reviewer rather than rejected by a check.

## What The Commit Hook Writes

`Build/git-hooks/commit-msg` adds the `Change-Id:` line, and it reads a stripped
copy of the message to decide whether to.

- The copy is the message with every `Signed-off-by:` line, every comment line
  and any diff taken out. Where that leaves nothing, the hook returns and no
  `Change-Id` is written.
- So a message that is only a sign-off counts as an empty one and gets no id,
  which is why the line is removed at all. Gerrit refuses a change that carries
  no `Change-Id`.
- The id is hashed from that same copy, so the trailer never enters it.
- An existing `Change-Id:` is left standing whatever else is amended, because
  the hook returns as soon as it finds one. Amending a sign-off in or out keeps
  the patch set valid.

## Breaking Changes

- Breaking changes must use `[!!!]` before the keyword.
- Breaking changes must be documented with a changelog RST file.
- Breaking changes should usually target `main`.
- A removed or narrowed PHP API gets an extension scanner matcher entry in the
  same patch, below `typo3/sysext/install/Configuration/ExtensionScanner/Php/`.
  How the removed member is written where it is used decides the file:
  - `MethodCallMatcher.php` — an instance method.
  - `MethodCallStaticMatcher.php` — a static method.
  - `PropertyPublicMatcher.php` — a removed public property.
  - `PropertyProtectedMatcher.php` — a public property that became protected.
  - `ClassNameMatcher.php` — a whole class or interface.
- Visibility routes a property and never a method. The method matchers are a
  weak match on the method name where it is used, and they do not resolve the
  class, so they cannot see one. A method that is protected, or that has become
  protected, is entered where a public one is.
  `RendererRegistry->getRendererInstances` went from public to protected in
  `Breaking-110277`, and it stands in `MethodCallMatcher.php`. The list above
  has no row for a protected method because none is needed, and that absence
  says nothing about whether an entry is owed.
- An entry is keyed by the fully qualified name with `->` or `::` and carries
  `restFiles`, naming the changelog file that removed it. The method matchers
  add `numberOfMandatoryArguments` and `maximumNumberOfArguments`. A member
  deprecated before it was removed lists both changelog files.
- Every Breaking and Deprecation entry carries exactly one of `NotScanned`,
  `PartiallyScanned` and `FullyScanned` in its `.. index::` line, and that tag
  is the claim those entries have to back: `FullyScanned` says every item the
  changelog entry names can be found. The scanner reads PHP, so what an entry
  changes in TypoScript, TCA, YAML or JavaScript is what leaves it partially
  scanned.
- `./Build/Scripts/runTests.sh -s checkExtensionScannerRst` checks that the
  changelog files the matchers name exist, and nothing checks the other
  direction. A missing entry surfaces when somebody audits the matcher files
  against the changelog.

## Changed Signatures

A signature change is the third breaking move beside removing and narrowing, and
adding a parameter is one — an optional parameter included. A public or
protected method on a class that is not final is an override point, and every
subclass declaring the old signature fatals as it loads.

- The obligation follows from the member being overridable rather than from an
  override anybody found. `Breaking-101133` files a changed parameter of
  `IconFactory->getIcon()` against "custom extensions extending the method", and
  `Breaking-110218` declares `LogRecord` final while calling the affected
  installations very unlikely.
- A member marked `@internal` takes an `Important` instead. `Important-107342`
  extended `FormPersistenceManagerInterface::listForms()` by two optional
  arguments and reached `13.4.x` on that ground. An entry is still owed; only
  its type changes, and that is what lets such a change reach a release line.
- Neither owes a matcher, and both are `NotScanned`. A matcher is keyed on where
  a member is called, an override is not a call, and an added optional parameter
  leaves every existing call site valid.
- So it decides the target branch before anything else. A maintained release
  line takes no breaking change, so a fix owed to one cannot carry the signature
  change at all, and the shape that reaches it is the additive one: a method of
  its own, or the state handed over on something the callee already receives.
  Declaring the class or the method final first is no cheaper, because that is
  itself a breaking change.
- Nothing in a core checkout reports any of this. No core class has to override
  the method, so the unit, functional, coding-guidelines and static-analysis
  runs are all green on the change.
- A member promoted from protected to public is not a signature change and owes
  none of it. The core promotes one in a plain `[TASK]` or `[BUGFIX]` commit
  carrying no changelog file, and such a patch reaches a maintained release
  line, which a breaking change cannot. The changelog holds the move in the
  other direction only: `Deprecation-86047` narrows public members of
  `TypoScriptFrontendController` to protected. A subclass that re-declares the
  member as protected fatals with "Access level … must be public", and the core
  files nothing for that either.

## Deprecations

- Deprecations must not use `[!!!]`.
- Deprecations may only use `[TASK]` or `[FEATURE]`.
- Deprecations must be documented with a changelog RST file.
- Deprecations need migration guidance and may need extension scanner
  considerations.
- All of the above is the authoring side. Reading it — what a given version
  deprecated, and what that means for code that uses it — works the other way
  round: the changelog files below `Documentation/Changelog/` of the core
  package and the matchers below the install package's
  `Configuration/ExtensionScanner/Php/` are what an installation is checked
  against, by the Extension Scanner in the Install Tool. Both directories ship
  with a Composer installation.

## The Changelog Entry a Message Announces

- A breaking change, a deprecation, a feature and anything else that may require
  manual action carry a changelog file in the same patch. A casual bug fix
  carries none, because the commit message is what informs the reader.
- Which of the four types that is, which directory the file goes into when the
  change is backported, what it is named and what reports one that is wrong is
  one page: `typo3_rule_lookup` with `documentId="core/contribution/changelog"`,
  which also stands as
  `typo3://guides/core/contribution/changelog`.
- The `Releases:` trailer and that directory are one reading: the oldest branch
  the trailer names is the release whose directory the file goes into.
