# Where every task starts

## Nothing starts until the server answers

A skill is a file the installer left behind: it loads and reads the same whether
the tools behind it are connected or not, and neither side notices. So the first
call below is also the check.

- A client may carry this server's name in each tool's —
  `mcp__<server>__typo3_project_describe` — so a search for the bare name comes
  back empty where the server is connected. Look for the qualified form before
  reading an empty result as an answer about the server.
- No `typo3_` tool in this session, or a first call that errors: stop, say this
  workflow needs the server and it is not there, and name what came back.
- Do not fall back to general TYPO3 knowledge or start reading the checkout.
  That answer carries this workflow's order and confidence and none of its
  evidence, and nothing in it says which of the two it is.
- Continue only when asked to after saying so, and repeat it in the answer and
  in every finding a lookup would have carried.

## The order

This is an order rather than a list: each step decides what the next one is
worth. Where a step below carries a condition for skipping it, that condition is
narrow on purpose — a prescription that gets skipped teaches the next reader to
skip the ones that matter too.

1. **`typo3_project_describe`** — the repository and whether anything is
   installed in it yet, its TYPO3 and PHP version, the extensions that are the
   project's own, its sites, and the commands this repository actually declares.
   Every later answer is filtered by that version, and a check the repository
   does not declare is a wrong answer however sensible it sounds. It ends with
   the whole procedures this server carries, as ids: that list is the only place
   they are named to a client that renders no resource list, and each one is a
   `typo3_rule_lookup` with that `documentId` rather than a search.
2. **`typo3_extension_describe`** for each extension in scope — what it
   registers, and what it ships beside that: its manual, its README, its test
   layers, its XLF files with the source language each one declares. What it
   does *not* ship is answered too, and that is the half no file listing can
   give you.

   Where step 1 reported none — a core checkout is the case, since it names the
   project's own extensions and not TYPO3's — that answer is this step, and
   there is nothing to call. Say so.
3. **`typo3_task_guide`** with a short English task, the paths it touches, the
   target version and the change type — the workflow this task belongs to and
   the checks that come with it.

   Run it in every session, this skill's own tasks included. The brief is built
   from the paths as well as the task text, and no skill knows which paths the
   caller is holding: a skill that covers the task is not that brief, and
   skipping the step costs the hints and the core checks those paths match.
   Where the guide's own answer is what named this skill, this is one call for
   an answer already in the session — the price of a step there is nothing to
   decide about.
4. **`typo3_hint_lookup`** for each subsystem in scope, with its concrete paths.
   One query per subsystem; a single broad query is not subsystem evidence.

   Where step 3 ran with those paths, its answer says whether this step is still
   owed. A brief that carried everything the lookup matched says so — "these are
   everything typo3_hint_lookup matches for these paths" — and there the call is
   made and asking again returns the same hints. One that stopped short says
   that instead and names the ids it left, and those are what is owed: fetch
   them by id rather than repeating the query. Read the sentence rather than the
   populated `hints` key, which is present either way and does not tell the two
   apart. `omittedHints` is that sentence as data: empty where the brief carried
   everything, and the ids it left where it stopped short.
5. **`typo3_changelog_lookup` with `type: deprecation`**, at each major the
   package declares, with the query omitted and `limit` raised to carry that
   major whole. Those two are the changelog's own axes, and the extension's
   vocabulary is not among them: an entry carries a query only when its title
   carries every word of it at once, and the core titled those entries about its
   own code.

   That is one call per declared major, and what comes back is the major. Every
   entry carries its own index tags: `ext:core`, `ext:frontend`, `ext:form` and
   the rest name the system extension a change is **in**, and `TCA`,
   `TypoScript`, `Fluid`, `YAML`, `Backend`, `Frontend` name the surface. Step 2
   picks the package's entries out of that answer by those tags — the system
   extensions it requires, renders through or registers into, and the kinds of
   file it ships — which costs no further call. An extension key of your own is
   not among them and matches nothing, and `tag` narrows one question inside a
   major rather than composing the sweep out of eleven.

   Step 2 is what the answers are checked against, which is the other half the
   words were doing. Verify each identifier that comes back in the checkout — a
   deprecation nothing here calls is not a finding — and carry the
   `FullyScanned` / `PartiallyScanned` tag into the answer, because it says
   whether the Extension Scanner can find the remaining call sites or whether
   that reading is yours. Bounded this way the sweep is writable before a file
   is opened, which is why it is a step of the order rather than something the
   reading stumbles into.

   **What its silence is worth.** A changelog records change events, so a
   pattern nothing has touched for ten majors has no entry at all. An empty
   sweep is therefore not an answer about what still works. "Does this still
   work in version N" goes to `typo3_documentation_lookup` at that version —
   here, and whenever the reading raises it again.

   That is a question for a documented surface — a ViewHelper, a TCA type, a
   TypoScript setting. The manual matches page titles, section paths and the
   property names each manual declares, never the text of a page, so a PHP
   identifier has no page to be titled after. A property is reached by its own
   name where that name is written the way code is, or where the query is
   nothing but the name.

   An identifier goes to `typo3_changelog_lookup` under its own name, which
   reaches the entries writing it however the change was titled, and then to the
   class below. Where the manual has no page for a surface either, that is a
   result and not an answer. Undocumented is not unsupported.

   **A second declared major.** A package declaring more than one asks a second
   question of every deprecation the sweep returns: whether the replacement is
   on the lower one. The entry's `issue` is a query of its own, and it reaches
   every entry filed under that number. The Feature the core announced the
   replacement in is among them, and the version it was released in is what
   settles that question. Where the number reaches no sibling, nothing wrote an
   entry for the replacement, and `typo3_rule_lookup` with
   `documentId="extension/compatibility/a-declared-major-that-is-not-installed"`
   is the reading that closes it.

   **Where the sweep is not owed.** A task that produces no change does not
   reach this step at all. The property is what the task produces, and a triage,
   a reproduction and a review are illustrations of it rather than the list it
   is read off. The sweep asks what a package will have to stop calling, and a
   task that writes nothing is not going to call anything.

   The exemption ends where the workflow produces a change. A review asked to
   make the change is that other workflow, and it starts this order again
   holding the files it is about to write.

   Skip the sweep only where the change touches no TYPO3 API — a code style
   fixer, a CI file, an `.editorconfig`. A deprecation is a statement about API
   the package calls, so a change that calls none has nothing for the sweep to
   land on and it is empty before it is run. That condition is worth stating
   because this step is the largest answer the order asks for: one call per
   declared major, carrying that major's deprecations whole. Which side a change
   falls on is read off the files it touches and never off the task it started
   as — one PHP file edited along the way puts it back among the ordinary ones,
   and a skip there costs the deprecation no finding would have walked into. How
   small the change is decides nothing either, because three statements can call
   a deprecated API as easily as three hundred. A test file is one of those
   wherever it sits: it calls the API it exercises and the framework around it,
   both of which deprecate. A fixture is exempt where it is data the suite reads
   and not where it is a class.

**Before the reading**, write down what the order established: the version every
later answer is filtered by, the packages in scope, the commands this repository
declares, and which steps were discharged by what. Those are answers already in
the session rather than a second reading, and what the files show belongs to the
report at the other end. A caller who cannot see what an answer rests on cannot
tell it from one that rests on nothing.

**Then** read the checkout. Not before: listing the files first makes everything
after the listing look optional, and the conventions arrive as a footnote to a
verdict that has already formed.

**Before the first edit**, name the files this change will create, change or
delete. A deletion is the caller's to ask for, and this is somebody else's
checkout: it is the one act nothing here can put back.

**Last**, the report names every step of this order it did not reach, and what
stood in for it. That is an answer already in the session, a condition that made
the step empty, or an exemption. A step passed over in silence cannot be told
from one that was dropped.

## When the lookups run out

A behaviour question that survives the lookups above is read out of the
installed source rather than guessed at. What answers it is the class that
implements the behaviour and the one it inherits from. That reading is the step
after the lookups, and what it replaces is changing the code until it works. A
first change that did not work is evidence about the reading, so the second
attempt at one failure reads the source rather than changing the code again.
What it settles is what this installation does and never what TYPO3 supports. So
a finding says the question could not be settled beyond the version installed,
and an answer built on the reading names the version it holds for.

## What each runtime lookup adds after the extension answer

`typo3_extension_describe` in step 2 says what one package registers. The
lookups below say what the installation resolved, which is a different fact even
where the words are the same, so step 2 has made none of these calls:

- `typo3_backend_module_lookup` — the tree position, the labels, the access
  level, the routes and the navigation component the parent module supplies.
  Step 2 lists the modules the package declares, and that inheritance is what a
  declaration cannot show.
- `typo3_icon_lookup` — whether an identifier is registered, across every
  installed package, which is what validates the ones a template actually uses.
  Step 2 lists the identifiers this package contributes.
- `typo3_label_lookup` — the labels as the installation resolves them, its
  overrides applied. Step 2 lists the package's XLF files and the source
  language each declares, never what a unit says here.
- `typo3_fluid_namespace_list` — the prefixes any template may use without
  declaring them, from every package at once. Step 2 lists the package's own
  declarations, so an empty list there is no evidence that nothing is registered
  globally.
- `typo3_configuration_lookup` — the resolved configuration value, after every
  extension has had its say, and for a form data group the order the providers
  really run in. Step 2 answers nothing about that surface at all, and what a
  registration declares is not what the installation resolves.
- `typo3_service_lookup` — the class the container really injects for a service
  id, an interface or a tag, decorations and overrides applied. Step 2 lists
  what the package's own `Services.yaml` declares, never what won.
- `typo3_schema_lookup` — the columns TYPO3 derives for a table from its TCA,
  with the type, the nullability and the default each one gets. Step 2 lists the
  tables the package registers and nothing about their shape.
- `typo3_flexform_lookup` — the data structure the installation resolves a
  `type=flex` field to, sheet by sheet, listeners and migrations applied. Step 2
  lists the content elements a package registers, never the structure each one's
  form builds.
- `typo3_record_lookup` — the rows of any table the installation has TCA for:
  how many there are, where they sit, what one column holds across them and
  which rows depart from its default. Step 2 has no row in its answer at all.

None of these says whether what it reports is right. `typo3_hint_lookup` and
`typo3_documentation_lookup` do, and a subsystem confirmed by its own runtime
lookup can still break every rule that governs it, so it is not established
until both were asked.

## A rule is read in both directions

It says what new code should do, and it says what this checkout is already doing
wrong. A file that has settled into the opposite of a rule is a finding, not a
local style to preserve: consistency with a project's own habit establishes
nothing about whether the habit is right.

## What the code is for is evidence, and the repository states it

A mechanism that costs something is not a defect for costing it. Before
reporting one, find what it is there for — the manual, the README, the
changelog, the setting it is driven by, the versions the package declares it
supports — and say so. Where a purpose is documented, what you have is a
trade-off to name with its cost and its alternative, not a defect; where you
cannot find one, the finding says that it could not be established rather than
that none exists. Skipping this turns a review into a list of everything the
author did on purpose.

## What a finding rests on is part of the finding

Three things carry one: a file that was read, at its path and its line; a
command that was run, with what it printed; a mechanism traced into an installed
package. Say which of the three it is. Leaving it unsaid gives a finding read
out of a CI file the weight of one with a verified line.

Where one of the project's own commands would settle it, run it.
`typo3_project_describe` marks each command it lists **check**, **change** or
**unknown**, read off the declared body: a check reports and hands the code back
as it was, so even a task told not to change files runs it, and the linter the
repository already declares is the cheapest evidence in it. A change is not run
under that instruction, and an unknown — a test suite, a shell pipeline, a
console command — is named in the answer as evidence that is available rather
than run unasked. What a check prints is not the finding: the configuration that
makes it fail is still what the finding is about, and the run is what takes that
finding from derived to established.

## What this server does not know

It does not read your working tree. Which files changed, which branch you are
on, and whether a path or an identifier still exists there are yours to
establish — then pass the concrete paths back, because that is what turns a
general convention into an answer about this code.

## Query it in English

The knowledge is written in English and matched lexically, so a query in another
language reaches the loanwords the two happen to share and nothing else.
Translate the subject before calling and the answer back afterwards, whatever
language you are speaking with the user.
