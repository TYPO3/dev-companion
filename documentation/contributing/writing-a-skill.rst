:navigation-title: Writing a skill

Writing a task skill
====================

What the published skills are is in `AGENTS.md <../../AGENTS.md>`_. The order
every one of them starts in is `skills/base.md <../../skills/base.md>`_, which
is one file rather than a paragraph each. This page is the other half: how a
skill is **written**, and what holds each rule to that.

The rules are narrow for one reason. A skill is not a document this repository
keeps. It is a copy the installer writes into somebody else's project. There
nothing reports that it has fallen behind the server it came from, and no
release of this server corrects it. Whatever it states, it states permanently,
in a context that is paid for by the token, about an installation it cannot see.

Earning a skill
---------------

A domain earns one when a scenario or a recorded session shows that the tools
and skills that exist **fail to carry the task**. Not when the subject is large
enough to look like it deserves one. Release, static analysis, performance and
security have each looked like a skill at some point. What settles it is a case
in `scenarios/ <../../scenarios/readme.md>`_ or a run in
`scenarios/runs/ <../../scenarios/runs/>`_. There a session reached for the
workflow that exists and came up short.

A new skill also buys a **baseline run**. That is the case prompt run in an
environment without the skills, recorded beside the run it stands against. That
is what turns "the skill helped" into a measurement, and only a new skill buys
it. An edit stays on the author's word, because the charge falls on every change
and nobody here has seen what it would catch
(`D-SKL-035 <../../decisions/task-skills/skl-035-a-new-skill-is-measured-against-a-run-without-it.md>`_).

What such a run shows is almost always smaller than the domain. An order nobody
keeps, a step that only runs when a finding happens to walk into it, a boundary
two skills both believe they own. The skill takes shape around that and around
nothing else. "Less is more" is not a preference here — it is an instruction
every session in another project loads before it does anything.

Research first
--------------

A skill comes from the current state of the practice, never from recall. What
that costs is one session's read. What recall costs is a file in somebody else's
project that states a practice permanently. That practice was current when its
author last happened to see it. Before the first line:

* Ask this server what it already answers about the domain, with the tools the
  skill will route to. ``typo3_documentation_lookup`` for the official
  documentation at the versions in play, ``typo3_hint_lookup`` for the
  conventions, ``typo3_changelog_lookup`` for what moved. An author who has not
  called a tool routes to an answer shape they guess at. A surface the server
  already covers does not need a paragraph in a skill.
* Read the current official documentation of the domain itself. Where the task
  runs through tools this server does not own, a package tool, a registry, a CI
  runner, a test harness, read theirs. Which tools exist, and what each one does
  by default, is exactly the fact that moves after publication.
* Read what the failing run actually did, call by call, rather than what its
  report concluded. The gap the skill takes shape around is in the calls.

None of that research goes into the skill, and
:ref:`the rules <writing-a-skill-the-rules>` still hold. They decide what the
skill **asks**. Which surfaces exist at all, and which of them a tool already
owns. And where the practice moves so fast that only an instruction to check
survives in a file. Written from recall, a skill invents surfaces that do not
exist and misses the one that decides the case.

Review before publishing
------------------------

A skill is not finished when its tests pass. Those hold its shape: the name, the
base, the references, that it keeps no second copy of what a tool owns. No
assertion here can say whether the workflow it describes is the one a maintainer
runs. None can say whether its order matches how the work goes, or whether the
step that decides the outcome is in it at all. The person who asked for the
skill can say all three, and is the only one who can.

So show the skill before the commit: ``SKILL.md`` and every reference, whole,
not a summary. And **ask for feedback by name**. Does this match how the task
really goes, which step is absent, which one is wrong, what does it claim that
is not true here. "Does this look good?" gets agreement, not review. What comes
back goes in before the commit, because the next release of this server does not
correct the copy in somebody else's project.

Nothing holds the file back while that happens. The installer publishes a
directory below ``skills/`` with a ``SKILL.md``. So the review is what the
commit waits for and the commit is what publishes. See
`D-SKL-087 <../../decisions/task-skills/skl-087-every-skill-in-the-directory-is-published.md>`_,
which took out the declaration that used to do it and says what would bring one
back.

.. _writing-a-skill-the-rules:

The rules
---------

* It stands under the name it calls itself, with a description a client can
  route on — ``SkillTest::everySkillIsPublishedUnderTheNameItCallsItself``
* It says which server it needs, in the field the standard has for it —
  ``SkillTest::everySkillSaysWhichServerItNeeds``
* Its front matter carries the standard's fields and nothing else —
  ``SkillTest::everyFrontMatterFieldIsOneTheStandardDefines``
* Every description fits the budget all of them share —
  ``SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn``
* Every directory in ``skills/`` goes out —
  ``SkillTest::everySkillInTheDirectoryIsPublished``
* It starts from the base before it reaches for anything of its own —
  ``SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence``
* It keeps no second copy of what a tool owns —
  ``SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns``
* It routes through the owners of its own facts, in the order it needs them —
  ``SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder``
* A call it names in order not to make it stands as a discharge and routes
  nowhere — ``SkillTest::everyDischargedCallIsWrittenAsOneAndRoutedNowhere``
* Every reference is one hop away and loaded on demand —
  ``SkillTest::everyReferenceIsOneHopAwayAndLoadedOnDemand``
* A paragraph three skills share stops to be a copy —
  ``SkillTest::aParagraphThreeSkillsShareStopsBeingCopied``
* A skill that judges keeps its checklist beside it —
  ``SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem``
* It says what it owns — ``SkillTest::everySkillStatesWhatItOwns``
* A crossing into another skill is an instruction at the moment it happens, and
  never only in the paragraph where the workflow ends —
  ``SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor``
* Every ``typo3://`` resource it names is one this server serves —
  ``SkillTest::everyResourceASkillNamesIsOneTheServerServes``
* The call that reads a guide it names hands it over —
  ``SkillTest::everyGuideASkillNamesIsHandedOverByTheCallThatReadsIt``
* What it sends the session to read out of an answer is a key that answer
  carries —
  ``SkillTest::everyCallTheBaseFixesAnswersWithWhatItSendsTheSessionToRead``

Most of them run over the skills directory rather than over a list, so they hold
a skill added later without a registration anywhere. That is the point, because
the list is the thing a new skill never sees. Three do not: the routing rule and
the checklist rule read their list from ``ROUTING_SKILLS``, and the last one
makes the calls the base fixes.
``SkillTest::theAuthoringContractIsWrittenDownAndNamesWhatHoldsIt`` holds this
table and that set to each other in both directions. A rule here with no test
behind it, or a directory-wide assertion nobody wrote down, fails.

**The name and the description.** The directory name, the ``name:`` in the front
matter and the name every other skill calls it by are one string. The
``description`` is the only part of a skill a client reads before the choice. So
it consists of the words a user brings, the request, the symptom, the files
under change, and never of this server's tool names.
`D-AUD-003 <../../decisions/audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md>`_
is what a wrong one costs. A review prompt whose every criterion the conformance
skill's body would have met did not activate it. All thirty-five calls of that
session went through Bash.

What it does not carry is the **workflow the body owns**. A client reads a
description that lists the steps where the body has not loaded yet. It is then
the whole of what the session has. obra/superpowers measured a description that
says "code review between tasks" produce one review where the skill's flow
specified two. So the description names the task, the sides and where the skill
stops, and every ordered step stays in the body. Patch checkout opened with
"find the change, fetch the patch set, put it on the branch it targets". That
sentence is the body's section headings in one line. Where such a clause carries
a word a user would type, the word stays as a trigger rather than as a step.
What names another skill stays whatever it looks like. A client reads a boundary
before the choice and it is the only thing that can send the task elsewhere.
`D-SKL-024 <../../decisions/task-skills/skl-024-a-description-names-the-task-and-leaves-the-steps-to-the-body.md>`_
is the sweep that cut six of them, and nothing holds this over the directory.
Which clause is a summary is a read of the body, not a property of the file. A
step clause also **narrows** what the description names, which is the stronger
half. That one listed a way to do the job that moves the branch throughout. So a
request that named a git worktree read as another skill's case altogether.
``SkillTest::aWorktreeTaskMatchesTheSkillThatOwnsTheCheckout`` holds the trigger
and the body it promises, on the one measured description.

The clause a description **opens on** narrows the hardest, because everything
after it reads as a case of it. ``typo3-extension-upgrade`` listed the
replacement of what a major removed among its shapes and opened on the carry of
a package to another set of versions. So a bug report whose cause is such a
removal read as a premise the task did not meet. The skill stayed shut for a
whole session
(`D-SKL-061 <../../decisions/task-skills/skl-061-the-upgrade-description-is-reachable-from-a-defect.md>`_).
A premise is rewritten with the body it governs, and
``SkillTest::aDefectInsideTheDeclaredRangeMatchesTheRemovalSkill`` holds that
pair.

It names **every side of the domain the skill owns**. A skill that owns two
sides of one thing says so in the first line rather than in the ninth item of a
list. A domain named by one of its halves sends the other half elsewhere.
"Frontend content elements" for a skill that also owns the backend preview of
the same element is one. It goes to whichever skill happens to carry a word of
it, and the body that covers it never loads. That is the same entry's second
sighting, one task later, and
``SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement`` holds the
measured pair. Which sides a skill owns is not readable off the file, so nothing
holds this over the directory. It is a question the author answers against
``This skill owns …`` and the crossings in the body. A crossing that names one
side while the description names both is the file at odds with itself in
somebody else's project.

And where the body owns **two jobs**, the description names both. A job is not a
side of one thing and not a step. It produces a deliverable somebody can take
and stops, and a user asks for it in words of its own.
``typo3-core-issue-triage`` opens on a backlog whose candidates it hands over
before anybody chooses an issue, and its description named only the issue. So a
session that searched that backlog six times opened nothing
(`D-SKL-076 <../../decisions/task-skills/skl-076-a-description-names-both-jobs-a-skills-body-owns.md>`_,
held by ``SkillTest::aBacklogSearchMatchesTheSkillThatOwnsTheCandidates``).
Three trims took the clause out in stages, and each read it as a summary of the
body. That is what this rule asks for and what a job is the exception to. Which
jobs a body owns is a read of the body, so nothing holds this over the directory
either. ``typo3-extension-health`` is the other case, and the report it hands
over before it changes a file stands in its description already
(`D-SKL-064 <../../decisions/task-skills/skl-064-the-audit-and-the-work-that-answers-it-are-one-skill.md>`_).

**The budget every description shares.** A client reads all of the descriptions
in one listing against one character budget. In Claude Code that is one percent
of the context window at three or four bytes to the token. So 6000 characters on
a 200k session and 30000 on a 1M one. Over that it drops whole descriptions
rather than shortens them, least-used first, which is every skill of this
server's on a fresh install. The dropped skill stands in the list by its name
alone and nothing tells the session it happened. So a description is paid for by
the other skills and not by its own, and a thirteenth skill costs the twelve.
`D-SKL-026 <../../decisions/task-skills/skl-026-the-descriptions-are-written-to-the-listing-budget-they-share.md>`_
is where that arithmetic came off the client, what the twelve cost after it, and
what the client's own bundled skills leave over.

The commit that adds a skill answers what the budget asks, because that is the
commit the listing grows in
(`D-SKL-054 <../../decisions/task-skills/skl-054-the-listing-budget-is-what-a-client-reads.md>`_).
Where the room is not there, it is where a description shrinks or a workflow
merges into one that already exists. That is a question about which descriptions
are worth their room, and one nobody can answer with only the newest in hand.

**Starting from the base.** The skill links ``references/base.md`` and then
states what it *adds* to it. It never restates a step the base already fixes.
Five hand-written copies of one order is what the base replaced, and the copy
that drifts is always the one already out in somebody's project.

**Nothing a tool owns.** No TYPO3 version number, no API signature, no
dependency constraint, no backend markup, and no command nobody asked the
checkout for. A version in a permanently loaded instruction is the one fact
nobody can ask again when the installation turns out to be a different one. No
answer built on it ever says where it came from. What that leaves is the version
a step states as the **boundary of a property**, which nobody asks the
installation for. Step 3 of ``typo3-development-installation`` says from which
major the setup command reports an option as disabled. Without a bound that
sentence sends a caller below it in search of an option that is not there
(`D-SKL-057 <../../decisions/task-skills/skl-057-a-commands-options-are-read-from-the-installed-console.md>`_).
The guard reads a version out of ``TYPO3 <n>``, so which of the two a bare
number is stays the author's, like a layout key. The same holds for a package
name. It is one word in a published file that no release of this server can
correct. So it stands where a task reads it once, a reference, rather than where
every task carries it. That one and an environment variable are the two kinds a
test can find in a body. ``SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns``
reads ``SKILL.md`` alone for them
(`D-SKL-052 <../../decisions/task-skills/skl-052-the-injected-size-of-a-skill-is-what-the-retention-rule-leaves.md>`_).
A layout key and a command option have no such shape and stay the author's.

**Which server it needs.** ``skills/`` is also read by whoever copies it, and a
copied skill has no server. ``references/base.md`` comes at publication. So the
first instruction of that copy is a link to nothing, and every lookup under it
is a tool the session does not have. The guard in the base does not reach it.
That one serves a session whose tools do not answer, not one whose base never
arrived. So the skill says it in ``compatibility``, the field the standard keeps
for an environment requirement. One line, and the same line in every skill,
because it is a fact about this package rather than about a workflow. That is
also the one package name a skill may carry. The package-name rule is about what
a *task* reaches for, and this is what the reader has to install before any of
it works. No version goes in it. The front matter it sits in has a form a reader
of the standard can parse. An unquoted ``: `` in a description breaks that form.
Three of them broke the whole block for everything but this repository's own
patterns.

**Routing, in order.** What a skill adds to the base is a short list of tools in
the order it needs them. The ``ROUTING_SKILLS`` map of ``SkillTest`` records it.
The four calls the base already fixes are deliberately absent from that list.

**A call named in order not to make it is a discharge.** Where a step would
prescribe a tool and something the session already has in hand answers it, the
step says so in one construct. The tool's own name, then ``is discharged by``,
then what answers it instead. ``DISCHARGED_TOOLS`` records the tool rather than
the routings. The routing assertion finds the tool's name in the body. So any
other wording of "you already have this" satisfies it while it tells the caller
the opposite. The construct is what the assertion skips and what keeps the two
lists apart
(`D-SKL-055 <../../decisions/task-skills/skl-055-a-call-named-in-order-not-to-make-it-is-a-discharge.md>`_).
Discharging and routing are exclusive: a body that names the tool a second time
is routing to what it has just discharged.

**The page a step expects read whole.** Where a step describes a whole procedure
and routes to a search for it, name the call that reads the page at that step.
That is ``typo3_rule_lookup`` with the ``documentId`` the procedure exists whole
as. A search answers the section the query matched. So a session that only
searches reads a procedure in pieces and never learns it is one. A core session
did that with the Gerrit push page
(`D-AUD-007 <../../decisions/audience/aud-007-the-prose-documents-are-named-where-a-session-already-looks.md>`_).
The ``typo3://guides`` address may stand beside the call and is not the
handover. It is delivery to a client that renders a resource list. The session
this last came off had none, held the ids anyway and searched
(`D-ANS-070 <../../decisions/answers/ans-070-a-document-is-handed-over-by-the-call-that-reads-it.md>`_).
Name it once, where the need is, and not at every mention. This is not a licence
to restate the page — the call is the routing, and what is behind it stays
there.

**References.** Anything a task reads once, a checklist, a rubric, one layer's
implementation guide, is a file below ``references/``. ``SKILL.md`` names it
together with when to read it. One hop: a reference that loads another reference
is a body whose size the skill no longer decides.

**Judgment keeps a checklist; construction does not.** A skill that assesses
something needs a rubric and surfaces beside it. A skill that builds something
needs registries, and registries are tools.

**What it owns, and where it stops.** Every skill says ``This skill owns …``,
and where its work runs into another skill's, the crossing is explicit. Name the
verified stop point and stop before the first edit to the other owner's files.
Activate that owner and carry across only the scope and verified behaviour it
needs
(`R-SKL-003 <../../requirements/task-skills/skl-003-crossing-into-another-skills-work-is-an-explicit-transition.md>`_).

**And it stands where it happens.** That paragraph is where a reader learns the
boundary, and it is the last thing in the file. A session reads it at the moment
it leaves a workflow, which is the moment of least appetite for another one. So
the crossing itself stands as a step at the point it occurs. It tells the
session to invoke the successor by name and says what crosses over
(`R-SKL-018 <../../requirements/task-skills/skl-018-a-skill-that-hands-over-tells-the-session-to-invoke-the-next-one.md>`_).
An imperative alone is not it. One skill closed on ``Activate`` and three
skills' names, a session read that sentence and crossed none of them
(`D-SKL-053 <../../decisions/task-skills/skl-053-an-absence-in-the-extension-answer-names-the-skill-that-owns-it.md>`_).
Where the moment is something the reader says, the step names the sentence that
fires it and the sentence that does not. That half is the author's, like the
sides a description names.

**A crossing the reader's sentence fires names the act that begins the work
too.** A sentence needs recognition. A core review quoted this crossing and then
edited the files it had just reviewed without the crossing
(`D-SKL-077 <../../decisions/task-skills/skl-077-the-crossing-out-of-a-review-is-recognised-on-the-first-edit-meant-to-survive.md>`_,
held by ``SkillTest::theCrossingOutOfAReviewNamesTheEditThatBeginsTheRework``).
What the skill permits and is not that act stands beside it: a scratch probe,
put back and with no diff left. The step points at the section that draws that
boundary rather than copies it.

**A crossing that restarts the order names the calls whose arguments changed.**
The successor opens on the base, and a session that has just walked it reads
that step as answered. A core review crossed into the patch workflow as
instructed. It then wrote the patch without the deprecation sweep and without
hints on the paths it edited
(`D-SKL-072 <../../decisions/task-skills/skl-072-a-workflow-handover-names-the-calls-the-next-order-restarts-with.md>`_,
held by
``SkillTest::theCrossingOutOfAReviewNamesTheCallsTheOrderRestartsWith``). What
the paragraph names is the calls the crossing changes the answer to. It names
them as calls rather than as the order to restart, and three of them rather than
a checklist. Which crossings restart an order is the author's, like the sides a
description names.

**A workflow that ends in public stops once more before it gets there.** The
last step can publish: a tracker entry, a pushed change, a release. There the
skill asks before that step whether what it found is a security defect. It stops
there when it is. The finding stands and the publication step stays untaken.
Where the report goes instead is a lookup rather than a line in the file
(`R-SKL-020 <../../requirements/task-skills/skl-020-a-workflow-that-ends-in-public-stops-when-the-finding-is-a-vulnerability.md>`_,
held by ``SkillTest::aWorkflowThatEndsInPublicationStopsAtAVulnerability``). The
question goes to every finding rather than to the ones that read as alarms.
Which skills end in public is not readable off a file. So this is the author's,
like the sides a description names.

**A skill whose product is a report says what form the report has.** A section
that fixes the severity bands, what each finding owes and the surfaces it closes
on specifies a document. That is not a chat reply. It is what makes that
document long. So it says the report is markdown the reader can copy and that
the answer is where it goes
(`R-SKL-023 <../../requirements/task-skills/skl-023-a-skill-whose-product-is-a-report-says-the-report-is-copyable-markdown.md>`_,
held by ``SkillTest::aReportIsCopyableMarkdownAndTheAnswerIsWhereItGoes``). A
path is the caller's to ask for, and where the caller asks for one it is outside
the checkout the skill assessed. Which skills produce a report is the author's
too, read off the bodies.

Publishing it
-------------

Publishing is deleting the ``metadata`` declaration above, and
``Installer::skills()`` is the directory minus what still carries it. There is
no list to add the name to: one existed, and a list beside the file is a second
place the same fact lives.

What that one edit turns on. ``bin/typo3-dev-companion install`` copies the
skill into every client's own skills directory, the server serves it as a
``typo3://skill`` resource, and ``knowledge/task-intents.json`` may name it.
Nothing this server answers with may name a skill before that. A route into one
nobody has installed is worse than none, because the caller cannot tell the two
apart. ``SkillTest::everySkillNamedInKnowledgeIsPublished`` holds every name
there to what the installer publishes. The intent that routes to it comes in the
same commit and never before it
(`D-SKL-013 <../../decisions/task-skills/skl-013-the-guide-names-the-skill-that-owns-the-task.md>`_).

Two things the skill does not carry itself come at publication. The installer
copies ``skills/base.md`` into the new directory as ``references/base.md``, one
copy per skill. Each of them lands in another project alone, and a link out of
its own directory would resolve here and nowhere a session reads it. And
``knowledge/server-scope.json`` has to name the workflow among what the server
covers, or ``ScopeTest::everyPublishedSkillIsAnnouncedByTheScope`` fails. A
skill served to a client that the scope does not announce is one nothing tells
the caller about.

Then run the installer in the checkout that plays the environment the skill is
for, before any run that is meant to measure it. The published skills are a copy
and nothing reports that they are older than the server.
`todo/reference/ <../../todo/reference/>`_ says which checkout plays which
environment on this machine and how to reach the installer there.

What nothing holds
------------------

Three of this page's steps are the author's and nothing reads them off a file.
That a domain earned a skill at all. That research on the practice came before
the file. That somebody saw the skill and heard the questions. Each leaves the
same trace as its absence. A skill written from recall has exactly the shape of
one written from the documentation. It is wrong in places no assertion knows to
look. They stand here because that is all anybody can do for them. The author
who skips them is usually the one who has not read this page.

And that a session **does** what a skill says. Most of the table comes off the
file, which makes it a proxy. The wording is present and a reorganisation can
leave it present while the behaviour goes. One gap narrower than behaviour is
checkable and has its check. A skill does not only name a tool, it says what to
read out of the answer. For the four calls the base fixes a test asserts those
keys on the answer the tool really returns
(`D-SKL-025 <../../decisions/task-skills/skl-025-a-routed-tool-is-called-and-held-to-what-it-reads.md>`_).
What that leaves is prose going stale against a tool that kept every key.
`D-EVI-002 <../../decisions/evidence/evi-002-a-skill-crossing-is-read-rather-than-run.md>`_
accepts that proxy for the skill crossing and says why no forward run will
replace it. Everywhere else, what measures the behaviour is a case in
`scenarios/contracts/ <../../scenarios/contracts/readme.md>`_ or an open review
in `scenarios/forward/ <../../scenarios/forward/readme.md>`_. A forward run
grades the answer a session produced, never the file it came out of. That is why
the author contract is the half of a skill that has to stand in a file instead.
