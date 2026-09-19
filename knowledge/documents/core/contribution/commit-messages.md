---
description: >-
  Who reads a core commit message, and the subject line, the body and the trailers it carries, Gerrit's own among them.
whenToUse: >-
  When you write or amend the message of a patch to the core, which is the only repository these rules describe.
hints: []
---

# TYPO3 Core Commit Message Rules

Source:
https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html

A TYPO3 core commit message is part of the contribution workflow, and tools
check it. Keep this document in line with the official TYPO3 Core Contribution
Guide.

## Who Reads It

- A person who wants to know what the commit did reads a commit message. They
  read it in `git log`, in a blame, in a review.
- Write it in plain English, and only as long as that answer needs.
- The diff carries the detail, so the message does not repeat it. Nothing here
  asks for a full account of the change.

## Summary Line

- Start with one of `[BUGFIX]`, `[FEATURE]`, `[TASK]`, or `[DOCS]`.
- Add `[!!!]` before the keyword for a breaking change.
- `[!!!]` is the only prefix a merge-ready subject carries.
- Do not use `[SECURITY]` unless the TYPO3 Security Team handles the change.
- Keep the whole subject line below 52 characters if possible. The keyword
  prefix counts, so `[BUGFIX]` spends nine of them and leaves the summary 43.
- Use the imperative present tense, for example `Fix`, `Add`, `Improve`, or
  `Remove`.
- Describe what the patch changes, not what used to be broken.
- Start the summary text after the keyword with a capital letter.
- Avoid `EXT:some_extension` in the subject when the changed files already make
  the extension context clear.

## Work in Progress

- `[WIP]` and `[POC]` go before the keyword, where `[!!!]` goes:
  `[WIP][BUGFIX] Parse User TSConfig for user settings`. They mark a state, not
  a kind of change: work in progress, and proof of concept.
- Nobody offers a change with one of them for merge. Both come off before the
  merge, and no merged commit carries either.
- People write `[POC]` as `[PoC]` as often as not, and the two are the same
  marker.
- Gerrit says the same thing to the review server rather than to a reader. A
  push with `%wip` opens the change as work in progress.

## Body

- Separate the summary and the body with a blank line.
- Keep the body brief. Say what changed and why.
- Do not repeat full reproduction instructions from the Forge issue.
- Wrap body lines by hand at 72 characters.
- Write the body as short, precise prose rather than as a list.
- Name no count of what the change touched. "In 68 files", "in four spellings",
  "12 occurrences": the number is in the diff. A reviewer asks you to take a
  count out of the body.

## The Longest Line The Hook Accepts

The hook accepts a line of 72 characters and refuses a line of 73.
`Build/git-hooks/commit-msg` draws that boundary in `checkForLineLength()`. That
is `grep -q -E '^[^#].{72}'`: one character for `[^#]` and 72 after it. So 73 is
the shortest line that matches.

- The hook measures every line, not only the body. It reads the subject, the
  trailers and the `Change-Id:` it writes the same way. A line that starts with
  `#` is the only exemption.
- The checkout's own `AGENTS.md` puts it as "no line of the message may reach 72
  characters". That is one character stricter than the hook it cites. Where you
  read the two together, the hook is what refuses a commit.
- `typo3_commit_message_guide` wraps a core body at 71, one under the hook. So a
  draft it returns with no `body-line-too-long` check passes the hook and the
  checkout's `AGENTS.md` alike.
- The check is the same on every maintained branch. So the branch a patch
  targets does not change it.

## Relationships

- `Resolves: #12345` is required.
- Several resolved issues need one `Resolves:` line per issue.
- `Related: #12345` is optional and cannot replace `Resolves:`.
- `Releases: main, 13.4` lists the target versions.
- Do not create or change `Change-Id:` by hand. The commit hook creates it. Keep
  it when you amend an existing Gerrit patch set.

## Release Targets

- `Releases:` names branches: `main` and the maintained release lines, comma
  separated.
- Which lines those are changes with every LTS release and every support window
  that closes. So it is a lookup and not a rule to remember.
  `typo3_commit_message_guide` names them where you leave the trailer out. It
  reports a branch that is out of regular support as an error.
- A line out of regular support still has releases, and the ELTS partners make
  them. A patch pushed to Gerrit is not one of them.
- The branch list in a checkout does not answer this. `git branch -r` reaches
  back to `TYPO3_3-6`. A count of `Releases:` trailers on recent commits samples
  what other changes needed rather than what this one does.
- Which of the maintained lines a change reaches is your judgement of where the
  defect is. The trailer is the claim you verified it there, when you read the
  changed file on each branch you name.
- A feature, a deprecation and a breaking change go to `main`. A backport of one
  happens, and it is the release managers' call. `origin/main..origin/13.4`
  carries three `[FEATURE]` commits against 969 `[BUGFIX]` ones, and
  `origin/main..origin/14.3` carries none at all.
- A bug fix and a task go to `main` and to the one release line back from it. A
  defect present on an older maintained line does not put that line in the
  trailer. The older lines take priority bug fixes and grave or
  security-relevant defects. When you name one for an ordinary fix, you ask a
  merger to cherry-pick onto the wrong line.
- So the trailer is two judgements rather than one. Where the defect is, on each
  line, is the first. Whether its severity earns an older line is the second.
  You state that judgement rather than derive it from the first.
- What a release branch carries since its cut is `origin/main..origin/14.3`. A
  plain log on that branch, or a `--since` window over it, answers about the
  history it shares with `main`. It reports every change made before the branch
  existed as if the branch had taken it. The same count is 0 one way and 188 the
  other. The two differ by one operator and give opposite answers about whether
  features reach a release line.

## The Trailers A Core Commit Carries

A core commit message carries `Resolves:`, `Related:`, `Releases:`,
`Signed-off-by:` and the `Change-Id:` the hook writes. It carries no trailer
beyond those five.

- `Signed-off-by:` is on every TYPO3 core patch. `git commit -s` writes it from
  your git identity. `git config format.signOff true` makes that the default.
  `git commit --amend -s` adds it to a patch set that went out without one.
- The line is the Developer Certificate of Origin rather than a second author
  field. When you sign it, you say two things. The project may publish the
  contribution under GPL v2, and it violates nobody else's rights.
- That warranty is yours whatever wrote the code. An AI tool does not divide it
  and does not diminish it. A contribution nobody stands behind is one nobody
  merges.
- The rule comes from the TYPO3 Association board's statement on GPL and
  AI-generated code of 2026-07-20. The board recommends the certificate as what
  makes a contributor's provenance representation explicit and auditable. The
  board put it as a recommendation to consider; this project requires it.
- Nobody sets `Co-Authored-By:`, and nobody sets a trailer that names the agent
  or the session a patch came from. Who held the keyboard is the author field
  and the review, not a line in the message.
- A change to any of this is the maintainer's call. A session that believes it
  owes a trailer asks before it writes one. It does not read the answer out of
  whichever file it happens to hold.
- The checkout asks for the trailer in one place and checks it nowhere. The
  core's `AGENTS.md` says to sign off every commit and names the certificate.
  The one line in `Build/git-hooks/commit-msg` that names `Signed-off-by:`
  deletes it. The official Contribution Guide's appendix lists the trailers and
  stops before it, and `CONTRIBUTING.md` is silent.
- The merged history is the practice the rule replaces. It carries the sign-off
  on about one commit in a hundred on `main`.
  `git log -500 --format=%b | grep -c '^Signed-off-by:'` counts that. So a
  reviewer strikes a patch without one, and no check rejects it.

## What The Commit Hook Writes

`Build/git-hooks/commit-msg` adds the `Change-Id:` line, and it reads a stripped
copy of the message to decide whether to.

- The copy is the message without every `Signed-off-by:` line, every comment
  line and any diff. Where that leaves nothing, the hook returns and writes no
  `Change-Id`.
- So a message that is only a sign-off counts as an empty one and gets no id.
  That is why the hook removes the line at all. Gerrit refuses a change that
  carries no `Change-Id`.
- The hook hashes the id from that same copy, so the trailer never enters it.
- The hook leaves an existing `Change-Id:` as it stands whatever else you amend.
  It returns as soon as it finds one. An amend that adds or removes a sign-off
  keeps the patch set valid.

## Breaking Changes

- A breaking change must use `[!!!]` before the keyword.
- A breaking change must have a changelog RST file.
- A breaking change should usually target `main`.
- A removed or narrowed PHP API gets an extension scanner matcher entry in the
  same patch, below `typo3/sysext/install/Configuration/ExtensionScanner/Php/`.
  How code writes the removed member where it uses it decides the file:
  - `MethodCallMatcher.php` — an instance method.
  - `MethodCallStaticMatcher.php` — a static method.
  - `PropertyPublicMatcher.php` — a removed public property.
  - `PropertyProtectedMatcher.php` — a public property that became protected.
  - `ClassNameMatcher.php` — a whole class or interface.
- Visibility routes a property and never a method. The method matchers are a
  weak match on the method name where code uses it. They do not resolve the
  class, so they cannot see visibility, and a protected method goes with a
  public one. `RendererRegistry->getRendererInstances` went from public to
  protected in `Breaking-110277`, and it stands in `MethodCallMatcher.php`. The
  list above has no row for a protected method because it needs none. That
  absence says nothing about whether the change owes an entry.
- The fully qualified name with `->` or `::` keys an entry. The entry carries
  `restFiles`, which names the changelog file that removed the member. The
  method matchers add `numberOfMandatoryArguments` and
  `maximumNumberOfArguments`. A member deprecated before its removal lists both
  changelog files.
- Every Breaking and Deprecation entry carries exactly one of `NotScanned`,
  `PartiallyScanned` and `FullyScanned` in its `.. index::` line. That tag is
  the claim those entries have to back. `FullyScanned` says the scanner finds
  every item the changelog entry names. The scanner reads PHP. So what an entry
  changes in TypoScript, TCA, YAML or JavaScript leaves it partially scanned.
- `./Build/Scripts/runTests.sh -s checkExtensionScannerRst` checks that the
  changelog files the matchers name exist. Nothing checks the other direction. A
  missing entry surfaces when somebody audits the matcher files against the
  changelog.

## Changed Signatures

A signature change is the third breaking move beside a removal and a narrowing.
An added parameter is one, an optional parameter included. A public or protected
method on a class that is not final is an override point. Every subclass that
declares the old signature fatals as it loads.

- The obligation follows from the member being overridable, not from an override
  anybody found. `Breaking-101133` files a changed parameter of
  `IconFactory->getIcon()` against "custom extensions extending the method".
  `Breaking-110218` declares `LogRecord` final and calls the affected
  installations very unlikely.
- A member marked `@internal` takes an `Important` instead. `Important-107342`
  extended `FormPersistenceManagerInterface::listForms()` by two optional
  arguments and reached `13.4.x` on that ground. The change still owes an entry.
  Only its type changes, and that is what lets such a change reach a release
  line.
- Neither owes a matcher, and both are `NotScanned`. A matcher keys on where
  code calls a member. An override is not a call, and an added optional
  parameter leaves every existing call site valid.
- So it decides the target branch before anything else. A maintained release
  line takes no breaking change. So a fix you owe to one cannot carry the
  signature change at all. The shape that reaches it is the additive one. That
  is a method of its own, or the state on something the callee already receives.
  To declare the class or the method final first is no cheaper, because that is
  itself a breaking change.
- Nothing in a core checkout reports any of this. No core class has to override
  the method. So the unit, functional, coding-guidelines and static-analysis
  runs are all green on the change.
- A member promoted from protected to public is not a signature change and owes
  none of it. The core promotes one in a plain `[TASK]` or `[BUGFIX]` commit
  that carries no changelog file. Such a patch reaches a maintained release
  line, which a breaking change cannot. The changelog holds the move in the
  other direction only: `Deprecation-86047` narrows public members of
  `TypoScriptFrontendController` to protected. A subclass that re-declares the
  member as protected fatals with "Access level … must be public". The core
  files nothing for that either.

## Deprecations

- A deprecation must not use `[!!!]`.
- A deprecation may only use `[TASK]` or `[FEATURE]`.
- A deprecation must have a changelog RST file.
- A deprecation needs migration guidance and may need extension scanner
  considerations.
- All of the above is the author's side. The reader's side works the other way
  round. It asks what a given version deprecated, and what that means for code
  that uses it. The Extension Scanner in the Install Tool checks an installation
  against two directories. Those are the changelog files below
  `Documentation/Changelog/` of the core package, and the matchers below the
  install package's `Configuration/ExtensionScanner/Php/`. Both ship with a
  Composer installation.

## The Changelog Entry a Message Announces

- A breaking change, a deprecation and a feature carry a changelog file in the
  same patch. So does anything else that may require manual action. A casual bug
  fix carries none, because the commit message informs the reader.
- One page says which of the four types that is, and which directory a
  backport's file goes into. It says what name the file has and what reports one
  that is wrong. That is `typo3_rule_lookup` with
  `documentId="core/contribution/changelog"`, which also stands as
  `typo3://guides/core/contribution/changelog`.
- The `Releases:` trailer and that directory are one judgement. The oldest
  branch the trailer names is the release whose directory the file goes into.
