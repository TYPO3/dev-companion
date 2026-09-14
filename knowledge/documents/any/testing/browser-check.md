---
description: >-
  How you look at a change in a real browser: which installation shows it, how a browser in a container reaches a DDEV site, and where the harness and its output go.
whenToUse: >-
  When you have to see a defect rather than assert it — a position, a stacking order, something that appears only while the page scrolls — and when a screenshot or a browser session has to run against an installation that already has the content.
hints:
  - browser-tests
  - browser-tests-outside-core
---

# Looking at a Change in a Real Browser

A spec asserts what somebody already knows. The look is the step before it, and
it needs an installation that can show the case at all.

Look at both sides. The backend is the half people skip, because every request
to check something names the frontend. No frontend screenshot shows what an
editor gets. That is the entry in the element wizard and the preview a record
draws in the page module. It is also the badges a record carries when nothing
declares its type.

## Which Installation Shows It

The core ships a suite that installs the instance its own browser tests run
against. It publishes that instance on a local port and leaves it up.
`typo3_test_run_guide` names it and prints what it costs. That instance is a
styleguide: it demonstrates components, and it carries no content of its own
beyond them.

You enter the backend on it as `admin` with the password `password`.
`Build/tests/playwright/config.ts` holds those two as the defaults, under
`ACCESSIBILITY_BACKEND_ADMIN_USERNAME` and
`ACCESSIBILITY_BACKEND_ADMIN_PASSWORD`. A run overrides them the same way.

Where the case needs content, look at the installation that has it. Content is
several languages, a page long enough to scroll, a particular TCA, or a record
only one installation has. That is usually a DDEV project that runs. The next
section says how a browser in a container reaches it.

## Reaching a DDEV Site From a Container

`ddev describe -j` gives you the names: the project's hostname, its
`primary_url` and its `httpurl`.

The DDEV router publishes ports 80 and 443 on `127.0.0.1` alone. A container you
start with `--add-host host.docker.internal:host-gateway` resolves the hostname
to the host's gateway address, where nothing listens. Every request fails with a
connection refused before any TYPO3 code runs. The route that works is the
network the router is already on:

```bash
docker run --rm --network ddev_default <image> <command>
```

The router joins `ddev_default` and carries each project's hostname as a network
alias on it. So `https://<project>.ddev.site` resolves inside the container, and
you configure nothing further.

Two conditions on that:

- **The container does not trust the certificate.** DDEV installs its
  certificate authority on the host, not in the image, so an HTTPS request fails
  verification. Use the `httpurl` from `ddev describe -j`, or set
  `ignoreHTTPSErrors: true` in the Playwright configuration.
- **A wildcard hostname is not an alias.** An additional hostname written as
  `*.example.ddev.site` is a name Docker's resolver cannot answer. Map it onto
  the router explicitly:

```bash
ROUTER=$(docker inspect -f '{{ (index .NetworkSettings.Networks "ddev_default").IPAddress }}' ddev-router)
docker run --rm --network ddev_default --add-host <hostname>:${ROUTER} <image> <command>
```

## Where the Harness and Its Output Go

Node resolves a dependency from the directory of the file that imports it,
upwards. So a Playwright configuration outside the directory whose
`node_modules` holds `@playwright/test` resolves nothing. A `node_modules`
symlink beside the configuration makes it run from anywhere.

Paths inside the configuration are relative to the directory you start the run
from. That is the same directory in the container. In a core checkout that is
`Build`. `Build/typo3temp/` is not ignored while `/typo3temp/*` is. So a
screenshot written as `./typo3temp/shot.png` lands in a directory the next
commit picks up. Write the harness and everything it produces below the
checkout's own `typo3temp/var/`. Git ignores that directory, so it cannot reach
a patch.
