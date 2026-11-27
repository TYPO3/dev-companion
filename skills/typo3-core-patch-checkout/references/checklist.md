# Resolve or stop

One rule decides every conflict, and it is about what you may know:

**You can resolve a conflict only where the change itself decides it.** The
patch says what it wants, and the branch says what is there now. Where the two
leave exactly one way to write the hunk, you only write down what is decided.
Where they leave a choice, the choice is the author's. If you take it, you
produce a patch nobody wrote. Others then read, run and report on it as if it
were the author's.

Apply the rule per conflict, not per file. One hunk of a file can be decided
already, and the next one can be a decision for the author. The second one ends
the work, whatever happened in the first.

## Resolve

- **Context moved.** The lines the patch touches stay as they were; something
  above or below them shifted. Nothing to decide.
- **A rename the branch made and the patch predates.** The change's intent stays
  the same under the new name, and the branch uses the new name everywhere.
  Verify the second before you believe the first.
- **Both sides made the same edit.** Keep one copy.
- **Import, use-statement and namespace order.** The tool the checkout declares
  decides these, not you. Run it and take what it writes.

## Stop

- **Both sides changed the same lines with different intent.** This is the case
  the rule exists for. Any resolution is a decision for the author.
- **The branch already fixed what the patch fixes, differently.** The patch may
  now be unnecessary, partly unnecessary, or a better approach that should
  replace the other. All three are the author's and a reviewer's call.
- **The API the patch calls is gone or changed shape.** To rewrite a call site
  to the new API is to write the patch. The branch may also no longer need the
  change at all.
- **The conflict is in a test's expectations.** What the expectation should be
  is the substance of the change, never a merge artefact.
- **The conflict is in a changelog entry, a fixture or generated output.** Which
  version it belongs to and what it says are decisions the diff does not make.
- **You would have to read the issue again to decide it.** Then the change no
  longer answers the question itself.
- **More than a handful of hunks conflict.** Resolvable one by one or not, the
  patch is stale. Let its author rebase it. Say how many hunks conflict and
  where.

## Whose change it is

The stop rules protect an author who is not there. The `owner` field of the
change answers whose change it is. Compare its `email` with
`git config user.email`. In a chain, each link has an owner of its own, so
decide per link.

On the requester's own change, two stop rules fall away:

- **More than a handful of hunks.** Resolve them one by one, and say that you
  did and on whose change.
- **Generated output.** Resolve the sources, then rebuild the output with the
  build the checkout declares. `typo3_rule_lookup` with
  `documentId="core/contribution/committed-build-output"` gives that build.

Every other stop rule stays. Each of them is still a decision, also on the
requester's own change. There the requester is the author and is present. So the
stop report is the question to them: which side wins, hunk by hunk.

## Taking your own work onto the patch

The same rule, applied to a whole file rather than to a hunk. Your work and the
patch have two different bases. Whether the two bases agree about a file decides
whether you can copy your version of that file whole.

- **The file is identical on both bases.** Your copy overwrites nothing of the
  author's, so you can take it whole. Establish that by a comparison of the two
  bases on that path. A read of the file alone does not show it.
- **The two bases differ on it.** Your version carries whatever moved on your
  base as well as your own work. If you take it whole, you silently undo the
  author's side. Move your own hunks instead, and the rule above decides each of
  them.
- **The patch itself touches the file.** Treat every hunk there as a conflict,
  whatever the bases say.

Say per file which of the three it was. The author has to be able to check that,
and the diff between two patch sets does not show it.

## What a stop reports

- The change, its patch set, its target branch, and the commit you put the
  checkout back on.
- Which files and which hunks conflicted, and for each one which stop rule it
  hit.
- What the two sides wanted, in one sentence each. The person who rebases the
  patch later needs that, and a diff does not say it.
- Whether you resolved anything before the stop, and what. An aborted rebase
  still tells the next person which hunks resolved without a decision.

## After any resolution

- The build and the suites that cover the touched paths say whether the
  resolution works. A rebase nobody ran is not a rebase that worked.
- Say that the checkout holds the patch on other code and is no longer the
  revision under review. Every result from it is about the commit you made and
  not about the patch set the reviewers see. To report one as the other is the
  failure this whole file guards against.
- The rebased state is local. A push opens a further patch set on the change it
  came from. The Change-Id in the commit links the two. Where the user asked for
  that, it is a normal move. The Gerrit workflow page says what its author must
  get.

  The push belongs to `typo3-core-patch-development`. Where the user did not ask
  for it, the state stays here and the report is the answer.
