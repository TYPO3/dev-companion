---
date: 2026-10-31T14:30:45+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5
tool: typo3_project_describe, typo3_task_guide
directory: /home/lina/trials/site12-with, /home/lina/trials/site12-without
---

# a second hostname below the project has no certificate

## Observation

Two runs added a second site to a DDEV project and gave it a hostname below the
project's own. Both wrote `additional_hostnames` and restarted the environment.
Both ended by naming the URL to open:

```
https://library.site12-with.ddev.site/
```

The name resolves, and the site answers. No browser opens it. The router serves
one certificate, and its names are these:

```
DNS:localhost, DNS:*.ddev.local, DNS:ddev-router, DNS:ddev-router.ddev,
DNS:ddev-router.ddev_default, DNS:*.ddev.site, IP Address:127.0.0.1
```

`*.ddev.site` matches one label. `library.site12-with.ddev.site` carries two
below `ddev.site`, so the certificate covers it nowhere:

```
curl: (60) SSL: no alternative certificate subject name matches target host
name 'library.site12-with.ddev.site'
```

## Why both runs could believe it worked

A request over HTTP answers, and so does a request that skips the check. The
site is correct, the base is correct, and the page renders. Only a client that
validates the certificate refuses, and that client is the user's browser rather
than anything the session runs.

So the run tests the site and the user tests the certificate. The session reports
success in good faith, and the user cannot open the one artifact the brief asked
for.

## What the corpus says and does not say

`site-base-collision` tells a session that a second site under one host carries
that host in its base as well. It is right, and it is the statement that sends
the session to a hostname of its own.

The `installation` intent covers the environment around it: the lifecycle hooks
in `.ddev/config.yaml`, what `additional.php` supplies, and the trusted hosts
pattern a clone fails on. None of it names a certificate. So the corpus routes a
session to a second host and says nothing about which hosts the environment can
serve.

## Suggestion

Judge whether one sentence belongs where the second host is proposed. A sibling
name under `ddev.site` stands one label deep and the wildcard covers it. A name
below the project stands two deep and the wildcard does not.

Verify before writing it that this holds beyond the machine this came from. What
this session measured is one router's certificate, on one host, on one day. The
statement to write is which names DDEV issues a certificate for, and that is a
read of DDEV rather than of TYPO3.

Leave out what to do about it. Whether the user accepts a warning, takes a
sibling name, or issues a certificate is the user's call, and the hint states
the options rather than picks one.

`SITE-13` carries both runs.
