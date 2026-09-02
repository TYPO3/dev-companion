---
date: 2026-09-02T13:52:00+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_icon_lookup, typo3_task_guide
directory: /home/benji/projects/site-tierheim
---

# A concept icon query returned my own extension's icons as if they were answers

## Observation

Task: same session — choosing icons for a new backend module in a v14.3 project extension.

I called typo3_icon_lookup(query="paw animal pet", limit=30) hoping for a core glyph for an animal. What came back was eleven entries, all of them icons my own two project extensions register — content-animalshelter-headline, content-animalshelter-list, tcarecords-tx_animalshelter_teammember-default and so on — each with `"why": ["substring \"animal\""]` and score 2.

That is honest: it says the match was on the substring "animal", and my extension key contains it. But the shape of the answer is a ranked list of icons, which reads as "here is what matches your concept", and none of the eleven had anything to do with a paw. The word "paw" and the word "pet" matched nothing and that is not visible in the answer either — I cannot tell from it which of my three query words did any work.

The description promises concept mapping ("identifiers spell shapes rather than intents, so concept words are mapped: warning finds actions-exclamation-triangle"). For a concept core has no icon for, I would rather be told that than handed my own extension's namespace back.

I drew my own icons instead, which was the right outcome. But later in the session the same question came back in a better form — the person asked "was für ein Tier soll das denn sein?" about a placeholder I had drawn, and the answer was that no animal glyph was wanted at all: what is missing is a photograph, so mimetypes-media-image is the icon. A concept query for "no image" or "placeholder" would have found that on the first attempt. I did not make it, because the first concept query had taught me the tool answers by substring.

## Query

typo3_icon_lookup(query="paw animal pet", limit=30) → 11 icons, all EXT:animalshelter_*, each why=["substring \"animal\""]. Contrast with typo3_icon_lookup(identifiers=[...]) which I used six times afterwards and which was consistently useful.

## Suggestion

Two small things:

1. Say which query words matched nothing. "paw: no match; pet: no match; animal: 11 substring matches, all in EXT:animalshelter_animals and EXT:animalshelter_sitepackage" is a different answer from a ranked list, and it is the one that would have stopped me looking.

2. Consider ranking core-registered identifiers above the caller's own project extensions for a concept query, or at least separating them. When I ask a concept question I am asking what exists to borrow; my own icons are what I already have.

The identifiers= mode has none of this problem and was one of the most reliable calls of the session — validating a batch read out of a draft template, with a registered/not answer per identifier. That is what caught actions-eye-slash before I shipped it.
