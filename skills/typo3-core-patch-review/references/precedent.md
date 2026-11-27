# Finding a precedent in the changelog

Read this when a finding argues from what an earlier change of the same kind
required. `typo3_changelog_lookup` finds the earlier entry, and this page says
how to ask it.

## List the kind first

**List the kind before you search for words: `type` and `version`, and no query
at all.** An earlier change is a precedent because of its shape, and a shape has
no words of its own. The entry that settles a finding often shares no noun with
the diff. Two reviews missed their precedent with a query and found it by hand
afterwards.

- `type` is the obligation the finding is about.
- `version` is the line the precedent would sit on. That is a released line the
  core backports the change to, or an earlier major.

A released line publishes few entries per type. So the listing is everything the
core did of that kind, and you pick a precedent from its titles. A major still
in development has more entries of a type than the default answer returns, so
raise `limit` there.

## Then ask in the words of a title

**Ask it in the words of the entry's title, not in the identifier the diff
removes.** Your list of removals gives you class and method names. A removal
entry has a title about the subsystem or the kind of API. It lists the
identifiers inside the file.

So a query that names an identifier and comes back empty has established
nothing. A query narrowed to the branch this patch targets has established
nothing either. A precedent sits under the version it landed in, which is always
an earlier one.

Where the listing and the words both miss, the precedent can still exist. Look
in `Documentation/Changelog`, which this server does not read and you do. Say
which of the two answered.

## Read what kind of change an entry came from

**What kind of change an entry came out of is two readings rather than one.**

- `typo3_forge_lookup` with its issue number says what the reporter filed the
  issue as.
- The commit keyword says what the change was. A review argues from the keyword,
  for example that an earlier bugfix of this kind required an entry.

The two disagree in both directions, so read the keyword itself.
`git log --diff-filter=A` over the entry's own file names the commit that added
it. The issue behind a security entry is not public. An unavailable answer there
is not an outage.
