---
description: >-
  How a code comment, a docblock, a test name and a changelog entry in a patch have to read, with the rejected sentence and its replacement for each rule.
whenToUse: >-
  Before you write the comments and docblocks of a patch somebody else reviews, and again before you hand the patch over. Also when you review the prose of somebody else's patch: its comments, its docblocks and its commit message. It is about the wording rather than about whether the comment is owed at all, which is the codebase's own rule.
hints: []
---

# The Prose a Patch Carries

A reviewer reads your comments. A patch whose code is right and whose prose is
not costs a review round. The reviewer spends that round on sentences rather
than on the change.

Most of these rules come from one core patch. The reviewer accepted its code
after one round and rejected its comments twice. The rules on terms and on the
whole text come from a second patch. Its review found the gap late. Each rule
carries a rejected sentence and the sentence that replaced it.

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

## Keep the Sentence Short and Plain

These rules come from ASD-STE100, Simplified Technical English. The core does
not require STE. A reviewer still reads a plain sentence faster.

- An instruction has at most 20 words. A description has at most 25.
- Use the simple tenses: present, past and future. Use the present tense where
  you can.
- Use a noun cluster of three words at most.
  - Rejected: "the site root page ancestry query"
  - Written: "the query for the ancestry of the site root pages"
- Keep the articles and "that".
  - Rejected: "Finds sites user has access to through web mounts."
  - Written: "Finds the sites that the user has access to through the web
    mounts."

The STE dictionary of approved words is not part of this server. So judge a word
by the two rules that follow, not from memory of the dictionary.

## One Term for One Thing, Across the Patch

The commit message, the comments, the identifiers and the code near the change
use one term for one thing. Take the term from the code near the change.

- Rejected: `isSiteReachable()` and the comment "the sites a user can reach", in
  a patch that calls `SourceHostProvider::getAccessibleHosts()`.
- Written: `isSiteAccessible()` and "the sites a user has access to".

Compare the terms across all of them, not one sentence at a time. A commit
message that says "web mount page" beside a docblock that says "web mount" names
one thing twice. Each text is correct alone, and only the comparison finds it.

## Read the Prose as One Text

Read the commit message and the comments in the order a reviewer reads them.
Each sentence connects clearly to the sentence before it. A chain of "this", "in
this case" and "thus" makes the reader carry every step.

- Rejected: "A web mount page can be the parent page of site root pages. This
  web mount page can belong to no site itself. In this case, the module finds no
  site and shows no host."
- Written: "Where a web mount is above site root pages and in no site itself,
  the module shows no host for those sites."

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
