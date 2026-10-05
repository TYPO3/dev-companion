---
date: 2026-10-05T09:42:11+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5
tool: core-tests, hint, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# a probe that counts rows misses disabled rows unless it removes QueryBuilder restrictions

## Observation

Task: count the password hashes that the styleguide generator writes, in a temporary functional probe.
My probe used ConnectionPool->getConnectionForTable()->createQueryBuilder() with the default restrictions.
The default restrictions hid the two demo be_users. They have disable=1. The first count was wrong by one hash on each side.
I saw the gap only because the generator code inserts those users. Then I added getRestrictions()->removeAll() and ran again.
The core-tests hint covers assertCSVDataSet for persistence. It does not warn about this for ad-hoc counts in a probe.
The rendering and timing probe documents also do not mention it.

## Query

Temporary probe: TimingProbeTest in typo3/sysext/styleguide/Tests/Functional/TcaDataGenerator, counting values LIKE '$argon2%' in tx_styleguide_*, be_users, fe_users.

## Suggestion

Add one hint line to core-tests or to the probe documents: a probe that reads rows to count what a run wrote removes the QueryBuilder restrictions first, because deleted, hidden and disabled rows are otherwise invisible.
