---
id: D-GUI-019
title: 'The audit brief names the lookup that reads a configuration default'
date: 2026-08-26
status: open
readings:
  - 2026-09-01
---

# D-GUI-019 — The audit brief names the lookup that reads a configuration default

**The `audit` brief's tool list names `typo3_configuration_lookup`, because a
review whose finding turns on a configuration default is otherwise answered from
the file the core ships.**

A core patch review established four configuration facts by grepping
`DefaultConfiguration.php` and shipped one of them as a statement about an
administrator's installation.

## Evidence

- `feedback/2026-08-24-205113`, a review of Gerrit change 93079 in a core
  checkout. Four questions, all about resolved configuration:
  `LOG/writerConfiguration`, which caches `SYS/caching/cacheConfigurations` puts
  in the `pages` group, which `SYS/passwordPolicies` entries carry a `generator`
  key, and what `BE/passwordPolicy` and `FE/passwordPolicy` default to. All four
  were answered by reading the shipped default file.
- The session did not reject the tool. Its account is that
  `typo3_configuration_lookup` was not in the results it pulled and that it
  never went back to the tool list once it was inside the reading.
- What it cost the work product is stated in the feedback: "the global default
  writer for WARNING and above is FileWriter only" was presented as settling
  what an administrator would find, and it is a statement about what ships. The
  third question was answered with a regex over a text slice and produced two
  false positives that were neighbouring keys.
- The brief did not name the tool. `TaskGuide::answer()` was run here on
  2026-08-26 with the session's own shape — `changeType: "audit"`, the task
  text, and
  `typo3/sysext/backend/Classes/Form/FieldControl/PasswordGenerator.php` — and
  returned ten `nextTools`, of which the two from the `audit` intent were
  `typo3_project_describe` and `typo3_extension_describe`.
- The same tool is already routed for the neighbouring shape. The `diagnosis`
  intent has carried "`typo3_configuration_lookup`, for what this installation
  has configured where the shipped default says otherwise" since `D-SKL-065` was
  built.
- **The enumeration half of the feedback is already answered.** The probe reads
  the path with `ArrayUtility::getValueByPath`, which returns whatever sits at
  the path, subtree included — read in `.checkouts/13.4` on 2026-08-26, where a
  segment loop replaces the value with its child and returns it. So
  `LOG/writerConfiguration` answers with every writer and `SYS/passwordPolicies`
  with every policy and its keys, in one call each.
- The description already names the example the session needed.
  `ConfigurationLookup::description()` offers `SYS/caching/cacheConfigurations`
  as one of three subjects to use it for, which is the session's second question
  verbatim.
- `skills/base.md` names the tool, in the paragraph **What each runtime lookup
  adds after the extension answer**, which every published skill carries a copy
  of. It is framed against the `typo3_extension_describe` call of step 2 — a
  call a core patch review does not make — and it stands before the diff has
  raised a question. The session read it there and reports it as read too early
  to bind.
- The review checklist has no configuration surface.
  `skills/typo3-core-patch-review/references/checklist.md` lists ten, and the
  one the claim sat on is **Behaviour**: what the patch changes for code that
  calls it.

## Decided

- **Step 3, routing.** The tool exists, it answers all four questions as it
  stands, and nothing led to it from the moment the paths were known.
- **The `audit` intent's tool list gains the line**, in
  `knowledge/task-intents.json`, saying what it adds over the shipped default
  and that a path names a subtree as readily as a leaf. Re-run after the change,
  the session's own call returns it.
- **Nothing is built for the enumeration suggestion.** It asks for a capability
  the tool has, so what was missing was the call rather than the answer, and the
  second half of the added line is where a caller now reads it.
- **Rejected: a configuration surface in the review checklist.** That is
  `D-SKL-030`'s shape and it is the more expensive one — it obliges a call on
  every patch for a value most diffs do not turn on, which is that entry's own
  second **Wrong if**. The brief's list is read at the moment the paths are
  known and costs nothing where no finding needs it.
- **Rejected: rewording the runtime-lookup paragraph in `skills/base.md`.** It
  is copied into every published skill, so the framing it would gain lands in
  projects no release of this server corrects, and the delivery it failed at is
  one the brief makes at the right moment instead.

## Assumed

- That the brief is read where the session says it would have been. Its account
  is that naming the tool at the point the paths are known would have caught it,
  and nobody has watched an audit session act on that list.
- That an audit is run where the tool can answer. A checkout with no installed
  installation gets `unsupported`, which is why the line is written for the case
  a finding turns on a default rather than as a step of every review.
- That a configuration default is common enough in a review to be worth a line
  in every audit brief. One session measured it, on one patch.

## Wrong if

- A second audit session reads a default configuration file by hand with the
  tool named in the brief. Then the list was not the lever, and the
  **Behaviour** surface of the review checklist is what is left to try.
- An audit calls it on most patches and it answers nothing worth a finding. Then
  the line bought a call per review for a value that rarely decides anything,
  and it belongs to the change types that touch configured behaviour.
- A session in an audit calls it and reports `unsupported` because its checkout
  is not installed. Then the line belongs to the intent's project half rather
  than to the core reviews the feedback came from.
- A session asks for a path above a leaf and reports that it got one value back.
  Then the enumeration half was not answered and the tool owes the shape after
  all.
