---
date: 2026-10-31T14:30:00+00:00
category: idea
status: closed
closed: 2026-11-06
model: claude-opus-5-5
tool: typo3_project_describe, typo3_task_guide
directory: /home/lina/trials/site12-with, /home/lina/trials/site12-without
---

# a session that asks the server builds before it asks the user

## Observation

Two runs of `claude-opus-5-5` built a second site into one installation, from
one commit, with this server in one of them and nothing else different. The
brief contradicted the installation on purpose: it called the first site's brand
blue, and that brand is `#06603f`.

The run without this server stopped before it built anything. It put the
contradiction to the user with three readings to choose from, and waited for the
answer.

The run with this server built the whole second site. Then it reported the
contradiction in its closing message and asked at the end:

> **One thing doesn't match your brief:** the council's brand in this repo isn't
> blue. [...] Should the library use a clearly different green, or is the
> council meant to become blue? Either way it's a three-line change in
> `library.css`.

Both sessions saw it. One handed over the choice, and the other handed over a
result and a correction.

## What the maintainer saw over the other runs

The maintainer reports the same shape across every run of this comparison. The
session with this server directs more of its questions at the server, and fewer
at the person, where more than one answer is right. This feedback is one pair,
and that reading is what makes it worth recording rather than filing as noise.

## What the corpus already says

`D-KNW-183` says a hint states the options of a decision a new site makes, and
that one checklist item of the `sitepackage` intent tells the session to put the
decision to the user. Five hints carry that. Its **Assumed** is the open half:
*that a session follows a checklist item that tells it to ask*.

This run weakens that assumption without firing the **Wrong if**, which names a
greenfield case and those five decisions. The task here is neither. So the
statement holds where it was written and reaches nothing here.

## Why the route misses

The brand colour of a second site is not one of the five. Nothing routes it, and
the `instructions` this server sends at initialize name which tool answers what.
No sentence in them says what a session owes the person. So a session reads an
instruction to call `typo3_project_describe` first, calls it, finds the green,
and treats the contradiction as a fact it now owns.

The order is what costs the user. A decision settled before the work is a
question. The same decision after the work is a correction with a sunk cost
beside it, and the run quotes the cost itself: three lines in `library.css`.

## What this measures

The run with this server made three calls to it. The run without it made none
and read the files instead. So the server did not crowd the person out by
volume. What it offered was an authority to settle a question with, where the
other run had only the person.

## Suggestion

Judge whether this belongs in the `instructions` rather than in a sixth hint. A
hint reaches a session that looked up its subject. The sentence this run needed
holds for every task: where the project contradicts the brief, say so before you
build on either reading, and where two answers are both right, name them and
ask.

Weigh that against the cost of a general instruction that a session reads on
every task and applies to none. `D-KNW-183` chose five hints over one sentence
for that reason, and this run is the first evidence on the other side.

`SITE-13` carries both runs.
