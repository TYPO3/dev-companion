---
id: D-KNW-057
title: A document declares what it is and when to reach for it
date: 2026-08-04
status: open
coveredBy:
  - KnowledgeTest::aHintAnswerNamesTheDocumentThatExpandsIt
  - KnowledgeTest::everyHintADocumentSaysItExpandsExists
  - KnowledgeTest::theFrontMatterDescribesTheDocumentAndReachesNoAnswer
  - KnowledgeTest::theResourceCardIsWhatTheDocumentDeclaresPlusWhoItIsFor
---

# D-KNW-057 — A document declares what it is and when to reach for it

**A document declares in front matter what it is, when to reach for it and which
hints it is the long form of. Nothing states those a second time.**

A document says nothing about itself today. What a client reads when it picks
one comes from `knowledge/server-scope.json`, whose rows answer a different
question, what this server covers at all. The link to the hints is a sentence
somebody remembered to write.

## Evidence

- `Documents::description()` builds the card out of the `covers` row naming the
  document: its `topic`, plus a sentence read off its `scope`. Both belong to
  the coverage statement, and the document is not consulted.
- A client picks a resource out of a list rather than calls it mid-task,
  `R-ANS-022`. So that card is the whole of what the choice rests on. A topic
  phrased for the scope answer is not a description of the page.
- The link exists in one direction only. `typo3_rule_lookup` returns
  `alsoInHints` for every query it answers. A hint that names the document that
  expands it is prose somebody wrote by hand, which is the failure `D-KNW-008`
  named. A cell nothing routes to is in reach only by a guess at its words.
- The relationship is already modelled once. Every entry in
  `knowledge/catalog/reference/entries.json` carries a `hint` naming the
  convention it is a worked example of, and `typo3_reference_list` answers with
  it.
- A search of its body cannot find what a document is for. Eight realistic
  queries all reach the new PHPUnit document, and the lexical match decides
  which section they land on. "set up tests for my extension" lands on the
  database credentials rather than on the file it exists to hand over.
- The corpus already declares metadata this way. A requirement, a decision and a
  todo each say at the top of the file what they are.

## Decided

- Three fields: `description`, what the document is; `whenToUse`, when a caller
  reaches for it; `hints`, the ids this document is the long form of.
  `whenToUse` is the word the test suites and the hints already use. `hints` is
  the field `references.json` spells in the singular for the same link.
- Who the document answers for is not among them. That is the directory it sits
  in — `D-KNW-058` — so the two cannot disagree.
- The front matter is the source for the resource card.
  `Documents::description()` reads it, and the `covers` row still answers what
  this server covers, which is a different sentence for a different question.
- `typo3_hint_lookup` names the documents whose front matter lists the hint it
  returns. So the document declares the link once, rather than every hint that
  wants it.
- `Documents` strips the front matter before it searches or hands over a
  section. It describes the document rather than answers a query. Left in the
  corpus, it matches words about a page instead of words in it.
- `symfony/yaml` parses it rather than a regex per key, unlike the requirement
  and decision front matter. A description is a sentence and a sentence carries
  colons.

## Assumed

- Three fields are enough for a caller to choose between two documents about one
  subject. The case that disproves it is two whose descriptions read alike.
- A `whenToUse` written once stays true. Nothing measures it. It is prose in a
  file the corpus keeps rather than in a copy somebody else holds, so a visit
  corrects it.

## Wrong if

- A card promises something its body does not carry, which no assertion can see.
- The front matter reaches a caller as part of a section, or as part of a file a
  section hands over.
- A document lists a hint that does not exist. Or a subject grows a document
  that no hint names and that is in reach only by a guess at its words.
- A fourth field arrives that repeats what the coverage row or the directory
  already says.
