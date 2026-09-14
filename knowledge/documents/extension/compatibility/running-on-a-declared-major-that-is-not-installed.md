---
description: >-
  How you stand a run against a TYPO3 major the package declares and the installation does not have up beside that installation, and what a green run there proves.
whenToUse: >-
  When a change has to hold on more than one declared major and the installation supplies one of them — before you write the claim about the other one down. It says what CI already covers, where the second Composer root goes, what it costs the installation, and how you tell a cell that could have failed from one that could not.
hints: []
---

# Running a Package on a Declared Major That Is Not Installed

A claim about a major is worth what you ran it on. What runs it is the package's
own suite resolved against that major, in a Composer root of its own. That root
stands beside the installation rather than in place of it. Nobody repins the
installation the developer works in. It is the one thing in the room nobody can
rebuild from the repository.

Whether a symbol is there on that major is the next question, and a read of the
branch settles it. This page begins where that one stops, at the run.

Where the installation runs in a container, every command below runs inside it.
The interpreter, the extensions and the database service are the container's. A
run on the host proves a cell nobody has.

## Ask What CI Already Covers, Before You Install Anything

The matrix a repository declares is one file. A read of it decides whether you
need the rest of this page at all. That file is `.github/workflows/`,
`.gitlab-ci.yml`, or whatever the repository's own configuration is. Read which
majors the matrix resolves and which commands it runs per cell. A matrix that
installs a major and then runs one linter has not run the suite there.

A push of the branch proves a covered cell, and that is the cheaper answer. It
proves the commit rather than the tree, so a claim about uncommitted changes
still needs the local run.

Nothing here reads that file for you. `typo3_project_describe` reports what the
repository declares as its own commands, which is what a cell should run. You
read the workflow files from the checkout.

## A Composer Root of Its Own

The second root is a directory with its own `composer.json`. Place it inside the
project so a container reaches it, and outside the document root so nothing
serves it. `var/` is both, and TYPO3's own base distribution already ignores
everything below it. A project whose ignore list differs is worth one look. A
vendor tree staged by accident is a large commit.

```json
{
    "repositories": [
        { "type": "path", "url": "../../packages/<key>", "options": { "symlink": true } }
    ],
    "require": {
        "typo3/cms-core": "^<the other major>",
        "<vendor>/<package>": "@dev"
    },
    "require-dev": {
        "typo3/testing-framework": "^<the release that major pairs with>"
    },
    "config": {
        "allow-plugins": {
            "typo3/class-alias-loader": true,
            "typo3/cms-composer-installers": true
        }
    },
    "extra": { "typo3/cms": { "web-dir": "public" } }
}
```

```bash
composer update --no-interaction    # in that directory
```

The path repository keeps one source tree. Composer symlinks the package into
the second root's `vendor/`, so you make a fix once and both cells run it. A
package that is its own repository root is the same arrangement without the
copy. The second root requires the package from its own checkout.

The root requires the other major, so the solver resolves the package's declared
range to that side. A range that does not resolve is the result rather than a
setback. It says the declaration is a claim nobody has installed.

The price is one core download and the disk that core takes. It buys a tree you
throw away with the directory. That is why it is cheaper than the pin the
question usually starts as.

## What It Writes, and What the Installation Keeps

Nothing of the installation moves. On the run this page came from,
`composer.json` and `composer.lock` were byte-identical afterwards. The
installed core answered the same version to its own console, and the frontend
answered as before. There is nothing to restore, because the run took nothing.

The two roots share the container and the database service. The resolve writes
neither.

## What the Second Root Resolves Differently

The platform pin does not carry over. An installation may pin the interpreter it
resolves against in `config.platform.php`. A fresh root has no such pin, so it
resolves against whatever PHP the container runs. Carry the pin over where the
cell is meant to be about that PHP. Otherwise the pin is a fact of the first
root alone.

The development tooling floats. The second root's solver takes the newest
release its constraints admit. So the test runner there can be a major above the
one the installation has. The same configuration file then goes through a
stricter reader in one cell than in the other. A warning only one cell prints is
about the runner rather than about the core.

## Whether the Database Survives

It does, and it is not the expensive half. A functional run never uses the
configured database itself. Each test class gets one derived from that name, and
the live one stays as it was. `typo3://guides/extension/testing/phpunit` says
what credentials it reads and what a finished run leaves behind. This page does
not repeat that.

So the second root needs no database of its own. Point it at the same service as
the installation, and that is the whole setup.

Both roots share that derivation. The name comes from the test class, so one
suite run in two roots claims one database name. The runs are serial; two at
once are one run that tears down the other's. The instance directory is per root
and collides with nothing.

## Which Checks Are Worth a Second Run There

Whatever reads the core: the unit suite, the functional suite, and static
analysis. Static analysis resolves the symbols it judges out of the core you
point it at. What reads only the package's own files says the same thing in both
roots, and you run it once. That is the formatter and the syntax check.

A cell that cannot fail proves nothing. A fresh root has several ways to skip
what you think it runs. Make it red before you believe it green. Assert
something only one of the majors has, and watch the cell that should fail, fail.
That is one throwaway test. It is the difference between a proof and a directory
that produced the word `OK`.

## What the Second Root Does Not Give

It is a resolved dependency tree, not an installation. It has no settings file,
no site configuration and no content, so nothing there serves a page. Its
console lists its commands and states the version it is. A command that reaches
the database stops on the connection named `Default`, which nobody has
configured. That reads like a broken setup, and it is the second root at work as
intended.

So this does not prove a claim about what renders on the other major. That needs
an installation of that major, which is a project of its own and a different
workflow.

## What It Leaves Behind

Remove the directory, and that is the whole teardown. The resolved tree, the
published assets and the test instances go with it. The per-class databases the
functional runs created stay until somebody drops them. That is the same set the
installed cell leaves, and `typo3://guides/extension/testing/phpunit` deals with
it.

Name the second root in the report by what it resolved rather than by where it
sat. The directory is gone by then. What stands is which major and which release
the suite ran against.
