---
date: 2026-11-06T07:02:44+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/lina/projects/typo3/tools/dev-companion
---

# nothing says a project commits settings.php, and a session advised against it

## Observation

A maintainer asked a v14.3 project with this server which files to commit. The answer put `config/system/settings.php` under "don't commit", because it holds the encryption key, the install tool password hash and a database password. The maintainer judged that wrong: the configuration that later extensions write into that file is then lost.

The maintainer is right, and the core says why. `Core\Configuration\ConfigurationManager` lists `EXTENSIONS` among the keys it writes into `settings.php`, and `Core\Configuration\ExtensionConfiguration` is the wrapper over `TYPO3_CONF_VARS['EXTENSIONS']`. So every extension's configuration lands there, beside `SYS/systemMaintainers` and the feature toggles. A project that leaves the file out of the repository keeps none of it.

No statement here says a project commits that file. `project-configuration-files` in `knowledge/hints/project.json` states the owners of the two files in `config/system/`. It says `settings.php` is the configuration array written by TYPO3's setup and configuration tools. It says `additional.php` is the project-owned place for environment-dependent overrides, and then: keep that code in version control. The subject of that sentence is `additional.php`.

So the corpus describes what the file is and leaves what to do with it to the reader. It also names nothing that accumulates in it.

Two statements push a reader the other way. The security hint names a committed secret, its sink in the repository's history, and that deleting the file leaves the value in every clone. The DDEV statement in the same project hint ends: never commit production secrets. A session that reads that `settings.php` carries the install tool password hash, and then reaches those two, concludes the file stays out. That is a sound reading of what is written.

The distribution ignores the two files the other way around. A fresh 14.3.10 from `typo3/cms-base-distribution` ignores `/var/*`, `/vendor` and `/public/*`, and `git check-ignore config/system/settings.php` reports that file is not ignored. DDEV writes `config/system/.gitignore` and excludes `/additional.php` in it. So the generated file is the ignored one, and the answer inverted that.

The question also does not reach the statement. `bin/cli hints:probe "which files should I commit in a TYPO3 project"` ranks `new-project-line`, `extension-repository-dependencies`, `backend-modules` and `extension-asset-build`, and `project-configuration-files` is absent. The same hint ranks first for "is settings.php committed or gitignored". So the statement answers the narrow question and never meets the one a newcomer asks.

## Query

"Which files should I commit?" in a TYPO3 14.3 project with this server, after a session had built a sitepackage in it. Checked afterwards with `bin/cli hints:probe` for the commit question and for the settings question, a grep of `knowledge/` for a statement that commits the file, `git check-ignore config/system/settings.php` in a fresh 14.3.10, and `ConfigurationManager` and `ExtensionConfiguration` in that core.

## Suggestion

Say that a project commits `settings.php`, in `project-configuration-files`, where the owners are already stated. Say what is lost otherwise, because that is the part a reader cannot derive: the `EXTENSIONS` array every extension's configuration lands in, `SYS/systemMaintainers`, and the feature toggles.

Say where the secrets go instead. That hint already describes `additional.php` reading environment variables, so the statement is one clause: the values that must not be committed belong there rather than in a file left untracked.

Name the install tool password hash as the exception the answer was right to worry about. It sits in the committed file, and `feedback/2026-11-06-064446-the-install-tool-password-the-setup-sets.md` reports what that costs. A statement that says commit the file owes the reader the reset that follows.

Route the general question. "Which files should I commit" is what a newcomer asks after a first build, and it reaches four hints that do not answer it.

The maintainer states the practice this project uses, and no statement here
describes it. One `additional.php` is project-owned and committed. It reads
`Environment::getContext()` and includes `AdditionalConfiguration_<context>.php`
beside itself, so the environment selects the file rather than a name in the
code. It includes a `Credentials.php` last, which is the file that is not
committed. Both included files stand in `.gitignore`, and the credentials file
is out of bounds for an agent. The same file also includes an `additional.php`
that one of the project's own extensions ships.

Every include is guarded with `is_file()`, so a clone without the file boots.
That is the property a statement has to name. An unguarded include fails the
first request on a missing file rather than on configuration.

Two things a statement about the shape still owes the reader. The committed
file merges `$customChanges` after each include and never resets it. So an
earlier file's values are applied again after a later one, and each included
file reassigns that array rather than adding to it. And the context file
carries the trusted hosts pattern `project-configuration-files` names, so a
clone without it fails with exception 1396795884 until somebody writes it.
Which party writes that file on a new machine is the question the statement
answers.

A probe of the commit question measures the repair. It asserts `project-configuration-files` is in the answer, and that the answer says the file is committed.
