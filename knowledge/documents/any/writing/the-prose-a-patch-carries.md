---
description: >-
  How a code comment, a docblock, a test name and a changelog entry in a patch have to read, with the rejected sentence and its replacement for each rule.
whenToUse: >-
  Before you write the comments and docblocks of a patch somebody else reviews, and again before you hand the patch over. It is about the wording rather than about whether the comment is owed at all, which is the codebase's own rule.
hints: []
---

# The Prose a Patch Carries

A reviewer reads your comments. A patch whose code is right and whose prose is
not costs a review round. The reviewer spends that round on sentences rather
than on the change.

These rules come from one core patch. The reviewer accepted its code after one
round and rejected its comments twice. Each rule below carries the sentence the
reviewer rejected and the sentence that replaced it.

Nothing here says whether you owe a comment. That is the codebase's own rule.
The TYPO3 core asks for a comment only where it adds meaning. This page is about
the ones you keep.

## Say What Is

Open on the statement. A sentence that starts with a denial makes the reader
hold two things.

- Rejected: "The expression is not the client's: it is handed over in the
  validation rule."
- Written: "The server sends the pattern in the validation rule."

## Name Who Acts

Give every sentence a subject the codebase already has: `DataHandler`,
`FormEngine`, the browser, this test. A passive with no actor hides the one fact
the comment exists to carry.

- Rejected: "So an annotation is written where inference cannot reach."
- Written: "Add a type annotation only where TypeScript cannot infer the type."

## One Thought per Sentence

Where a sentence needs a colon, a dash or a semicolon to hold its second half,
it is two sentences. A colon introduces a list or a definition. It does not
deliver a punchline.

- Rejected: "The expression is not the client's: it is handed over in the
  validation rule, so that the browser evaluates what DataHandler stores the
  value by."
- Written: "The server sends the pattern in the validation rule. This test only
  checks that the client applies it."

## End on the Object

A sentence that ends on a preposition has lost the thing it is about.

- Rejected: "the expression DataHandler stores the value by"
- Written: "the pattern DataHandler applies on save"

## Name the Place Instead of Pointing at It

"Above", "below" and "here" describe a file somebody has edited since. Name the
class, the method or the file.

- Rejected: "keeps the browser from ever disagreeing with the two ends below"
- Written: "keeps the browser and `DataHandler` applying the same pattern"

## Use the Words the Codebase Uses

Everyone who reads a metaphor you invented for one comment has to decode it. It
names nothing anybody can search for.

- Rejected: "The four below are valid to `GeneralUtility::validEmail()` and are
  narrowed away on purpose."
- Written: "`GeneralUtility::validEmail()` accepts these four. The stored format
  rejects them deliberately."

## A Longer Correct Sentence Beats a Short Broken One

Compression that damages the grammar is not brevity. Where the short form does
not parse, take the words back.

- Rejected: "The syntax of a stored value is narrower than what may be sent
  mail."
- Written: "A stored value has a narrower syntax than an address you can send
  mail to."

## One Fact, One Place

Explain a mechanism where it starts and name it elsewhere. A handover from PHP
to TypeScript explained in three places is two explanations too many. The three
drift apart on the next change.

## Say Why

A comment carries what the code cannot: the reason, the constraint, the trap,
what breaks if somebody changes it back. Where you can delete the comment and
lose nothing, delete it.

## Length Is a Ceiling

Two lines for an inline comment. A class docblock over three short paragraphs
describes a design. A design belongs in the changelog entry the patch owes, not
in the class.

## The Check Before You Hand It Over

Read each comment once, at reading speed. Where you have to go back to parse it,
rewrite it. Every rejected sentence on this page failed that check, and its
author had not run it.
