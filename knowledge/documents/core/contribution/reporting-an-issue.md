---
description: >-
  What a new issue on the TYPO3 Core project carries: how you establish that nobody has filed it yet, the trackers it goes under, the fields of a Bug and the one it does not go without, where the area comes from, who sets the target version, and the Textile the description renders as.
whenToUse: >-
  When you write the title and the description of a core bug report, or fill in the new-issue form on forge.typo3.org — the report a patch's Resolves: trailer points at included.
hints: []
---

# Filing a TYPO3 Core Bug Report

Source: https://forge.typo3.org/projects/typo3cms-core

You file a core issue on the Redmine at forge.typo3.org, in the TYPO3 Core
project. You need an account, and the form is behind the login. So this page
reads what a report carries off the issues people filed rather than off the
form. `https://forge.typo3.org/issues/<number>.json` answers one whole.
`https://forge.typo3.org/projects/typo3cms-core.json?include=trackers,issue_categories`
answers the project's own lists.

## Whether Somebody Has Already Reported It

Two calls, and the first of them does not settle it on its own.

```
typo3_forge_lookup with query="<two or three words of the symptom>"
typo3_forge_lookup with open="newest", createdSince="<YYYY-MM-DD>", limit=50
```

A hit on the search is conclusive, and an empty answer is not. The search
matches text, unranked, and every word has to be in the same issue. So a report
worded differently is invisible to it. You read an empty answer against the
second call: everything filed since that day, newest first, read as subjects.
The day is the earliest anybody could have reported the defect. That is when the
code that carries it shipped, or when you first saw it.

`total` says whether that was the whole set or a page of it. Where it is larger
than the rows, move `createdSince` later until the two agree. What a page of
that end left out is older than its last row.

`category` narrows it further where the area is certain. An issue filed under no
Category is in no area at all, and thousands of the open bugs carry none. So the
day narrows the search to those, and the area does not.

## What a Report Carries

- **Tracker** — `Bug` for something broken. The project offers `Bug`, `Feature`,
  `Task`, `Story` and `Epic` and nothing else. So a tracker Redmine has
  elsewhere is not one this project takes.
- **Subject** — the title, and the line a triage reads instead of the report.
  Name the subsystem and what it does wrong.
- **Description** — the report itself, which Redmine renders as Textile. The
  section below is the syntax, and the one after it the shape.
- **TYPO3 Version** — the major the report is against, on its own rather than as
  a full release number. **This is the one field a Bug does not go without.**
- **Category** — the area the core files it under. Optional, and the section
  below says where the value comes from.
- **PHP Version** — the major and minor you produced the report on. Optional,
  and most reports leave it empty.
- **Priority** — `Should have` unless you have a reason, which is what nearly
  every report carries. `Must have`, `Could have`, `Won't have this time` and
  `-- undefined --` are the others.
- **Target version** — the reporter leaves it empty. The section below says why.
- **Complexity** — `trivial`, `easy`, `medium` or `hard`, and a guess about
  somebody else's work. Optional.
- **Is Regression** — a checkbox for a defect an earlier release did not have.
  Optional.
- **Sprint Focus** — whoever runs a sprint sets it there, not the reporter.
- **Tags** — free text, comma separated. Optional.
- **Assignee** — leave it empty. Whoever takes a report on assigns it.

## The Area

The project administers the categories and the core adds to them, so this page
does not copy them. One call answers which one a subject belongs to:

```
typo3_forge_lookup with open="oldest", category="<your own words>", limit=1
```

`categoriesUsed` in that answer is the tracker's own spelling of the areas those
words named. The field takes that spelling. A word that names several areas gets
all of them in the answer. So you tell two candidates apart rather than pick
between them. `category="*"` asks for the whole list without a subject.

A word that names no area gets an answer that says so. That is a statement about
the word rather than about the project.

## The Target Version

A report leaves it empty, and most core bugs carry none at any point. It names
the release the schedule puts a fix in. Whoever takes the fix on decides that,
not whoever describes the defect.

The values it gets later are the project's own open versions. `next-patchlevel`
and `Candidate for patchlevel` carry mostly bugs. `Candidate for Major Version`
carries mostly features. The open majors carry what the schedule puts in a
release by name. Which of the two patchlevel versions a given bugfix takes is
the scheduler's call. The tracker does not tell you.

## The Markup

**The description field renders Textile, not Markdown.**

- Heading: `h2. Steps to reproduce`, and the level is the digit.
- Ordered list: `#` and a space, one per line. Unordered: `*` and a space.
- Inline code: `@Foo::bar()@`. A backtick renders as a backtick.
- Code block: `<pre><code class="php">` … `</code></pre>`, and the class selects
  the syntax highlight. `class="diff"` colours a diff. `<pre>` on its own keeps
  the format and adds no highlight.
- Bold: `*word*`. Quote: `bq.` or `>` at the start of the line.

Nothing inside `<pre>` needs an escape. A placeholder written as `<type>`
arrives as `<type>`. So an escape of the angle brackets by hand puts the
entities in the output.

Redmine rewrites three things outside it, silently and in a way that survives
into the filed report:

- **A Markdown fence is not a fence.** Three backticks render as three
  backticks, in a paragraph that has lost the block's indentation with it.
- **Redmine reads a diff pasted as text as markup.** A leading `+` and `-`
  around the lines between them become an insertion span. The removed lines
  disappear into it. Wrap a diff in `<pre><code class="diff">`.
- **The auto-linker eats a placeholder inside a URL.** `https://<domain>/route`
  becomes a link whose text is doubly escaped. Put the URL in `@…@` where it
  carries a placeholder.

Consecutive lines that start `1.`, `2.`, `3.` are not a list either. They render
as one paragraph with a line break between them. That reads as intended and is
not what `#` produces.

## What the Description Says

Somebody who decides whether to reproduce a core report reads it. So you write
it in the order that decision goes. The core's own reports use these headings,
as `h2.` or `h3.`:

- **Problem** — the symptom, in the words it showed in. An exception message, a
  wrong value, a rendering that differs from the expected one.
- **Steps to reproduce** — an ordered list from a state somebody else can reach.
  A step that names a fixture only you have is where a report stalls.
- **Cause** — the class and method the defect is in, and what it does wrong.
  Optional, and it turns a report into a patch somebody can write.
- **Suggested fix** — a diff, where the cause section named one.

The description is what the reporter saw. What people decide about the report
goes into its comments afterwards and never back into this field.
