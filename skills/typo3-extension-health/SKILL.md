---
name: typo3-extension-health
description: 'Review a TYPO3 project, sitepackage or extension against its checkout and active installation and put it right — "look over my repository and fix it": TCA, services, backend modules, content elements, site sets, TypoScript, Fluid, labels, icons, security boundaries and deprecated APIs. The audit reports first; nothing is changed before the list is agreed.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Health

This skill covers the state of a whole package. That is what is wrong with it,
what each finding is worth, and which skill takes it on. It also covers the work
on the agreed list until the list is empty. A request for either half starts
here. It stops at a change in the area of another skill. It also stops at a
single change somebody proposes against the package.

**Step 7 is the gate between the two halves.** Edit nothing before it, whatever
the request asked for. A request for a review ends with the report. A request
for changes passes through the same report on its way to them.

Keep this skill as routing, assessment and order. It holds no TYPO3 fact that
depends on a version.

## Steps at a glance

1. Work through the base.
2. Read the checklist.
3. Write the surface list into your reply.
4. Narrow the reading to the surfaces the request names, never the list.

Then ask for the conventions of each surface, read the checkout and write the
report. A request for a review ends there.

5. Write one item per finding.
6. Find what the branches already hold for each item.
7. Show the list whole, and wait for agreement.
8. Keep the list in the session.
9. Group the items by skill, and run the deprecation sweep.
10. Hand each group to the skill that covers it.
11. Work an item no skill covers only where a check proves the change.
12. Ask where the change lands.
13. Commit per item.
14. Run the audit again.
15. Report what remains.

## Establish scope and evidence

1. Work through [references/base.md](references/base.md) first. It fixes the
   order every task starts in. In an assessment that order matters most: a rule
   you fetch after the reading only confirms a verdict.

2. Read [references/checklist.md](references/checklist.md). It gives the audit
   surfaces, the finding gate and the severity rubric.

3. Write the surface list down before you open a single file. Write it into your
   reply, one line per surface, so the reader sees what you will cover before
   you read. A client with a task list takes the items there too.

   - `typo3_task_guide` with `changeType="audit"` returns the surfaces as
     `surfaces`. That answer is the list to write.
   - Take the checklist's surfaces, narrowed to the ones this kind of checkout
     can have.
   - Each entry names its `lookup`. Write that call beside the surface.
   - A subsystem the checkout ships that no entry names gets a row of its own.

   The report closes on this same list, with every entry answered.

   A surface is in scope because the checklist names it, not because the file
   tree shows it. A file listing cannot show a manual nobody wrote, a test that
   does not exist or an absent documentation tree. Those absences are often the
   finding. So derive the list from the checklist and
   `typo3_extension_describe`. Then let the reading answer it.

4. Where the request names what it is about, read only the surfaces it names.
   Examples are security only, configuration only, or one subsystem. Mark the
   rest **not requested** on that same list. The request narrows the reading,
   never the list.

   An entry nobody asked about takes one line. That line says what a review of
   the surface would read: the lookups and the files. Without the entry, the
   reader cannot ask for the rest. A request that names no surface is not a
   focused one, and you read every entry.

## Ask before you judge, on every surface in scope

The surface list says which surfaces are in play, and the reading says what is
there. Neither says whether it is right. The conventions say that. Ask for them
**before** you form a view of the subsystem, not to confirm a view you already
have. Make these calls for each surface in scope:

- `typo3_hint_lookup` with the subsystem's concrete paths and a short English
  description. Send one query per surface. A broad query is no evidence about a
  subsystem. Neither is a query that states a finding you already formed.

  A surface can have no files in the checkout. Ask for it by the hint ids that
  `surfaces` names for it. Its absence is often the finding, and you have no
  paths to pass.

- The runtime lookup for the surface, where one exists. The base names each one
  and what it adds after the extension answer. An audit makes that call per
  surface in scope. An answer from what the package declares is about the
  package, not about this installation.

- `typo3_documentation_lookup` with several short English queries and the target
  version, where an official API or configuration detail decides the finding.
  Ask it each time a surface raises "does this still work here". The base says
  why the changelog cannot answer that.

- `typo3_ter_lookup` with the extension key, on the package surface. It says
  what the Extension Repository has published. The package itself cannot answer
  that. `ext_emconf.php` names the version under release and keeps that name
  after the release. So a published checkout reads exactly like an unpublished
  one. Where the Extension Repository has no entry for the key, report that as
  the answer. It is not a finding. An extension that ships only through Composer
  has no entry there.

Read the checkout for what none of those can know. That is the files, the
registrations, the tests, the documentation and the conventions the project
follows.

### How to judge what you read

- Read every rule in both directions. It says what new code should do. It also
  says what this checkout already does wrong.

- A file that follows the opposite of a rule is a finding, not a local style to
  keep. The project's own habits are part of the assessment, so consistency with
  them establishes nothing.

- The absence of an optional subsystem is not a defect.

- A surface that is present and that nobody asked about is **unassessed**, and
  unassessed is not clean. Say so in the result. Otherwise a defect nobody
  looked for reads like an absent defect.

- Tell a verified violation apart from a recommendation and from missing
  evidence.

- Report the deprecation sweep of the base the same way. A review that found
  nothing says the sweep ran and came back empty, with the majors it covered.
  Otherwise a reader cannot tell an empty sweep from one that never ran.

### A declared major the installation does not supply

The base's first step reports `coreConstraint` beside `typo3Version`. A finding
that depends on the version holds on the installed major. Where the constraint
names only the installed major, this is one line on the coverage list and no
call. Where it names a major the installation does not supply, that gap applies
to every such finding.

Two procedures close the gap. This skill does not repeat them:

- `typo3_rule_lookup` with
  `documentId="extension/compatibility/a-declared-major-that-is-not-installed"`.
  It says whether the API the code calls exists on the other major. It reads
  that branch one symbol at a time, not the package as a whole.

- `typo3_rule_lookup` with
  `documentId="extension/compatibility/running-on-a-declared-major-that-is-not-installed"`.
  Use it where you must run the claim instead of read it. It says what the
  repository's own pipeline already covers. Read that before you install
  anything. It also says how a separate Composer root sets up the other major.

A declared major above the installed one needs its breaking changes read first.
A review may skip the deprecation sweep of the base. It may not skip these. The
review brief names that call in `owed`, with the package's PHP as its paths.
Check each entry it returns against the files that entry names. A check that
runs on the installed major clears none of these entries, because its analysis
reads the installed core. Read each file an entry names, or report the entry
under **Open**.

Where you run neither procedure, report the gap. Do not claim the finding for
the whole declared range. The finding holds where you established it. Name the
majors you did not establish it on.

## Report

**Write the report in the skeleton of
[references/report.md](references/report.md), every heading in its order and
every entry in its line form.** The three review skills share that skeleton, so
two audits compare section by section.

- Order the findings by severity. Each finding has the fields the skeleton gives
  it. Those fields are the finding gate of the checklist.

- Beside the findings, report what you raised while you read and dropped, with
  what dropped it. The checklist section *When a candidate may be dropped* sets
  the bar. A candidate you could neither establish nor disprove is open, not
  dropped.

- Close on coverage, not on a summary. Coverage is the surface list written in
  step 3. Every entry in it has a mark: assessed, unassessed, not requested or
  not applicable. It is that list and not a recollection at the end. A summary
  from memory reports what you noticed you skipped, never what you never
  reached.

- **An assessed row cites the lookup its entry names.** A surface you read and
  never asked about is unassessed. The reading alone cannot say whether it is
  right.

- Unassessed and not requested both mean that you established nothing there, and
  they are not the same thing. Unassessed is a gap of this review. Not requested
  is what the request left out. Say which of the two per entry, and let neither
  read as clean.

Without the list, a thorough report and a narrow one look alike. A report that
reads fewer surfaces finds fewer defects, and then it looks as thorough.

### The form of the report

**The report is markdown the reader can copy, and the answer is where it goes.**
People move an audit into an issue, a ticket or a chat, and rendered output does
not survive the move. Put the whole report in one fenced `markdown` block, with
a fence of four backticks. A client that renders the answer copies rendered
text, and the fence keeps the source. Write it to a file only where the caller
asks for one, at a path outside the checkout under assessment.

**The answer opens with that fence.** Write no sentence before it. That includes
the sentence about which checks ran and that nothing changed. It belongs under
**Verification**.

**A document the caller asks for is this report, in this order and with every
part above.** A summary in another shape drops the ranks, the paths, the dropped
candidates and the coverage. A reader checks an audit by those.

### Where a review ends

**A request that asked for a review ends here.** Report, name the skill for each
finding as step 10 says, and stop. An instruction to change the package asks for
the rest: "fix it", "put that right", "do the first three". It arrives after the
report.

**A question about a finding is not that instruction.** "Is that really
breaking", "why is that one high" and "are you sure" ask for evidence, not for a
change. Where a sentence could be either, ask which the user meant.

A review that changes nothing still runs the checks. The commands
`typo3_project_describe` marks as checks leave the code as it was. So an audit
told not to change files runs them. It reports what they printed under
**Verification**.

## Agree the list before anything changes

5. Write one item per finding, in the severity order of the report. Each item
   gives:

   - what the finding is, and the file or registration it is about;
   - its severity;
   - the skill that covers it;
   - its state.

   A finding the report left open is an item too. So is a surface the report
   marked unassessed. The work on those items is to establish what the audit
   could not.

6. Establish what the repository already holds for each item, and write it on
   the item before you show the list. A finding already fixed on an unmerged
   branch reads like one nobody touched. Then only the maintainer can tell the
   two apart.

   Read more than the open pull requests. Also read the branches pushed without
   one, and the maintained release lines. A branch without a pull request is the
   one a reader misses. It is where a maintainer's unfinished work sits.

   Beside its own state, each item gets one of three marks:

   - **untouched**: nothing in the repository addresses it.
   - **addressed**: a named branch or pull request changes it.
   - **colliding**: an unmerged change reaches the same places without a settled
     finding.

   "Read what a branch already holds" below says how to find that out.

7. Show that list whole. Let the maintainer cut items, reorder them or stop
   before you make a single change. Do not start the work while you show the
   list. No later step can repair a skip of this one. Where the list arrives
   together with its changes, the maintainer had no chance to disagree.

8. Keep the list in the session, not in the repository. Report the state of each
   item as the work goes. A worklist committed into somebody's history is a file
   nobody asked for, and somebody has to remove it again. The history keeps the
   commits the items produced.

A request can arrive with a report from an earlier session. Read it whole rather
than run the audit again. Say which of the two you built the list from. That
report is evidence about the checkout as it stood then.

### Read what a branch already holds

Run `git fetch --prune` first. Compare each branch against the base it targets,
not against the one the audit ran on. Otherwise the remote-tracking branches
answer for a state that has moved.

1. `git diff --name-only <base>...<branch>` names the files the branch touches.
   A branch that names none holds nothing the base does not already have.

2. Then run `git diff <base> <branch> -- <those files>`. An empty diff means one
   thing: **the base already holds what the branch has in those files**.

3. A non-empty diff does not mean that the branch holds something new. A fix
   that landed and a file the base edited later give the same non-empty diff. So
   read the diff against the finding. Do not count it.

Two shortcuts do not answer this question:

- `git cherry` compares patch ids and calls a squash-merged branch fully
  outstanding.
- An unrestricted two-dot diff reports how far behind the branch is.

The branch listing covers the pull requests opened from the repository itself.
One opened from a fork is reachable only through the forge. That reading is
`gh pr list --json number,title,headRefName,files`. It needs a GitHub remote,
the `gh` tool and a login. Assume none of the three. Where it does not answer,
say that you did not read the pull requests and ask the maintainer. Do not
report the branches as the whole surface.

An addressed item stays on the list. An unmerged branch holds a claim about the
finding rather than the fix. So read it against the finding, the way you read
the checkout. Remove it from the list only where the checklist section *When a
candidate may be dropped* permits the drop.

## Work the list off item by item

9. Take the items in the order of the list, grouped by the skill that covers
   them. Activate each skill once for all its items, not once per finding. That
   skill decides how its own area changes.

   Where the list came from a review, you skipped the deprecation sweep of the
   base, because you wrote nothing. Run it now, before the first item. That is
   one call per declared major, over the paths the items name. A session that
   comes back here with the base already read often forgets the sweep.

10. **Invoke the skill that covers them.** Hand over only the scope and the
    verified behaviour it needs: the finding, its evidence and the paths. Stop
    before you edit files in the area of another skill. Name that skill in the
    report whether or not the user asked for fixes. A reader who decides what to
    do next needs it too.

11. Where no skill covers an item, work it here only where the project's own
    checks prove the change. Change nothing wider than the finding, and run the
    check that covers it. An item nothing here can prove goes back unassigned in
    the final report. Such a finding shows an area that no skill covers yet. A
    change you make on your own judgement hides that area.

12. Settle where the change lands before the first commit:

    - which branch the commits go on;
    - whether the pull request squashes;
    - which released lines get the fix.

    That is the repository's own policy, and nothing here reads it. Ask the
    maintainer before you push a branch and before you open a pull request. A
    branch listing and a tag scheme are not that answer. They say which branches
    exist, never which ones are still supported.

    The core fixes on the main branch and cherry-picks down. That is the core's
    own process and the default nowhere else. Ask once and work from the answer.
    Keep it in the session, as you keep the list.

13. Commit per item, or per group of items in the area of one skill. Write the
    message with `typo3_commit_message_guide` and `workflow="project"`. Say in
    it which item the commit closed. A session that ends halfway is read from
    the log. A log that names the finding each commit closed shows how far the
    work got.

## Close the work

14. Run the audit again on the worked list. Do not only read the files it
    changed. The environment that writes a file can still rewrite one that reads
    correctly. The difference shows only once that environment runs again. A
    read of your own diff does not show that the finding is gone.

15. Report what remains:

    - the items still open;
    - the items dropped, with what dropped them;
    - the items sent back unassigned;
    - every finding the audit left open or unassessed that this work did not
      settle.

    In a summary, a reader cannot tell a finished list from an abandoned one.

## Where this skill stops

- **A change in the area of another skill** goes to that skill. Those are
  `typo3-extension-testing`, `typo3-extension-documentation`,
  `typo3-backend-module-development`, `typo3-content-element-development` and
  `typo3-extension-upgrade`. Give that skill the item, and let it do the work.

- **The result of the sweep.** What the sweep returned goes to
  `typo3-extension-upgrade` whole. That skill covers the move of the package to
  another supported range. It also sets the order of the deprecations. Where you
  give it one deprecation at a time, you set that order instead.

- **One change proposed against the package**, such as a pull request, a patch
  or a branch, is `typo3-extension-patch-review`. You judge it against its diff,
  not against the whole repository.
