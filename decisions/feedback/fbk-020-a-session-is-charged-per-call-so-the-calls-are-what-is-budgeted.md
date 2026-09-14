---
id: D-FBK-020
title: A session is charged per call, so the calls are what is budgeted
date: 2026-08-02
status: confirmed
coveredBy:
  - CliTest::theTodoItHandsOverNamesTheFileItIs
---

# D-FBK-020 — A session is charged per call, so the calls are what is budgeted

**What a session costs is one context per tool call rather than one per token
read. So what it hears is about the number of calls.**

The rules a session gets were about what it reads. The 82 worktree sessions of
2026-08-02 are the first run with a measure of their cost, and it sits somewhere
else.

## Evidence

- 82 sessions, 5414 tool calls, 718 million cached input tokens read back and
  5.9 million written out. The context a call reads again was 124k at its peak
  and 82k on average. So a call costs about as much as the session so far.
- ~~Not one of the 5414 went out beside another: every turn carried exactly
  one.~~ Wrong, and the visits below say why. The shape of the count cannot hold
  two calls, so it reads as one per turn whatever the sessions did. Grouped on
  the message, 2020 of them went out beside another.
- 4046 were `bash`, and 2092 of those were `cat`, `sed`, `grep` and `ls`. The
  sessions reached for the file tool 624 times, the search tool 15 times across
  all 82, and the glob tool never.
- 40 of the 66 calls a session made came before its first change. 546 were `ls`
  against `todo/`, `decisions/` and `requirements/`, 207 of them against `todo/`
  alone. That is the file the command that hands the todo over had just read.
- 401 calls were `sed -n` windows into a file the session opened again
  afterwards. One session opened `src/Installation/Extension.php` sixteen times,
  another `src/Tool/ExtensionScope.php` nine.
- Failures were not the cost: 77 errors in 82 sessions, 1.4% of the calls.
- Every one of the 82 opened `documentation/records/working-a-todo.rst`; 13
  opened `AGENTS.md`.

## Decided

- What a session hears is about calls. Send what depends on nothing together.
  Reach for a file with the client's own file and search tools, and open it once
  rather than in windows. It is in `AGENTS.md` as the rule and in the message a
  parallel session starts with. That message arrives before the reads the rule
  is about.
- `bin/cli todo:next` names the file the todo is, in the line under the title.
  The command has just read it, and a session without it searches a directory
  that also holds everybody else's claims.
- Rejected: the pages every session opens inline in the handover. The six of
  them are 103 KB against roughly five calls saved, and unlike a call every call
  that follows pays for them again.
- Rejected: a session told to read less. What the sessions read is what made the
  answers right. 40 orientation calls is the shape of a step read against the
  checkout rather than waste.

## Assumed

- ~~The client is free to batch and did not. A launch that forbids it would make
  the first rule unreachable, and nothing in the transcripts tells the two
  apart.~~ Settled against itself on 2026-08-02. It was free and it did, on 37%
  of its calls, before it heard anything.
- Reading the same in fewer calls does not read it worse. The rule replaces
  three windows into a file with one open of it, not the file with a memory of
  it.

## Wrong if

- The next run of ten measures the same calls per session, or fewer calls at the
  same cached tokens. Then the cost is not where this puts it.
- A session batches and acts on a stale read — an edit composed against a file
  another call in the same message had already changed.
- Sessions no longer reach for `.checkouts/` and the manuals at the same rate.
  The orientation calls were not the waste; the second read of one file was.

## Confirmed on 2026-08-02

The run of ten is in and none of the three **Wrong if** fired. Calls per session
fell from 66.6 to 56.1, and the cached tokens fell with them rather than stayed
put.

**One number in the Evidence is wrong, and it is the second bullet.** A
transcript may write one assistant message as several lines, each with the same
id and usage again. So a count per line can never see two calls in one message.
Grouped on the message, 2020 of the 5465 baseline calls went out beside another.
The client was free to batch and did, before it heard anything. So what the rule
moved is the batch share from 37% to 58%.

What the run rules out is the case the **Wrong if** named, fewer calls bought at
the same token cost. That is the whole of what it settles: the ten todos are not
the 82, and n is 9.

## Confirmed on 2026-08-14

Measured a second time because a feedback reported that eight sessions batched
nothing at all. They batched: the dataset reproduces exactly, and grouped on the
message 218 of the 369 calls went out beside another. The miscount is the one
corrected above, reached again by somebody who had not read that far down. In
this client every line carries exactly one call, so "one call per turn" is an
identity rather than a result. A third session filed the same account an hour
later.

None of the three **Wrong if** fired. What the feedback reports and this
confirms is the second bullet: the rule moved the cheap-`bash` share once and
has not moved it since. Nothing holds any of it and nothing here can, since a
call pattern lives in transcripts outside this checkout.

## Since then

The first measure from the **caller's** side is in, and it profiles a session
that uses the server rather than works here. The statement holds where it
stands. The server's 17% of the calls is about 17% of the cost. So what this
server costs its caller tracks how often the caller calls it rather than how
much any one answer said. The profile also separates calls from requests, which
is the distinction three sessions above collapsed into one.

Two things bound it and neither moves it. A payload in a caller's session pays
back over more and larger requests than one here. Half of everything this server
cost that session was one array printed again on answers it had asked by id. The
largest single item is not a payload at all. Nine debug cycles cost more than
all the server calls together, every one a question this server did not answer.
That is `D-FBK-027`'s premise measured from the outside.

## Since then

A second caller-side measure covers what the *absence* of an answer costs. A
session spent about thirty round trips, every one of them `bash` and none
against this server. 43 percent of it went on a single subject nothing here
covered. The number stays for the reason the one above does, with one addition.
The change it measures has since landed, and all three subjects are in the
corpus now. So the same task run again would measure not whether the answers are
there but whether the session calls for them at all. That is the zero this
session also reported.

**A cost like this one is not visible from inside this repository.** A caller's
dig in somebody else's checkout leaves nothing behind unless a debrief counts
it.

## Since then

The four visits above got their judgement on 2026-08-22 against `D-DOC-041`, and
none of them collapses. Each is a measure of a different run. The ten worktree
sessions after the rule, the eight of 2026-08-13, and the two caller sessions
that counted themselves. A number belongs to the day and the dataset it came
from. What is not measured per run is nothing.

What the visits overtook stands struck above. The evidence bullet with one call
per turn, which three sessions have now reached on their own in the same wrong
shape. And the assumption that the client did not batch.
