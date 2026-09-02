# SKILL-09 — Follow the escaped value to the thing that emits it

**Environment:** `E-EXT`, in an extension whose detail template decodes HTML
entities around an editor-supplied title inside a ViewHelper of its own ·
**Contract:** `held` — `R-SKL-004`
**Held by:** `SkillTest::aSecurityFindingIsNotEstablishedUntilItsSinkIs`,
`HintsTest::bothSidesOfAnInjectionQuestionReachTheSinkMethod`,
`SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem`; the first reads
back the sentence the checklist writes and the second that both an escaping and
an injection question reach the method behind it, but that a review actually
follows the value instead of stopping at the opt-out is **not guarded** — a
sentence and a lookup are the kind of hold a reorganization satisfies while the
behavior goes. This case is what measures the rest.

**Read 2026-09-02:** the finding gate in
`typo3-extension-health/references/checklist.md` still carries every clause this
case is written against — the sink named and its code read, a ViewHelper handing
its children on as path rather than end, the opt-out that prevents double
encoding, the unverified finding naming the unread class. Whether a review
follows the value is unguarded as before.

> Before we hand this extension over to a new maintainer, check whether any
> editor-supplied value reaches the frontend unescaped. Report what you find
> with evidence and severity. This is a review only; do not change files.

**What has to come out of it**

- The value is followed to the thing that emits it: the answer names the tag,
  attribute, header or API it is finally written through, and the call that
  escapes it there or the absence of one.
- That reading crosses into the installed package below the vendor tree, because
  the class that emits the value is not one the extension ships.
- A ViewHelper that hands its rendered children to another component is
  recognized as emitting nothing, so the opt-out inside it is on the path to a
  sink rather than at the end of one.
- An opt-out that exists to keep a value from being encoded twice is reported as
  that — at most a maintainability note naming what it depends on — instead of
  as a violation.
- Where the value cannot be followed that far, the finding is reported as
  unverified and says which class was not read, rather than being dropped or
  promoted.
- Escaping stays an assessed surface either way: an opt-out whose sink does not
  escape is still a finding, and severity follows the demonstrated consequence.

**How it fails**

- The finding is established from the template line, the disabled escaping on
  the core formatting ViewHelper and the plain field type in TCA — three correct
  citations, none of them the sink.
- The only class opened is the one that confirms what the review already
  believes, while the extension's own ViewHelper and the class that emits the
  resolved value go unread.
- The opt-out is given an active security consequence and a place in the
  priority order while nothing in the answer says where the value is emitted.
- Escaping disappears from the review instead: nothing is reported because
  nothing was followed, and the answer does not say the surface was left
  unassessed.
- The sink is asserted from memory — the core escapes this on its own — without
  opening the installed class, which is the same unread claim with its sign
  flipped.
