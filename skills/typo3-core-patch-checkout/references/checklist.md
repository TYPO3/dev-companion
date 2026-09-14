# Resolve or stop

One rule decides every conflict, and it is about what you may know:

**You can resolve a conflict only where the change itself decides it.** The
patch says what it wants, and the branch says what is there now. Where those two
together leave exactly one way to write the hunk, to write it is transcription.
Where they leave a choice, the choice is the author's. To take it produces a
patch nobody wrote. Others then read, run and report on it as if it were the
author's.

Apply it per conflict, not per file. A change can be transcription in one hunk
and somebody else's decision in the next. The second one ends the work whatever
happened in the first.

## Resolve

- **Context moved.** The lines the patch touches stay as they were; something
  above or below them shifted. Nothing to decide.
- **A rename the branch made and the patch predates.** The change's intent stays
  the same under the new name, and the branch uses the new name everywhere.
  Verify the second before you believe the first.
- **Both sides made the same edit.** To keep one copy is the only reading.
- **Import, use-statement and namespace order.** The tool the checkout declares
  decides these, not you. Run it and take what it writes.

## Stop

- **Both sides changed the same lines with different intent.** This is the case
  the rule exists for. Any resolution is an authoring decision.
- **The branch already fixed what the patch fixes, differently.** The patch may
  now be unnecessary, partly unnecessary, or a better approach that should
  replace the other. All three are the author's and a reviewer's call.
- **The API the patch calls is gone or changed shape.** To rewrite a call site
  to the new API is to write the patch. It is also the moment the branch might
  no longer need the change at all.
- **The conflict is in a test's expectations.** What the expectation should be
  is the substance of the change, never a merge artefact.
- **The conflict is in a changelog entry, a fixture or generated output.** Which
  version it belongs to and what it says are decisions upstream of the diff.
- **You would have to read the issue again to decide it.** That is the signal
  that the change no longer carries its own answer.
- **More than a handful of hunks conflict.** Resolvable one by one or not, the
  patch is stale. A rebase by its author is the honest outcome. Say how many and
  where.

## Taking your own work onto the patch

The same rule, applied to a whole file rather than to a hunk. Your work and the
patch have two different bases. Whether those two bases already agree about a
file decides whether a file of yours can go over wholesale.

- **The file is identical on both bases.** You overwrite nothing of the
  author's, so a copy of yours over it is transcription. Establish that by a
  comparison of the two bases on that path. A read of the file says nothing
  about it.
- **The two bases differ on it.** Your version carries whatever moved on your
  base as well as your own work. To take it whole reverts the author's side
  without a word. Move your own hunks instead, and the rule above decides each
  of them.
- **The patch itself touches the file.** A conflict from the start, whatever the
  bases say, and never a transcription.

Say per file which of the three it was. The author has to be able to check that,
and the diff between two patch sets does not carry it.

## What a stop reports

- The change, its patch set, its target branch, and the commit you put the
  checkout back on.
- Which files and which hunks conflicted, and for each one which stopping rule
  it hit.
- What the two sides wanted, in one sentence each. That makes the report usable
  by whoever rebases it properly, and it is the part a diff does not say.
- Whether you resolved anything before the stop, and what. A partly resolved
  carry you then aborted still tells the next person which hunks are free.

## After any resolution

- The build and the suites that cover the touched paths say whether the
  resolution holds. A carry that produced a checkout nobody ran is not a carry
  that worked.
- Say that the checkout holds the patch on other code and is no longer the
  revision under review. Every result from it is about the commit you made and
  not about the patch set the reviewers see. To report one as the other is the
  failure this whole file guards.
- The carried state is local. A push opens a further patch set on the change it
  came from. The identifier that links the two travels with the commit. Where
  the user asked for that, it is a normal move. The Gerrit workflow page says
  what it owes its author.

  The push itself belongs to the workflow that owns the amend of a change. Where
  the user did not ask for it, the state stays here and the report is the
  answer.
