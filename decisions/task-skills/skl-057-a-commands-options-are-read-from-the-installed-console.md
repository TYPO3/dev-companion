---
id: D-SKL-057
title: "A command's options are read from the installed console"
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::theSetupOptionsAreReadFromTheConsole
---

# D-SKL-057 — A command's options are read from the installed console

**A caller reads which options the setup command offers from the installation's
own console. `typo3_documentation_lookup` keeps what a value means and what the
command refuses.**

The core composes one of those option descriptions from the packages the
installation has active. So the manual's option list and this installation's
differ by a package, and no version-bound page can carry the difference.

## Evidence

- `feedback/2026-08-18-070611`, a boot of the t3g/blog DDEV installation on
  14.3.6 in `/home/benji/projects/blog`. Step 3 of *Create one where none is
  declared* in `typo3-development-installation` routes the option set to
  `typo3_documentation_lookup`. The session ran `ddev exec typo3 setup --help`
  instead. It reports the option names, the defaults that already match DDEV,
  and `--distribution` rendered as
  `[disabled] Requires typo3/cms-impexp to be installed`. It filed the
  substitution as a strength rather than as a cost.
- `.checkouts/14.3/typo3/sysext/install/Classes/Command/SetupCommand.php:151`
  composes that description from
  `$this->packageManager->isPackageActive('impexp')`, so the string is a fact
  about the installation and not about the version. `.checkouts/main` carries
  the same composition; `.checkouts/13.4` has none, so the property starts at
  14.
- It is the only one of its kind. Swept on 2026-08-18, `isPackageActive` occurs
  in two of the core's own command classes under `.checkouts/14.3`. Only
  `SetupCommand` uses it to compose an option description. The other,
  `ExtensionListCommand:98`, is in what the command prints rather than in what
  it declares.
- The console is reachable at that step. Its own command is what the step runs.
  So the binary that prints the option set is the one the install is about to
  run with.
- The manual half is not redundant, and the same step names what `--help` does
  not carry. The value a connection option accepts is not necessarily the value
  that lands in the settings afterwards. The command refuses a database that
  already holds tables.
- Step 4 is where the difference lands. `--distribution` is the seeding option,
  and a caller holding the manual's list would reach for the one option this
  installation reports as disabled.
- Both sources cost one call, so nothing here saves a round trip and
  `D-FBK-027`'s measure does not apply. What is at stake is whether the answer
  describes this installation.
- [AGENTS.md](../../AGENTS.md) already states the rule this is an instance of.
  The server reads facts an installation owns from that installation, because no
  bundled answer could be right for it.

## Decided

- **Rung 3, routing, and queued.** The source that owns the fact exists and the
  step sends the question elsewhere. The change is a sentence in a published
  skill, which [judging.rst](../../documentation/records/judging.rst) reviews
  rather than improvises.
- **The boundary is the option set against what an option means.** Which options
  this console offers, and which of them its packages have disabled, is the
  installation's. What a value does to the settings, and what the command
  refuses, is the manual's. That is the split
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
  says a strength carries.
- **Rejected: a tool that answers a console command's options.** Both sources
  are one call, so nothing comes off the caller, and the surface would be
  Symfony's help output for every command an installation registers.
- **Rejected: replacing the lookup.** Two of the three things the step tells the
  caller to check are not in `--help`, so a routing that moved the whole
  question would take them with it.
- **The step names `--distribution` rather than states a rule.** The sweep above
  found one option of this kind, so a general sentence would generalise a single
  case. The one option is the one step 4 reaches for. That is
  [`D-SKL-048`](skl-048-a-build-workflow-says-a-symptom-is-a-lookup-trigger.md)'s
  *the example is the shape, not the id* read from the other side. Here the
  concrete thing is what the caller needs and the rule is what would go stale.
- **The step says the property starts at 14.** A caller on 13.4 gets the same
  list from either source. A distinction stated where there is none is what a
  reader takes for a version-independent rule.
- **No requirement.** What one would state is every routed lookup, and one
  option on one command is not that.

## Assumed

- That the output the session quotes is what the checkout composes. Nobody here
  has that installation. What this entry verified is the code that writes the
  string.
- That the fact that arrives is worth the sentence. The session read
  `[disabled]` and stopped. So the measure is a caller told the truth about its
  own installation rather than a prevented failure.

## Wrong if

- A session reads the option set off the installed console and misses one of the
  two things the manual carries. The routing would then have moved the whole
  question where only half of it belonged.
- A second option of this kind appears, in `setup` or in another command a
  workflow here routes to. The name `--distribution` would then be the narrow
  form of a rule that had earned its statement. The step would turn out to teach
  one case.
- The option set the console prints turns out to differ from the manual's in
  ways a caller acts on wrongly. That is an option present here and
  undocumented, or a name the manual spells differently. The split would then be
  finer than two sources, and what the step owes is which one wins rather than
  which one to ask.

## Since then

The sentence stands in step 3, and a read of `.checkouts/13.4` during the write
made the version bound worth more than this entry states. That branch declares
no `--distribution` on `setup` at all, so the option is absent there rather than
merely unmarked. A caller below 14 given it unbounded would look for one that is
not in its help.

That is also why the bound survives the author contract's ban on a version
number in a skill body. What the ban keeps out is the version a step assumes
about the installation, which is re-asked; a boundary in the past is the
opposite, and `writing-a-skill.rst` now says so.
