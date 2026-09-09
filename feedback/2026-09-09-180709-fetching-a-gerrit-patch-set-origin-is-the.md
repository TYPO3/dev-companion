---
date: 2026-09-09T18:07:09+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# Fetching a Gerrit patch set: origin is the GitHub mirror, refs/changes needs the ssh remote

## Observation

Task: get the final patch set of Gerrit change 95482 into an existing review worktree and compare it against local work.

Concrete trap, cost one failed call: in a TYPO3 core checkout `origin` is asymmetric — fetch URL git@github.com:TYPO3/typo3.git (the mirror), push URL ssh://<user>@review.typo3.org:29418/Packages/TYPO3.CMS. So

  git fetch origin refs/changes/82/95482/3:refs/tmp/ps3
  fatal: couldn't find remote ref refs/changes/82/95482/3

`git remote -v` and `.gitreview` (host, port 29418, project) were what told me, and fetching the ssh URL directly worked. A reviewer who knows Gerrit will still get this wrong once per checkout, because the ref path is right and the remote silently is not.

What the whole job actually cost, in calls: (1) detail?o=CURRENT_REVISION&o=MESSAGES&o=ALL_REVISIONS — returned a JSON blob so dominated by gravatar URLs per account that I could only read its head and learned nothing usable; (2) detail?o=ALL_REVISIONS piped through python for the patch-set list — this is the call I should have made first; (3) /comments for the inline threads with author, patch set and line — the single highest-value call of the session, it carried the whole review history; (4) the failed origin fetch; (5) /revisions/current/patch?zip for the diff; (6) the ssh fetch for a local ref. Six calls for: patch-set list, comments, diff, local ref. Later I repeated (1) and (3) to pick up the reviewer's reply.

I never called typo3_gerrit_lookup. I assumed it returns change metadata I already had from curl and not the diff or the comment threads. That assumption is untested and may well be wrong — but it is the assumption a caller makes from the name alone, and the name is all a caller sees before paying a round trip for the schema.

## Query

git fetch origin refs/changes/82/95482/3:refs/tmp/ps3 — then curl -H "Accept: application/json" https://review.typo3.org/changes/95482/detail?o=CURRENT_REVISION&o=MESSAGES&o=ALL_REVISIONS — then https://review.typo3.org/changes/95482/comments — then https://review.typo3.org/changes/95482/revisions/current/patch?zip — then git fetch ssh://<user>@review.typo3.org:29418/Packages/TYPO3.CMS refs/changes/82/95482/3

## Suggestion

One call taking a change number and returning, in one answer: current patch-set number and SHA, the parent SHA, the diff, the inline comment threads with author / patch set / line / resolved state, the change's own messages, and — the part no REST endpoint gives you — the fetch command that works in a core checkout, i.e. the ssh remote from .gitreview rather than `origin`, with the refs/changes/NN/CHANGE/PS path already assembled. That would have replaced six calls with one.

If typo3_gerrit_lookup already returns some of this, put those nouns in its description — "diff", "inline comments", "patch set", "fetch ref" — because a description that says only that it looks up a Gerrit change reads as "metadata I already have" and loses to curl.

Independently of the tool: the asymmetric `origin` in a core checkout is a fact worth carrying in the checkout/patch guidance, with .gitreview named as the place the real remote is written down.
