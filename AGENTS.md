# Working on this repository

## What comes first

**What a person cannot understand is a defect, and so is what a machine cannot
read.** Not a style question and not a matter of taste: a defect, reported and
fixed like any other.

Everything here has both readers, and where they pull apart both are served
rather than one traded away — a name a person understands and a check can read,
a title that scans in a listing and parses as data, an answer whose text says
what its schema says. The rest of this file is what that comes to in each
corner, and where one of these rules makes something harder to understand, it is
the rule that is wrong.

What that looks like in practice, and each is a defect on its own: a file name
nobody can read at a glance, a sentence that will not end, a function name with
three clauses in it. A formulation is short, precise and in plain words — the
rule is in [Prose](#prose) and the measures are `bin/cli prose:check`.

Two more follow and are worth saying outright:

- **What is read as data lives where data lives**, and is said once. A title, a
  test that holds an entry, a version an answer is bound to — parsed out of
  prose, each is a regex somebody has to write again and get right again.
- **Where a person has to guess what something means, that is the defect**, even
  where nothing fails on it. A title that repeats its own statement, an id that
  arrives only in a docblock, a file whose name claims something the file no
  longer says: each costs the reader the work the writer saved.

## Layout

```
bin/typo3-dev-companion  # stdio entrypoint; the client launches it as a subprocess
bin/cli            # everything this repository is kept in order by; run it with nothing for the list
src/               # grouped by what a class is: only Paths and the bootstrap sit loose
src/Server/        # starting this server and setting a project up for it
src/Server/Entrypoint.php  # what `bin/typo3-dev-companion` runs: the commands, the usage, the transport
src/Server/Factory.php     # builds the mcp/sdk server from the tool definitions
src/Server/Installer.php   # writes the client setup, publishes the skills, puts back a publication that has gone stale
src/Server/ExcludedTools.php  # what TYPO3_DEV_COMPANION_EXCLUDE_TOOLS takes away
src/Tool/          # one class per tool: its description, its schemas, its answer
src/Tool/Tool.php  # the interface each one implements; ReadOnlyTool carries the annotations
src/Tool/Registry.php  # every tool this server has, and the only place one is switched on
src/Result/        # what a tool hands back, and what several of them build one from
src/Knowledge/     # what this package knows about TYPO3: read from knowledge/, or computed where a rule is applied rather than looked up
src/Knowledge/Scope.php  # which kind of work an answer is for, and how a path is placed in it
src/Knowledge/Coverage.php  # knowledge/server-scope.json: what the server covers, and the initialize instructions
src/Knowledge/Catalog/  # the component catalog and the translation domain derivation
src/Installation/  # what only the installation being read can answer: its icons, labels, namespaces, changelog, project and extensions
src/Installation/Instance.php  # finds the TYPO3 installation the agent is working in
src/Installation/Typo3Cli.php  # runs that installation's console, via DDEV where there is one
src/Installation/Typo3Runtime.php  # boots it in a subprocess and asks its container
src/Installation/probe.php  # what runs over there; never included here
src/Manual/        # the third source: the versioned TYPO3 manuals, and the core changelog above what an installation ships
src/Contribution/  # the fourth source: the services the core's own process runs through
src/Publication/   # the fifth source: the registry a TYPO3 extension is published to, and what it already holds
src/Http/Fetch.php # the one way this server reads a host outside itself: the timeouts, the redirect limit and the agent
src/Search/        # the lexical matching every prose and label lookup goes through
src/Feedback/      # the feedback channel; a development tool, offered from a standalone checkout alone
src/Feedback/Card.php  # the todo a feedback arrives with
src/Sdk/           # the adapters onto mcp/sdk: tool dispatch and the typo3:// resources, documents and skills
src/Paths.php      # where this checkout keeps things; the one class both halves share
src/bootstrap.php  # locates the Composer autoloader
src/Upkeep/        # what `bin/cli` runs on this repository, and nothing the server answers with
src/Upkeep/Cli.php # the console application, and the only place a command is switched on
src/Upkeep/Command/  # one class per command, named `<subject>:<verb>` by its own #[AsCommand]
src/Upkeep/Links.php # every path this repository writes between its own files, and whether it still resolves
src/Upkeep/Rst.php   # the reStructuredText the generators write: the underline per level, the directive, the literal, the two roles
src/Upkeep/Site.php  # documentation/ as the source a generator publishes: the links that leave it, rewritten
src/Upkeep/Todo.php  # todo/ as data: what recurs, what is queued, what each todo serves
src/Upkeep/PinnedPackage.php  # which release of a package the core pins each covered major is read against
knowledge/         # the knowledge base (markdown + JSON), the data source
knowledge/documents/  # the prose corpus: searched by typo3_rule_lookup, served as typo3://core resources
feedback/          # feedback left by agents about this server (standalone checkout only)
feedback/archive/  # the ones that were worked off; kept, because a session's report is evidence
scenarios/         # user prompts and what has to come out of them, one case per file
scenarios/forward/ # open forward reviews: the only kind that is run and recorded
scenarios/contracts/ # targeted cases per audience, task skill and situation: one named task shape each
scenarios/runs/    # one recorded forward run per review, with the judgment per criterion
skills/            # canonical task skills installed into agent clients, and served as typo3://skill resources
skills/base.md     # the order every task starts in, copied into each published skill as references/base.md
requirements/      # what must hold, and what holds it there: one per file; open ones are not built yet
decisions/         # what a change assumed, and what would show it to be wrong: one per file
todo/              # the order of the work: one todo per file, and where a file sits is the stage it is in
todo/open/         # the queue, read by the priority in each head and then by the day in each id
todo/waiting/      # what nothing here can start, carrying the question it is blocked on
documentation/     # what the server is and how a procedure is carried out; published whole as the site; reStructuredText, where everything around it is markdown — D-DOC-029
documentation/usage/   # having this server answer in somebody's own project
documentation/server/  # what it can be asked and where each answer comes from
documentation/contributing/  # working on the server itself: the commands, the session, the skills, the words
documentation/records/ # what is written down and where: feedback into todo, requirements, decisions, forward runs
documentation/images/  # the drawings a page names, and the mark at its three optical sizes
documentation/guides.xml  # what the render is: the copy, the theme, and everything the bar, the tab and the footer say — D-DOC-024
tests/             # unit, tool contract, and stdio smoke tests
vendor/            # Composer dependencies (mcp/sdk); gitignored
```

Every class below `src/` sits in the group it belongs to, and one that fits none
of them is a group nobody has named yet rather than a file at the root. An
answer comes from five of them — `Knowledge/`, `Installation/`, `Manual/`,
`Contribution/` and `Publication/` — and the last three reach outside this
process, which is what their tools' `openWorldHint` says. `Paths` sits loose
because the server and the upkeep both stand on it.

Both binaries are the same shape: locate the autoloader, hand the arguments to
the class that owns them — `Server\Entrypoint`, and `Upkeep\Cli` by way of the
console application it builds. What a command does, what it is called and what
it takes are declared beside the code that does it, never in `bin/`.

## Where a session starts, and what it owes the next one

    bin/cli todo:next

That is the whole of it, and it prints **one todo** — the first that is due,
whole, with its own command already run. `bin/cli todo:list` is the overview.

The todo it prints is a claim, not an instruction: one session's belief about
where to start, written before the work it describes and by somebody who has
left. So it is read against what the repository does today, and where that
reading says the step is done, impossible or two steps, the file is corrected
before the work rather than after it.

A question the step turns on is **settled**, never recalled. What cannot be
established here is a result and is recorded as one — the todo trimmed to what
is still open, the requirement `not guarded`, the decision saying what evidence
would settle it. What none of those can absorb is a guess written with the
confidence of a reading, because nothing afterwards can tell the two apart.

What has no source here is **asked**, before the change rather than in the
commit that presents it. Ask with the reading done — the options, what each
costs, a recommendation — and offer to put the todo back, which is not a worse
outcome than one done on a guess. What comes back goes into `todo/` or
`decisions/`, because an answer that lives only in the conversation ends with
it.

Nor in whatever the agent can remember on its own. A client's private memory is
not a place this repository keeps anything: what was established goes into
`todo/`, `requirements/`, `decisions/` or `documentation/`, where the next
session reads it and a wrong line can be corrected.

Keeping [todo/](todo/readme.md) current is part of the work, not a step after
it.

What is read before the first change, how a question is settled, what is asked
and what the file says afterwards:
[documentation/records/working-a-todo.rst](documentation/records/working-a-todo.rst).
How `next` decides what is due, and how the work moves between `feedback/`,
`requirements/`, `decisions/` and `todo/`:
[documentation/records/readme.rst](documentation/records/readme.rst).

Several sessions at once is `bin/cli todo:claim <n>`, one worktree and one
branch each, and `bin/cli todo:home <id>` is how one of them comes back.
`bin/cli todo:drop <id>` gives one back unmerged. All three carry their step out
rather than printing it, so none is a reason to read anything first. The
worktree standing on a todo's branch is what says somebody has it in hand, so
`todo:next` in one hands over that todo rather than the front of the queue, and
every command that hands out work passes over it. What that costs and what still
collides:
[documentation/records/working-todos-in-parallel.rst](documentation/records/working-todos-in-parallel.rst).

## How a session reads

A session is charged one context per call, not one per token: the number of
calls is the budget, and what one of them returns is nearly free — `D-FBK-020`,
where the counts are.

- **Send the calls that do not depend on each other together.** Most of what a
  session reads at the start turns on nothing that came before it.
- **Reach for a file with the client's own file and search tools**, rather than
  spelling out through `cat`, `sed`, `grep` and `ls` what one call answers.
- **Open a file once, whole.** A window into a file the session opens again
  afterwards is two calls for one reading.

What that does not license is reading less. The step is still read against what
the repository does today, and a question it turns on is still settled from a
source — in fewer calls, not from memory.

## What things are called

One thing, one word. Where two compete, the one that wins is the one somebody
outside this checkout can see — a tool name, a directory name, a CLI subject —
because those are known by clients installed months ago and by paths people
wrote down, while prose can be rewritten this afternoon.

**A name carries one claim, and names its own subject.** It is read where
nothing else is — a failure list, a directory of hundreds of entries, a listing
in a client — so a reader who has not opened the file has to know from it what
is meant. Three ways it stops doing that, and each is a defect:

- **Two claims in one name.**
  `everyLineIsSetUpOnAFileRatherThanOnAContainerOfItsOwn` states a case and the
  case it is being told apart from; the second half is what the docblock is for.
  `bin/cli prose:check` counts the names and the titles that do this, worst
  first.
- **A subject the name never says.** "Three audiences, not one" counts without
  naming what is counted, and "Activation is the client's" refers to something
  only the body introduces. What the reader is left with is the shape of a
  statement and none of it.
- **A negation where the affirmative is what is meant.** "What the scope
  excludes is not what the server answers" is read twice; "A subject the
  not-covered list omits is in scope" is read once. It is the prose rule below,
  where it is read most.

A directory below `src/` is named in the **singular**, for what one of the
things in it is: `Tool/` holds one tool per class, `Command/` one command,
`Manual/` one manual. The plural splits every name in two — a class lands in
`Commands/` and is referred to as a command all the way down. The directories
outside `src/` keep the names their callers know them by.

What arrives through `typo3_feedback_record` is **a feedback**, countable, and
[documentation/contributing/glossary.rst](documentation/contributing/glossary.rst)
is where that and the words around it are defined. Two of them go wrong
reliably: **record** is the verb it arrives by and never a noun, because in
TYPO3 a record is a row in the database; and **verdict** belongs to
`scenarios/`, where it is how a run came out, while what becomes of a feedback
is its **answer**.

## Less is more

Every task is also an occasion to leave the code smaller than it was. A change
is finished when what it added is there **and** what it made unnecessary is
gone.

- Before writing an abstraction, look for the one the change makes redundant.
  Two answers of the same shape share a formatter, and a branch nobody takes is
  deleted rather than kept for symmetry.
- Prefer the change that removes a concept to the change that adds one. A
  parameter that is always passed the same value is not a parameter, and a
  helper with one caller is that caller.
- Code written to be general is speculation until the second caller exists. The
  second caller is also what shows what the two actually have in common.
- Deleting needs no feature to justify it. A simplification that stands on its
  own is its own commit, and a review of code nobody has touched in a while is a
  legitimate task.
- Shorter is not the same as denser. Fewer concepts, fewer branches, fewer
  moving parts — not fewer lines wrung out of the same logic.

What this does not license is a smaller API. Where a framework offers several
ways in, take the one its own documentation and tests treat as the main path,
even where that means more files. A cheaper-looking variant that needs an extra
nudge to behave is not the smaller change: the nudge is the added concept. Say
which way in is the main one and which is being proposed; do not decide it for
brevity.

## Prose

Short and precise, everywhere: `knowledge/`, the tool descriptions, this file,
`documentation/`, a commit message. Every reader pays per token, and half of
them are machines.

The rules below hold whatever the markup. `documentation/` is reStructuredText
because it is published as the site and a reference into another page has to
resolve; everything else here is markdown — `D-DOC-029`. Inside `documentation/`
a name is a double-backtick literal, another page is `:doc:`, a place in one is
`:ref:` and a label above the heading it names, and a path leaving the tree is
an embedded link `Site` rewrites to GitHub.

- One point per sentence. A sentence that restates the previous one in other
  words is deleted, not shortened.
- The rule first, the reason after it, and only where the reason is not obvious.
  A justification nobody would dispute is filler.
- One example, where an example is needed at all. The second one rarely adds a
  case and always adds a paragraph.
- Say what is, not what it is not. A list of what something is not belongs where
  the confusion actually happened.
- Length is a symptom. A paragraph that will not come out short is usually two
  points, or one that is not yet understood.
- No count of something that grows. "34 files holding 120 hints" is true on the
  day it is written and wrong on the next commit, and nothing fails when it
  turns. Name the thing and the command that counts it — "one file per subject,
  which `bin/cli hints:coverage` counts" — or say "and many more". A number
  belongs where it was measured: a decision records what a sweep found on its
  date, and a report prints what is true when it runs.

### Comments

A comment earns its lines by saying what the code cannot: why this and not the
obvious alternative, what was measured, what breaks if somebody changes it back.
Everything else is deleted, and the same rules apply as above.

- A comment that restates the line under it is noise. Where the code is unclear,
  the fix is the name, not a sentence explaining the name.
- No docblock that repeats the signature. `@param` and `@return` earn their line
  where they say an array's shape and nowhere else.
- The reason lives in one place. Where a decision or a requirement carries it,
  the comment names the id instead of retelling it.
- Length is a symptom here too. Six paragraphs above a private method is usually
  a decision that was never written down as one.

`bin/cli prose:check` counts what that costs: the sentences over 30 words, worst
file first. It fails on one of them — the bold sentence a requirement or a
decision opens with, because a reader who stops after it is supposed to know
what was settled. The rest is a report, since a long sentence can be the right
one and a rewrite driven by a counter produces two short sentences saying what
one said.

It counts the comments too, where what grew is how many there are rather than
how long any one of them is: the share of the PHP that is comment, and every
comment that names a decision and retells it anyway, most prose first. The
delimiters, the blank lines and the annotations are not counted, so ten means
ten lines somebody wrote — `D-DOC-035`.

The last thing it counts is the tables that hold a cell no line fits. A cell
that will not fit on a line means the content is a list rather than a table,
because what a table buys over a list is a column that can be scanned —
`D-DOC-001`. It is a report because only a reader can say whether a cell can be
shortened, and the exception has to say so where it is taken.

`bin/cli prose:format <path>` is the other half and rewrites rather than
reports: the prose this repository writes about itself, rewrapped at the column
it is already written at. What it is for is the paragraph a rename left ragged.
It moves the line breaks and the padding of a table and nothing else, which
`ProseTest` asserts over the whole corpus rather than trusts, and it leaves
alone everything a break means something in — which those are is what the two
markups disagree about, so the file is asked which it is.

Named no path it sweeps, and a sweep reaches what nobody is holding: in a
worktree the files that branch changed, and in the checkout `main` stands on the
corpus minus what the standing claims changed — `D-DOC-063`. The second of those
is a diff to look at before it is a diff to make.

A table is padded to the width of each column's widest cell, so a column can be
scanned in the state the file is written in rather than only where something
renders it. Both forms render the same, which is what made the compact one look
like the cheaper choice.

## Tool names

Every tool is named `typo3_<subject>_<verb>`. The prefix never varies, the
subject is what the tool is about, and the verb comes from a closed list of six,
because the verb is what tells a caller which shape the answer has:

- `lookup` — a query goes in, matching entries come out, and finding nothing is
  a legitimate answer: `typo3_component_lookup`, `typo3_rule_lookup`.
- `guide` — an answer composed for the task at hand, which always exists:
  `typo3_task_guide`, `typo3_commit_message_guide`.
- `list` — an enumeration of what is there, no query needed:
  `typo3_feedback_list`.
- `scope` — what a source covers and where its boundary runs:
  `typo3_server_scope`, `typo3_snapshot_scope`.
- `describe` — what one thing the caller names is and what it registers:
  `typo3_project_describe`, `typo3_extension_describe`.
- `record` — the tool writes into this server's own checkout:
  `typo3_feedback_record`.

Nothing here writes into the TYPO3 installation it read, and that boundary is
what "read-only" means throughout this repository. `record` is the other kind of
writing and says so, because one word for both reads as a hole in the posture —
`D-FBK-042`.

`scope` and `describe` are the pair that gets confused. A scope answers for a
source and states the boundary of what it can be asked; a describe answers for
one thing the caller named and states what that thing is. `D-SCO-010` is why the
two project and extension tools carry the second.

**A new tool names the tool a caller might have called instead**, and that one
names it back. Which two those are is a reading of the descriptions rather than
anything the declarations say — `D-ANS-072` measured what a check over `covers`
would have proposed — so it is a step of adding a tool, and the pair is held by
a row in `ScopeTest::theToolsACallerCannotChooseBetweenNameEachOther`.

A new tool takes the verb whose answer shape it already has, and two tools
sharing an output schema share their verb. When none of the six fits, the tool
is probably doing two things at once — split it before inventing a seventh verb.
If a seventh is genuinely needed, add it to `ToolNamingTest` in the same commit,
so that list stays the only place the vocabulary is defined.

Leave `core` out of a name: this server is about the TYPO3 core throughout, so
the segment separates nothing.

A tool is one class in `src/Tool/`, implementing `TYPO3\DevCompanion\Tool\Tool`:
what it is called, what it takes, what shape it answers in, and the answer
itself stand in one file, so a description cannot go stale against the answer it
describes without the two being edited apart. `TYPO3\DevCompanion\Tools` is the
list of them, and the only place a tool is switched on. Nothing else belongs
below `src/Tool/` — what more than one tool builds its answer from is
`TYPO3\DevCompanion\Result\`, and the adapters onto `mcp/sdk` are
`TYPO3\DevCompanion\Sdk\`.

The word is the protocol's: an MCP tool is what the SDK declares as
`Mcp\Schema\Tool`, beside `Prompt` and `Resource`. Nothing here is a "server
tool".

Every tool returns a `ToolResult`: the text plus the same answer as data. The
data half is a contract — clients may validate it against the `outputSchema()`
the tool declares, so a field a schema requires has to be present on every path
through the tool, misses included. Add fields rather than renaming them. A
record shape more than one tool answers with belongs in
`TYPO3\DevCompanion\Result\Schema`, so a client reads one model rather than two.

## Checks

```bash
composer ci     # lint, coding guidelines, static analysis, tests — what CI runs
composer test   # phpunit only
composer stan   # phpstan only
composer cgl    # rewrite to the guidelines; cgl:ci reports and rewrites nothing
```

```bash
bin/cli knowledge:format          # the JSON below knowledge/, in the one form
bin/cli knowledge:format <path>   # only that part of it
```

- The JSON below `knowledge/` is written by that command and by nothing else:
  PHP's pretty print at the indentation `.editorconfig` states, slashes and
  unicode as they were typed, key order untouched. `JsonTest` fails on a file
  that is not in the form. `.editorconfig` is where an indentation is said at
  all — `StructureTest` holds the PHP one to php-cs-fixer's.
- The guidelines are php-cs-fixer's own, and `.php-cs-fixer.dist.php` is where
  they are declared: PER-CS 3.0 and the few rules on top of it this repository
  writes by. A rule is added there when the code already follows it and the
  fixer is what keeps it followed — not to introduce a style nobody has written
  in yet, which is a reformatting of the whole tree wearing a rule's clothes.
- **A version this repository pins is checked against the day's release** when
  the file carrying it is touched: node and the actions in `.github/workflows/`,
  the libraries in `composer.json`, a `.ddev/config.yaml` where a checkout has
  one. One that is behind is raised, or the reason it stays is written beside
  it, and a raise nobody has established as safe is asked for rather than taken.
  Where a runtime this package declares rules the newest release out, the pin
  takes the newest version that declaration allows — `D-COD-007`, where PHPUnit
  is that case. No file here names the version that must be used, because that
  number moves and nothing fails when it does — `R-COD-004`, with `R-ANS-037`
  for what an answer may name and `R-SKL-029` for what a skill does with the
  project it reads.
- One file, one class. A second class in a file is not autoloadable under PSR-4,
  so it works until somebody uses it from anywhere else and then fails as a
  missing class — held by `StructureTest::everyFileDeclaresOneClass`.
- **A unit test holds a small part and stubs what is outside it** — `R-COD-003`.
  It starts nothing and arranges nothing on the machine: no console, no
  container, no request, no waiting on something being up, and no executable
  written into a temporary directory and put on the `PATH`. What the code
  reaches outside through is a seam a caller replaces —
  `TYPO3\DevCompanion\Process\CommandRunner` for a command or an executable
  lookup, handed in with `Typo3Cli::useRunner()`, `Environments::useRunner()` or
  `Checkouts::useRunner()`, and `Todo::useDirectory()` for a queue to write
  into. Where a class has no such seam, making one is part of the work. The
  double is PHPUnit's — `self::createStub()` where it only has to answer,
  `self::createMock()` where the call itself is the assertion. Several inputs to
  one behaviour are a `#[DataProvider]` with a named case each, so a failure
  names the input. `tests/Smoke/` is where a subprocess is the subject, and what
  it starts is this repository's own CLI. `D-COD-004` has the reasoning,
  including why no test polices this one.
- A directory is read with `symfony/finder`, whatever the depth — held by
  `StructureTest::everyDirectoryIsReadThroughTheFinder` and stated in
  `D-COD-003`. A directory that may be absent is guarded with `is_dir()`,
  because Finder throws where `glob()` returned nothing.
- Every entrypoint is driven by a test that goes through it. `tests/Unit/`
  reaches a class at a time, which is where a command can be held to its rules
  and still be unreachable: what it reads is resolved from where its own file
  sits, and moving that file is not something any of those tests goes past. Both
  binaries have such a test, and a third would need one — held by
  `StdioServerTest`, `EntrypointTest` and `UpkeepTest`.
- `tests/Unit/` covers the searching, ranking, and rendering logic;
  `tests/Contract/` holds every tool to its declared schemas and annotations, on
  a hit and on a miss, and to the naming schema; `tests/Smoke/` drives both
  entrypoints as subprocesses — `bin/typo3-dev-companion` over JSON-RPC,
  `bin/cli` by its reading commands.
- `src/Upkeep/Command/` and the console are held to each other by
  `UpkeepCommandTest`: every class in the directory is registered, the
  application carries no command that is not one of them, each is named
  `<subject>:<verb>` and describes itself, and what a command declares on the
  parameters of its `__invoke` is what the console binds. That last one is the
  quiet failure — the console reads those parameters at one moment only, and a
  command it stops asking keeps every argument in its signature while refusing
  the caller who passes one.
- Every path this repository writes between its own files resolves —
  `bin/cli links:check`, and `LinksTest` so a rename that misses a reference
  fails the suite rather than the next reader. The anchor is not held, because a
  heading moves and the link still lands on the page. One dead link has a repair
  this repository knows — a feedback that was archived after somebody linked to
  it — and `bin/cli links:repair` writes it, on the branch and in `todo:home`
  after the rebase (`D-DOC-064`).
- **A test that holds a decision or a requirement declares it**:
  `#[Decision('D-DOC-048')]` and `#[Requirement('R-COD-003')]` over the method
  it is held by, or over the class where the whole class is the answer.
  `bin/cli decisions:cover` and `bin/cli requirements:cover` write that entry's
  `coveredBy` or `heldBy` from them — the attribute is the source and the front
  matter is the copy, so a renamed test rewrites the entry instead of orphaning
  a name in it. The checks fail on a copy that says anything else and on an
  attribute naming an id no entry has, and a failing test prints every entry
  that rested on it — `D-DOC-048`, `D-DOC-049`. A revoked decision is declared
  by nothing: when one is revoked, the attribute moves to the entry that revoked
  it — `D-DOC-052`.
- **What is written about the code you are about to change** is
  `bin/cli entries:lookup <path>`: the entries naming the classes at that path,
  and the tests that hold them. The attributes answer from the failing end; this
  is the call before the change rather than after it — `D-DOC-050`.
- **A test asserts the demand, not its absence.** Where a requirement says what
  must be there, the assertion says it too:
  `ScopeTest::everyDescriptionOfTheServerNamesAllThreeAudiences` reads the three
  places a reader meets the server whole. A negative assertion is what is left
  where the affirmative cannot be stated over the population — the same
  requirement's other test reads three hundred surfaces, and a hint about
  backend CSS names the core and nothing else, correctly.
- A behaviour worth a rule in `knowledge/` is worth a test: ranking that must
  prefer one match over another, an answer that must say "no match" instead of
  guessing, a catalog field that must stay usable.
- `FeedbackTest` writes real feedback below `feedback/` and removes them again.
  A leftover file carries `phpunit-feedback-fixture` in its text.

`bin/cli checkouts:update` creates the core checkouts a knowledge change is
verified against:
[documentation/contributing/working-on-the-server.rst](documentation/contributing/working-on-the-server.rst).
`bin/cli environment:create E-SITE` makes the other kind — a DDEV project with
TYPO3 installed in it, below `.environments/` and gitignored the same way, for
the half of this server that needs an installation to answer from. It is the
environment and never the subject of a recorded review (`D-EVI-004`).

## Feedback workflow

Agents using this server record improvement feedback through
`typo3_feedback_record`, one markdown file per feedback below `feedback/`.

**The channel is a development tool for building this server, not part of using
it.** `Channel::isAvailable()` offers the two tools only where this package is
the Composer root package, so a project that installed the server as a
dependency never sees them. That is also why `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`
does not reach them — `R-SCO-009` names them beside `typo3_server_scope` as what
a caller cannot take away.

`scenarios/` holds the sessions those came out of, so they can be run again. A
prompt names a kind of project, never one installation on somebody's machine —
that lives in `todo/reference/`, where it can go stale without taking a case
with it.

- A feedback arriving brings its card with it: `typo3_feedback_record` writes
  `todo/open/<the id derived from the feedback>.md` beside the report. A
  feedback that got here some other way — added by hand, or its card deleted
  while it stayed open — is reported by `bin/cli todo:check` and by CI, and the
  repair is a card written into `todo/open/` by hand.
- A feedback is worked off in a commit that both implements the improvement
  **and** archives it with `bin/cli feedback:archive <feedback>`, so `feedback/`
  only ever holds open items and the commit that moved it is the record of what
  came of it.
- Never mark one done by editing its `status:` front matter, and never archive
  one that was only partially addressed — trim it to the part that is left.
- Nothing is deleted from the archive. A feedback is a session's report about
  this server, which is evidence nothing else here holds.
- What outlives it goes to three directories, and none of them is the feedback's
  own: `requirements/` for what must be true from now on and what holds it
  there, `decisions/` for what a change rested on and what would show it wrong,
  `todo/` for the order of the work.
- Where what the session found unclear is a **structure** rather than a
  statement — in which order the steps go, what a thing consists of — the answer
  is a document instead: `knowledge/documents/` where the caller needed it,
  `documentation/` where a session working here did. A structure written as a
  requirement is one sentence saying the shape should be clear.
- Three states mean unfinished — a requirement marked **open**, one held by
  `not guarded`, a decision still `open` whose **Wrong if** nobody has been back
  to. All three are legitimate, so no check may fail on them, and
  `bin/cli unresolved:list` reads them out instead.

How each of those is carried out — judging a feedback, what each of the three
files holds, and what `bin/cli unresolved:list` reports:
[documentation/records/readme.rst](documentation/records/readme.rst). The prompt
that gets a feedback out of a session this repository cannot read, and why it
asks what it asks:
[documentation/records/asking-for-a-debrief.rst](documentation/records/asking-for-a-debrief.rst).
What a requirement is and what its three states mean:
[documentation/records/requirements.rst](documentation/records/requirements.rst),
and the sections one is written in:
[documentation/records/writing-a-requirement.rst](documentation/records/writing-a-requirement.rst).
What a decision carries that a commit message cannot, and what `open`,
`confirmed` and `revoked` promise a reader:
[documentation/records/decisions.rst](documentation/records/decisions.rst),
and the sections one is written in, with what a later session adds to the foot:
[documentation/records/writing-a-decision.rst](documentation/records/writing-a-decision.rst).
Running a forward review, judging it, and reading one that stopped without an
error:
[documentation/records/forward-runs.rst](documentation/records/forward-runs.rst).
What each kind of scenario is for: [scenarios/readme.md](scenarios/readme.md).

## What describes this server to someone else

Four things describe this server outward, and they ship with the code. A change
that leaves any of them wrong is not finished: a stale one is not a
documentation debt, it is a lie the server tells its callers.

- `documentation/readme.rst` — what the server is and what it will not do, and
  the page the site opens on. Its paragraphs are a promise; when a capability
  changes what the server may touch, that promise is the first thing that
  becomes false. `readme.md` at the root is the landing page for somebody
  arriving at the repository, and repeats only the title, the experimental note
  and the covered lines.
- `knowledge/server-scope.json` — `covers`, `doesNotCover`, `routing`, and the
  `instructions` clients receive at initialize time. A new tool belongs in
  `covers` and in `routing`; a boundary that moved belongs in `doesNotCover`.
- `AGENTS.md` — the layout list and the rules here, including this one.
- Every tool `description` and `outputSchema` in `src/`, which is the only
  documentation a client actually reads.

Some of it is already guarded: `ScopeTest` holds the scope and the tool list to
each other in both directions, and `ToolNamingTest` holds every tool name
written in `knowledge/`, in a skill, or in a rendered answer to the registry.
Those catch a name going stale, not a sentence going false. Prose is on you.

A skill is installed into somebody else's project, where a stale name is not
corrected by the next release of this server. So it is written under rules of
its own — what it is named and routed by, what it may state, what it leaves to
the tool that owns it, and what has to be shown before a domain becomes one at
all:
[documentation/contributing/writing-a-skill.rst](documentation/contributing/writing-a-skill.rst),
where every rule names the test that holds it.

Before committing, reread the paragraphs your change touches rather than
searching for a keyword. The sentence that goes wrong is usually the general one
written before the exception existed, and it will not contain the word you would
grep for.

## Commits

A commit message is read by a person who wants to know what the commit did.
Write it for them: plain English, and only as long as that answer needs. The
diff carries the detail, so the message does not have to.

- Split changes into small, single-purpose commits and commit as soon as each
  part is verified.
- The subject is a keyword and then what the commit did, in plain words:
  `[TASK]`, `[BUGFIX]` or `[FEATURE]`, and none other. A documentation change
  here is a `[TASK]` — `[DOCS]`, `[SECURITY]` and `[!!!]` belong to the core's
  process, which this checkout does not run.
- The whole subject line, keyword included, is under 52 characters where it can
  be and never past 72; the body wraps at 72. Those are the two severities
  `typo3_commit_message_guide` returns for `workflow="project"` — 52 is what it
  prefers and 72 is what it fails on, and `D-DOC-013` measured what this
  repository writes against both. The 80 `bin/cli prose:format` wraps at belongs
  to the markdown corpus and reaches no commit message.
- The body says only what the diff cannot: what was measured, what was rejected,
  what the change rests on. Where a decision or a requirement already carries
  that, the body names the id instead. A body that summarises the entry beside
  it is two copies of one reading, and the file is the one a reader searches.
- The prose rule holds here as everywhere: one point per sentence, no sentence
  restating the one above it. What it does not ask for is density: a subject
  nobody understands without the diff open is too short, not short enough.
  Nothing measures a commit message, so all of it is held by rereading it before
  `git commit`.
- **No trailer names the tool or the session that wrote the commit.** Not
  `Co-Authored-By: Claude`, not `Claude-Session:`, not a generated-with line. An
  agent's client instructs it to add them, and this file overrides that
  instruction. The commit is authored by the person it is committed under, and a
  session link resolves for the one client it came from.
- Only commit the files you changed yourself in this session. The working tree
  may already contain unrelated modifications or staged changes from someone
  else — leave them alone.
- Stage explicitly with `git add <path>`; never `git add -A`, `git add .`,
  `git commit -a` or any other blanket staging. Check `git status` and
  `git diff --staged` first, and `git restore --staged <path>` anything you did
  not change.

## Knowledge base

Three audiences read what is written here: core contributors, extension authors,
and site developers — and the same person is often two of them in one checkout,
because extensions are developed inside site installations. All three are served
deliberately, so knowledge that holds only for core contribution is written as
core-only rather than as the rule, and knowledge that holds only from one TYPO3
version says so; see the audience requirements in `requirements/audience/`.

In the code and in every payload that is one word, `scope`: the
`Knowledge\Scope` enum, whose cases are `core`, `project`, `extension`, `any`
and `uncertain`. A statement in `knowledge/` declares one, a path is placed in
one, and nothing else says the same thing under another name — `D-KNW-005`.
Audience stays the word for the idea, in `requirements/audience/` and in prose;
`scope` is the word anything machine-readable uses.

- **Everything below `knowledge/` is written in English**, and so is every query
  that reaches it. That is a property of the matcher rather than a preference:
  matching is lexical, so a query in another language reaches only the loanwords
  the two share. The server tells the calling agent to translate — in the
  `instructions` it sends at initialize, in `typo3_server_scope`, and on the
  free-text parameters of the tools that match against prose. A German sentence
  in a hint is a statement nothing can find.
- Everything the tools answer from lives below `knowledge/`, with one exception:
  facts owned by an installation are read from that installation, because no
  bundled answer could be right for it. Runtime registries use `Typo3Cli` where
  TYPO3 exposes a command and `Typo3Runtime` where it does not — that boots the
  installation in a subprocess and asks its container, which is the only source
  that knows what a package registers dynamically. Component contracts and the
  fallbacks read the files those packages ship without executing them. An answer
  that came from the files where the container was meant to answer says so and
  says what it leaves out: a **failsafe** container is core-only and looks
  complete, so it is never handed on. Add to `knowledge/` by default; reach for
  the installation only when the answer genuinely depends on which packages and
  TYPO3 version are active. The order those three are asked in, how the probe is
  delivered, and what a fallback owes the caller:
  [documentation/server/asking-the-installation.rst](documentation/server/asking-the-installation.rst).
- The installation is never derived from `getcwd()` on its own. `Instance` walks
  up from a directory it was handed, keeps it private and null until then, and
  `Server\Entrypoint` is the only thing that hands one in — a request-serving
  endpoint has no such relationship to its callers, and its document root may
  itself sit inside an installation. Naming the root with
  `TYPO3_DEV_COMPANION_ROOT` is a decision rather than a derivation and holds
  everywhere.
- Never load an installation into this process. `Typo3Cli` shells out, so its
  autoloader, its dependencies, and its PHP version stay on the other side of a
  process boundary and a failure is an exit code rather than a dead session.
- Never start something on the caller's machine as a side effect of a lookup. A
  stopped DDEV project is reported with the command that would fix it.
- Add new rules or scripts to `knowledge/` first; promote recurring workflow
  logic to a tool only when it has earned it. **What earns it is the round trips
  it takes off the caller.** A session is charged one context per call
  (`D-FBK-020`), so a question that costs it four calls and a trap — the Forge
  issue that answers 403, then 200 with a challenge page, then JSON whose
  decision sits in a field nobody would guess — is worth a tool that answers it
  in one. The cost moves here permanently and that is the trade rather than the
  objection. What does not earn it is a fact the caller reads once from its own
  checkout, and anything whose lookup would report `unavailable` often enough
  that the call buys nothing (`D-FBK-027`).
- Verify facts against the core checkouts below `.checkouts/` before writing
  them into `knowledge/`, and bind what does not hold on all of them. The
  checkouts are this repository's own — one worktree per covered version,
  created and updated by `bin/cli checkouts:update`, gitignored and re-fetchable
  at any time. Verifying against whatever checkout happens to be on the machine
  makes the evidence unreproducible for the next person. A statement whose
  subject is `typo3/testing-framework` is verified there too, in
  `.checkouts/testing-framework/<line>`, which the same command keeps.

### Which versions an answer holds for

The knowledge base covers more than one TYPO3. A statement that does not hold on
all of them says so **as data, not as prose** — `since` and `until` on the
statement, never a version number in the sentence, which `HintsTest` enforces. A
bound statement is verified on both sides of its boundary and the commit message
names both branches; that is evidence nobody can reconstruct later. Which
versions are covered is declared in `knowledge/versions.json` and nowhere else.

The mechanism exists because the alternative is worse: a caller on an LTS given
a `main` answer changes code that then fails at runtime, and the failure is
silent. What follows from it — where the binding sits, what belongs in `hints`
rather than `checks`, `binding: "core"`, and why the catalogs withhold an entry
instead of qualifying it — is in
[documentation/server/versions.rst](documentation/server/versions.rst).
