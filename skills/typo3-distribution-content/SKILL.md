---
name: typo3-distribution-content
description: Ship the content of a TYPO3 site inside an extension, as a distribution — the initial content a package carries, the export artifact and the files beside it, and the site configuration that travels with them. Use for initial content, data.xml, Initialisation, seeding a page tree, and a distribution that has to come up on a fresh installation.
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Distribution Content

Produce the content a distribution ships, in an order where each step decides
what the next one can be. Keep this skill as routing and workflow. Never keep
command options, table names, package names or version numbers. Each of those
belongs to a tool that releases on its own cycle. You cannot ask any of them
again from here.

## Where this starts

Work through [references/base.md](references/base.md) first. Two of its answers
are this workflow's entry condition rather than findings. Those are the
installation you produce the content in, and the extension that carries it. You
can export nothing before both exist. Where the installation is the one that is
missing, say so. Bring it into existence in the workflow that owns that before
you come back.

Then read what an extension ships content as, once, before you write anything:
`typo3_hint_lookup` with `id=sitepackage-initial-content`. It names the three
other ids this workflow turns on. Fetch each of them at the step below that
needs it rather than now.

## Seed what exists nowhere yet

A distribution's first content has nothing to export from. So the artifact
begins as records a script writes into the installation. `typo3_hint_lookup`
with `id=datahandler-seeding` owns that. It says what has to boot, which user
the write runs as, and what one call resolves for itself.

What this workflow adds is that you **read the seed back out of the installation
before you export it**. A record takes the defaults its configuration gives it,
which are not the defaults its columns carry. One of those decides whether a
visitor gets a page at all. So query the installation for what landed: the tree,
the elements on it, the relations that hang off them.

Where it is wrong, correct the script rather than the records. The script is
what the next version of the artifact comes out of. A record you fix by hand is
a correction the next export loses.

## Export it

`typo3_hint_lookup` with `id=impexp-artifact` owns the export. It says which
command writes it and what the package has to require for the import to exist at
all. It says what the command leaves out without a word. It says where the
command puts the file, which is not where the argument said.

`typo3_documentation_lookup` at the version you ship is the other half. Ask for
the page on how you create a distribution. That page has already decided which
records belong in the artifact and which are the receiving installation's own.

What this workflow adds is that **a command that reports success is not evidence
that the export worked**. It reports the same success for an artifact without
its images and for one without a whole table. Both failures are invisible until
somebody installs the package. So check the artifact, not the message. Every
table the tree holds is a part of it. The part that carries files is there, with
one file per referenced file beside it.

## Place it in the package

The artifact and the directory of files that belongs to it both move into the
extension. They take the names the import looks for;
`id=sitepackage-initial-content` states them. Neither arrives there on request.
The export keeps only the last segment of the name you gave it. So to put both
in place is a step of this workflow and not an argument to the command.

## Ship the site configuration beside the export, not inside it

A site configuration can travel two ways, and they are not equivalent. One of
them loses the address the site answers on, because the receiving installation
cannot know it. `typo3_hint_lookup` with `id=initial-content-references` owns
which route does what, and what survives an import at all.

What this workflow adds is what the intact route copies: **the whole directory
the site keeps its configuration in**. It does not copy only the one file in it
that is obviously configuration. Whatever else the installation put beside it is
part of the site and has to travel with it. The rendering that defines the site
is among that. Left behind, the receiving installation resolves the site, finds
every page, and renders nothing. That reads as a broken import and is not one.

Only the root page gets rewritten to the record the import created. Anything
else in that configuration that names a record by number points at a stranger on
the second installation. So a configuration written for a distribution names as
few of them as it can. Where one is unavoidable, the package says so where
somebody reads it.

## Prove it on an installation that has never seen this package

The artifact is write-only on the installation it came from. The import runs
once and the installation remembers it. So a second import there proves nothing,
and a read of the document back is reasoning rather than verification.
`typo3_hint_lookup` with `id=initial-content-import-once` owns why, and names
the cheap form of the same proof.

The proof is an installation that has never had this package, with the package
active before the install runs. Check three things there. A session usually
stops at the first one alone.

1. The records arrived: the tree, the content on it, and the files it references
   as files rather than as rows.
2. The site answers on the address that installation names for itself, and it
   renders. A site that resolves and renders nothing passes every check you make
   from the console.
3. Every page the artifact carries answers, not only the one at the root. A page
   shipped invisible and a reference that points at a stranger both survive an
   import that reports nothing wrong.

Read what the receiving installation got with `typo3_configuration_lookup`
rather than off the files you gave it. The merged result is what it runs on.

Then report what the package is: which records it ships, which files, and which
site it configures. Name the installation you ran the three checks above on.
Draft the message for it with `typo3_commit_message_guide` and
`workflow="project"`. The artifact, its files and the site configuration are
that repository's own files, which is the workflow that argument names.

## Where this stops

This skill owns the content a distribution ships and the package that carries
it. That is the seed of what exists nowhere yet, and the export. It is where the
artifact and its files sit in the extension, and the site configuration beside
them. It is the installation that proves the result. It owns both directions of
that one thing: the write of the content and its shipment. Neither half is
complete without the other.

It does not own what makes up the content. The templates, the elements an editor
fills in, and the extension that renders them are the sitepackage's. The
crossing is explicit in both directions. On the way out, state what the artifact
verifiably contains and stop before you edit that owner's files. On the way in,
a package whose content has to ship is this workflow from the seed onwards.

It does not own an installation either. To bring one into existence, and to
repair one that came up wrong, belong to the installation workflow. This one
uses two of them and creates neither. Where you cannot get the second
installation, say that the proof did not run. Do not substitute a read of the
artifact for it.
