:navigation-title: Working on the server

Working on the server itself
============================

For someone changing this repository rather than using it. The conventions are
in `AGENTS.md <../../AGENTS.md>`_; these are the commands they rest on.

.. image:: ../images/repository-map.svg
    :zoomable:
    :alt: The stdio runtime routes client calls through Server, Tool and Result
          classes to four answer sources, while the separate repository CLI runs
          upkeep commands over knowledge, evidence and work records.

The upkeep CLI
--------------

Everything this repository is kept in order by is one command — the requirement
and decision files, the forward-run scenarios, the hint corpus, the bundled
catalogs, and the core checkouts below. Run it with nothing and it says what it
supports:

.. code-block:: bash

    bin/cli                   # every command it carries, grouped by subject
    bin/cli todo:next         # the one todo that is due now, and nothing else
    bin/cli entries:lookup <path>  # what is written about the code you are about to change
    bin/cli repository:check  # requirements, decisions, scenarios and the todos against their formats
    bin/cli help <command>    # what one command takes, and what each argument is


``bin/typo3-dev-companion`` is the server itself and carries none of this.

Every command prints in one voice, and ``Voice`` is the class it is: a heading
in bold, rows under it, a verdict at the end with a green or a red mark before
it, a problem on the error stream, and a note in grey for what is context rather
than answer. A command that takes long draws a bar over its steps. The colour
and the bar are for a terminal, so a pipe, a log and ``--no-ansi`` get the same
words without them — ``D-DOC-067``.

Core checkouts
--------------

The knowledge is bound to TYPO3 versions, so writing it means checking a
statement on both sides of the boundary it claims. ``knowledge/versions.json``
declares the lines that are covered, and one command turns them into checkouts
this repository owns:

.. code-block:: bash

    bin/cli checkouts:update   # create what is missing, update what is there
    bin/cli checkouts:status   # what exists, at which revision


They land below ``.checkouts/``, which is gitignored — one treeless clone plus a
worktree per version, so four lines share one object store (under a gigabyte in
total). Nothing at runtime reads them: they are how the knowledge is verified,
not where the answers come from.

One command here does answer from one: ``bin/cli tools:record`` records the tool
pages against the newest released checkout. So a checkout carrying anything
``checkouts:update`` did not put there — a ``composer install``, first of all —
is refused rather than recorded from, and ``D-DOC-034`` is why.

Those recorded pages are also the only corpus that says what an answer costs the
caller who asked for it. ``bin/cli tools:measure`` reads them and prints the
text bytes and the data bytes per tool, worst first, so a trim starts at the top
of a list rather than at whichever tool somebody noticed. It calls nothing and
fails on nothing: a long answer can be the right one, and a counter that failed
would be answered by splitting a tool in two.

The same command keeps the packages the core pins rather than contains:
``typo3/testing-framework``, the harness a project extension tests in
(``D-KNW-106``), and ``typo3fluid/fluid``, the engine a template is parsed by
(``D-KNW-146``). Which release line belongs to which major is not recorded
anywhere: each covered branch pins it in its own manifest — the harness in
``require-dev``, the engine in ``require`` — and one worktree per pinned line is
checked out at that line's newest tag. So a statement about either is verified
in ``.checkouts/<package>/<line>`` the way a statement about the core is
verified in ``.checkouts/<branch>``, and ``bin/cli checkouts:verify`` re-reads
them.

Scenario environments
---------------------

A case is only meaningful in the working directory it names, and two of the five
``scenarios/readme.md`` defines are made by this checkout rather than found on
the machine:

.. code-block:: bash

    bin/cli environment:status             # which ones this checkout has, and which are missing
    bin/cli environment:create E-SITE      # a DDEV project with TYPO3 installed in it
    bin/cli environment:create E-SITE 13.4 # the same, on another covered version
    bin/cli environment:create E-NONE      # a directory with no installation above it


They land below ``.environments/``, which is gitignored. ``E-SITE`` is a run of
``ddev`` commands: a TYPO3 project, its containers, TYPO3's own base
distribution at the version asked for, the system extensions this server's
console path asks for, the setup that writes the database, the admin user and a
site configuration, and last the extension of the project's own — one table with
rows in it, which a base distribution registers nothing of (``D-EVI-010``).
Minutes on a cold Composer cache, seconds on a warm one, and running it again
finishes one that stopped halfway.

There is one installation per covered version, each its own directory and its
own DDEV project, and the version named none is the covered stable one. Asked
for one that is already installed the command starts its containers and puts
that extension in rather than building anything, so an environment is made once
and kept — ``D-EVI-006``, which also has what one costs on disk.

The development line is one of them and is built differently: from the base
distribution's ``dev-main`` at a dev stability, on PHP 8.5, because that is what
its core declares. It is the only line on which this server's answers about the
next major can be seen at all, and it moves under the machine daily — nothing
re-makes it, so ``ddev delete`` and ``create`` again is what refreshes it.

What it is for is a directory in which ``ddev exec vendor/bin/typo3 …`` answers
— the half of this server that no test reaches, and where both ``D-DIS-007`` and
``R-DIS-018`` were found by a real run instead. What it is not for is a recorded
forward review: a scaffold's defects are this repository's own, so a review
still runs in a real project. The other three environments say where they come
from when you ask for them, and the reasoning is ``D-EVI-004``.

The published documentation
---------------------------

This directory is published as a site, and nothing else in the repository is:
the site opens on :doc:`the manual's own page <../index>` and the checkout's
``readme.md`` is a file it does not carry — ``D-DOC-026``. What is generated is
a copy rather than these files.

**This repository writes the copy and stops there** — ``D-DOC-028``. The
renderer is a build tool and none of it is committed here, so a deployment
prepares the copy and installs a renderer of its own. Locally that is one
command, which fetches the renderer into ``.site/renderer`` the first time and
reuses it after:

.. code-block:: bash

    bin/cli documentation:preview         # the whole site, into .site/html
    php -S localhost:8000 -t .site/html   # read it at http://localhost:8000/

``--watch`` is both of those in one terminal: it serves the site on the port
``--port`` names, 8000 unless told otherwise, and renders again after every
save below ``documentation/`` or ``skills/``, saying which file it saw, until
Ctrl-C takes the server down with it. A render draws a bar over its steps, and
one that failed on a half-typed directive is rendered over by the save that
finishes it. The server reads the pages from disk on every request, so a reload
is all the browser needs.


``bin/cli documentation:prepare`` is the first of its steps on its own — the
copy, into ``.site/source``, with no renderer, no theme and no network. That is
what ``.github/workflows/documentation.yml`` runs before installing a renderer
into the runner's own temporary directory.

The order is not a choice. The renderer publishes the copy rather than these
sources, so a render before a prepare renders the previous one; the theme's
finish step reads the pages the renderer has just written, and it is what copies
the stylesheet, the script and the faces beside them and writes the index the
search bar fetches. The workflow spells the same three steps out, which is the
one thing here written down twice.

Delete ``.site/`` to render against the theme as it stands. The preview keeps
the renderer it fetched, and a deployment resolves it fresh on every run.

The site is read over a server rather than by opening ``.site/html/index.html``:
the search fetches its index as a file beside the pages, and a browser refuses
that fetch over ``file://``. Everything else on the page survives it, so a site
opened from disk looks whole and has no search.

87 of the links here point at a decision, a requirement or a class, and a
visitor of the site has none of those. The copy turns each of them into the file
on GitHub and leaves the rest as written, so these pages keep the paths a reader
of the checkout follows. It also publishes every ``readme.md`` as the
``index.md`` a generator serves as the directory itself, and drops the heading a
link names in another page, which this renderer answers by discarding the link.
What that costs is ``D-DOC-017``.

The renderer is phpDocumentor Guides, configured in ``guides.xml``. Nothing here
requires it: one package is asked for, ``typo3/soul-guides-theme``, and the
renderer comes with it. Resolved into this package's own ``require-dev`` it
would add 34 packages to every ``composer install``, so it is fetched into a
directory of its own instead — ``D-DOC-028``.

That configuration sits beside the pages, as ``documentation/guides.xml``, which
is where a TYPO3 extension keeps its own and where ``-c documentation`` names it
on the render step. Everything else stays relative to the working directory —
the ``input`` and ``output`` it declares, the renderer, the finish step — which
is why both commands are run at the root. It is the one file below
``documentation/`` that is not published: ``D-DOC-027``.

**What the page looks like is not this repository's to invent, and no longer
this repository's to carry either.** The design system publishes itself as a
theme for this renderer: the layout, the stylesheet, the script, the two
families, the icons and the search all ship inside that package, and what stood
here — a layout, a stylesheet, a script and an asset build of its own — is gone.
What is left here is ``guides.xml``, and everything the bar, the tab and the
footer say is configured in it rather than by copying a template. ``D-DOC-024``
is the move; ``D-DOC-023``, which vendored the same system by hand, is revoked
with it.

The front page is the one page set in that theme's ``marketing`` layout —
``D-DOC-030``. ``:layout: marketing`` stands above the title, because the parser
takes a field list as metadata only while no title has been found, and what
follows is a run of ``band`` directives with the page's claims in them. What a
band, a grid, a card and a surface take is
`the theme's own manual <https://typo3.github.io/soul-design-system/guides-theme/directives.html>`_;
nothing here renders any of them.

Two things in ``guides.xml`` are load-bearing. ``theme="soul"`` selects a theme
that has to exist first, and the extension element below it is what makes it
exist. ``automatic-menu`` is the other: the rail and the trail are a ``toctree``
in this renderer, which is a reStructuredText directive this markdown corpus
cannot write, and with it on the same tree is built out of the directories
instead. So **every directory of this documentation needs its own
``readme.md``** — a page whose directory has none is attached to nothing and
lands in no menu at all, which
``SiteTest::everyDirectoryOfTheDocumentationHasItsOwnPage`` is what stops.

The mark is this repository's own drawing and lives with the pages, as
``images/signet-s.svg``, ``-m`` and ``-l``: a signet is redrawn per optical size
rather than scaled, and a browser picks between them by the slot it needs. Each
is written the way the system asks artwork to be written — one ``var()`` with a
hex fallback per shape, and the whole drawing under one ``id`` — so a mark
referenced into the page carries the page's own ink and the file still renders
on its own.

One thing the local preview cannot show is the type. The faces are
``font-display: optional``, so a browser uses one only where it is already
cached — which is what stops the wordmark being re-laid out on every navigation,
and what makes ``php -S`` render the whole site in the fallback, since it serves
no cache header. What is deployed does.

``.github/workflows/documentation.yml`` runs all of it on every push to ``main``
and deploys the result to
`GitHub Pages <https://typo3.github.io/dev-companion/>`_. It needs
``Settings → Pages → Source: GitHub Actions`` on the repository: a deployment
from a branch serves the root or ``/docs``, and this directory is neither. Node
is there for the finish step alone, which is one bundled file and installs
nothing.

The drawings are the open half. A markdown image is an inline node, the theme
renders a figure for the reStructuredText directive alone, and a plain ``<img>``
is a document of its own that cannot be told which mode the page is in. So a
reader in dark reads a light drawing, and a drawing is read at the width of the
column rather than at the size it was drawn at. What each would need is in
``D-DOC-024``.

Tests
-----

.. code-block:: bash

    composer ci      # lint, coding guidelines, static analysis, tests — what CI runs
    composer test    # phpunit only
    composer stan    # phpstan only
    composer cgl     # bring every PHP file to the guidelines; cgl:ci only reports


``composer ci`` lints, checks the coding guidelines, runs the static analysis,
and runs the test suite: the search and ranking logic, every tool against its
declared schemas and annotations, and the stdio entrypoint driven as a real
subprocess. CI runs the same command on every supported PHP version.

A test that holds a decision or a requirement says so where it is:
``#[Decision('D-DOC-048')]`` and ``#[Requirement('R-COD-003')]`` over the
method, or over the class where the class as a whole is the answer.
``bin/cli decisions:cover`` and ``bin/cli requirements:cover`` write the entry's
``coveredBy`` and ``heldBy`` from those attributes, and the checks fail on a
copy that says anything else — so the entry cannot name a test that was renamed
away. A failing run ends with the entries the failures were holding, each with
its title and its path, which is what sends the session that made a test red to
the entry rather than to the assertion.

The guidelines are php-cs-fixer's, configured in ``.php-cs-fixer.dist.php`` and
nowhere else: PER-CS 3.0 plus the handful of rules this repository writes by —
strict types declared, imports sorted with global classes left unimported,
single quotes, trailing commas in multiline arrays. ``cgl`` rewrites the files
and ``cgl:ci`` reports what it would rewrite, which is the half ``ci`` runs
because a check may not change the code it is judging.
