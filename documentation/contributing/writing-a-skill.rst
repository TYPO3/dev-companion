:navigation-title: Writing a skill

Writing a task skill
====================

What the published skills are is in `AGENTS.md <../../AGENTS.md>`_, and the
order every one of them starts in is `skills/base.md <../../skills/base.md>`_,
which is one file rather than a paragraph each. This page is the other half: how
a skill is **written**, and what holds each rule to that.

The rules are narrow for one reason. A skill is not a document this repository
keeps — it is a copy the installer writes into somebody else's project, where
nothing reports that it has fallen behind the server it came from and no release
of this server corrects it. Whatever it states, it states permanently, in a
context that is paid for by the token, about an installation it cannot see.

Earning a skill
---------------

A domain earns one when a scenario or a recorded session shows that the tools
and skills that exist **fail to carry the task** — not when the subject is large
enough to look like it deserves one. Release, static analysis, performance and
security have each looked like a skill at some point; what settles it is a case
in `scenarios/ <../../scenarios/readme.md>`_ or a run in
`scenarios/runs/ <../../scenarios/runs/>`_ where the existing workflow was
reached for and came up short.

A new skill also buys a **baseline run**: the case prompt run in an environment
the skills are not installed into, recorded beside the run it is compared
against. That is what turns "the skill helped" into a measurement, and it is
bought for a new skill alone — an edit stays on the author's word, because the
charge falls on every change and what it would catch has not been seen here
(`D-SKL-035 <../../decisions/task-skills/skl-035-a-new-skill-is-measured-against-a-run-without-it.md>`_).

What such a run shows is almost always smaller than the domain: an order nobody
keeps, a step that only runs when a finding happens to walk into it, a boundary
two skills both believe they own. The skill is written around that and around
nothing else. "Less is more" is not a preference here — it is an instruction
every session in another project loads before it does anything.

Research first
--------------

A skill is written against the current state of the practice, never from recall.
What that costs is one session's reading; what recall costs is a file that
states, permanently and in somebody else's project, a practice that was current
when its author last happened to see it. Before the first line:

* Ask this server what it already answers about the domain, with the tools the
  skill will route to. ``typo3_documentation_lookup`` for the official
  documentation at the versions in play, ``typo3_hint_lookup`` for the
  conventions, ``typo3_changelog_lookup`` for what moved. An author who has not
  called a tool is routing to an answer shape they are guessing at, and a
  surface the server already covers does not need a paragraph in a skill.
* Read the current official documentation of the domain itself, and where the
  task runs through tools this server does not own — a packaging tool, a
  registry, a CI runner, a test harness — read theirs. Which tools exist, and
  what each one does by default, is exactly the fact that moves after the file
  is published.
* Read what the failing run actually did, call by call, rather than what its
  report concluded. The gap the skill is written around is in the calls.

None of that research goes into the skill; the rule below still holds. It
decides what the skill **asks**: which surfaces exist at all, which of them a
tool already owns, and where the practice moves fast enough that only an
instruction to check survives being written down. Written from recall, a skill
invents surfaces that do not exist and misses the one that decides the case.

Review before publishing
------------------------

A skill is not finished when its tests pass. Those hold its shape — the name,
the base, the references, that it keeps no second copy of what a tool owns — and
no assertion here can say whether the workflow it describes is the one a
maintainer actually runs, whether its order matches how the work really goes, or
whether the step that decides the outcome is in it at all. The person who asked
for the skill can say all three, and is the only one who can.

So the skill is shown before it is committed: ``SKILL.md`` and every reference,
whole, not summarised. And feedback is **asked for by name** — does this match
how the task is really done, which step is missing, which one is wrong, what
does it claim that is not true here. "Does this look good?" gets agreement, not
review. What comes back is worked in before the commit, because the copy in
somebody else's project is not corrected by the next release of this server.

Nothing holds the file back while that happens. A directory below ``skills/``
carrying a ``SKILL.md`` is published, so the review is what the commit waits for
and the commit is what publishes —
`D-SKL-087 <../../decisions/task-skills/skl-087-every-skill-in-the-directory-is-published.md>`_,
which took out the declaration that used to do it and says what would bring one
back.

The rules
---------

* It is filed under the name it calls itself, with a description a client can
  route on — ``SkillTest::everySkillIsPublishedUnderTheNameItCallsItself``
* It says which server it needs, in the field the standard has for it —
  ``SkillTest::everySkillSaysWhichServerItNeeds``
* Its front matter carries the standard's fields and nothing else —
  ``SkillTest::everyFrontMatterFieldIsOneTheStandardDefines``
* Every description is written to the budget all of them share —
  ``SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn``
* Every directory in ``skills/`` is published —
  ``SkillTest::everySkillInTheDirectoryIsPublished``
* It starts from the base before it reaches for anything of its own —
  ``SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence``
* It keeps no second copy of what a tool owns —
  ``SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns``
* It routes through the owners of its own facts, in the order it needs them —
  ``SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder``
* A call it names in order not to make it is written as a discharge and routed
  nowhere — ``SkillTest::everyDischargedCallIsWrittenAsOneAndRoutedNowhere``
* Every reference is one hop away and loaded on demand —
  ``SkillTest::everyReferenceIsOneHopAwayAndLoadedOnDemand``
* A paragraph three skills share stops being copied —
  ``SkillTest::aParagraphThreeSkillsShareStopsBeingCopied``
* A skill that judges keeps its checklist beside it —
  ``SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem``
* It says what it owns — ``SkillTest::everySkillStatesWhatItOwns``
* A crossing into another skill is an instruction at the moment it happens, and
  never only in the paragraph the workflow is left in —
  ``SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor``
* Every ``typo3://`` resource it names is one this server serves —
  ``SkillTest::everyResourceASkillNamesIsOneTheServerServes``
* Every guide it names is handed over by the call that reads it —
  ``SkillTest::everyGuideASkillNamesIsHandedOverByTheCallThatReadsIt``
* What it sends the session to read out of an answer is a key that answer
  carries —
  ``SkillTest::everyCallTheBaseFixesAnswersWithWhatItSendsTheSessionToRead``

Most of them run over the skills directory rather than over a list, so a skill
added later is held to them without anybody registering it anywhere — which is
the point, because the list is the thing a new skill is written without ever
seeing. Three do not: the routing rule and the checklist rule read their list
from ``ROUTING_SKILLS``, and the last one makes the calls the base fixes.
``SkillTest::theAuthoringContractIsWrittenDownAndNamesWhatHoldsIt`` holds this
table and that set to each other in both directions: a rule here with no test
behind it, or a directory-wide assertion nobody wrote down, fails.

**The name and the description.** The directory name, the ``name:`` in the front
matter and the name every other skill calls it by are one string. The
``description`` is the only part of a skill read before it is chosen, so it is
written in the words a user brings — the request, the symptom, the files being
touched — and never in this server's tool names.
`D-AUD-003 <../../decisions/audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md>`_
is what a wrong one costs: a review prompt whose every criterion the conformance
skill's body would have met did not activate it, and all thirty-five calls of
that session went through Bash.

What it does not carry is the **workflow the body owns**. A description that
lists the steps is read where the body has not been loaded yet, and it is then
the whole of what the session has: obra/superpowers measured a description
saying "code review between tasks" producing one review where the skill's flow
specified two. So the description names the task, the sides and where the skill
stops, and every ordered step stays in the body — a sentence beginning "find the
change, fetch the patch set, put it on the branch it targets", which patch
checkout opened with, is the body's section headings in one line. Where such a
clause carries a word a user would type, the word stays as a trigger rather than
as a step. What names another skill stays whatever it looks like: a boundary is
read before the choice and is the only thing that can send the task elsewhere.
`D-SKL-024 <../../decisions/task-skills/skl-024-a-description-names-the-task-and-leaves-the-steps-to-the-body.md>`_
is the sweep that cut six of them, and nothing holds this over the directory —
which clause is a summary is a reading of the body, not a property of the file.
A step clause also **narrows** what the description names, which is the stronger
half: that one listed a way of doing the job that moves the branch throughout,
so a request naming a git worktree read as another skill's case altogether.
``SkillTest::aWorktreeTaskMatchesTheSkillThatOwnsTheCheckout`` holds the trigger
and the body it promises, on the one description that was measured.

The clause a description **opens on** narrows the hardest, because everything
after it is read as a case of it. ``typo3-extension-upgrade`` listed replacing
what a major removed among its shapes and opened on carrying a package to
another set of versions, so a bug report whose cause is such a removal read as a
premise the task did not meet and the skill stayed shut for a whole session
(`D-SKL-061 <../../decisions/task-skills/skl-061-the-upgrade-description-is-reachable-from-a-defect.md>`_).
A premise is rewritten with the body it governs, and
``SkillTest::aDefectInsideTheDeclaredRangeMatchesTheRemovalSkill`` holds that
pair.

It names **every side of the domain the skill owns**, and a skill that owns two
sides of one thing says so in the opening line rather than in the ninth item of
a list. A domain named by one of its halves — "frontend content elements" for a
skill that also owns the backend preview of the same element — sends the other
half to whichever skill happens to carry a word of it, and the body that covers
it is never loaded. That is the same entry's second sighting, one task later,
and ``SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement`` holds
the pair it was measured on. Which sides a skill owns is not readable off the
file, so nothing holds this over the directory: it is a question the author
answers against ``This skill owns …`` and the crossings in the body, and a
crossing that names one side while the description names both is the file
disagreeing with itself in somebody else's project.

And where the body owns **two jobs**, the description names both. A job is not a
side of one thing and not a step: it produces a deliverable somebody can be
handed and stops, and a user asks for it in words of its own.
``typo3-core-issue-triage`` opens on a backlog whose candidates are handed over
before any issue is chosen, and its description named only the issue — so a
session that searched that backlog six times opened nothing
(`D-SKL-076 <../../decisions/task-skills/skl-076-a-description-names-both-jobs-a-skills-body-owns.md>`_,
held by ``SkillTest::aBacklogSearchMatchesTheSkillThatOwnsTheCandidates``).
Three trims took the clause out in stages, each reading it as a summary of the
body, which is what the rule above asks for and what a job is the exception to.
Which jobs a body owns is a reading of the body, so nothing holds this over the
directory either: ``typo3-extension-health`` is the other case, and the report
it hands over before a file is changed stands in its description already
(`D-SKL-064 <../../decisions/task-skills/skl-064-the-audit-and-the-work-that-answers-it-are-one-skill.md>`_).

**The budget every description shares.** A client reads all of the descriptions
in one listing against one character budget — in Claude Code, one percent of the
context window converted at three or four bytes to the token, so 6000 characters
on a 200k session and 30000 on a 1M one. Over that it drops whole descriptions
rather than shortening them, least-used first, which is every skill of this
server's on a fresh install; the dropped skill is listed by its name alone and
nothing tells the session it happened. So a description is paid for by the other
skills and not by its own, and a thirteenth skill costs the twelve.
`D-SKL-026 <../../decisions/task-skills/skl-026-the-descriptions-are-written-to-the-listing-budget-they-share.md>`_
is where that arithmetic was read off the client, what the twelve cost after it,
and what the client's own bundled skills leave over.

What the budget asks is asked of the commit that adds a skill, because that is
the commit the listing grows in
(`D-SKL-054 <../../decisions/task-skills/skl-054-the-listing-budget-is-what-a-client-reads.md>`_).
Where the room is not there, it is where a description is shortened or a
workflow is merged into one that already exists — a question about which
descriptions are worth their room, and one nobody can answer while writing only
the newest.

**Starting from the base.** The skill links ``references/base.md`` and then
states what it *adds* to it. It never restates a step the base already fixes:
five hand-written copies of one order is what the base replaced, and the copy
that drifts is always the one already published into somebody's project.

**Nothing a tool owns.** No TYPO3 version number, no API signature, no
dependency constraint, no backend markup, and no command the checkout has not
been asked for. A version in a permanently loaded instruction is the one fact
that cannot be re-asked when the installation turns out to be a different one,
and no answer built on it ever says where it came from. What that leaves is the
version a step states as the **boundary of a property**, which the installation
is not asked for: step 3 of ``typo3-development-installation`` says from which
major the setup command reports an option as disabled, because unbounded that
sentence sends a caller below it looking for an option that is not there
(`D-SKL-057 <../../decisions/task-skills/skl-057-a-commands-options-are-read-from-the-installed-console.md>`_).
The guard reads a version out of ``TYPO3 <n>``, so which of the two a bare
number is stays the author's, like a layout key. The same holds for a package
name: it is one word in a published file that no release of this server can
correct, so it is written where a task reads it once — a reference — rather than
where every task carries it. That one and an environment variable are the two
kinds a body can be shown to carry, and
``SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns`` reads ``SKILL.md`` alone
for them
(`D-SKL-052 <../../decisions/task-skills/skl-052-the-injected-size-of-a-skill-is-what-the-retention-rule-leaves.md>`_).
A layout key and a command option have no such shape and stay the author's.

**Which server it needs.** ``skills/`` is also read by whoever copies it, and a
copied skill has no server. ``references/base.md`` is written at publication, so
the first instruction of that copy is a link to nothing and every lookup under
it is a tool the session does not have. The guard in the base does not reach it:
that one is written for a session whose tools do not answer, not for one whose
base never arrived. So the skill says it in ``compatibility``, the field the
standard keeps for an environment requirement — one line, and the same line in
every skill, because it is a fact about this package rather than about a
workflow. That is also the one package name a skill may carry: the paragraph
above is about what a *task* reaches for, and this is what the reader has to
install before any of it works. No version goes in it. The front matter it sits
in is written so a reader of the standard can parse it, which an unquoted ``: ``
in a description does not — three of them broke the whole block for everything
but this repository's own patterns.

**Routing, in order.** What a skill adds to the base is a short list of tools in
the order it needs them, recorded in the ``ROUTING_SKILLS`` map of
``SkillTest``. The four calls the base already fixes are deliberately absent
from that list.

**A call named in order not to make it is a discharge.** Where a step would
prescribe a tool and something the session is already holding answers it, the
step says so in one construct — the tool's own name, then ``is discharged by``,
then what answers it instead — and the tool is recorded in ``DISCHARGED_TOOLS``
rather than among the routings. A routing is asserted by finding the tool's name
in the body, so any other wording of "you already have this" satisfies the
routing rule while telling the caller the opposite; the construct is what the
assertion skips and what the two lists are kept apart by
(`D-SKL-055 <../../decisions/task-skills/skl-055-a-call-named-in-order-not-to-make-it-is-a-discharge.md>`_).
Discharging and routing are exclusive: a body that names the tool a second time
is routing to what it has just discharged.

**The page a step expects read whole.** Where a step describes a whole procedure
and routes to a search for it, name the call that reads the page at that step:
``typo3_rule_lookup`` with the ``documentId`` the procedure exists whole as. A
search answers the section the query matched, so a session that only searches
reads a procedure in pieces without learning it is one — which is what a core
session did with the Gerrit push page
(`D-AUD-007 <../../decisions/audience/aud-007-the-prose-documents-are-named-where-a-session-already-looks.md>`_).
The ``typo3://guides`` address may stand beside the call and is not the
handover: it is delivery to a client that renders a resource list, and the
session this was last read off had none, held the ids anyway and searched
(`D-ANS-070 <../../decisions/answers/ans-070-a-document-is-handed-over-by-the-call-that-reads-it.md>`_).
Name it once, where it is needed, and not at every mention. This is not a
licence to restate the page — the call is the routing, and what is behind it
stays there.

**References.** Anything a task reads once — a checklist, a rubric, one layer's
implementation guide — is a file below ``references/``, named in ``SKILL.md``
together with when to read it. One hop: a reference that loads another reference
is a body whose size the skill no longer decides.

**Judgment keeps a checklist; construction does not.** A skill that assesses
something needs a rubric and surfaces beside it. A skill that builds something
needs registries, and registries are tools.

**What it owns, and where it stops.** Every skill says ``This skill owns …``,
and where its work runs into another skill's, the crossing is explicit: name the
verified stopping point, stop before editing the other owner's files, activate
that owner, carry across only the scope and verified behaviour it needs
(`R-SKL-003 <../../requirements/task-skills/skl-003-crossing-into-another-skills-work-is-an-explicit-transition.md>`_).

**And it is written where it happens.** That paragraph is where a reader learns
the boundary, and it is the last thing in the file — read at the moment a
workflow is being left, which is the moment of least appetite for opening
another one. So the crossing itself stands as a step at the point it occurs,
telling the session to invoke the successor by name and saying what crosses over
(`R-SKL-018 <../../requirements/task-skills/skl-018-a-skill-that-hands-over-tells-the-session-to-invoke-the-next-one.md>`_).
An imperative alone is not it: one skill closed on ``Activate`` and three
skills' names, a session read that sentence and crossed none of them
(`D-SKL-053 <../../decisions/task-skills/skl-053-an-absence-in-the-extension-answer-names-the-skill-that-owns-it.md>`_).
Where the moment is something the reader says, the step names the sentence that
fires it and the sentence that does not — and that half is the author's, like
the sides a description names.

**A crossing the reader's sentence fires names the act that begins the work
too.** A sentence has to be recognised, and a core review quoted this crossing
and then edited the files it had just reviewed without taking it
(`D-SKL-077 <../../decisions/task-skills/skl-077-the-crossing-out-of-a-review-is-recognised-on-the-first-edit-meant-to-survive.md>`_,
held by ``SkillTest::theCrossingOutOfAReviewNamesTheEditThatBeginsTheRework``).
What the skill permits and is not that act is named beside it: a scratch probe,
put back and leaving no diff. The section drawing that boundary is pointed at
rather than copied.

**A crossing that restarts the order names the calls whose arguments changed.**
The successor opens on the base, and a session that has just walked it reads
that step as answered: a core review crossed into the patch workflow as
instructed and then wrote the patch without the deprecation sweep and without
hints on the paths it edited
(`D-SKL-072 <../../decisions/task-skills/skl-072-a-workflow-handover-names-the-calls-the-next-order-restarts-with.md>`_,
held by
``SkillTest::theCrossingOutOfAReviewNamesTheCallsTheOrderRestartsWith``). What
the paragraph names is the calls the crossing changes the answer to, as calls
rather than as the order to restart, and three of them rather than a checklist.
Which crossings restart an order is the author's, like the sides a description
names.

**A workflow that ends in public stops once more before it gets there.** Where
the last step publishes — a tracker entry, a pushed change, a release — the
skill asks before that step whether what it found is a security defect, and
stops there when it is: the finding stands, the publishing step is not taken,
and where the report goes instead is a lookup rather than a line in the file
(`R-SKL-020 <../../requirements/task-skills/skl-020-a-workflow-that-ends-in-public-stops-when-the-finding-is-a-vulnerability.md>`_,
held by ``SkillTest::aWorkflowThatEndsInPublicationStopsAtAVulnerability``). It
is asked of every finding rather than of the ones that read as alarming, and
which skills end in public is not readable off a file — so this is the author's,
like the sides a description names.

**A skill whose product is a report says what form the report has.** A section
that fixes the severity bands, what each finding owes and the surfaces it closes
on has specified a document rather than a chat reply, and it is what makes that
document long — so it says the report is markdown the reader can copy and that
the answer is where it goes
(`R-SKL-023 <../../requirements/task-skills/skl-023-a-skill-whose-product-is-a-report-says-the-report-is-copyable-markdown.md>`_,
held by ``SkillTest::aReportIsCopyableMarkdownAndTheAnswerIsWhereItGoes``). A
path is the caller's to ask for, and where one is asked for it is outside the
checkout the skill assessed. Which skills produce a report is the author's too,
read off the bodies.

Publishing it
-------------

Publishing is deleting the ``metadata`` declaration above, and
``Installer::skills()`` is the directory minus what still carries it. There is
no list to add the name to: one existed, and a list beside the file is a second
place the same fact lives.

What that one edit turns on: the skill is copied into every client's own skills
directory by ``bin/typo3-dev-companion install``, it is served as a
``typo3://skill`` resource, and ``knowledge/task-intents.json`` may name it.
Nothing this server answers with may name a skill before that — a route into one
nobody has installed is worse than none, because the caller cannot tell the two
apart — and ``SkillTest::everySkillNamedInKnowledgeIsPublished`` holds every
name there to what is published. The intent that routes to it is written in the
same commit and never before it
(`D-SKL-013 <../../decisions/task-skills/skl-013-the-guide-names-the-skill-that-owns-the-task.md>`_).

Two things the skill does not carry itself are supplied at publication. The
installer copies ``skills/base.md`` into the new directory as
``references/base.md`` — one copy per skill, because each of them lands in
another project alone and a link out of its own directory would resolve here and
nowhere it is actually read. And ``knowledge/server-scope.json`` has to name the
workflow among what the server covers, or
``ScopeTest::everyPublishedSkillIsAnnouncedByTheScope`` fails: a skill served to
a client that the scope does not announce is one nothing tells the caller about.

Then run the installer in the checkout that plays the environment the skill is
for, before any run that is meant to measure it. The published skills are a copy
and nothing reports that they are older than the server;
`todo/reference/ <../../todo/reference/>`_ says which checkout plays which
environment on this machine and how the installer is reached there.

What nothing holds
------------------

Three of the steps above are the author's and nothing reads them off a file:
that a domain earned a skill at all, that the practice was researched before it
was written, and that the skill was shown and asked about. Each leaves the same
trace as its absence — a skill written from recall is shaped exactly like one
written from the documentation, and it is wrong in places no assertion knows to
look. They are written down because that is all that can be done for them, and
because the author who skips them is usually the one who has not read this page.

And that a session **does** what a skill says. Most of the table is read off the
file, which makes it a proxy: the wording is present and a reorganisation can
leave it present while the behaviour goes. One gap narrower than behaviour is
checkable and is checked — a skill does not only name a tool, it says what to
read out of the answer, and for the four calls the base fixes those keys are
asserted on the answer the tool really returns
(`D-SKL-025 <../../decisions/task-skills/skl-025-a-routed-tool-is-called-and-held-to-what-it-reads.md>`_).
What that leaves is prose going stale against a tool that kept every key.
`D-EVI-002 <../../decisions/evidence/evi-002-a-skill-crossing-is-read-rather-than-run.md>`_
accepts that proxy for the skill crossing and says why no forward run will
replace it. Everywhere else, what measures the behaviour is a case in
`scenarios/contracts/ <../../scenarios/contracts/readme.md>`_ or an open review
in `scenarios/forward/ <../../scenarios/forward/readme.md>`_ — and a forward run
grades the answer a session produced, never the file it came out of, which is
why the authoring contract is the half of a skill that has to be written down
instead.
