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
`Releases:` trailer names, and that rule is now stated in
`knowledge/hints/php.json`.**

Step 1a of the ladder, closed on the spot. The corpus held both halves — which
interpreter each line requires, and what the trailer commits a change to — in
two places that never met, so the caller was left to join them.

## Evidence

- The miss reproduces. `bin/cli hints:probe` on the feedback's own query reached
  `php-versions`, `deprecated-apis` and four others, none of which mentions a
  backport. No file below `knowledge/` or `skills/` joined the `Releases:`
  trailer to a PHP version before this change.
- The two halves that were already here. `php-versions` states the constraint,
  the platform pin and the tested range per covered line. The `Release Targets`
  section of `knowledge/documents/core/contribution/commit-messages.md` states
  what the trailer claims — where the defect is, and whether its severity earns
  an older line — and stops there.
- The TYPO3 claim holds, read on all four checkouts. The `php` constraint is PHP
  ^8.1 on the oldest covered line, PHP ^8.2 on the two middle ones and PHP ^8.5
  on the development line, so a patch written on the development line and
  released on either middle one drops three minor versions.
- The trap is real and is one form rather than a family. `new X()->method()`
  occurs in 360 files below `typo3/sysext/` on the development line and in none
  on any released line. Asymmetric visibility and property hooks, which the
  feedback names beside it, occur nowhere in the core on any of the four.
- The parse floor was measured rather than recalled: `php -l` under PHP 8.3 —
  itself above the middle lines' floor — reports
  `syntax error, unexpected token "->"` for `new A()->m()` and accepts
  `(new A())->m()`.
- The workaround the caller reaches for first is removed by the branch's own
  tooling. `Build/php-cs-fixer/config.php` sets `new_expression_parentheses` on
  the development line and on none of the three released ones, and the tree
  shows what the rule's default does: 410 files on the newest released line
  write the parenthesised form against 41 on the development line, and every one
  of those 41 is a `new` with no member access after it.

## Decided

- The hint is the home, not `typo3_commit_message_guide`, which the feedback
  proposed. The tool sees a message and never a diff, so it could only warn that
  a floor exists; the session that needed the rule was reviewing a file and
  reached the corpus by path and task, which is where the rule now sits.
- Not the `Release Targets` section either, for the same reason read forward:
  the constraint binds the diff, and the diff is written before anybody drafts
  the trailer.
- A hint of its own rather than a paragraph on `php-versions`, because the two
  answer different questions — which interpreter a line requires, and which
  syntax a change may use — and `D-KNW-030` puts one question in one hint.
- Closed on the spot rather than queued. The lookups this needed were the four
  checkouts and one `php -l`, all made in the judging run, and the change
  touches no code, no schema and no skill — `D-FBK-052`.
- The feature list the feedback offered is not carried. Naming asymmetric
  visibility and property hooks would state a prohibition on syntax the core
  never writes; the hint states the rule and the one form that occurs, and names
  the run that finds any other.
- `coveredBy: []`. What the entry settles is where a statement lives and what it
  says, and no assertion distinguishes that from the same words in another file.

## Assumed

- The session that needs this arrives with a path and a task rather than with a
  commit message, which is how the reporting session arrived. A session that
  drafts the trailer first would meet the rule only through the document, and
  nothing here delivers it there.
- `new X()->method()` stays the only PHP 8.4 or later syntax the core adopts
  ahead of its released lines. The hint is written so that a second one costs a
  sentence rather than a rewrite.

## Wrong if

- A backport breaks on syntax the hint does not describe — property hooks or
  asymmetric visibility entering the development line, or a PHP 8.5 form after
  them. The rule still holds and the bound statement under it is then a list,
  which is the shape `D-KNW-030` warns about: the hint splits rather than grows.
- The development line drops `new_expression_parentheses`, or php-cs-fixer
  changes what its default does. Then parenthesising is the stable form after
  all and the second bound statement is wrong rather than stale.
- A session reports the same cost again after this, having reached
  `typo3_commit_message_guide` and not the corpus. That would show the delivery
  judgement above backwards, and the answer is the tool warning the feedback
  asked for.
