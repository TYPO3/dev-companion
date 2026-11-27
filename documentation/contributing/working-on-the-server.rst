:navigation-title: Working on the server

Working on the server itself
============================

This page covers the commands for a person who changes this repository rather
than uses it. It covers the upkeep CLI, the core checkouts, the scenario
environments, the published documentation and the tests. The conventions are in
`AGENTS.md <../../AGENTS.md>`_, and this page gives the commands they rely on.

.. diagram::
    :zoomable:
    :caption: The server surface and the repository workflow share paths and bootstrap, not responsibilities.

    flow canvas=960
      columns 1.5,1.1,1.4,1.1,1.1
      gap 24,40
    title "Runtime answers; upkeep maintains"
    description "The stdio runtime routes client calls through the Server, Tool and Result classes, which answer from five sources. The separate repository CLI runs upkeep commands over knowledge, evidence and work records. Both stand on one bootstrap and one Paths class."

    group runtime "Runtime: what clients ask"
      stdio "stdio entrypoint"
        at 0,0
        body "bin/typo3-dev-companion"
        ink
      server `Server/`
        at 1,0
        body "entrypoint · factory · transport"
      tool `Tool/`
        at 2,0
        body "with Sdk/: registry · schemas · dispatch"
      result `Result/`
        at 3,0
        span 2
        body "readable text + structured content"
      group sources "The five answer sources"
        knowledge `Knowledge/`
          at 0,1
          body "bundled corpus"
        installation `Installation/`
          at 1,1
          body "project and runtime"
        manual `Manual/`
          at 2,1
          body "official manuals"
        contribution `Contribution/`
          at 3,1
          body "Forge and Gerrit"
        publication `Publication/`
          at 4,1
          body "extension registries"
    group upkeep "Upkeep: what maintains this checkout"
      cli "repository CLI"
        at 0,2
        body "bin/cli"
        ink
      console `Upkeep/Cli`
        at 1,2
        body "registers every command"
      command `Upkeep/Command/`
        at 2,2
        body "one subject:verb each"
      records "Repository records"
        at 3,2
        span 2
        body "knowledge · evidence · todo · decisions"
        store
    foundation "Shared foundation"
      at 0,3
      span 5
      body "src/bootstrap.php locates the autoloader; src/Paths.php resolves this checkout."
      emphasis

    stdio -> server
    server -> tool
    tool -> result
    tool -> sources
    cli -> console
    console -> command
    command -> records

The upkeep CLI
--------------

One command keeps this repository in order. It covers the requirement and
decision files, the forward-run scenarios, the hint corpus, the bundled catalogs
and the core checkouts. Run it with no arguments and it lists what it supports:

.. code-block:: bash

    bin/cli                   # every command it has, grouped by subject
    bin/cli todo:next         # the one todo that is due now, and nothing else
    bin/cli entries:lookup <path>  # what is written about the code you are about to change
    bin/cli repository:check  # requirements, decisions, scenarios and the todos against their formats
    bin/cli help <command>    # what one command takes, and what each argument is


``bin/typo3-dev-companion`` is the server itself and has none of these commands.

Every command prints in one voice, and the class ``Voice`` is that voice:

* a heading in bold, with rows under it,
* a verdict at the end, with a green or a red mark before it,
* a problem on the error stream,
* a note in grey for what is context rather than answer,
* a bar over the steps of a command that takes long.

The colour and the bar are for a terminal. A pipe, a log and ``--no-ansi`` get
the same words without them — ``D-DOC-067``.

Core checkouts
--------------

The knowledge has TYPO3 versions as its bound. So a writer checks a statement on
both sides of the boundary it claims. ``knowledge/versions.json`` declares the
covered lines, and one command turns them into checkouts of this repository's
own:

.. code-block:: bash

    bin/cli checkouts:update   # create what is missing, update what is there
    bin/cli checkouts:status   # what exists, at which revision


The checkouts land below ``.checkouts/``, which git ignores. They are one
treeless clone plus a worktree per version, so four lines share one object store
(under a gigabyte in total). Nothing at runtime reads them. A writer verifies
the knowledge against them, and no answer comes from them.

The recorded tool pages
~~~~~~~~~~~~~~~~~~~~~~~

One command does answer from a checkout. ``bin/cli tools:record`` records the
tool pages against the newest released checkout. It refuses a checkout with
anything in it that ``checkouts:update`` did not put there, a
``composer install`` first of all. ``D-DOC-034`` gives the reason.

Those recorded pages are also the only corpus that says what an answer costs its
caller. ``bin/cli tools:measure`` reads them and prints the text bytes and the
data bytes per tool, worst first. The data counts as the compact JSON a client
gives the model. Above them the command prints each definition as ``tools/list``
sends it, with the output schema apart, largest schema first. So a trim starts
at the top of a list rather than at whichever tool somebody noticed.

``tools:measure`` calls nothing and fails on nothing. A long answer can be the
right one, and a split of a tool in two would answer a counter that failed.

Each client decides which half it gives the model, and ``D-EVI-011`` measured
two clients. Claude Code gives the model the data and drops the text. opencode
gives it the text. Neither gives it the output schema.

``bin/cli tools:tokens`` measures this again in Claude Code. It runs
``claude -p`` three times through ``bin/cli tools:proxy``. The proxy relays the
server with ``structuredContent`` or ``outputSchema`` taken away. The command
then reads from the trace what the ``tool_result`` contained and what it cost.
It costs money and half a minute, and it needs ``claude`` on the machine.

The packages the core pins
~~~~~~~~~~~~~~~~~~~~~~~~~~

``checkouts:update`` also keeps the packages the core pins rather than contains:

* ``typo3/testing-framework``, the harness a project extension tests in
  (``D-KNW-106``),
* ``typo3fluid/fluid``, the engine that parses a template (``D-KNW-146``).

No file states which release line belongs to which major. Each covered branch
pins it in its own manifest: the harness in ``require-dev``, the engine in
``require``. One worktree per pinned line sits at the newest tag of that line.
So a writer verifies a statement about either package in
``.checkouts/<package>/<line>``, as a statement about the core in
``.checkouts/<branch>``. ``bin/cli checkouts:verify`` reads them again.

Scenario environments
---------------------

A case means something only in the work directory it names.
``scenarios/readme.md`` defines five environments. This checkout makes two of
them rather than finds them on the machine:

.. code-block:: bash

    bin/cli environment:status             # which ones this checkout has, and which are missing
    bin/cli environment:create E-SITE      # a DDEV project with TYPO3 installed in it
    bin/cli environment:create E-SITE 14.3 --camino # with the content theme_camino ships, beside the empty one
    bin/cli environment:create E-SITE 13.4 # the same, on another covered version
    bin/cli environment:create E-NONE      # a directory with no installation above it


They land below ``.environments/``, which git ignores. ``E-SITE`` is a run of
``ddev`` commands, and it sets up, in this order:

* a TYPO3 project and its containers,
* the base distribution of TYPO3 at the version you ask for,
* the system extensions the console path of this server asks for,
* the setup that writes the database, the admin user and a site configuration,
* last, an extension of the project's own, with one table and rows in it.

A base distribution registers nothing of that kind (``D-EVI-010``). The run
takes minutes on a cold Composer cache and seconds on a warm one. A second run
finishes one that stopped halfway.

Each covered version has one installation, in its own directory and its own DDEV
project. Where you name no version, the command takes the newest released one.
Where the installation already exists, the command starts its containers and
puts that extension in rather than builds anything. So an environment comes once
and stays — ``D-EVI-006``, which also says what one costs on disk.

The development line is one of them, with a different build. It comes from the
``dev-main`` of the base distribution at a dev stability, on PHP 8.5, because
its core declares that. Only on that line can anybody see the answers of this
server about the next major. It moves under the machine daily. Nothing rebuilds
it, so ``ddev delete`` and ``create`` again refresh it.

An environment gives a directory in which ``ddev exec vendor/bin/typo3 …``
answers. No test reaches that half of this server, and a real run there found
both ``D-DIS-007`` and ``R-DIS-018``. An environment is never the subject of a
recorded forward review. The defects of a scaffold are this repository's own, so
a review still runs in a real project. The other three environments say where
they come from when you ask for them, and ``D-EVI-004`` gives the reasons.

The published documentation
---------------------------

The site publishes this directory, and nothing else in the repository. The site
opens on :doc:`the manual's own page <../index>` and leaves out the
``readme.md`` of the checkout (``D-DOC-026``). The generator writes a copy, and
the site renders from the copy rather than from these files.

**This repository writes the copy and stops there** — ``D-DOC-028``. The
renderer is a build tool, and no part of it sits in this repository. So a
deployment prepares the copy and installs its own renderer. Locally that is one
command. It fetches the renderer into ``.site/renderer`` the first time and
reuses it after:

.. code-block:: bash

    bin/cli documentation:preview         # the whole site, into .site/html
    php -S localhost:8000 -t .site/html   # read it at http://localhost:8000/

The local preview
~~~~~~~~~~~~~~~~~

``--watch`` does both of those in one terminal. It serves the site on the port
``--port`` names, 8000 by default. After every save below ``documentation/`` or
``skills/``, it renders again and names the file it saw. Ctrl-C stops it and
takes the server down with it. A render draws a bar over its steps. A save that
completes a half-typed directive renders over the render that failed on it.

The server reads the pages from disk on every request, so the browser needs only
a reload.

``bin/cli documentation:prepare`` is the first of the steps on its own: the
copy, into ``.site/source``, with no renderer, no theme and no network.
``.github/workflows/documentation.yml`` runs it before it installs a renderer
into the temporary directory of the runner.

Run the steps in this order. The renderer publishes the copy rather than these
sources, so a render before a prepare renders the previous copy. The finish step
of the theme reads the pages the renderer has just written. It draws every
element on them ahead of the browser and writes the index the search bar
fetches. The workflow spells out the same three steps, the one thing here
written down twice.

Delete ``.site/`` to render against the theme as it is now. The preview keeps
the renderer it fetched, and a deployment resolves it fresh on every run.

Read the site over a server, not from ``.site/html/index.html`` on disk. The
search fetches its index as a file beside the pages, and a browser refuses that
fetch over ``file://``. Everything else on the page works from disk. So a site
opened from disk looks whole and has no search.

The links in the copy
~~~~~~~~~~~~~~~~~~~~~

Many links here point at a decision, a requirement or a class, and a visitor of
the site has none of those. The copy turns each of them into the file on GitHub
and leaves the rest as they are. So these pages keep the paths a reader of the
checkout follows.

The copy also publishes every ``readme.rst`` as the ``index.rst`` that a
generator serves as the directory itself. It removes the heading from a link
that names one in another page, because this renderer drops such a link whole.
``D-DOC-017`` says what that costs.

The renderer and the theme
~~~~~~~~~~~~~~~~~~~~~~~~~~

The renderer is phpDocumentor Guides, configured in ``guides.xml``. Nothing here
requires it. The step asks for one package, ``typo3/soul-guides-theme``, and the
renderer comes with it. In the ``require-dev`` of this package, it would add the
whole dependency tree of the renderer to every ``composer install``. So it goes
into a directory of its own instead (``D-DOC-028``).

That configuration sits beside the pages, as ``documentation/guides.xml``. A
TYPO3 extension keeps its configuration there too, and ``-c documentation``
names it on the render step. Everything else stays relative to the work
directory: the ``input`` and ``output`` it declares, the renderer, the finish
step. So both commands run at the root. It is the one file below
``documentation/`` that the site does not publish — ``D-DOC-027``.

**The design system decides what the page looks like, and its theme package
ships it.** The design system publishes itself as a theme for this renderer. The
layout, the stylesheet, the script, the two font families, the icons and the
search all ship inside that package. The layout, stylesheet, script and asset
build this repository once had are gone. What remains here is ``guides.xml``,
which holds everything the bar, the tab and the footer say, rather than a copied
template. ``D-DOC-024`` is the move, and it revokes ``D-DOC-023``, which copied
the same system in by hand.

The front page is the one page in the ``marketing`` layout of that theme —
``D-DOC-030``. ``:layout: marketing`` stands above the title, because the parser
takes a field list as metadata only while it has found no title. A run of
``band`` directives with the claims of the page follows.
`The theme's own manual <https://typo3.github.io/soul-design-system/guides-theme/directives.html>`_
says what a band, a grid, a card and a surface take. Nothing here renders any of
them.

**Write a diagram as a** ``.. diagram::`` **directive in the page.** The finish
step of the theme draws it into the page before the site ships. So nobody draws
an SVG for a diagram or generates one. The maintainer settled that on
2026-11-27.
`Writing a diagram <https://typo3.github.io/soul-design-system/design-system/brand-and-imagery/diagrams/writing.html>`_
in the manual of the theme gives the text format and every layout. Three of its
rules decide whether a page gets a diagram at all:

* One claim per diagram. Write the claim as a sentence before you draw.

* A drawing that still works as a list is no diagram. Write the list.

* ``title`` and ``description`` are necessary. The title is the claim, and the
  description says what a reader who cannot see the drawing would see.

``theme="soul"`` in ``guides.xml`` selects a theme that must exist first, and
the extension element below it makes the theme exist. The rail and the trail are
the ``toctree`` that each directory's own page has. So **every directory of this
documentation needs its own ``readme.rst``**. A page whose directory has none
attaches to nothing and lands in no menu at all.
``SiteTest::everyDirectoryOfTheDocumentationHasItsOwnPage`` stops that.

The mark and the type
~~~~~~~~~~~~~~~~~~~~~

The mark is this repository's own drawing and lives with the pages, as
``images/signet-s.svg``, ``-m`` and ``-l``. A signet gets a new drawing per
optical size rather than a scale, and a browser picks between them by the slot
it needs. ``Server\Factory`` sends ``-s`` and ``-l`` to a client at
``initialize`` as the icons of the server. So a client lists the server under
the same mark.

Each drawing follows the form the system asks of artwork: one ``var()`` with a
hex fallback per shape, and the whole drawing under one ``id``. So a mark
referenced into the page takes the ink of the page, and the file still renders
on its own.

The local preview cannot show the type. The faces are
``font-display: optional``, so a browser uses one only where it is already in
the cache. That stops a second layout of the wordmark on every navigation. It
also makes ``php -S`` render the whole site in the fallback, because ``php -S``
serves no cache header. The deployed site does.

The deployment
~~~~~~~~~~~~~~

``.github/workflows/documentation.yml`` runs all of it on every push to
``main``. It deploys the result to
`GitHub Pages <https://typo3.github.io/dev-companion/>`_. The repository needs
``Settings → Pages → Source: GitHub Actions``. A deployment from a branch serves
the root or ``/docs``, and this directory is neither. Node is there for the
finish step alone, which is one bundled file and installs nothing.

The drawings are still open
~~~~~~~~~~~~~~~~~~~~~~~~~~~

A markdown image is an inline node, and the theme renders a figure only for the
reStructuredText directive. A plain ``<img>`` is a document of its own and
cannot learn which mode the page is in. So a reader in dark mode sees a light
drawing. A drawing also appears at the width of the column rather than at its
drawn size. ``D-DOC-024`` says what each would need.

Tests
-----

.. code-block:: bash

    composer ci      # lint, coding guidelines, static analysis, tests — what CI runs
    composer test    # phpunit only
    composer stan    # phpstan only
    composer cgl     # bring every PHP file to the guidelines; cgl:ci only reports


``composer ci`` lints, checks the guidelines, runs the static analysis and runs
the test suite. The suite covers the search and how it ranks, every tool against
its declared schemas and annotations, and the stdio entrypoint as a real
subprocess. CI runs the same command on every supported PHP version.

A test that holds a decision or a requirement declares it where the test is. It
has ``#[Decision('D-DOC-048')]`` and ``#[Requirement('R-COD-003')]`` over the
method, or over the class where the class as a whole is the answer.
``bin/cli decisions:cover`` and ``bin/cli requirements:cover`` write the
``coveredBy`` and ``heldBy`` of the entry from those attributes. The checks fail
on a copy that says anything else, so an entry cannot name a test somebody
renamed. A failed run ends with the entries the failed tests held, each with its
title and its path. So the session that made a test red goes to the entry rather
than to the assertion.

The guidelines are those of php-cs-fixer, in ``.php-cs-fixer.dist.php`` and
nowhere else. They are PER-CS 3.0 plus the few rules this repository writes by:

* strict types declared,
* imports sorted, with global classes left unimported,
* single quotes,
* a comma after the last item of a multiline array.

``cgl`` rewrites the files, and ``cgl:ci`` reports what it would rewrite. ``ci``
runs ``cgl:ci``, because a check may not change the code it judges.
