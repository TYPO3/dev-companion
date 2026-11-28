---
name: typo3-distribution-content
description: Ship the content of a TYPO3 site inside an extension, as a distribution — the initial content a package ships, the export artifact and the files beside it, and the site configuration that travels with them. Use for initial content, data.xml, Initialisation, seeding a page tree, and a distribution that has to come up on a fresh installation.
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Distribution Content

This skill covers the content a distribution ships and the package that ships
it. That is the script that writes the content and the export that ships it.
Neither half is complete without the other. The skill ends with a proof on an
installation that has never seen the package. It stops before the templates and
the content elements, which `typo3-sitepackage-development` covers. It also
stops before an installation is created or repaired, which
`typo3-development-installation` covers.

**A command that reports success is not evidence.** The export, the import and
the console all report success for content that is incomplete. So every step
below checks the result itself, not the message.

Keep this skill as routing: it says which tool to ask and when to stop. Command
options, table names, package names and version numbers come from the lookups. A
copy of them here would go out of date in somebody else's project.

## Steps at a glance

1. Establish the installation and the extension.
2. Seed the content with a script, and read it back.
3. Export it, and check the export file.
4. Put the export file and its files into the extension.
5. Put the site configuration directory beside them.
6. Prove the package on an installation that has never seen it.
7. Report what the package ships, and commit it.

## 1. Establish the installation and the extension

1. Work through [references/base.md](references/base.md) first. It fixes the
   order every task starts in.

2. Check that two things exist. One is the installation you produce the content
   in. The other is the extension that will ship it. You can export nothing
   before both exist.

   If the installation is missing, say so. Invoke
   `typo3-development-installation` to create it, then come back here.

3. Read how an extension ships content, before you write anything. Call
   `typo3_hint_lookup` with `id=sitepackage-initial-content`. It names three
   more hint ids. Fetch each one at the step that needs it, not now.

## 2. Seed the content and read it back

The first content of a distribution has no source to export from. So a script
writes the records into the installation. Call `typo3_hint_lookup` with
`id=datahandler-seeding`. It says what has to boot and which user the script
writes as. It also says what one call resolves by itself.

**Read the seed back out of the installation before you export it.** A record
gets the defaults from its configuration, not the defaults of its columns. One
of those defaults decides whether a visitor sees a page at all. So query the
installation for what arrived:

- the page tree;
- the content elements on it;
- the relations of those elements.

If something is wrong, correct the script, not the records. The next version of
the export comes from the script. A record you correct by hand is lost at the
next export.

## 3. Export it

1. Call `typo3_hint_lookup` with `id=impexp-artifact`. It gives:

   - the command that writes the export file;
   - what the package must require, so that the import exists at all;
   - what the command leaves out without a warning;
   - where the command puts the file, which is not where the argument said.

2. Call `typo3_documentation_lookup` at the version you ship. Ask for the page
   on how to create a distribution. It says which records belong in the export
   file and which belong to the target installation.

Then check the export file, not the message of the command. The command reports
the same success for a file without its images and for one without a table.
Nobody sees either failure until somebody installs the package. Check two
things:

- Every table the page tree uses is in the file.
- The part for files is in the file, and one file for each referenced file is
  beside it.

## 4. Put the export into the extension

Move the export file and its files directory into the extension. They need the
names the import looks for, which `id=sitepackage-initial-content` gives.

The command does not put them there. It keeps only the last segment of the name
you gave it. So the move is a step of its own, not an argument to the command.

## 5. Put the site configuration beside the export

A site configuration can go into the package in two ways, and they differ. One
way loses the address the site answers on, because the target installation
cannot know it. Call `typo3_hint_lookup` with `id=initial-content-references`.
It says what each way does, and what survives an import at all.

**Copy the whole directory of the site configuration**, not only the one file in
it that is obviously configuration. Everything the installation put beside that
file belongs to the site. The rendering setup of the site is among it. Without
it, the target installation finds the site and every page, and renders nothing.
That looks like a broken import, but it is not one.

The import changes only the root page to the record it created. Every other
record number in the configuration then points at an unrelated record. So a
configuration for a distribution names as few record numbers as it can. If one
is unavoidable, write that down in the package, where people read it.

## 6. Prove it on a new installation

The installation the export came from cannot prove anything. The import runs
only once there, and the installation remembers that. So a second import there
proves nothing. A read of the export file does not prove the import either. Call
`typo3_hint_lookup` with `id=initial-content-import-once`. It says why, and it
gives a cheaper form of the same proof.

Use an installation that has never had this package. Activate the package before
the install runs. Then check all three of these. Sessions often stop after the
first one.

1. **The records arrived:** the page tree, the content on it, and the referenced
   files as files, not as rows.

2. **The site answers and renders**, on the address that installation names for
   itself. A site that resolves and renders nothing passes every console check.

3. **Every page in the export answers**, not only the root page. A page shipped
   hidden survives an import that reports no error. So does a record number that
   points at the wrong record.

Read the configuration of the target installation with
`typo3_configuration_lookup`, not from the files you gave it. That installation
runs on the merged result.

If you cannot get a second installation, say that the proof did not run. Do not
replace it with a read of the export file.

## 7. Report and commit

Report what the package ships: which records, which files, and which site it
configures. Name the installation you ran the three checks on.

Draft the commit message with `typo3_commit_message_guide` and
`workflow="project"`. The export file, its files and the site configuration are
files of the project repository. That is the workflow the argument selects.

## Where this skill stops

- **The templates, the content elements and the extension that renders them**
  are `typo3-sitepackage-development`. Stop before you edit those files, and
  report what the export file verifiably contains. A sitepackage whose content
  has to ship comes here, at step 2.

- **The creation or repair of an installation** is
  `typo3-development-installation`. This skill uses two installations and
  creates neither.
