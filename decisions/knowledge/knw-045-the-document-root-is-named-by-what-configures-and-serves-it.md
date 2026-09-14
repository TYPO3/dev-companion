---
id: D-KNW-045
title: 'The document root is named by what configures and serves it'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::whereAOneOffScriptMayNotGoNamesTheDocumentRoot
---

# D-KNW-045 — The document root is named by what configures and serves it

**The clause that places a one-off script names the document root too and
identifies it by `extra.typo3/cms.web-dir`. It says a file there goes out to
visitors and stays behind.**

The docroot DDEV serves below `/var/www/html` stands with it, because that is
the path the session that reported this had in front of it.

[`D-KNW-026`](knw-026-the-one-off-script-rule-is-owed-the-place-it-does-not-name.md)
queued the lookup rather than wrote the sentence. Where a Composer
installation's document root is and what DDEV serves are questions no read of
this repository answers. This is what the lookup returned, and it moved the
sentence.

## Evidence

- The default is `public`. `typo3/cms-composer-installers` v5.0.2, the version
  every Composer installation on this machine has locked, carries
  `'web-dir' => 'public'` in `Config::$defaultConfig`. It merges
  `extra.typo3/cms` from the root package over it, and resets a `web-dir`
  outside the Composer root back to the default with a caveat. Where the root
  package is `typo3/cms` itself the default becomes `.`, which is the core
  checkout rather than a project. `InstallerScripts/EntryPoint` writes its
  target to `$pluginConfig->get('web-dir') . '/' . $this->target`, so the entry
  point lands in the same directory the setting names.
- DDEV mounts the project directory there. Its documentation for the performance
  settings says "DDEV mounts a fast Docker volume onto `/var/www/html` inside
  the `web` container". Its documentation calls `docroot` the "Relative path to
  the document root containing `index.php` or `index.html`", which "DDEV will
  attempt to detect ... otherwise falling back to the current directory". The
  TYPO3 quickstart configures
  `ddev config --project-type=typo3 --docroot=public`. Read at DDEV v1.25.1, the
  version installed here.
- So the served directory is `/var/www/html/<docroot>` and not `/var/www/html`.
  `site-new`, the project the feedback came out of, has `type: typo3` and
  `docroot: public` in `.ddev/config.yaml` and `extra.typo3/cms.web-dir` at
  `public` in its `composer.json`. It has a `#ddev-generated`
  `.ddev/nginx_full/nginx-site.conf` headed "ddev typo3 config" whose server
  block is `root /var/www/html/public;`.
- That is `D-KNW-026`'s first **Wrong if**. The file the session wrote,
  `/var/www/html/check_record.php`, sat at the project root, one directory above
  the document root, and nothing served it.
- The clause had moved as well. It is on `project-build-and-scripts` in
  `knowledge/hints/project.json` since the move in
  [`D-KNW-032`](knw-032-the-corpus-is-filed-by-question-and-two-splits-were-taken-back.md).
  It is not on `project-repository-layout` in
  `knowledge/architecture-hints/general.json` where `D-KNW-026` recorded it.
- Two of the three probes `D-KNW-026` recorded with no reach now reach it. On
  2026-08-03, before this change, "where do I put a one-off script" returned
  `project-build-and-scripts` at `appliesTo(14) + text(242)`. "one-off debug
  script placement in a TYPO3 project" returned it at `text only(253)`.
- The feedback's own query was the one that still reached nothing. Afterwards
  `bin/cli hints:probe "writing and executing a PHP script in the live webroot to introspect core classes"`
  returns `project-build-and-scripts` first, at `appliesTo(7) + text(274)`. "can
  I write a debug php file into the webroot" returns it at
  `appliesTo(7) + text(368)`.

## Decided

- Both reasons stand rather than one. A file that goes out to visitors is what
  separates the document root from every other wrong place. A file that outlives
  the run is what also covers the project root above it, which is where the
  reported file actually went. It is what the deployed tree carries afterwards.
- The setting names the directory and not a path, so a project whose `web-dir`
  is elsewhere reads the sentence correctly. The container path stands too,
  because that is what a session at work through DDEV sees.
- The clause stays on `project-build-and-scripts`. Placing a script is already
  that hint's sentence, and `var/` and the document root are the same question
  answered about two directories.
- `appliesTo` gains `debug script`, `scratch script`, `document root` and
  `webroot`; `one-off script` was already there. Those are the words of a
  session about to write the file, rather than of one that asks where files go.
- The introspection half stays where it landed. `feedback/2026-08-01-003933`
  owns it, and
  [`D-SKL-004`](../task-skills/skl-004-a-question-no-lookup-settles-is-read-from-the-installed-source.md)
  puts it in `skills/base.md` rather than in this corpus. So the two sentences
  are in one place each and neither stands twice.

## Assumed

- That a project has one document root and the site is the only thing served
  from it. A second vhost rooted elsewhere in the project goes out to visitors
  too, and DDEV ships an example for it. The sentence names only the first.
- That DDEV is the local environment the caller is in. Another one serves
  whatever its vhost roots at. The `web-dir` half of the sentence still holds
  there, and the `/var/www/html` half reads as the example it is.

## Wrong if

- A session gets the clause and writes into the document root anyway. That is
  step 4 and a rewrite, not a placement — carried over from
  `D-KNW-026`.
- DDEV no longer mounts the project at `/var/www/html`, or its TYPO3 project
  type no longer defaults the docroot to `public`. Both had their read at
  v1.25.1.
- `typo3/cms-composer-installers` moves its default off `public`. The sentence
  names the key first for that reason, but "public/ where that key is absent"
  would then be wrong.
- The next report of this comes from a session that never asked where a file
  goes. The rule would then have to arrive from the introspection question,
  which is `003933`'s route rather than this one's.
