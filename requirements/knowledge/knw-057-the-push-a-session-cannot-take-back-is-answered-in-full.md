---
id: R-KNW-057
title: 'The push a session cannot take back is answered in full'
status: held
restsOn: [D-SKL-005]
heldBy:
  - KnowledgeTest::theUnlistedPushIsAnsweredBesideTheOneThatPublishes
  - KnowledgeTest::theWriteDirectionIsAnsweredAroundThePushItself
---

# R-KNW-057 — The push a session cannot take back is answered in full

**The corpus answers the push itself: which form it takes, where the checkout
sends it, and what the issue behind it has to be.**

Which form it takes is a visibility no second push undoes. So the answer says
who can then see the change rather than only how to type the option. A git
worktree changes none of the three, and the corpus says so. That is the doubt a
session raised at the moment of the push.

Everything before the push is local and reversible. The push is neither. It is
the one step of a core patch where a session chooses a visibility it cannot undo
with a second try. `typo3-core-patch-development` makes the choice mandatory and
routes it here: *"`typo3_rule_lookup` for the Gerrit workflow has both forms"*.
So an answer that carries only the form that publishes is worse than no answer,
because the caller heard the other one is in it.

The other three are the same step read from the other side. Where a push lands
is a property of the checkout rather than of the project's name, and a session
can only read it. Whether the refspec still holds from a worktree is the doubt a
user raised outright at the moment of the push. A change that hangs off a closed
report asks reviewers to reverse a decision nothing in the change mentions.

## From

A core patch session on Forge #105403 that pushed to Gerrit as a private change.
It established every mechanical fact of the push from the checkout or from its
own knowledge (`feedback/2026-08-02-144848`, 2026-08-02).
[`D-SKL-005`](../../decisions/task-skills/skl-005-core-contribution-earns-two-task-skills.md)
trimmed it to these four on 2026-08-03 after a re-run of the rest against the
server.
