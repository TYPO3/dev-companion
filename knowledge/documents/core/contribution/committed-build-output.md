---
description: >-
  The JavaScript and CSS the core commits beside the sources they come from: how you read a change to one without a build, rebuild one where nothing of yours is at risk, rebuild every link of a rebased chain, and resolve one a backport conflicted in.
whenToUse: >-
  When a change touches Build/Sources/TypeScript or Build/Sources/Sass together with the generated file below Resources/Public/ that belongs to it. The question is whether the committed file carries the source change, how you produce it after an edit or after a rebase of a chain, or what you do with a backport that came back with conflict markers in it. The checkGruntClean suite answers the first of those and stages the whole tree on the way, so it is no way there from a checkout with work of your own.
hints:
  - backend-typescript
---

# The Build Output the Core Commits

## Where the Committed Build Output Comes From

`Build/Sources/TypeScript/<ext-key>/` compiles and minifies into
`typo3/sysext/<ext>/Resources/Public/JavaScript/`. `Build/Sources/Sass/`
compiles into the same packages' `Resources/Public/Css/`. The source directory
carries the extension key with its underscores as dashes, so `rte-ckeditor`
builds into `rte_ckeditor`. The core commits both outputs, so a patch that edits
a source carries the rebuilt file in the same commit. A review of either half
reviews both. The core's own `AGENTS.md` states the rule that follows: nobody
edits a file below `Resources/Public/JavaScript` or `Resources/Public/Css` by
hand.

The minifier writes the generated file onto one line. EXT:form's `view-model.js`
on 14.3 is twelve lines of licence header and one line of 24,240 characters.
`backend.css` carries one of 33,918 (measured 2026-08-26). So `git diff` prints
that line twice and tells a reader nothing. The next section is for that.

## Reading a Minified Diff Without a Build

Split both sides on the separators the minifier keeps, and diff what comes out:

```bash
P=typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js
tokenise() { sed 's/\([;{}]\)/\1\n/g'; }

# two revisions of it, the commit's parent against the commit
diff <(git show "$parent:$P" | tokenise) <(git show "$commit:$P" | tokenise)

# the committed one against the one a build has just written
diff <(git show "HEAD:$P" | tokenise) <(tokenise < "$P")
```

Measured on 14.3's `06dc629259`, a one-token null-safety fix: four lines of
output, `l=a.querySelector(…)` against `l=a?.querySelector(…)`.

Where the change lands in a token line too long to read, put `,` into the
character class as well. On that file the longest line falls from 1,961
characters to 263.

This settles whether the committed file carries the source change and nothing
besides it. It runs no build and writes nothing.

## Rebuilding It Where Nothing of Yours Is at Risk

Sometimes you have to produce the file rather than read it. Somebody edited the
source and nothing built it, or you suspect the committed output is stale. Then
the build runs in a worktree branched off the target branch, and your own
checkout stays untouched:

```bash
git worktree add --detach ../build-check origin/14.3
cd ../build-check
CI=true ./Build/Scripts/runTests.sh -s "$suite"
git status --porcelain
```

`$suite` is the branch's frontend build. Which one that is belongs to the branch
rather than to this page. `typo3_test_run_guide`, given the changed paths and
that branch as `targetVersion`, names it and prints the whole command. It runs
npm inside `Build/`, and git tracks that directory's `package.json` and
`package-lock.json`. So a fresh worktree needs no `composerInstall` first.

An empty `git status` says the branch's committed output is what its sources
produce. So anything the next build alters is yours. Measured on 2026-08-26 at
the tips of 14.3 and main: both came back empty, and neither run rewrote
`Build/package-lock.json`.

Then apply the source half of the work in that worktree, build again, and read
the diff above. A session that reverted one commit's TypeScript hunk on 14.3 and
rebuilt altered two files. Those were the source and the generated file that
belongs to it.

A build touches two things besides its output. `exec:stylefix` runs
`stylelint --fix` over `Build/Sources/Sass/**/*.scss`, so a Sass source can come
back rewritten. `npm install` can rewrite `Build/package-lock.json`. Stage what
your own work owns and nothing else.

Run `git worktree remove ../build-check` when you have the answer. Git ignores
the `node_modules` the build installed below `Build/`, and it goes with the
worktree.

## Output No Source Produces Any More

A build overwrites what it emits and deletes nothing below `typo3/sysext`. So a
generated file whose source somebody renamed or deleted survives it and leaves
`git status` empty. That is the one question the procedure above does not
answer. It is why `checkGruntClean` deletes every generated `.js` before it
builds. In the same worktree you ask it when you delete them there:

```bash
find typo3/sysext -name '*.js' -not -path '*/theme_camino/*' -not -path '*/Fixtures/*' -not -path '*/Documentation/*' -delete
CI=true ./Build/Scripts/runTests.sh -s "$suite"
git status --porcelain
```

That is `checkGruntClean`'s own body without the `git add *` it ends in.
Measured on 2026-08-26 in a worktree at 14.3's tip: every deleted file came back
identical to the committed one. `git status` printed nothing.

The suite itself does not run there. The same run's build succeeded. Each of its
git calls failed with `fatal: not a git repository: <the worktree's gitdir>`.
That directory sits outside the one the container mounts, so the suite reported
FAILURE over a clean tree.

## Rebuilding Every Commit of a Chain

Each link of a chain commits its own build output. So a rebase owes a build per
link rather than one at the tip. `git rebase --exec` runs a command after each
commit it picks. A command that fails stops the rebase on that commit. A command
that amends the commit carries the amend into every commit above it.

Build the target branch first, as above, and require an empty `git status`. Read
the chain for a renamed or deleted source, because the build leaves its old
output in place. Then run the rebase on the chain's branch, in a worktree of its
own:

```bash
cat > ../rebuild.sh <<'SCRIPT'
#!/bin/sh
set -e
CI=true ./Build/Scripts/runTests.sh -s "$suite"
git checkout -- Build/package-lock.json
outside=$(git status --porcelain | grep -v -E ' typo3/sysext/[^/]+/Resources/Public/(JavaScript|Css)/' || true)
[ -z "$outside" ] || { echo "$outside"; exit 1; }
git add -A typo3/sysext
git diff --cached --quiet || git commit --amend --no-edit
SCRIPT
chmod +x ../rebuild.sh

OLD_TIP=$(git rev-parse HEAD)
OLD_BASE=$(git merge-base HEAD origin/main)
git rebase --exec "suite=$suite ../rebuild.sh" origin/main
git range-diff "$OLD_BASE..$OLD_TIP" origin/main..HEAD
```

The script puts the lock file back and stops at anything outside the generated
JavaScript and CSS. That is a Sass source the stylefix rewrote, or a file the
link did not own. It amends only where the output changed. A stop leaves the
rebase on the link that caused it, and `git rebase --abort` puts the branch
back.

`git range-diff` is the check afterwards. `=` on a link says the rebased commit
is the pushed one, output included. A link with a difference shows the
generated file that moved, and the tokenised diff above reads it. A session
rebuilt a chain of 13 links this way on 2026-10-16. Each build took about 40
seconds, and every link came back `=`.

## A Backport That Conflicts in a Generated File

A cherry-pick onto a release branch conflicts in the generated file whenever the
two sources diverged in the same module. It does so where the source half
applies cleanly.

Gerrit's "Cherry pick" action commits the conflict markers instead of a refusal.
Change 95412's patch set 1 carried them in EXT:form's `view-model.js`. The only
record is a change message that reads "The following files contain Git
conflicts". CI answered `Verified-1` on it (read from the review server on
2026-08-27). So search a backport made that way for markers before you do
anything else with it.

The target branch's own build resolves the conflict:

1. Put the target branch's committed file back. While the cherry-pick waits,
   that is `git checkout --ours -- <the generated path>`. Where a fetched patch
   set already commits the markers, it is
   `git checkout <the target branch> -- <the generated path>`.
2. Build in a worktree branched off the target branch, as above, with the source
   half of the patch applied.
3. Stage the generated file the patch owns and nothing else.

The tokenised diff shows that the resolution is right. It shows exactly the
change the source makes, and no other generated file moved. It is also how you
discover a stale committed output on the target branch.

## The Same Module Built on Two Branches

Identifier mangling is a property of the whole module. So one expression carries
a different variable name on each branch. Measured on 2026-08-26 for EXT:form's
`view-model.js`: 404 token lines on 14.3 against 426 on main, 364 of them
different. The sources differ in a handful of lines. The same import binds to
`x` on one branch and to `k` on the other.

That is why neither side of the conflict resolves it. The newer side puts its
whole module onto the older branch and presents as a resolved file. The older
side keeps the output the fix was meant to replace.
