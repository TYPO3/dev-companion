---
description: >-
  The run from a cloned project repository to a site that answers on both sides, in the order the steps have to go in, with what decides each one and what says it worked.
whenToUse: >-
  When you have to bring a repository that declares its own environment up locally and nothing is installed below it yet — a fresh clone, or one whose installation somebody tore down. For a package that declares no procedure, you create an installation instead, which starts a step earlier.
hints:
  - installation-boot
  - installation-setup
---

# Booting a Clone Into a Running Installation

The repository carries the code and the configuration. The installation is
everything else. To boot one, you supply what the clone does not carry. Each
step decides what the next one can be, so the order matters.

## What the Clone Does Not Carry

The database, the files the storages point at, and everything below `var/`. None
of the three is in version control. So a clone answers nothing until they
arrive, and you read in the repository what supplies them.
`typo3_project_describe` reports the environment and the pull recipes the data
comes from. It reports the hooks bound to each stage, with the command each
runs.

The environment configuration file carries the lifecycle whole. A start of the
environment runs it whether or not you read it.

Where that lifecycle exists, it is the procedure rather than a description of
one. A command you type beside it boots something the repository does not
describe. Read the repository's own instructions next to it. You cannot derive
from the code which data an installation here is meant to hold.

## The Order the Steps Go In

1. Start the environment the repository declares. It carries the interpreter and
   the database every later command runs against, so nothing before it is a
   step.
2. Install the dependencies inside that environment.
3. Start it a second time, because the first start ran before there was an
   installation for it to find.
4. Bring the data in: the import the repository declares, or the unattended
   install where it declares none.
5. Make the installation agree with the code in front of it: the schema first,
   then the caches.
6. Create a backend login, because the users arrive without their passwords.
7. Check the site's base against the host you serve it under.
8. Request the frontend and the backend. Where either does not answer, read what
   the installation wrote down.

Steps 1 to 3 are the environment. Steps 4 to 6 are console commands inside it.
Steps 7 and 8 say whether the boot worked. None of them changes anything that
already works. A boot is not a repair. Where a declared step fails, the finding
is which one failed and on what.

## Why You Start the Environment Twice

A DDEV project decides where the settings files go by detection rather than by
the start. On every start it looks for an installed TYPO3:
`vendor/typo3/cms-core/Classes/Information/Typo3Version.php` in a Composer
installation. When it finds none, it points both paths at the project root and
writes nothing. So the first start in a clone leaves no
`config/system/additional.php`. Every request then fails with exception
1396795884 for the trusted hosts pattern that file supplies.

 The detection also runs before the post-start hooks. So a hook that installs
 the dependencies gets the file at the next start rather than in that one.

The second start is therefore a step of the boot and not a repair. A
project-owned `additional.php` is the other way out, and it is there from the
first request. `typo3_hint_lookup` with the id `project-configuration-files`
says who owns that file and what it costs to take it over.

## Where the Data Comes From

The repository decides which of two cases this is. The answer is in what it
declares rather than in what a boot usually looks like.

Where it declares an import, that import is the step, and it is two imports
rather than one. An import is a dump, a pull recipe, or a hook that fetches one.
The database and the files are separate. So a clone that brought the database
and not the files keeps every `sys_file` row with nothing behind it.
`typo3 cleanup:localprocessedfiles` deletes the processed-file records whose
files are gone.

Where it declares none, the install is how the installation comes into
existence. You run it once and unattended. `typo3 setup` refuses an existing
`config/system/settings.php` with exception 1669747685. It refuses a database
that already holds tables with exception 1669747200. So the guard a script needs
is that file rather than an exit code.

Under DDEV the variables ride in on the command line.
`ddev exec bash -c '<assignments> vendor/bin/typo3 setup --no-interaction'` is
the form that carries every one of them. `typo3_hint_lookup` with the id
`installation-setup` says what the command takes and what each value becomes in
the file it writes. It says which options are inert on which major.

## Making the Installation Agree With the Code

Somebody dumped an imported database from a different point in the history than
the code in front of it. Two commands close that gap, in this order.

`typo3 extension:setup` is the first. It migrates the schema of every active
package before it runs each package's own setup. It applies the additive
suggestions only. It drops nothing and renames nothing.

 So a column the code no longer declares survives the run. A table the code
 needs and the dump lacks comes into being. `database:updateschema` is not the
 core's command. A repository whose hooks call it has `typo3-console` required
 beside the core.

`typo3 cache:flush` is the second, and you owe it because the dump carries
caches. The hash, pages and rootline caches use the database backend by default.
So the imported tables hold another installation's cached pages, hashes and
rootlines. The command empties those and the file-backed ones below `var/` with
them.

## The Login the Dump Did Not Bring

Backend users arrive with the dump and their passwords do not. So a boot creates
a user of its own with `typo3 backend:user:create`. Two of its answers show up
only in a script. It asks the password question even under `--no-interaction`
where neither the option nor the environment variable supplied one. So an
unattended hook waits on standard input instead of a failure.

It refuses a username the imported database already holds with exception
1670797516. That is how a second boot fails on the step that worked the first
time. `backend:resetpassword` is the way into a user that exists.

Past those two checks the command reports nothing of its own. So the `be_users`
row, or a login, says whether it created the user. `typo3_hint_lookup` with the
id `installation-boot` says which environment variables it reads where the
option is absent.

## The Host You Serve the Site Under

The site configuration is in the repository. So a clone serves under whatever
host its base names. That is rarely the host this machine reaches it on.

TYPO3 builds one route per site and per language out of that base. The route
requires the base's host, scheme and port. So a request that arrives on the
local host matches no site. The installation answers its own root with a
page-not-found rather than with the page tree.

A base that is a bare path carries no host requirement and matches every host.
`%env()` in the base is the other way one configuration serves two environments.

Above that sits the host check. TYPO3 refuses an `HTTP_HOST` that does not match
`SYS/trustedHostsPattern` with exception 1396795884. An empty pattern denies
every host there is. Read what the installation resolved with
`typo3_configuration_lookup` rather than off the files. A generated
`additional.php` merges over what the site configuration and the install wrote.

## What Says the Boot Worked

The site that answers is the proof, and it is two requests. One is the frontend
on the URL the installation names for itself, and the other is the backend. A
green start says the container came up. A command that exits 0 says it ran.
Neither says the installation serves anything.

Where a side does not answer, read the failure from what the installation wrote
down, not from the page. That is `typo3_hint_lookup` with the id
`installation-exception-output`.

The two failures a boot is likeliest to meet are in this document. A trusted
hosts pattern that refuses the local host answers HTTP 500. TYPO3 never writes
that to the log at all. A site base that matches no host answers the project
root with a page-not-found and throws nothing anywhere. Tear nothing down to
establish any of it. An installation somebody asked for and you then destroyed
is a change nobody asked for.
