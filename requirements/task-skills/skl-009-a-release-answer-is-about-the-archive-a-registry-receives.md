---
id: R-SKL-009
title: 'A release answer is about the archive a registry receives'
status: open
judged: 2026-08-22
---

# R-SKL-009 — A release answer is about the archive a registry receives

**What this server answers about a release is about the archive a registry
receives and the mechanism that built it, not about the checkout.**

A checkout that passes every check it declares establishes nothing about what
ships. The archive is a different set of files. A mechanism that is not the
working tree chooses them, and it is not the same mechanism for every registry.
A version-control export honours the exclusion attributes committed beside the
files. A registry's own packaging tool applies an exclusion list that ships with
the tool and never reads those attributes. Both are correct about their own
rules. One commit hands two registries two different file sets, and nothing in
the repository reports it.

That difference is readable only between the archives. Against the working tree
the two mechanisms agree by construction, and against one archive alone there is
nothing to compare it to. Which mechanism left a file out, and where the list
that decided it lives, is part of the difference rather than background to it. A
rule the maintainer cannot find is a rule they cannot fix.

What a workflow around that owes is not settled, and this entry does not settle
it. Four questions are open. What such a task is for at all. That is to carry a
release out, to describe the procedure so the maintainer does it, or to set the
release tools up. Where it stops, since a tag, a push or an upload changes state
other people depend on. Whether the session runs the commands the project
declares as checks against the candidate. And whether the session resolves the
package from its declared dependencies somewhere other than the working tree,
which already has them. Nothing here has measured or decided any of the four.

Extension release is due to come back, and what it comes back as is open. A
session worked `typo3/tailor` setup first, and it turned out not to be the
piece. What it asks of an extension is four facts, which `extension-ter-release`
in `knowledge/hints/extension.json` carries, and nothing in them is an order a
workflow keeps. The card that carried the question went to the maintainer on
2026-08-04, 2026-08-12 and 2026-08-19, and the third answer deleted it. Whether
the release run earns a skill instead is open and nothing queues it. The run
stops short of `ter:publish`, which needs a TER token nothing here holds. What
revives the question is a filed session that brings the words, or somebody who
wants the release driven end to end.

## From

The feedback of 2026-07-30 17:44 and its re-run in `E-EXT` on 2026-07-31, seven
commits past a tag. No skill activated and the session called no tool across
forty-one `Bash` calls. Not one of the six published skills carried the words
*release*, *publish*, *registry*, *artifact*, *archive* or *tag*. The run
established the gap the user asked it to name. The export and the registry
tool's archive shipped different file sets out of one commit. The difference was
editor configuration under version control that `git archive` dropped for an
`export-ignore` attribute and `tailor create-artefact` kept. Tailor filters by
its own `conf/ExcludeFromPackaging.php` and never reads `.gitattributes`. No
check in that green checkout said so. The counts the report gives, 1558 against
1559, do not square with the two files it names as the difference. The mechanism
is what the run established, and nobody can re-check the arithmetic from here.

A session reproduced the mechanism outside that checkout on 2026-08-04, against
`typo3/tailor` 1.7.0 installed into a scratch project and run over a fixture
extension. An `export-ignore` attribute on a tracked directory took it out of
`git archive` and left it in the artefact. No occurrence of `gitattributes` or
`export-ignore` exists anywhere in Tailor's source outside the filename it
excludes from the package. The same fixture established that the exclusion list
does not mean what it reads as. Its entries go into patterns unquoted.
Directories match `/^<entry>/i` against the path relative to the extension root,
files `/<entry>$/i` against the filename alone. So a top-level directory whose
name merely begins with a listed one drops out, `binx/` and `publicity/` for
`bin` and `public`. The same name nested deeper survives. Any regular-expression
character in an entry keeps its meaning, so the `.` in `phpstan.neon` takes
`phpstanXneon` with it. That is a second way one commit hands two registries
different file sets. It is readable neither in the archive nor in the list a
maintainer would go and read.

## Held by

Nothing. No skill orders this task, no tool answers it, and no scenario states
what a session would have to produce.
