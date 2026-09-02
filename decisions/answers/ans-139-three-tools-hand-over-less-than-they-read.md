---
id: D-ANS-139
title: Three tools hand over less than they read
date: 2026-09-02
status: open
---

# D-ANS-139 — Three tools hand over less than they read

**Each of the three opens the source that answers the caller's next question and
stops one field short of it, so what is built is the handing over.**

## Evidence

- `feedback/2026-09-02-135009`. A deprecation sweep returned 75 titles, two of
  which stopped the session's work, and neither said what to write instead. It
  then read the two `.rst` files out of the installed core package for their
  Migration sections and used both heavily. `Installation\Changelog::read()`
  opens that same file for every matched entry and parses the title, the tags
  and the stated removal out of it. The section it does not take is between
  `Impact` and the end of a file it already holds.
- `feedback/2026-09-02-135200`. A concept query for "paw animal pet" came back
  as eleven icons the caller's own two extensions register, each labelled
  `substring "animal"`. The tool scores term by term and writes what each
  matched into `why`; what it does not write is the terms that matched nothing,
  so the answer cannot say that two of the three words reached no icon at all.
  `typo3_forge_lookup` answers exactly that shape on a miss, as `terms`.
- `feedback/2026-09-02-135028`. The component catalog answered three questions
  right off classes and sub-components, and cost the session two silent failures
  where the contract was a data attribute: `data-on-change`, which only
  `EXT:redirects`' own module implements, and `data-bs-content` on a modal that
  reads `data-content`. The catalog derives classes and custom properties from
  the installed backend sources; the attributes a component's own module reads
  are in those same sources and are not taken.

## Decided

- All three are built, and each is queued with the boundary written here rather
  than left to the card. What justifies it is the measure `D-FBK-027` sets: each
  cost the session a read or a browser round trip for something the tool had
  already opened.
- **The changelog answers one entry whole.** A call that names a single entry —
  an issue number, or a query matching one — carries the Migration section; a
  sweep carries the titles as it does now. That is the feedback's own shape and
  the one the volume argues for: 75 migrations is not an answer.
- **The icon lookup says which query words reached nothing.** It is a miss
  statement per term rather than a ranking change, and `R-ANS-006` is the rule
  it belongs to. The tracker tool's `terms` is the model.
- **Ranking the caller's own icons below the core's is not decided here.** The
  session asks for it and the reason is good — a concept query asks what exists
  to borrow — but a project icon is legitimately reusable and one report is not
  enough to hide it behind. What the term statement does is remove the reason
  the session stopped looking, which is the half that is established.
- **The component catalog carries the data attributes a component's own module
  reads**, from the installed sources it already parses for classes. The
  boundary is the same one the classes have: what the installed backend
  declares, never what an extension adds.

## Assumed

- That the Migration section is parseable the way the title and the removal are.
  It is a section of the same reStructuredText and nothing has tried.
- That the attributes are derivable from the module sources. The session found
  the modal's set by grepping `dataset\.` out of a minified file, which is
  evidence they are in there and not that a parser finds them cleanly.
- That one report per finding is enough here. It is, because each names a source
  the tool already opens: the cost of building is small and the evidence that it
  was wanted does not have to carry the whole argument.

## Wrong if

- The Migration section on a single entry is as long as the sweep it replaced.
  The answer would then be a link to the file rather than the section, and the
  round trip is back.
- The attributes come back as noise — every `data-` an installed template
  happens to carry rather than the contract a module reads. The catalog would
  then be publishing what it cannot vouch for, which is what the curated index
  exists not to do.
- A session reports being sent to a core icon where its own was the right one.
  The decision not to rerank would then have been the wrong half to keep.
