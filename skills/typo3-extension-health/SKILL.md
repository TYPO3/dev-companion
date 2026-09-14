---
name: typo3-extension-health
description: 'Review a TYPO3 project, sitepackage or extension against its checkout and active installation and put it right — "look over my repository and fix it": TCA, services, backend modules, content elements, site sets, TypoScript, Fluid, labels, icons, security boundaries and deprecated APIs. The audit reports first; nothing is changed before the list is agreed.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Health

Establish what is wrong with a package against evidence. Then work the agreed
list off until it is empty. Keep this skill as routing, assessment and
sequencing method. Do not embed versioned TYPO3 facts.

The two halves are one workflow, and the gate between them is step 5. The audit
answers a request that asked for a review. A request that asked for changes
passes through the same report on its way to them. Edit nothing before that
gate, whatever the request asked for.

## Establish scope and evidence

1. Work through [references/base.md](references/base.md). It fixes the order
   every task here starts in, and an assessment is where that order matters
   most. A rule you fetch after the reading confirms a verdict instead of a test
   of it.
2. Read [references/checklist.md](references/checklist.md) for the audit
   surfaces, the finding gate, and the severity rubric.
3. Write the surface list down before you open a single file. Take the
   checklist's surfaces, narrowed to the ones this kind of checkout can have. It
   is the work list. The coverage the report closes on is this same list with
   every entry answered.
4. Where the request names what it is about (security only, configuration only,
   one subsystem), read the surfaces it names. Mark the rest **not requested**
   on that same list. The request narrows the reading, never the list. An entry
   nobody asked about costs one line. A dropped entry is what leaves the reader
   unable to ask for the rest. A request that names no surface is not a focused
   one, and you read every entry.

A surface is in scope because the checklist names it, not because the file tree
shows it. A file list first inverts that. `find` cannot show a manual nobody
wrote, a test that does not exist, or a documentation tree that is absent. So
the surfaces it hides are exactly the ones whose absence is the finding. Derive
the list from the checklist and `typo3_extension_describe`. Then let the reading
answer it.

## Ask before you judge, on every surface in scope

Scope says which surfaces are in play, and the reading says what is there.
Neither says whether it is right. That comes from the owner of the convention.
Ask for it **before** you form a view of the subsystem, not to confirm one you
already have:

- `typo3_hint_lookup` with the subsystem's concrete paths and a short English
  description. One query per surface in scope. A single broad query is not
  subsystem evidence. Ask for a surface the checkout has no files for by its
  hint id instead. That is the surface whose absence is the finding, and the one
  whose paths you cannot pass.
- The runtime lookup that owns the surface, where one exists. The base names
  them and what each adds after the extension answer. An audit makes that call
  per surface in scope. An answer off what the package declares is about the
  package rather than about this installation.
- `typo3_documentation_lookup` with several short English queries and the target
  version, where an official API or configuration detail decides the finding.
  Ask it for every "does this still work here" a surface raises. The base says
  why the changelog cannot answer that one.
- `typo3_ter_lookup` with the extension key, on the package surface. What the
  Extension Repository has published is the one thing about this package the
  package cannot answer. `ext_emconf.php` names the version under release and
  goes on with that name afterwards. So a published checkout reads exactly like
  an unpublished one. A key with no publication under it is an answer rather
  than a finding. An extension distributed through Composer alone has no
  registration here.

Read the checkout for what none of those can know. That is the files themselves,
the registrations, the tests, the documentation, and the conventions the project
has settled into.

Then read every returned rule in both directions. It says what new code should
do, and it says what this checkout already does wrong. A file that settled into
the opposite of a rule is a finding, not a local style to preserve. The
project's own habits are part of what you assess, so consistency with them
establishes nothing.

Do not report the absence of an optional subsystem as a defect. But a surface
that is present and nobody asked about is **unassessed**, and unassessed is not
clean. Say so in the result. A defect nobody looked for and an absent defect
read the same in a report that does not separate them. Tell a verified violation
from a recommendation and from missing evidence.

Report the deprecation sweep the base fixes the same way. A review that found
nothing says the sweep ran and came back empty, with the majors it covered. A
reader cannot tell a sweep that shows only when it produces a finding from one
that never ran. The surface it covers is the one whose silence reads as a clean
bill for the next major.

A finding that depends on a version holds on the installed one, and the package
declares a range. The base's first step reports `coreConstraint` beside
`typo3Version`. So where the constraint names a major the installation does not
supply, that gap is on every such finding. To close it is a whole procedure this
skill does not repeat:

- `typo3_rule_lookup` with
  `documentId="extension/compatibility/a-declared-major-that-is-not-installed"`.
  It says whether the API the code calls is there on the other major. It is a
  reading of that branch, and the question is per symbol rather than per
  package.
- `typo3_rule_lookup` with
  `documentId="extension/compatibility/running-on-a-declared-major-that-is-not-installed"`.
  That is where you have to run the claim instead of read it. It says what the
  repository's own pipeline already covers, which you read before you install
  anything. It says how a Composer root of its own stands the other major up
  beside the installation.

An audit that runs neither reports the gap rather than the range. The finding
holds where you established it, and you name the majors you did not establish it
on. Where the constraint names the installed major alone, this is one line on
the coverage list and no call.

## Report

Order findings by severity and include:

1. the concrete file or runtime registration;
2. the observed behavior or configuration;
3. the applicable MCP or official-documentation evidence;
4. the consequence;
5. a scoped remediation and relevant project check.

Beside them, report what you raised while you read and dropped, with what
dropped it. A candidate let go in silence and a surface nobody opened leave the
same trace in the report. The checklist's *What a dropped candidate owes* is the
bar each one has to meet. That includes the one you could neither establish nor
disprove, which you report as open rather than dropped.

Close on coverage rather than on a summary. That is the surface list written in
step 3, every entry marked assessed, unassessed or not requested, clean ones
briefly. It is that list and not a recollection at the end. A summary from
memory reports what the session noticed it skipped, never what it never reached.

Without the list a thorough report and a narrow one look alike. The cheapest way
to look thorough is to examine less. Unassessed and not requested both mean that
you established nothing there, and they are not the same thing. One is this
review's gap, the other is what the request left out. Say which of the two per
entry, and let neither read as clean.

**The report is markdown the reader can copy, and the answer is where it goes.**
The findings and the coverage list together make it long, and length makes the
form matter. Somebody carries an audit into an issue, a ticket or a chat, and
rendered output does not survive the move. Write it to a file only where the
caller asks for one, at a path outside the checkout under assessment.

**A request that asked for a review ends here.** Report, name the owning
workflow per finding as step 10 says, and stop. An instruction to change the
package asks for the rest: "fix it", "put that right", "do the first three". It
arrives after the report, which is where you leave a review.

**A question about a finding is not that instruction.** "Is that really
breaking", "why is that one high", "are you sure": each asks you to defend the
report. It wants the evidence rather than a change. Where the sentence could be
either, ask which the user meant.

Stopping at findings is not stopping at reading. The commands
`typo3_project_describe` marks as checks hand the code back as it was. So an
audit told not to change files runs them. It reports what they printed.

## The list is written down and agreed before anything changes

5. Write one item per finding, in the report's own severity order. Each item
   carries what the finding is and the file or registration it is about. It
   carries its severity, the workflow that owns it, and a state. A finding the
   report left open, and a surface it reported unassessed, are items too. Their
   work is to establish what the audit could not.
6. Establish what the repository already carries against each item, and write it
   on the item before you show the list. A finding already fixed on a branch
   nobody merged reads on the list exactly like one nothing has touched. The
   maintainer is then the only party who can tell them apart.

   The surface is wider than the open pull requests: branches pushed without
   one, and the maintained release lines. The branch with no pull request is the
   one that gets missed. It is where a maintainer's own unfinished work sits.
   Beside its own state, the item carries one of three. It is **untouched**, or
   **carried** by a named branch or pull request. Or it is **colliding** with an
   unmerged change that reaches those places without a settled finding.
7. Show that list whole. Let the maintainer cut items, reorder them or stop,
   before you make a single change. That is what the whole order exists for, and
   it is the one step nothing downstream recovers. Do not begin the work while
   you show the list. A list that arrives together with the changes it produced
   is one nobody had the chance to disagree with.
8. Keep the list in the session rather than in the repository. A worklist
   committed into somebody's history is a file nobody asked for, in a project
   this workflow visits. Somebody has to take it out again afterwards. So report
   each item's state as the work goes. What the history keeps is the commits the
   items produced.

Git answers step 6, per branch, after a `git fetch --prune`. Read against the
base that branch targets rather than the one the audit ran on. Otherwise the
remote-tracking branches answer for a state that has moved.
`git diff --name-only <base>...<branch>` names the files the branch touches. A
branch that names none holds nothing the base does not already have.

Then run `git diff <base> <branch> -- <those files>`. Its empty answer settles
one reading: **the base already holds what the branch has in those files**.
Non-empty is not the opposite of that, which is where a reader goes wrong. A fix
that landed and a file the base edited afterwards produce the same non-empty
diff. So read it against the finding rather than count it.

Two shortcuts do not answer this at all. `git cherry` compares patch ids and
calls a squash-merged branch fully outstanding. An unrestricted two-dot diff
reports how far behind the branch is.

The branch listing covers the pull requests opened from the repository itself.
One opened from a fork is reachable only through the forge. That reading is
`gh pr list --json number,title,headRefName,files`, where the remote is GitHub
and the tool has a login. Assume none of the three. Where it does not answer,
say that you did not read the pull requests and ask the maintainer. Do not
report the branches as the whole surface.

An item you found already carried is not thereby dropped. An unmerged branch
holds a claim about the finding rather than the fix. So read it against the
finding the way you read the checkout. The checklist's *What a dropped candidate
owes* is the bar it has to clear before the item leaves the list.

Where the request arrives with a report from an earlier session, read it whole
rather than run the audit again. Say which of the two you built the list from. A
report from an earlier session is evidence about the checkout as it stood then.

## The list is worked off item by item

9. Take the items in the list's order, grouped by the workflow that owns them.
   One activation that covers that owner's items costs less than one per
   finding. The owner decides how its own area changes.

   Where the list came from a review, the base's sweep was exempt while you
   wrote nothing. You owe it now. Run it before the first item. That is one call
   per declared major, over the paths the items name. It is the one step of that
   order an audit skips. So to re-enter here with the document already read is
   what leaves it unrun.
10. **Invoke the skill that owns them** and carry across only the scope and the
    verified behaviour it needs. That is the finding, the evidence under it, the
    paths. Stop before you edit files another owner has. The crossing is the
    transition itself, not a detail of the item. Name that workflow in the
    report whether or not the user requested fixes. A reader who decides what to
    do next needs it as much as a session told to do it.
11. Where an item has no owning workflow, work it here only where the project's
    own checks prove the change. That is the change, the check that covers it,
    and nothing wider than the finding. An item nothing here can prove goes back
    unassigned in the closing report instead. A finding no workflow owns and no
    check covers is a hole in the map. A change on judgement is what hides the
    hole.
12. Settle where the change lands before the first commit. That is which branch
    these commits belong on, whether the pull request squashes, and which
    released lines carry the fix. That is the repository's own policy, and
    nothing here reads it.

    Ask the maintainer, before you push a branch and before you open a pull request. A branch listing and a tag scheme are not that answer. They say which branches exist, never which are still supported.

    What the core does is the core's own process and the default nowhere else. The core fixes on the main branch and cherry-picks down. Ask once and work from the answer. Keep it in the session the way you keep the list.
13. Commit per item, or per group of items in one owner's area. Say which item
    that commit closed, in the message from `typo3_commit_message_guide` with
    `workflow="project"`. A reader reads a session that ends halfway out of the
    log. That is why the state belongs in the commits rather than in the list
    alone. A log that says which finding each commit closed is what makes it
    readable.

## What closes it

14. Run the audit above again on the worked list. Do not only read the files it
    changed. The environment that owns a file can still rewrite one that reads
    correctly. The difference shows only once that environment runs again. Work
    that grades itself off its own diff has no evidence the finding is gone.
15. Report what remains: the items still open, the items dropped with what
    dropped them, and the ones sent back unassigned. Report every finding the
    audit left open or unassessed that this work did not settle. A finished list
    and an abandoned one read alike in a summary.

## Where this stops

This skill owns the state of a whole package. That is what is wrong with it,
what each finding is worth, and who takes it onward. It is the agreed list until
it is empty. It owns both halves of that one thing, and a request for either
arrives at the same door.

It does not own the changes in another workflow's area. Those cross to
`typo3-extension-testing`, `typo3-extension-documentation`,
`typo3-backend-module-development`, `typo3-content-element-development` or
`typo3-extension-upgrade`. What this skill carries across the crossing is the
item and not the work.

What the sweep returned goes to `typo3-extension-upgrade` whole. It owns the
move of the package to another supported range. A handover of one deprecation at
a time decides the order that workflow exists to establish.

It does not own one change proposed against the package either. You judge a pull
request, a patch or a branch somebody offers against that diff rather than
against the repository. To run this surface list on a one-line change is what
`typo3-extension-patch-review` exists instead of.
