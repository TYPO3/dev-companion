---
date: 2026-09-03T23:56:05+00:00
category: idea
status: open
model: claude-opus-5[1m]
directory: /home/benji/projects/site-tierheim
---

# datahandler-seeding was right on every point a seeding script actually hits

## Observation

Task: repair the content of a migrated project site on TYPO3 v14 - create a 404 page with a content element on it, add a missing content element to a page, correct a dozen existing records - against a running DDEV installation, with no console command in the repository to do any of it.

I asked typo3_hint_lookup with id=datahandler-seeding before writing the script, and it was the one lookup in the session that saved me from a defect rather than confirming what I already knew. Three of its statements earned their place immediately:

- Bootstrap::init($classLoader) and nothing else, with the note that the CLI binary's failsafe=true is what makes a copied bootstrap fail as "a constructor missing its arguments" rather than as a missing service. I would have copied the binary.
- The backend user in that order - initializeBackendUser(CommandLineUserAuthentication::class), then initializeBackendAuthentication(), then $GLOBALS['LANG'] from LanguageServiceFactory::createFromUserPreferences() - which is not derivable from the API and which DataHandler needs before it writes anything.
- pages.hidden defaults to 1 in TCA against 0 in the schema, so a page seeded without saying otherwise arrives hidden. I set hidden=0 explicitly because of that line; the page answered as the site's 404 target on the first run instead of becoming a second not-found I would have debugged from the wrong end.

The two-call rule for NEW ids that a non-relation field cannot substitute held as well: the content element's links had to name real page ids, and I wrote them in a second call from substNEWwithIDs, exactly as the hint says.

Recording this because the tool description asks for what worked. This hint is the shape the ones in the TCA and site-configuration areas would be worth having: what the API does not tell you, in the order a script hits it, with the failure each mistake produces.

The one thing I looked for in it and did not find, for a later addition: how to attach a file to a record's file field from such a script - retrieving or indexing the file, and the sys_file_reference shape DataHandler expects on v14. That is the next thing a content repair needs after creating the record, and it is where I stopped and left two images in a rich text field rather than guess.
