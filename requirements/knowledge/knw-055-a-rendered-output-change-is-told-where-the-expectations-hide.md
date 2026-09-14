---
id: R-KNW-055
title: 'A rendered-output change is told where the expectations hide'
status: held
restsOn: [D-KNW-044]
heldBy:
  - HintsTest::aRenderedOutputChangeIsToldWhereTheExpectationsHide
  - HintsTest::theIterateNarrowlyNoteCarriesTheOneChangeItIsWrongFor
---

# R-KNW-055 — A rendered-output change is told where the expectations hide

**A caller who changes a rendered output learns where the core hides the
expectations that assert it. A narrowed run is the wrong move for this one
change.**

Which suite can fail is not the question a rendered-output change asks. The
change is one line. What it costs is the expectations elsewhere that quote the
old shape verbatim, a URI, a tag, an attribute. So `core-tests` says where those
sit and how to aim a search at them. The invocation notes carry the exception
beside the iterate-narrowly sentence, because that sentence is what sends this
caller round the loop. A narrowed run reports the blast radius one failed suite
at a time, at a full functional run per round.

Two halves of the statement do the work, and either alone leaves it wrong. A
search only in files named `*Test.php` misses about half of them. The
expectation is as often in a fixture the test loads, and a test that `require`s
its expected markup carries no literal at all. A search for the changed value
misses the rest. It sits escaped in a PCRE, split across a `sprintf()` format,
concatenated at runtime, substituted into a `{$...}` placeholder or soft-wrapped
mid-path by quoted-printable. The resource path around it survives every one of
those.

## From

`feedback/2026-08-02-145003`, a session that changed the shape of rendered
resource URIs across the core. It found about 141 expectations in 23 files one
failed suite at a time, over roughly fifteen full functional runs of several
minutes each. Five rewrite passes each still missed cases. The same session
reported the cost a second time, from the assessment end, in
`feedback/2026-08-02-145128` (2026-08-02).
