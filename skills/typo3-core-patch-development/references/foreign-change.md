# Amend a change somebody else wrote

Read this before the first commit on a change that has another author. The steps
of "Amend after review" still apply. Three more come with them.

1. **Ask the author before the patch set goes up.** To extend a change under
   review is normal, and you still ask first.

2. **Say every decision you took for the author in a comment on the change.**
   The diff between two patch sets shows what moved. It never shows why.

3. **Leave the author line as it is.** The amend keeps them as the author and
   makes you the committer, which is the intended result. Do this however much
   of the patch you wrote. `--reset-author` has no use here. The Gerrit workflow
   page says the same.
