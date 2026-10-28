---
date: 2026-10-28T07:49:20+00:00
category: wrong-answer
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# the nginx robots.txt trap is a DDEV 1.25.3 regression, and the hint names no way to take the file...

## Observation

Task: serve robots.txt from a TYPO3 static route on a DDEV project, TYPO3 14.3, DDEV 1.25.3.

project-configuration-files states the trap correctly. The generated nginx-site.conf carries location = /robots.txt as an exact match with no try_files, so nginx answers 404 and TYPO3 never sees the request. That is what I observed.

Two things in that statement are wrong or missing.

It says the file carries #ddev-generated and that editing it is a change the next start overwrites. That holds while the marker is there. Remove the marker and DDEV stops rewriting the file, which is the mechanism the same hint describes for additional.php a paragraph earlier. The sentence closes off the one-line fix: a try_files in that location. It offers only webserver_type apache-fpm.

The trap is also bound to a DDEV version and the hint states it as standing. DDEV introduced it in 1.25.3 through PR 8461, which suppressed 404 logs for favicon.ico and robots.txt. The maintainer who wrote it says in issue 8607 that the robots.txt condition went in without a clear reason. It is fixed in 1.25.4, released 2026-09-02. So a reader on 1.25.4 or later chases a problem that is gone, and a reader on 1.25.3 is sent to change webserver when an upgrade settles it.

Two further facts, from the attempt. The generated config includes /mnt/ddev_config/nginx/*.conf inside the server block, so .ddev/nginx/*.conf can add configuration. It cannot override that location: nginx refuses to start with duplicate location "/robots.txt". And the edit has to land before DDEV next renders the file. I edited it and ran ddev restart in one step, and the marker came back.

## Query

typo3_hint_lookup ids=[project-configuration-files] targetVersion=14. Observed on DDEV v1.25.3 with webserver_type nginx-fpm, project type typo3.

## Suggestion

Bind the statement to the DDEV versions it holds on. It is 1.25.3 alone, and 1.25.4 carries the fix. Name issue 8607, because a reader on the broken version is served by an upgrade rather than by a change of webserver.

Say that removing #ddev-generated takes nginx-site.conf over, the way the same hint already says it for additional.php, and name the cost: the file stops tracking DDEV's own changes.

Say that .ddev/nginx/*.conf is included and cannot override an exact-match location, so it is no way out of this one.
