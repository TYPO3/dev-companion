---
id: D-KNW-126
title: 'The syntax floor a core patch is bound by is a subject this server owns'
date: 2026-08-27
status: open
readings:
  - 2026-09-01
coveredBy: []
---

# D-KNW-126 — The syntax floor a core patch is bound by is a subject this server owns

**The PHP a patch must parse under is the floor of the oldest branch its
`Releases:` trailer names. `knowledge/hints/php.json` now states that rule.**

Step 1a of the ladder, closed on the spot. The corpus held both halves in two
places that never met. Which interpreter each line requires, and what the
trailer commits a change to. So the caller had to join them.

## Evidence

- The miss reproduces. `bin/cli hints:probe` on the feedback's own query reached
  `php-versions`, `deprecated-apis` and four others, none of which mentions a
  backport. No file below `knowledge/` or `skills/` joined the `Releases:`
  trailer to a PHP version before this change.
- The two halves that were already here. `php-versions` states the constraint,
  the platform pin and the tested range per covered line. The `Release Targets`
  section of `knowledge/documents/core/contribution/commit-messages.md` states
  what the trailer claims and stops there. That is where the defect is, and
  whether its severity earns an older line.
- The TYPO3 claim holds, read on all four checkouts. The `php` constraint is PHP
  ^8.1 on the oldest covered line, PHP ^8.2 on the two middle ones and PHP ^8.5
  on the development line. So a patch written on the development line and
  released on either middle one drops three minor versions.
- The trap is real and is one form rather than a family. `new X()->method()`
  occurs in 360 files below `typo3/sysext/` on the development line and in none
  on any released line. Asymmetric visibility and property hooks, which the
  feedback names beside it, occur nowhere in the core on any of the four.
- A measure settled the parse floor rather than recall. `php -l` under PHP 8.3,
  itself above the middle lines' floor, reports
  `syntax error, unexpected token "->"` for `new A()->m()` and accepts
  `(new A())->m()`.
- The branch's own tools remove the workaround the caller reaches for first.
  `Build/php-cs-fixer/config.php` sets `new_expression_parentheses` on the
  development line and on none of the three released ones. The tree shows what
  the rule's default does. 410 files on the newest released line write the form
  with parentheses against 41 on the development line. Every one of those 41 is
  a `new` with no member access after it.

## Decided

- The hint is the home, not `typo3_commit_message_guide`, which the feedback
  proposed. The tool sees a message and never a diff, so it could only warn that
  a floor exists. The session that needed the rule reviewed a file and reached
  the corpus by path and task, which is where the rule now sits.
- Not the `Release Targets` section either, for the same reason read forward.
  The constraint binds the diff, and the diff exists before anybody drafts the
  trailer.
- A hint of its own rather than a paragraph on `php-versions`, because the two
  answer different questions. Which interpreter a line requires, and which
  syntax a change may use. `D-KNW-030` puts one question in one hint.
- Closed on the spot rather than queued. The lookups this needed were the four
  checkouts and one `php -l`, all made in the judging run, and the change
  touches no code, no schema and no skill — `D-FBK-052`.
- The feature list the feedback offered is not carried. Asymmetric visibility
  and property hooks named would state a prohibition on syntax the core never
  writes. The hint states the rule and the one form that occurs, and names the
  run that finds any other.
- `coveredBy: []`. What the entry settles is where a statement lives and what it
  says, and no assertion distinguishes that from the same words in another file.

## Assumed

- The session that needs this arrives with a path and a task rather than with a
  commit message, which is how the reporting session arrived. A session that
  drafts the trailer first would meet the rule only through the document, and
  nothing here delivers it there.
- `new X()->method()` stays the only PHP 8.4 or later syntax the core adopts
  ahead of its released lines. The hint stands so that a second one costs a
  sentence rather than a rewrite.

## Wrong if

- A backport breaks on syntax the hint does not describe. Property hooks or
  asymmetric visibility that enter the development line, or a PHP 8.5 form after
  them. The rule still holds and the bound statement under it is then a list,
  which is the shape `D-KNW-030` warns about. The hint splits rather than grows.
- The development line drops `new_expression_parentheses`, or php-cs-fixer
  changes what its default does. Then the parentheses are the stable form after
  all and the second bound statement is wrong rather than stale.
- A session reports the same cost again after this, after it reached
  `typo3_commit_message_guide` and not the corpus. That would show the delivery
  judgement above backwards, and the answer is the tool warning the feedback
  asked for.
