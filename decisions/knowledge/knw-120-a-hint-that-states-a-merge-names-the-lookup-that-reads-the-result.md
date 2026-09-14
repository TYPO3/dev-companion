---
id: D-KNW-120
title: A hint that states a merge names the lookup that reads the result
date: 2026-08-25
status: open
readings:
  - 2026-09-01
coveredBy: []
---

# D-KNW-120 — A hint that states a merge names the lookup that reads the result

**A hint that says more than one file assembles a configuration value names
`typo3_configuration_lookup` and the path. Those files are what a reader
otherwise opens.**

`project-configuration-files` diagnosed a session at an HTTP 500 in one step.
The resolved `SYS/trustedHostsPattern` was its open question, and it read
`additional.php` and curled the page for it.

## Evidence

- `feedback/2026-08-24-140421`, an extension carried to v13.4 and v14 in
  `/home/benji/projects/ext-usercentrics`. It reports five tools it loaded or
  got a pointer to and never called. `typo3_configuration_lookup` is the one
  where it names the moment. *The right moment was a frontend answering HTTP 500
  with an empty log, where the question was whether SYS/trustedHostsPattern was
  set. I read the generated additional.php and curled the page instead.*
- The same session's strength report,
  [`feedback/archive/2026-08-24-140340`](../../feedback/archive/2026-08-24-140340-the-hint-ids-carried-this-session-and-four-of.md),
  says which hint it held. `project-configuration-files`, fetched by id,
  predicted the failure before it happened and *I knew what it was in one step
  instead of hunting*. So the corpus reached this session, and what it reached
  it with is the hint this entry changes.
- That hint states the merge and names only the two files. Read on 2026-08-25:
  `settings.php` is what the setup tools write, `additional.php` is *optional
  PHP loaded afterwards*, and DDEV's copy is *merged over
  `$GLOBALS['TYPO3_CONF_VARS']` with `array_replace_recursive`*. A reader who
  has just learned that layers assemble the value learns nothing about how to
  read the layered result.
- The sentence exists, in one place, and it is a page rather than a hint.
  `knowledge/documents/project/installation/booting-a-clone.md` says *Read what
  the installation actually resolved with `typo3_configuration_lookup` rather
  than off the files, because a generated `additional.php` is merged over what
  the site configuration and the install wrote*. That page is the one the same
  debrief reports as named twice and never opened, `feedback/2026-08-24-140239`.
- Two hints already do this and nothing declares it as a rule. `tca.json`:
  *which groups and providers your installation really has is what
  typo3_configuration_lookup with path SYS/formEngine/formDataGroup answers*.
  `fal.json`: *The list is the installation's rather than the core's, because
  every extension may add to it.* Both are the same shape. A value more than one
  contributor assembles, and the call that reads the assembly.
- `installation-boot` and `installation-exception-output` carry the
  trusted-hosts mechanism too, and neither is the placement. The first states
  the host check inside the boot sequence. The second states which exceptions go
  to the log and which only show, which is the diagnosis rather than the value.
  `project-configuration-files` is the one that owns the two files.

## Decided

- **Step 2, delivery, and closed on the spot.** The statement already stands
  verified in `booting-a-clone`, nothing about TYPO3 needs a lookup, and no
  contract moves. Those are the three conditions
  [judging.rst](../../documentation/records/judging.rst) puts on a change in the
  judging run, and
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md)
  is why holding the evidence is what decides it.
- **One placement, not three.** `installation-boot` and
  `installation-exception-output` state the same mechanism and keep their text.
  Two forms of one rule is what
  [`D-ANS-009`](../answers/ans-009-a-shipped-file-deprecation-is-found-by-the-tool-that-lists-the-file.md)
  names as the thing most likely to go wrong here. The reader who needs the call
  is the one who learns about the files.
- **Not the placement the feedback asks for.** It proposes
  `installation-exception-output`, on the symptom. The session's own strength
  report says `project-configuration-files` answered it.
  [`D-KNW-092`](knw-092-what-an-unanswering-installation-is-diagnosed-from-is-a-subject-this-server-owns.md)
  built the symptom hint to say what a failure writes down rather than what a
  value resolves to.
- **The document keeps the procedure.**
  [`D-KNW-095`](knw-095-the-installation-procedure-is-a-document-and-the-hints-keep-the-facts.md)
  splits the ordered run into the page and leaves the facts in the hints. Where
  a caller reads the value from is a fact about the value, not a step of the
  boot. So it belongs on the hint side of that split and stays on the page as
  well.
- **The call answers on the installation this sends a caller to.** A frontend
  that refuses the host header is a middleware at work.
  `Configuration/RequestMiddlewares.php` registers `VerifyHostHeader`, read on
  `.checkouts/13.4`. `Typo3Runtime` boots the installation in a subprocess and
  asks its container, so no request stack runs and nothing checks a host header.
  The one case where the value is most worth reading is not the case where the
  lookup is unavailable.
- **`coveredBy: []`.** What would show this wrong is a session that opens the
  files anyway, and no assertion over the corpus sees that. A test that holds
  one hint's text to one tool name would pass on the day of its write and say
  nothing about the reader.

## Assumed

- That a caller told the call exists makes it. The session reports the opposite
  for four other tools in the same feedback. This is the one where the sentence
  was not in front of it.
- That the resolved value is what the caller wants at that moment. The session
  says so. Its question was whether the pattern had a value, not which file set
  it.

## Wrong if

- A session with this hint reads `additional.php` anyway. Then a call named in a
  hint is not the lever. What remains is
  [`D-GUI-012`](../guides/gui-012-the-brief-names-the-guide-the-recognized-work-belongs-to.md)'s
  open question about an answer handed over rather than named.
- The added sentence pulls `project-configuration-files` onto queries about a
  configuration value that has nothing to do with the two files. Its `appliesTo`
  stays as it was, so that would be the body's terms rather than its patterns.
- A hint elsewhere states a merge and the call is wrong for it. A value the
  installation resolves per request, or one no path reaches. Then this is a
  sentence about two files rather than a rule about merges.
