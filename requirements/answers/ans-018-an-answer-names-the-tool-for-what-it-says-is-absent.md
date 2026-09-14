---
id: R-ANS-018
title: 'An answer names the tool for what it says is absent'
status: held
restsOn: [D-ANS-031]
heldBy:
  - PackageSourcesTest::aMissThatOffersARequeryNamesTheCorpusToAskNext
  - PackageSourcesTest::aMissWithNoRequeryToOfferNamesBothCorporaThatAnswer
  - ProjectTest::whatACoreCheckoutDoesNotDeclareIsSaidWithTheToolThatHasIt
---

# R-ANS-018 — An answer names the tool for what it says is absent

**An answer that says something is not here names the tool that has it.**

An absence reads as a dead end, and the caller goes to look by hand. There
nothing checks what it finds against the checkout it stands in.

## From

A session in a core checkout that `typo3_project_describe` told "the core's
testing suites do not" exist among its declared commands. It named no tool that
has them. It reported a preference for a `Build/bin/phpunit` that checkout has
no directory for (`feedback/2026-08-01-114807`, 2026-08-01). The same gap from
the other end the next day. Four `gerrit:setup` hook installers answered "what
can I run here", while `Build/Scripts/runTests.sh`, called about thirty times
that session, stood nowhere (`feedback/2026-08-02-144350`).
