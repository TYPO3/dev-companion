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

**Read 2026-09-02:** the finding gate in `typo3-extension-health/references/checklist.md` still carries every clause this case stands against. The sink named and its code read, and a ViewHelper that hands its children on as path rather than end. The opt-out that prevents a double encode, and the unverified finding that names the unread class. Nothing guards whether a review follows the value, as before.

> Before we hand this extension over to a new maintainer, check whether any
> editor-supplied value reaches the frontend unescaped. Report what you find
> with evidence and severity. This is a review only; do not change files.

**What has to come out of it**

- The review follows the value to the thing that emits it. The answer names the
  tag, attribute, header or API that writes it in the end. It names the call
  that escapes it there or the absence of one.
- That reading crosses into the installed package below the vendor tree, because
  the class that emits the value is not one the extension ships.
- The review recognizes a ViewHelper that hands its rendered children to another
  component as one that emits nothing. So the opt-out inside it is on the path
  to a sink rather than at the end of one.
- The review reports an opt-out that exists to keep a value from a second encode
  as that. At most it is a maintainability note that names what it depends on,
  and not a violation.
- Where the review cannot follow the value that far, it reports the finding as
  unverified and says which class it did not read. It neither drops nor promotes
  it.
- Escaping stays an assessed surface either way: an opt-out whose sink does not
  escape is still a finding, and severity follows the demonstrated consequence.

**How it fails**

- The finding rests on the template line, the disabled escape on the core
  formatting ViewHelper and the plain field type in TCA. Three correct
  citations, none of them the sink.
- The only class the review opens is the one that confirms what it already
  believes. The extension's own ViewHelper and the class that emits the resolved
  value go unread.
- The opt-out gets an active security consequence and a place in the priority
  order while nothing in the answer says where the value comes out.
- The escape disappears from the review instead. The review reports nothing
  because it followed nothing, and the answer does not say the surface stayed
  unassessed.
- The review asserts the sink from memory, the core escapes this on its own,
  without a look at the installed class. That is the same unread claim with its
  sign flipped.
