---
id: D-SKL-069
title: Each runtime lookup says what it adds after the extension answer
date: 2026-08-21
status: open
---

# D-SKL-069 — Each runtime lookup says what it adds after the extension answer

**`skills/base.md` names the five runtime lookups with what each adds after its
own step 2. `typo3_extension_describe` answers what a package registers and they
answer what the installation resolved.**

The pair the base draws today is runtime against conventions. Read in the order
the base fixes, that describes a call the session has already made. So a session
that has done step 2 has satisfied the runtime half of every surface and is
right to skip five tools.

## Evidence

- The feedback of 2026-08-19 09:44, out of the same blog extension audit as
  `D-SKL-068`. The session called none of the five and says why.
  `typo3_extension_describe` had returned the four backend modules, 24 icon
  identifiers, the XLF files with their source languages and the site sets with
  their files. It "treated the describe output as the runtime half for every one
  of those surfaces". Its own count is `typo3_extension_describe`,
  `typo3_hint_lookup` five times, `typo3_changelog_lookup` twelve times,
  `typo3_rule_lookup` once and `typo3_commit_message_guide` five times. That is
  against six named tools it read and passed over.
- **The wording is in `skills/base.md`, not in the skill the feedback names.**
  The section *Two kinds of lookup, and neither stands in for the other* names
  the five and pairs them against `typo3_hint_lookup` and
  `typo3_documentation_lookup`. Step 2 of the same file is
  `typo3_extension_describe`. The installer copies that file into every
  published skill as `references/base.md`, so the collision is in each of them.
- `typo3-extension-health` restates the section in *Ask before judging, on every
  surface in scope*, in the same words. That is the copy the feedback quotes,
  and a second copy of a rule the base already fixes is what
  `documentation/contributing/writing-a-skill.rst` forbids.
- **None of the five is redundant after step 2**, read off the tool
  declarations. `backendModules` is the identifiers in
  `Configuration/Backend/Modules.php`. `typo3_backend_module_lookup` carries the
  tree position, the labels, the access level, the routes and the navigation
  component the parent supplies. Its description says a read of the registration
  files "says a module is not page-tree navigated when it is". `icons` is what
  this extension registers. `typo3_icon_lookup` validates the identifiers a
  template uses, from every package at once, which is what the schema field
  itself points at. `artifacts.languageFiles` is the XLF paths and the source
  language each declares, while `typo3_label_lookup` searches the labels with
  the installation's overrides applied. `fluidNamespaces` is that extension's
  own `Configuration/Fluid/Namespaces.php`, and `typo3_fluid_namespace_list`
  answers what is globally available. Nothing in the extension answer is about
  `TYPO3_CONF_VARS` at all. That is the surface the session named as the one it
  could have settled in one call. That is the
  `SYS/formEngine/formDataGroup/tcaDatabaseRecord` order
  `typo3_configuration_lookup` resolves, which it judged from the registration
  in `ext_localconf.php` instead.
- **The `fluidNamespaces: []` half of the suggestion is already answered.** The
  schema field says the prefixes come from that extension's own
  `Configuration/Fluid/Namespaces.php`. The rendered answer says of
  `ext_localconf.php`: "a hook, an RTE preset or a global Fluid namespace it
  sets is in none of the lists above". That landed on 2026-08-04 in `2da06932`,
  a fortnight before the run, and the session read the empty list correctly. The
  tool owes nothing. What remains is a skill that says which lookup answers the
  surface.
- `typo3_documentation_lookup` is the sixth uncalled tool, and the feedback
  withdraws it. The session's version questions were "since when does this
  exist", which the manual cannot answer by page title, and it says so itself.
  `R-SKL-022` already puts that tool at the surfaces it can answer.

## Decided

- **Step 4 of the ladder, wording.** All six tools exist, the rule was in the
  active skill, and the session read it and applied it. What it lost against is
  the distinction it draws. Runtime against conventions is not the pair that
  decides the call, and this package's registrations against what the
  installation resolved is.
- *Queued* rather than closed on the spot, for the reason `D-SKL-068` gives one
  card over. What changes is a published skill, which lands in somebody else's
  project and gets a review rather than an improvisation.
- **The correction goes into `skills/base.md`**, which is where the sentence is.
  The `typo3-extension-health` restatement goes with it rather than gets a
  second correction.
- **The base discharges none of the five.** `D-SKL-055`'s construct is for a
  call something already in the session answers, and step 2 answers none of
  these five questions whole. The text is per lookup and says what it adds.
- Priority `normal`. What sets it is that the wording sits in the file every
  published skill carries and that the failure is silent. Five surfaces reported
  assessed off a static registration list is what `R-SKL-013` exists to prevent.
  What keeps it off `high` is one session, whose own account is that the audit
  did not visibly suffer for it.
- The wording itself is not decided here, nor whether the section stays one
  paragraph or becomes a line per lookup. Both are the todo's, and both come off
  the tool declarations rather than off anything about TYPO3.

## Assumed

- That `typo3_extension_describe` stays the first answer for an extension-scoped
  task. Were step 2 to stop to cover modules, icons and language files, the
  section would be right as it stands.
- That a session reads the base in the order it declares, so step 2 is what the
  later section stands against. The one measurement is a session that says it
  read it that way.

## Wrong if

- A corrected base sends a session to the five and each returns what step 2
  already had. Then the surfaces really do overlap and the base owed a discharge
  rather than a distinction.
- The extra calls change no finding in a recorded run. Then the rule costs five
  round trips per audit for nothing. It belongs at the surfaces where a finding
  turned on it rather than on every surface in scope.
- The next report of the same skip comes out of a skill whose base carries the
  correction. Then the wording was not what lost. The cause is the schema a
  client that defers has to load before it may call anything. `D-SKL-060` and
  `D-AUD-011` judged that, and its card waits in `todo/waiting/`.

## Since then

The correction says the distinction rather than the pair, what one package
registers against what the installation resolved. Each of the five names what
step 2 could not have had. Nothing measures it yet. No recorded run carries an
audit made with the corrected base, so neither of the first two **Wrong if** has
evidence either way.
