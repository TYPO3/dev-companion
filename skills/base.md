# Where every task starts

## Nothing starts until the server answers

A skill is a file the installer left behind. It loads and reads the same whether
the tools behind it are there or not, and neither side notices. So the first
call below is also the check.

- A client may carry this server's name in each tool's name:
  `mcp__<server>__typo3_project_describe`. So a search for the bare name comes
  back empty where the server is there. A search for a tool's schema needs the
  same form. A `select:` on the bare names returns nothing where the tools are
  there. Look for the qualified form before you read an empty result as an
  answer about the server.
- No `typo3_` tool in this session, or a first call that errors: stop. Say that
  this workflow needs the server and it is not there, and name what came back.
- Do not fall back to general TYPO3 knowledge, and do not start to read the
  checkout. That answer carries this workflow's order and confidence and none of
  its evidence. Nothing in it says which of the two it is.
- Continue only when the user asks you to after you said so. Repeat it in the
  answer and in every finding a lookup would have carried.

## The order

This is an order rather than a list. Each step decides what the next one is
worth. Where a step below carries a condition to skip it, that condition is
narrow on purpose. A skipped prescription teaches the next reader to skip the
ones that matter too.

1. **`typo3_project_describe`** — the repository and whether it holds an
   installation yet. It reports the TYPO3 and PHP version, the project's own
   extensions, its sites, and the commands this repository declares. That
   version filters every later answer. A check the repository does not declare
   is a wrong answer however sensible it sounds.

   The answer ends with the whole procedures this server carries, as ids. That
   list is the only place a client that renders no resource list sees their
   names. Each one is a `typo3_rule_lookup` with that `documentId` rather than a
   search.
2. **`typo3_extension_describe`** for each extension in scope. It says what the
   extension registers, and what it ships beside that. That is its manual, its
   README, its test layers, and its XLF files with the source language each one
   declares. It also says what the extension does *not* ship, and that is the
   half no file listing gives you.

   Where step 1 reported no extension, that answer is this step, and there is
   nothing to call. Say so. A core checkout is that case, because step 1 names
   the project's own extensions and not TYPO3's.
3. **`typo3_task_guide`** with a short English task, the paths it touches, the
   target version and the change type. It answers the workflow this task belongs
   to and the checks that come with it.

   Run it in every session, this skill's own tasks included. The guide builds
   the brief from the paths as well as the task text. No skill knows which paths
   the caller holds.

   A skill that covers the task is not that brief. A skipped step costs the
   hints and the core checks those paths match. Where the guide's own answer
   named this skill, this is one call for an answer already in the session. The
   price of a step there is nothing to decide about.
4. **`typo3_hint_lookup`** for each subsystem in scope, with its concrete paths.
   One query per subsystem. A single broad query is not subsystem evidence.

   Where step 3 ran with those paths, its answer says whether you still owe this
   step. A brief that carried everything the lookup matched says so: "these are
   everything typo3_hint_lookup matches for these paths". There the guide made
   the call, and the same query returns the same hints. A brief that stopped
   short says that instead and names the ids it left. You owe those: fetch them
   by id rather than repeat the query.

   Read the sentence rather than the populated `hints` key. That key is present
   either way and does not tell the two apart. `omittedHints` is that sentence
   as data. It is empty where the brief carried everything, and it holds the ids
   the brief left where it stopped short.
5. **`typo3_changelog_lookup` with `type: deprecation`**, at each major the
   package declares. Omit the query and raise `limit` to carry that major whole.
   Those two are the changelog's own axes, and the extension's vocabulary is not
   among them. An entry carries a query only when its title carries every word
   of it at once. The core titled those entries about its own code.

   That is one call per declared major, and what comes back is the major. Every
   entry carries its own index tags. `ext:core`, `ext:frontend`, `ext:form` and
   the rest name the system extension a change is **in**. `TCA`, `TypoScript`,
   `Fluid`, `YAML`, `Backend`, `Frontend` name the surface.

   Step 2 picks the package's entries out of that answer by those tags. The tags
   are the system extensions it requires, renders through or registers into, and
   the kinds of file it ships. That costs no further call. An extension key of
   your own is not among them and matches nothing. `tag` narrows one question
   inside a major rather than composes the sweep out of eleven.

   You check the answers against step 2, which is the other half the words did.
   Verify each identifier that comes back in the checkout. A deprecation nothing
   here calls is not a finding.

   Carry the `FullyScanned` / `PartiallyScanned` tag into the answer. It says
   whether the Extension Scanner can find the remaining call sites or whether
   that reading is yours. Bounded this way, you can write the sweep before you
   open a file. That is why it is a step of the order rather than something the
   reading stumbles into.

   **What its silence is worth.** A changelog records change events. So a
   pattern nothing has touched for ten majors has no entry at all. An empty
   sweep is therefore not an answer about what still works. "Does this still
   work in version N" goes to `typo3_documentation_lookup` at that version. Ask
   it here, and whenever the reading raises it again.

   That is a question for a documented surface: a ViewHelper, a TCA type, a
   TypoScript setting. The manual matches page titles, section paths and the
   property names each manual declares, never the text of a page. So a PHP
   identifier has no page named after it. You reach a property by its own name
   where the query writes that name the way code does. You also reach it where
   the query is nothing but the name.

   An identifier goes to `typo3_changelog_lookup` under its own name. That
   reaches the entries that write it, however the core titled the change. Then
   it goes to the class below. Where the manual has no page for a surface
   either, that is a result and not an answer. Undocumented is not unsupported.

   **A second declared major.** A package that declares more than one asks a
   second question of every deprecation the sweep returns. Is the replacement on
   the lower one? The entry's `issue` is a query of its own, and it reaches
   every entry filed under that number. The Feature the core announced the
   replacement in is among them.

   The version the core released it in settles that question. Where the number
   reaches no sibling, nobody wrote an entry for the replacement.
   `typo3_rule_lookup` with
   `documentId="extension/compatibility/a-declared-major-that-is-not-installed"`
   is the reading that closes it.

   **Where you do not owe the sweep.** A task that produces no change does not
   reach this step at all. The property is what the task produces. A triage, a
   reproduction and a review illustrate it; they are not the list you read it
   off. The sweep asks what a package will have to stop calling. A task that
   writes nothing is not going to call anything.

   The exemption ends where the workflow produces a change. A review asked to
   make the change is that other workflow. It starts this order again with the
   files it is about to write. To carry somebody else's patch onto current code
   is on the same side. It writes commits. The sweep says whether the code that
   moved under the patch deprecated something the patch calls.

   Skip the sweep only where the change touches no TYPO3 API: a code style
   fixer, a CI file, an `.editorconfig`. A deprecation is a statement about API
   the package calls. So a change that calls none has nothing for the sweep to
   land on. The sweep is empty before it runs.

   That condition is worth a statement, because this step is the largest answer
   the order asks for. It is one call per declared major, with that major's
   deprecations whole. You read which side a change falls on off the files it
   touches, never off the task it started as. One PHP file edited along the way
   puts it back among the ordinary ones.

   A skip there costs the deprecation no finding would have walked into. How
   small the change is decides nothing either. Three statements can call a
   deprecated API as easily as three hundred.

   A test file is one of those wherever it sits. It calls the API it exercises
   and the framework around it, and both deprecate. A fixture is exempt where it
   is data the suite reads, and not where it is a class.

**Before the reading**, write down what the order established. That is the
version that filters every later answer, the packages in scope, and the commands
this repository declares. Write down which steps what discharged. Those are
answers already in the session rather than a second reading. What the files show
belongs to the report at the other end. A caller who cannot see what an answer
rests on cannot tell it from one that rests on nothing.

**Then** read the checkout. Not before. A file list first makes everything after
the list look optional. The conventions then arrive as a footnote to a verdict
that has already formed.

**Before the first edit**, name the files this change will create, change or
delete. A deletion is the caller's to ask for, and this is somebody else's
checkout. It is the one act nothing here can put back.

**Last**, the report names every step of this order it did not reach, and what
stood in for it. That is an answer already in the session, a condition that made
the step empty, or an exemption. A reader cannot tell a step passed over in
silence from one somebody dropped.

## When the lookups run out

A behaviour question that survives the lookups above is one you read out of the
installed source. Do not guess at it. The class that implements the behaviour
and the one it inherits from answer it. That reading is the step after the
lookups. It replaces a change to the code until it works.

A first change that did not work is evidence about the reading. So the second
attempt at one failure reads the source rather than changes the code again.

What it settles is what this installation does and never what TYPO3 supports. So
a finding says that you could not settle the question beyond the version
installed. An answer built on the reading names the version it holds for.

## What each runtime lookup adds after the extension answer

`typo3_extension_describe` in step 2 says what one package registers. The
lookups below say what the installation resolved. That is a different fact even
where the words are the same. So step 2 has made none of these calls:

- `typo3_backend_module_lookup` — the tree position, the labels, the access
  level, the routes and the navigation component the parent module supplies.
  Step 2 lists the modules the package declares. A declaration cannot show that
  inheritance.
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
  extension has had its say. For a form data group it gives the order the
  providers really run in. Step 2 answers nothing about that surface at all.
  What a registration declares is not what the installation resolves.
- `typo3_service_lookup` — the class the container really injects for a service
  id, an interface or a tag. Decorations and overrides count. Step 2 lists what
  the package's own `Services.yaml` declares, never what won.
- `typo3_schema_lookup` — the columns TYPO3 derives for a table from its TCA. It
  gives the type, the nullability and the default each one gets. Step 2 lists
  the tables the package registers and nothing about their shape.
- `typo3_flexform_lookup` — the data structure the installation resolves a
  `type=flex` field to, sheet by sheet, with listeners and migrations applied.
  Step 2 lists the content elements a package registers, never the structure
  each one's form builds.
- `typo3_record_lookup` — the rows of any table the installation has TCA for. It
  says how many there are and where they sit. It says what one column holds
  across them and which rows depart from its default. Step 2 has no row in its
  answer at all.

None of these says whether what it reports is right. `typo3_hint_lookup` and
`typo3_documentation_lookup` do. A subsystem its own runtime lookup confirmed
can still break every rule that governs it. So it is not established until you
asked both.

## A rule reads in both directions

It says what new code should do, and it says what this checkout already does
wrong. A file that settled into the opposite of a rule is a finding, not a local
style to preserve. Consistency with a project's own habit establishes nothing
about whether the habit is right.

## What the code is for is evidence, and the repository states it

A mechanism that costs something is not a defect because it costs. Before you
report one, find what it is there for and say so. That is the manual, the
README, the changelog, the setting that drives it, or the declared versions.

Where the documentation states a purpose, what you have is a trade-off, not a
defect. Name it with its cost and its alternative. Where you cannot find one,
the finding says that you could not establish one, not that none exists. If you
skip this, your review is a list of everything the author did on purpose.

## What a finding rests on is part of the finding

Three things carry one. A file you read, at its path and its line. A command you
ran, with what it printed. A mechanism you traced into an installed package. Say
which of the three it is. If you leave it unsaid, a finding from a CI file
weighs as much as one with a verified line.

Where one of the project's own commands would settle it, run it.
`typo3_project_describe` marks each command it lists **check**, **change** or
**unknown**, read off the declared body. A check reports and hands the code back
as it was. So even a task told not to change files runs it. The linter the
repository already declares is the cheapest evidence in it.

Do not run a change under that instruction. Name an unknown in the answer as
evidence that is available, and do not run it unasked. An unknown is a test
suite, a shell pipeline, a console command.

What a check prints is not the finding. The configuration that makes it fail is
still what the finding is about. The run takes that finding from derived to
established.

## What this server does not know

It does not read your working tree. You establish which files changed, which
branch you are on, and whether a path or an identifier still exists there. Then
pass the concrete paths back, because that turns a general convention into an
answer about this code.

## Query it in English

The knowledge is English and the match is lexical. So a query in another
language reaches the loanwords the two happen to share and nothing else.
Translate the subject before the call and the answer back afterwards, whatever
language you speak with the user.
