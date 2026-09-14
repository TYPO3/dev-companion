---
id: D-ANS-019
title: 'Three registration kinds are read the way core reads them'
date: 2026-08-02
status: confirmed
coveredBy:
  - ProjectTest::aFlexFormBoundThroughACallThisDoesNotReadIsStillReported
  - ProjectTest::aFormSetIsAnsweredWithTheDefinitionsItStores
  - ProjectTest::aSiteSetIsAnsweredByTheFilesCoreReadsItFor
  - ProjectTest::theFlexFormAContentElementBindsIsOnItsEntry
---

# D-ANS-019 — Three registration kinds are read the way core reads them

**The answer reads each of the three registrations `R-ANS-014` names by what
core reads it by. Each of the three moved inside the covered range.**

That is four call shapes that bind a FlexForm, eight file names in a site set
directory, and two ways to register a form configuration.

`D-ANS-014` left what each of the three is to this step, because a fix named
from a single sitepackage is a copy-down. The read is against `.checkouts/` at
12.4, 13.4 and 14.3, and it changed the answer in two places. The FlexForm call
the feedback named carries a deprecation on the version the audit ran on. One of
the three kinds does not exist at all on the oldest covered major.

## Evidence

- Four calls bind a FlexForm, not one.
  `ExtensionManagementUtility::addPiFlexFormValue()` binds by its third argument
  on 12.4 (`ExtensionManagementUtility.php:1032`) and 13.4 (`:945`). On 14.3
  (`:971`) it raises `E_USER_DEPRECATED`, goes away in v15, and points at two
  replacements. Both are in 14.3. `addPlugin()` takes the data structure as its
  second argument (`:921`) where 13.4's second argument is the plugin type
  (`:875`). `ExtensionUtility::registerPlugin()` takes it as a seventh
  (`extbase/Classes/Utility/ExtensionUtility.php:119`). The fourth is the
  `columnsOverrides` assignment on `pi_flexform` the deprecation message names,
  which is what the first one writes into TCA anyway.
- The bound file stands in the file's own text, and the identifier it belongs to
  usually does too. Every core caller passes the data structure as a `FILE:EXT:`
  literal. The identifier is a literal in the `columnsOverrides` and direct
  forms. In the shape core writes itself, felogin and form on 13.4, it is the
  variable `$contentTypeName` with what `registerPlugin()` returned. That return
  is `strtolower($extensionName)` without its underscores plus `'_'` plus
  `strtolower($pluginName)`. It comes from two arguments that do stand in the
  file, on all three majors.
- The answer reads a site set directory for eight names beside `config.yaml`.
  `YamlSetDefinitionProvider` reads `settings.definitions.yaml`, `settings.yaml`
  and `labels.xlf` on 13.4 and 14.3, and `route-enhancers.yaml` on 14.3 only
  (`:123`) — the feature is `14.1/Feature-107837-RouteEnhancersInSiteSets.rst`.
  `page.tsconfig` is the default `pagets`, and the set directory is the default
  `typoscript` path, which `SysTemplateTreeBuilder::handleSetInclude()` reads
  `constants.typoscript`, `setup.typoscript` and `include_static_file.txt` from.
  `AbstractServiceProvider::configureSetCollector()` is the walk, at depth 1.
- Site sets do not exist on 12.4: `typo3/sysext/core/Classes/Site/Set/` is not
  in that checkout. The answer carries the kind where it is there, and it is
  nothing on the oldest covered major. That needs no bound, because an extension
  that ships no such directory gets an empty list either way.
- A form configuration registers two ways.
  `14.2/Feature-109412-FormYamlAutoDiscovery.rst` and
  `FormYamlCollectorConfigurator` collect
  `Configuration/Form/<SetName>/config.yaml` from every active package one level
  down. The `name` the file declares identifies it rather than the directory,
  `priority` orders it, and `EXTENSIONS.form.disabledSets` switches it off.
  Before it, and still read in 14.3 with a deprecation, TypoScript registers a
  YAML file under `plugin.tx_form.settings.yamlConfigurations` or the `module.`
  one beside it. That is `Mvc/Configuration/ConfigurationManager.php:71`,
  deprecated by `14.2/Deprecation-109412`. Both files carry
  `persistenceManager.allowedExtensionPaths`, which is where the definitions
  are.

## Decided

- The list is three kinds, and the answer carries each where its own mechanism
  exists rather than binds it to a version. The answer reads the extension's
  files; which version reads them is the installation's. The schema and the
  rendered answer say the two instead: `route-enhancers.yaml` from v14.1, the
  data structure argument and the form set from v14.2.
- The FlexForm goes on the `contentElements` entry, per `R-ANS-014`. A bound
  file whose identifier no entry carries lands in `unlistedFlexForms` rather
  than drops. The two parsers disagree, and a registration this answer read and
  then said nothing about is the silence `R-ANS-012` stands against.
- The answer resolves `registerPlugin()`'s return value where a variable takes
  it, which extends `stringVariables()` from one shape to two. Declining it
  would leave every FlexForm bound the way core binds its own unreadable on the
  two LTS lines.
- `registerPlugin()` and `addPlugin()` are **not** added as sources of content
  element identifiers. Whether an Extbase plugin belongs in that list and how
  the answer describes it is `D-ANS-015`'s second queued item, on its own
  branch. Here it would be two sessions that edit one list.
- The site set answer is the file names and not their contents. What
  `route-enhancers.yaml` enhances is the file, and a caller with its name can
  read it. That it is there at all is what no tree walk told anybody to look
  for.
- The answer states a form storage configured as a file mount as unanswerable
  rather than leaves it out. It is a record, and nothing that reads files
  reaches it.
- `todo/2026-08-02-120201-a-form-set-is-a-registration-the-extension-answer-never-reaches`
  goes rather than stays in the queue. It is `D-ANS-015`'s form-set half, which
  `R-ANS-014` names as one of its three, and the text above answers both
  questions it asked.

## Assumed

- That a `config.yaml` which declares its own `typoscript`, `pagets` or `labels`
  path is rare enough to be a sentence rather than a field. None of the sets in
  the three checkouts declares one, and the answer says the list is the
  defaults.
- That an inline FlexForm is worth a report of its presence and not worth a
  quote. The XML is a document rather than a fact about the registration, and no
  core caller writes one.
- That `plugin.tx_form.settings.yamlConfigurations`, read out of the extension's
  own TypoScript, is the same statement as the registration. A site that
  registers another extension's form YAML from its own setup lands under that
  site's extension. That is where the file that says it lives.

## Wrong if

- An extension binds a FlexForm through a fifth shape and the answer reports the
  element with none. `unlistedFlexForms` would not catch it, because the answer
  read nothing, and only a session that opened the override file would notice.
- A form set turns out to register at run time rather than by its directory.
  `FormYamlCollectorConfigurator` reads `TYPO3_CONF_VARS` at
  service-instantiation time, so an `ext_localconf.php` that writes
  `disabledSets` switches a set off that this answer still lists.
- A session with the site set's file list still opens the set directory to find
  out what is in it. The names would then have been the wrong half, and the
  session wanted what each file declares.
- The `registerPlugin()` signature no longer comes from its first two arguments.
  Every identifier resolved through a variable would then be wrong rather than
  absent, which is the failure `R-ANS-012` exists to prevent.

## Confirmed on 2026-08-23

The three readings are where this put them and the four tests still hold them.
Nothing has reported any of the four **Wrong if**. The second gained half an
answer since. The schema of the entry names the field that switches a set off.
So a caller who reads the data learns what does it rather than finds out that
the list is longer than the installation's.
