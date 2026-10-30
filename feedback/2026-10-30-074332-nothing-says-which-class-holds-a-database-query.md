---
date: 2026-10-30T07:43:32+00:00
category: missing-knowledge
status: open
---

# nothing says which class holds a database query

## Observation

A session on a professional update project wrote a private `findBy` method
into a service class. The maintainer reported it. The run is not identified
here yet, so the first step of the card reads it in the project it came from.

Two things are wrong with that method, and this corpus prevents neither. A
private method cannot be called from anywhere else, so the next caller writes
the query again. The name `findBy` is repository vocabulary, so the class says
it is a service and the method says it is a repository.

**The corpus states where a query lives once, and for the route with no PHP in
it.** `frontend-records` says a table of your own needs no model, no
repository, and no controller, because a data processor reads it. Where a
session does write PHP, nothing says which class holds the query.
`persistence-reading` is the hint such a task reaches, and it answers which
rows come back rather than which class asks for them. `extbase-domain-mapping`
carries `Classes/Domain/Repository/` in `appliesTo` rather than in a hint, so
the string matches a query and instructs nobody.

**The greenfield runs of 2026-10-29 split on the same question.** Both wrote a
console command over `tx_jambositepackage_waste_report`. `claude-opus-5` wrote
`Classes/Domain/Repository/WasteReportRepository.php` and gave the command two
methods to call. `claude-fable-5-1` gave `WasteDigestCommand` a
`ConnectionPool` and built four query builders in the command body. Neither run
raised the choice. So the gap shows in a new site and in an existing one.

**The core gives no rule to copy, and a statement that asserts one is wrong.**
On `typo3/cms-core` v14.3.10, 204 classes below `Classes/` use `ConnectionPool`
or `QueryBuilder`. The class name ends in `Repository` for 27 of them, in
`Controller` for 23, and in `Service` for 13. The count comes from the core a
trial installed, because the machine has no `.checkouts/`.

So this corpus cannot say that a query belongs in a repository rather than in a
service. It can say what a repository buys, and it can say what the private
method costs.

## Suggestion

Say what a repository buys when an extension writes PHP of its own. That is one
place per table, the restrictions set once, and a seam a test replaces. Say it
as a project convention rather than a core rule, because the core queries from
controllers, services, and upgrade wizards too.

Say that a query method is not private. A private `findBy` is a query the next
caller writes again, and a method no test reaches directly.

This moves the same boundary the content blocks statement moves. The layout of
somebody's own `Classes/` is architecture rather than TYPO3, and this server has
answered for TYPO3. `typo3_extension_describe` already reads that layout and
states nothing about it.

The audit is where such a statement lands first. `typo3-extension-health`
reviews a repository against its checkout, and a private query method in a
service is the kind of finding that skill reports.
