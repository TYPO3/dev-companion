:navigation-title: Writing a skill

Writing a task skill
====================

This page covers how to write a task skill, and the test that holds each rule.
`AGENTS.md <../../AGENTS.md>`_ says what the published skills are.
`skills/base.md <../../skills/base.md>`_ gives the order every skill starts in,
in one file rather than a paragraph in each skill.

The steps at a glance:

1. Show that the domain earns a skill.
2. Research the current practice before the first line.
3. Write the skill to the rules.
4. Show the whole skill to the person who asked for it, and ask for feedback by
   name.
5. Commit it, which publishes it, and run the installer where the skill is
   measured.

The rules are narrow for one reason: a skill is a copy in somebody else's
project, not a document this repository keeps. The installer writes it there.
Nothing there reports that it has fallen behind the server it came from, and no
release of this server corrects it. It states everything permanently, in a
context the reader pays for by the token, about an installation it cannot see.

Earning a skill
---------------

A domain earns a skill when a scenario or a recorded session shows that the
existing tools and skills **fail at the task**. The size of a subject alone
earns nothing. Release, static analysis, performance and security have each
looked like a skill at some point. A case in
`scenarios/ <../../scenarios/readme.md>`_ or a run in
`scenarios/runs/ <../../scenarios/runs/>`_ settles the question. In it, a
session used the existing workflow and came up short.

A new skill also needs a **baseline run**. That is the case prompt, run in an
environment without the skills. It is recorded beside the run it is compared
with. That run turns "the skill helped" into a measurement, and only a new skill
needs one. An edit rests on its author's word. The cost would fall on every
change, and nobody here has seen what such a run would catch
(`D-SKL-035 <../../decisions/task-skills/skl-035-a-new-skill-is-measured-against-a-run-without-it.md>`_).

What such a run shows is almost always smaller than the domain. It is an order
nobody keeps, or a step that runs only when a finding happens to walk into it.
Or it is a boundary two skills both claim. The skill takes shape around that and
nothing else. "Less is more" is an instruction here, and every session in
another project loads it before it does anything.

Research first
--------------

Write a skill from the current state of the practice, never from recall. The
research costs one session's reading. Recall costs a file in somebody else's
project that states a practice permanently, as it was when its author last saw
it. Before the first line:

* Ask this server what it already answers about the domain, with the tools the
  skill will route to. ``typo3_documentation_lookup`` gives the official
  documentation at the versions in play, ``typo3_hint_lookup`` the conventions,
  ``typo3_changelog_lookup`` what moved. An author who has not called a tool
  routes to an answer shape they guess at. A surface the server already covers
  needs no paragraph in a skill.

* Read the current official documentation of the domain itself. Where the task
  runs through tools outside this server, read theirs: a package tool, a
  registry, a CI runner, a test harness. Which tools exist, and what each does
  by default, is exactly the fact that moves after publication.

* Read what the failed run did, call by call, rather than what its report
  concluded. The gap the skill takes shape around is in the calls.

None of that research goes into the skill, and
:ref:`the rules <writing-a-skill-the-rules>` still hold. The research decides
what the skill **asks**. It shows which surfaces exist at all, and which of them
a tool already answers. It also shows where the practice moves so fast that only
an instruction to check survives in a file. A skill from recall invents surfaces
that do not exist and misses the one that decides the case.

Review before publishing
------------------------

A skill needs a review after its tests pass. The tests hold its shape: the name,
the base, the references, and that it keeps no second copy of what a tool
answers. No assertion can say whether the workflow it describes is the one a
maintainer runs. None can say whether its order matches how the work goes, or
whether the step that decides the outcome is in it at all. Only the person who
asked for the skill can say all three.

So show the skill before the commit: ``SKILL.md`` and every reference, whole,
not a summary. **Ask for feedback by name**:

* Does this match how the task really goes?
* Which step is absent?
* Which step is wrong?
* What does it claim that is not true here?

"Does this look good?" gets agreement, not review. Put what comes back in before
the commit, because the next release of this server does not correct the copy in
somebody else's project.

Nothing holds the file back during the review. The installer publishes every
directory below ``skills/`` with a ``SKILL.md``. So the commit waits for the
review, and the commit publishes.
`D-SKL-087 <../../decisions/task-skills/skl-087-every-skill-in-the-directory-is-published.md>`_
took out the declaration that once held a skill back, and it says what would
bring one back.

.. _writing-a-skill-the-rules:

The rules
---------

* It sits under the name it calls itself, with a description a client can route
  on — ``SkillTest::everySkillIsPublishedUnderTheNameItCallsItself``
* It says which server it needs, in the field the standard has for it —
  ``SkillTest::everySkillSaysWhichServerItNeeds``
* Its front matter has the fields of the standard and nothing else —
  ``SkillTest::everyFrontMatterFieldIsOneTheStandardDefines``
* Every description fits the budget all of them share —
  ``SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn``
* Every directory in ``skills/`` goes out —
  ``SkillTest::everySkillInTheDirectoryIsPublished``
* It starts from the base before it reaches for anything of its own —
  ``SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence``
* It keeps no second copy of what a tool answers —
  ``SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns``
* It routes to the tools that answer its facts, in the order it needs them —
  ``SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder``
* A call it names so as not to make it takes the form of a discharge and routes
  nowhere — ``SkillTest::everyDischargedCallIsWrittenAsOneAndRoutedNowhere``
* Every reference is one hop away, and the skill loads it on demand —
  ``SkillTest::everyReferenceIsOneHopAwayAndLoadedOnDemand``
* A paragraph three skills share is no longer copied —
  ``SkillTest::aParagraphThreeSkillsShareStopsBeingCopied``
* A skill that judges keeps its checklist beside it —
  ``SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem``
* It says what it covers — ``SkillTest::everySkillSaysWhatItCovers``
* A crossing into another skill is an instruction at the moment it happens, and
  never only in the paragraph where the workflow ends —
  ``SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor``
* Every ``typo3://`` resource it names is one this server serves —
  ``SkillTest::everyResourceASkillNamesIsOneTheServerServes``
* The call that reads a guide it names delivers that guide —
  ``SkillTest::everyGuideASkillNamesIsHandedOverByTheCallThatReadsIt``
* What it sends the session to read out of an answer is a key that answer has —
  ``SkillTest::everyCallTheBaseFixesAnswersWithWhatItSendsTheSessionToRead``

Most of the rules run over the skills directory rather than over a list. So they
hold a skill added later, with no registration anywhere. That matters, because a
new skill never sees the list. Three do not: the routing rule and the checklist
rule read their list from ``ROUTING_SKILLS``, and the last one makes the calls
the base fixes.

``SkillTest::theAuthoringContractIsWrittenDownAndNamesWhatHoldsIt`` holds this
list and that set to each other in both directions. It fails on a rule here with
no test behind it, and on a directory-wide assertion nobody wrote down.

The name and the description
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The directory name, the ``name:`` in the front matter and the name every other
skill calls it by are one string. A client reads the ``description`` alone
before it chooses a skill. So the description consists of the words a user
brings: the request, the symptom, the files under change. It never contains the
tool names of this server.

`D-AUD-003 <../../decisions/audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md>`_
shows what a wrong description costs. A review prompt did not activate the
conformance skill, though the body of that skill would have met every criterion
of the prompt. All thirty-five calls of that session went through Bash.

The description leaves out the **workflow of the body**. Before the body loads,
a client reads a description that lists the steps, and that list is then all the
session has. obra/superpowers measured a description that says "code review
between tasks". It produced one review where the flow of the skill specified
two. So the description names the task, the sides and where the skill stops, and
every ordered step stays in the body.

Patch checkout opened with "find the change, fetch the patch set, put it on the
branch it targets". That sentence is the section headings of the body in one
line. Where such a clause has a word a user would type, the word stays as a
trigger rather than as a step. A clause that names another skill stays, whatever
it looks like. A client reads a boundary before the choice, and only a boundary
can send the task elsewhere.

`D-SKL-024 <../../decisions/task-skills/skl-024-a-description-names-the-task-and-leaves-the-steps-to-the-body.md>`_
is the sweep that cut six of them, and nothing holds this rule over the
directory. Whether a clause is a summary depends on a read of the body, not on a
property of the file.

A step clause also **narrows** what the description names, which is the stronger
half. The patch checkout description listed a way to do the job that moves the
branch throughout. So a request that named a git worktree read as the case of
another skill altogether.
``SkillTest::aWorktreeTaskMatchesTheSkillThatOwnsTheCheckout`` holds the trigger
and the body it promises, on the one measured description.

The clause a description **opens on** narrows the hardest, because everything
after it reads as a case of it. ``typo3-extension-upgrade`` opened on the move
of a package to another set of versions. It listed the replacement of what a
major removed only among its shapes. So a bug report caused by such a removal
read as a premise the task did not meet. The skill stayed shut for a whole
session
(`D-SKL-061 <../../decisions/task-skills/skl-061-the-upgrade-description-is-reachable-from-a-defect.md>`_).
Rewrite a premise together with the body it governs.
``SkillTest::aDefectInsideTheDeclaredRangeMatchesTheRemovalSkill`` holds that
pair.

A description names **every side of the domain the skill covers**. A skill that
covers two sides of one thing says so in the first line, not in the ninth item
of a list. A domain named by one of its halves sends the other half elsewhere.
"Frontend content elements" for a skill that also covers the backend preview of
the same element is one such case. That task goes to whichever skill has a word
of it, and the body that covers it never loads.

That is the second sighting of the same entry, one task later.
``SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement`` holds the
measured pair. Nobody can read off the file which sides a skill covers, so
nothing holds this over the directory. The author answers it against
``This skill covers …`` and the crossings in the body. A crossing that names one
side, while the description names both, sets the file against itself in somebody
else's project.

Where the body has **two jobs**, the description names both. A job is neither a
side of one thing nor a step. It produces a deliverable somebody can take, and
then stops. A user asks for it in words of its own.

``typo3-core-issue-triage`` opens on a backlog and gives its candidates before
anybody chooses an issue. Its description named only the issue. So a session
that searched that backlog six times opened nothing
(`D-SKL-076 <../../decisions/task-skills/skl-076-a-description-names-both-jobs-a-skills-body-owns.md>`_,
held by ``SkillTest::aBacklogSearchMatchesTheSkillThatOwnsTheCandidates``).
Three trims took the clause out in stages, and each read it as a summary of the
body. A summary is what this rule removes, and a job is the exception to it.

Which jobs a body has is a read of the body, so nothing holds this over the
directory either. ``typo3-extension-health`` is the other case. Its description
already names the report it gives before it changes a file
(`D-SKL-064 <../../decisions/task-skills/skl-064-the-audit-and-the-work-that-answers-it-are-one-skill.md>`_).

The description budget
~~~~~~~~~~~~~~~~~~~~~~

**The budget every description shares.** A client reads all descriptions in one
listing against one character budget. In Claude Code that is one percent of the
context window, at three or four bytes to the token. So it is 6000 characters on
a 200k session and 30000 on a 1M one. Over the budget, the client drops whole
descriptions rather than shortens them, least-used first. On a fresh install
that is every skill of this server.

The dropped skill stays in the list by its name alone, and nothing tells the
session. So the other skills pay for a description, not the skill itself, and a
thirteenth skill costs the twelve.
`D-SKL-026 <../../decisions/task-skills/skl-026-the-descriptions-are-written-to-the-listing-budget-they-share.md>`_
says where in the client that arithmetic came from, and what the twelve cost
after it. It also says what the bundled skills of the client leave over.

The commit that adds a skill answers what the budget asks, because the listing
grows in that commit
(`D-SKL-054 <../../decisions/task-skills/skl-054-the-listing-budget-is-what-a-client-reads.md>`_).
Where the room is missing, that commit is where a description shrinks or a
workflow merges into an existing one. That question is about which descriptions
are worth their room. Nobody can answer it with only the newest description in
hand.

The base and the tools
~~~~~~~~~~~~~~~~~~~~~~

**Starting from the base.** The skill links ``references/base.md`` and then
states what it *adds* to it. It never restates a step the base already fixes.
The base replaced five hand-written copies of one order. The copy that drifts is
always the one already in somebody's project.

**Nothing a tool answers.** No TYPO3 version number, no API signature, no
dependency constraint, no backend markup, and no command nobody asked the
checkout for. A version in an instruction that stays loaded is the one fact
nobody can ask again when the installation turns out to be different. No answer
built on it says where it came from.

What remains is a version that a step states as the **boundary of a property**,
which nobody asks the installation for. Step 3 of
``typo3-development-installation`` says from which major the setup command
reports an option as disabled. Without the bound, that sentence sends a caller
below it to look for an option that is not there
(`D-SKL-057 <../../decisions/task-skills/skl-057-a-commands-options-are-read-from-the-installed-console.md>`_).
The guard reads a version out of ``TYPO3 <n>``. So the author decides which of
the two a bare number is, as with a layout key.

The same holds for a package name. It is one word in a published file that no
release of this server can correct. So it goes where a task reads it once, in a
reference, rather than where every task meets it. A package name and an
environment variable are the two kinds a test can find in a body.
``SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns`` reads ``SKILL.md`` alone
for them
(`D-SKL-052 <../../decisions/task-skills/skl-052-the-injected-size-of-a-skill-is-what-the-retention-rule-leaves.md>`_).
A layout key and a command option have no such shape, and the author decides
them.

**Which server it needs.** Whoever copies ``skills/`` reads it too, and a copied
skill has no server. ``references/base.md`` comes only at publication. So the
first instruction of a copy links to nothing, and every lookup under it is a
tool the session does not have. The guard in the base does not reach that copy.
The guard serves a session whose tools do not answer, not one whose base never
arrived.

So the skill says it in ``compatibility``, the field the standard keeps for an
environment requirement. It is one line, the same in every skill, because it is
a fact about this package rather than about a workflow. It is also the one
package name a skill may have. The package-name rule is about what a *task*
reaches for. This line is what the reader must install before any of it works,
and no version goes in it.

The front matter has a form a reader of the standard can parse. An unquoted
``: `` in a description breaks that form. Three of them broke the whole block
for every parser except the patterns of this repository.

Routing and discharge
~~~~~~~~~~~~~~~~~~~~~

**Routing, in order.** What a skill adds to the base is a short list of tools,
in the order it needs them. The ``ROUTING_SKILLS`` map of ``SkillTest`` records
it. The four calls the base already fixes are absent from that list on purpose.

**A call named in order not to make it is a discharge.** Where a step would
prescribe a tool and something the session already has answers it, the step says
so in one construct. That is the name of the tool, then ``is discharged by``,
then what answers it instead. ``DISCHARGED_TOOLS`` records the tool rather than
the routings. The routing assertion finds the name of the tool in the body. So
any other wording of "you already have this" satisfies the assertion while it
tells the caller the opposite.

The assertion skips the construct, and the construct keeps the two lists apart
(`D-SKL-055 <../../decisions/task-skills/skl-055-a-call-named-in-order-not-to-make-it-is-a-discharge.md>`_).
Discharge and routing exclude each other. A body that names the tool a second
time routes to what it has just discharged.

**The page a step expects read whole.** Where a step describes a whole procedure
and routes to a search for it, name the call that reads the page at that step.
That call is ``typo3_rule_lookup`` with the ``documentId`` of the whole
procedure. A search answers with the section the query matched. So a session
that only searches reads a procedure in pieces and never learns it is one. A
core session did that with the Gerrit push page
(`D-AUD-007 <../../decisions/audience/aud-007-the-prose-documents-are-named-where-a-session-already-looks.md>`_).

The ``typo3://guides`` address may stand beside the call, but the call delivers
the page. The address delivers it only to a client that renders a resource list.
The session this rule came from had no such list, and it searched although it
had the ids
(`D-ANS-070 <../../decisions/answers/ans-070-a-document-is-handed-over-by-the-call-that-reads-it.md>`_).
Name the call once, where the need is, not at every mention. This rule is no
licence to restate the page: the call is the routing, and the content stays
behind it.

References and checklists
~~~~~~~~~~~~~~~~~~~~~~~~~

**References.** A file below ``references/`` holds anything a task reads once: a
checklist, a rubric, the implementation guide of one layer. ``SKILL.md`` names
the file together with when to read it. A reference is one hop away. A reference
that loads another reference makes a body whose size the skill no longer
decides.

**Judgment keeps a checklist; construction does not.** A skill that assesses
something needs a rubric and surfaces beside it. A skill that builds something
needs registries, and registries are tools.

Scope and crossings
~~~~~~~~~~~~~~~~~~~

**What it covers, and where it stops.** Every skill says ``This skill covers …``
at its start. Where its work runs into the work of another skill, the crossing
is explicit. Name the verified stop point, and stop before the first edit to the
files the other skill covers. Invoke that skill, and give it only the scope and
the verified behaviour it needs
(`R-SKL-003 <../../requirements/task-skills/skl-003-crossing-into-another-skills-work-is-an-explicit-transition.md>`_).

**And the crossing sits where it happens.** A reader learns the boundary from
that paragraph, and it is the last thing in the file. A session reads it at the
moment it leaves a workflow, the moment of least appetite for another one. So
the crossing itself is a step at the point where it occurs. That step tells the
session to invoke the successor by name, and says what crosses over
(`R-SKL-018 <../../requirements/task-skills/skl-018-a-skill-that-hands-over-tells-the-session-to-invoke-the-next-one.md>`_).

An imperative alone is not enough. One skill closed on ``Activate`` and the
names of three skills. A session read that sentence and crossed into none of
them
(`D-SKL-053 <../../decisions/task-skills/skl-053-an-absence-in-the-extension-answer-names-the-skill-that-owns-it.md>`_).
Where the moment is something the reader says, the step names the sentence that
fires the crossing and the sentence that does not. The author decides that half,
as with the sides a description names.

**A crossing the reader's sentence fires names the act that begins the work
too.** A sentence works only where the session recognises it. A core review
quoted this crossing and then edited the files it had just reviewed, without the
crossing
(`D-SKL-077 <../../decisions/task-skills/skl-077-the-crossing-out-of-a-review-is-recognised-on-the-first-edit-meant-to-survive.md>`_,
held by ``SkillTest::theCrossingOutOfAReviewNamesTheEditThatBeginsTheRework``).
Beside that act the step names what the skill permits that is not the act: a
scratch probe, put back, with no diff left. The step points at the section that
draws that boundary rather than copies it.

**A crossing that restarts the order names the calls whose arguments changed.**
The successor opens on the base, and a session that has just walked the base
reads that step as answered. A core review crossed into the patch workflow as
instructed. It then wrote the patch without the deprecation sweep and without
hints on the paths it edited
(`D-SKL-072 <../../decisions/task-skills/skl-072-a-workflow-handover-names-the-calls-the-next-order-restarts-with.md>`_,
held by
``SkillTest::theCrossingOutOfAReviewNamesTheCallsTheOrderRestartsWith``). The
paragraph names the calls whose answer the crossing changes. It names them as
calls rather than as the order to restart, and three of them rather than a
checklist. The author decides which crossings restart an order, as with the
sides a description names.

Public endings and reports
~~~~~~~~~~~~~~~~~~~~~~~~~~

**A workflow that ends in public stops once more before it gets there.** The
last step can publish: a tracker entry, a pushed change, a release. Before that
step, the skill asks whether what it found is a security defect, and stops there
when it is. The finding stays, and the session does not take the publication
step. A lookup, not a line in the file, says where the report goes instead
(`R-SKL-020 <../../requirements/task-skills/skl-020-a-workflow-that-ends-in-public-stops-when-the-finding-is-a-vulnerability.md>`_,
held by ``SkillTest::aWorkflowThatEndsInPublicationStopsAtAVulnerability``). The
question goes to every finding, not only to the ones that read as alarms.

Nobody can read off a file which skills end in public. So the author decides
this, as with the sides a description names.

**A skill whose product is a report says what form the report has.** A section
that fixes the severity bands, what each finding must state and the surfaces it
closes on specifies a document, not a chat reply. That section is what makes the
document long. So the skill says that the report is markdown the reader can
copy. It also says that the answer is where the report goes
(`R-SKL-023 <../../requirements/task-skills/skl-023-a-skill-whose-product-is-a-report-says-the-report-is-copyable-markdown.md>`_,
held by ``SkillTest::aReportIsCopyableMarkdownAndTheAnswerIsWhereItGoes``). The
report sits in one fenced block, because a client that renders the answer copies
the rendered text.

The caller decides whether to ask for a path. Where the caller asks for one, the
path is outside the checkout the skill assessed. The author decides which skills
produce a report, from the bodies.

Publishing it
-------------

To publish a skill, delete the ``metadata`` declaration above.
``Installer::skills()`` is the directory minus the skills that still have it.
There is no list to add the name to. A list existed once, and a list beside the
file is a second place for the same fact.

That one edit turns on three things:

* ``bin/typo3-dev-companion install`` copies the skill into the skills directory
  of every client.
* The server serves it as a ``typo3://skill`` resource.
* ``knowledge/task-intents.json`` may name it.

Nothing this server answers with may name a skill before that. A route into a
skill nobody has installed is worse than none, because the caller cannot tell
the two apart. ``SkillTest::everySkillNamedInKnowledgeIsPublished`` holds every
name there to what the installer publishes. The intent that routes to the skill
comes in the same commit, never before it
(`D-SKL-013 <../../decisions/task-skills/skl-013-the-guide-names-the-skill-that-owns-the-task.md>`_).

Two things come at publication that the skill does not have itself:

* The installer copies ``skills/base.md`` into the new directory as
  ``references/base.md``, one copy per skill. Each skill lands in another
  project alone. A link out of its own directory would resolve here and nowhere
  a session reads it.

* ``knowledge/server-scope.json`` must name the workflow among what the server
  covers, or ``ScopeTest::everyPublishedSkillIsAnnouncedByTheScope`` fails.
  Nothing tells the caller about a skill that the scope does not announce.

Then run the installer in the checkout that plays the environment the skill is
for, before any run meant to measure it. The published skills are a copy, and
nothing reports that they are older than the server.
`todo/reference/ <../../todo/reference/>`_ says which checkout plays which
environment on this machine, and how to reach the installer there.

What nothing holds
------------------

Three steps of this page are the author's, and nothing reads them off a file:

* that a domain earned a skill at all,
* that research on the practice came before the file,
* that somebody saw the skill and the author heard the questions.

Each leaves the same trace as its absence. A skill from recall has exactly the
shape of one from the documentation. It is wrong in places no assertion knows to
look. This page states them because that is all anybody can do for them. The
author who skips them is usually the one who has not read this page.

Nothing holds either that a session **does** what a skill says. Most of the list
reads the file, which makes it a proxy. The wording can stay present through a
reorganisation while the behaviour goes. One gap narrower than behaviour is
checkable and has its check. A skill names a tool and also says what to read out
of the answer. For the four calls the base fixes, a test asserts those keys on
the answer the tool really returns
(`D-SKL-025 <../../decisions/task-skills/skl-025-a-routed-tool-is-called-and-held-to-what-it-reads.md>`_).

What remains is prose that goes stale against a tool that kept every key.
`D-EVI-002 <../../decisions/evidence/evi-002-a-skill-crossing-is-read-rather-than-run.md>`_
accepts that proxy for the skill crossing and says why no forward run will
replace it. Everywhere else, a case in
`scenarios/contracts/ <../../scenarios/contracts/readme.md>`_ or an open review
in `scenarios/forward/ <../../scenarios/forward/readme.md>`_ measures the
behaviour. A forward run grades the answer a session produced, never the file it
came from. So the author contract is the half of a skill that must live in a
file instead.
