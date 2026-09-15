:navigation-title: Working on the server

Working on the server itself
============================

For someone who changes this repository rather than uses it. The conventions are
in `AGENTS.md <../../AGENTS.md>`_; these are the commands they rest on.

.. image:: ../images/repository-map.svg
    :zoomable:
        :alt: The stdio runtime routes client calls through Server, Tool and Result
          classes to four answer sources. The separate repository CLI runs
          upkeep commands over knowledge, evidence and work records.

The upkeep CLI
--------------

Everything that keeps this repository in order is one command. The requirement
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

Every command prints in one voice, and ``Voice`` is the class it is. A heading
in bold, rows under it, a verdict at the end with a green or a red mark before
it. A problem on the error stream, and a note in grey for what is context rather
than answer. A command that takes long draws a bar over its steps. The colour
and the bar are for a terminal, so a pipe, a log and ``--no-ansi`` get the same
words without them — ``D-DOC-067``.

Core checkouts
--------------

The knowledge has TYPO3 versions as its bound, so a writer checks a statement on
both sides of the boundary it claims. ``knowledge/versions.json`` declares the
covered lines, and one command turns them into checkouts this repository owns:

.. code-block:: bash

    bin/cli checkouts:update   # create what is missing, update what is there
    bin/cli checkouts:status   # what exists, at which revision


They land below ``.checkouts/``, which git ignores. One treeless clone plus a
worktree per version, so four lines share one object store (under a gigabyte in
total). Nothing at runtime reads them. They are how a writer verifies the
knowledge, not where the answers come from.

One command here does answer from one: ``bin/cli tools:record`` records the tool
pages against the newest released checkout. So a checkout with anything in it
``checkouts:update`` did not put there, a ``composer install`` first of all,
gets a refusal rather than a record. ``D-DOC-034`` is why.

Those recorded pages are also the only corpus that says what an answer costs the
caller who asked for it. ``bin/cli tools:measure`` reads them and prints the
text bytes and the data bytes per tool, worst first. So a trim starts at the top
of a list rather than at whichever tool somebody noticed. It calls nothing and
fails on nothing. A long answer can be the right one, and a split of a tool in
two would answer a counter that failed.

The same command keeps the packages the core pins rather than contains.
``typo3/testing-framework``, the harness a project extension tests in
(``D-KNW-106``), and ``typo3fluid/fluid``, the engine that parses a template
(``D-KNW-146``). Which release line belongs to which major stands nowhere. Each
covered branch pins it in its own manifest, the harness in ``require-dev``, the
engine in ``require``. One worktree per pinned line stands at that line's newest
tag. So a writer verifies a statement about either in
``.checkouts/<package>/<line>`` the way a statement about the core in
``.checkouts/<branch>``. ``bin/cli checkouts:verify`` re-reads them.

Scenario environments
---------------------

A case only means something in the work directory it names. This checkout makes
two of the five ``scenarios/readme.md`` defines rather than finds them on the
machine:

.. code-block:: bash

    bin/cli environment:status             # which ones this checkout has, and which are missing
    bin/cli environment:create E-SITE      # a DDEV project with TYPO3 installed in it
    bin/cli environment:create E-SITE 13.4 # the same, on another covered version
    bin/cli environment:create E-NONE      # a directory with no installation above it


They land below ``.environments/``, which git ignores. ``E-SITE`` is a run of
``ddev`` commands. A TYPO3 project, its containers, TYPO3's own base
distribution at the version you ask for, the system extensions this server's
console path asks for. The setup that writes the database, the admin user and a
site configuration. Last the extension of the project's own, one table with rows
in it, which a base distribution registers nothing of (``D-EVI-010``). Minutes
on a cold Composer cache, seconds on a warm one, and a second run finishes one
that stopped halfway.

There is one installation per covered version, each its own directory and its
own DDEV project. The version named none is the covered stable one. Asked for
one that already exists the command starts its containers and puts that
extension in rather than builds anything. So an environment comes once and
stays, see ``D-EVI-006``, which also has what one costs on disk.

The development line is one of them and has a different build. It comes from the
base distribution's ``dev-main`` at a dev stability, on PHP 8.5, because that is
what its core declares. It is the only line on which anybody can see this
server's answers about the next major at all. It moves under the machine daily.
Nothing re-makes it, so ``ddev delete`` and ``create`` again is what refreshes
it.

What it is for is a directory in which ``ddev exec vendor/bin/typo3 …`` answers.
That is the half of this server that no test reaches, and where a real run found
both ``D-DIS-007`` and ``R-DIS-018`` instead. What it is not for is a recorded
forward review. A scaffold's defects are this repository's own, so a review
still runs in a real project. The other three environments say where they come
from when you ask for them, and the reasoning is ``D-EVI-004``.

The published documentation
---------------------------

This directory goes out as a site, and nothing else in the repository does. The
site opens on :doc:`the manual's own page <../index>` and the checkout's
``readme.md`` is a file it does not carry (``D-DOC-026``). The generator writes
a copy rather than these files.

**This repository writes the copy and stops there** — ``D-DOC-028``. The
renderer is a build tool and none of it sits in this repository. So a deployment
prepares the copy and installs a renderer of its own. Locally that is one
command, which fetches the renderer into ``.site/renderer`` the first time and
reuses it after:

.. code-block:: bash

    bin/cli documentation:preview         # the whole site, into .site/html
    php -S localhost:8000 -t .site/html   # read it at http://localhost:8000/

``--watch`` is both of those in one terminal. It serves the site on the port
``--port`` names, 8000 unless told otherwise. It renders again after every save
below ``documentation/`` or ``skills/``, says which file it saw, until Ctrl-C
takes the server down with it. A render draws a bar over its steps, and the save
that finishes a half-typed directive renders over the one that failed on it. The
server reads the pages from disk on every request, so a reload is all the
browser needs.


``bin/cli documentation:prepare`` is the first of its steps on its own — the
copy, into ``.site/source``, with no renderer, no theme and no network. That is
what ``.github/workflows/documentation.yml`` runs before installing a renderer
into the runner's own temporary directory.

The order is not a choice. The renderer publishes the copy rather than these
sources, so a render before a prepare renders the previous one. The theme's
finish step reads the pages the renderer has just written. It is what copies the
stylesheet, the script and the faces beside them and writes the index the search
bar fetches. The workflow spells the same three steps out, which is the one
thing here written down twice.

Delete ``.site/`` to render against the theme as it stands. The preview keeps
the renderer it fetched, and a deployment resolves it fresh on every run.

Read the site over a server rather than open ``.site/html/index.html``. The
search fetches its index as a file beside the pages, and a browser refuses that
fetch over ``file://``. Everything else on the page survives it, so a site
opened from disk looks whole and has no search.

87 of the links here point at a decision, a requirement or a class, and a
visitor of the site has none of those. The copy turns each of them into the file
on GitHub and leaves the rest as they are. So these pages keep the paths a
reader of the checkout follows. It also publishes every ``readme.md`` as the
``index.md`` a generator serves as the directory itself. It drops the heading a
link names in another page, which this renderer answers with a dropped link.
What that costs is ``D-DOC-017``.

The renderer is phpDocumentor Guides, configured in ``guides.xml``. Nothing here
requires it. The step asks for one package, ``typo3/soul-guides-theme``, and the
renderer comes with it. Resolved into this package's own ``require-dev`` it
would add 34 packages to every ``composer install``. So it lands in a directory
of its own instead (``D-DOC-028``).

That configuration sits beside the pages, as ``documentation/guides.xml``. That
is where a TYPO3 extension keeps its own and where ``-c documentation`` names it
on the render step. Everything else stays relative to the work directory, the
``input`` and ``output`` it declares, the renderer, the finish step. That is why
both commands run at the root. It is the one file below ``documentation/`` that
is not published: ``D-DOC-027``.

**What the page looks like is not this repository's to invent, and no longer
this repository's to carry either.** The design system publishes itself as a
theme for this renderer. The layout, the stylesheet, the script, the two
families, the icons and the search all ship inside that package. What stood
here, a layout, a stylesheet, a script and an asset build of its own, is gone.
What remains here is ``guides.xml``. Everything the bar, the tab and the footer
say stands in it rather than in a copied template. ``D-DOC-024`` is the move. It
revokes ``D-DOC-023``, which vendored the same system by hand.

The front page is the one page set in that theme's ``marketing`` layout —
``D-DOC-030``. ``:layout: marketing`` stands above the title, because the parser
takes a field list as metadata only while it has found no title. What follows is
a run of ``band`` directives with the page's claims in them. What a band, a
grid, a card and a surface take is
`the theme's own manual <https://typo3.github.io/soul-design-system/guides-theme/directives.html>`_;
nothing here renders any of them.

Two things in ``guides.xml`` carry weight. ``theme="soul"`` selects a theme that
has to exist first, and the extension element below it is what makes it exist.
``automatic-menu`` is the other. The rail and the trail are a ``toctree`` in
this renderer, which is a reStructuredText directive this markdown corpus cannot
write. With it on, the same tree comes out of the directories instead. So
**every directory of this documentation needs its own ``readme.md``**. A page
whose directory has none attaches to nothing and lands in no menu at all, which
``SiteTest::everyDirectoryOfTheDocumentationHasItsOwnPage`` stops.

The mark is this repository's own drawing and lives with the pages, as
``images/signet-s.svg``, ``-m`` and ``-l``. A signet gets a new drawing per
optical size rather than a scale, and a browser picks between them by the slot
it needs. Each follows the form the system asks of artwork, one ``var()`` with a
hex fallback per shape, and the whole drawing under one ``id``. So a mark
referenced into the page carries the page's own ink and the file still renders
on its own.

One thing the local preview cannot show is the type. The faces are
``font-display: optional``, so a browser uses one only where it is already in
the cache. That is what stops a second layout of the wordmark on every
navigation. It is also what makes ``php -S`` render the whole site in the
fallback, since it serves no cache header. What is deployed does.

``.github/workflows/documentation.yml`` runs all of it on every push to ``main``
and deploys the result to
`GitHub Pages <https://typo3.github.io/dev-companion/>`_. It needs
``Settings → Pages → Source: GitHub Actions`` on the repository. A deployment
from a branch serves the root or ``/docs``, and this directory is neither. Node
is there for the finish step alone, which is one bundled file and installs
nothing.

The drawings are the open half. A markdown image is an inline node, and the
theme renders a figure for the reStructuredText directive alone. A plain
``<img>`` is a document of its own that cannot learn which mode the page is in.
So a reader in dark reads a light drawing, and a drawing appears at the width of
the column rather than at its drawn size. What each would need is in
``D-DOC-024``.

Tests
-----

.. code-block:: bash

    composer ci      # lint, coding guidelines, static analysis, tests — what CI runs
    composer test    # phpunit only
    composer stan    # phpstan only
    composer cgl     # bring every PHP file to the guidelines; cgl:ci only reports


``composer ci`` lints, checks the coding guidelines, runs the static analysis,
and runs the test suite. That is the search and ranking logic, every tool
against its declared schemas and annotations, and the stdio entrypoint driven as
a real subprocess. CI runs the same command on every supported PHP version.

A test that holds a decision or a requirement says so where it is.
``#[Decision('D-DOC-048')]`` and ``#[Requirement('R-COD-003')]`` over the
method, or over the class where the class as a whole is the answer.
``bin/cli decisions:cover`` and ``bin/cli requirements:cover`` write the entry's
``coveredBy`` and ``heldBy`` from those attributes. The checks fail on a copy
that says anything else, so the entry cannot name a test somebody renamed away.
A failed run ends with the entries the failures held, each with its title and
its path. That is what sends the session that made a test red to the entry
rather than to the assertion.

The guidelines are php-cs-fixer's, in ``.php-cs-fixer.dist.php`` and nowhere
else. PER-CS 3.0 plus the handful of rules this repository writes by. Strict
types declared, imports sorted with global classes left unimported, single
quotes, a comma after the last item of a multiline array. ``cgl`` rewrites the
files and ``cgl:ci`` reports what it would rewrite. That is the half ``ci`` runs
because a check may not change the code it judges.
