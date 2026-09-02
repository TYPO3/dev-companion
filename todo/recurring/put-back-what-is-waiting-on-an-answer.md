---
serves: [todo/]
every: 7 days
checked: 2026-09-02
run: [bin/cli todo:waiting]
---

# Put back what is waiting on an answer

Take each question the listing prints and ask it — of the person who maintains
this repository, where that is who can answer it, and of the world where the
todo waits on something outside this checkout. What comes back decides one of
three things in this commit: the todo is numbered back into the queue at the
place the answer earns it, it is deleted because the answer made the work
unnecessary, or it stays with its question rewritten in the words it was asked
in this time. A todo in `waiting/` is offered to no session, so this is the only
thing that brings one back, and the date below is what keeps it from being asked
twice in an afternoon.
