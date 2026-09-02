---
date: 2026-09-02T13:50:45+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
directory: /home/benji/projects/site-tierheim
---

# No way to check a registration file before it is flushed; every mistake failed silently

## Observation

Task: same session — a v14.3 backend module built and then reshaped four times as the person I worked for changed what they wanted.

Counting the registration mistakes I made and how each was caught:

1. Sub-route `path` doubled → caught by typo3_backend_module_lookup, by chance, after tests had passed against it.
2. Parentless module lost its routes → caught by a Playwright test failing with the Layout module's chrome in the body ("Please select a page in the page tree"), then confirmed by typo3_backend_module_lookup.
3. position after:'*' not last → caught by reading the rendered module menu in a browser.
4. pages typeicon_classes mapping missing → caught by the person looking at the page tree.
5. A moved PHP class left the compiled DI container stale → the backend fell back to the Layout module again; `composer dump-autoload` plus a real cache rebuild fixed it. The error in the log was "Too few arguments to ...::__construct(), 1 passed in GeneralUtility.php", which points at the constructor rather than at the container.

Every one of these is a declarative file with no schema behind it, exactly as the backend-modules hint says ("a wrong key does not fail at boot, it fails when a user opens the module"). The hint is right, and it is also the whole of the help available: the only witness is the installation after a cache flush.

typo3_backend_module_lookup reads the booted installation, so it can only answer after the file is saved and the cache is cleared. That is still the single most useful call I made for this — it is what showed me the doubled path — but it is a post mortem, not a check.

## Query

Repeated cycle across the session: edit packages/animalshelter_animals/Configuration/Backend/Modules.php → ddev exec vendor/bin/typo3 cache:flush → typo3_backend_module_lookup(query="animalshelter") → read routes/position/navigationComponent → repeat.

## Suggestion

A tool that takes a path to a Configuration/Backend/Modules.php (or the array it returns) and reports what it would produce: the resolved route paths per entry, whether each entry will get routes at all, where it lands among its siblings, and which referenced identifiers (parent, position neighbour, iconIdentifier, labels domain) do not exist in this installation. It needs no cache flush and no save — it is the same resolution typo3_backend_module_lookup already reports, run against a file instead of the registry.

That one tool would have caught four of the five mistakes above before they reached a browser. If it is too much, the cheap half is the identifier check: parent, iconIdentifier and labels domain resolved against the installation, since all three fail at open time and none at read time.

Trimmed on 2026-09-02: the cheap half this names is done. typo3_backend_module_lookup takes a `file` and says which parent, which iconIdentifier and which labels it names that this installation does not have, without a cache flush and without the file being installed — the labels down to the trans-unit the module title is read from. The resolution half — route paths, whether an entry gets routes, sibling placement — is what is left. `D-FBK-055` says why it waits.
