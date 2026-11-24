---
date: 2026-11-24T20:29:04+00:00
category: missing-knowledge
status: closed
closed: 2026-11-24
model: claude-opus-5-5
tool: typo3-core-patch-review, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# A core patch review does not check that prose and code use the same terms

## Observation

Task: review core change 97935 "[BUGFIX] Show hosts of sites below a web mount". The user then asked about uniformity and readability.

The review checked each comment sentence alone. It did not compare the terms across the patch. The user found this gap, not the review.

The patch uses different words for one thing:
- The commit message says "web mount page". The docblock says "web mount".
- The commit message says "non-admin backend user". The docblock says "Other backend users".
- The patch says "reach" and isSiteReachable(). SourceHostProvider::getAccessibleHosts() is in the same class. The core uses doesUserHaveAccess().
- The commit message and the docblock both explain the role of isInWebMount(). They use different words.

The review also did not check how sentences connect. The commit message has five short sentences. Each sentence refers to the sentence before it. Each sentence is correct alone. The chain is hard to read.

The guide "any/writing/the-prose-a-patch-carries" has "Use the Words the Codebase Uses" and "One Fact, One Place". The review skill does not tell the agent to apply them across files. The checklist has no surface for terminology. The agent found the gap only after a question from the user.

This feedback is in addition to the feedback about ASD-STE100. That feedback is about the rules for one sentence.

## Query

Change 97935, patch set 1. Commit message and comments in typo3/sysext/core/Classes/Site/WebMountSiteFinder.php, compared with typo3/sysext/redirects/Classes/Data/SourceHostProvider.php and BackendUserAuthentication. User question: "what about uniformity and readability?"

## Suggestion

Add a review step for terminology. The step compares the terms in the commit message, the comments, the identifiers and the core code near the change. Each concept must have one term in all of them.

Tell the agent to take the term from the core code near the change. Examples are getAccessibleHosts() and isInWebMount().

Tell the agent to read the comments and the commit message as one text. The agent checks that each sentence connects clearly to the sentence before it. A chain of references such as "this", "in this case" and "thus" needs a check.

Add both checks to the "Comments and docblocks" surface that the ASD-STE100 feedback proposes. Give a sample report line for a term that is not the same in two places.
