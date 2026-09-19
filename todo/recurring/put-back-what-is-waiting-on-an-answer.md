---
serves: [todo/]
every: 7 days
checked: 2026-09-19
run: [bin/cli todo:waiting]
---

# Put back what is waiting on an answer

Take each question the listing prints and ask it. Ask the person who maintains
this repository, where that is who can answer it. Ask the world where the todo
waits on something outside this checkout. What comes back decides one of three
things in this commit. The todo goes back into the queue at the place the answer
earns it. It goes because the answer made the work unnecessary. Or it stays with
its question rewritten in the words of this ask. No session gets a todo in
`waiting/`, so this is the only thing that brings one back. The date below is
what keeps a question from a second ask in one afternoon.
