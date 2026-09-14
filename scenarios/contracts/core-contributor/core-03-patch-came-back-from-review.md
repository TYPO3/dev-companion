# CORE-03 — The patch came back from review

**Environment:** `E-CORE` · **Contract:** `held`
**Held by:** `CommitMessageTest::anExistingMessageIsSplitBackIntoItsParts`,
`CommitMessageTest::anAmendKeepsTheChangeId`,
`CommitMessageTest::summaryLengthIsCheckedAgainstBothLimits`

> Review says my commit message is wrong and the subject is too long. Here it is:
>
> ```
> [BUGFIX] fixed the broken behaviour of the record list when copying pages with translations
>
> This fixes the issue.
> Resolves: 98765
> Change-Id: I8f1c2f9a4b7d6e5c0a3b2d1e9f8c7b6a5d4e3f2a
> ```
>
> Fix it, then amend and push again.

**What the agent needs from this server**

- What each rule is: subject length, keyword, the capitalisation after it, the
  body, which trailers belong there and in which order.
- That the `Change-Id` that exists stays, because this is an amend and not a new
  change.
- The local Gerrit steps for a second patch set.

**What has to come out of it**

- The answer names and corrects every defect in the message, and the corrected
  message is ready to commit. The subject is shorter, the body says what changed
  and why rather than "this fixes the issue", the trailers are in the right
  order.
- The `Change-Id` survives.
- `git commit --amend` and the push refspec are the steps offered, not a new
  commit.

**How it fails**

- The `Change-Id` dropped or regenerated, which opens a second change on Gerrit.
- A message reported as fixed that still violates a rule the server knows.
