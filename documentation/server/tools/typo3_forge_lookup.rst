.. _typo3_forge_lookup:

``typo3_forge_lookup``
======================

*Read the issue tracker*

Reads the TYPO3 issue tracker at forge.typo3.org through the bot protection the
core's own AGENTS.md warns a hand-written request about. It tells a tracker that
did not answer from a search that matched nothing. Read it before you write a
patch. Three ways in, one per call. issue reads one issue whole: the report, the
comments that decided it, and the issues and review changes it names. It says
whether an installed package still ships the code the report cites. query finds
the other issues that describe the same thing, which the relations of one issue
carry only where somebody linked them. backlog enumerates the core project's
unresolved issues without a number or a wording: oldest filed, longest untouched
or newest. Narrow it by tracker, area, date and person, and widen it with
status. breakdown answers how a large set spreads instead of a page of it. A
miss is an answer. An issue that does not exist gets that answer. Words that
match nothing get a count one word at a time, which does not say that nobody
reported it. A query reaches only text somebody wrote. So an issue whose
description is empty is reachable by its subject alone, and a wording the code
never uses reaches it from nowhere. That is why "has anybody attempted this
before" goes to typo3_gerrit_lookup by repository path. The change comes back
whatever its name, and the issue with it, off the trailers its commit message
carries. The patch for an issue on review.typo3.org is typo3_gerrit_lookup. This
tool reads only, with no credential: you comment, assign and close yourself. A
new issue is typo3_issue_report_guide, which drafts it and fills in the form.
Answers from: network, packages.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`network <answer-sources-network>`,
:ref:`packages <answer-sources-packages>`.

Takes
-----

.. code-block:: yaml

    # Forge issue number, for example 110348. Reads that one issue whole, comments
    # included; narrow those with notes when you read many. Not with query or
    # backlog.
    issue: integer  # optional
    # Words to search the tracker for, for example "image cache busting". A
    # full-text search over subject, description and comments, which is the one way
    # to find a duplicate nobody has linked. Every word has to be in the same issue.
    # A term nobody would have written — a method name, a class — empties the
    # answer whatever else is in it. Pass the two or three words that name the
    # subject rather than every word you have. A miss counts what each word reaches
    # on its own, in terms. Nothing ranks the hits, and one wording does not settle
    # it: ask again in the reporter's words as well as your own. A person's name
    # matches only where somebody wrote it, so pass it as reportedBy or assignedTo
    # with backlog instead. Not with issue or backlog.
    query: string  # optional
    # One of: oldest, stale, newest. Enumerate the core project's unresolved issues
    # instead of a read of one or a match of words. "oldest" orders them by filing
    # date, "stale" by how long nobody has touched them, "newest" by what came in
    # last. The filing date describes the report, and how long it has gone untouched
    # describes the attention it got. An issue that is both is the candidate a
    # triage looks for. "stale" with tracker and category is where a triage of the
    # backlog starts. "newest" is where a duplicate of a defect somebody has just
    # found is. A wording reaches only the issues with that wording. So a read of
    # the subjects filed since the defect could have appeared is what settles a
    # negative. Pair it with createdSince, which turns that end into a set the count
    # says you have seen the whole of. Unresolved is the tracker's own set of open
    # statuses: New, Accepted, Under Review, Needs Feedback, On Hold and Postponed.
    # tracker, category, words, createdBefore, createdSince, updatedBefore,
    # reportedBy, assignedTo, involving, breakdown and offset narrow this way in and
    # no other; status widens it. Not with issue or query.
    backlog: string  # optional
    # One of: all, people. Which comments come back with an issue. "all" is every
    # one of them, which is what a read of a single issue wants. The comments are
    # where the decision is, and the one that settles it is regularly the last of
    # sixteen. "people" drops the patch-set pings a review bot wrote, which on some
    # issues is half the volume. reviews carries the change numbers in them either
    # way, so nothing is lost. Ask for it when you read candidates one issue at a
    # time. There the cost of ten such reads decides whether you read the comments
    # at all. The answer says how many it dropped whichever you ask for. Narrows
    # issue; query and backlog ignore it.
    notes: string  # optional
    # One of: Bug, Feature, Major Feature, Support, Task, Story, Suggestion,
    # Impediment, Epic, Work Package, Topic. Only issues filed under this tracker,
    # for example "Bug". Set it before you read a set: an old Bug claims something
    # is broken today, an old Feature that somebody wanted something once.
    tracker: string  # optional
    # Only issues the core files under this area, in your own words: "rte", "backend
    # ui", "workspaces", "fluid". Matched against the project's own category names
    # one word at a time, so a half-remembered name reaches the right area. A word
    # that names several — "backend" — selects all of them and says which. It is
    # the way in for "are there known bugs in the RTE" and "the oldest issues in the
    # backend UI". It answers "has somebody already reported this" too: enumerate
    # the area and read the subjects. Where a system extension key names no area
    # itself, it resolves to the area that holds that extension's issues. "impexp"
    # reads "Import/Export (T3D)". A word that names none or several gets every area
    # the project has as its answer. categoriesUsed carries the tracker's own
    # spelling of the areas reached, which a report filed by hand has to carry. Pass
    # "*" for the list of areas on its own, which reads no issues.
    category: string  # optional
    # Only issues filed before this day, as YYYY-MM-DD. With createdSince it is the
    # far end of one window rather than a second filter.
    createdBefore: string  # optional
    # Only issues filed on or after this day, as YYYY-MM-DD. It is what makes the
    # recent end a set instead of a page. limit stops at 50 against thousands of
    # open issues, so a day to count from brings the page and the set together.
    # total says whether it did. It also reaches where category cannot. An issue
    # filed under no Category is in no area at all, and the report you look for is
    # regularly one of those.
    createdSince: string  # optional
    # Only issues nobody has touched since this day, as YYYY-MM-DD. It finds the
    # report everybody has walked past, which age alone does not. An issue filed in
    # 2009 and commented on last month is in work.
    updatedBefore: string  # optional
    # Only issues this person filed, by their name rather than a tracker id: "Frank
    # Nägler", or "nägler". This answers "what has this person reported", which
    # query cannot. query matches text, and a name in the text is as often somebody
    # else who wrote it. The tool resolves the name against the core project's
    # members. Where they hold no membership, it resolves it against the reporters
    # and assignees of the issues that carry the name. A name that reaches several
    # people resolves to none of them, and the answer says which they were. Pair it
    # with status "all" for everything somebody has ever filed, and with breakdown
    # for the shape of it rather than the first page. involving is the union of this
    # and assignedTo.
    reportedBy: string  # optional
    # Only issues this person holds, by their name, resolved the same way as
    # reportedBy. What somebody reported is their history; what they hold is what
    # they are on the hook for. An assignee on an old issue is usually who last
    # touched it rather than who works on it. Both of these together are the issues
    # somebody filed and holds; involving is their union.
    assignedTo: string  # optional
    # Only issues this person is on either side of — what they filed and what they
    # hold, as one set. The tracker cannot answer this: it ANDs its filters, so
    # reportedBy and assignedTo together mean filed AND holds, a set nobody wants.
    # Pass it instead of those two, not beside them. Each row's reportedBy and
    # assignedTo say which side it came in on.
    involving: string  # optional
    # Only issues whose subject or description carries one of these words at the
    # start of a word, for example "translat l10n overlay fallback" — "translat"
    # reaches translation and translated. Any of the words is enough, because the
    # words of a topic are its synonyms. This is the topic inside an area, where
    # category alone selects a hundred issues and query cannot take a category. The
    # tool reads the whole set the other filters select, up to a thousand issues,
    # and total counts the matches. wordsScan says how many it read. Narrows
    # backlog; not with query.
    words: string  # optional
    # How many issues of the set to pass over before the page starts, so limit 50
    # and offset 50 is the second page. That is the way through a set larger than 50
    # a page at a time. Narrows backlog; a search and a read ignore it.
    offset: integer  # optional
    # Answer how the matched set spreads instead of the rows of it: how many issues
    # per status, per tracker, per area, per year filed and per year last touched.
    # The last one says which updatedBefore makes the set fit a page. A set of 50 or
    # fewer comes back with its rows beside the counts. For a person this is the
    # answer rather than a summary of it. "621 filed, 617 closed, 4 open,
    # concentrated 2014-2016, mostly Backend User Interface" says what a page of 50
    # out of 621 cannot. Ask for it whenever the question is what somebody or some
    # area has been about. Ask for the rows once the question is which issue to
    # read. It costs a read per hundred issues and stops at a thousand, and it says
    # so where it did.
    breakdown: boolean  # optional
    # One of: open, closed, all. Which statuses the enumeration covers. "open", the
    # default, is the tracker's own unresolved set; "closed" is what it has marked
    # closed, Rejected included; "all" is both. A question about a person needs
    # "all". What somebody has filed over the years is mostly closed, and an
    # enumeration that hides those answers 4 where the number is 621.
    status: string  # optional
    # How many entries come back. A search answers with at most 25 whatever you ask
    # for. Other words reach a set that needs a page, and more of these do not. An
    # enumeration stops at 50 a page; offset reaches the next one, and breakdown
    # answers how a larger set spreads.
    limit: integer  # optional

The call carries exactly one of these sets of arguments:

- ``issue``
- ``query``
- ``backlog``

Answers with
------------

.. code-block:: yaml

    # One of: answered, empty, unavailable.
    status: string
    # The tracker the answer came from.
    source: string
    # What the call read, so you can ask the same question again by hand. A union is
    # two reads, and both stand here with a space between them.
    url: string
    # The words the search sent to the tracker, so you can ask a set that looks too
    # narrow again in other words. Empty on a read by number and on an enumeration.
    query: string
    # The TYPO3 version of the installation the tool placed the names in cites
    # against, so a verdict about a symbol has a version. Empty where the tool found
    # no installation, and then every cited name is unplaced: a statement about this
    # machine and not about the code.
    placedAgainst: string
    # How many issues matched in total, of which results carries at most limit.
    # Where the two differ the answer is a page and not the set. A narrower filter
    # reaches more of it, and a bigger limit does not. Zero on a read by number.
    total: integer
    # What each word of the query reaches on its own, which says which of them
    # emptied the answer. Two class names look alike from here and the tracker may
    # know only one, so the tool reads this rather than guesses. It asks on a miss
    # alone, one read per word. That is why a query holds more than one word and no
    # more than a few. Empty otherwise, and short of the query where the tracker
    # stopped its answers partway through it.
    terms:
      - # The word, lowercased as the search used it.
        term: string
        matchCount: integer
    # Every area the core files its issues under, read from the project itself. You
    # correct a category word that matches none or several from the answer rather
    # than from a second call. Answered where category did not resolve to exactly
    # one area, and where it is "*". Empty otherwise, which says nothing about the
    # project: the project administers the vocabulary and a copy here goes stale.
    categories: [string]
    # The categories the category word resolved to, in the tracker's own spelling.
    # Empty where the call named none. Empty too where the word matched none, which
    # answers no issues and is a statement about the word rather than about the
    # backlog.
    categoriesUsed: [string]
    # What reportedBy, assignedTo and involving resolved to, one entry per name the
    # call carried, in that order. The tool resolves a name against the core
    # project's members. Where they hold no membership, it resolves it against the
    # reporters and assignees of the issues that carry the name. Empty where the
    # call passed no name.
    people:
      - # One of: reportedBy, assignedTo, involving. Which argument the entry
        # answers for.
        filter: string
        # The name the call passed, as it passed it.
        asked: string
        # The person it resolved to, in the tracker's own spelling. Empty where it
        # resolved to nobody, which answers no issues and is a statement about the
        # name rather than about the backlog.
        name: string
        # The tracker's own user id, which is what it filters by and the only thing
        # it takes. Zero where the name resolved to nobody.
        id: integer
        # The people the name could have meant, where it reached more than one. A
        # name that reaches two resolves to neither and the tool reads nothing. Two
        # people merged into one backlog is a wrong answer nothing says is wrong.
        # Ask again with one of these. Empty where the name resolved, and where
        # nothing here carries it. That is a name this server cannot place rather
        # than a person who has filed nothing.
        candidates: [string]
    # How the matched set spreads, where breakdown is true. Null otherwise. results
    # carries the rows beside it where the set is complete and holds 50 or fewer.
    breakdown:
      # How many issues the counts are over. Equal to total where the read covered
      # the whole set.
      read: integer
      # Whether that is the whole matched set. False where the bound cut the read.
      # Then the counts are over the first issues read in the order asked for, the
      # oldest or the longest untouched. That is a shape of that end and not of the
      # set. Narrow the filters for the whole one.
      complete: boolean
      # One entry per dimension, always the five.
      counts:
        - # One of: status, tracker, category, year, touched. The dimension the
          # count runs over. year is the filing year, touched the year anything last
          # moved on the issue.
          dimension: string
          # The largest buckets first, and by name where two are the same size.
          buckets:
            - # The value, or "none" for the issues that carry none. An issue filed
              # under no area is a bucket rather than a row left out, so the buckets
              # add up to read.
              name: string
              count: integer
          # How many further buckets this dimension has, zero where it has none. The
          # tail of an area count is subsystems with one issue each.
          withheldBuckets: integer
          # How many issues those hold together, so the listed buckets and this add
          # up to read.
          withheldCount: integer
    # What words were matched against, where the call passed words. Null otherwise.
    wordsScan:
      # How many issues the other filters selected and the words were matched
      # against.
      read: integer
      # Whether that is the whole set the other filters select. False where the read
      # stopped at a thousand. Then total counts the matches in that part and is a
      # floor.
      complete: boolean
    # The issue, where status says answered and the call named a number. Null
    # otherwise.
    issue:
      id: integer
      subject: string
      # New, Accepted, Resolved, Closed, Rejected — the tracker's own word.
      status: string
      # Bug, Feature, Task, Epic.
      tracker: string
      priority: string
      # Who the tracker says holds this, empty where nobody does. An assignee is not
      # a promise that somebody works on it. On an issue nothing has moved on for
      # years it is usually who last did.
      assignedTo: string
      # The release the tracker schedules it for, empty where it names none.
      targetVersion: string
      # The TYPO3 version the report names, which is not the version it still
      # reproduces on.
      typo3Version: string
      phpVersion: string
      createdOn: string
      updatedOn: string
      # Where a person reads it.
      url: string
      # The report as the reporter wrote it, which is what the reporter saw and not
      # what the triage decided.
      description: string
      # Issues the tracker relates this one to, which is where it names a duplicate,
      # a blocker, and the issue of a revert. Each carries its subject, so you
      # decide from here which of them is worth a read, rather than from one call
      # each.
      relations:
        - # The other issue.
          issue: integer
          # What the issue is about, so a caller judges it without a read. Empty
          # where the tracker did not answer the one call that fills the whole set.
          subject: string
          # Bug, Feature, Task.
          tracker: string
          # Where the other issue stands.
          status: string
          # Where a person reads it.
          url: string
          # duplicates, relates, blocked, precedes.
          relation: string
      # The issues the description and the comments cite and no relation carries,
      # written as #NNNN or as a URL. A relation is somebody's triage; this is the
      # writer's own claim about prior art. On an old report that claim regularly
      # carries the case and is regularly wrong. Read it before you frame a patch
      # against it, and never pass this issue over as a duplicate on its strength.
      # Only a number the tracker answered for is here, which keeps a version out of
      # it. Empty where the texts cite nothing and where every citation is already a
      # relation.
      mentioned:
        - # The issue the text cites.
          issue: integer
          # What the issue is about, so a caller judges it without a read. Empty
          # where the tracker did not answer the one call that fills the whole set.
          subject: string
          # Bug, Feature, Task.
          tracker: string
          # Where the cited issue stands, which is what says whether the prior art
          # was dealt with.
          status: string
          # Where a person reads it.
          url: string
          # One of: description, note. Which text cites it. A number both of them
          # carry is a description, which is where the reporter framed the report.
          where: string
      # Every change on review.typo3.org the tool knows this issue to have, joined
      # from two sources. One is the handles the description and the journal name,
      # lifted out of the prose. The other is the changes whose commit message names
      # the issue number, asked of the review server. Neither half contains the
      # other, so an empty list means neither source has one. A change the prose
      # named carries the patch set and the date of that prose, while the state
      # beside it is the review server's. What a reviewer objected to is the
      # argument on the change, which is a typo3_gerrit_lookup call. An ABANDONED is
      # grounds to read that argument rather than to pass the issue over.
      reviews:
        - # The change number on review.typo3.org, which is what typo3_gerrit_lookup
          # takes as change.
          change: integer
          # NEW while the change is open, MERGED once it landed, ABANDONED once
          # somebody gave it up. That is where it stood at the call to the review
          # server. Empty where the tool did not ask or the server named no state,
          # which includes every change only the prose names.
          status: string
          # Where a person reads the change.
          url: string
          # The Change-Id the commit message carries, empty where no note named one.
          # typo3_gerrit_lookup takes this too, and it is what survives a rebase
          # onto another branch.
          changeId: string
          # The highest patch set a note mentioned, zero where none did. The review
          # server may be further along.
          patchSet: integer
          # The date of the last note that names this change, which is how old the
          # reference is and not when the change last moved.
          on: string
      # The files on the issue. On a report about a render these are usually
      # screenshots and regularly where the evidence is. A comment made of
      # !image.jpg! references reads as empty otherwise. Empty where the issue
      # carries none.
      attachments:
        - # The file name of the upload, which is also how a comment refers to it:
          # Redmine writes an inline image as !name.png! and says nothing else about
          # it.
          filename: string
          # image/png, image/jpeg, text/plain.
          contentType: string
          # Bytes.
          size: integer
          # The date of the upload, which says which comment it belongs to.
          on: string
          # Where the file itself is. It answers without a credential, and the read
          # is the caller's: nothing here fetches or transcribes one.
          url: string
      # The classes, methods and core files this report names, each with where it
      # stands in the packages this installation ships. A stale issue's status has
      # not moved by definition, so this is what says a 2015 report is about code
      # rewritten since. Read it before you open the checkout, as where a symbol is
      # rather than whether the defect reproduces. Read from the subject, the
      # description and every comment, which is where a reproduction regularly names
      # the class the description never did. Empty where the text names none, which
      # is the ordinary case for a report about a TCA key, a TypoScript path or a
      # table column.
      cites:
        - # The class, or the path of the file, as the report names it: a namespace
          # without the backslash in front, and a path from typo3/sysext/.
          name: string
          # One of: qualified, unqualified, file. How the report named it.
          # "qualified" is a class with its namespace, which places it without a
          # guess. "unqualified" is a bare class name, placed by the name of its
          # file. It can land on a package the report was never about, and one that
          # matches two packages names both. The tool takes a bare name only where
          # the report marks it as code or an installed package ships one under it.
          # A capitalised word is as often the label of a button. "file" is a path
          # in the core tree, as a pasted stack trace writes it.
          kind: string
          # The method the report names on that class, empty where it names none. A
          # ::class and a ::CONSTANT are not one, and the answer carries the class
          # alone.
          method: string
          # One of: shipped, notShipped, unplaced. "shipped" is a name an installed
          # package carries, the method included where the report named one.
          # "notShipped" is a name nothing installed carries. A name the core
          # removed and one from an extension you never installed look the same, and
          # the report does not tell the two apart. Where in has entries, the class
          # stands and the method named on it does not. "unplaced" is a name the
          # tool could not place at all: no installed package owns the namespace, or
          # there is no installation to read here. Never read unplaced as gone.
          state: string
          # Where the tool found it, one entry per package that carries it. Several
          # where a bare name matches more than one, and the choice between those is
          # the caller's. Empty where the tool found nothing and where nothing could
          # place it.
          in:
            - # The extension key of the package that carries it.
              extension: string
              # Where the file sits, from the installation root, so you open it
              # without a search.
              path: string
      # How many comments the issue carries in total.
      noteCount: integer
      # How many of those a review bot wrote, which notes: "people" is what drops.
      # Answered whichever value notes has. A journal full of patch-set pings that
      # answers zero here means the list of bot names has gone stale, not that
      # nobody pushed a patch.
      botNoteCount: integer
      # The most recent comments, oldest first. A closure, a reassignment and a "we
      # will not do this" are here rather than in the description.
      notes:
        - author: string
          on: string
          note: string
    # The issues the query matched or the enumeration selected, in the tracker's own
    # order — nothing here ranks them. An enumerated row also carries its
    # relations, its attachments and its reviews. Those three say whether somebody
    # answered it elsewhere or already attempted it, without a read of the whole
    # row. Empty on a read by number.
    results:
      - # The issue number, which is what this tool reads whole.
        issue: integer
        subject: string
        # Bug, Feature, Task, Epic.
        tracker: string
        # Where it stands: New, Accepted, Under Review, Resolved, Closed, Rejected.
        status: string
        # The area the core files it under, empty where it names none. A search hit
        # is a title and carries none of the five fields below, so one further call
        # reads them for the whole page. Empty here can mean that call did not reach
        # the tracker rather than that the issue has no area.
        category: string
        # Who filed it. This is the dimension reportedBy selects on, and a read of
        # it off a set answers who reports a backlog without a call per row.
        reportedBy: string
        # Who the tracker says holds this, empty where nobody does. What it decides
        # for a triage is whether the issue is free to take. On an old one it is
        # usually who last touched it rather than who is on it.
        assignedTo: string
        # When it was filed.
        createdOn: string
        # When anything last moved on it, which is the measure of neglect rather
        # than of age.
        updatedOn: string
        # Where a person reads it.
        url: string
        # The issues the tracker relates this one to, each with its subject, so a
        # row that duplicates something already decided shows without a read.
        # Answered on an enumeration and empty on a search hit, where nothing asked
        # for them.
        relations:
          - # The other issue.
            issue: integer
            # What the issue is about, so a caller judges it without a read. Empty
            # where the tracker did not answer the one call that fills the whole
            # set.
            subject: string
            # Bug, Feature, Task.
            tracker: string
            # Where the other issue stands.
            status: string
            # Where a person reads it.
            url: string
            # duplicates, relates, blocked, precedes.
            relation: string
        # The files on the issue, which on a report about a render are usually where
        # the evidence is. A report whose evidence is a screenshot is a different
        # candidate to one whose evidence is prose. Answered on an enumeration and
        # empty on a search hit, where nothing asked for them.
        attachments:
          - # The file name of the upload, which is also how a comment refers to it:
            # Redmine writes an inline image as !name.png! and says nothing else
            # about it.
            filename: string
            # image/png, image/jpeg, text/plain.
            contentType: string
            # Bytes.
            size: integer
            # The date of the upload, which says which comment it belongs to.
            on: string
            # Where the file itself is. It answers without a credential, and the
            # read is the caller's: nothing here fetches or transcribes one.
            url: string
        # The classes, methods and core files this report names, each with where it
        # stands in the packages this installation ships. A stale issue's status has
        # not moved by definition, so this is what says a 2015 report is about code
        # rewritten since. Read it before you open the checkout, as where a symbol
        # is rather than whether the defect reproduces. Read from the subject and
        # the description, which is what the page carries. An enumerated row holds
        # no comment, so a report that names its code only in one cites nothing
        # here. Empty on a search hit, where nothing asks for it. Empty where the
        # text names none, which is the ordinary case for a report about a TCA key,
        # a TypoScript path or a table column.
        cites:
          - # The class, or the path of the file, as the report names it: a
            # namespace without the backslash in front, and a path from
            # typo3/sysext/.
            name: string
            # One of: qualified, unqualified, file. How the report named it.
            # "qualified" is a class with its namespace, which places it without a
            # guess. "unqualified" is a bare class name, placed by the name of its
            # file. It can land on a package the report was never about, and one
            # that matches two packages names both. The tool takes a bare name only
            # where the report marks it as code or an installed package ships one
            # under it. A capitalised word is as often the label of a button. "file"
            # is a path in the core tree, as a pasted stack trace writes it.
            kind: string
            # The method the report names on that class, empty where it names none.
            # A ::class and a ::CONSTANT are not one, and the answer carries the
            # class alone.
            method: string
            # One of: shipped, notShipped, unplaced. "shipped" is a name an
            # installed package carries, the method included where the report named
            # one. "notShipped" is a name nothing installed carries. A name the core
            # removed and one from an extension you never installed look the same,
            # and the report does not tell the two apart. Where in has entries, the
            # class stands and the method named on it does not. "unplaced" is a name
            # the tool could not place at all: no installed package owns the
            # namespace, or there is no installation to read here. Never read
            # unplaced as gone.
            state: string
            # Where the tool found it, one entry per package that carries it.
            # Several where a bare name matches more than one, and the choice
            # between those is the caller's. Empty where the tool found nothing and
            # where nothing could place it.
            in:
              - # The extension key of the package that carries it.
                extension: string
                # Where the file sits, from the installation root, so you open it
                # without a search.
                path: string
        # The changes whose commit message names this issue, each with the state it
        # is in. One query to the review server reads them for the whole page. A
        # state and not a verdict: what a reviewer objected to is the argument on
        # the change, which is a typo3_gerrit_lookup call. An ABANDONED says to read
        # that argument rather than pass the issue over. The approach can be the
        # rejected part while the defect is real. Empty where nothing on the review
        # server names the issue and where the review server did not answer, which
        # this does not separate. Empty on a search hit too, where nothing asks for
        # it.
        reviews:
          - # The change number on review.typo3.org, which is what
            # typo3_gerrit_lookup takes as change.
            change: integer
            # NEW while the change is open, MERGED once it landed, ABANDONED once
            # somebody gave it up: where it stood at the read of the page. Empty
            # where the review server named no state.
            status: string
            # Where a person reads the change.
            url: string
        # True where the issue stands Under Review and every change in reviews is
        # ABANDONED. The status then claims a review nobody runs. False everywhere
        # else, a search hit included.
        reviewAbandoned: boolean
        # When a person last wrote a comment on it, bots left out. updatedOn moves
        # on any edit, a field somebody changed on many issues at once included, so
        # this is the measure of attention on the stale end. Read from the journal,
        # one read per row, on backlog "stale" alone. Empty everywhere else, and
        # where nobody ever commented.
        lastNoteOn: string
    # When this server read the tracker for this answer, in UTC as ISO 8601:
    # 2026-10-27T14:03:09Z. The process holds an answered read for 300 seconds, so
    # this can be earlier than the call. Where the answer stands on several reads,
    # this is the earliest of them. updatedOn says when the issue last moved, and
    # this says how old the read of it is. The list of areas and of people is held
    # for a day and does not count here.
    readAt: string
    # Why the source answered nothing, where status says unavailable. Null
    # otherwise.
    unavailable:
      # One of: source-not-answering, source-not-parseable. source-not-answering:
      # the tracker did not answer this time. source-not-parseable: something
      # answered with a page rather than with the API, which is what the bot
      # protection in front of it looks like from here.
      cause: string
      reason: string

Answered
--------

Recorded on 2026-10-28 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 15.0.0-dev, the main core checkout below .checkouts/. Its
console is out of reach: <installation> has no TYPO3 console — none of
bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed —
vendor/autoload.php is not there either, and composer install writes both.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

forge: what an issue says and what was decided
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "issue": "110348"
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: no issue matches "" at https://forge.typo3.org.
    These words matched nothing, which is not that nobody reported it: an issue worded differently is invisible to a full-text search.
    Every word has to be in the same issue, so one word nobody wrote empties the answer whatever else is in it.
    Ask again with the two or three words that name the subject: a term nobody would have written — a method name, a class — is regularly the one that emptied it.
    What no wording of the report reaches is enumerated instead: backlog "newest" with createdSince from the day the defect could first have been reported, and limit 50. Add category in your own words — "import export", "rte" — only where the area is certain: thousands of the open bugs carry no Category at all, and an area filter reaches none of them.
    Those subjects settle whether somebody already reported this where total and the rows agree, and are the recent end of a larger set where they do not — narrow the window until they do.
    Where the words are a person, pass them as reportedBy or assignedTo with backlog.

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/search.json?q=&issues=1&limit=15",
        "query": "",
        "placedAgainst": "",
        "total": 0,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [],
        "readAt": "2026-10-28T10:31:19Z",
        "unavailable": null
    }

forge: an issue whose evidence hangs off it
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "issue": "88556"
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: no issue matches "" at https://forge.typo3.org.
    These words matched nothing, which is not that nobody reported it: an issue worded differently is invisible to a full-text search.
    Every word has to be in the same issue, so one word nobody wrote empties the answer whatever else is in it.
    Ask again with the two or three words that name the subject: a term nobody would have written — a method name, a class — is regularly the one that emptied it.
    What no wording of the report reaches is enumerated instead: backlog "newest" with createdSince from the day the defect could first have been reported, and limit 50. Add category in your own words — "import export", "rte" — only where the area is certain: thousands of the open bugs carry no Category at all, and an area filter reaches none of them.
    Those subjects settle whether somebody already reported this where total and the rows agree, and are the recent end of a larger set where they do not — narrow the window until they do.
    Where the words are a person, pass them as reportedBy or assignedTo with backlog.

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/search.json?q=&issues=1&limit=15",
        "query": "",
        "placedAgainst": "",
        "total": 0,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [],
        "readAt": "2026-10-28T10:31:19Z",
        "unavailable": null
    }

forge: an issue without the patch-set pings
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "issue": "14858",
        "notes": "people"
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: no issue matches "" at https://forge.typo3.org.
    These words matched nothing, which is not that nobody reported it: an issue worded differently is invisible to a full-text search.
    Every word has to be in the same issue, so one word nobody wrote empties the answer whatever else is in it.
    Ask again with the two or three words that name the subject: a term nobody would have written — a method name, a class — is regularly the one that emptied it.
    What no wording of the report reaches is enumerated instead: backlog "newest" with createdSince from the day the defect could first have been reported, and limit 50. Add category in your own words — "import export", "rte" — only where the area is certain: thousands of the open bugs carry no Category at all, and an area filter reaches none of them.
    Those subjects settle whether somebody already reported this where total and the rows agree, and are the recent end of a larger set where they do not — narrow the window until they do.
    Where the words are a person, pass them as reportedBy or assignedTo with backlog.

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/search.json?q=&issues=1&limit=15",
        "query": "",
        "placedAgainst": "",
        "total": 0,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [],
        "readAt": "2026-10-28T10:31:19Z",
        "unavailable": null
    }

forge: no such issue
~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "issue": "99999999"
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: no issue matches "" at https://forge.typo3.org.
    These words matched nothing, which is not that nobody reported it: an issue worded differently is invisible to a full-text search.
    Every word has to be in the same issue, so one word nobody wrote empties the answer whatever else is in it.
    Ask again with the two or three words that name the subject: a term nobody would have written — a method name, a class — is regularly the one that emptied it.
    What no wording of the report reaches is enumerated instead: backlog "newest" with createdSince from the day the defect could first have been reported, and limit 50. Add category in your own words — "import export", "rte" — only where the area is certain: thousands of the open bugs carry no Category at all, and an area filter reaches none of them.
    Those subjects settle whether somebody already reported this where total and the rows agree, and are the recent end of a larger set where they do not — narrow the window until they do.
    Where the words are a person, pass them as reportedBy or assignedTo with backlog.

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/search.json?q=&issues=1&limit=15",
        "query": "",
        "placedAgainst": "",
        "total": 0,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [],
        "readAt": "2026-10-28T10:31:19Z",
        "unavailable": null
    }

forge: which other issues describe this
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "cache busting",
        "limit": 3
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: 3 issues match "cache busting"
    A full-text match over subject, description and comments, in the tracker's own order and unranked. Another wording finds another set, so this is which issues mention it rather than which one it duplicates. Read one whole by passing its number as issue.
    Where those words are a person's name, this is the issues that mention them and not the issues that are theirs: pass the name as reportedBy or assignedTo with backlog for that, which is a different set and regularly two orders of magnitude larger.

    ## #110991 Register default frontend JavaScript directly
    Task · Under Review · filed by Wouter Wolters · assigned to Wouter Wolters · filed 2026-10-09 · last touched 2026-10-09 · https://forge.typo3.org/issues/110991

    ## #107904 Cache-busting applied to folder paths
    Bug · Closed · Frontend · filed by Simon Praetorius · filed 2025-10-29 · last touched 2025-12-02 · https://forge.typo3.org/issues/107904

    ## #107869 Add option to not add cache busting to generated URIs
    Bug · Closed · filed by Helmut Hummel · filed 2025-10-27 · last touched 2025-12-02 · https://forge.typo3.org/issues/107869

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/search.json?q=cache%20busting&issues=1&limit=3",
        "query": "cache busting",
        "placedAgainst": "",
        "total": 16,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [
            {
                "issue": 110991,
                "subject": "Register default frontend JavaScript directly",
                "tracker": "Task",
                "status": "Under Review",
                "category": "",
                "reportedBy": "Wouter Wolters",
                "assignedTo": "Wouter Wolters",
                "createdOn": "2026-10-09T16:40:20Z",
                "updatedOn": "2026-10-09T16:40:36Z",
                "url": "https://forge.typo3.org/issues/110991",
                "relations": [],
                "attachments": [],
                "reviews": [],
                "cites": [],
                "lastNoteOn": "",
                "reviewAbandoned": false
            },
            {
                "issue": 107904,
                "subject": "Cache-busting applied to folder paths",
                "tracker": "Bug",
                "status": "Closed",
                "category": "Frontend",
                "reportedBy": "Simon Praetorius",
                "assignedTo": "",
                "createdOn": "2025-10-29T11:00:18Z",
                "updatedOn": "2025-12-02T12:04:43Z",
                "url": "https://forge.typo3.org/issues/107904",
                "relations": [],
                "attachments": [],
                "reviews": [],
                "cites": [],
                "lastNoteOn": "",
                "reviewAbandoned": false
            },
            {
                "issue": 107869,
                "subject": "Add option to not add cache busting to generated URIs",
                "tracker": "Bug",
                "status": "Closed",
                "category": "",
                "reportedBy": "Helmut Hummel",
                "assignedTo": "",
                "createdOn": "2025-10-27T19:48:27Z",
                "updatedOn": "2025-12-02T12:04:41Z",
                "url": "https://forge.typo3.org/issues/107869",
                "relations": [],
                "attachments": [],
                "reviews": [],
                "cites": [],
                "lastNoteOn": "",
                "reviewAbandoned": false
            }
        ],
        "readAt": "2026-10-28T10:31:19Z",
        "unavailable": null
    }

forge: nothing matches these words
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "quantumflux transponder"
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: no issue matches "quantumflux transponder" at https://forge.typo3.org.
    These words matched nothing, which is not that nobody reported it: an issue worded differently is invisible to a full-text search.
    Every word has to be in the same issue, so one word nobody wrote empties the answer whatever else is in it.
    Asked one word at a time: "quantumflux" reaches 0 · "transponder" reaches 0.
    No issue on the tracker carries "quantumflux" or "transponder". A query one of them is in is empty whatever else is in it, so drop them.
    What no wording of the report reaches is enumerated instead: backlog "newest" with createdSince from the day the defect could first have been reported, and limit 50. Add category in your own words — "import export", "rte" — only where the area is certain: thousands of the open bugs carry no Category at all, and an area filter reaches none of them.
    Those subjects settle whether somebody already reported this where total and the rows agree, and are the recent end of a larger set where they do not — narrow the window until they do.
    Where the words are a person, pass them as reportedBy or assignedTo with backlog.

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/search.json?q=quantumflux%20transponder&issues=1&limit=15",
        "query": "quantumflux transponder",
        "placedAgainst": "",
        "total": 0,
        "terms": [
            {
                "term": "quantumflux",
                "matchCount": 0
            },
            {
                "term": "transponder",
                "matchCount": 0
            }
        ],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [],
        "readAt": "2026-10-28T10:31:23Z",
        "unavailable": null
    }

forge: which of the words emptied the answer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "file renderer RendererRegistry FileRendererInterface"
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: no issue matches "file renderer RendererRegistry FileRendererInterface" at https://forge.typo3.org.
    These words matched nothing, which is not that nobody reported it: an issue worded differently is invisible to a full-text search.
    Every word has to be in the same issue, so one word nobody wrote empties the answer whatever else is in it.
    Asked one word at a time: "file" reaches 14094 · "renderer" reaches 1197 · "RendererRegistry" reaches 5 · "FileRendererInterface" reaches 0.
    No issue on the tracker carries "FileRendererInterface". A query it is in is empty whatever else is in it, so drop it.
    "RendererRegistry" is the narrowest of the rest and reaches something: ask it on its own, then read the subjects.
    What no wording of the report reaches is enumerated instead: backlog "newest" with createdSince from the day the defect could first have been reported, and limit 50. Add category in your own words — "import export", "rte" — only where the area is certain: thousands of the open bugs carry no Category at all, and an area filter reaches none of them.
    Those subjects settle whether somebody already reported this where total and the rows agree, and are the recent end of a larger set where they do not — narrow the window until they do.
    Where the words are a person, pass them as reportedBy or assignedTo with backlog.

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/search.json?q=file%20renderer%20RendererRegistry%20FileRendererInterface&issues=1&limit=15",
        "query": "file renderer RendererRegistry FileRendererInterface",
        "placedAgainst": "",
        "total": 0,
        "terms": [
            {
                "term": "file",
                "matchCount": 14094
            },
            {
                "term": "renderer",
                "matchCount": 1197
            },
            {
                "term": "RendererRegistry",
                "matchCount": 5
            },
            {
                "term": "FileRendererInterface",
                "matchCount": 0
            }
        ],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [],
        "readAt": "2026-10-28T10:31:32Z",
        "unavailable": null
    }

forge: the oldest issues nobody has resolved
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "backlog": "oldest",
        "limit": 3
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: 3 of 1950 open issues of the TYPO3 Core project, oldest filed first
    This is a page and not the set. offset=3 reads the next page. words narrows it to a topic, and a narrower filter — an earlier date, one tracker — to a part of it. breakdown answers how the whole of it is distributed.
    Age is a candidate and never a finding: read one whole by passing its number as issue, and what it still claims is established in the checkout rather than off this list.
    A row carries what the page came back with: the issues it is filed against, the files hanging off it, and the changes on review.typo3.org whose commit message names it, each with the state it is in. That state is where a change stands and not a verdict on the issue: an ABANDONED one is grounds to read the argument on it with typo3_gerrit_lookup, where the objection was written down and is regularly to the approach rather than to the defect. A row with no such line is one nothing there names — or one the review server did not answer for, which this list does not separate.

    ## #14277 Start/Stop time for pages is ignored in standard menu objects
    Feature · Under Review · Frontend · filed by Michael Stucki · unassigned · filed 2004-08-20 · last touched 2026-10-22 · https://forge.typo3.org/issues/14277
    Relation: relates #16815 — Bug · Closed · Sitemap ignoring "Start" and "End" flags · https://forge.typo3.org/issues/16815
    Relation: relates #98964 — Bug · Closed · Menu object caching creates too many records resulting in huge cache_hash table · https://forge.typo3.org/issues/98964
    Review: change 96945 · NEW · https://review.typo3.org/c/Packages/TYPO3.CMS/+/96945
    Review: change 61395 · ABANDONED · https://review.typo3.org/c/Packages/TYPO3.CMS/+/61395

    ## #14858 extended clipboard: setCopyMode can`t be set to copy by default
    Bug · New · Backend User Interface · filed by Sacha Vorbeck · unassigned · filed 2005-07-11 · last touched 2026-01-23 · https://forge.typo3.org/issues/14858
    Relation: relates #90676 — Epic · Accepted · Clipboard related bugs and features · https://forge.typo3.org/issues/90676
    Relation: duplicates #70759 — Feature · Closed · Changing the default clipboard option from  "move elements"  to "copy elements" · https://forge.typo3.org/issues/70759
    Review: change 70962 · ABANDONED · https://review.typo3.org/c/Packages/TYPO3.CMS/+/70962
    Review: change 38419 · ABANDONED · https://review.typo3.org/c/Packages/TYPO3.CMS/+/38419

    ## #15984 menu.showAccessRestrictedPages doesn't replace link for  "include subpages"
    Bug · Accepted · Frontend · filed by Wolfgang Sassik · unassigned · filed 2006-04-05 · last touched 2026-04-15 · https://forge.typo3.org/issues/15984
    Relation: relates #22860 — Bug · Closed · typolinkLinkAccessRestrictedPages_addParams doesn't work on restricted subpages · https://forge.typo3.org/issues/22860
    Relation: relates #26484 — Bug · Closed · extend to subpages in page properties in access tab does not work correctly · https://forge.typo3.org/issues/26484
    Relation: relates #78825 — Bug · Closed · Wrong pid determination when opening a nested access restriced page · https://forge.typo3.org/issues/78825
    Relation: precedes #32756 — Bug · Closed · Massive Memory Leak in 4.5.8+ / 4.6 · https://forge.typo3.org/issues/32756
    Files (1): 3129.diff
    Review: change 2545 · MERGED · https://review.typo3.org/c/Packages/TYPO3.CMS/+/2545
    Review: change 2544 · ABANDONED · https://review.typo3.org/c/Packages/TYPO3.CMS/+/2544
    Review: change 1186 · MERGED · https://review.typo3.org/c/Packages/TYPO3.CMS/+/1186

    ## What a page of the backlog opens
    `typo3-core-issue-triage` is the workflow a caller holding this page is in, and opening it comes before deciding anything about a row. Hand the page over rather than choosing from it: triaging a backlog and triaging one issue are two jobs, and the second takes a number. Where the choice is yours, read these in order and stop at the first that decides:
    - What has already happened to it. The row carries the change and the state it stands in; the reading is the argument under that state. `typo3_gerrit_lookup` answers it by the number, before the checkout is opened, and an ABANDONED is grounds to read it rather than to pass the row over.
    - Whether the code it names is still there. The row carries every class, method and core file its text cites with where each stands in the packages installed here, so a report whose names are all gone is settled without opening the checkout. A name it could not place decides nothing.
    - The category, against the branch you are standing on. One naming a subsystem the branch no longer ships settles the issue unread.
    - Where the symptom appears. A rendered fragment, a stored row or a resolved value needs no installation; one that shows only after a backend interaction needs one standing up.
    - How far the mechanism reaches. One class and the behaviour in it is the settleable shape, and several with the order between them is an interaction.
    - What the suite already models. A case added to a test file that exists is a reproduction with no fixture to build.
    `typo3_changelog_lookup` is what says whether the area was reworked since, which is what turns a valid report into one about code that is gone. Say which reading decided, and say of the rows you passed over that you passed over them.

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?limit=3&include=relations%2Cattachments&status_id=open&sort=created_on%3Aasc",
        "query": "",
        "placedAgainst": "15.0.0-dev",
        "total": 1950,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [
            {
                "issue": 14277,
                "subject": "Start/Stop time for pages is ignored in standard menu objects",
                "tracker": "Feature",
                "status": "Under Review",
                "category": "Frontend",
                "reportedBy": "Michael Stucki",
                "assignedTo": "",
                "createdOn": "2004-08-20T08:45:13Z",
                "updatedOn": "2026-10-22T10:28:38Z",
                "url": "https://forge.typo3.org/issues/14277",
                "relations": [
                    {
                        "issue": 16815,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/16815",
                        "subject": "Sitemap ignoring \"Start\" and \"End\" flags",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 98964,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/98964",
                        "subject": "Menu object caching creates too many records resulting in huge cache_hash table",
                        "tracker": "Bug",
                        "status": "Closed"
                    }
                ],
                "attachments": [],
                "reviews": [
                    {
                        "change": 96945,
                        "status": "NEW",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/96945"
                    },
                    {
                        "change": 61395,
                        "status": "ABANDONED",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/61395"
                    }
                ],
                "cites": [],
                "lastNoteOn": "",
                "reviewAbandoned": false
            },
            {
                "issue": 14858,
                "subject": "extended clipboard: setCopyMode can`t be set to copy by default",
                "tracker": "Bug",
                "status": "New",
                "category": "Backend User Interface",
                "reportedBy": "Sacha Vorbeck",
                "assignedTo": "",
                "createdOn": "2005-07-11T23:31:03Z",
                "updatedOn": "2026-01-23T08:30:36Z",
                "url": "https://forge.typo3.org/issues/14858",
                "relations": [
                    {
                        "issue": 90676,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/90676",
                        "subject": "Clipboard related bugs and features",
                        "tracker": "Epic",
                        "status": "Accepted"
                    },
                    {
                        "issue": 70759,
                        "relation": "duplicates",
                        "url": "https://forge.typo3.org/issues/70759",
                        "subject": "Changing the default clipboard option from  \"move elements\"  to \"copy elements\"",
                        "tracker": "Feature",
                        "status": "Closed"
                    }
                ],
                "attachments": [],
                "reviews": [
                    {
                        "change": 70962,
                        "status": "ABANDONED",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/70962"
                    },
                    {
                        "change": 38419,
                        "status": "ABANDONED",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/38419"
                    }
                ],
                "cites": [],
                "lastNoteOn": "",
                "reviewAbandoned": false
            },
            {
                "issue": 15984,
                "subject": "menu.showAccessRestrictedPages doesn't replace link for  \"include subpages\"",
                "tracker": "Bug",
                "status": "Accepted",
                "category": "Frontend",
                "reportedBy": "Wolfgang Sassik",
                "assignedTo": "",
                "createdOn": "2006-04-05T03:07:50Z",
                "updatedOn": "2026-04-15T09:44:14Z",
                "url": "https://forge.typo3.org/issues/15984",
                "relations": [
                    {
                        "issue": 22860,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/22860",
                        "subject": "typolinkLinkAccessRestrictedPages_addParams doesn't work on restricted subpages",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 26484,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/26484",
                        "subject": "extend to subpages in page properties in access tab does not work correctly",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 78825,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/78825",
                        "subject": "Wrong pid determination when opening a nested access restriced page",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 32756,
                        "relation": "precedes",
                        "url": "https://forge.typo3.org/issues/32756",
                        "subject": "Massive Memory Leak in 4.5.8+ / 4.6",
                        "tracker": "Bug",
                        "status": "Closed"
                    }
                ],
                "attachments": [
                    {
                        "filename": "3129.diff",
                        "contentType": "application/octet-stream",
                        "size": 905,
                        "on": "2010-06-10T22:46:58Z",
                        "url": "https://forge.typo3.org/attachments/download/6964/3129.diff"
                    }
                ],
                "reviews": [
                    {
                        "change": 2545,
                        "status": "MERGED",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/2545"
                    },
                    {
                        "change": 2544,
                        "status": "ABANDONED",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/2544"
                    },
                    {
                        "change": 1186,
                        "status": "MERGED",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/1186"
                    }
                ],
                "cites": [],
                "lastNoteOn": "",
                "reviewAbandoned": false
            }
        ],
        "readAt": "2026-10-28T10:31:47Z",
        "unavailable": null
    }

forge: what is known about one area
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "backlog": "stale",
        "category": "rte",
        "tracker": "Bug",
        "limit": 3
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: 3 of 12 open issues of the TYPO3 Core project, tracker Bug, in RTE (rtehtmlarea + ckeditor), longest untouched first
    This is a page and not the set. offset=3 reads the next page. words narrows it to a topic, and a narrower filter — an earlier date, one tracker — to a part of it. breakdown answers how the whole of it is distributed.
    Age is a candidate and never a finding: read one whole by passing its number as issue, and what it still claims is established in the checkout rather than off this list.
    A row carries what the page came back with: the issues it is filed against, the files hanging off it, and the changes on review.typo3.org whose commit message names it, each with the state it is in. That state is where a change stands and not a verdict on the issue: an ABANDONED one is grounds to read the argument on it with typo3_gerrit_lookup, where the objection was written down and is regularly to the approach rather than to the defect. A row with no such line is one nothing there names — or one the review server did not answer for, which this list does not separate.
    A row that names code carries it: the classes, methods and core files its own text cites, each with whether the packages installed here still carry it, at TYPO3 15.0.0-dev. A report whose names are all gone is a candidate to drop without opening the checkout, and one whose names all stand is a candidate to read. It is read from the subject and the description, because the page carries no comment, and a name it could not place is unplaced rather than gone.
    An area is where an issue was filed and not everything it is about. A report about this one regularly sits under another area, so what came back is a floor rather than the set — query the words as well where the question is about a subject. An issue carrying no Category at all is in no area, and thousands of the open bugs carry none, so no wording of this reaches one: ask again without category, narrowed by createdSince instead, where the question is whether it was reported.
    On this end a row also says when a person last commented. last touched moves on any edit, a field somebody changed on many issues at once included, so the comment is the measure of attention.

    ## #88690 Translated content elements are not available in linkbrowser of the ckeditor in free mode
    Bug · New · RTE (rtehtmlarea + ckeditor) · filed by Ronny Hauptvogel · unassigned · filed 2019-07-05 · last touched 2023-03-05 · last comment 2023-03-05 · https://forge.typo3.org/issues/88690
    Relation: relates #89701 — Bug · Closed · Link wizard lists only content elements of the default language · https://forge.typo3.org/issues/89701
    Relation: relates #90138 — Feature · Closed · Language and mode (free or connected) should be handled in the links module when creating an anchor to content · https://forge.typo3.org/issues/90138
    Relation: relates #91160 — Bug · Closed · Links to content element (anchor) in link wizard not possible when not in default language · https://forge.typo3.org/issues/91160
    Relation: relates #88382 — Bug · Closed · Link wizard lists all content elements of a page regardless of source language · https://forge.typo3.org/issues/88382
    Relation: relates #92809 — Bug · Accepted · Anchor Links in Link Wizard not translated correctly · https://forge.typo3.org/issues/92809

    ## #102053 ckeditor - fixAttrib.class.default of A-link not applied when link is inside a list element
    Bug · New · RTE (rtehtmlarea + ckeditor) · filed by RVVN no-lastname-given · unassigned · filed 2023-09-27 · last touched 2023-09-28 · last comment 2023-09-28 · https://forge.typo3.org/issues/102053
    Relation: relates #92756 — Bug · Closed · RTE - fixAttrib to A-link not applied when link is inside list · https://forge.typo3.org/issues/92756
    Files (1): rvvn-issue-102053-fix.patch
    Cites: TYPO3\CMS\Core\Html\RteHtmlParser::TS_transform_db — shipped by core

    ## #102595 Not possible to override richtextConfiguration via TSconfig if in a flexform
    Bug · Accepted · RTE (rtehtmlarea + ckeditor) · filed by Sybille Peters · unassigned · filed 2023-12-04 · last touched 2024-06-26 · last comment 2024-06-25 · https://forge.typo3.org/issues/102595
    Relation: relates #103532 — Bug · Under Review · Using TSconfig TCEFORM to change existing flexform field configuration does not work · https://forge.typo3.org/issues/103532
    Cites: PageTsconfig — shipped by backend and core
    Review: change 83728 · NEW · https://review.typo3.org/c/Packages/TYPO3.CMS/+/83728
    Review: change 83646 · ABANDONED · https://review.typo3.org/c/Packages/TYPO3.CMS/+/83646

    ## What a page of the backlog opens
    `typo3-core-issue-triage` is the workflow a caller holding this page is in, and opening it comes before deciding anything about a row. Hand the page over rather than choosing from it: triaging a backlog and triaging one issue are two jobs, and the second takes a number. Where the choice is yours, read these in order and stop at the first that decides:
    - What has already happened to it. The row carries the change and the state it stands in; the reading is the argument under that state. `typo3_gerrit_lookup` answers it by the number, before the checkout is opened, and an ABANDONED is grounds to read it rather than to pass the row over.
    - Whether the code it names is still there. The row carries every class, method and core file its text cites with where each stands in the packages installed here, so a report whose names are all gone is settled without opening the checkout. A name it could not place decides nothing.
    - The category, against the branch you are standing on. One naming a subsystem the branch no longer ships settles the issue unread.
    - Where the symptom appears. A rendered fragment, a stored row or a resolved value needs no installation; one that shows only after a backend interaction needs one standing up.
    - How far the mechanism reaches. One class and the behaviour in it is the settleable shape, and several with the order between them is an interaction.
    - What the suite already models. A case added to a test file that exists is a reproduction with no fixture to build.
    `typo3_changelog_lookup` is what says whether the area was reworked since, which is what turns a valid report into one about code that is gone. Say which reading decided, and say of the rows you passed over that you passed over them.

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?limit=3&include=relations%2Cattachments&status_id=open&sort=updated_on%3Aasc&tracker_id=1&category_id=1001",
        "query": "",
        "placedAgainst": "15.0.0-dev",
        "total": 12,
        "terms": [],
        "categories": [],
        "categoriesUsed": [
            "RTE (rtehtmlarea + ckeditor)"
        ],
        "people": [],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [
            {
                "issue": 88690,
                "subject": "Translated content elements are not available in linkbrowser of the ckeditor in free mode",
                "tracker": "Bug",
                "status": "New",
                "category": "RTE (rtehtmlarea + ckeditor)",
                "reportedBy": "Ronny Hauptvogel",
                "assignedTo": "",
                "createdOn": "2019-07-05T11:01:00Z",
                "updatedOn": "2023-03-05T17:47:02Z",
                "url": "https://forge.typo3.org/issues/88690",
                "relations": [
                    {
                        "issue": 89701,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/89701",
                        "subject": "Link wizard lists only content elements of the default language",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 90138,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/90138",
                        "subject": "Language and mode (free or connected) should be handled in the links module when creating an anchor to content",
                        "tracker": "Feature",
                        "status": "Closed"
                    },
                    {
                        "issue": 91160,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/91160",
                        "subject": "Links to content element (anchor) in link wizard not possible when not in default language",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 88382,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/88382",
                        "subject": "Link wizard lists all content elements of a page regardless of source language",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 92809,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/92809",
                        "subject": "Anchor Links in Link Wizard not translated correctly",
                        "tracker": "Bug",
                        "status": "Accepted"
                    }
                ],
                "attachments": [],
                "reviews": [],
                "cites": [],
                "lastNoteOn": "2023-03-05T17:47:02Z",
                "reviewAbandoned": false
            },
            {
                "issue": 102053,
                "subject": "ckeditor - fixAttrib.class.default of A-link not applied when link is inside a list element",
                "tracker": "Bug",
                "status": "New",
                "category": "RTE (rtehtmlarea + ckeditor)",
                "reportedBy": "RVVN no-lastname-given",
                "assignedTo": "",
                "createdOn": "2023-09-27T18:40:33Z",
                "updatedOn": "2023-09-28T20:35:57Z",
                "url": "https://forge.typo3.org/issues/102053",
                "relations": [
                    {
                        "issue": 92756,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/92756",
                        "subject": "RTE - fixAttrib to A-link not applied when link is inside list",
                        "tracker": "Bug",
                        "status": "Closed"
                    }
                ],
                "attachments": [
                    {
                        "filename": "rvvn-issue-102053-fix.patch",
                        "contentType": "text/plain",
                        "size": 432,
                        "on": "2023-09-27T18:39:53Z",
                        "url": "https://forge.typo3.org/attachments/download/37988/rvvn-issue-102053-fix.patch"
                    }
                ],
                "reviews": [],
                "cites": [
                    {
                        "name": "TYPO3\\CMS\\Core\\Html\\RteHtmlParser",
                        "kind": "qualified",
                        "method": "TS_transform_db",
                        "state": "shipped",
                        "in": [
                            {
                                "extension": "core",
                                "path": "typo3/sysext/core/Classes/Html/RteHtmlParser.php"
                            }
                        ]
                    }
                ],
                "lastNoteOn": "2023-09-28T07:04:56Z",
                "reviewAbandoned": false
            },
            {
                "issue": 102595,
                "subject": "Not possible to override richtextConfiguration via TSconfig if in a flexform",
                "tracker": "Bug",
                "status": "Accepted",
                "category": "RTE (rtehtmlarea + ckeditor)",
                "reportedBy": "Sybille Peters",
                "assignedTo": "",
                "createdOn": "2023-12-04T05:11:41Z",
                "updatedOn": "2024-06-26T03:47:55Z",
                "url": "https://forge.typo3.org/issues/102595",
                "relations": [
                    {
                        "issue": 103532,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/103532",
                        "subject": "Using TSconfig TCEFORM to change existing flexform field configuration does not work",
                        "tracker": "Bug",
                        "status": "Under Review"
                    }
                ],
                "attachments": [],
                "reviews": [
                    {
                        "change": 83728,
                        "status": "NEW",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/83728"
                    },
                    {
                        "change": 83646,
                        "status": "ABANDONED",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/83646"
                    }
                ],
                "cites": [
                    {
                        "name": "PageTsconfig",
                        "kind": "unqualified",
                        "method": "",
                        "state": "shipped",
                        "in": [
                            {
                                "extension": "backend",
                                "path": "typo3/sysext/backend/Classes/Form/FormDataProvider/PageTsConfig.php"
                            },
                            {
                                "extension": "core",
                                "path": "typo3/sysext/core/Classes/TypoScript/PageTsConfig.php"
                            }
                        ]
                    }
                ],
                "lastNoteOn": "2024-06-25T20:24:05Z",
                "reviewAbandoned": false
            }
        ],
        "readAt": "2026-10-28T10:31:48Z",
        "unavailable": null
    }

forge: a word that names no area
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "backlog": "oldest",
        "category": "quantumflux"
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: "quantumflux" names no area the core files issues under, so nothing was read. That is about the word and not about the backlog.
    The areas are: AdminPanel, Authentication, Backend API, Backend JavaScript, Backend User Interface, Caching, Categorization API, CLI, Code Cleanup, composer / dependencies / third-party, Content Rendering, Content Security Policy, Dashboard, Database API (Doctrine DBAL), DataHandler aka TCEmain, Documentation, Extbase, Extbase + l10n, Extension Manager, felogin, File Abstraction Layer (FAL), Fluid, Fluid Styled Content, Form Framework, FormEngine aka TCEforms, Frontend, Image Cropping, Image Generation / GIFBUILDER, Import/Export (T3D), Indexed Search, Install Tool, Language Manager (backend), Link Handling & Redirect Handling, Linkvalidator, Localization, Locking / Session Handling, Logging, Mailer API, Miscellaneous, Pagetree, Performance, Recycler, Reports, RTE (rtehtmlarea + ckeditor), scheduler, Security, SEO, Site Handling, Site Sets & Routing, System/Bootstrap/Configuration, t3editor, Tests, Themes, TypoScript, WebHooks - Incoming = Reactions + Outgoing, Workspaces

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?limit=15&include=relations%2Cattachments&status_id=open&sort=created_on%3Aasc",
        "query": "",
        "placedAgainst": "15.0.0-dev",
        "total": 0,
        "terms": [],
        "categories": [
            "AdminPanel",
            "Authentication",
            "Backend API",
            "Backend JavaScript",
            "Backend User Interface",
            "Caching",
            "Categorization API",
            "CLI",
            "Code Cleanup",
            "composer / dependencies / third-party",
            "Content Rendering",
            "Content Security Policy",
            "Dashboard",
            "Database API (Doctrine DBAL)",
            "DataHandler aka TCEmain",
            "Documentation",
            "Extbase",
            "Extbase + l10n",
            "Extension Manager",
            "felogin",
            "File Abstraction Layer (FAL)",
            "Fluid",
            "Fluid Styled Content",
            "Form Framework",
            "FormEngine aka TCEforms",
            "Frontend",
            "Image Cropping",
            "Image Generation / GIFBUILDER",
            "Import/Export (T3D)",
            "Indexed Search",
            "Install Tool",
            "Language Manager (backend)",
            "Link Handling & Redirect Handling",
            "Linkvalidator",
            "Localization",
            "Locking / Session Handling",
            "Logging",
            "Mailer API",
            "Miscellaneous",
            "Pagetree",
            "Performance",
            "Recycler",
            "Reports",
            "RTE (rtehtmlarea + ckeditor)",
            "scheduler",
            "Security",
            "SEO",
            "Site Handling, Site Sets & Routing",
            "System/Bootstrap/Configuration",
            "t3editor",
            "Tests",
            "Themes",
            "TypoScript",
            "WebHooks - Incoming = Reactions + Outgoing",
            "Workspaces"
        ],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [],
        "readAt": "2026-10-28T10:31:49Z",
        "unavailable": null
    }

forge: what one person has filed
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "backlog": "oldest",
        "reportedBy": "Frank Nägler",
        "status": "all",
        "limit": 3
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: 3 of 621 issues of the TYPO3 Core project whatever their status, filed by Frank Nägler, oldest filed first
    This is a page and not the set. offset=3 reads the next page, and breakdown answers how the whole set is distributed — a tracker or a date answers a smaller question than the one asked.
    Age is a candidate and never a finding: read one whole by passing its number as issue, and what it still claims is established in the checkout rather than off this list.
    A row carries what the page came back with: the issues it is filed against, the files hanging off it, and the changes on review.typo3.org whose commit message names it, each with the state it is in. That state is where a change stands and not a verdict on the issue: an ABANDONED one is grounds to read the argument on it with typo3_gerrit_lookup, where the objection was written down and is regularly to the approach rather than to the defect. A row with no such line is one nothing there names — or one the review server did not answer for, which this list does not separate.
    A row that names code carries it: the classes, methods and core files its own text cites, each with whether the packages installed here still carry it, at TYPO3 15.0.0-dev. A report whose names are all gone is a candidate to drop without opening the checkout, and one whose names all stand is a candidate to read. It is read from the subject and the description, because the page carries no comment, and a name it could not place is unplaced rather than gone.

    ## #15488 miscellaneous extensions dont work
    Bug · Closed · filed by Frank Nägler · unassigned · filed 2006-01-23 · last touched 2006-01-24 · https://forge.typo3.org/issues/15488

    ## #15890 PHP erros after search
    Bug · Closed · Indexed Search · filed by Frank Nägler · assigned to Dmitry Dulepov · filed 2006-03-23 · last touched 2018-10-02 · https://forge.typo3.org/issues/15890
    Cites: typo3/sysext/indexed_search/pi/class.tx_indexedsearch.php — no installed package ships it

    ## #18374 XCLASSing USER_INT objects does not work
    Bug · Closed · Communication · filed by Frank Nägler · assigned to Oliver Hader · filed 2008-03-05 · last touched 2010-08-06 · https://forge.typo3.org/issues/18374
    Relation: relates #17883 — Bug · Closed · Nested USER_INT, COA_INT, etc. objects are not rendered · https://forge.typo3.org/issues/17883
    Relation: relates #18504 — Bug · Closed · XCLASSes are not working with AJAX calls in t3lib_TCEforms_inline · https://forge.typo3.org/issues/18504
    Files (2): 0007759_41.patch, 0007759_42.patch

    ## What a page of the backlog opens
    `typo3-core-issue-triage` is the workflow a caller holding this page is in, and opening it comes before deciding anything about a row. Hand the page over rather than choosing from it: triaging a backlog and triaging one issue are two jobs, and the second takes a number. Where the choice is yours, read these in order and stop at the first that decides:
    - What has already happened to it. The row carries the change and the state it stands in; the reading is the argument under that state. `typo3_gerrit_lookup` answers it by the number, before the checkout is opened, and an ABANDONED is grounds to read it rather than to pass the row over.
    - Whether the code it names is still there. The row carries every class, method and core file its text cites with where each stands in the packages installed here, so a report whose names are all gone is settled without opening the checkout. A name it could not place decides nothing.
    - The category, against the branch you are standing on. One naming a subsystem the branch no longer ships settles the issue unread.
    - Where the symptom appears. A rendered fragment, a stored row or a resolved value needs no installation; one that shows only after a backend interaction needs one standing up.
    - How far the mechanism reaches. One class and the behaviour in it is the settleable shape, and several with the order between them is an interaction.
    - What the suite already models. A case added to a test file that exists is a reproduction with no fixture to build.
    `typo3_changelog_lookup` is what says whether the area was reworked since, which is what turns a valid report into one about code that is gone. Say which reading decided, and say of the rows you passed over that you passed over them.

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?limit=3&include=relations%2Cattachments&status_id=%2A&sort=created_on%3Aasc&author_id=52",
        "query": "",
        "placedAgainst": "15.0.0-dev",
        "total": 621,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [
            {
                "filter": "reportedBy",
                "asked": "Frank Nägler",
                "name": "Frank Nägler",
                "id": 52,
                "candidates": []
            }
        ],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [
            {
                "issue": 15488,
                "subject": "miscellaneous extensions dont work",
                "tracker": "Bug",
                "status": "Closed",
                "category": "",
                "reportedBy": "Frank Nägler",
                "assignedTo": "",
                "createdOn": "2006-01-23T11:36:05Z",
                "updatedOn": "2006-01-24T17:37:26Z",
                "url": "https://forge.typo3.org/issues/15488",
                "relations": [],
                "attachments": [],
                "reviews": [],
                "cites": [],
                "lastNoteOn": "",
                "reviewAbandoned": false
            },
            {
                "issue": 15890,
                "subject": "PHP erros after search",
                "tracker": "Bug",
                "status": "Closed",
                "category": "Indexed Search",
                "reportedBy": "Frank Nägler",
                "assignedTo": "Dmitry Dulepov",
                "createdOn": "2006-03-23T23:48:50Z",
                "updatedOn": "2018-10-02T12:33:16Z",
                "url": "https://forge.typo3.org/issues/15890",
                "relations": [],
                "attachments": [],
                "reviews": [],
                "cites": [
                    {
                        "name": "typo3/sysext/indexed_search/pi/class.tx_indexedsearch.php",
                        "kind": "file",
                        "method": "",
                        "state": "notShipped",
                        "in": []
                    }
                ],
                "lastNoteOn": "",
                "reviewAbandoned": false
            },
            {
                "issue": 18374,
                "subject": "XCLASSing USER_INT objects does not work",
                "tracker": "Bug",
                "status": "Closed",
                "category": "Communication",
                "reportedBy": "Frank Nägler",
                "assignedTo": "Oliver Hader",
                "createdOn": "2008-03-05T10:30:08Z",
                "updatedOn": "2010-08-06T13:20:14Z",
                "url": "https://forge.typo3.org/issues/18374",
                "relations": [
                    {
                        "issue": 17883,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/17883",
                        "subject": "Nested USER_INT, COA_INT, etc. objects are not rendered",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 18504,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/18504",
                        "subject": "XCLASSes are not working with AJAX calls in t3lib_TCEforms_inline",
                        "tracker": "Bug",
                        "status": "Closed"
                    }
                ],
                "attachments": [
                    {
                        "filename": "0007759_41.patch",
                        "contentType": "text/x-patch",
                        "size": 514,
                        "on": "2008-03-31T11:44:07Z",
                        "url": "https://forge.typo3.org/attachments/download/9069/0007759_41.patch"
                    },
                    {
                        "filename": "0007759_42.patch",
                        "contentType": "text/x-patch",
                        "size": 524,
                        "on": "2008-03-31T11:44:16Z",
                        "url": "https://forge.typo3.org/attachments/download/9068/0007759_42.patch"
                    }
                ],
                "reviews": [],
                "cites": [],
                "lastNoteOn": "",
                "reviewAbandoned": false
            }
        ],
        "readAt": "2026-10-28T10:31:49Z",
        "unavailable": null
    }

forge: a name naming more than one person
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "backlog": "oldest",
        "assignedTo": "daniel"
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: "daniel" as assignedTo names more than one person, so nothing was read. That is about the name and not about the backlog.
    Ask again with one of: Daniel Siepmann, Daniel Goerz, Daniel Lorenz, Daniel Gohlke, Daniel Windloff, Daniel Maier, Daniel Sattler

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?limit=15&include=relations%2Cattachments&status_id=open&sort=created_on%3Aasc",
        "query": "",
        "placedAgainst": "15.0.0-dev",
        "total": 0,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [
            {
                "filter": "assignedTo",
                "asked": "daniel",
                "name": "",
                "id": 0,
                "candidates": [
                    "Daniel Siepmann",
                    "Daniel Goerz",
                    "Daniel Lorenz",
                    "Daniel Gohlke",
                    "Daniel Windloff",
                    "Daniel Maier",
                    "Daniel Sattler"
                ]
            }
        ],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [],
        "readAt": "2026-10-28T10:31:50Z",
        "unavailable": null
    }

forge: everything one person has touched
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "backlog": "stale",
        "involving": "Frank Nägler",
        "limit": 3
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: 3 of 3 open issues of the TYPO3 Core project, filed by or assigned to Frank Nägler, longest untouched first
    That is the whole set on these filters.
    Age is a candidate and never a finding: read one whole by passing its number as issue, and what it still claims is established in the checkout rather than off this list.
    A row carries what the page came back with: the issues it is filed against, the files hanging off it, and the changes on review.typo3.org whose commit message names it, each with the state it is in. That state is where a change stands and not a verdict on the issue: an ABANDONED one is grounds to read the argument on it with typo3_gerrit_lookup, where the objection was written down and is regularly to the approach rather than to the defect. A row with no such line is one nothing there names — or one the review server did not answer for, which this list does not separate.
    A row that names code carries it: the classes, methods and core files its own text cites, each with whether the packages installed here still carry it, at TYPO3 15.0.0-dev. A report whose names are all gone is a candidate to drop without opening the checkout, and one whose names all stand is a candidate to read. It is read from the subject and the description, because the page carries no comment, and a name it could not place is unplaced rather than gone.
    On this end a row also says when a person last commented. last touched moves on any edit, a field somebody changed on many issues at once included, so the comment is the measure of attention.

    ## #89326 Prevent duplicate redirects in auto redirects
    Bug · Accepted · Link Handling & Redirect Handling · filed by Guido Schmechel · assigned to Frank Nägler · filed 2019-10-01 · last touched 2023-11-09 · last comment 2023-03-07 · https://forge.typo3.org/issues/89326
    Relation: relates #89325 — Task · Resolved · Prevent duplicate redirects in backend module · https://forge.typo3.org/issues/89325
    Relation: relates #89301 — Task · Accepted · Streamline automatic slug & redirects handling · https://forge.typo3.org/issues/89301
    Relation: relates #92448 — Bug · New · changing slug again after reverting an auto update causes wrong URLs on sub pages · https://forge.typo3.org/issues/92448
    Review: change 97034 · MERGED · https://review.typo3.org/c/Packages/TYPO3.CMS/+/97034
    Review: change 96706 · MERGED · https://review.typo3.org/c/Packages/TYPO3.CMS/+/96706

    ## #105403 f:image and cache busting issue
    Bug · Needs Feedback · Fluid · filed by Frank Nägler · unassigned · filed 2024-10-23 · last touched 2026-09-03 · last comment 2026-09-03 · https://forge.typo3.org/issues/105403
    Relation: relates #99203 — Feature · Closed · Re-introduce easy possibility to add paths via TypoScript · https://forge.typo3.org/issues/99203
    Relation: relates #105953 — Bug · Closed · f:uri.resource cache busting not working and in addition causing PHP warninigs when open_basedir is enabled · https://forge.typo3.org/issues/105953
    Cites: TYPO3Fluid\Fluid\Core\ViewHelper\Exception — no installed package ships it
    Review: change 95067 · NEW · https://review.typo3.org/c/Packages/TYPO3.CMS/+/95067

    ## #89259 Create new icons and replace icons for "Page enabled in menus" context menu
    Task · Under Review · Backend User Interface · filed by Frank Nägler · assigned to Benjamin Kott · filed 2019-09-25 · last touched 2026-10-15 · https://forge.typo3.org/issues/89259
    Relation: relates #85918 — Feature · Closed · Show "Page enabled in menus" in ContextMenu for pages · https://forge.typo3.org/issues/85918
    Relation: relates #102497 — Task · New · Unify display and grouping of context menus · https://forge.typo3.org/issues/102497
    Files (1): image.png
    Review: change 93281 · NEW · https://review.typo3.org/c/Packages/TYPO3.CMS/+/93281

    ## What a page of the backlog opens
    `typo3-core-issue-triage` is the workflow a caller holding this page is in, and opening it comes before deciding anything about a row. Hand the page over rather than choosing from it: triaging a backlog and triaging one issue are two jobs, and the second takes a number. Where the choice is yours, read these in order and stop at the first that decides:
    - What has already happened to it. The row carries the change and the state it stands in; the reading is the argument under that state. `typo3_gerrit_lookup` answers it by the number, before the checkout is opened, and an ABANDONED is grounds to read it rather than to pass the row over.
    - Whether the code it names is still there. The row carries every class, method and core file its text cites with where each stands in the packages installed here, so a report whose names are all gone is settled without opening the checkout. A name it could not place decides nothing.
    - The category, against the branch you are standing on. One naming a subsystem the branch no longer ships settles the issue unread.
    - Where the symptom appears. A rendered fragment, a stored row or a resolved value needs no installation; one that shows only after a backend interaction needs one standing up.
    - How far the mechanism reaches. One class and the behaviour in it is the settleable shape, and several with the order between them is an interaction.
    - What the suite already models. A case added to a test file that exists is a reproduction with no fixture to build.
    `typo3_changelog_lookup` is what says whether the area was reworked since, which is what turns a valid report into one about code that is gone. Say which reading decided, and say of the rows you passed over that you passed over them.

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?limit=3&include=relations%2Cattachments&author_id=52&status_id=open&sort=updated_on%3Aasc https://forge.typo3.org/projects/typo3cms-core/issues.json?limit=3&include=relations%2Cattachments&assigned_to_id=52&status_id=open&sort=updated_on%3Aasc",
        "query": "",
        "placedAgainst": "15.0.0-dev",
        "total": 3,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [
            {
                "filter": "involving",
                "asked": "Frank Nägler",
                "name": "Frank Nägler",
                "id": 52,
                "candidates": []
            }
        ],
        "breakdown": null,
        "wordsScan": null,
        "issue": null,
        "results": [
            {
                "issue": 89326,
                "subject": "Prevent duplicate redirects in auto redirects",
                "tracker": "Bug",
                "status": "Accepted",
                "category": "Link Handling & Redirect Handling",
                "reportedBy": "Guido Schmechel",
                "assignedTo": "Frank Nägler",
                "createdOn": "2019-10-01T21:04:28Z",
                "updatedOn": "2023-11-09T13:26:17Z",
                "url": "https://forge.typo3.org/issues/89326",
                "relations": [
                    {
                        "issue": 89325,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/89325",
                        "subject": "Prevent duplicate redirects in backend module",
                        "tracker": "Task",
                        "status": "Resolved"
                    },
                    {
                        "issue": 89301,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/89301",
                        "subject": "Streamline automatic slug & redirects handling",
                        "tracker": "Task",
                        "status": "Accepted"
                    },
                    {
                        "issue": 92448,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/92448",
                        "subject": "changing slug again after reverting an auto update causes wrong URLs on sub pages",
                        "tracker": "Bug",
                        "status": "New"
                    }
                ],
                "attachments": [],
                "reviews": [
                    {
                        "change": 97034,
                        "status": "MERGED",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/97034"
                    },
                    {
                        "change": 96706,
                        "status": "MERGED",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/96706"
                    }
                ],
                "cites": [],
                "lastNoteOn": "2023-03-07T17:53:01Z",
                "reviewAbandoned": false
            },
            {
                "issue": 105403,
                "subject": "f:image and cache busting issue",
                "tracker": "Bug",
                "status": "Needs Feedback",
                "category": "Fluid",
                "reportedBy": "Frank Nägler",
                "assignedTo": "",
                "createdOn": "2024-10-23T08:42:11Z",
                "updatedOn": "2026-09-03T12:21:57Z",
                "url": "https://forge.typo3.org/issues/105403",
                "relations": [
                    {
                        "issue": 99203,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/99203",
                        "subject": "Re-introduce easy possibility to add paths via TypoScript",
                        "tracker": "Feature",
                        "status": "Closed"
                    },
                    {
                        "issue": 105953,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/105953",
                        "subject": "f:uri.resource cache busting not working and in addition causing PHP warninigs when open_basedir is enabled",
                        "tracker": "Bug",
                        "status": "Closed"
                    }
                ],
                "attachments": [],
                "reviews": [
                    {
                        "change": 95067,
                        "status": "NEW",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/95067"
                    }
                ],
                "cites": [
                    {
                        "name": "TYPO3Fluid\\Fluid\\Core\\ViewHelper\\Exception",
                        "kind": "qualified",
                        "method": "",
                        "state": "notShipped",
                        "in": []
                    }
                ],
                "lastNoteOn": "2026-09-03T12:21:57Z",
                "reviewAbandoned": false
            },
            {
                "issue": 89259,
                "subject": "Create new icons and replace icons for \"Page enabled in menus\" context menu",
                "tracker": "Task",
                "status": "Under Review",
                "category": "Backend User Interface",
                "reportedBy": "Frank Nägler",
                "assignedTo": "Benjamin Kott",
                "createdOn": "2019-09-25T09:39:35Z",
                "updatedOn": "2026-10-15T20:06:12Z",
                "url": "https://forge.typo3.org/issues/89259",
                "relations": [
                    {
                        "issue": 85918,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/85918",
                        "subject": "Show \"Page enabled in menus\" in ContextMenu for pages",
                        "tracker": "Feature",
                        "status": "Closed"
                    },
                    {
                        "issue": 102497,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/102497",
                        "subject": "Unify display and grouping of context menus",
                        "tracker": "Task",
                        "status": "New"
                    }
                ],
                "attachments": [
                    {
                        "filename": "image.png",
                        "contentType": "image/png",
                        "size": 50552,
                        "on": "2019-09-25T09:39:07Z",
                        "url": "https://forge.typo3.org/attachments/download/34579/image.png"
                    }
                ],
                "reviews": [
                    {
                        "change": 93281,
                        "status": "NEW",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/93281"
                    }
                ],
                "cites": [],
                "lastNoteOn": "",
                "reviewAbandoned": false
            }
        ],
        "readAt": "2026-10-28T10:31:50Z",
        "unavailable": null
    }

forge: the shape of one person's history
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "backlog": "oldest",
        "involving": "Frank Nägler",
        "status": "all",
        "breakdown": true
    }

Text:

.. code-block:: text

    TYPO3 issue tracker: 764 issues of the TYPO3 Core project whatever their status, filed by or assigned to Frank Nägler, oldest filed first
    Counted over all 764 of them, as the shape of the set rather than its rows.
    Ask again without breakdown, narrowed to the part this points at, for the issues themselves.
    Status: Closed 727 · Rejected 34 · Accepted 1 · Needs Feedback 1 · Under Review 1
    Tracker: Task 401 · Bug 301 · Feature 54 · Epic 4 · Story 4
    Area: none 178 · Backend User Interface 163 · Backend API 126 · Backend JavaScript 109 · FormEngine aka TCEforms 29 · Install Tool 26 · Documentation 13 · Code Cleanup 12 · TypoScript 10 · Frontend 9 · Site Handling, Site Sets & Routing 8 · Fluid 7 · and 29 more holding 74
    Filed in: 2015 200 · 2014 124 · 2016 113 · 2018 104 · 2017 89 · 2019 62 · 2024 18 · 2022 12 · 2020 11 · 2023 5 · 2021 4 · 2006 3 · and 9 more holding 19
    Last touched in: 2017 354 · 2018 225 · 2019 61 · 2020 24 · 2024 24 · 2015 18 · 2022 12 · 2021 11 · 2014 8 · 2016 8 · 2026 7 · 2023 6 · and 5 more holding 6

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?limit=100&author_id=52&status_id=%2A&sort=created_on%3Aasc https://forge.typo3.org/projects/typo3cms-core/issues.json?limit=100&assigned_to_id=52&status_id=%2A&sort=created_on%3Aasc",
        "query": "",
        "placedAgainst": "15.0.0-dev",
        "total": 764,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [
            {
                "filter": "involving",
                "asked": "Frank Nägler",
                "name": "Frank Nägler",
                "id": 52,
                "candidates": []
            }
        ],
        "breakdown": {
            "read": 764,
            "complete": true,
            "counts": [
                {
                    "dimension": "status",
                    "buckets": [
                        {
                            "name": "Closed",
                            "count": 727
                        },
                        {
                            "name": "Rejected",
                            "count": 34
                        },
                        {
                            "name": "Accepted",
                            "count": 1
                        },
                        {
                            "name": "Needs Feedback",
                            "count": 1
                        },
                        {
                            "name": "Under Review",
                            "count": 1
                        }
                    ],
                    "withheldBuckets": 0,
                    "withheldCount": 0
                },
                {
                    "dimension": "tracker",
                    "buckets": [
                        {
                            "name": "Task",
                            "count": 401
                        },
                        {
                            "name": "Bug",
                            "count": 301
                        },
                        {
                            "name": "Feature",
                            "count": 54
                        },
                        {
                            "name": "Epic",
                            "count": 4
                        },
                        {
                            "name": "Story",
                            "count": 4
                        }
                    ],
                    "withheldBuckets": 0,
                    "withheldCount": 0
                },
                {
                    "dimension": "category",
                    "buckets": [
                        {
                            "name": "none",
                            "count": 178
                        },
                        {
                            "name": "Backend User Interface",
                            "count": 163
                        },
                        {
                            "name": "Backend API",
                            "count": 126
                        },
                        {
                            "name": "Backend JavaScript",
                            "count": 109
                        },
                        {
                            "name": "FormEngine aka TCEforms",
                            "count": 29
                        },
                        {
                            "name": "Install Tool",
                            "count": 26
                        },
                        {
                            "name": "Documentation",
                            "count": 13
                        },
                        {
                            "name": "Code Cleanup",
                            "count": 12
                        },
                        {
                            "name": "TypoScript",
                            "count": 10
                        },
                        {
                            "name": "Frontend",
                            "count": 9
                        },
                        {
                            "name": "Site Handling, Site Sets & Routing",
                            "count": 8
                        },
                        {
                            "name": "Fluid",
                            "count": 7
                        }
                    ],
                    "withheldBuckets": 29,
                    "withheldCount": 74
                },
                {
                    "dimension": "year",
                    "buckets": [
                        {
                            "name": "2015",
                            "count": 200
                        },
                        {
                            "name": "2014",
                            "count": 124
                        },
                        {
                            "name": "2016",
                            "count": 113
                        },
                        {
                            "name": "2018",
                            "count": 104
                        },
                        {
                            "name": "2017",
                            "count": 89
                        },
                        {
                            "name": "2019",
                            "count": 62
                        },
                        {
                            "name": "2024",
                            "count": 18
                        },
                        {
                            "name": "2022",
                            "count": 12
                        },
                        {
                            "name": "2020",
                            "count": 11
                        },
                        {
                            "name": "2023",
                            "count": 5
                        },
                        {
                            "name": "2021",
                            "count": 4
                        },
                        {
                            "name": "2006",
                            "count": 3
                        }
                    ],
                    "withheldBuckets": 9,
                    "withheldCount": 19
                },
                {
                    "dimension": "touched",
                    "buckets": [
                        {
                            "name": "2017",
                            "count": 354
                        },
                        {
                            "name": "2018",
                            "count": 225
                        },
                        {
                            "name": "2019",
                            "count": 61
                        },
                        {
                            "name": "2020",
                            "count": 24
                        },
                        {
                            "name": "2024",
                            "count": 24
                        },
                        {
                            "name": "2015",
                            "count": 18
                        },
                        {
                            "name": "2022",
                            "count": 12
                        },
                        {
                            "name": "2021",
                            "count": 11
                        },
                        {
                            "name": "2014",
                            "count": 8
                        },
                        {
                            "name": "2016",
                            "count": 8
                        },
                        {
                            "name": "2026",
                            "count": 7
                        },
                        {
                            "name": "2023",
                            "count": 6
                        }
                    ],
                    "withheldBuckets": 5,
                    "withheldCount": 6
                }
            ]
        },
        "wordsScan": null,
        "issue": null,
        "results": [],
        "readAt": "2026-10-28T10:31:52Z",
        "unavailable": null
    }
