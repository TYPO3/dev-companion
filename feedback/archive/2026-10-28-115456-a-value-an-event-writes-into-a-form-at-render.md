---
date: 2026-10-28T11:54:56+00:00
category: missing-knowledge
status: closed
closed: 2026-10-29
model: claude-opus-5
tool: typo3_hint_lookup, typo3_task_guide
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# A value an event writes into a form at render is frozen by the page cache

## Observation

The task was a waste collection complaint form on a TYPO3 14.3 project site, whose submissions are stored and mailed out once a day as a CSV.

The form carries a spam check of the package's own: a listener on AfterCurrentPageIsResolvedEvent stamps a signed timestamp into a form element as the form renders, and a validator reads its age on submit, rejecting a form answered faster than a person can read it. That listener is the mechanism the form-framework hint recommends: "To prefill from the current request, listen to AfterCurrentPageIsResolvedEvent: it carries the FormRuntime and the request and is dispatched after request state was initialized but before submitted values are processed. Set an element's default value there, so a submitted value still wins."

On a cached page it does not do that, and nothing in the hint says so. EXT:form registers its plugin with ExtensionUtility::configurePlugin('Form', 'Formframework', [FormFrontendController::class => ['render', 'perform']], [FormFrontendController::class => ['perform']]) - render is cacheable, only perform is not. So the listener runs once per page-cache entry rather than once per request: every visitor is handed the same timestamp, and by the time anybody submits it is older than any limit the validator could set. The event is dispatched for a request, but the value it writes reaches one visitor's render and every later visitor's cache hit.

Measured on the running site. Three requests seconds apart returned the same stamp, 1793187824. After re-registering render among the non-cacheable actions, three requests three seconds apart returned 1793188048, 1793188051 and 1793188054. Behaviour matched: before the change an instant submission was accepted and stored, after it the same submission was rejected and a submission four seconds later accepted.

Two things make this hard to catch. The failure is silent - the value is stale rather than missing, so every check that reads it succeeds. And it passes its own test: the first render after a cache flush stamps a fresh value, which is the state a developer is in while building the thing. This guard was verified working in an earlier session of this same project and had been ineffective under production conditions the whole time.

The shape is not specific to a nonce. Anything about the visit rather than about the form has it - a token, a value prefilled from a GET parameter, a name. EXT:form's own honeypot randomises its field name per render and is frozen by the same cache.

## Query

typo3_hint_lookup id=form-framework; typo3_task_guide task="store EXT:form submissions in an own database table with a custom finisher, and send a daily CSV digest by email from a console command"

## Suggestion

Say it in form-framework, next to the AfterCurrentPageIsResolvedEvent statement that leads into it, because that statement is what sends a reader down this path: the render action is cacheable, so a value this event writes is written once per page-cache entry and not once per request. A value that describes the visit rather than the form needs the plugin re-registered with render among the non-cacheable actions - ExtensionUtility::configurePlugin('Form', 'Formframework', [FormFrontendController::class => ['render', 'perform']], [FormFrontendController::class => ['render', 'perform']]) in the project's ext_localconf.php.

Worth naming the symptom with it, because there is none to notice: the value is stale rather than absent, and the first render after a cache flush is fresh, so the mechanism passes the test a developer runs on it while building it.

The same fact decides whether EXT:form's honeypot is worth anything on a cached page, so it may belong wherever the honeypot is described rather than only here.
