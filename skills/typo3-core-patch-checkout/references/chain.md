# A chain of changes

Read this when `typo3_gerrit_lookup` returns a `chain` with more than one open
link. A large refactoring arrives as a chain. "Rebase the chain" names this case
directly.

## Read the chain before the fetch

Take five things from it:

- **Which links are still open.** You must move those, and their number is the
  number of commits to move. The merged links below them are already on the
  branch, or should be.

- **Whether the merged link below is an ancestor.** Compare `chainedAt` with
  `patchSet` on that link. A link with `chainedAt` 18 and `patchSet` 23 merged
  as a revision the chain never sat on. Then `git merge-base --is-ancestor`
  answers no. Some of the conflicts follow from that one difference. Nothing in
  a checkout shows it.

- **The move is a range.** One change is `git cherry-pick FETCH_HEAD`. A chain
  is `git cherry-pick BASE..TIP`, or the same move as
  `git rebase --onto origin/main BASE TIP`. Name the branch
  `review/<change number>` after the **tip** change. That is the one the request
  named.

- **A later amend takes two steps.** You cannot amend a fix that belongs to a
  lower commit from the tip. Detach to that commit, amend, apply again what sat
  on top, and move the branch. Plan for it from the start. A rebuild of the
  output in every link is one step instead. `typo3_rule_lookup` with
  `documentId="core/contribution/committed-build-output"` gives it, section
  "Rebuilding Every Commit of a Chain".

- **Which links fail.** Every link has its labels. A link that Verified rejects
  names the core-ci jobs that failed, each with its log. A rebase that rebuilds
  output must know whether the build is the failure.

## Whose chain it is

The stop rules of this skill protect somebody else's patch. A chain is often the
requester's own. The `owner` field of each link answers this. Compare its
`email` with `git config user.email`. Each link has an owner of its own, so
decide per link. On the requester's own links, the rule against an absent
author's decisions protects nobody. There you may resolve more than a handful of
hunks. Say in the result that you did, and on whose change.

## Your own chain, with its content unchanged

A rebase of the requester's own chain is option 3 on every link. The requester
may be the owner of every link. Then three steps fall away: the ask, the comment
on the change, and the stop. That stop is the one at a conflict the change does
not decide.

The rest stays, in this order:

- **The target branch**, established before anything moves.
- **The build in every link**, where the chain commits build output. The rebuild
  section named in "A later amend takes two steps" gives it.
- **`git range-diff`** from the old range to the new one. `=` on every link
  proves that neither the content nor the message moved.
- **The deprecation sweep** of the base order. The code under the chain moved.
  The sweep says whether it deprecated something a link calls. Narrow it with
  the tag of each system extension the chain touches.
- **The checks on the new base.** `typo3_test_run_guide` with the paths the
  chain touches names them. A run before the rebase says nothing about the new
  base.
- **The push, in `typo3-core-patch-development`.** Where every link came back
  `=`, that skill needs only its rebase section and the push. The message, the
  changelog entry and the review stay as they were pushed.
