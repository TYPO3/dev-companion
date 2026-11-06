---
date: 2026-11-06T06:54:13+00:00
category: missing-knowledge
status: closed
closed: 2026-11-06
model: claude-opus-5
tool: typo3_hint_lookup, typo3_rule_lookup
directory: /home/lina/projects/typo3/tools/dev-companion
---

# no statement says how a v14 install tool password is changed

## Observation

A maintainer asked a v14.3 project with this server how to change the install tool password, and said not to change it. The session answered from its own knowledge and said so: the server's hints had nothing specific on this. That flag is correct behaviour, and it is the hole. Three things in the answer are wrong or missing for 14.3.

**It misses the command the core ships.** `install:password:set` stands in `typo3/cms-install` as `Command\PasswordSetCommand`, and `ServiceProvider` registers it. It generates a password where the caller names none, and writes `BE/installToolPassword`. It also compares the settings against the parsed configuration, so it reports an `additional.php` that overrides the value. That command is the whole answer to the question, and the reply never names it.

**The standalone entry point is gone.** The answer sends the reader to `/typo3/install.php` behind an `ENABLE_INSTALL_TOOL` file. In 14.3 `Configuration/Backend/Routes.php` declares `/install` and `/install.php` as redirects, both targeting `EntryPointRedirectController::redirectAction`. The install tool is four backend modules, and `Configuration/Backend/Modules.php` gives their paths: `/module/system/maintenance`, `/module/system/settings`, `/module/system/upgrade` and `/module/system/environment`.

**The modules are not where the answer looks for them.** Each of the four declares `parent: system` and `access: systemMaintainer`. So they sit under System rather than under a group called Admin Tools, and a backend user outside `SYS/systemMaintainers` sees none of them. The reporter met exactly that and said there are no Admin Tools. Each module also carries `sudoMode` with the group `systemMaintainer`, which is the re-confirmation the answer half describes.

The reporter's installation shows the access rule rather than a defect. `Service\SetupService` adds the admin user it creates to `SYS/systemMaintainers`. So a user who sees no such module is either a different user or outside that list, and `config/system/settings.php` answers which.

One step in the answer is unverified and probably obsolete. It says a failed login prints the hash of the password that was typed, to paste into `settings.php`. This session found nothing in 14.3 that writes a hash into a login error. `install:password:set` removes the need for it either way.

Verified against `vendor/typo3/cms-install` of a fresh 14.3.10 from `typo3/cms-base-distribution`.

## Query

"How can I change the install tool password of my TYPO3 installation? Tell me the steps, and do not change it for me." Asked in a TYPO3 14.3 project with this server installed. Checked afterwards against the 14.3.10 core: `Configuration/Backend/Routes.php`, `Configuration/Backend/Modules.php`, `Classes/Command/PasswordSetCommand.php` and `Classes/Service/SetupService.php`.

## Suggestion

A document rather than a hint, because the gap is a procedure and not a statement. It goes into `knowledge/documents/`, where the caller needed it.

It names `install:password:set` first, because the core ships it and it writes the value. It names the four module paths and the `systemMaintainer` access, because a reader who is not a system maintainer sees no module and no statement here explains that. It says the two `/install` routes redirect, so nobody sends a v14 reader to a standalone entry point.

It says the backend password and the install tool password are two values that `typo3 setup` sets from one. `feedback/2026-11-06-064446-the-install-tool-password-the-setup-sets.md` reports that half, and a reader judges the two together.

The version bound is not established here. This session read 14.3.10 alone, and the statement needs the v13 side before it carries `since`. What to check there is whether `/typo3/install.php` serves the install tool rather than redirecting, and whether the four modules carry the same paths.

A probe of "how can I change the install tool password" measures the repair. It asserts the answer names `install:password:set`, and that it names no standalone `/typo3/install.php`.
