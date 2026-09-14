---
id: R-KNW-056
title: 'The placement answer names the document root as a place a script may not go'
status: held
restsOn: [D-KNW-026, D-KNW-045]
heldBy:
  - HintsTest::whereAOneOffScriptMayNotGoNamesTheDocumentRoot
---

# R-KNW-056 — The placement answer names the document root as a place a script may not go

**A caller who places a one-off script learns that the document root is a place
it may not go. The web server serves a file there, and it outlives the run.**

Which directory that is belongs in the same answer, and so does the project root
above it. Where such a script goes already had an answer. The only place it
named as wrong was `var/`, for the one reason that `var/` is in the ignore list.
That leaves the directory a session reaches for unmentioned, and the reason it
gives does not carry. A file in the document root is not in the ignore list; the
server serves it.

The directory's name is half of it. `extra.typo3/cms.web-dir` in the root
`composer.json` decides it, and `public/` is what a project has when the key is
absent. So an answer that names only a path is right about one project. The
container path DDEV serves from is what the same session sees in front of it.
The answer names it as the example it is.

Both reasons are necessary. That the server serves it is what makes the document
root different from anywhere else a file could land. That it outlives the run is
what covers the project root above it. That is where the reported file went, and
it is in the tree that ships to deployment.

To reach it is the other half. The words are the ones of a session that has
already decided to write the file rather than of one that asks where files
belong. The query this came from named a webroot, a PHP script and debugging,
and reached nothing.

## From

`feedback/2026-08-01-003938` (2026-08-01), a session that debugged the `Record`
class. It wrote `/var/www/html/check_record.php` into a DDEV TYPO3 project and
executed it in the container. The user rejected the action, and nothing in this
corpus had said not to.
