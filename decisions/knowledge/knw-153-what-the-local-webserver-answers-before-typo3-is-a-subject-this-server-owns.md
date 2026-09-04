---
id: D-KNW-153
title: What the local webserver answers before TYPO3 is a subject this server owns
date: 2026-09-04
status: open
coveredBy:
  - HintsTest::whatTheLocalWebserverAnswersFirstIsStatedWhereTheOwnershipIs
---

# D-KNW-153 — What the local webserver answers before TYPO3 is a subject this server owns

**The corpus states that DDEV's generated nginx answers `/robots.txt` and
`/favicon.ico` itself, so a correct static route answers HTTP 404 locally and
works on the Apache it is deployed to.**

The configuration is right and the environment is what lies, so the obvious next
move — doubting the route — is the wrong one.

## Evidence

- `feedback/2026-09-03-235351`. A v14 project added the `routes` entry, flushed
  caches, and got HTTP 404 from nginx. The session confirmed the route by
  patching the copy inside the container and reloading: HTTP 200, `text/plain`,
  correct body.
- Read in this repository's own `.environments/e-site-14.3`, which
  `bin/cli environment:create` makes: `.ddev/nginx_full/nginx-site.conf` carries
  `location = /favicon.ico` and `location = /robots.txt`, each with
  `access_log off; log_not_found off;` and no `try_files`, so neither request
  reaches `index.php`. The file's fourth line is `#ddev-generated`, and
  `.ddev/.gitignore` excludes both it and
  `apache/apache-site.conf`.
- The Apache side is the core's own. `root-htaccess` ends in
  `RewriteCond %{REQUEST_FILENAME} !-f` and `RewriteRule ^.*$ index.php`, so a
  request for a path that is not a file reaches TYPO3 — which is why the same
  route works in production and fails locally.
- `webserver_type: nginx-fpm` is what `.ddev/config.yaml` carries, and its own
  comment names `apache-fpm` as the alternative.

## Decided

- Step 1a, and the statement goes to `project-configuration-files`. That hint
  already owns the boundary between what DDEV generates and what the project
  writes, and this is a third file on the same boundary rather than a subject of
  its own.
- The `routes` statement of `site-sets` names it, because that is where the
  failure is met.
- The fix stated is `webserver_type: apache-fpm`, which matches the local
  environment to the server rather than fighting DDEV for a generated file.
- The report's second half is left out. It says `ddev config --webserver-type=`
  rewrites `config.yaml` and discards its comments, and nothing here can
  establish that without registering a project on the machine — so the corpus
  says to set the key and does not say what running that command costs.

## Assumed

- That the environment this was read in is the one a project has. It is DDEV's
  own generated template for a `type: typo3` project, so a project that has not
  taken the file over has this one.

## Wrong if

- DDEV drops those two location blocks, which would make the statement a warning
  about something that no longer happens.
- A session reports the same failure on `apache-fpm`, which would mean the
  webserver type is not what decides it.
