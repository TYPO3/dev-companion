---
id: R-SCO-009
title: 'Individual tools can be excluded'
status: held
restsOn: [D-AUD-004, D-FBK-042, D-AUD-006]
heldBy:
  - ExcludedToolsTest::neitherSurfaceCallsAToolExcludedThatIsInTheList
  - ExcludedToolsTest::onlyTheCallerShortensTheList
  - ExcludedToolsTest::theFeedbackToolsFollowTheChannelAndNoExclusionReachesThem
  - ExcludedToolsTest::theScopeNamesWhatTheCallerExcluded
  - ExcludedToolsTest::theToolThatExplainsAShortListCannotBeExcluded
---

# R-SCO-009 — Individual tools can be excluded

**A caller can exclude individual tools with
`TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`, except the three the server names.**

The scope answer names the omissions that result, so a shorter tool list carries
its reason. It names nothing else. The answer reports a name in the variable
that took no tool away as that, rather than as a capability the server lacks
([`D-AUD-006`](../../decisions/audience/aud-006-the-server-reports-the-exclusion-that-happened.md)).

Every tool that answers about TYPO3 can go. Three cannot, and both reasons are
about what the caller keeps in hand:

- `typo3_server_scope`, because it is what tells a client why the list is
  shorter than the documentation says. A client that lost it cannot tell a
  configured server from a broken one.
- `typo3_feedback_record` and `typo3_feedback_list`, because the feedback
  channel is a development tool for the work on this server rather than part of
  its use. `Channel::isAvailable()` offers them from a standalone checkout
  alone. So the caller who could exclude them is whoever works on this
  repository. What they exclude is the only route by which a session hands back
  what it found.

Neither exception weakens the read-only posture, and to read it as one is the
mistake
[`D-FBK-042`](../../decisions/feedback/fbk-042-the-read-only-boundary-is-the-installation.md)
answers. `typo3_feedback_record` writes into this server's own checkout and
never into the installation it reads.

## From

The two fixed profiles forced a caller that wants all but one tool to pay for
all of them (2026-07-30). `D-AUD-004` removed the profiles on 2026-08-02
([`D-AUD-004`](../../decisions/audience/aud-004-every-client-is-offered-every-tool.md)),
and this is what remains of them: the subtraction the caller declares.

The exceptions arrived on 2026-08-04, after `453e439` read the feedback one as a
defect. What holds the status at `held` is that the code and this entry now say
the same thing. `Registry::offered()` filters `TOOLS` and appends the feedback
tools past the filter, which is exactly the list above.
