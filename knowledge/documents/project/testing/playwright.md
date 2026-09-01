---
description: >-
  The Playwright configuration, the backend login and a spec per project a repository owns, whole, and the environment they read the site from.
whenToUse: >-
  When a repository that serves a TYPO3 site has no browser suite yet, for what a visitor gets and for what an editor does. A rendering test through a functional test is neither; it runs no script and speaks no HTTP.
hints:
  - browser-tests
  - browser-tests-outside-core
  - environment-variables
---

# Setting Up Playwright in a TYPO3 Project

This page is one step of a larger workflow: where a browser harness is being set
up rather than a spec repaired, `typo3-extension-testing` is what orders that
work. Looking at a change rather than specifying it is a different act at a
different moment, and `any/testing/browser-check` is that one.

The suite belongs to what is deployed rather than to a package: the specs need a
served site and a real URL. It runs in three projects — the frontend as a
visitor sees it, the login that authenticates once, and the backend journeys
that depend on it. The core's own configuration does not transfer — it points
its test directory into the core tree, writes its report below `typo3temp/` and
logs into the instance the install tool made.

## Build/playwright.config.ts

```ts
import { defineConfig } from '@playwright/test';
import * as path from 'path';
import * as dotenv from 'dotenv';

// Resolved from this file rather than from the working directory: dotenv's own
// default is process.cwd(), so the suite would read a different .env depending
// on where it was started.
//
// The cascade is the order of this list and nothing else. dotenv does not
// overwrite what is already in process.env, so a variable CI exports wins over
// both files, and the first file that defines one wins over the second. Do not
// add override: true to make .env.local win — it inverts the list, and the
// committed .env would beat the private file.
dotenv.config({
  path: [
    path.join(__dirname, '../.env.local'),
    path.join(__dirname, '../.env'),
  ],
  quiet: true,
});

export default defineConfig({
  testDir: './tests/browser',
  timeout: 30_000,
  expect: { timeout: 10_000 },
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  reporter: [['list'], ['html', { outputFolder: '../var/playwright-report', open: 'never' }]],
  outputDir: '../var/playwright-results',
  use: {
    baseURL: process.env.TYPO3_BASE_URL,
    ignoreHTTPSErrors: true,
    trace: 'on-first-retry',
  },
  projects: [
    {
      name: 'login',
      testMatch: 'helper/login.setup.ts',
    },
    {
      // No login dependency and no stored state: a visitor is anonymous, and a
      // backend session changes what the frontend renders.
      name: 'frontend',
      testMatch: 'frontend/**/*.spec.ts',
    },
    {
      name: 'e2e',
      testMatch: 'e2e/**/*.spec.ts',
      dependencies: ['login'],
      use: { storageState: path.join(__dirname, '.auth/backend.json') },
    },
  ],
});
```

## Build/tests/browser/helper/login.setup.ts

**Since:** 14

```ts
import { test as setup, expect } from '@playwright/test';
import * as path from 'path';

const state = path.join(__dirname, '../../../.auth/backend.json');

setup('authenticate in the backend', async ({ page }) => {
  const user = process.env.TYPO3_BACKEND_USER;
  const password = process.env.TYPO3_BACKEND_PASSWORD;
  expect(user, 'TYPO3_BACKEND_USER is not set').toBeTruthy();
  expect(password, 'TYPO3_BACKEND_PASSWORD is not set').toBeTruthy();

  await page.goto('/typo3/');
  await page.getByRole('textbox', { name: 'Username' }).fill(user!);
  await page.getByRole('textbox', { name: 'Password' }).fill(password!);
  await page.getByRole('button', { name: 'Login', exact: true }).click();
  await page.waitForLoadState('networkidle');

  await expect(page.locator('typo3-backend-sidebar-toggle')).toBeVisible();
  await page.context().storageState({ path: state });
});
```

## Build/tests/browser/helper/login.setup.ts

**Until:** 13

```ts
import { test as setup, expect } from '@playwright/test';
import * as path from 'path';

const state = path.join(__dirname, '../../../.auth/backend.json');

setup('authenticate in the backend', async ({ page }) => {
  const user = process.env.TYPO3_BACKEND_USER;
  const password = process.env.TYPO3_BACKEND_PASSWORD;
  expect(user, 'TYPO3_BACKEND_USER is not set').toBeTruthy();
  expect(password, 'TYPO3_BACKEND_PASSWORD is not set').toBeTruthy();

  await page.goto('/typo3/');
  await page.getByRole('textbox', { name: 'Username' }).fill(user!);
  await page.getByRole('textbox', { name: 'Password' }).fill(password!);
  await page.getByRole('button', { name: 'Login', exact: true }).click();
  await page.waitForLoadState('networkidle');

  await expect(page.getByRole('button', { name: 'Minimize/maximize module menu' })).toBeVisible();
  await page.context().storageState({ path: state });
});
```

## Build/tests/browser/frontend/pages.spec.ts

```ts
import { test, expect } from '@playwright/test';

// One entry per page type the site has: the list that finds the layout nobody
// rendered since the template changed.
const pages = [
  { name: 'the home page', path: '/' },
];

for (const page_ of pages) {
  test(`${page_.name} renders for a visitor`, async ({ page }) => {
    const response = await page.goto(page_.path);

    expect(response?.status()).toBe(200);
    await expect(page).toHaveTitle(/.+/);
    // TYPO3 answers 200 with its own error page where rendering threw, so the
    // status alone does not say the page came out.
    await expect(page.locator('body')).not.toContainText('Oops, an error occurred');
  });
}
```

## Build/tests/browser/e2e/backend.spec.ts

```ts
import { test, expect } from '@playwright/test';

test('the page module opens for an authenticated editor', async ({ page }) => {
  await page.goto('/typo3/module/web/layout');
  await page.waitForLoadState('networkidle');

  // The backend answers the login form for an unauthenticated request, so the
  // URL is what says the stored state was accepted.
  await expect(page).not.toHaveURL(/\/typo3\/login/);
});
```

## Reaching into a module

The spec above asserts the URL and nothing in the page, which is why it holds on
any installation. A spec that asserts what a module renders needs one more fact:
the backend shell puts the module into an iframe with the id
`typo3-contentIframe`, named `list_frame`, so a locator on `page` finds nothing
inside a module and `page.frameLocator('#typo3-contentIframe')` is where such a
spec starts. Nothing says so when it is missing — the locators simply match no
element.

Inside the page module, one content element is `.t3-page-ce`, carrying
`id="element-tt_content-<uid>"` with `data-table` and `data-uid` beside it, and
its preview is `.t3-page-ce-body`. The header the page module draws for a record
is inside that body, which is what makes "is the header there once or twice" an
assertion a preview template can be held to.

The module menu is the `nav` with the id `modulemenu`. Its accessible name is a
translated label, so a spec that addresses it by name asserts the backend's
language along with it, and the id does not.

## An Assertion Is Evidence Once It Has Been Seen to Fail

A green suite is a claim about its assertions and not about the site. Before a
new assertion counts, break the thing it is about — change the row, hide the
element, rename the identifier — run it, watch it go red, restore, and watch it
go green. Both directions, because an assertion that is red for an unrelated
reason proves as little as one that cannot fail.

It matters more here than in a unit suite, because three shapes pass silently in
a browser and all three read as a passing test:

- **A locator that matches nothing.** An assertion on an empty locator set, a
  filter that eliminated everything, a loop over a list that came back empty.
  Assert the count before the loop.
- **A substring that the surrounding chrome carries.** Module headers, the site
  name, navigation labels: they are on the page whatever the module under test
  rendered. Assert on data the page under test owns.
- **An assertion on text the module never emits.** This is what a wrong route
  produces — the backend answers with another module, whose chrome then matches
  the substring above. Assert on a marker the intended route owns.

The third one is the reason a module path is read rather than remembered. A
backend module is addressed by its own path, its identifier is what an alias
keeps, and a spec carried over from an older project can therefore name a path
that has moved. `typo3_backend_module_lookup` answers what the path is in the
installation the suite runs against.

## The environment the suite reads

```dotenv
TYPO3_BASE_URL=https://example.ddev.site
TYPO3_BACKEND_USER=admin
TYPO3_BACKEND_PASSWORD=
```

Both files sit at the project root, beside `Build/`, which is where a project
that reads a `.env` at all keeps it — so the suite reads the same file the rest
of the repository does rather than one of its own. Commit `.env` with the
password left empty, and keep `.env.local` out of the repository for what one
developer fills in. CI exports the same three as environment variables and needs
neither file.

That the suite brings its own reader is not a duplication of something the
installation does. TYPO3 ships no `.env` reader: whatever puts that file into
the process environment — the development environment, the container runtime, a
dotenv component the project required — is the project's own choice, and a
project that has made none still runs this suite. The two readers meet on one
file and depend on each other for nothing.

Three properties of the loader decide that layout, and none of them is the
default anybody assumes. It resolves a relative path against `process.cwd()`, so
the configuration passes paths of its own — otherwise a suite started from the
project root and one started from `Build/` read different files, and one of them
reads none. It does not overwrite a variable already in the environment, which
is what lets CI win over both files without either being touched. And of the
files, the first one in the list that defines a variable is the one that wins.

`override: true` is the option to leave alone. It does not make the private file
win — it inverts the list, so the committed `.env` overwrites `.env.local` and
the developer's own value is the one that disappears. The cascade is the order
of that list and nothing else.

## What the login setup asserts, and why it differs by version

The setup logs in once and every other project depends on it, so the
authenticated state is written once and reused. That is not only about speed:
the backend rate-limits failed logins per client, and a suite that logs in per
spec spends that budget on itself. A run that has tripped it answers "your login
attempt did not succeed" to correct credentials until the limiter's window
passes or `var/cache/data/ratelimiter/` is emptied.

What tells a successful login from a rejected one is a backend element, and that
is what moves between majors — the two sections above are the same file with
that one assertion changed. The specs themselves need no such split: whether the
backend answered the module or the login form is a question the URL answers.

`getByLabel('Password')` is the locator to avoid on this form. The visibility
toggle beside the field carries `Toggle password visibility` as its accessible
name, so the label matches two elements and Playwright refuses the ambiguity —
intermittently, because the toggle is added by script and a fast run can fill
the field before it exists. `getByRole('textbox', { name: 'Password' })` names
the one element meant.

## When the extension itself is the Composer root

An extension repository that installs TYPO3 into itself — `config.bin-dir` and
`config.vendor-dir` below `.build/`, `extra.typo3/cms.web-dir` at
`.build/public` — runs everything above unchanged. `config/` and `var/` sit at
the repository root rather than below the web directory, because
`typo3/cms-composer-installers` sets the application directory to the Composer
root and refuses any other value. So the report and the results land where the
configuration already sends them, and the `.env` files stay beside `Build/`.

The two things that differ are the two the suite never names: the site is served
out of `.build/public`, and the console is `.build/bin/typo3`. A spec reaches
the site over HTTP through `TYPO3_BASE_URL` and calls no console, so neither
path appears in a Playwright file.

## What is not committed

`.env.local` is the first entry. `Build/.auth/` holds the session the setup
writes and is generated, and so are `var/playwright-report/` and
`var/playwright-results/`, which the configuration puts below `var/` because
that is where a TYPO3 project already keeps what it does not deploy. Ignore all
four. Accepted visual baselines are the exception and belong beside the specs
that own them.
