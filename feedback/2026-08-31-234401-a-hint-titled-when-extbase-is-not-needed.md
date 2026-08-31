---
date: 2026-08-31T23:44:01+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3-content-element-development
directory: /home/benji/projects/site-tierheim
---

# A hint titled "When Extbase Is Not Needed" existed and the DataProcessor-vs-plugin decision never...

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14; the animal listing shows 3101 records with filtering, pagination and a detail view. The user, closing the session: "du hast auch wieder dataprocessors, einem extbase plugin vorgezogen — obwohl du die menge der daten kanntest."

What I built: BK2K\AnimalShelterAnimals\DataProcessing\AnimalListProcessor, about 230 lines, with private methods fetchList, countList, buildPagination, fetchSingle, applyFilters, resolveStoragePids and enrich. Hand-rolled QueryBuilder, hand-rolled page arithmetic, hand-rolled reading of `tier` and `seite` out of the query params, hand-rolled checking that a requested uid sits on an allowed storage pid. Every one of those is something Extbase provides — repository, pagination, argument mapping, and a controller that separates list from show.

The data volume was known from the first hour: the crawl found 104 current animals and 2997 adoption successes before any code was written. So the scale was an input to the decision and I did not treat it as one.

The relevant part for this server: the hint corpus carries an entry whose title is exactly the decision — `extbase`, "What Extbase Is For, and When It Is Not Needed". Beside it sit extbase-pagination ("Paginating a List"), extbase-domain-mapping, extbase-arguments ("What Arrives From a Request, and What Silently Does Not") and extbase-plugin-registration. Also frontend-dataprocessors, the other side of the same question.

I called none of them. My seventeen typo3_hint_lookup calls were: installation-setup, php-versions, environment-runtime-readers, project-configuration-files, site-sets, sitepackage-templates, sitepackage-backend-layouts, record-routing, tca-formengine, record-page-title, language-files, content-elements, content-element-preview, content-element-shape, and three task-shaped queries. I fetched record-routing — the routing of a record detail view — which is downstream of the architecture decision, and never the decision itself.

All six ids were listed in the availableHints index of answers I received, repeatedly, across the whole session. They were one call away the entire time. The decision was made in the first hours, by default, because a DataProcessor is what you reach for when you are thinking about getting rows onto a page rather than about what the plugin has to do over its life.

This is the same failure as the html CType and the missing backend module, and it has the same shape: a structural choice made from the import's point of view, at a moment when nothing asked the question out loud.

## Query

Whole session. Hints never called though present in every availableHints index returned: extbase, extbase-pagination, extbase-domain-mapping, extbase-arguments, extbase-plugin-registration, extbase-persistence-internals, frontend-dataprocessors. Called instead: record-routing (downstream). Built: packages/animalshelter_animals/Classes/DataProcessing/AnimalListProcessor.php with fetchList/countList/buildPagination/fetchSingle/applyFilters against 3101 records in tx_animalshelter_animal.

## Suggestion

The `extbase` hint answers the question. What is missing is anything that raises it.

Put the decision where a content element is designed. typo3-content-element-development's "Choose the content model first" section decides fields and child tables; it does not ask what renders the element, and for anything list-shaped that is the larger decision. A paragraph there — a bounded element renders from TypoScript and a DataProcessor; a list with filtering, pagination, a detail view or its own routing is a plugin, and the tipping point is roughly when the processor grows a second query — would have caught me, because that skill was eventually active and I read it.

And make record count part of it, the same way it should be part of the backend-module question. Three thousand records with a filter and a detail view is not a borderline case; it is on the far side of every threshold. A hint that names the symptoms of having chosen wrong — a DataProcessor that has grown a count query beside its list query, that reads request arguments itself, that computes page numbers, that re-checks storage pids on a single-record lookup — lets a session recognise it has crossed the line even after it has, which is the only recognition available once the code exists.

Cross-reference it from frontend-dataprocessors, which is where somebody about to write one lands.
