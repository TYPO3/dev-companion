# Work on this repository

## What comes first

**What a person cannot understand is a defect, and so is what a machine cannot
read.** A session reports and fixes both like any other defect, and neither is a
matter of taste.

Everything here has both readers, and where the two pull apart the writer serves
both. A name is one a person understands and a check can read. A title scans in
a listing and parses as data. An answer's text says what its schema says. The
rest of this file states what that means in each corner. Where one of these
rules makes something harder to understand, the rule is wrong.

Each of these is a defect on its own. A file name nobody reads at a glance, a
sentence that does not end, a function name with three clauses. A formulation is
short, precise and in plain words. [Prose](#prose) states the rule, and
`bin/cli prose:check` measures it.

Two more rules follow, and each is worth its own statement:

- **Data lives where data lives**, and the writer says it once. A title, a test
  that holds an entry, the version that bounds an answer: each is data. Where a
  reader has to parse one out of prose, somebody writes a regex again and gets
  it right again.
- **Where a person has to guess what something means, that is the defect**, even
  where nothing fails on it. A title that repeats its statement, an id that
  arrives only in a docblock, a file name that claims what the file no longer
  says. Each costs the reader the work the writer saved.

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
src/Upkeep/Voice.php # what every command prints in: the heading, the row, the verdict, the problem, the note and the bar
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

Every class below `src/` sits in the group it belongs to. A class that fits no
group needs a group nobody has named yet, and it does not sit at the root. An
answer comes from five groups: `Knowledge/`, `Installation/`, `Manual/`,
`Contribution/` and `Publication/`. The last three reach outside this process,
and their tools' `openWorldHint` says so. `Paths` sits loose because the server
and the upkeep both stand on it.

Both binaries have the same shape: they locate the autoloader and hand the
arguments to the class that owns them. That class is `Server\Entrypoint` for one
and `Upkeep\Cli` for the other, by way of the console application it builds. A
command declares what it does, its name and what it takes beside the code that
does it, never in `bin/`.

## Where a session starts, and what it owes the next one

    bin/cli todo:next

That is the whole of it. The command prints **one todo**: the first that is due,
whole, with its own command already run. `bin/cli todo:list` prints the
overview.

The todo it prints is a claim, not an instruction. One session wrote its belief
about where to start, before the work it describes, and that session has left.
So the reader checks the todo against what the repository does today. Where that
check finds the step done, impossible or two steps, the session corrects the
file before the work rather than after it.

A session **settles** a question the step turns on and never recalls it. What
the session cannot establish here is a result, and the session records it as
one. The todo shrinks to what is still open, the requirement says `not guarded`,
the decision says what evidence would settle it. None of those can absorb a
guess written with the confidence of a fact, because nothing afterwards can tell
the two apart.

A session **asks** what has no source here, before the change rather than in the
commit that presents it. Ask after you have read: give the options, what each
costs, and a recommendation. Offer to put the todo back, which is a better
outcome than a step done on a guess. The answer goes into `todo/` or
`decisions/`, because an answer that lives only in the conversation ends with
it.

The agent's own memory is not a place either. A client's private memory is not
where this repository keeps anything. What a session establishes goes into
`todo/`, `requirements/`, `decisions/` or `documentation/`, where the next
session reads it and can correct a wrong line.

A session keeps [todo/](todo/readme.md) current as part of the work, not as a
step after it.

[documentation/records/working-a-todo.rst](documentation/records/working-a-todo.rst)
says what a session reads before the first change, how it settles a question,
what it asks and what the file says afterwards.
[documentation/records/readme.rst](documentation/records/readme.rst) says how
`next` decides what is due, and how the work moves between `feedback/`,
`requirements/`, `decisions/` and `todo/`.

Several sessions at once use `bin/cli todo:claim <n>`, one worktree and one
branch each, and `bin/cli todo:home <id>` brings one of them back.
`bin/cli todo:drop <id>` gives one back unmerged. All three carry their step out
rather than print it, so none of them is a reason to read anything first.

The worktree that stands on a todo's branch says that somebody has that todo in
hand. So `todo:next` in that worktree hands over that todo rather than the front
of the queue. Every command that hands out work passes over it.
[documentation/records/working-todos-in-parallel.rst](documentation/records/working-todos-in-parallel.rst)
says what that costs and what still collides.

## How a session reads

A session pays one context per call, not one per token. The number of calls is
the budget, and what one call returns is nearly free — `D-FBK-020` holds the
counts.

- **Send the calls that do not depend on each other together.** At the start,
  most of what a session reads turns on nothing before it.
- **Reach for a file with the client's own file and search tools.** One call
  answers what `cat`, `sed`, `grep` and `ls` spell out in four.
- **Open a file once, whole.** A window into a file the session opens again
  afterwards costs two calls for one read.

None of that licenses a session to read less. The session still checks the step
against what the repository does today. It still settles a question from a
source — in fewer calls, not from memory.

## What things are called

One thing, one word. Where two words compete, the one somebody outside this
checkout can see wins: a tool name, a directory name, a CLI subject. Clients
installed months ago and paths people wrote down know those, while a writer can
rewrite prose this afternoon. A metaphor invented for one sentence is a second
word for something that has one. Everyone who reads it has to decode it, and it
names nothing anybody can search for.

**A name carries one claim, and names its own subject.** A reader meets a name
where nothing else stands. That is a failure list, a directory of hundreds of
entries, a listing in a client. So a reader who has not opened the file has to
know from the name what it means. A name stops that in three ways, and each is a
defect:

- **Two claims in one name.**
  `everyLineIsSetUpOnAFileRatherThanOnAContainerOfItsOwn` states a case and the
  case it is told apart from; the docblock owns the second half.
  `bin/cli prose:check` counts the names and the titles that do this, worst
  first.
- **A subject the name never says.** "Three audiences, not one" counts without a
  name for what it counts. "Activation is the client's" refers to something only
  the body introduces. The reader gets the shape of a statement and none of it.
- **A negation where the writer means the affirmative.** A reader reads "What
  the scope excludes is not what the server answers" twice. The reader reads "A
  subject the not-covered list omits is in scope" once. That is
  [Prose](#prose)'s "say what is, not what it is not", applied where a reader
  meets it most.

A directory below `src/` has a **singular** name, for what one thing in it is.
`Tool/` holds one tool per class, `Command/` one command, `Manual/` one manual.
The plural splits every name in two: a class lands in `Commands/` and the code
refers to it as a command all the way down. The directories outside `src/` keep
the names their callers know them by.

What arrives through `typo3_feedback_record` is **a feedback**, countable.
[documentation/contributing/glossary.rst](documentation/contributing/glossary.rst)
defines that word and the words around it. Two of them go wrong reliably.
**Record** is the verb it arrives by and never a noun, because in TYPO3 a record
is a row in the database. **Verdict** belongs to `scenarios/`, where it says how
a run came out, while what becomes of a feedback is its **answer**.

## Less is more

Every task is also an occasion to leave the code smaller than it was. A change
ends when what it added is there **and** what it made unnecessary is gone.

- Before you write an abstraction, look for the one the change makes redundant.
  Two answers of the same shape share a formatter. Delete a branch nobody takes
  rather than keep it for symmetry.
- Prefer the change that removes a concept to the change that adds one. A
  parameter that always takes the same value is not a parameter, and a helper
  with one caller is that caller.
- Code written to be general is speculation until the second caller exists. The
  second caller also shows what the two have in common.
- A deletion needs no feature to justify it. A simplification that stands on its
  own is its own commit. A review of code nobody has touched in a while is a
  legitimate task.
- Shorter is not the same as denser. Fewer concepts, fewer branches, fewer parts
  — not fewer lines wrung out of the same logic.

None of that licenses a smaller API. Where a framework offers several ways in,
take the one its own documentation and tests treat as the main path. Take it
even where that means more files. A cheaper variant that needs an extra nudge to
behave is not the smaller change: the nudge is the added concept. Say which way
in is the main one and which one you propose; do not decide it for brevity.

## Prose

Short and precise, everywhere: `knowledge/`, the tool descriptions, this file,
`documentation/`, a commit message. Every reader pays per token, and half of
them are machines.

This repository writes in ASD-STE100, Simplified Technical English —
`D-DOC-070`. The rules below are the STE rules this corpus keeps, with the ones
this repository adds. They hold whatever the markup.

`documentation/` is reStructuredText because the site publishes it and a
reference into another page has to resolve. Everything else here is markdown —
`D-DOC-029`. Inside `documentation/` a name is a double-backtick literal and
another page is `:doc:`. A place in a page is `:ref:` with a label above the
heading it names. A path that leaves the tree is an embedded link `Site`
rewrites to GitHub.

- One point per sentence. Delete a sentence that restates the previous one in
  other words; do not shorten it.
- A sentence has at most 25 words, and at most 20 in a procedure. `skills/` and
  `knowledge/documents/` are the procedures. A longer sentence is two points.
- A sentence names who acts: the test, the check, the session, the caller, the
  server. "The rule holds" says nothing about what holds it.
- A verb stands in the present tense and in a finite form. The -ing form is for
  a name (`working-a-todo.rst`) and for nothing else. "A session settles the
  question", not "the settlement of the question is the session's".
- A paragraph has at most six sentences and opens with its topic.
- A procedure gives one instruction per sentence. "Run the test, then read the
  log" is one step with an order in it, and STE permits that.
- A word has one meaning. The
  [glossary](documentation/contributing/glossary.rst) is the dictionary, and a
  word this repository uses in a sense of its own stands there.
- The half before a colon stands on its own. What follows is a list, a
  definition or the reason. Where the first half is only a setup, the colon
  delivers the point and the sentence is two.
- The rule first, the reason after it, and only where the reason is not obvious.
  A justification nobody disputes is filler.
- One example, where the point needs one at all. The second one rarely adds a
  case and always adds a paragraph.
- Say what is, not what it is not. A list of what something is not belongs where
  the confusion happened.
- Length is a symptom. A paragraph that does not come out short is two points,
  or one point the writer has not understood yet.
- A longer correct sentence beats a short broken one. Where the short form does
  not parse, take the words back.
- Name the place rather than point at it. "Above" and "below" describe a file
  somebody has edited since. Name the section, the class or the file. "Below
  `src/`" is a path rather than a pointer.
- No count of something that grows. "34 files with 120 hints" is true on the day
  the writer writes it and wrong on the next commit. Nothing fails when it
  turns. Name the thing and the command that counts it ("one file per subject,
  which `bin/cli hints:coverage` counts"), or say "and many more". A number
  belongs where somebody measured it. A decision records what a sweep found on
  its date, and a report prints what is true when it runs.

A session weighs a standard from outside one rule at a time against this corpus
and does not adopt it whole. `D-DOC-069` reads the patch page's six rules that
way. `D-DOC-070` reads ASD-STE100 that way, and records that the maintainer then
adopted it whole, with the numbers in front of them.

### Comments

A comment earns its lines when it says what the code cannot. That is why this
and not the obvious alternative, what somebody measured, what breaks if somebody
changes it back. Delete everything else. The prose rules hold for a comment too.

- A comment that restates the line under it is noise. Where the code is unclear,
  fix the name, not a sentence that explains the name.
- No docblock that repeats the signature. `@param` and `@return` earn their line
  where they say an array's shape and nowhere else.
- The reason lives in one place. Where a decision or a requirement carries it,
  the comment names the id and does not retell it.
- Length is a symptom here too. Six paragraphs above a private method is usually
  a decision nobody has written down as one.

`bin/cli prose:check` counts what that costs: the sentences over the measure,
worst file first, and the sentences that carry a passive or an -ing form. It
fails on one of them, the bold sentence a requirement or a decision opens with.
A reader who stops after it must know what the entry settled. The rest is a
report. A long sentence can be the right one, and a rewrite driven by a counter
produces two short sentences that say what one said.

It counts the comments too, where what grew is how many there are rather than
how long each is. It prints the share of the PHP that is comment, and every
comment that names a decision and retells it anyway, most prose first. It skips
the delimiters, the blank lines and the annotations, so ten means ten lines
somebody wrote — `D-DOC-035`.

The last thing it counts is the tables with a cell no line fits. A cell that
does not fit on a line means the content is a list rather than a table. What a
table buys over a list is a column a reader can scan — `D-DOC-001`. It is a
report because only a reader can say whether a cell can shrink. The exception
has to say so where the writer takes it.

`bin/cli prose:format <path>` is the other half, and it rewrites rather than
reports. It rewraps the prose this repository writes about itself at the column
that prose already stands at. It serves the paragraph a rename left ragged. It
moves the line breaks and the padding of a table and nothing else, which
`ProseTest` asserts over the whole corpus. It leaves alone everything a break
means something in. The two markups disagree about which those are, so the
command asks the file which it is.

Named no path, it sweeps what nobody has in hand. In a worktree that is the
files the branch changed. In the checkout `main` stands on, it is the corpus
minus what the standing claims changed — `D-DOC-063`. The second of those is a
diff to look at before it is a diff to make.

The command pads a table to the width of each column's widest cell. So a reader
can scan a column in the file as it stands, not only where something renders it.
Both forms render the same, which made the compact one look like the cheaper
choice.

## Tool names

Every tool has the name `typo3_<subject>_<verb>`. The prefix never varies, the
subject is what the tool is about, and the verb comes from a closed list of six.
The verb tells a caller which shape the answer has:

- `lookup` — a query goes in, the entries that match come out, and an empty
  result is a legitimate answer: `typo3_component_lookup`, `typo3_rule_lookup`.
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

Nothing here writes into the TYPO3 installation it reads, and that boundary is
what "read-only" means throughout this repository. `record` is the other kind of
write and says so, because one word for both reads as a hole in the posture —
`D-FBK-042`.

`scope` and `describe` are the pair a caller confuses. A scope answers for a
source and states the boundary of what a caller can ask it. A describe answers
for one thing the caller named and states what that thing is. `D-SCO-010` says
why the two project and extension tools carry the second.

**A new tool names the tool a caller might have called instead**, and that one
names it back. Which two those are comes from a read of the descriptions rather
than from anything the declarations say. `D-ANS-072` measured what a check over
`covers` would have proposed. So the pair is a step of the work that adds a
tool, and a row in `ScopeTest::theToolsACallerCannotChooseBetweenNameEachOther`
holds it.

A new tool takes the verb whose answer shape it has, and two tools that share an
output schema share their verb. When none of the six fits, the tool probably
does two things at once: split it before you invent a seventh verb. If you need
a seventh, add it to `ToolNamingTest` in the same commit, so that list stays the
only place that defines the vocabulary.

Leave `core` out of a name. This server is about the TYPO3 core throughout, so
the segment separates nothing.

A tool is one class in `src/Tool/`, and it implements
`TYPO3\DevCompanion\Tool\Tool`. Its name, what it takes, what shape it answers
in, and the answer itself stand in one file. So a description cannot go stale
against the answer it describes unless somebody edits the two apart.
`TYPO3\DevCompanion\Tools` is the list of them, and the only place that switches
a tool on. Nothing else belongs below `src/Tool/`. What more than one tool
builds its answer from is `TYPO3\DevCompanion\Result\`, and the adapters onto
`mcp/sdk` are `TYPO3\DevCompanion\Sdk\`.

The word is the protocol's: an MCP tool is what the SDK declares as
`Mcp\Schema\Tool`, beside `Prompt` and `Resource`. Nothing here is a "server
tool".

Every tool returns a `ToolResult`: the text plus the same answer as data. The
data half is a contract. A client may validate it against the `outputSchema()`
the tool declares. So a field a schema requires has to be present on every path
through the tool, misses included. Add fields rather than rename them. A record
shape more than one tool answers with belongs in
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

- That command writes the JSON below `knowledge/`, and nothing else does. The
  form is PHP's pretty print at the indentation `.editorconfig` states, slashes
  and unicode as the writer typed them, key order untouched. `JsonTest` fails on
  a file that is not in the form. `.editorconfig` is the one place that states
  an indentation; `StructureTest` holds the PHP one to php-cs-fixer's.
- The guidelines are php-cs-fixer's own, and `.php-cs-fixer.dist.php` declares
  them: PER-CS 3.0 and the few rules on top of it this repository writes by. Add
  a rule there when the code already follows it and the fixer is what keeps it
  followed. Do not add one to introduce a style nobody has written in yet: that
  is a reformat of the whole tree in a rule's clothes.
- **A session checks a version this repository pins against the day's release**
  when it touches the file that carries it. Those files are node and the actions
  in `.github/workflows/`, the libraries in `composer.json`, and a
  `.ddev/config.yaml` where a checkout has one. The session raises one that is
  behind, or writes the reason it stays beside it. It asks before a raise nobody
  has established as safe.
- Where a runtime this package declares rules the newest release out, the pin
  takes the newest version that declaration allows. `D-COD-007` records PHPUnit
  as that case. No file here names the version a caller must use, because that
  number moves and nothing fails when it does — `R-COD-004`. `R-ANS-037` says
  what an answer may name and `R-SKL-029` what a skill does with the project it
  reads.
- One file, one class. A second class in a file is not autoloadable under PSR-4.
  It works until somebody uses it from anywhere else, and then it fails as a
  class PHP cannot find — `StructureTest::everyFileDeclaresOneClass` holds it.
- **A unit test holds a small part and stubs what is outside it** — `R-COD-003`.
  It starts nothing and arranges nothing on the machine: no console, no
  container, no request, no wait on something to come up. It writes no
  executable into a temporary directory to put on the `PATH`. The code reaches
  outside through a seam a caller replaces.
  `TYPO3\DevCompanion\Process\CommandRunner` is the seam for a command or an
  executable lookup, handed in with `Typo3Cli::useRunner()`,
  `Environments::useRunner()` or `Checkouts::useRunner()`.
  `Todo::useDirectory()` is the seam for a queue to write into, and a class
  without a seam gets one as part of the work.
- The double in a unit test is PHPUnit's: `self::createStub()` where it only has
  to answer, `self::createMock()` where the call itself is the assertion.
  Several inputs to one behaviour are a `#[DataProvider]` with a named case
  each, so a failure names the input. `tests/Smoke/` is where a subprocess is
  the subject, and what it starts is this repository's own CLI. `D-COD-004`
  gives the reasons, and says why no test polices this one.
- The code reads every directory with `symfony/finder`, whatever the depth —
  `StructureTest::everyDirectoryIsReadThroughTheFinder` holds it and `D-COD-003`
  states it. Guard a directory that may be absent with `is_dir()`, because
  Finder throws where `glob()` returned nothing.
- A test drives every entrypoint and goes through it. `tests/Unit/` reaches a
  class at a time, and there a test can hold a command to its rules while nobody
  can reach the command. The command resolves what it reads from where its own
  file sits, and none of those tests notices a move of that file. Both binaries
  have such a test, and a third would need one — `StdioServerTest`,
  `EntrypointTest` and `UpkeepTest` hold them.
- `tests/Unit/` covers the search, the ranking and the render logic.
  `tests/Contract/` holds every tool to its declared schemas and annotations, on
  a hit and on a miss, and to the naming schema. `tests/Smoke/` drives both
  entrypoints as subprocesses: `bin/typo3-dev-companion` over JSON-RPC,
  `bin/cli` by the commands that only read.
- `UpkeepCommandTest` holds `src/Upkeep/Command/` and the console to each other.
  The console registers every class in the directory and carries no command that
  is not one of them. Each command has a `<subject>:<verb>` name and describes
  itself. What a command declares on the parameters of its `__invoke` is what
  the console binds. That last one fails quietly, because the console reads
  those parameters at one moment only. A command it stops asking keeps every
  argument in its signature while it refuses the caller who passes one.
- Every path this repository writes between its own files resolves.
  `bin/cli links:check` reads them, and `LinksTest` fails the suite on a rename
  that misses a reference, before the next reader does. The test skips the
  anchor, because a heading moves and the link still lands on the page. One dead
  link has a repair this repository knows: a feedback somebody archived after
  somebody else linked to it. `bin/cli links:repair` writes that repair, on the
  branch and in `todo:home` after the rebase (`D-DOC-064`).
- **A test that holds a decision or a requirement declares it**. It carries
  `#[Decision('D-DOC-048')]` or `#[Requirement('R-COD-003')]` over the method
  that holds it, or over the class where the whole class is the answer.
  `bin/cli decisions:cover` and `bin/cli requirements:cover` write that entry's
  `coveredBy` or `heldBy` from them. The attribute is the source and the front
  matter is the copy. So a renamed test rewrites the entry instead of an
  orphaned name in it.
- The cover checks fail on a copy that says anything else and on an attribute
  that names an id no entry has. A test that fails prints every entry that
  rested on it — `D-DOC-048`, `D-DOC-049`. Nothing declares a revoked decision:
  when a session revokes one, the attribute moves to the entry that revoked it —
  `D-DOC-052`.
- **`bin/cli entries:lookup <path>` prints what the records say about the code
  you are about to change.** That is the entries that name the classes at that
  path, and the tests that hold them. The attributes answer from the end that
  fails; this is the call before the change rather than after it — `D-DOC-050`.
- **A test asserts the demand, not its absence.** Where a requirement says what
  must be there, the assertion says it too.
  `ScopeTest::everyDescriptionOfTheServerNamesAllThreeAudiences` reads the three
  places a reader meets the server whole. A negative assertion is what remains
  where nobody can state the affirmative over the population. The same
  requirement's other test reads three hundred surfaces, and a hint about
  backend CSS names the core and nothing else, correctly.
- A behaviour worth a rule in `knowledge/` is worth a test. A rank that must
  prefer one match over another earns one. So does an answer that must say "no
  match" instead of a guess, and a catalog field that must stay usable.
- `FeedbackTest` writes real feedback below `feedback/` and removes them again.
  A leftover file carries `phpunit-feedback-fixture` in its text.

`bin/cli checkouts:update` creates the core checkouts a session verifies a
knowledge change against:
[documentation/contributing/working-on-the-server.rst](documentation/contributing/working-on-the-server.rst).
`bin/cli environment:create E-SITE` makes the other kind: a DDEV project with
TYPO3 in it, below `.environments/` and gitignored the same way. It serves the
half of this server that needs an installation to answer from. It is the
environment and never the subject of a recorded review (`D-EVI-004`).

## Feedback workflow

Agents that use this server record improvement feedback through
`typo3_feedback_record`, one markdown file per feedback below `feedback/`.

**The channel is a development tool for the work on this server, not part of its
use.** `Channel::isAvailable()` offers the two tools only from the Composer root
package. So a project that installed the server as a dependency never sees them.
That is also why `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS` does not reach them:
`R-SCO-009` names them beside `typo3_server_scope` as what a caller cannot take
away.

`scenarios/` holds the sessions those came out of, so a session can run them
again. A prompt names a kind of project, never one installation on somebody's
machine. That lives in `todo/reference/`, where it can go stale without a case
that goes with it.

- A feedback brings its card with it: `typo3_feedback_record` writes
  `todo/open/<the id derived from the feedback>.md` beside the report.
  `bin/cli todo:check` and CI report a feedback that got here some other way.
  Somebody added it by hand, or deleted its card while it stayed open. The
  repair is a card somebody writes into `todo/open/` by hand.
- A session works a feedback off in a commit that both implements the
  improvement **and** archives it with `bin/cli feedback:archive <feedback>`. So
  `feedback/` only ever holds open items, and the commit that moved it is the
  record of what came of it.
- Never mark one done by an edit to its `status:` front matter. Never archive
  one you addressed only in part: trim it to the part that remains.
- Nobody deletes anything from the archive. A feedback is a session's report
  about this server, which is evidence nothing else here holds.
- What outlives it goes to three directories, and none of them is the feedback's
  own. `requirements/` takes what must be true from now on and what holds it
  there. `decisions/` takes what a change rested on and what would show it
  wrong. `todo/` takes the order of the work.
- Where the session found a **structure** unclear rather than a statement, the
  answer is a document. A structure is which order the steps go in, or what a
  thing consists of. The document goes into `knowledge/documents/` where the
  caller needed it, and into `documentation/` where a session here did. A
  structure written as a requirement is one sentence that says the shape should
  be clear.
- Three states mean unfinished: a requirement marked **open**, one with
  `not guarded`, a decision still `open` whose **Wrong if** nobody has been back
  to. All three are legitimate, so no check may fail on them.
  `bin/cli unresolved:list` reads them out instead.

[documentation/records/readme.rst](documentation/records/readme.rst) says how a
session judges a feedback, what each of the three files holds, and what
`bin/cli unresolved:list` reports.
[documentation/records/asking-for-a-debrief.rst](documentation/records/asking-for-a-debrief.rst)
holds the prompt that gets a feedback out of a session this repository cannot
read, and says why it asks what it asks.
[documentation/records/requirements.rst](documentation/records/requirements.rst)
says what a requirement is and what its three states mean.
[documentation/records/writing-a-requirement.rst](documentation/records/writing-a-requirement.rst)
gives its sections.
[documentation/records/decisions.rst](documentation/records/decisions.rst) says
what a decision carries that a commit message cannot, and what `open`,
`confirmed` and `revoked` promise a reader.
[documentation/records/writing-a-decision.rst](documentation/records/writing-a-decision.rst)
gives its sections, and what a later session adds to the foot.

[documentation/records/forward-runs.rst](documentation/records/forward-runs.rst)
says how a session runs a forward review, judges it, and reads one that stopped
without an error. [scenarios/readme.md](scenarios/readme.md) says what each kind
of scenario is for.

## What describes this server to someone else

Four things describe this server outward, and they ship with the code. A change
that leaves any of them wrong is not finished. A stale one is not a
documentation debt; it is a lie the server tells its callers.

- `documentation/readme.rst` — what the server is and what it will not do, and
  the page the site opens on. Its paragraphs are a promise. When a capability
  changes what the server may touch, that promise is the first thing that
  becomes false. `readme.md` at the root is the first page for somebody who
  arrives at the repository. It repeats only the title, the experimental note
  and the covered lines.
- `knowledge/server-scope.json` — `covers`, `doesNotCover`, `routing`, and the
  `instructions` clients receive at initialize time. A new tool belongs in
  `covers` and in `routing`; a boundary that moved belongs in `doesNotCover`.
- `AGENTS.md` — the layout list and the rules here, this one included.
- Every tool `description` and `outputSchema` in `src/`, which is the only
  documentation a client reads.

Tests guard some of it. `ScopeTest` holds the scope and the tool list to each
other in both directions. `ToolNamingTest` holds every tool name in
`knowledge/`, in a skill, or in a rendered answer to the registry. Those catch a
name that goes stale, not a sentence that goes false. Prose is on you.

A client installs a skill into somebody else's project, where the next release
of this server does not correct a stale name. So a skill has rules of its own.
They say what names and routes it, what it may state, and what it leaves to the
tool that owns it. They say what a session has to show before a domain becomes
one at all.
[documentation/contributing/writing-a-skill.rst](documentation/contributing/writing-a-skill.rst)
states them, and every rule there names the test that holds it.

Before you commit, reread the paragraphs your change touches rather than search
for a keyword. The sentence that goes wrong is usually the general one somebody
wrote before the exception existed. It will not contain the word you would grep
for.

## Commits

A person who wants to know what the commit did reads the commit message. Write
it for them: plain English, and only as long as that answer needs. The diff
carries the detail, so the message does not have to.

- Split changes into small, single-purpose commits. Commit as soon as you have
  verified each part.
- The subject is a keyword and then what the commit did, in plain words:
  `[TASK]`, `[BUGFIX]` or `[FEATURE]`, and none other. A documentation change
  here is a `[TASK]`. `[DOCS]`, `[SECURITY]` and `[!!!]` belong to the core's
  process, which this checkout does not run.
- The whole subject line, keyword included, is under 52 characters where it can
  be and never past 72; the body wraps at 72. Those are the two severities
  `typo3_commit_message_guide` returns for `workflow="project"`: it prefers 52
  and fails on 72. `D-DOC-013` measured what this repository writes against
  both. The 80 `bin/cli prose:format` wraps at belongs to the markdown corpus
  and reaches no commit message.
- The body says only what the diff cannot: what somebody measured, what they
  rejected, what the change rests on. Where a decision or a requirement carries
  that, the body names the id instead. A body that summarises the entry beside
  it is two copies of one thought, and the file is the one a reader searches.
- The prose rule holds here as everywhere: one point per sentence, no sentence
  that restates the one above it. It does not ask for density. A subject nobody
  understands without the diff open is too short, not short enough. Nothing
  measures a commit message, so you hold all of it by a reread before
  `git commit`.
- **No trailer names the tool or the session that wrote the commit.** Not
  `Co-Authored-By: Claude`, not `Claude-Session:`, not a generated-with line. An
  agent's client instructs it to add them, and this file overrides that
  instruction. The person whose name the commit carries authors it, and a
  session link resolves for the one client it came from.
- Commit only the files you changed yourself in this session. The tree may
  already contain unrelated modifications or staged changes from someone else.
  Leave them alone.
- Stage explicitly with `git add <path>`; never `git add -A`, `git add .`,
  `git commit -a` or any other blanket stage. Check `git status` and
  `git diff --staged` first, and `git restore --staged <path>` anything you did
  not change.

## Knowledge base

Three audiences read the knowledge here: core contributors, extension authors,
and site developers. The same person is often two of them in one checkout,
because people develop extensions inside site installations. The writer serves
all three on purpose. Knowledge that holds only for core contribution says
core-only rather than the rule, and knowledge that holds only from one TYPO3
version says so. The audience requirements in `requirements/audience/` state it.

In the code and in every payload the word is `scope`: the `Knowledge\Scope`
enum, whose cases are `core`, `project`, `extension`, `any` and `uncertain`. A
statement in `knowledge/` declares one, `Scope` places a path in one, and
nothing else says the same thing under another name — `D-KNW-005`. Audience
stays the word for the idea, in `requirements/audience/` and in prose; `scope`
is the word anything machine-readable uses.

- **Everything below `knowledge/` is English**, and so is every query that
  reaches it. That is a property of the matcher rather than a preference. The
  match is lexical, so a query in another language reaches only the loanwords
  the two share. The server tells the agent to translate in three places. They
  are the `instructions` it sends at initialize, `typo3_server_scope`, and the
  free-text parameters of the tools that match against prose. A German sentence
  in a hint is a statement nothing can find.
- Everything the tools answer from lives below `knowledge/`, with one exception.
  The server reads facts an installation owns from that installation, because no
  bundled answer could be right for it. Runtime registries use `Typo3Cli` where
  TYPO3 exposes a command and `Typo3Runtime` where it does not. `Typo3Runtime`
  boots the installation in a subprocess and asks its container, the only source
  that knows what a package registers at runtime. Component contracts and the
  fallbacks read the files those packages ship and execute nothing.
- An answer that came from the files where the container should have answered
  says so and says what it leaves out. A **failsafe** container is core-only and
  looks complete, so the server never hands it on. Add to `knowledge/` by
  default; reach for the installation only when the answer depends on which
  packages and TYPO3 version are active.
  [documentation/server/asking-the-installation.rst](documentation/server/asking-the-installation.rst)
  says in which order the server asks the three, how it delivers the probe, and
  what a fallback owes the caller.
- Nothing derives the installation from `getcwd()` on its own. `Instance` walks
  up from a directory somebody handed it, and keeps it private and null until
  then. `Server\Entrypoint` is the only thing that hands one in. An endpoint
  that serves requests has no such relationship to its callers, and its document
  root may itself sit inside an installation. `TYPO3_DEV_COMPANION_ROOT` names
  the root as a decision rather than a derivation, and it holds everywhere.
- Never load an installation into this process. `Typo3Cli` shells out, so its
  autoloader, its dependencies, and its PHP version stay on the other side of a
  process boundary. A failure is an exit code rather than a dead session.
- Never start something on the caller's machine as a side effect of a lookup.
  The server reports a stopped DDEV project with the command that would fix it.
- Add new rules or scripts to `knowledge/` first. Promote a workflow that recurs
  to a tool only when it has earned it. **What earns it is the round trips it
  takes off the caller.** A session pays one context per call (`D-FBK-020`). So
  a question that costs it four calls and a trap is worth a tool that answers it
  in one.
- The Forge issue is that question. It answers 403, then 200 with a challenge
  page, then JSON whose decision sits in a field nobody would guess. The cost
  moves here permanently, and that is the trade rather than the objection. A
  fact the caller reads once from its own checkout does not earn it. Neither
  does a lookup that would report `unavailable` so often that the call buys
  nothing (`D-FBK-027`).
- Verify facts against the core checkouts below `.checkouts/` before you write
  them into `knowledge/`, and bind what does not hold on all of them. The
  checkouts are this repository's own: one worktree per covered version, which
  `bin/cli checkouts:update` creates and updates, gitignored and re-fetchable at
  any time. A fact somebody verified against whatever checkout happens to be on
  the machine has evidence the next person cannot reproduce. A statement about
  `typo3/testing-framework` has its checkout too, in
  `.checkouts/testing-framework/<line>`, which the same command keeps.

### Which versions an answer holds for

The knowledge base covers more than one TYPO3. A statement that does not hold on
all of them says so **as data, not as prose**. It carries `since` and `until`,
never a version number in the sentence, and `HintsTest` enforces that. The
writer verifies a bound statement on both sides of its boundary, and the commit
message names both branches. That is evidence nobody can reconstruct later.
`knowledge/versions.json` declares the versions the knowledge covers, and
nothing else does.

The mechanism exists because the alternative is worse. A caller on an LTS who
gets a `main` answer changes code that then fails at runtime, and the failure is
silent. [documentation/server/versions.rst](documentation/server/versions.rst)
says what follows from it. It says where the bound sits and what belongs in
`hints` rather than `checks`. It says what `binding: "core"` is, and why the
catalogs withhold an entry instead of a qualification.
