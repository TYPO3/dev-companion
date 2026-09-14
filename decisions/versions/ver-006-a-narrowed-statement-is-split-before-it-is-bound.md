---
id: D-VER-006
title: A narrowed statement is split before it is bound
date: 2026-08-18
status: confirmed
---

# D-VER-006 — A narrowed statement is split before it is bound

**A statement whose subject survived its boundary under a new condition splits,
so `until` binds only the half that stopped to hold.**

A reader takes a range as the whole truth set of the sentence it sits on, which
is what `D-VER-001` chose it for. On a sentence with two claims, it expires
both, and the caller reads the half that survives as removed.

## Evidence

- `installation-setup` carried a statement at `until: 13`, beside a `since: 14`
  statement about `--distribution`. It read "--create-site <url> and
  TYPO3_SETUP_CREATE_SITE create the root page and its site configuration under
  config/sites/, and nothing suppresses them". A session that installed on
  14.3.6 read the pair as "--create-site is gone on v14". It recovered the true
  reading only from a clause in the middle of the neighbour paragraph
  (`feedback/2026-08-18-070632`).
- `.checkouts/13.4` reaches `getSiteSetup()` and `createSiteConfiguration()`
  with no distribution branch in `SetupCommand` at all. `.checkouts/14.3` and
  `.checkouts/main` guard the same call with `$distributions['active'] === []`
  and warn where it does not hold. 14 narrowed the option and did not remove it.
  The clause that expired is "nothing suppresses them".
- The cost runs both ways. A belief in its removal leaves the installation with
  what the same hint calls no way to learn its own URL. A belief that it is
  unconditional produces the doubled root page the operations checklist warns
  about.

## Decided

- The bound goes on the claim, so a compound statement splits before either half
  gets a bound.
- `installation-setup` takes that shape. What `--create-site` writes has no
  bound across the covered majors. The condition that arrived becomes a
  `since: 14` statement of its own, in front of `--distribution` rather than
  inside it. The second copy of the condition goes with it.
- Rejected: to keep the tag and repair the prose, which puts version words back
  into a sentence. `HintsTest` forbids that and `D-VER-001` is why.

## Assumed

- The reader takes the range as the statement's whole truth set rather than
  reads the neighbour statements against it. That is what the answer renders.
  `D-VER-001` chose a filter over a qualifier so a caller need not read around
  it.

## Wrong if

- A split leaves the unbound half general enough that a caller acts on it where
  the condition applies. That is the doubled root page, rather than the absent
  site configuration this one produced.
- Another `until`-bound statement turns up whose subject survived its boundary.
  `D-VER-001`'s read of 2026-08-02 checked every range for holes and could not
  see this. A compound statement bound whole has a contiguous range.

## Confirmed on 2026-08-18

The second **Wrong if** happened twice and the rule absorbed both. A session
read every `until`-bound statement against the four checkouts, and two carried a
claim that outlived the boundary. `css-color-surface-tokens`, whose second half
holds on all four branches, and `form-framework`, where the registration expires
at 14 and the deprecation is what arrives there. Both split. Four more looked
like the defect and are not. They survive as deprecated routes, and the
successor is where the answer sends a caller who reads them as removed anyway.
The first **Wrong if** shows up in a session that acts on a split, not in a
checkout.
