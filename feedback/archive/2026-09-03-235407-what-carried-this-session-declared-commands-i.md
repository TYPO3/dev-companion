---
date: 2026-09-03T23:54:07+00:00
category: idea
status: closed
closed: 2026-09-04
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_documentation_lookup, typo3_commit_message_guide
directory: /home/benji/projects/site-tierheim
---

# what carried this session: declared commands I would have guessed at, and two exact documentation...

## Observation

Task: set up an XML sitemap and robots.txt for a TYPO3 v14 project site. Recording what worked, so it is not broken later.

typo3_project_describe earned its place as the first call. The commands block is the part I would otherwise have invented: cs:check, lint:xliff, test:functional with their ddev invocations and each marked check or change. I ran exactly those three and nothing else, and the "a check you recommend that the repository does not declare is a wrong answer" framing is what stopped me from reaching for phpstan, which this repository does not have. The sites block with each site's resolved set list was the second most useful thing - it told me at a glance that no set carried typo3/seo-sitemap, which was the whole answer to the user's opening question. It also correctly identified the environment as DDEV with its post-start hooks, which mattered later when a restart re-ran them.

typo3_documentation_lookup answered two questions exactly, on the first query, with the canonical page each time: the Static Routes page gave me the routes/staticText shape verbatim including the Sitemap: line in the example body, and the XML sitemap page gave me the full RecordsXmlSitemapDataProvider configuration and the PageType decorator for /sitemap.xml. Passing a returned URL back as page= and getting the whole page as text with its code examples is the right shape for this - I did not have to guess at key names once. The user asked "was sagt die Dokumentation dazu?" mid-task and this is what let me answer with the actual text rather than a paraphrase.

typo3_commit_message_guide wrapped three bodies at 72 characters and caught a subject at 67 characters, telling me the summary was 15 over and declining to shorten it for me because the shortened claim would be mine. That division of labour is right: it measured and left the wording alone.

One thing I did not do, and the guide was right to insist on it: the checklist told me to sweep deprecations with typo3_changelog_lookup at v14 before writing, and I skipped it. Nothing went wrong, but that was luck rather than judgement - I extended an @internal-adjacent extension point of EXT:seo without checking what 14 had changed about it.

## Query

typo3_project_describe (no arguments); typo3_documentation_lookup queries=["static routes robots.txt site configuration","XML sitemap SEO configuration","records sitemap data provider custom table"] targetVersion="14", then page= for the two matching URLs; typo3_commit_message_guide keyword="TASK"/"BUGFIX" with summary and body.

## Suggestion

Keep the commands block of typo3_project_describe exactly as it is, including the runs classification and the ddev invocation - it is the single field that most changed what I did. Keep the resolved set list per site for the same reason.

Keep the two-step search-then-read shape of typo3_documentation_lookup. Returning several short queries in one call and then fetching a page by its canonical URL is what made it cheap enough to use before writing rather than after failing.

Keep commit_message_guide's refusal to rewrite a summary it judged too long, and keep it stating the arithmetic.
