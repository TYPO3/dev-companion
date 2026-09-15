---
id: D-ANS-158
title: A declared class, method or command is reached by its own name
date: 2026-09-15
status: open
coveredBy:
  - DocumentationTest::aDeclaredClassMethodOrCommandIsReachedByItsOwnName
---

# D-ANS-158 — A declared class, method or command is reached by its own name

**`Manual\Documentation` admits the classes, interfaces, methods and console
commands a manual declares, by the name a caller writes, the way it admits a
declared property.**

The answer told a caller that a class or method name has no page with its title.
That was true of the table of contents and false of the inventory. The inventory
of TYPO3 Explained at 14.3 declares 291 classes and 1507 methods, each with the
anchor of the section that documents it.

## Evidence

- **Read from `objects.inv.json` of TYPO3 Explained at 14.3 on 2026-09-15.**
  `php:class` 291, `php:interface` 10, `php:method` 1507, `std:console:command`
  61. A class arrives as `\TYPO3\CMS\Core\Page\AssetCollector` on
  `ApiOverview/Assets/Index.html#typo3-cms-core-page-assetcollector`. A method
  arrives as `\TYPO3\CMS\Core\Page\AssetCollector::addJavaScript`, a command as
  `vendor/bin/typo3 cache:flushtags`. The other three searched manuals declare
  none of these roles.
- **The 291 short class names are distinct.** The 1507 methods have 908 names
  between them. `getRequest` is declared on 48 pages, `getFile` on 23,
  `getRecord` and `getFolder` on 17 each. 23 names stand on more than five
  pages, and every one of those is a getter of an event.
- **What the search answered before.** `AssetCollector` alone reached "File
  collections" and "Working with collections" by the split of its second half.
  `cache:flushtags` reached the two cache pages by its first. The `insteadOf`
  hint on a miss told the caller to drop to the property or ViewHelper, because
  the index held nothing else.
- **What it answers now, live at 14.3.** `AssetCollector`, its qualified name
  and `AssetCollector::addJavaScript` each answer first with the Assets section
  they name. `cache:flushtags` inside a sentence about a deploy answers first
  with the command's own section. `getRequest` alone answers as before, with no
  event page for it.

## Decided

- The roles this admits are `std:confval`, `php:class`, `php:interface`,
  `php:method` and `std:console:command`, in one list. What admits an object is
  what admits a property since `D-ANS-144`. That is a query word written the way
  code is, or a query that is nothing but the name. A colon between two words is
  now such a shape, because that is how a command and a method are written.
- A class is reached by its short name or its qualified one. A method is reached
  as `Class::method` in either spelling of the class, and never by the method
  alone. A query that names only `getRequest` asks for none of the 48 pages in
  particular, and six of them would read as an answer.
- A ViewHelper argument is not admitted. The ViewHelper reference declares 736
  of them, `value` on 48 pages and `additionalAttributes` on 38. The
  ViewHelper's own page is already titled after its tag.
- The result carries the display name as its title. So a class answers as its
  qualified name, and a command with the binary in front of it. That is what the
  manual states, and a caller reads which one it got.

## Assumed

- That a name written in code form names the subject of the question. That is
  `D-ANS-144`'s assumption, and it holds here for the same reason.
- That the three other manuals keep declaring none of these roles. A manual that
  starts to declare methods with generic names admits them by `::` only, which
  is the rule above.

## Wrong if

- A ranked prose question loses its page to a declared class. The class is
  admitted only for a word in code form, so the question has to carry one. The
  seven queries of `D-ANS-032` answer as before on 2026-09-15.
- A caller writes a method alone often enough that the miss costs more than the
  48 pages would. Then the rule admits a method by its own name where it stands
  on one page.
