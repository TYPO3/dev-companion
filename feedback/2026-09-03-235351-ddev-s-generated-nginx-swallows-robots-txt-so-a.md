---
date: 2026-09-03T23:53:51+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/site-tierheim
---

# DDEV's generated nginx swallows /robots.txt, so a correct static route looks broken locally

## Observation

Task: serve robots.txt from a TYPO3 v14 site's static routes on a project whose local environment is DDEV and whose production server is Apache.

I added the routes entry, flushed caches, requested /robots.txt and got HTTP 404 from nginx - not from TYPO3. The cause is DDEV's own generated nginx-site.conf, which contains

  location = /robots.txt {
      access_log off;
      log_not_found off;
  }

An exact-match location with no try_files, so the request never reaches index.php and the middleware never runs. The file carries #ddev-generated and is gitignored, and the container holds a copy made at start rather than a live mount, so editing it on the host changes nothing until a restart. I confirmed the route itself was fine by patching the copy inside the container and reloading nginx: HTTP 200, text/plain, correct body.

This is a nasty class of confusion because the configuration is right and the environment is what lies. Nothing in TYPO3 logs an error, nothing in the site configuration looks wrong, and the obvious next move - doubting the route - is the wrong one. On Apache with the shipped public/.htaccess the same route works, because the rewrite sends anything that is not a file to index.php; so a project deploying to Apache and developing on DDEV nginx sees the feature fail only locally.

The project-configuration-files hint is already detailed and accurate about DDEV owning config/system/additional.php, the #ddev-generated marker, and what taking that file over costs. So the boundary between DDEV's generated files and the project's own is a subject this server already covers - the webserver configuration is simply not part of it yet.

Secondary observation from the same task: `ddev config --webserver-type=apache-fpm` rewrites config.yaml wholesale and drops every comment the project had written into it, reordering keys into DDEV's canonical form. On this repository that destroyed two explanatory comment blocks. I reverted and changed the one line by hand.

## Query

typo3_hint_lookup task="XML sitemap configuration, records sitemap provider for a custom table, robots.txt route in site configuration, site sets and settings definitions" targetVersion="14" paths=["config/sites/main/config.yaml"] - returned project-configuration-files, which covers DDEV's ownership of additional.php but says nothing about its webserver configuration. Reproduce: a staticText route for robots.txt on a DDEV project with webserver_type nginx-fpm.

## Suggestion

Add a statement to project-configuration-files, or to a static-routes hint if one is written for the routes gap: DDEV's generated nginx configuration matches /robots.txt and /favicon.ico as exact locations with no try_files, so a static route or a page-type route for either answers 404 locally while working on Apache, where public/.htaccess rewrites anything that is not a file to index.php. The file is #ddev-generated and gitignored, and the container copies it at start rather than mounting it, so a host edit needs a restart. Setting webserver_type to apache-fpm makes the local environment match an Apache production server and is the fix that does not fight DDEV for ownership of a generated file.

Worth adding beside it: `ddev config --webserver-type=...` rewrites the whole config.yaml and discards the project's comments, so the one-line edit by hand is the safer route in a repository that documents its own configuration.
