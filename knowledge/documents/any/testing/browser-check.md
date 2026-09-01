---
description: >-
  How a change is looked at in a real browser: which installation shows it, how a browser in a container reaches a DDEV site, and where the harness and its output go.
whenToUse: >-
  When a defect has to be seen rather than asserted — a position, a stacking order, something that only appears while scrolling — and when a screenshot or a browser session has to run against an installation that already has the content.
hints:
  - browser-tests
  - browser-tests-outside-core
---

# Looking at a Change in a Real Browser

A spec asserts what somebody already knows. Looking is the step before it, and
it needs an installation that can show the case at all.

Both sides are looked at. The backend is the half that gets skipped, because
every request to check something names the frontend — and what an editor is
handed is visible from no frontend screenshot: the entry in the element wizard,
the preview a record draws in the page module, and the badges it carries when
its type is one nothing declares.

## Which Installation Shows It

The core ships a suite that installs the instance its own browser tests run
against, publishes it on a local port and leaves it up; `typo3_test_run_guide`
names it and prints what it costs. That instance is a styleguide: it
demonstrates components, and it carries no content of its own beyond them.

The backend on it is entered as `admin` with the password `password`.
`Build/tests/playwright/config.ts` is where those two are the defaults, under
`ACCESSIBILITY_BACKEND_ADMIN_USERNAME` and
`ACCESSIBILITY_BACKEND_ADMIN_PASSWORD`, which is also how a run overrides them.

Where the case needs content — several languages, a page long enough to scroll,
a particular TCA or a record that only one installation has — the installation
that has it is the one to look at. That is usually a running DDEV project, and
reaching it from a browser in a container is the part below.

## Reaching a DDEV Site From a Container

`ddev describe -j` is where the names come from: the project's hostname, its
`primary_url` and its `httpurl`.

The DDEV router publishes ports 80 and 443 on `127.0.0.1` alone. A container
started with `--add-host host.docker.internal:host-gateway` resolves the
hostname to the host's gateway address, where nothing is listening, and every
request fails with a connection refused before any TYPO3 code runs. The route
that works is the network the router is already on:

```bash
docker run --rm --network ddev_default <image> <command>
```

The router joins `ddev_default` and carries each project's hostname as a network
alias on it, so `https://<project>.ddev.site` resolves inside the container with
nothing further to configure.

Two conditions on that:

- **The certificate is not trusted in the container.** DDEV's certificate
  authority is installed on the host, not in the image, so an HTTPS request
  fails verification. Use the `httpurl` from `ddev describe -j`, or set
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
upwards. A Playwright configuration written outside the directory whose
`node_modules` holds `@playwright/test` therefore resolves nothing; a
`node_modules` symlink beside the configuration is what makes it run from
anywhere.

Paths inside the configuration are relative to the working directory the run was
started from, which is the same directory in the container. In a core checkout
that is `Build`, and `Build/typo3temp/` is not ignored while `/typo3temp/*` is —
so a screenshot written as `./typo3temp/shot.png` lands in a directory the next
commit picks up. Write the harness and everything it produces below the
checkout's own `typo3temp/var/`, which is ignored and cannot reach a patch.
