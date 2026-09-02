---
date: 2026-09-02T13:49:21+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_task_guide, typo3_backend_module_lookup
directory: /home/benji/projects/site-tierheim
---

# A sub-route's path is appended to the module's, and no hint says so

## Observation

Task: build a backend module for maintaining 3101 animal records in a TYPO3 v14.3 project extension, and later add routes to it for changing a status, exporting and deleting.

The backend-modules hint (delivered inline by typo3_task_guide, changeType=feature) states: "routes maps a route name to a target of the form Controller::class . '::method'; the entry named _default is what the module opens with." It does not say what happens to a `path` stated on a non-default route.

I wrote:

    'listing' => [
        'path' => '/module/animalshelter/animals/listing',
        'target' => AnimalModuleController::class . '::updateListingAction',
    ],

TYPO3 appended that to the module's own path and registered the route at
/module/animalshelter/animals/module/animalshelter/animals/listing.

Nothing failed. The form action was built with buildUriFromRoute() from the same registration, so the link and the route agreed with each other; five Playwright tests exercising the status change passed against the wrong URL. I found it only because I called typo3_backend_module_lookup afterwards for an unrelated reason and read the `routes[].path` in its answer.

The correct form is core's: state no `path` on a sub-route at all and let the route name be appended (EXT:backend Configuration/Backend/Modules.php, site_configuration). I established that by reading that file, not from the hint.

This is a silent-by-construction mistake: the registration file has no schema, the route resolves, the link works, and the only witness is the registry.

## Query

typo3_task_guide(task="Add a backend module for maintaining animal records with filtering, search and status change", changeType="feature", targetVersion="14.3", paths=["packages/animalshelter_animals/Configuration/Backend/Modules.php", ...]) — the backend-modules hint returned in that answer. Then typo3_backend_module_lookup(query="animalshelter") which exposed the doubled path.

## Suggestion

Add one sentence to the backend-modules hint: a sub-route inherits the module's `path` and has its route name appended to it, so a `path` stated on it is appended a second time; core states none. Ideally with the negative example, because the wrong form is what an author writes when copying the module entry's own shape.

Related: `methods` on a route is not mentioned in the hint either. I found it in core's site_configuration entry and used it to make the state-changing routes POST-only.
