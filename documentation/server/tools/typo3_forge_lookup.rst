.. _typo3_forge_lookup:

``typo3_forge_lookup``
======================

Reads the TYPO3 issue tracker at forge.typo3.org through the bot protection the
core's own AGENTS.md warns a hand-written request about. It tells a tracker that
did not answer from a search that matched nothing. Read it before writing a
patch. Three ways in, one per call. issue reads one issue whole: the report, the
comments that decided it, the issues and review changes it names, and whether
the code it cites is still shipped here. query finds the other issues describing
the same thing, which the relations of one issue carry only where somebody
linked them. backlog enumerates the core project's unresolved issues without a
number or a wording: oldest filed, longest untouched or newest. Narrow it by
tracker, area, date and person, widen it with status, and breakdown answers how
a large set is distributed instead of a page of it. A miss is an answer. An
issue that does not exist is answered as such, and words matching nothing are
counted one word at a time — which is not that nobody reported it. The patch for
an issue on review.typo3.org is typo3_gerrit_lookup. Reading only, and no
credential: commenting, assigning and closing stay yours. Answers from: network,
packages.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`network <answer-sources-network>`,
:ref:`packages <answer-sources-packages>`.

Takes
-----

.. code-block:: yaml

    # Forge issue number, with or without the leading #, for example "110348". Reads
    # that one issue whole, comments included — narrow those with notes when
    # reading many. Not with query or backlog.
    issue: string  # optional
    # Words to search the tracker for, for example "image cache busting". A
    # full-text search over subject, description and comments, which is how a
    # duplicate nobody has linked is found at all. Every word has to be in the same
    # issue. A term nobody would have written — a method name, a class — empties
    # the answer whatever else is in it. Pass the two or three words that name the
    # subject rather than every word you have; a miss counts what each word reaches
    # on its own, in terms. Nothing is ranked and one wording does not settle it:
    # ask again in the reporter's words as well as your own. A person's name matches
    # only where somebody wrote it, so pass it as reportedBy or assignedTo with
    # backlog instead. Not with issue or backlog.
    query: string  # optional
    # One of: oldest, stale, newest. Enumerate the core project's unresolved issues
    # instead of reading one or matching words. "oldest" orders them by when they
    # were filed, "stale" by how long nobody has touched them, "newest" by what came
    # in last. Filed long ago is about the report, untouched for years about the
    # attention it got, and an issue that is both is the candidate a triage is
    # looking for. "stale" with tracker and category is where a triage of the
    # backlog starts. "newest" is where a duplicate of a defect somebody has just
    # found is. A wording reaches only the issues worded that way, so reading the
    # subjects filed since it could have been is what settles a negative. Pair it
    # with createdSince, which turns that end into a set the count says you have
    # seen the whole of. Unresolved is the tracker's own set of open statuses: New,
    # Accepted, Under Review, Needs Feedback, On Hold and Postponed. tracker,
    # category, createdBefore, createdSince, updatedBefore, reportedBy, assignedTo,
    # involving and breakdown narrow this way in and no other; status widens it. Not
    # with issue or query.
    backlog: string  # optional
    # One of: all, people. Which comments come back with an issue. "all" is every
    # one of them, which is what reading a single issue wants. The comments are
    # where the decision is, and the one that settles it is regularly the last of
    # sixteen. "people" drops the patch-set pings a review bot wrote, which on some
    # issues is half the volume. The change numbers in them are lifted into reviews
    # either way, so nothing is lost. Ask for it when reading candidates one issue
    # at a time, where the cost of ten such reads decides whether the comments get
    # read at all. How many were dropped is answered whichever you ask for. Narrows
    # issue and is ignored by query and backlog.
    notes: string  # optional
    # One of: Bug, Feature, Major Feature, Support, Task, Story, Suggestion,
    # Impediment, Epic, Work Package, Topic. Only issues filed under this tracker,
    # for example "Bug". Worth setting before reading a set: an old Bug claims
    # something is broken today, an old Feature that something was wanted once.
    tracker: string  # optional
    # Only issues the core files under this area, in your own words: "rte", "backend
    # ui", "workspaces", "fluid". Matched against the project's own category names
    # one word at a time, so a half-remembered name reaches the right area. A word
    # naming several — "backend" — selects all of them and says which. It is the
    # way in for "are there known bugs in the RTE" and "the oldest issues in the
    # backend UI". It answers "has this already been reported" too: enumerate the
    # area and read the subjects. A word naming none or several is answered with
    # every area the project has. categoriesUsed carries the tracker's own spelling
    # of the areas reached, which a report filed by hand has to carry. Pass "*" for
    # the list of areas on its own, which reads no issues.
    category: string  # optional
    # Only issues filed before this day, as YYYY-MM-DD. With createdSince it is the
    # far end of one window rather than a second filter.
    createdBefore: string  # optional
    # Only issues filed on or after this day, as YYYY-MM-DD. It is what makes the
    # recent end a set instead of a page. limit stops at 50 against thousands of
    # open issues, so a day to count from brings the page and the set together;
    # total says whether it did. It also reaches where category cannot: an issue
    # filed under no Category is in no area at all, and the report you are looking
    # for is regularly one of those.
    createdSince: string  # optional
    # Only issues nobody has touched since this day, as YYYY-MM-DD. It finds the
    # report everybody has walked past, which age alone does not: an issue filed in
    # 2009 and commented on last month is being worked.
    updatedBefore: string  # optional
    # Only issues this person filed, by their name rather than a tracker id: "Frank
    # Nägler", or "nägler". This answers "what has this person reported", which
    # query cannot: it matches text, and a name in the text is as often somebody
    # else writing it. The name is resolved against the core project's members and,
    # where they hold no membership, against the people the issues carrying that
    # name were filed by or handed to. A name reaching several people resolves to
    # none of them and the answer says which they were. Pair it with status "all"
    # for everything somebody has ever filed, and with breakdown for the shape of it
    # rather than the first page. involving is the union of this and assignedTo.
    reportedBy: string  # optional
    # Only issues this person holds, by their name, resolved the same way as
    # reportedBy. What somebody reported is their history; what they are assigned is
    # what they are on the hook for. An assignee on an old issue is usually who last
    # touched it rather than who is working on it. Passing both of these is the
    # issues somebody filed and holds; involving is their union.
    assignedTo: string  # optional
    # Only issues this person is on either side of — what they filed and what they
    # hold, as one set. The tracker cannot be asked this: it ANDs its filters, so
    # reportedBy and assignedTo together mean filed AND holds, a set nobody wants.
    # Passed instead of those two, not beside them. Every row says which side it
    # came in on.
    involving: string  # optional
    # Answer how the matched set is distributed instead of the rows of it: how many
    # issues per status, per tracker, per area and per year. For a person this is
    # the answer rather than a summary of it. "621 filed, 617 closed, 4 open,
    # concentrated 2014-2016, mostly Backend User Interface" says what a page of 50
    # out of 621 cannot. Ask for it whenever the question is what somebody or some
    # area has been about, and for the rows once it is which issue to read. It costs
    # a read per hundred issues and stops at a thousand, saying so where it did.
    breakdown: boolean  # optional
    # One of: open, closed, all. Which statuses the enumeration covers. "open", the
    # default, is the tracker's own unresolved set; "closed" is what it has marked
    # closed, Rejected included; "all" is both. A question about a person needs
    # "all": what somebody has filed over the years is mostly closed, and an
    # enumeration hiding those answers 4 where the number is 621.
    status: string  # optional
    # How many entries come back. A search answers with at most 25 whatever is asked
    # for: a set that has to be paged through is answered by other words rather than
    # by more of these. Nothing reaches past 50 and there is no offset; a matched
    # set larger than that is answered by breakdown.
    limit: integer  # optional

The call carries exactly one of these sets of arguments: ``issue`` — or
``query`` — or ``backlog``.

Answers with
------------

.. code-block:: yaml

    # One of: answered, empty, unavailable.
    status: string
    # The tracker the answer came from.
    source: string
    # What was read, so the same question can be asked again by hand. A union is two
    # reads and both are named, separated by a space.
    url: string
    # The words the tracker was searched for, so a set that looks too narrow can be
    # asked again in other words. Empty where an issue was read by number and where
    # the open issues were enumerated.
    query: string
    # The TYPO3 version of the installation the names in cites were placed against,
    # so a verdict about a symbol is read at a version. Empty where no installation
    # was found, and then every cited name is unplaced — a statement about this
    # machine and not about the code.
    placedAgainst: string
    # How many issues matched in total, of which results carries at most limit.
    # Where the two differ the answer is a page and not the set, and what reaches
    # more of it is a narrower filter rather than a bigger limit. Zero where an
    # issue was read by number.
    total: integer
    # What each word of the query reaches on its own, which says which of them
    # emptied the answer. Two class names look alike from here and the tracker may
    # know only one, so this is read rather than guessed at. Asked on a miss alone,
    # one read per word, which is why a query holds more than one word and no more
    # than a few. Empty otherwise, and short of the query where the tracker stopped
    # answering partway through it.
    terms:
      - # The word, lowercased as it was searched for.
        term: string
        matchCount: integer
    # Every area the core files its issues under, read from the project itself. A
    # category word matching none or several is corrected from the answer rather
    # than from a second call. Answered where category was passed and did not
    # resolve to exactly one area, and where it was passed as "*". Empty otherwise,
    # which says nothing about the project: the project administers the vocabulary
    # and a copy here would go stale.
    categories: [string]
    # The categories the category word resolved to, in the tracker's own spelling.
    # Empty where none was asked for. Empty too where the word matched none, which
    # is answered as no issues and is a statement about the word rather than about
    # the backlog.
    categoriesUsed: [string]
    # What reportedBy, assignedTo and involving resolved to, one entry per name the
    # call carried, in that order. A name is resolved against the core project's
    # members and, where they hold no membership, against the people the issues
    # carrying that name were filed by or handed to. Empty where no name was passed.
    people:
      - # One of: reportedBy, assignedTo, involving. Which argument the entry
        # answers for.
        filter: string
        # The name that was passed, as it was passed.
        asked: string
        # The person it resolved to, in the tracker's own spelling. Empty where it
        # resolved to nobody, which is answered as no issues and is a statement
        # about the name rather than about the backlog.
        name: string
        # The tracker's own user id, which is what it filters by and the only thing
        # it takes. Zero where the name resolved to nobody.
        id: integer
        # The people the name could have meant, where it reached more than one. A
        # name reaching two resolves to neither and nothing is read, because merging
        # two people into one backlog is a wrong answer nothing says is wrong. Ask
        # again with one of these. Empty where the name resolved, and where nothing
        # here carries it — a name this server cannot place rather than a person
        # who has filed nothing.
        candidates: [string]
    # How the matched set is distributed, where breakdown was asked for. Null
    # otherwise, and null where nothing matched.
    breakdown:
      # How many issues the counts are over. Equal to total where the whole set was
      # read.
      read: integer
      # Whether that is the whole matched set. False where the bound cut the read.
      # Then the counts are over the first issues read in the order asked for, the
      # oldest or the longest untouched. That is a shape of that end and not of the
      # set. Narrow the filters for the whole one.
      complete: boolean
      # One entry per dimension, always the four.
      counts:
        - # One of: status, tracker, category, year. What the issues are counted by.
          # year is the year they were filed in.
          dimension: string
          # The largest buckets first, and by name where two are the same size.
          buckets:
            - # The value, or "none" for the issues that carry none. An issue filed
              # under no area is a bucket rather than a row left out, so the buckets
              # add up to read.
              name: string
              count: integer
          # How many further buckets this dimension has, zero where it has none. The
          # tail of an area count is subsystems holding one issue each.
          withheldBuckets: integer
          # How many issues those hold together, so the listed buckets and this add
          # up to read.
          withheldCount: integer
    # The issue, where status says answered and a number was asked for. Null
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
      # a promise that somebody is working on it — on an issue nothing has moved
      # on for years it is usually who last did.
      assignedTo: string
      # The release it is scheduled for, empty where none is set.
      targetVersion: string
      # The TYPO3 version it was reported against, which is not the version it still
      # reproduces on.
      typo3Version: string
      phpVersion: string
      createdOn: string
      updatedOn: string
      # Where a person reads it.
      url: string
      # The report as it was written, which is what the reporter saw and not what
      # was decided.
      description: string
      # Issues this one is filed against, which is where a duplicate, a blocker, and
      # the issue a revert was filed under are named. Each carries its subject, so
      # which of them is worth reading is decided from here rather than from one
      # call each.
      relations:
        - # The other issue.
          issue: integer
          # What the issue is about, so it can be judged without being read. Empty
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
      # writer's own claim about prior art, which on an old report is regularly
      # load-bearing and regularly wrong. Read it before a patch is framed against
      # it, and never pass this issue over as a duplicate on its strength. Only a
      # number the tracker answered for is here, which keeps a version out of it.
      # Empty where the texts cite nothing and where every citation is already a
      # relation.
      mentioned:
        - # The issue the text cites.
          issue: integer
          # What the issue is about, so it can be judged without being read. Empty
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
      # Every change on review.typo3.org this issue is known to have, joined from
      # two sources. One is the handles the description and the journal name, lifted
      # out of the prose. The other is the changes whose commit message names the
      # issue number, asked of the review server. Neither half contains the other,
      # so an empty list means neither source has one. A change the prose named
      # carries the patch set and the date of that prose, while the state beside it
      # is the review server's. What a reviewer objected to is the argument on the
      # change, which is a typo3_gerrit_lookup call. An ABANDONED is grounds to read
      # that argument rather than to pass the issue over.
      reviews:
        - # The change number on review.typo3.org, which is what typo3_gerrit_lookup
          # takes as change.
          change: integer
          # NEW while the change is open, MERGED once it landed, ABANDONED when it
          # was given up — where it stood when the review server was asked. Empty
          # where it was not asked or named no state, which includes every change
          # only the prose names.
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
          # When the last note naming this change was written, which is how old the
          # reference is and not when the change last moved.
          on: string
      # The files hanging off the issue. On a report about rendering these are
      # usually screenshots and regularly where the evidence is: a comment made of
      # !image.jpg! references reads as empty otherwise. Empty where the issue
      # carries none.
      attachments:
        - # The name the file was uploaded under, which is also how a comment refers
          # to it: Redmine writes an inline image as !name.png! and says nothing
          # else about it.
          filename: string
          # image/png, image/jpeg, text/plain.
          contentType: string
          # Bytes.
          size: integer
          # When it was uploaded, which is what says which comment it belongs to.
          on: string
          # Where the file itself is. It answers without a credential, and reading
          # it is the caller's: nothing here fetches or transcribes one.
          url: string
      # The classes, methods and core files this report names, each with where it
      # stands in the packages this installation ships. A stale issue's status is
      # untouched by definition, so this is what says a 2015 report is about code
      # rewritten since. Read it before opening the checkout, as where a symbol is
      # rather than whether the defect reproduces. Read from the subject, the
      # description and every comment, which is where a reproduction regularly names
      # the class the description never did. Empty where the text names none, which
      # is the ordinary case for a report about a TCA key, a TypoScript path or a
      # table column.
      cites:
        - # The class, or the path of the file, as the report names it: a namespace
          # without its leading backslash, and a path from typo3/sysext/.
          name: string
          # One of: qualified, unqualified, file. How the report named it.
          # "qualified" is a class with its namespace, which places it without
          # guessing. "unqualified" is a bare class name, placed by the name of its
          # file. It can land on a package the report was never about, and one
          # matching two packages names both. A bare name is taken only where the
          # report marks it as code or an installed package ships one under it. A
          # capitalised word is as often the label of a button. "file" is a path in
          # the core tree, as a pasted stack trace writes it.
          kind: string
          # The method the report names on that class, empty where it names none. A
          # ::class and a ::CONSTANT are not one and are answered as the class
          # alone.
          method: string
          # One of: shipped, notShipped, unplaced. "shipped" is a name an installed
          # package carries, the method included where one was named. "notShipped"
          # is a name nothing installed carries: core having removed it and an
          # extension you never installed look the same, and the report does not
          # tell the two apart. Where in is filled it means the class stands and the
          # method named on it does not. "unplaced" is a name this could not place
          # at all: no installed package owns the namespace, or there is no
          # installation to read here. Never read unplaced as gone.
          state: string
          # Where it was found, one entry per package carrying it — several where
          # a bare name matches more than one, and picking one of those is the
          # caller's. Empty where it was not found and where nothing could place it.
          in:
            - # The extension key of the package that carries it.
              extension: string
              # Where the file sits, from the installation root, so it is opened
              # without being searched for.
              path: string
      # How many comments the issue carries in total.
      noteCount: integer
      # How many of those a review bot wrote, which notes: "people" is what drops.
      # Answered whichever way notes was asked. A journal full of patch-set pings
      # answering zero here is the list of bot names gone stale, not an issue nobody
      # pushed a patch for.
      botNoteCount: integer
      # The most recent comments, oldest first. A closure, a reassignment and a "we
      # will not do this" are here rather than in the description.
      notes:
        - author: string
          on: string
          note: string
    # The issues the query matched or the enumeration selected, in the tracker's own
    # order — nothing here ranks them. An enumerated row also carries its
    # relations, its attachments and its reviews: the three that say it was answered
    # elsewhere or already attempted, without reading it whole. Empty where an issue
    # was read by number.
    results:
      - # The issue number, which is what this tool reads whole.
        issue: integer
        subject: string
        # Bug, Feature, Task, Epic.
        tracker: string
        # Where it stands: New, Accepted, Under Review, Resolved, Closed, Rejected.
        status: string
        # The area the core files it under, empty where none is set. A search hit is
        # a title and carries none of the five fields below, so they are read for
        # the whole page in one further call. Empty here can mean that call did not
        # reach the tracker rather than that the issue has no area.
        category: string
        # Who filed it. This is the dimension reportedBy selects on, and reading it
        # off a set answers who a backlog is being reported by without a call per
        # row.
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
        # The issues this one is filed against, each with its subject, so a row that
        # duplicates something already decided is seen without being read. Answered
        # on an enumeration and empty on a search hit, where nothing asked for them.
        relations:
          - # The other issue.
            issue: integer
            # What the issue is about, so it can be judged without being read. Empty
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
        # The files hanging off the issue, which on a report about rendering are
        # usually where the evidence is. A report whose evidence is a screenshot is
        # a different candidate to one whose evidence is prose. Answered on an
        # enumeration and empty on a search hit, where nothing asked for them.
        attachments:
          - # The name the file was uploaded under, which is also how a comment
            # refers to it: Redmine writes an inline image as !name.png! and says
            # nothing else about it.
            filename: string
            # image/png, image/jpeg, text/plain.
            contentType: string
            # Bytes.
            size: integer
            # When it was uploaded, which is what says which comment it belongs to.
            on: string
            # Where the file itself is. It answers without a credential, and reading
            # it is the caller's: nothing here fetches or transcribes one.
            url: string
        # The classes, methods and core files this report names, each with where it
        # stands in the packages this installation ships. A stale issue's status is
        # untouched by definition, so this is what says a 2015 report is about code
        # rewritten since. Read it before opening the checkout, as where a symbol is
        # rather than whether the defect reproduces. Read from the subject and the
        # description, which is what the page carries. An enumerated row holds no
        # comment, so a report that names its code only in one is answered here as
        # citing nothing. Empty on a search hit, where it is not asked. Empty where
        # the text names none, which is the ordinary case for a report about a TCA
        # key, a TypoScript path or a table column.
        cites:
          - # The class, or the path of the file, as the report names it: a
            # namespace without its leading backslash, and a path from
            # typo3/sysext/.
            name: string
            # One of: qualified, unqualified, file. How the report named it.
            # "qualified" is a class with its namespace, which places it without
            # guessing. "unqualified" is a bare class name, placed by the name of
            # its file. It can land on a package the report was never about, and one
            # matching two packages names both. A bare name is taken only where the
            # report marks it as code or an installed package ships one under it. A
            # capitalised word is as often the label of a button. "file" is a path
            # in the core tree, as a pasted stack trace writes it.
            kind: string
            # The method the report names on that class, empty where it names none.
            # A ::class and a ::CONSTANT are not one and are answered as the class
            # alone.
            method: string
            # One of: shipped, notShipped, unplaced. "shipped" is a name an
            # installed package carries, the method included where one was named.
            # "notShipped" is a name nothing installed carries: core having removed
            # it and an extension you never installed look the same, and the report
            # does not tell the two apart. Where in is filled it means the class
            # stands and the method named on it does not. "unplaced" is a name this
            # could not place at all: no installed package owns the namespace, or
            # there is no installation to read here. Never read unplaced as gone.
            state: string
            # Where it was found, one entry per package carrying it — several
            # where a bare name matches more than one, and picking one of those is
            # the caller's. Empty where it was not found and where nothing could
            # place it.
            in:
              - # The extension key of the package that carries it.
                extension: string
                # Where the file sits, from the installation root, so it is opened
                # without being searched for.
                path: string
        # The changes whose commit message names this issue, asked of the review
        # server in one query for the whole page, each with the state it is in. A
        # state and not a verdict: what a reviewer objected to is the argument on
        # the change, which is a typo3_gerrit_lookup call. An ABANDONED is grounds
        # to read that argument rather than to pass the issue over — the approach
        # can be the rejected part while the defect is real. Empty where nothing on
        # the review server names the issue and where the review server did not
        # answer, which this does not separate. Empty on a search hit too, where it
        # is not asked.
        reviews:
          - # The change number on review.typo3.org, which is what
            # typo3_gerrit_lookup takes as change.
            change: integer
            # NEW while the change is open, MERGED once it landed, ABANDONED when it
            # was given up — where it stood when the page was read. Empty where
            # the review server named no state.
            status: string
            # Where a person reads the change.
            url: string
    # Why nothing was answered, where status says unavailable. Null otherwise.
    unavailable:
      # One of: source-not-answering, source-not-parseable. source-not-answering:
      # the tracker did not answer this time. source-not-parseable: something
      # answered with a page rather than with the API, which is what the bot
      # protection in front of it looks like from here.
      cause: string
      reason: string

Answered
--------

Recorded on 2026-09-02 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 15.0.0-dev, the main core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed
— vendor/autoload.php is not there either, and composer install writes both.
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

    #110348 Rework AdminPanel "imagesOnPage" feature
    Task · Resolved · priority Should have · https://forge.typo3.org/issues/110348
    Assigned to Benni Mack — which says who holds it and not that somebody is working on it.
    Target version: 15.0
    Reported against TYPO3 15 — which is what the reporter had, not what it still reproduces on.

    ## Reported
    The "imagesOnPage" feature is older than git. It needs to be revised to be integrated into FAL.

    ## Changes on review.typo3.org (1)
    Two sources joined: the handles the report and the comments name, and the changes whose commit message names this issue, asked of the review server. Neither half contains the other, so this list being empty is what says no change is in flight. A patch set and a date are what the prose said the day it was written; the state beside them is where the change stood just now. What a reviewer objected to is the argument on the change — pass the number as change to typo3_gerrit_lookup, or the Change-Id, which is what survives a rebase onto another branch.
    - change 95040 · MERGED · patch set 2 · named 2026-08-01 · https://review.typo3.org/c/95040

    ## Comments (3 of 3, oldest first)
    What was decided is here rather than above.

    **Gerrit Code Review**, 2026-07-31T19:23:24Z
    Patch set 1 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.
    It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040

    **Gerrit Code Review**, 2026-08-01T06:13:04Z
    Patch set 2 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.
    It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040

    **Benni Mack**, 2026-08-02T20:45:10Z
    Applied in changeset commit:e82b930e6e0587842427496c5ce01f625b27fb66.

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/issues/110348.json?include=journals,relations,attachments",
        "query": "",
        "placedAgainst": "15.0.0-dev",
        "total": 0,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "issue": {
            "id": 110348,
            "subject": "Rework AdminPanel \"imagesOnPage\" feature",
            "status": "Resolved",
            "tracker": "Task",
            "priority": "Should have",
            "assignedTo": "Benni Mack",
            "targetVersion": "15.0",
            "typo3Version": "15",
            "phpVersion": "",
            "createdOn": "2026-07-31T18:06:11Z",
            "updatedOn": "2026-08-02T20:45:10Z",
            "url": "https://forge.typo3.org/issues/110348",
            "description": "The \"imagesOnPage\" feature is older than git. It needs to be revised to be integrated into FAL.",
            "relations": [],
            "mentioned": [],
            "attachments": [],
            "reviews": [
                {
                    "change": 95040,
                    "changeId": "",
                    "patchSet": 2,
                    "on": "2026-08-01T06:13:04Z",
                    "url": "https://review.typo3.org/c/95040",
                    "status": "MERGED"
                }
            ],
            "cites": [],
            "noteCount": 3,
            "botNoteCount": 2,
            "notes": [
                {
                    "author": "Gerrit Code Review",
                    "on": "2026-07-31T19:23:24Z",
                    "note": "Patch set 1 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040"
                },
                {
                    "author": "Gerrit Code Review",
                    "on": "2026-08-01T06:13:04Z",
                    "note": "Patch set 2 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040"
                },
                {
                    "author": "Benni Mack",
                    "on": "2026-08-02T20:45:10Z",
                    "note": "Applied in changeset commit:e82b930e6e0587842427496c5ce01f625b27fb66."
                }
            ]
        },
        "results": [],
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

    #88556 One line break in DB field causes 3 rendered p-tags in CKEditor
    Bug · Closed · priority Should have · https://forge.typo3.org/issues/88556
    Assigned to nobody.
    Target version: Candidate for patchlevel
    Reported against TYPO3 12, PHP 8.2 — which is what the reporter had, not what it still reproduces on.
    Relation: relates #96466 — Bug · Rejected · RTE parse func paragraph duplication bug · https://forge.typo3.org/issues/96466
    Cited in the text below and filed as no relation, so this is the writer's own claim about prior art rather than somebody's triage. It is regularly wrong: read it before framing a patch against it, and never pass this issue over as a duplicate of it.
    Mentioned in the note: #88655 — Bug · Closed · richtextConfiguration, set via TCA for a text field is ignored · https://forge.typo3.org/issues/88655

    ## Reported
    <pre><code class="html">
    <p>Hello World
    </p><ul><li>foo bar</li></ul>
    </code></pre>
    
    When writing this into a DB field with enabled RTE it causes 3 additional empty p-tags in CKEditor. These can be saved too. See attachment for a sample.
    
    Not sure whether this is a CKEditor or TYPO3 related issue.

    ## Changes on review.typo3.org (3)
    Two sources joined: the handles the report and the comments name, and the changes whose commit message names this issue, asked of the review server. Neither half contains the other, so this list being empty is what says no change is in flight. A patch set and a date are what the prose said the day it was written; the state beside them is where the change stood just now. What a reviewer objected to is the argument on the change — pass the number as change to typo3_gerrit_lookup, or the Change-Id, which is what survives a rebase onto another branch.
    - change 95108 · MERGED · patch set 1 · named 2026-08-05 · https://review.typo3.org/c/95108
    - change 95131 · MERGED · patch set 1 · named 2026-08-06 · https://review.typo3.org/c/95131
    - change 95132 · MERGED · patch set 1 · named 2026-08-06 · https://review.typo3.org/c/95132

    ## Attachments (7)
    On a report about rendering these are usually where the evidence is, and Redmine writes an inline image into a comment as !filename! — so a comment below that is nothing but a filename is referring to one of these. Read the ones the report turns on; this server does not fetch them.
    - ckeditor-3-p-tags.png · image/png · 15 kB · 2019-06-13 · https://forge.typo3.org/attachments/download/34363/ckeditor-3-p-tags.png
    - db_field_value.jpg · image/jpeg · 17 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37897/db_field_value.jpg
    - rte_view.jpg · image/jpeg · 11 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37898/rte_view.jpg
    - rte_view_sourcecode.jpg · image/jpeg · 21 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37899/rte_view_sourcecode.jpg
    - fe_output.jpg · image/jpeg · 17 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37900/fe_output.jpg
    - fe_output_sourcecode.jpg · image/jpeg · 32 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37901/fe_output_sourcecode.jpg
    - db_field_value_wo_linebreak.jpg · image/jpeg · 15 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37902/db_field_value_wo_linebreak.jpg

    ## Comments (15 of 15, oldest first)
    What was decided is here rather than above.

    **Benni Mack**, 2019-06-13T13:37:33Z
    Are you using the RTE in a FlexForm?

    **Alex Nostadt**, 2019-06-13T13:40:26Z
    Benni Mack wrote:
    > Are you using the RTE in a FlexForm?
    
    No, I use it in a TCA.
    
    When removing the single existing line break between "World" and closing p-tag no additional line breaks are created. I have switched temporary to T3's default preset but the behaviour was the same.
    
    That's my TCA field config:
    <pre><code class="php">
    <?php
            'myField' => [
                'exclude' => 1,
                'label' => $ll . 'myField',
                'config' => [
                    'type' => 'text',
                    'cols' => 40,
                    'rows' => 15,
                    'eval' => 'trim',
                    'enableRichtext' => true,
                ],
            ],
    </code></pre>

    **Riccardo De Contardi**, 2019-06-29T21:50:09Z
    Is this somehow related? #88655

    **Alex Nostadt**, 2019-07-02T12:07:18Z
    Riccardo De Contardi wrote:
    > Is this somehow related? #88655
    
    I will check within this week.

    **Alex Nostadt**, 2019-07-08T18:57:16Z
    I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.

    **Benni Mack**, 2019-08-16T12:46:29Z
    Alex Nostadt wrote:
    > I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.
    
    Can you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure.

    **Alex Nostadt**, 2019-08-17T09:05:59Z
    Benni Mack wrote:
    > Alex Nostadt wrote:
    > > I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.
    > 
    > Can you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure.
    
    I could reproduce it with the default T3 config as well but can provide it as well within the next days.

    **Alex Nostadt**, 2019-08-26T15:38:15Z
    Alex Nostadt wrote:
    > Benni Mack wrote:
    > > Alex Nostadt wrote:
    > > > I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.
    > > 
    > > Can you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure.
    > 
    > I could reproduce it with the default T3 config as well but can provide it as well within the next days.
    
    Sorry, forgot this ticket. Add this to my priority list now.

    **Alex Nostadt**, 2019-08-30T08:42:51Z
    That's the RTE configuration:
    (I include Specific.yaml. We have multiple "Specific.yaml" files and our Default.yaml is our common denominator)
    *Default.yaml*
    <pre><code class="yaml">
    imports:
        - { resource: "EXT:rte_ckeditor/Configuration/RTE/Processing.yaml" }
        - { resource: "EXT:rte_ckeditor/Configuration/RTE/Editor/Base.yaml" }
        - { resource: "EXT:rte_ckeditor/Configuration/RTE/Editor/Plugins.yaml" }
        - { resource: "EXT:rte_ckeditor_image/Configuration/RTE/Plugin.yaml" }
    
    processing:
        ## allowed default attributes (added by us is..: "style")
        allowAttributes: [class, id, title, dir, lang, xml:lang, itemscope, itemtype, itemprop, style]
    
    editor:
        externalPlugins:
            find:
                resource: "EXT:provider/Resources/Public/CKEditor/Plugins/find/"
    
        config:
            # can be "default", but a custom stylesSet can be defined here, which fits TYPO3 best
            stylesSet:
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:align-center", element: "p", styles: { text-align: "center"} }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:justify", element: "p", styles: { text-align: "justify"} }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:author", element: "p", attributes: { class: 'author' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:citation", element: "p", attributes: { class: 'citation' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:check", element: ["ul"], attributes: { class: 'check' } }
    
            toolbarGroups:
                - { name: styles, groups: [ styles ] }
                - "/"
                - { name: editing, groups: [ find, selection, spellchecker, editing ] }
                - { name: forms, groups: [ forms ] }
                - { name: basicstyles, groups: [ basicstyles, cleanup ] }
                - { name: paragraph, groups: [ list, indent, blocks, align, bidi, paragraph ] }
                - { name: links, groups: [ links ] }
                - { name: insert, groups: [ insert ] }
                - { name: colors, groups: [ colors ] }
                - { name: others, groups: [ others ] }
                - "/"
                - { name: clipboard, groups: [ clipboard, undo ] }
                - { name: document, groups: [ mode, document, doctools ] }
                - { name: tools, groups: [ tools ] }
                - { name: about, groups: [ about ] }
    
            format_tags: "p;h1;h2;h3;h4"
    
            justifyClasses:
                - text-left
                - text-center
                - text-right
                - text-justify
    
            buttons:
                link:
                    url:
                        properties:
                            class:
                                default: 'external-link'
                    properties:
                        class:
                            allowedClasses: 'external-link'
    
            classes:
                external-link:
                    name: 'External Link'
    
            classesAnchor:
                externalLink:
                    class: 'external-link'
                    type: 'url'
                    target: '_blank'
    
            removeButtons:
                - Save
                - NewPage
                - Preview
                - Print
                - Templates
                - Cut
                - Copy
                - Paste
                - PasteFromWord
                - Form
                - Checkbox
                - Radio,
                - TextField
                - Textarea
                - Select
                - Button
                - ImageButton
                - HiddenField
                - Outdent
                - Indent
                - Flash
                - HorizontalRule
                - Smiley
                - PageBreak
                - Iframe
                - Font
                - FontSize
                - TextColor
                - BGColor
                - ShowBlocks
                - Blockquote
                - About
    </code></pre>
    
    *Specific.yaml*
    <pre><code class="yaml">
    imports:
        - { resource: "EXT:provider/Configuration/RTE/Default.yaml" }
    
    editor:
        externalPlugins:
            placeholder_select:
                resource: "EXT:provider/Resources/Public/CKEditor/Plugins/placeholder_select/"
    
        config:
            contentsCss: ["EXT:rte_ckeditor/Resources/Public/Css/contents.css", "EXT:provider/Resources/Public/Css/editor.css"]
            # can be "default", but a custom stylesSet can be defined here, which fits TYPO3 best
            stylesSet:
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:large", element:  ["p", "ul", "ol", "h1", "h2", "h3"], attributes: { class: 'large' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:teaser-text", element: "p", attributes: { class: 'teaser-text' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:light-gray", element: ["p", "ul", "ol"], attributes: { class: 'light-gray' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:all-borders", element: "table", attributes: { class: 'all-borders' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:horizontal-borders", element: "table", attributes: { class: 'horizontal-borders' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:vertical-borders", element: "table", attributes: { class: 'vertical-borders' } }
    
    </code></pre>

    **David Menzel**, 2023-08-07T18:58:28Z
    We have the same problem since (at least) TYPO3 10. Now using TYPO3 11.
    
    A bodytext database field has the following text:
    
    !db_field_value.jpg!
    
    The RTE ckeditor looks like this:
    
    !rte_view.jpg!
    
    RTE ckeditor Source code view:
    
    !rte_view_sourcecode.jpg!
    
    Output in FE:
    
    !fe_output.jpg!
    
    FE source code:
    
    !fe_output_sourcecode.jpg!
    
    You notice the additional empty p-Tags before/after the pre-tag?
    
    However, when I remove the linebreaks in the database field:
    !db_field_value_wo_linebreak.jpg!
    
    the FE works and there are no additional empty p-tags.
    When I save the RTE field again, the empty p-tags are back again because the linebreaks are back in the db field.
    
    Not sure why it only happens before/after the pre-tag.
    
    TYPO3 11.5.30 and PHP 7.4

    **David Menzel**, 2025-03-06T05:47:19Z
    Problem still exists in TYPO3 12.4.27 and CKEditor 5.
    
    There's an additional empty <p>-Tag before and after the codeblock.

    **Gerrit Code Review**, 2026-08-05T03:25:06Z
    Patch set 1 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.
    It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95108

    **Gerrit Code Review**, 2026-08-06T19:26:43Z
    Patch set 1 for branch *14.3* of project *Packages/TYPO3.CMS* has been pushed to the review server.
    It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95131

    **Gerrit Code Review**, 2026-08-06T19:26:58Z
    Patch set 1 for branch *13.4* of project *Packages/TYPO3.CMS* has been pushed to the review server.
    It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95132

    **Benjamin Kott**, 2026-08-06T20:15:08Z
    Applied in changeset commit:b406a9416431d1945756ce418d9c3726844f5325.

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/issues/88556.json?include=journals,relations,attachments",
        "query": "",
        "placedAgainst": "15.0.0-dev",
        "total": 0,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "issue": {
            "id": 88556,
            "subject": "One line break in DB field causes 3 rendered p-tags in CKEditor",
            "status": "Closed",
            "tracker": "Bug",
            "priority": "Should have",
            "assignedTo": "",
            "targetVersion": "Candidate for patchlevel",
            "typo3Version": "12",
            "phpVersion": "8.2",
            "createdOn": "2019-06-13T13:35:40Z",
            "updatedOn": "2026-08-11T08:46:35Z",
            "url": "https://forge.typo3.org/issues/88556",
            "description": "<pre><code class=\"html\">\r\n<p>Hello World\r\n</p><ul><li>foo bar</li></ul>\r\n</code></pre>\r\n\r\nWhen writing this into a DB field with enabled RTE it causes 3 additional empty p-tags in CKEditor. These can be saved too. See attachment for a sample.\r\n\r\nNot sure whether this is a CKEditor or TYPO3 related issue.",
            "relations": [
                {
                    "issue": 96466,
                    "relation": "relates",
                    "url": "https://forge.typo3.org/issues/96466",
                    "subject": "RTE parse func paragraph duplication bug",
                    "tracker": "Bug",
                    "status": "Rejected"
                }
            ],
            "mentioned": [
                {
                    "issue": 88655,
                    "subject": "richtextConfiguration, set via TCA for a text field is ignored",
                    "tracker": "Bug",
                    "status": "Closed",
                    "url": "https://forge.typo3.org/issues/88655",
                    "where": "note"
                }
            ],
            "attachments": [
                {
                    "filename": "ckeditor-3-p-tags.png",
                    "contentType": "image/png",
                    "size": 15472,
                    "on": "2019-06-13T13:31:29Z",
                    "url": "https://forge.typo3.org/attachments/download/34363/ckeditor-3-p-tags.png"
                },
                {
                    "filename": "db_field_value.jpg",
                    "contentType": "image/jpeg",
                    "size": 17301,
                    "on": "2023-08-07T18:36:48Z",
                    "url": "https://forge.typo3.org/attachments/download/37897/db_field_value.jpg"
                },
                {
                    "filename": "rte_view.jpg",
                    "contentType": "image/jpeg",
                    "size": 11397,
                    "on": "2023-08-07T18:39:23Z",
                    "url": "https://forge.typo3.org/attachments/download/37898/rte_view.jpg"
                },
                {
                    "filename": "rte_view_sourcecode.jpg",
                    "contentType": "image/jpeg",
                    "size": 21339,
                    "on": "2023-08-07T18:40:49Z",
                    "url": "https://forge.typo3.org/attachments/download/37899/rte_view_sourcecode.jpg"
                },
                {
                    "filename": "fe_output.jpg",
                    "contentType": "image/jpeg",
                    "size": 17378,
                    "on": "2023-08-07T18:42:14Z",
                    "url": "https://forge.typo3.org/attachments/download/37900/fe_output.jpg"
                },
                {
                    "filename": "fe_output_sourcecode.jpg",
                    "contentType": "image/jpeg",
                    "size": 31557,
                    "on": "2023-08-07T18:46:05Z",
                    "url": "https://forge.typo3.org/attachments/download/37901/fe_output_sourcecode.jpg"
                },
                {
                    "filename": "db_field_value_wo_linebreak.jpg",
                    "contentType": "image/jpeg",
                    "size": 15438,
                    "on": "2023-08-07T18:50:25Z",
                    "url": "https://forge.typo3.org/attachments/download/37902/db_field_value_wo_linebreak.jpg"
                }
            ],
            "reviews": [
                {
                    "change": 95108,
                    "changeId": "",
                    "patchSet": 1,
                    "on": "2026-08-05T03:25:06Z",
                    "url": "https://review.typo3.org/c/95108",
                    "status": "MERGED"
                },
                {
                    "change": 95131,
                    "changeId": "",
                    "patchSet": 1,
                    "on": "2026-08-06T19:26:43Z",
                    "url": "https://review.typo3.org/c/95131",
                    "status": "MERGED"
                },
                {
                    "change": 95132,
                    "changeId": "",
                    "patchSet": 1,
                    "on": "2026-08-06T19:26:58Z",
                    "url": "https://review.typo3.org/c/95132",
                    "status": "MERGED"
                }
            ],
            "cites": [],
            "noteCount": 15,
            "botNoteCount": 3,
            "notes": [
                {
                    "author": "Benni Mack",
                    "on": "2019-06-13T13:37:33Z",
                    "note": "Are you using the RTE in a FlexForm?"
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-06-13T13:40:26Z",
                    "note": "Benni Mack wrote:\r\n> Are you using the RTE in a FlexForm?\r\n\r\nNo, I use it in a TCA.\r\n\r\nWhen removing the single existing line break between \"World\" and closing p-tag no additional line breaks are created. I have switched temporary to T3's default preset but the behaviour was the same.\r\n\r\nThat's my TCA field config:\r\n<pre><code class=\"php\">\r\n<?php\r\n        'myField' => [\r\n            'exclude' => 1,\r\n            'label' => $ll . 'myField',\r\n            'config' => [\r\n                'type' => 'text',\r\n                'cols' => 40,\r\n                'rows' => 15,\r\n                'eval' => 'trim',\r\n                'enableRichtext' => true,\r\n            ],\r\n        ],\r\n</code></pre>"
                },
                {
                    "author": "Riccardo De Contardi",
                    "on": "2019-06-29T21:50:09Z",
                    "note": "Is this somehow related? #88655"
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-07-02T12:07:18Z",
                    "note": "Riccardo De Contardi wrote:\r\n> Is this somehow related? #88655\r\n\r\nI will check within this week."
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-07-08T18:57:16Z",
                    "note": "I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process."
                },
                {
                    "author": "Benni Mack",
                    "on": "2019-08-16T12:46:29Z",
                    "note": "Alex Nostadt wrote:\r\n> I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.\r\n\r\nCan you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure."
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-08-17T09:05:59Z",
                    "note": "Benni Mack wrote:\r\n> Alex Nostadt wrote:\r\n> > I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.\r\n> \r\n> Can you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure.\r\n\r\nI could reproduce it with the default T3 config as well but can provide it as well within the next days."
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-08-26T15:38:15Z",
                    "note": "Alex Nostadt wrote:\r\n> Benni Mack wrote:\r\n> > Alex Nostadt wrote:\r\n> > > I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.\r\n> > \r\n> > Can you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure.\r\n> \r\n> I could reproduce it with the default T3 config as well but can provide it as well within the next days.\r\n\r\nSorry, forgot this ticket. Add this to my priority list now."
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-08-30T08:42:51Z",
                    "note": "That's the RTE configuration:\r\n(I include Specific.yaml. We have multiple \"Specific.yaml\" files and our Default.yaml is our common denominator)\r\n*Default.yaml*\r\n<pre><code class=\"yaml\">\r\nimports:\r\n    - { resource: \"EXT:rte_ckeditor/Configuration/RTE/Processing.yaml\" }\r\n    - { resource: \"EXT:rte_ckeditor/Configuration/RTE/Editor/Base.yaml\" }\r\n    - { resource: \"EXT:rte_ckeditor/Configuration/RTE/Editor/Plugins.yaml\" }\r\n    - { resource: \"EXT:rte_ckeditor_image/Configuration/RTE/Plugin.yaml\" }\r\n\r\nprocessing:\r\n    ## allowed default attributes (added by us is..: \"style\")\r\n    allowAttributes: [class, id, title, dir, lang, xml:lang, itemscope, itemtype, itemprop, style]\r\n\r\neditor:\r\n    externalPlugins:\r\n        find:\r\n            resource: \"EXT:provider/Resources/Public/CKEditor/Plugins/find/\"\r\n\r\n    config:\r\n        # can be \"default\", but a custom stylesSet can be defined here, which fits TYPO3 best\r\n        stylesSet:\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:align-center\", element: \"p\", styles: { text-align: \"center\"} }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:justify\", element: \"p\", styles: { text-align: \"justify\"} }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:author\", element: \"p\", attributes: { class: 'author' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:citation\", element: \"p\", attributes: { class: 'citation' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:check\", element: [\"ul\"], attributes: { class: 'check' } }\r\n\r\n        toolbarGroups:\r\n            - { name: styles, groups: [ styles ] }\r\n            - \"/\"\r\n            - { name: editing, groups: [ find, selection, spellchecker, editing ] }\r\n            - { name: forms, groups: [ forms ] }\r\n            - { name: basicstyles, groups: [ basicstyles, cleanup ] }\r\n            - { name: paragraph, groups: [ list, indent, blocks, align, bidi, paragraph ] }\r\n            - { name: links, groups: [ links ] }\r\n            - { name: insert, groups: [ insert ] }\r\n            - { name: colors, groups: [ colors ] }\r\n            - { name: others, groups: [ others ] }\r\n            - \"/\"\r\n            - { name: clipboard, groups: [ clipboard, undo ] }\r\n            - { name: document, groups: [ mode, document, doctools ] }\r\n            - { name: tools, groups: [ tools ] }\r\n            - { name: about, groups: [ about ] }\r\n\r\n        format_tags: \"p;h1;h2;h3;h4\"\r\n\r\n        justifyClasses:\r\n            - text-left\r\n            - text-center\r\n            - text-right\r\n            - text-justify\r\n\r\n        buttons:\r\n            link:\r\n                url:\r\n                    properties:\r\n                        class:\r\n                            default: 'external-link'\r\n                properties:\r\n                    class:\r\n                        allowedClasses: 'external-link'\r\n\r\n        classes:\r\n            external-link:\r\n                name: 'External Link'\r\n\r\n        classesAnchor:\r\n            externalLink:\r\n                class: 'external-link'\r\n                type: 'url'\r\n                target: '_blank'\r\n\r\n        removeButtons:\r\n            - Save\r\n            - NewPage\r\n            - Preview\r\n            - Print\r\n            - Templates\r\n            - Cut\r\n            - Copy\r\n            - Paste\r\n            - PasteFromWord\r\n            - Form\r\n            - Checkbox\r\n            - Radio,\r\n            - TextField\r\n            - Textarea\r\n            - Select\r\n            - Button\r\n            - ImageButton\r\n            - HiddenField\r\n            - Outdent\r\n            - Indent\r\n            - Flash\r\n            - HorizontalRule\r\n            - Smiley\r\n            - PageBreak\r\n            - Iframe\r\n            - Font\r\n            - FontSize\r\n            - TextColor\r\n            - BGColor\r\n            - ShowBlocks\r\n            - Blockquote\r\n            - About\r\n</code></pre>\r\n\r\n*Specific.yaml*\r\n<pre><code class=\"yaml\">\r\nimports:\r\n    - { resource: \"EXT:provider/Configuration/RTE/Default.yaml\" }\r\n\r\neditor:\r\n    externalPlugins:\r\n        placeholder_select:\r\n            resource: \"EXT:provider/Resources/Public/CKEditor/Plugins/placeholder_select/\"\r\n\r\n    config:\r\n        contentsCss: [\"EXT:rte_ckeditor/Resources/Public/Css/contents.css\", \"EXT:provider/Resources/Public/Css/editor.css\"]\r\n        # can be \"default\", but a custom stylesSet can be defined here, which fits TYPO3 best\r\n        stylesSet:\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:large\", element:  [\"p\", \"ul\", \"ol\", \"h1\", \"h2\", \"h3\"], attributes: { class: 'large' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:teaser-text\", element: \"p\", attributes: { class: 'teaser-text' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:light-gray\", element: [\"p\", \"ul\", \"ol\"], attributes: { class: 'light-gray' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:all-borders\", element: \"table\", attributes: { class: 'all-borders' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:horizontal-borders\", element: \"table\", attributes: { class: 'horizontal-borders' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:vertical-borders\", element: \"table\", attributes: { class: 'vertical-borders' } }\r\n\r\n</code></pre>"
                },
                {
                    "author": "David Menzel",
                    "on": "2023-08-07T18:58:28Z",
                    "note": "We have the same problem since (at least) TYPO3 10. Now using TYPO3 11.\r\n\r\nA bodytext database field has the following text:\r\n\r\n!db_field_value.jpg!\r\n\r\nThe RTE ckeditor looks like this:\r\n\r\n!rte_view.jpg!\r\n\r\nRTE ckeditor Source code view:\r\n\r\n!rte_view_sourcecode.jpg!\r\n\r\nOutput in FE:\r\n\r\n!fe_output.jpg!\r\n\r\nFE source code:\r\n\r\n!fe_output_sourcecode.jpg!\r\n\r\nYou notice the additional empty p-Tags before/after the pre-tag?\r\n\r\nHowever, when I remove the linebreaks in the database field:\r\n!db_field_value_wo_linebreak.jpg!\r\n\r\nthe FE works and there are no additional empty p-tags.\r\nWhen I save the RTE field again, the empty p-tags are back again because the linebreaks are back in the db field.\r\n\r\nNot sure why it only happens before/after the pre-tag.\r\n\r\nTYPO3 11.5.30 and PHP 7.4"
                },
                {
                    "author": "David Menzel",
                    "on": "2025-03-06T05:47:19Z",
                    "note": "Problem still exists in TYPO3 12.4.27 and CKEditor 5.\r\n\r\nThere's an additional empty <p>-Tag before and after the codeblock."
                },
                {
                    "author": "Gerrit Code Review",
                    "on": "2026-08-05T03:25:06Z",
                    "note": "Patch set 1 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95108"
                },
                {
                    "author": "Gerrit Code Review",
                    "on": "2026-08-06T19:26:43Z",
                    "note": "Patch set 1 for branch *14.3* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95131"
                },
                {
                    "author": "Gerrit Code Review",
                    "on": "2026-08-06T19:26:58Z",
                    "note": "Patch set 1 for branch *13.4* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95132"
                },
                {
                    "author": "Benjamin Kott",
                    "on": "2026-08-06T20:15:08Z",
                    "note": "Applied in changeset commit:b406a9416431d1945756ce418d9c3726844f5325."
                }
            ]
        },
        "results": [],
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

    #14858 extended clipboard: setCopyMode can`t be set to copy by default
    Bug · New · priority Should have · https://forge.typo3.org/issues/14858
    Assigned to nobody.
    Target version: Candidate for patchlevel
    Reported against TYPO3 8, PHP 7.2 — which is what the reporter had, not what it still reproduces on.
    Relation: relates #90676 — Epic · Accepted · Clipboard related bugs and features · https://forge.typo3.org/issues/90676
    Relation: duplicates #70759 — Feature · Closed · Changing the default clipboard option from  "move elements"  to "copy elements" · https://forge.typo3.org/issues/70759

    ## Reported
    Hi,
    
    I couldn`t find any TCAdefaults or other TSconfig option to switch the copy mode of the extended clipboard to copy by default.
    
    At the moment the default is "move" which can be very annoying.
    
    Please add the possibility to choose the default behaviour of the "setCopyMode" button.
    
    Thanks,
    Sacha
    
    
    
    
    (issue imported from #M1277)

    ## Changes on review.typo3.org (2)
    Two sources joined: the handles the report and the comments name, and the changes whose commit message names this issue, asked of the review server. Neither half contains the other, so this list being empty is what says no change is in flight. A patch set and a date are what the prose said the day it was written; the state beside them is where the change stood just now. What a reviewer objected to is the argument on the change — pass the number as change to typo3_gerrit_lookup, or the Change-Id, which is what survives a rebase onto another branch.
    - change 38419 · ABANDONED · patch set 3 · named 2015-04-01 · https://review.typo3.org/c/38419
    - change 70962 · ABANDONED · patch set 5 · named 2023-01-14 · https://review.typo3.org/c/70962

    ## Comments (8 of 16, oldest first)
    What was decided is here rather than above.
    8 of them a review bot wrote and they were dropped. The changes they named are above. Ask for notes "all" to read them; a count of 0 on an issue with patch-set pings means this filter does not know the bot that wrote them.

    **Sebastian Kurfuerst**, 2005-10-22T19:07:47Z
    This issue is more complicated than it seems, because there setting for "move" is empty - and there is no easy condition to find out whether the default should apply or not. I will have a deeper look into it.
    Greets, Sebastian

    **Sebastian Kurfuerst**, 2005-10-23T20:16:15Z
    this issue is not so easy to fix and I don't see a nice solution at the moment. A patch is welcome, but currently I cannot have a deeper look into it.
    Greets, Sebastian

    **Oliver Hader**, 2011-09-19T12:55:23Z
    Should be a UserTS configuration and maybe an additional setting in the user preferences.

    **Gabriel Kaufmann TYPOworx GmbH | NewMedia**, 2013-04-11T09:11:03Z
    Is there anything new for the TYPO3 4.6 oder 4.7 Tree?

    **Tilo Baller**, 2015-10-21T15:28:17Z
    What was the reason for abandoning the patch in review and what is the current progress of this feature?
    
    I got several requests from customers for adjusting the default behaviour to 'copy' instead of 'move'.

    **Daxboeck no-lastname-given**, 2018-06-19T14:18:23Z
    I did now ask my developers to create a patch as the default of "move" when someone selects the 2nd or 3rd clipboard is very annoying.
    It is against a thought of safety, it is against common sense.
    I just had too many cases where by accident stuff has been moved instead of copied.
    "copy" must be the default, there should be no doubt about it.

    **Sybille Peters**, 2023-01-14T19:27:42Z
    Patch https://review.typo3.org/c/Packages/TYPO3.CMS/+/70962 was abandoned.

    **Benni Mack**, 2026-01-23T08:30:36Z
    Summarizing the current state:
    
    - Currently, the option is set to "move" hardcoded without any possibility to change this.
    - Right now, this is debateble is the setCopyMode should be "copy" or "move"
    - If this should be configurable, then we need a new option, making this not a bug, but actually a feature

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/issues/14858.json?include=journals,relations,attachments",
        "query": "",
        "placedAgainst": "15.0.0-dev",
        "total": 0,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "issue": {
            "id": 14858,
            "subject": "extended clipboard: setCopyMode can`t be set to copy by default",
            "status": "New",
            "tracker": "Bug",
            "priority": "Should have",
            "assignedTo": "",
            "targetVersion": "Candidate for patchlevel",
            "typo3Version": "8",
            "phpVersion": "7.2",
            "createdOn": "2005-07-11T23:31:03Z",
            "updatedOn": "2026-01-23T08:30:36Z",
            "url": "https://forge.typo3.org/issues/14858",
            "description": "Hi,\r\n\r\nI couldn`t find any TCAdefaults or other TSconfig option to switch the copy mode of the extended clipboard to copy by default.\r\n\r\nAt the moment the default is \"move\" which can be very annoying.\r\n\r\nPlease add the possibility to choose the default behaviour of the \"setCopyMode\" button.\r\n\r\nThanks,\r\nSacha\r\n\r\n\r\n\r\n\r\n(issue imported from #M1277)",
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
            "mentioned": [],
            "attachments": [],
            "reviews": [
                {
                    "change": 38419,
                    "changeId": "",
                    "patchSet": 3,
                    "on": "2015-04-01T20:54:18Z",
                    "url": "https://review.typo3.org/c/38419",
                    "status": "ABANDONED"
                },
                {
                    "change": 70962,
                    "changeId": "",
                    "patchSet": 5,
                    "on": "2023-01-14T19:27:42Z",
                    "url": "https://review.typo3.org/c/70962",
                    "status": "ABANDONED"
                }
            ],
            "cites": [],
            "noteCount": 16,
            "botNoteCount": 8,
            "notes": [
                {
                    "author": "Sebastian Kurfuerst",
                    "on": "2005-10-22T19:07:47Z",
                    "note": "This issue is more complicated than it seems, because there setting for \"move\" is empty - and there is no easy condition to find out whether the default should apply or not. I will have a deeper look into it.\r\nGreets, Sebastian"
                },
                {
                    "author": "Sebastian Kurfuerst",
                    "on": "2005-10-23T20:16:15Z",
                    "note": "this issue is not so easy to fix and I don't see a nice solution at the moment. A patch is welcome, but currently I cannot have a deeper look into it.\r\nGreets, Sebastian"
                },
                {
                    "author": "Oliver Hader",
                    "on": "2011-09-19T12:55:23Z",
                    "note": "Should be a UserTS configuration and maybe an additional setting in the user preferences."
                },
                {
                    "author": "Gabriel Kaufmann TYPOworx GmbH | NewMedia",
                    "on": "2013-04-11T09:11:03Z",
                    "note": "Is there anything new for the TYPO3 4.6 oder 4.7 Tree?"
                },
                {
                    "author": "Tilo Baller",
                    "on": "2015-10-21T15:28:17Z",
                    "note": "What was the reason for abandoning the patch in review and what is the current progress of this feature?\r\n\r\nI got several requests from customers for adjusting the default behaviour to 'copy' instead of 'move'."
                },
                {
                    "author": "Daxboeck no-lastname-given",
                    "on": "2018-06-19T14:18:23Z",
                    "note": "I did now ask my developers to create a patch as the default of \"move\" when someone selects the 2nd or 3rd clipboard is very annoying.\r\nIt is against a thought of safety, it is against common sense.\r\nI just had too many cases where by accident stuff has been moved instead of copied.\r\n\"copy\" must be the default, there should be no doubt about it."
                },
                {
                    "author": "Sybille Peters",
                    "on": "2023-01-14T19:27:42Z",
                    "note": "Patch https://review.typo3.org/c/Packages/TYPO3.CMS/+/70962 was abandoned."
                },
                {
                    "author": "Benni Mack",
                    "on": "2026-01-23T08:30:36Z",
                    "note": "Summarizing the current state:\r\n\r\n- Currently, the option is set to \"move\" hardcoded without any possibility to change this.\r\n- Right now, this is debateble is the setCopyMode should be \"copy\" or \"move\"\r\n- If this should be configurable, then we need a new option, making this not a bug, but actually a feature"
                }
            ]
        },
        "results": [],
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

    TYPO3 issue tracker: no issue 99999999 at https://forge.typo3.org.

Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/issues/99999999.json?include=journals,relations,attachments",
        "query": "",
        "placedAgainst": "15.0.0-dev",
        "total": 0,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "issue": null,
        "results": [],
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

    ## #107904 Cache-busting applied to folder paths
    Bug · Closed · Frontend · filed by Simon Praetorius · filed 2025-10-29 · last touched 2025-12-02 · https://forge.typo3.org/issues/107904

    ## #107869 Add option to not add cache busting to generated URIs
    Bug · Closed · filed by Helmut Hummel · filed 2025-10-27 · last touched 2025-12-02 · https://forge.typo3.org/issues/107869

    ## #105953 f:uri.resource cache busting not working and in addition causing PHP warninigs when open_basedir is enabled
    Bug · Closed · Fluid · filed by Christian Ludwig · filed 2025-01-16 · last touched 2025-08-12 · https://forge.typo3.org/issues/105953

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/search.json?q=cache%20busting&issues=1&limit=3",
        "query": "cache busting",
        "placedAgainst": "",
        "total": 15,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "issue": null,
        "results": [
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
                "cites": []
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
                "cites": []
            },
            {
                "issue": 105953,
                "subject": "f:uri.resource cache busting not working and in addition causing PHP warninigs when open_basedir is enabled",
                "tracker": "Bug",
                "status": "Closed",
                "category": "Fluid",
                "reportedBy": "Christian Ludwig",
                "assignedTo": "",
                "createdOn": "2025-01-16T20:23:02Z",
                "updatedOn": "2025-08-12T14:36:32Z",
                "url": "https://forge.typo3.org/issues/105953",
                "relations": [],
                "attachments": [],
                "reviews": [],
                "cites": []
            }
        ],
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
        "issue": null,
        "results": [],
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
    Asked one word at a time: "file" reaches 13953 · "renderer" reaches 1175 · "RendererRegistry" reaches 5 · "FileRendererInterface" reaches 0.
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
                "matchCount": 13953
            },
            {
                "term": "renderer",
                "matchCount": 1175
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
        "issue": null,
        "results": [],
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

    TYPO3 issue tracker: 3 of 2432 open issues of the TYPO3 Core project, oldest filed first
    This is a page and not the set. What comes after it is reached by a narrower filter — an earlier date, one tracker — rather than by a larger limit, because the order is the tracker's own and more of it is more of the same end. breakdown answers how the whole of it is distributed.
    Age is a candidate and never a finding: read one whole by passing its number as issue, and what it still claims is established in the checkout rather than off this list.
    A row carries what the page came back with: the issues it is filed against, the files hanging off it, and the changes on review.typo3.org whose commit message names it, each with the state it is in. That state is where a change stands and not a verdict on the issue: an ABANDONED one is grounds to read the argument on it with typo3_gerrit_lookup, where the objection was written down and is regularly to the approach rather than to the defect. A row with no such line is one nothing there names — or one the review server did not answer for, which this list does not separate.

    ## #14277 Start/Stop time for pages is ignored in standard menu objects
    Feature · Accepted · Frontend · filed by Michael Stucki · unassigned · filed 2004-08-20 · last touched 2025-04-04 · https://forge.typo3.org/issues/14277
    Relation: relates #16815 — Bug · Closed · Sitemap ignoring "Start" and "End" flags · https://forge.typo3.org/issues/16815
    Relation: relates #98964 — Bug · Closed · Menu object caching creates too many records resulting in huge cache_hash table · https://forge.typo3.org/issues/98964
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
        "total": 2432,
        "terms": [],
        "categories": [],
        "categoriesUsed": [],
        "people": [],
        "breakdown": null,
        "issue": null,
        "results": [
            {
                "issue": 14277,
                "subject": "Start/Stop time for pages is ignored in standard menu objects",
                "tracker": "Feature",
                "status": "Accepted",
                "category": "Frontend",
                "reportedBy": "Michael Stucki",
                "assignedTo": "",
                "createdOn": "2004-08-20T08:45:13Z",
                "updatedOn": "2025-04-04T06:59:33Z",
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
                        "change": 61395,
                        "status": "ABANDONED",
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/61395"
                    }
                ],
                "cites": []
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
                "cites": []
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
                "cites": []
            }
        ],
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

    TYPO3 issue tracker: 3 of 22 open issues of the TYPO3 Core project, tracker Bug, in RTE (rtehtmlarea + ckeditor), longest untouched first
    This is a page and not the set. What comes after it is reached by a narrower filter — an earlier date, one tracker — rather than by a larger limit, because the order is the tracker's own and more of it is more of the same end. breakdown answers how the whole of it is distributed.
    Age is a candidate and never a finding: read one whole by passing its number as issue, and what it still claims is established in the checkout rather than off this list.
    A row carries what the page came back with: the issues it is filed against, the files hanging off it, and the changes on review.typo3.org whose commit message names it, each with the state it is in. That state is where a change stands and not a verdict on the issue: an ABANDONED one is grounds to read the argument on it with typo3_gerrit_lookup, where the objection was written down and is regularly to the approach rather than to the defect. A row with no such line is one nothing there names — or one the review server did not answer for, which this list does not separate.
    A row that names code carries it: the classes, methods and core files its own text cites, each with whether the packages installed here still carry it, at TYPO3 15.0.0-dev. A report whose names are all gone is a candidate to drop without opening the checkout, and one whose names all stand is a candidate to read. It is read from the subject and the description, because the page carries no comment, and a name it could not place is unplaced rather than gone.
    An area is where an issue was filed and not everything it is about. A report about this one regularly sits under another area, so what came back is a floor rather than the set — query the words as well where the question is about a subject. An issue carrying no Category at all is in no area, and thousands of the open bugs carry none, so no wording of this reaches one: ask again without category, narrowed by createdSince instead, where the question is whether it was reported.

    ## #87400 CKEditor: assign correct CSS class to tags with entryHTMLparser_db
    Bug · New · RTE (rtehtmlarea + ckeditor) · filed by Benedikt Imminger · unassigned · filed 2019-01-11 · last touched 2019-01-11 · https://forge.typo3.org/issues/87400
    Relation: relates #87314 — Feature · New · allowedAttribs / allowAttributes usage in config · https://forge.typo3.org/issues/87314
    Relation: relates #92943 — Bug · Closed · RTE ckeditor does not respect YAML configuration · https://forge.typo3.org/issues/92943
    Files (1): RTE Bug.mov

    ## #97817 RTE removes line with empty, allowed tags when saving
    Bug · New · RTE (rtehtmlarea + ckeditor) · filed by Jigal van Hemert · unassigned · filed 2022-06-28 · last touched 2022-06-28 · https://forge.typo3.org/issues/97817
    Cites: typo3/sysext/core/Classes/Html/RteHtmlParser.php — shipped by core · TYPO3\CMS\Core\Html\RteHtmlParser::divideIntoLines — shipped by core

    ## #88690 Translated content elements are not available in linkbrowser of the ckeditor in free mode
    Bug · New · RTE (rtehtmlarea + ckeditor) · filed by Ronny Hauptvogel · unassigned · filed 2019-07-05 · last touched 2023-03-05 · https://forge.typo3.org/issues/88690
    Relation: relates #89701 — Bug · Closed · Link wizard lists only content elements of the default language · https://forge.typo3.org/issues/89701
    Relation: relates #90138 — Feature · Closed · Language and mode (free or connected) should be handled in the links module when creating an anchor to content · https://forge.typo3.org/issues/90138
    Relation: relates #91160 — Bug · Closed · Links to content element (anchor) in link wizard not possible when not in default language · https://forge.typo3.org/issues/91160
    Relation: relates #88382 — Bug · Closed · Link wizard lists all content elements of a page regardless of source language · https://forge.typo3.org/issues/88382
    Relation: relates #92809 — Bug · Accepted · Anchor Links in Link Wizard not translated correctly · https://forge.typo3.org/issues/92809

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
        "total": 22,
        "terms": [],
        "categories": [],
        "categoriesUsed": [
            "RTE (rtehtmlarea + ckeditor)"
        ],
        "people": [],
        "breakdown": null,
        "issue": null,
        "results": [
            {
                "issue": 87400,
                "subject": "CKEditor: assign correct CSS class to tags with entryHTMLparser_db",
                "tracker": "Bug",
                "status": "New",
                "category": "RTE (rtehtmlarea + ckeditor)",
                "reportedBy": "Benedikt Imminger",
                "assignedTo": "",
                "createdOn": "2019-01-11T11:07:13Z",
                "updatedOn": "2019-01-11T11:07:13Z",
                "url": "https://forge.typo3.org/issues/87400",
                "relations": [
                    {
                        "issue": 87314,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/87314",
                        "subject": "allowedAttribs / allowAttributes usage in config",
                        "tracker": "Feature",
                        "status": "New"
                    },
                    {
                        "issue": 92943,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/92943",
                        "subject": "RTE ckeditor does not respect YAML configuration",
                        "tracker": "Bug",
                        "status": "Closed"
                    }
                ],
                "attachments": [
                    {
                        "filename": "RTE Bug.mov",
                        "contentType": "video/quicktime",
                        "size": 3779702,
                        "on": "2019-01-11T11:01:30Z",
                        "url": "https://forge.typo3.org/attachments/download/34053/RTE%20Bug.mov"
                    }
                ],
                "reviews": [],
                "cites": []
            },
            {
                "issue": 97817,
                "subject": "RTE removes line with empty, allowed tags when saving",
                "tracker": "Bug",
                "status": "New",
                "category": "RTE (rtehtmlarea + ckeditor)",
                "reportedBy": "Jigal van Hemert",
                "assignedTo": "",
                "createdOn": "2022-06-28T07:46:04Z",
                "updatedOn": "2022-06-28T07:46:04Z",
                "url": "https://forge.typo3.org/issues/97817",
                "relations": [],
                "attachments": [],
                "reviews": [],
                "cites": [
                    {
                        "name": "typo3/sysext/core/Classes/Html/RteHtmlParser.php",
                        "kind": "file",
                        "method": "",
                        "state": "shipped",
                        "in": [
                            {
                                "extension": "core",
                                "path": "typo3/sysext/core/Classes/Html/RteHtmlParser.php"
                            }
                        ]
                    },
                    {
                        "name": "TYPO3\\CMS\\Core\\Html\\RteHtmlParser",
                        "kind": "qualified",
                        "method": "divideIntoLines",
                        "state": "shipped",
                        "in": [
                            {
                                "extension": "core",
                                "path": "typo3/sysext/core/Classes/Html/RteHtmlParser.php"
                            }
                        ]
                    }
                ]
            },
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
                "cites": []
            }
        ],
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
        "issue": null,
        "results": [],
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
    This is a page and not the set, and limit stops at 50. What reaches the rest is breakdown, which answers how the whole set is distributed — there are no other words to narrow a person by, and a tracker or a date answers a smaller question than the one asked.
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
                "cites": []
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
                ]
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
                "cites": []
            }
        ],
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
        "issue": null,
        "results": [],
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

    TYPO3 issue tracker: 3 of 5 open issues of the TYPO3 Core project, filed by or assigned to Frank Nägler, longest untouched first
    This is a page and not the set, and limit stops at 50. What reaches the rest is breakdown, which answers how the whole set is distributed — there are no other words to narrow a person by, and a tracker or a date answers a smaller question than the one asked.
    Age is a candidate and never a finding: read one whole by passing its number as issue, and what it still claims is established in the checkout rather than off this list.
    A row carries what the page came back with: the issues it is filed against, the files hanging off it, and the changes on review.typo3.org whose commit message names it, each with the state it is in. That state is where a change stands and not a verdict on the issue: an ABANDONED one is grounds to read the argument on it with typo3_gerrit_lookup, where the objection was written down and is regularly to the approach rather than to the defect. A row with no such line is one nothing there names — or one the review server did not answer for, which this list does not separate.

    ## #89259 Create new icons and replace icons for "Page enabled in menus" context menu
    Task · Accepted · Backend User Interface · filed by Frank Nägler · assigned to Benjamin Kott · filed 2019-09-25 · last touched 2019-09-25 · https://forge.typo3.org/issues/89259
    Relation: relates #85918 — Feature · Closed · Show "Page enabled in menus" in ContextMenu for pages · https://forge.typo3.org/issues/85918
    Relation: relates #102497 — Task · New · Unify display and grouping of context menus · https://forge.typo3.org/issues/102497
    Files (1): image.png

    ## #89326 Prevent duplicate redirects in auto redirects
    Bug · Accepted · Link Handling & Redirect Handling · filed by Guido Schmechel · assigned to Frank Nägler · filed 2019-10-01 · last touched 2023-11-09 · https://forge.typo3.org/issues/89326
    Relation: relates #89325 — Task · New · Prevent duplicate redirects in backend module · https://forge.typo3.org/issues/89325
    Relation: relates #89301 — Task · Accepted · Streamline automatic slug & redirects handling · https://forge.typo3.org/issues/89301
    Relation: relates #92448 — Bug · New · changing slug again after reverting an auto update causes wrong URLs on sub pages · https://forge.typo3.org/issues/92448

    ## #104918 Drag & Drop to create pages in pagetree is not usable anymore
    Bug · Accepted · Pagetree · filed by Frank Nägler · unassigned · filed 2024-09-11 · last touched 2024-10-15 · https://forge.typo3.org/issues/104918
    Relation: relates #104697 — Bug · Accepted · Unexpected behaviour - placing new items in the page tree. Safari Desktop · https://forge.typo3.org/issues/104697
    Relation: duplicates #106028 — Bug · Closed · Cannot create/move page at end of tree using d&d · https://forge.typo3.org/issues/106028

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
        "total": 5,
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
        "issue": null,
        "results": [
            {
                "issue": 89259,
                "subject": "Create new icons and replace icons for \"Page enabled in menus\" context menu",
                "tracker": "Task",
                "status": "Accepted",
                "category": "Backend User Interface",
                "reportedBy": "Frank Nägler",
                "assignedTo": "Benjamin Kott",
                "createdOn": "2019-09-25T09:39:35Z",
                "updatedOn": "2019-09-25T09:39:35Z",
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
                "reviews": [],
                "cites": []
            },
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
                        "status": "New"
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
                "reviews": [],
                "cites": []
            },
            {
                "issue": 104918,
                "subject": "Drag & Drop to create pages in pagetree is not usable anymore",
                "tracker": "Bug",
                "status": "Accepted",
                "category": "Pagetree",
                "reportedBy": "Frank Nägler",
                "assignedTo": "",
                "createdOn": "2024-09-11T19:40:39Z",
                "updatedOn": "2024-10-15T09:46:22Z",
                "url": "https://forge.typo3.org/issues/104918",
                "relations": [
                    {
                        "issue": 104697,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/104697",
                        "subject": "Unexpected behaviour - placing new items in the page tree. Safari Desktop",
                        "tracker": "Bug",
                        "status": "Accepted"
                    },
                    {
                        "issue": 106028,
                        "relation": "duplicates",
                        "url": "https://forge.typo3.org/issues/106028",
                        "subject": "Cannot create/move page at end of tree using d&d",
                        "tracker": "Bug",
                        "status": "Closed"
                    }
                ],
                "attachments": [],
                "reviews": [],
                "cites": []
            }
        ],
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
    Status: Closed 725 · Rejected 34 · Accepted 3 · Needs Feedback 1 · Under Review 1
    Tracker: Task 401 · Bug 301 · Feature 54 · Epic 4 · Story 4
    Area: none 178 · Backend User Interface 163 · Backend API 126 · Backend JavaScript 109 · FormEngine aka TCEforms 29 · Install Tool 26 · Documentation 13 · Code Cleanup 12 · TypoScript 10 · Frontend 9 · Site Handling, Site Sets & Routing 8 · Fluid 7 · and 29 more holding 74
    Filed in: 2015 200 · 2014 124 · 2016 113 · 2018 104 · 2017 89 · 2019 62 · 2024 18 · 2022 12 · 2020 11 · 2023 5 · 2021 4 · 2006 3 · and 9 more holding 19

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
                            "count": 725
                        },
                        {
                            "name": "Rejected",
                            "count": 34
                        },
                        {
                            "name": "Accepted",
                            "count": 3
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
                }
            ]
        },
        "issue": null,
        "results": [],
        "unavailable": null
    }
