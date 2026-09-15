# What is being worked on

This directory exists so a session can end anywhere. It holds the **order of the
work** and where the last one stopped. What must be true is
[requirements/](../requirements/readme.md), the questions real sessions asked is
`feedback/`, and the map of what the audiences need is `scenarios/`. Those three
outlive the work. This one goes with it.

Nobody reads it to start. `bin/cli todo:next` prints the one todo that is due
and nothing else, and `bin/cli todo:list` is the overview. This is where both of
them read from.

One todo is one file, and where it sits says what it is. Two of the directories
are the stages one runs through, and the other two sit beside the stages rather
than among them. What a session has in hand is not a stage. It is a todo in the
queue with a worktree on the branch its name derives (`D-DOC-060`).

- **`open/`** — the queue, read by the priority in each head and, where two say
  the same, by the date in each name. Nothing here is a position. So a move of a
  todo is an edit of one line rather than a rename of a run of files. Two
  sessions that add work at once cannot pick the same place. A finished one
  goes, and a new one is a new file.
- **`waiting/`** — what no session can start, because it waits on an answer
  nothing here can produce. It says what it waits on, `bin/cli todo:next` offers
  it to nobody, and the answer is what moves it back into the queue. A recurring
  todo asks every seven days what is in here, so a question waits rather than
  disappears. `bin/cli todo:park` is what moves one here, off the question it
  names.

The queue is an order, not an assignment, so `bin/cli todo:next` would hand the
same first item to everybody who asks. What keeps two sessions off one todo is
the worktree. `bin/cli todo:claim` cuts one per todo it takes, on the branch
that todo's name derives. Every command that hands out work passes over a todo
one stands on. Nothing moves and nothing gets written, so a todo whose worktree
came down is workable again with nothing to put back. `bin/cli todo:home` takes
the worktree down once the branch has merged.

There is no fourth stage for finished work. A finished todo goes, and the commit
that finished it is the record. A directory of them would be a second thing to
keep true. The one exception is not here at all. A worked-off feedback stays in
`feedback/archive/`, because the agent that asks what became of its report has
no git to read the deletion out of.

- **`recurring/`** — what comes round and is never deleted, so it has no closing
  to run towards.
- **`reference/`** — not work at all: what a session would otherwise rediscover
  and mistake for some.

A todo in a stage carries its id as its name, `T-<yymmdd>-<hash>`, and that is
the whole of the file name. The day is what a listing sorts by and the digest is
what two writers in one second cannot both produce. A card's digest derives from
the feedback it serves, so a reader finds the pair from either end
(`D-DOC-061`). What the todo is about is the title inside it, which every
listing prints.

Each file opens with front matter, then its title. It is the front matter a
requirement and a decision carry, and the same `TYPO3\DevCompanion\Upkeep\Entry`
reads it (`D-DOC-062`):

```markdown
---
serves: [todo/]
priority: normal
---

# What the step is about
```

Six keys, and a key that is none of them is what `bin/cli todo:check` reports:

- `serves: [<ids>]` — what this answers for: a requirement, a decision, a
  feedback, a scenario, a directory. Without it, it is an idea rather than a
  todo, and ideas go in the feedback that had them. A decision stands by its id
  where the step is that entry's **Wrong if** gone back to. It stands as
  `decisions/` where it is the pile under sort.
- `priority: high`, `normal` or `low` — where it stands among the rest, and the
  whole list. Every todo in a stage carries one and a recurring todo carries
  none, because a cadence is what orders an appointment. A todo written for a
  feedback starts at `low`. A raise is the judgement the card asks for. A judged
  card that is still `low` says so because somebody decided it, not because
  nobody has looked. What several sessions reported is not `low`.
- `every: session` or `every: 7 days` — the cadence of a recurring todo. A
  cadence in days is an appointment and comes before the queue; `session` is a
  sighting and comes after it, when the queue is empty.
- `checked: <date>` — when a todo measured in days last ran. The session that
  runs it writes the date.
- `run: [<command>]` — where the step starts. `bin/cli todo:next` runs the ones
  this repository owns and names the rest.
- `waitingOn: >` — what a todo waits on, in the words of the question, on the
  indented lines under it. The folded form takes a question of any length and
  needs no quoting, which a question carrying a colon does.
  `bin/cli todo:waiting` is what asks it again, and `bin/cli todo:park` is what
  moves a todo carrying one out of the queue. Written mid-work it stays in
  `open/` until the branch comes home, where the branch still holds the finished
  half.

Then one paragraph, and one only: the **next concrete step**, in enough detail
that someone who has read nothing else can start. "Continue with the bindings"
is not that; "bind the statements in `php.json` against `.checkouts/12.4` and
`13.4`" is. Two steps are two todos. A paragraph prints whole, and a session
that reads three of them to find where to start reads instead of works.

That is the form. What is due and in which order:
[documentation/records/readme.rst](../documentation/records/readme.rst). What a
finished, a half done or a put back todo leaves here:
[documentation/records/working-a-todo.rst](../documentation/records/working-a-todo.rst).
