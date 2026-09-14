---
id: D-KNW-062
title: 'What a hint pays with is the mechanism and the file'
date: 2026-08-04
status: open
---

# D-KNW-062 — What a hint pays with is the mechanism and the file

**A hint pays with the sentence naming the mechanism and the class it lives in;
the advice around it produced none of one session's findings.**

A "fix this project" run recorded what paid rather than what failed, and the
four hints it names all paid the same way.

## Evidence

- `feedback/2026-08-04-180035`. `environment-variables` produced the High
  finding: a readme documenting four variables and no `getenv()` line behind
  them. `content-element-preview` produced three, because it says
  `GridColumnItem` renders the preview header before it dispatches the event the
  Fluid renderer answers. The session confirmed it at `GridColumnItem.php:81`
  and could not have found it in the templates. `language-files` produced two,
  because `target-language` is optional in XLIFF 1.2 and
  `XliffLoader::parseXliff1()` decides on that attribute alone. So a translation
  without it is schema-valid and the loader discards it in silence.
  `project-configuration-files` produced two and later diagnosed a regression
  the session had introduced itself.
- Three of those four are `R-KNW-005` statements: where a mechanism fails
  silently, the hint names the failure. The fourth is the same shape with an
  owner instead of a failure.
- The corpus prevented findings in the other direction too, which is worth the
  same. `sitepackage-templates` and the package's own `settings.yaml` explained
  that `<f:layout name="Default"/>` in a content element resolves to
  fluid_styled_content's frame on purpose. The session had queued it as a bug.
  `typo3_icon_lookup` settled six identifiers in six calls, all exact, so the
  session filed nothing about them.
- The `language-files` statement came out of an earlier feedback, closed as
  "[TASK] Say what a missing target-language costs a translation file". It paid
  twice in this one session. That is the channel's own measure —
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
  — read from the other end.
- `feedback/2026-09-03-235605`, a seeding run five weeks later, is the same
  measurement on `datahandler-seeding`: `Bootstrap::init($classLoader)` with the
  failsafe flag and the constructor error it produces, the three backend-user
  calls in order, and `pages.hidden` defaulting to 1 in TCA against 0 in the
  schema. The session set `hidden=0` because of the third. It says the page
  answered as the site's 404 target on the first run instead of as a second
  not-found. All three are a mechanism and the failure it produces.
- Two answer shapes carried the same weight. `typo3_changelog_lookup` returns
  the full tag list for the version and type. That let the session write an
  eight-call deprecation sweep against real tags before it opened a file. Three
  system extensions absent from that list answered "no v14 deprecations there"
  by absence rather than by an empty search nobody could trust.
  `typo3_extension_describe`'s artifacts block reported `sourceLanguage: "de"`
  on four XLF files as a finding straight out of the answer. A second run after
  the work is what verified two items closed.

## Decided

- Nothing changes. The feedback asks for none, and a strength is evidence about
  a boundary rather than a decision to confirm
  ([`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)).
- This entry writes the boundary down because the next editor of these hints
  cannot see it. What carried each finding is the causal sentence, not the
  advice around it. A hint cut down to its advice would have produced none of
  the six.
- The full tag list stays in every changelog answer for the same reason. It is
  what makes a bounded sweep possible, and absence is only an answer where the
  axis is complete.

## Assumed

- That the session would not have reached the four findings from the checkout.
  The session says so for three of them and names the file it confirmed the
  fourth in; nothing here re-ran the audit.

## Wrong if

- A session reports that it missed a mechanism a hint states in exactly this
  form. Then the sentence is not what carries it, and delivery is where the
  corpus loses its findings.
- Somebody cuts the hints down and findings of this kind go on. Then the advice
  carried them after all.

## Since then

All four hints still carry the sentence that paid. The second **Wrong if**
cannot fire yet, because its premise has not happened. No commit against the
hints since names a cut, and the corpus went the other way, with a mean body
above the matcher's reference. Nothing reports the first either. The feedback
that names any of those mechanisms is the session this entry came from and its
neighbours of the same days.

The counter-case arrived with the seeding evidence above and is the same
boundary from outside. `fal-writing` said a file attachment is a relation
DataHandler writes and named no field, so the session stopped and left two
images in a rich text field. What it needed was `uid_local` and where the
`sys_file` uid comes from, which `datahandler-relations` now states.
