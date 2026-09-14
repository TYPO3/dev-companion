---
id: R-FBK-011
title: 'A recorded feedback carries no secret out of the installation'
status: held
restsOn: [D-FBK-019]
heldBy:
  - FeedbackTest::aLongBase64ValueIsTakenOutAndAWordIsNot
  - FeedbackTest::aPasswordInADatabaseUrlGoesWithoutTheHostGoingWithIt
  - FeedbackTest::aValueGoesAndTheNameThatSaysWhatItWasStays
  - FeedbackTest::aValueThatLooksLikeACredentialNeverReachesTheFile
  - FeedbackTest::everyFieldAFeedbackIsWrittenFromIsRead
  - FeedbackTest::theRulesTakeNothingOutOfTheCorpusButTheKeyTheyWereWrittenFor
  - FeedbackTest::theToolSaysWhatItTookOutOfWhatItWasHanded
  - FeedbackTest::whatASessionQuotesAboutTheCoreIsLeftAlone
---

# R-FBK-011 — A recorded feedback carries no secret out of the installation

**A value that looks like a credential does not reach the file. Where the
channel took one out, the file says so and the answer says so.**

This server's only write moves text from the project a session stands in into a
checkout of its own. That checkout gets committed and pushed. A key, a password
or a token pasted as evidence goes out of one repository into another. There the
person who owns it does not look and cannot take it back.

What the report needs is the path and the shape of the answer, never the value.
"The key at `SYS/encryptionKey` is the active one, hardcoded in
`config/system/settings.php`" is the whole finding. The 96 characters after it
establish nothing further.

With a mark, never in silence. The archive keeps a session's report because the
report is the evidence. So a reader has to see that something went out, and go
and ask. The session that wrote it stood in the installation the value came
from, and it is the one reader who still knows what stood there.

## From

`feedback/archive/2026-07-31-185900-after-the-audit-i-invoked-typo3-dev-companion.md`
(2026-07-31), which pasted the live encryption key of the audited site into its
observation while it reported that `typo3_configuration_lookup` had worked.

This entry and `D-FBK-019` both said the observation and the query quoted the
key, once each. The file says once. Its query names the argument and
`config/system/settings.php:118` without the value, and the commit that first
recorded it, `77d242b`, says the same. So the second field is a place the value
could go rather than one it went.

## Held by

`Channel::record()` reads every field a feedback comes from before it writes any
of them, and `Redaction` says what counts as a credential. That is a hexadecimal
or base64 run of 64 characters or more, and a value assigned to a name that says
what it is. It is the password in a URL that carries one. A session settled each
threshold against the 207 recorded feedback rather than by reason. A rule that
redacts a revision or a class name costs more than the leak it prevents.

Behind the guard, the `observation` and `query` descriptions in
`src/Tool/FeedbackRecord.php` say what a finding needs: the path, the shape,
where the value came from. They say that a value the installation keeps secret
is not part of it. That is what asks the session not to paste one in the first
place, and it is what the residue below rests on.

Not guarded, and left to the words of the fields to carry. A base64 value with a
`/` in it, which the corpus showed looks the same as a class path. base64url and
the JWTs made of it, whose `-` and `_` run into every changelog identifier. A
short secret on its own, with no name beside it and no shape to know it by. A
session that does not paste them is what holds those.
