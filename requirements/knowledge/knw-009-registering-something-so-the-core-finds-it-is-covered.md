---
id: R-KNW-009
title: 'Registering something so the core finds it is covered'
status: held
heldBy:
  - HintsTest::registeringSomethingSoTheCoreFindsItIsCovered
---

# R-KNW-009 — Registering something so the core finds it is covered

**"How do I register this so the core finds it" is a covered question.**

A content type registration and a class the container resolves by name both fail
at request time. Neither is a convention of a subsystem or a piece of backend
markup, the two places the session looked for an answer.

## From

A content element registered with a call signature from the previous major. A
page title provider that was not public and therefore never found (2026-07-29).
