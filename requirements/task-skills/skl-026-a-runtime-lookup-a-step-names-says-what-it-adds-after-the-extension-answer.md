---
id: R-SKL-026
title: 'A runtime lookup a step names says what it adds after the extension answer'
status: held
restsOn: [D-SKL-069]
heldBy:
  - SkillTest::everyRuntimeLookupSaysWhatItAddsAfterTheExtensionAnswer
---

# R-SKL-026 — A runtime lookup a step names says what it adds after the extension answer

**Where a step names a runtime lookup, it says what that lookup adds after the
`typo3_extension_describe` call the order has already made.**

The two are not the same question. The extension answer says what one package
registers. It reads that from its own files and from the booted installation for
the tables, content elements and icons attributed to it. A runtime lookup says
what the installation resolved. That is the value every extension has had its
say on, and the identifier whatever package registered it. It is the label with
the overrides applied, and the module tree position the parent supplies. A step
that names the second without a word about it asks for a call the reader has
just made.

The word about what it adds is what makes the call survive the read. A session
with the identifiers, the modules and the language files from step 2 in hand
will not spend five round trips on registration reports. It will report those
surfaces as assessed off a registration list.

## From

The feedback of 2026-08-19 09:44, from a full audit of a blog extension before
its v14 release. The active skill named `typo3_backend_module_lookup`,
`typo3_icon_lookup`, `typo3_label_lookup`, `typo3_fluid_namespace_list` and
`typo3_configuration_lookup`, and the session called none. The reason was that
`typo3_extension_describe` had already returned the modules, the icons, the site
sets and the XLF files. The one that would have added something was
`typo3_configuration_lookup`. The extension registers a FormEngine data provider
with `depends` and `after`. The run judged the order off the registration in
`ext_localconf.php` rather than asked the installation what it resolved.
`D-SKL-069` is the judgement, and it puts the words in `skills/base.md`, which
every published skill carries a copy of.
