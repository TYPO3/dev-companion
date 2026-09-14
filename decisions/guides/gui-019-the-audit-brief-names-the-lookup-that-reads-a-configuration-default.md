---
id: D-GUI-019
title: 'The audit brief names the lookup that reads a configuration default'
date: 2026-08-26
status: open
readings:
  - 2026-09-01
---

# D-GUI-019 — The audit brief names the lookup that reads a configuration default

**The `audit` brief's tool list names `typo3_configuration_lookup`. A review
whose result turns on a configuration default otherwise answers from the file
the core ships.**

A core patch review established four configuration facts with a grep of
`DefaultConfiguration.php` and shipped one of them as a statement about an
administrator's installation.

## Evidence

- `feedback/2026-08-24-205113`, a review of Gerrit change 93079 in a core
  checkout. Four questions, all about resolved configuration.
  `LOG/writerConfiguration`, which caches `SYS/caching/cacheConfigurations` puts
  in the `pages` group, which `SYS/passwordPolicies` entries carry a `generator`
  key. And what `BE/passwordPolicy` and `FE/passwordPolicy` default to. A read
  of the shipped default file answered all four.
- The session did not reject the tool. Its account is that
  `typo3_configuration_lookup` was not in the results it pulled. It never went
  back to the tool list once it was inside the read.
- The feedback states what it cost the work product. "the global default writer
  for WARNING and above is FileWriter only" stood as settled about what an
  administrator would find. It is a statement about what ships. A regex over a
  text slice answered the third question and produced two false positives that
  were neighbour keys.
- The brief did not name the tool. `TaskGuide::answer()` ran here on 2026-08-26
  with the session's own shape: `changeType: "audit"`, the task text, and
  `typo3/sysext/backend/Classes/Form/FieldControl/PasswordGenerator.php`. It
  returned ten `nextTools`, of which the two from the `audit` intent were
  `typo3_project_describe` and `typo3_extension_describe`.
- The same tool already has a route for the neighbour shape. The `diagnosis`
  intent has carried "`typo3_configuration_lookup`, for what this installation
  has configured where the shipped default says otherwise" since `D-SKL-065`.
- **The enumeration half of the feedback is already answered.** The probe reads
  the path with `ArrayUtility::getValueByPath`, which returns whatever sits at
  the path, subtree included. Read in `.checkouts/13.4` on 2026-08-26, where a
  segment loop replaces the value with its child and returns it. So
  `LOG/writerConfiguration` answers with every writer and `SYS/passwordPolicies`
  with every policy and its keys, in one call each.
- The description already names the example the session needed.
  `ConfigurationLookup::description()` offers `SYS/caching/cacheConfigurations`
  as one of three subjects for it, which is the session's second question
  verbatim.
- `skills/base.md` names the tool, in the paragraph **What each runtime lookup
  adds after the extension answer**, which every published skill carries a copy
  of. Its frame is the `typo3_extension_describe` call of step 2, a call a core
  patch review does not make. It stands before the diff has raised a question.
  The session read it there and reports it as read too early to bind.
- The review checklist has no configuration surface.
  `skills/typo3-core-patch-review/references/checklist.md` lists ten, and the
  one the claim sat on is **Behaviour**: what the patch changes for code that
  calls it.

## Decided

- **Step 3, routing.** The tool exists, it answers all four questions as it
  stands, and nothing led to it from the moment the paths stood known.
- **The `audit` intent's tool list gains the line**, in
  `knowledge/task-intents.json`. It says what the tool adds over the shipped
  default and that a path names a subtree as readily as a leaf. Re-run after the
  change, the session's own call returns it.
- **Nothing gets built for the enumeration suggestion.** It asks for a
  capability the tool has, so the gap was the call rather than the answer. The
  second half of the new line is where a caller now reads it.
- **Rejected: a configuration surface in the review checklist.** That is
  `D-SKL-030`'s shape and it is the more expensive one. It obliges a call on
  every patch for a value most diffs do not turn on, which is that entry's own
  second **Wrong if**. A session reads the brief's list at the moment the paths
  stand known, and it costs nothing where no result needs it.
- **Rejected: a rewrite of the runtime-lookup paragraph in `skills/base.md`.**
  It goes into every published skill as a copy, so the frame it would gain lands
  in projects no release of this server corrects. The delivery it failed at is
  one the brief makes at the right moment instead.

## Assumed

- That a session reads the brief where the session says it would have. Its
  account is that the tool's name at the point the paths stand known would have
  caught it. Nobody has watched an audit session act on that list.
- That an audit runs where the tool can answer. A checkout with no installation
  gets `unsupported`. That is why the line serves the case a result turns on a
  default rather than a step of every review.
- That a configuration default is common enough in a review to be worth a line
  in every audit brief. One session measured it, on one patch.

## Wrong if

- A second audit session reads a default configuration file by hand with the
  tool named in the brief. Then the list was not the lever, and the
  **Behaviour** surface of the review checklist is what remains to try.
- An audit calls it on most patches and it answers nothing worth a result. Then
  the line bought a call per review for a value that rarely decides anything. It
  belongs to the change types that touch configured behaviour.
- A session in an audit calls it and reports `unsupported` because its checkout
  is not installed. Then the line belongs to the intent's project half rather
  than to the core reviews the feedback came from.
- A session asks for a path above a leaf and reports that it got one value back.
  Then the enumeration half was not answered and the tool owes the shape after
  all.
