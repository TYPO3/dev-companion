---
id: D-SKL-043
title: 'A rule query carries two subjects'
date: 2026-08-14
status: open
coveredBy:
  - SkillTest::aRuleQueryCarriesTwoSubjectsAndAThirdIsACallOfItsOwn
---

# D-SKL-043 — A rule query carries two subjects

**A skill that tells a caller how many subjects one rule query carries states a
count a measure of the corpus bears. That count is two.**

[`D-SKL-011`](skl-011-the-call-plan-a-skill-writes-down-is-measured.md)
replaced a count with *length is the limit rather than the count*. Its second
**Wrong if** described what would follow: a review that asks four obligations in
one query and reports what came back absent. That happened on 2026-08-13, and
nothing came back at all.

## Evidence

- `feedback/2026-08-13-214857` reports a review of Gerrit change 93319 that
  asked `typo3_rule_lookup` for `changelog entry testing review readiness` at
  `targetVersion "15.0"` and got `matchCount: 0`. It names the skill's bundle
  sentence as what it followed, and a read of
  `documentId="core/contribution/rules"` as what it recovered with.
- The miss reproduces whole, re-run on 2026-08-14 from this worktree through
  `Tool\RuleLookup::answer()` with the feedback's own arguments. First *No
  knowledge section matched "changelog entry testing review readiness"*. Then
  *No section carries more than 3 of the 5 words: "changelog entry review"
  reaches 3 sections, "changelog review readiness" reaches 3 sections — ask
  again with the one that narrows best*. Then the `documentation-changelog` hint
  and the topic list.
- Every part of it answers on its own. `testing` returns `## Testing` of the
  contribution rules at coverage 1.0 and score 107. `review readiness` returns
  `## Review Readiness` at 1.0 and 148. `changelog entry` returns
  `## Breaking Changes`, `## Changed Signatures` and `## Changelog Files` at
  1.0. Four of the five words still answer — `changelog entry review readiness`
  returns six sections, `## Review Readiness` first at 0.691. The fifth word is
  what empties it.
- The count is where the cliff is, measured over the 62 `##` headings the prose
  corpus carries, asked in the corpus's own words. All 1891 pairs: one empty. Of
  37,820 triples, 379 taken at a fixed stride: 102 empty, 27%. Of 557,845
  quadruples, 140 at the same stride: 80 empty, 57%.
- A shared document does not save a third subject, which is what the replaced
  sentence promised. Exhaustive within one document: 351 triples of its own
  headings return nothing 34 times and return all three 31 times. 500 quadruples
  return nothing 84 times and all four twice.
  `documentation testing review readiness` returns `## Testing` alone at
  coverage 0.532. Those are three headings of `core/contribution/rules`, the
  document the feedback's session went on to read whole.
- The pair holds where the sentence claimed it.
  `breaking change changelog entry`, the query `D-SKL-011` measured, still
  returns `## Breaking Changes`, `## Changed Signatures` and
  `## Changelog Files` at 1.0. The one pair of 1891 that empties is
  `removing the probe the pre-commit hook`.
- The tool already offers the one-call recovery.
  `Documents::largestReachingSubsets()` named two three-word subsets that return
  sections, which is
  [`D-ANS-016`](../answers/ans-016-a-miss-names-the-query-that-would-have-hit.md)'s
  computation on this corpus. The report names only the topic listing as what
  the miss did right, and nothing records whether the session read the subsets
  line.
- The tool's own schema asks for one. `RuleLookup::inputSchema()` describes
  `query` as *Topic to look up, in English, for example testing, review,
  deprecation, or code style* — singular, and four single-word examples. The
  skill is what told the caller otherwise.

## Decided

- **Step 4, wording, closed on the spot.** The rule arrived, the session
  followed it, and it was wrong about the corpus, the same diagnosis `D-SKL-011`
  made of the sentence it replaced. The skill's contract stands. Its
  `description` and the ownership boundary it closes on have not changed, and
  nothing here looked up anything about TYPO3.
- The bound stands as a count, because the count is what a caller can check
  before the call. *Length is the limit* named the mechanism accurately and
  asked the caller to estimate a coverage share against a corpus they cannot
  see.
- A shared document moves down from the rule to the reason a pair is worth one
  query, which is what the measurement supports.
- The exception list goes. *A genuinely different subject — testing, code style,
  the Gerrit workflow — is a call of its own* is what a bound of two already
  says. It was the half the session read as exhaustive.
- **The per-term fallback the feedback asks for is not built.** `D-ANS-037`'s
  **Since then** measured it over 490 queries. Admission of every section a
  query term reaches returns the nearest unrelated section to 87 queries that
  reach nothing today. 40 of the 41 scenario prompts are among them. The subsets
  in the miss are that fallback in the form the floor survives. The miss offers
  the union as a query to ask rather than returns it as an answer.
- **The tool description is not changed either.** The feedback asks it to state
  a conjunction, and there is none. The search keeps a section at half the
  query's weight rather than at all of it. That is why four words answer where
  five do not. A share in a description a client caches is the mistake this
  entry is about, one file further out.
- This entry revokes `D-SKL-011` rather than amends it. Its statement carries
  *obligations that share a document are one call* unbounded, which is what the
  sweep above disproves at three. A reader of the listing sees the headline and
  the status alone.

## Assumed

- That the stride sample stands for the population at three and four subjects.
  It walks the lexicographic order of the combinations rather than a random
  draw. So a run of headings that share a word enters the sample as its share of
  that order.
- That a caller writes what the sweep asked. Every query in it is the corpus's
  own heading text. A reviewer writes the subject in their own words, which
  reaches fewer sections rather than more.
- That a reviewer reads a count as a bound where they read *length is the limit*
  as permission. Nothing measures what the next recorded review run asks for.
- That no other file states a count for this tool. `one call` reaches four
  skills and none of them is about `typo3_rule_lookup`; `base.md` routes it by
  `documentId`. `knowledge/server-scope.json` still routes a review to
  *`typo3_rule_lookup` per obligation the diff raises*, which is inside the
  bound and left where `D-SKL-011` left it.

## Wrong if

- A review asks two subjects in one call and reports the second one absent. Then
  two is over the bound as well, and what holds is the measured pair rather than
  a count at all.
- A later feedback reports a three-subject query that answers whole and the
  split as the cost. Then the corpus grew into the combination, and a session
  measures the count again rather than raises it on one report.
- A review reads the bound as a ceiling on words and splits
  `breaking change changelog entry` back into two calls. Then *subject* carries
  more than it can and the sentence needs the pair spelled out.
- The next skill sentence that states a retrieval behaviour turns out stale the
  same way. `D-SKL-011`'s fourth **Wrong if** said this twice over. Two entries
  have now corrected one paragraph about the ranker. The lesson would be that a
  skill states the subject to ask in and no property of the search at all.
