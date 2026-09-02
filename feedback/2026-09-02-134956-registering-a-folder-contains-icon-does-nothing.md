---
date: 2026-09-02T13:49:56+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_icon_lookup, typo3_hint_lookup
directory: /home/benji/projects/site-tierheim
---

# Registering a folder-contains icon does nothing without the typeicon_classes mapping

## Observation

Task: same session — let a sysfolder declare that it holds the animal records, so the backend module resolves its storage from the page tree instead of from a site setting.

I registered an item on pages.module and an icon for it:

    $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
        'label' => '...',
        'value' => 'animalshelter_animals',
        'icon' => 'apps-pagetree-folder-contains-animalshelter_animals',
    ];

plus the SVG in Configuration/Icons.php. typo3_icon_lookup confirmed the identifier registered, and confirmed that core's own apps-pagetree-folder-contains-fe_users exists — which is what made me believe the naming convention alone was the mechanism.

It is not. The folder kept the default icon everywhere. The person I worked for reported it: "das icon für contains plugin wird nicht im tree und auch nicht in der breadcrumb ausgegeben machen wir etwas falsch?"

IconFactory::mapRecordTypeToIconIdentifier() builds the record type 'contains-' . $row['module'] (EXT:core Classes/Imaging/IconFactory.php, ~line 152) and looks it up in $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'] — where core lists 'contains-shop', 'contains-fe_users' and friends. Without

    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-animalshelter_animals']
        = 'apps-pagetree-folder-contains-animalshelter_animals';

the `icon` on the item is only what the select field shows while the folder is being marked.

Nothing raises. The folder is simply drawn with the default, which is why a person had to see it rather than a check reporting it.

## Query

typo3_icon_lookup(identifiers=["apps-pagetree-folder-contains-fe_users","apps-pagetree-folder-default"]) — both registered; then typo3_icon_lookup(identifiers=["apps-pagetree-folder-contains-animalshelter_animals", ...]) after registering my own. Neither answer mentions typeicon_classes.

## Suggestion

Two places could carry it:

1. typo3_icon_lookup: when an identifier matches the apps-pagetree-folder-contains-* shape, say that the page tree reaches it through pages.ctrl.typeicon_classes['contains-<module>'] and not through the identifier's name.

2. A hint (or the drawing-a-content-icon rule, which I did not read — see my separate note about the rules list) for "marking a sysfolder as holding records of a type": the pages.module item, the typeicon_classes entry, the icon registration, and that a hidden or non-254 page carrying the marking is a mistake rather than a second storage.

The whole feature was ~40 lines and two of the four required pieces were invisible to me until a human looked at the screen.
