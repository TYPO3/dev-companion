---
id: D-FBK-014
title: Every stage is a directory, and closing is none
date: 2026-08-02
status: confirmed
coveredBy:
  - TodoTest::aTodoThatNamesAQuestionIsParkedWhereNobodyIsOfferedIt
---

# D-FBK-014 — Every stage is a directory, and closing is none

**A todo is in `open/`, `progress/` or `waiting/`, and its close is a deletion
rather than a fourth place.**

Four states existed and three of them were directories. The queue was the one
addressed by the absence of one, which cost a special case in every place that
built a path.

## Evidence

- `todo/` on the day of the move: three queued files loose beside `readme.md`,
  and four directories. `progress/`, `waiting/` and `recurring/` held todos and
  `reference/` held two pages that are not work at all. `Todo::read()` addressed
  the queue as the empty string and `Todo::parse()` built the path from a
  ternary on the kind. A release of a claim wrote a bare `todo/` prefix. One
  state of four, spelled differently in three places.
- Nothing has ever stayed because it reached its end. `feedback/archive/` is the
  only directory here that holds closed items, and
  [`R-FBK-002`](../../requirements/feedback/fbk-002-a-feedback-that-was-worked-off-stays-answerable-for.md)
  says what it is for. `typo3_feedback_list` answers an agent somewhere else,
  and that agent cannot read a deletion out of a commit.

## Decided

- The queue moves into `todo/open/`, so that where a file sits says which stage
  it is in for every todo alike. The number stays what it was, the place in the
  order.
- Closing is not a stage. A finished todo goes and the commit that finished it
  is the record. A directory of them would be a second thing to keep true. This
  repository has already twice split one shared file for that reason.
- `feedback/archive/` is not the counter-example it looks like. What decides is
  not how valuable the closed card was but whether whoever asks about it can
  read a commit. This repository can, and the agent that recorded a feedback
  cannot.
- `recurring/` and `reference/` stay beside the stages rather than among them.
  One has a clock and never closes, the other is not work. Rejected in the work
  on this todo: `reference/` below `documentation/`. AGENTS.md puts the
  machine-specific half there deliberately, where it can go stale and take no
  scenario with it. The page that lists what stays out of the queue on purpose
  only works where the session that would rediscover it looks.

## Assumed

- That three stages are all a todo has. Nothing here reviews, approves or
  schedules, so the states are: nobody has it, somebody has it, nobody can start
  it.
- That a deletion loses nothing anybody will look for. It is what this
  repository has always done. No session has yet asked a question about a
  finished todo that `git log` could not answer.

## Wrong if

- Somebody writes a second list of finished work, because a question recurs that
  git cannot answer cheaply enough. Then the close is a stage after all and this
  entry is the reason it was not one.
- A fourth directory arrives beside the three and is neither a stage nor plainly
  beside them. That would mean the split into stages and non-stages does not
  hold the cases.
- `reference/` grows back into a backlog. It already holds one page with three
  unrelated items, which is the shape
  [`D-FBK-008`](fbk-008-one-todo-is-one-file-and-the-queue-is-in-the-names.md)
  split `todo.md` away from. A second such page would say the store has a hole
  that people fill there.

## Confirmed on 2026-08-22

Three stages and no fourth. `progress/` is absent because nothing is in hand,
and an empty stage is a directory git does not keep. That is the same shape
`D-FBK-013` settled for the queue, and it costs nothing. Nobody has written a
second list of finished work: git answered the questions this session asked of
the history each time. `reference/` shrank rather than grew, since two of its
three items are what `environment:create` now makes. That answers the third
**Wrong if** the other way round.

## Since then

There are two stages rather than three. `progress/` was the state a claim sat
in, and `D-DOC-060` reads it off the worktree instead. So a todo somebody has in
hand is a file in `open/` that has not moved. What this entry decided stands:
where a file sits still says which stage it is in, and the close is still a
deletion.

The second **Wrong if** watched for a fourth directory beside the three. What
arrived is the opposite, one of the three turned out to be answerable from git,
and the split into stages and non-stages held it.
