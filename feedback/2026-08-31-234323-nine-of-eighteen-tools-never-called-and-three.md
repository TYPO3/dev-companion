---
date: 2026-08-31T23:43:23+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_flexform_lookup, typo3_schema_lookup, typo3_configuration_lookup, typo3_ter_lookup, typo3_test_run_guide, typo3_commit_message_guide
directory: /home/benji/projects/site-tierheim
---

# Nine of eighteen tools never called, and three of them cover work I did by hand and got wrong

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14. Measured from the full transcript: 796 tool calls over 5h04m, of which 682 Bash and 51 to this server — a ratio of 1:13. Nine of the eighteen typo3_* tools were never called once. This is the half the server cannot see, so it is worth naming precisely.

Three of the unused ones cover work I did by hand and got wrong or did the long way.

typo3_flexform_lookup. At 23:12 a functional test of mine failed: I had written a data provider case ['isSenior' => 1] expecting one animal back and got five. I then ran two shell commands — a grep for element names in Configuration/FlexForms/AnimalList.xml, then a python regex over the same file — to discover the field is called onlySenior. There is a tool named for exactly that question and I reached for grep. The failure was mine, the field name was knowable, and it cost a red test plus two round trips.

typo3_schema_lookup. At 23:0x I was deciding whether INSERT INTO new SELECT * FROM old was safe between two tables generated from the same TCA. I diffed `SHOW COLUMNS` output through md5sum, concluded the columns were identical because the names matched, ran the insert, and it failed on a type error — the column ORDER differed. Had it not failed, the positional insert would have shifted 3101 rows silently. I never asked the server what either table's schema was.

typo3_configuration_lookup. base.md, which was in my context the whole session, says this is what reports the value the installation resolved, after every extension has had its say. I read config/sites/main/settings.yaml and config/system/settings.php directly with sed and grep, repeatedly, including while changing what those files contain.

Two more I read and passed over on an assumption:

typo3_ter_lookup — I needed a package that provides symfony's XLIFF linter as a binary. I assumed a tool named for the TER would not know about a Composer-only package and ran `ddev composer search xliff lint` against Packagist instead. It found thoerner/xliff-lint. The assumption was never tested.

typo3_test_run_guide — named in the browser-tests hint as carrying "that suite, what it prints and what the run costs". I ran Playwright by hand throughout, including working out project filtering and -g flags by trial.

The remainder, unused with no particular regret: typo3_component_lookup, typo3_translation_domain_lookup, typo3_system_extension_lookup, typo3_snapshot_scope, typo3_server_scope, typo3_forge_lookup, typo3_gerrit_lookup, typo3_permalink_lookup, typo3_reference_list, typo3_script_lookup, typo3_feedback_list. typo3_commit_message_guide I did not call because the user committed himself — but the skills say to draft with it and I never offered.

## Query

Not called in a 5h session: typo3_flexform_lookup, typo3_schema_lookup, typo3_configuration_lookup, typo3_ter_lookup, typo3_test_run_guide, typo3_commit_message_guide, typo3_component_lookup, typo3_translation_domain_lookup, typo3_system_extension_lookup, and the scope/forge/gerrit/permalink/reference/script/feedback_list tools. Work done by shell instead: grep+python regex over packages/animalshelter_animals/Configuration/FlexForms/AnimalList.xml to find the field name onlySenior; `SHOW COLUMNS` diffed by md5sum between tx_tierheim_animal and tx_animalshelter_animal; sed/grep over config/sites/main/settings.yaml; `composer search xliff lint` against Packagist.

## Suggestion

Trimmed on 2026-09-01. Two of the three asks are answered: typo3_extension_describe
now names typo3_schema_lookup where it lists the tables and typo3_flexform_lookup
where it lists a binding, each with the argument to pass — D-ANS-129; and
typo3_ter_lookup does not reach Composer packages, which its description already
says in as many words, so the assumption was right and there is nothing to
correct. What is left open is the other one.

Give the hint answers the same treatment they already give documents.
project-extension-tests carries a documents array and that is how I found the
phpunit guide — the single best call of the session. Hints that imply a tool
could name it the same way: browser-tests naming typo3_test_run_guide,
frontend-dataprocessors naming typo3_configuration_lookup.
