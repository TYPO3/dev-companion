# Where every task starts

Every TYPO3 task starts in the same order. This file gives that order and the
rules for the evidence behind a finding. The skill that links here adds its own
steps after it.

Two words have a fixed meaning here:

- **checkout**: the repository you work in, with its files.
- **major**: a major TYPO3 version, such as one a package declares in its
  constraints.

In short:

1. Check that the server answers.
2. Make steps 1 and 3 of the order in every task. Make the other calls where the
   task needs them, in sequence.
3. Write down what they established.
4. Then read the checkout.
5. Before the first edit, name the files you will create, change or delete.
6. In the report, say what each finding rests on and which steps you skipped.

## Nothing starts until the server answers

This skill needs the typo3-dev-companion server. The skill also loads when the
server is not connected, and nothing warns you then. So check first that the
server answers. The first call of the order does that.

- Your client may put the server name in front of each tool name, as in
  `mcp__<server>__typo3_project_describe`. A search for the bare name then finds
  nothing, although the server is there. A `select:` on the bare names finds
  nothing too. Search for the full name before you decide that the server is
  missing.

- No `typo3_` tool in this session, or a first call that errors: stop. Say that
  this workflow needs the server and that it is not there. Name what came back.

- Do not fall back to general TYPO3 knowledge, and do not start to read the
  checkout. An answer built that way looks like one from this workflow, but it
  has none of its evidence. Nothing in it shows the difference.

- Continue only when the user asks you to after you said so. Then say in the
  answer that the server was missing. Say it again in every finding a lookup
  would have supported.

## The order

Make the calls in this sequence, and only the ones the task needs. Steps 1 and 3
apply to every task. Steps 2, 4 and 5 each say when the task needs them. Later
calls use what earlier answers returned. If you leave a step out, say why in the
report.

1. **`typo3_project_describe`** — the repository, and whether it has a TYPO3
   installation yet. The answer gives:

   - the TYPO3 and PHP version, which filters every later answer;
   - the project's own extensions and its sites;
   - the commands the repository declares.

   Recommend only checks the repository declares. Any other check is a wrong
   answer, however sensible it sounds.

   The answer ends with a list of guides, by id. Each guide is a complete
   procedure, such as the Gerrit workflow. Some clients show these guides
   nowhere else. Read a guide with `typo3_rule_lookup` and its `documentId`, not
   with a search.

2. **`typo3_extension_describe`** for each extension in scope. The answer gives:

   - what the extension registers;
   - what it ships: its manual, its README, its test layers and its XLF files;
   - the source language of each XLF file;
   - what the extension does *not* ship, which a file listing cannot show.

   If step 1 reported no extension, there is nothing to call here. Say so. A
   core checkout is that case: step 1 lists the project's own extensions, not
   the ones TYPO3 ships.

3. **`typo3_task_guide`** with a short English task, the paths it touches and
   the change type. The answer is called the brief. It names the workflow the
   task belongs to and the checks that come with it.

   Leave `targetVersion` out unless the task is about one major alone. Without
   it, the guide answers for every major the package declares, the installed one
   included. A version you pass narrows the answer to that version.

   Run it in every session, also for this skill's own tasks. The guide builds
   the brief from the paths as well as the task text. No skill knows which paths
   the caller works on.

   An installed skill does not replace the brief. Without the call, you miss the
   hints and the core checks that match your paths. The call is cheap, even
   where the brief only confirms this skill.

4. **`typo3_hint_lookup`** for each subsystem in scope, with its concrete paths.
   Send one query per subsystem. A single broad query is no evidence about a
   subsystem.

   If step 3 ran with those paths, its answer says whether you still need this
   step. Read its `omittedHints` field:

   - **Empty:** the brief already has every hint this lookup returns for those
     paths. Its text then says "these are everything typo3_hint_lookup matches
     for these paths". The same query returns the same hints, so do not send it.
   - **Not empty:** it lists the ids the brief left out. Fetch those by id
     rather than repeat the query.

   The `hints` field is present in both cases and does not tell them apart.

5. **`typo3_changelog_lookup` with `type: deprecation`**, once for each major
   the package declares. This is the deprecation sweep. The section "The
   deprecation sweep" says how to make the call, how to read the answer and when
   to skip it.

## The deprecation sweep

### The call

Omit the query and raise `limit` to get that major whole. Do not search with
words from the extension. A query matches an entry only when the entry title has
every word of it. The core titled those entries after its own code, so your
words miss most of them. So you make one call per declared major, and each
answer is that whole major.

### Pick the entries that concern the package

Every entry has tags of two kinds:

- `ext:core`, `ext:frontend`, `ext:form` and the rest name the system extension
  a change is **in**.
- `TCA`, `TypoScript`, `Fluid`, `YAML`, `Backend`, `Frontend` name the area.

Use step 2 to choose the tags that matter. They are the system extensions the
package requires, renders through or registers into. They are also the kinds of
file it ships. Keep the entries with those tags. This costs no further call.

An extension key of your own is not among the tags and matches nothing. The
`tag` parameter is for one narrow question, not for the sweep.

### Check every hit

Check in the checkout whether the code calls each identifier that comes back. A
deprecation the code does not call is not a finding.

Put the `FullyScanned` / `PartiallyScanned` tag into the answer. It says whether
the Extension Scanner can find the remaining call sites, or whether you must
read them.

The sweep needs no file of the checkout, so it comes before the reading.

### What an empty sweep means

A changelog records changes. A feature nobody changed for ten majors has no
entry at all. So an empty sweep says nothing about what still works. Ask "Does
this still work in version N" whenever the question comes up, and send it here:

- **A documented feature**, such as a ViewHelper, a TCA type or a TypoScript
  setting: `typo3_documentation_lookup` at that version. The search matches page
  titles, section names and the names a manual declares. It never searches the
  text of a page. A manual declares properties, classes, methods and console
  commands. Query such a name the way code writes it, or query the name alone.

- **A PHP identifier the manual does not declare**: it has no page named after
  it. Search `typo3_changelog_lookup` for a PHP identifier by its exact name.
  That finds every entry that mentions it, whatever the core titled the change.
  After that, read the class itself, as "When the lookups run out" says.

The manual may have no page for a feature either. Report that you found none.
Undocumented does not mean unsupported.

### A second declared major

A package that declares more than one major asks one more question of every
deprecation the sweep returns. Is the replacement there on the lower major?

1. Query the entry's `issue` number. It returns every entry filed under that
   number, the Feature entry of the replacement included.
2. The version of that Feature entry answers the question.
3. If the number returns no other entry, nobody wrote one for the replacement.
   Then read `typo3_rule_lookup` with
   `documentId="extension/compatibility/a-declared-major-that-is-not-installed"`.

### When to skip the sweep

The sweep finds calls that new code must replace. Skip it in two cases:

- **No change.** A task that produces no change skips this step. Triage, a
  reproduction and a review are examples, not a complete list.
- **No TYPO3 API.** Skip the sweep only where the change touches no TYPO3 API: a
  code style fixer, a CI file, an `.editorconfig`. A deprecation is a statement
  about API the package calls. So a change that calls no API cannot produce a
  deprecation finding.

This exemption ends as soon as the work produces a change. These cases need the
sweep:

- A review that is asked to make the change. Start this order again with the
  files it will write.
- A rebase of somebody else's patch onto current code also writes commits. The
  sweep says whether the code under the patch deprecated something the patch
  calls.
- One PHP file edited along the way.
- A test file always counts, wherever it sits. It calls the API it tests and the
  test framework, and both deprecate. A fixture is exempt where it is data the
  suite reads, not where it is a class.

Decide it from the files the change touches, never from the task description.
The size of the change decides nothing. Three statements can call a deprecated
API as easily as three hundred. This step returns the largest answer of the
order, so skip it where it finds nothing.

## After the calls

**Before the reading**, write down what the order established:

- the version that filters every later answer;
- the packages in scope;
- the declared commands;
- which steps an earlier answer made unnecessary, and which answer that was.

What the files show belongs in the report at the end. A reader who cannot see
what an answer rests on cannot tell it from a guess.

**Then** read the checkout, and not before. If you read the files first, the
later steps seem optional. You then judge the code before you know its
conventions.

**Before the first edit**, name the files this change will create, change or
delete. Delete a file only when the user asks for it. This is somebody else's
checkout, and you cannot undo a deletion here.

**Last**, the report names every step of this order it did not reach, and why.
The reason is an earlier answer that already covered it, a condition that made
it empty, or an exemption. Without this, a reader cannot tell a step you skipped
on purpose from one you forgot.

## When the lookups run out

If a behaviour question is still open after the lookups above, read the answer
in the installed source. Do not guess. The class that implements the behaviour
and the one it inherits from answer it. Read them instead of changing the code
until it works.

If a first change did not work, your reading was probably wrong. Read the source
again before a second attempt.

The source shows what this installation does, not what TYPO3 supports in
general. So say in the finding that the answer holds only for the installed
version, and name that version.

## What each runtime lookup adds after the extension answer

`typo3_extension_describe` in step 2 says what one package registers. The
lookups below say what the running installation really uses, with all packages
combined. That is a different fact, even where the words are the same. Step 2
replaces none of these calls:

- `typo3_backend_module_lookup` — the tree position, the labels, the access
  level, the routes and the navigation component the parent module supplies.
  Step 2 lists the modules the package declares. A declaration cannot show what
  a module inherits from its parent.

- `typo3_icon_lookup` — whether any installed package registers an identifier.
  That validates the ones a template uses. Step 2 lists the identifiers this
  package contributes.

- `typo3_label_lookup` — the labels as the installation resolves them, with its
  overrides applied. Step 2 lists the package's XLF files and the source
  language each declares, never what a unit says here.

- `typo3_fluid_namespace_list` — the prefixes any template may use without a
  declaration, from every package at once. Step 2 lists the package's own
  declarations. So an empty list there is no evidence that no package registers
  a prefix globally.

- `typo3_configuration_lookup` — the resolved configuration value, after every
  extension has changed it. For a form data group it gives the order the
  providers really run in. Step 2 says nothing about that. What a registration
  declares is not what the installation resolves.

- `typo3_service_lookup` — the class the container really injects for a service
  id, an interface or a tag. Decorations and overrides count. Step 2 lists what
  the package's own `Services.yaml` declares, not what the container uses in the
  end.

- `typo3_schema_lookup` — the columns TYPO3 derives for a table from its TCA. It
  gives the type, the nullability and the default of each. Step 2 lists the
  tables the package registers and nothing about their columns.

- `typo3_flexform_lookup` — the data structure the installation resolves a
  `type=flex` field to, sheet by sheet, with listeners and migrations applied.
  Step 2 lists the content elements a package registers, never the structure
  each one's form builds.

- `typo3_record_lookup` — the rows of any table the installation has TCA for. It
  says how many there are and on which pages. It says what one column holds
  across them and which rows differ from its default. Step 2 has no row in its
  answer at all.

None of these says whether what it reports is correct. `typo3_hint_lookup` and
`typo3_documentation_lookup` say that. A runtime lookup can confirm a subsystem
that still breaks every rule for it. So ask both before you call a subsystem
correct.

## A rule applies to existing code too

A rule says what new code should do. It also says what this checkout already
does wrong. A file that follows the opposite of a rule is a finding, not a local
style to keep. A project's own habit says nothing about whether the habit is
right.

## Find out why a costly mechanism exists

A mechanism that costs something is not a defect because it costs. Before you
report one, find what it is there for and say so. Look in the manual, the
README, the changelog, the setting that drives it, or the declared versions.

Where the documentation states a purpose, you have a trade-off, not a defect.
Name it with its cost and its alternative. Where you cannot find one, the
finding says that you could not find one, not that none exists. Skip this, and
your review lists everything the author did on purpose.

## Ask when the project contradicts the request

Where the project contradicts the user's request, say so before you build on
either. This server states what the project contains. It does not state what the
person meant. Where two answers are both right, name them and ask. A question
after the work can mean the work was wasted.

## Say what each finding rests on

A finding rests on one of three things:

- A file you read, at its path and its line.
- A command you ran, with what it printed.
- A mechanism you traced into an installed package.

Say which of the three it is. Otherwise a finding from a CI file weighs as much
as one with a verified line.

Read several files with one call per file, and send the calls together. One
command that prints them all counts its lines across all files. A line number
from that output is wrong in every file after the first.

Where one of the project's own commands would settle it, run it.
`typo3_project_describe` marks each command it lists **check**, **change** or
**unknown**, from what the command runs:

- **check** — A check reports and leaves the code as it was. So run it even in a
  task told not to change files. The linter the repository already declares is
  the cheapest evidence in it.
- **change** — In a task told not to change files, do not run it.
- **unknown** — A test suite, a shell pipeline, a console command. Name an
  unknown in the answer as evidence that is available, and do not run it
  unasked.

What a check prints is not the finding. The finding is about the configuration
that makes the check fail. The run confirms the finding.

## What this server does not know

It does not read your working tree. You find out which files changed and which
branch you are on. You also check whether a path or an identifier still exists
there. Then pass the concrete paths to the tools. That turns a general
convention into an answer about this code.

## Query it in English

The knowledge is English, and the search matches words, not meaning. A query in
another language finds only the loanwords the two languages share. Translate the
subject before the call and the answer back afterwards, whatever language you
speak with the user.
