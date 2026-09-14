.. _typo3_label_lookup:

``typo3_label_lookup``
======================

Search the labels registered in the TYPO3 installation you work in and the XLF
files below project config/sites. Reuse is local to the translation resource the
code at hand already uses. Pass resource whenever you know it, and do not
reference a match from another module or package merely because its text is
identical. The console answers with the resource overrides the installation
applies. The files supply an answer when the console does not answer, and they
report non-standard names or resources with no static reference. Every match
comes back as a translation domain reference. That reference for a file this
installation does not have, one a patch is about to add, is
typo3_translation_domain_lookup. Answers from: installation, packages.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`,
:ref:`packages <answer-sources-packages>`.

Takes
-----

.. code-block:: yaml

    # Words from the label text or its trans-unit id, for example "save document" or
    # "labels.title". Several words match on their own, whatever their case and
    # order. A label has to carry every one of them, in its text or in its id. When
    # none carries all of them, the answer says how far each word reaches on its
    # own.
    query: string
    # Restrict the search to the extension that owns the code at hand.
    extension: string  # optional
    # Restrict the search to the exact XLF resource the code at hand already uses,
    # for example
    # "EXT:my_sitepackage/Resources/Private/Language/Backend/Import.xlf". A match
    # from another resource is not a reuse candidate. Where no label in it reaches
    # the query, the answer names the resources that do hold one. So you replace a
    # guessed path with one that exists.
    resource: string  # optional
    # Maximum number of labels to return.
    limit: integer  # optional

Answers with
------------

.. code-block:: yaml

    query: string
    matchCount: integer  # optional
    # One of: installation, packages. installation: its assembled runtime state
    # answered. packages: the server read the files the installed packages ship,
    # because it could not ask the console. The answer misses the overrides that
    # apply at runtime.
    answeredBy: string  # optional
    # How many labels each word of the query reaches on its own, inside the
    # extension and the resource the call named. That says where to narrow when the
    # query as a whole reaches none. A label answers the query only where it carries
    # every word.
    terms:  # optional
      - # The word, lowercased as the search used it.
        term: string
        matchCount: integer
    # The same words counted outside the resource, inside the extension the call
    # named or the resource implies. Returned only where a word reaches there and
    # nothing inside the resource, which makes the resource what emptied this answer
    # rather than the words.
    termCountsWithoutTheNarrowing:  # optional
      - # The word, lowercased as the search used it.
        term: string
        matchCount: integer
    # The resources that hold a label with every word of the query. Returned where
    # the call named a resource and no label at all in it reaches the query. So you
    # replace a guessed path with one that exists. Empty means no resource holds
    # such a label.
    resources: [string]  # optional
    resourceDiagnostics:  # optional
      - # The XLF resource this diagnosis describes.
        resource: string
        # Where the file sits: package, site-set, or project-site.
        location: string
        # Whether the file name follows the convention for its location.
        conventionalName: boolean
        # Whether the search found an implicit or static reference.
        referenced: boolean
        # Source files that name the resource. A conventional site-set labels.xlf
        # names its adjacent config.yaml as an implicit reference.
        references: [string]
        # Naming, discovery, and static-reference warnings for this resource.
        warnings: [string]
    labels:  # optional
      - # The reusable label reference: a translation domain for package labels or
        # an LLL file reference for project-site labels.
        ref: string
        # The translation domain, empty for a project-site XLF that TYPO3 does not
        # register as a package resource.
        domain: string
        # The trans-unit id.
        key: string
        # The label text in the searched locale.
        source: string
        # The XLF file it lives in.
        resource: string  # optional
    unsupported:  # optional
      # One of: no-installation, misconfigured, installation-not-answering.
      # no-installation: nothing to ask from here, and searched says where the
      # discovery looked. misconfigured: the caller named an installation the server
      # could not use, so the discovery searched nothing.
      # installation-not-answering: the discovery found one and its console did not
      # answer. A stopped container or a database with no schema is that state, and
      # it ends without a reinstall.
      cause: string
      # What stopped it, in the words the attempt produced.
      reason: string
      # One of: installed, not-installed, undeclared, null. The state of the
      # repository the caller stands in, which the cause does not say. installed:
      # packages sit below the root the discovery found, so the caller lacks no
      # install. not-installed: the repository declares TYPO3 and has no packages
      # below it yet, so this call answers once composer install has run.
      # undeclared: nothing in the directories the discovery walked declares TYPO3,
      # so an install here answers nothing. Null where the discovery looked at
      # nothing: a named root the server could not use, or an entrypoint that handed
      # no directory in.
      repositoryState: string or null  # optional
      # What the reason means where the message alone does not say it. A console
      # that starts and then fails on a missing table has a database without a
      # schema, not a broken installation. Empty where the server knows nothing
      # beyond the reason.
      diagnosis: string  # optional
      # Every directory the discovery walked, in order. "Nothing found" and "the
      # server started somewhere else" read the same, and only this list tells them
      # apart. Empty where the discovery never ran.
      searched: [string]
      # The setting the server could not use. Null where the caller set nothing.
      misconfiguration: string or null  # optional
      settings:
        # Environment variable that names the installation root.
        root: string
        # Environment variable that names the console command.
        console: string

The answer carries exactly one of these sets of fields: ``query``, ``resource``,
``matchCount``, ``answeredBy``, ``terms``, ``labels`` — or ``query``,
``unsupported``.

Answered
--------

Recorded on 2026-09-10 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.7-dev, the 14.3 core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Its dependencies are not installed —
vendor/autoload.php is not there either, and composer install writes both.
Answered against composer-project, TYPO3 14.3.0, the installation this
repository writes below .fixtures/, whose console answers. The tools that
declare ``answeredBy`` carry an answer from each, under a heading naming which;
every other answer is from the first alone, because nothing in it would differ.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

labels: hit
~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "save"
    }

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    106 label(s) in <installation> match "save" — showing the first 25:
    - backend.alt_doc:buttons.confirm.duplicate_record_changed.yes
      "Yes, save and duplicate this record"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:buttons.confirm.close_without_save.yes
      "Discard changes"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:buttons.confirm.close_without_save.no
      "Keep editing"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:buttons.confirm.save_and_close
      "Save and close"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.confirm.duplicate_record_changed.title
      "Do you want to save before duplicating this record?"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.confirm.duplicate_record_changed.content
      "You currently have unsaved changes. Do you want to save your changes before duplicating this record?"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.confirm.new_record_changed.title
      "Do you want to save before adding?"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.confirm.new_record_changed.content
      "You need to save your changes before creating a new record. Do you want to save and create now?"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:buttons.confirm.new_record_changed.yes
      "Yes, save and create now"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.confirm.view_record_changed.title
      "Do you want to save before viewing?"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.confirm.view_record_changed.content
      "You currently have unsaved changes. You can either discard these changes or save and view them."
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.confirm.view_record_changed.invalid_form
      "The form appears to be invalid, therefore "Save changes and view" is not available."
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.confirm.view_record_changed.content.is-new-page
      "You need to save your changes before viewing the page. Do you want to save and view them now?"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:buttons.confirm.view_record_changed.no-save
      "View without changes"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:buttons.confirm.view_record_changed.save
      "Save changes and view"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.confirm.close_without_save.title
      "Unsaved changes"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.confirm.close_without_save.content
      "You currently have unsaved changes which will be discarded if you close without saving."
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.alert.save_with_error.title
      "You have errors in your form!"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:label.alert.save_with_error.content
      "Please check the form, there is at least one error in your form."
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:buttons.alert.save_with_error.ok
      "OK"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:notification.record_saved.title.singular
      "Record saved"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:notification.record_saved.title.plural
      "Records saved"
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:notification.record_saved.message
      "Record "%s" has been successfully saved."
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.alt_doc:notification.mass_saving.message
      "%s records have been successfully saved."
      EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
    - backend.mfa:save.failure
      "Could not update MFA provider %s. Please try again."
      EXT:backend/Resources/Private/Language/locallang_mfa.xlf

    Reference a label by the ref shown first. Package resources use a translation domain; project-site resources use the full LLL file reference.

    A match is reusable only when its resource is the one already used at the consuming code. A label from another module or package is not a shared vocabulary merely because its text matches; call again with resource once that usage context is known.

    Write a new trans-unit in English in the unprefixed source file, and put any other wording in the locale-prefixed file beside it — de.locallang.xlf for locallang.xlf — under the same unit id. A source file that is not English is a defect to correct in place rather than a convention to continue, and adding an en.-prefixed file is not that correction: typo3_hint_lookup with id=language-files has what it is.

    Read from the XLF files of the installed packages and project site configuration: the console could not be asked (<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both). What that leaves out is the assembled runtime state — a label an installation replaces through LANG/resourceOverrides is shown here as its package ships it.

Data:

.. code-block:: json

    {
        "query": "save",
        "resource": null,
        "matchCount": 106,
        "labels": [
            {
                "ref": "backend.alt_doc:buttons.confirm.duplicate_record_changed.yes",
                "domain": "backend.alt_doc",
                "key": "buttons.confirm.duplicate_record_changed.yes",
                "source": "Yes, save and duplicate this record",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:buttons.confirm.close_without_save.yes",
                "domain": "backend.alt_doc",
                "key": "buttons.confirm.close_without_save.yes",
                "source": "Discard changes",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:buttons.confirm.close_without_save.no",
                "domain": "backend.alt_doc",
                "key": "buttons.confirm.close_without_save.no",
                "source": "Keep editing",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:buttons.confirm.save_and_close",
                "domain": "backend.alt_doc",
                "key": "buttons.confirm.save_and_close",
                "source": "Save and close",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.confirm.duplicate_record_changed.title",
                "domain": "backend.alt_doc",
                "key": "label.confirm.duplicate_record_changed.title",
                "source": "Do you want to save before duplicating this record?",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.confirm.duplicate_record_changed.content",
                "domain": "backend.alt_doc",
                "key": "label.confirm.duplicate_record_changed.content",
                "source": "You currently have unsaved changes. Do you want to save your changes before duplicating this record?",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.confirm.new_record_changed.title",
                "domain": "backend.alt_doc",
                "key": "label.confirm.new_record_changed.title",
                "source": "Do you want to save before adding?",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.confirm.new_record_changed.content",
                "domain": "backend.alt_doc",
                "key": "label.confirm.new_record_changed.content",
                "source": "You need to save your changes before creating a new record. Do you want to save and create now?",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:buttons.confirm.new_record_changed.yes",
                "domain": "backend.alt_doc",
                "key": "buttons.confirm.new_record_changed.yes",
                "source": "Yes, save and create now",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.confirm.view_record_changed.title",
                "domain": "backend.alt_doc",
                "key": "label.confirm.view_record_changed.title",
                "source": "Do you want to save before viewing?",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.confirm.view_record_changed.content",
                "domain": "backend.alt_doc",
                "key": "label.confirm.view_record_changed.content",
                "source": "You currently have unsaved changes. You can either discard these changes or save and view them.",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.confirm.view_record_changed.invalid_form",
                "domain": "backend.alt_doc",
                "key": "label.confirm.view_record_changed.invalid_form",
                "source": "The form appears to be invalid, therefore \"Save changes and view\" is not available.",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.confirm.view_record_changed.content.is-new-page",
                "domain": "backend.alt_doc",
                "key": "label.confirm.view_record_changed.content.is-new-page",
                "source": "You need to save your changes before viewing the page. Do you want to save and view them now?",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:buttons.confirm.view_record_changed.no-save",
                "domain": "backend.alt_doc",
                "key": "buttons.confirm.view_record_changed.no-save",
                "source": "View without changes",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:buttons.confirm.view_record_changed.save",
                "domain": "backend.alt_doc",
                "key": "buttons.confirm.view_record_changed.save",
                "source": "Save changes and view",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.confirm.close_without_save.title",
                "domain": "backend.alt_doc",
                "key": "label.confirm.close_without_save.title",
                "source": "Unsaved changes",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.confirm.close_without_save.content",
                "domain": "backend.alt_doc",
                "key": "label.confirm.close_without_save.content",
                "source": "You currently have unsaved changes which will be discarded if you close without saving.",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.alert.save_with_error.title",
                "domain": "backend.alt_doc",
                "key": "label.alert.save_with_error.title",
                "source": "You have errors in your form!",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:label.alert.save_with_error.content",
                "domain": "backend.alt_doc",
                "key": "label.alert.save_with_error.content",
                "source": "Please check the form, there is at least one error in your form.",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:buttons.alert.save_with_error.ok",
                "domain": "backend.alt_doc",
                "key": "buttons.alert.save_with_error.ok",
                "source": "OK",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:notification.record_saved.title.singular",
                "domain": "backend.alt_doc",
                "key": "notification.record_saved.title.singular",
                "source": "Record saved",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:notification.record_saved.title.plural",
                "domain": "backend.alt_doc",
                "key": "notification.record_saved.title.plural",
                "source": "Records saved",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:notification.record_saved.message",
                "domain": "backend.alt_doc",
                "key": "notification.record_saved.message",
                "source": "Record \"%s\" has been successfully saved.",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.alt_doc:notification.mass_saving.message",
                "domain": "backend.alt_doc",
                "key": "notification.mass_saving.message",
                "source": "%s records have been successfully saved.",
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
            },
            {
                "ref": "backend.mfa:save.failure",
                "domain": "backend.mfa",
                "key": "save.failure",
                "source": "Could not update MFA provider %s. Please try again.",
                "resource": "EXT:backend/Resources/Private/Language/locallang_mfa.xlf"
            }
        ],
        "terms": [
            {
                "term": "save",
                "matchCount": 106
            }
        ],
        "resourceDiagnostics": [
            {
                "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf",
                "location": "package",
                "conventionalName": true,
                "referenced": true,
                "references": [
                    "Build/Sources/TypeScript/backend/contextual-record-edit.ts",
                    "Build/Sources/TypeScript/backend/element/contextual-record-edit-trigger.ts",
                    "Build/Sources/TypeScript/backend/form-engine-review.ts",
                    "Build/Sources/TypeScript/backend/form-engine.ts",
                    "Build/Sources/TypeScript/backend/form-engine/container/flex-form-container-container.ts",
                    "Build/Sources/TypeScript/backend/form-engine/container/inline-control-container.ts",
                    "Build/Sources/TypeScript/backend/form-engine/element/select-tree-toolbar.ts",
                    "Build/Sources/TypeScript/backend/form-engine/element/suggest/result-container.ts",
                    "Build/Sources/TypeScript/backend/grid-editor.ts",
                    "Build/Sources/TypeScript/filelist/file-delete.ts",
                    "EXT:backend/Classes/Controller/ContextualRecordEditController.php",
                    "EXT:backend/Classes/Controller/EditDocumentController.php",
                    "EXT:backend/Classes/Form/Element/BackendLayoutWizardElement.php",
                    "EXT:backend/Classes/Form/Element/GroupElement.php",
                    "EXT:backend/Classes/Form/FormResultCompiler.php",
                    "EXT:backend/Classes/RecordList/DatabaseRecordList.php",
                    "EXT:backend/Resources/Private/Partials/PageLayout/RecordDefault/Header.fluid.html",
                    "EXT:backend/Resources/Private/Templates/Form/ContextualRecordEdit.fluid.html",
                    "EXT:backend/Resources/Public/JavaScript/contextual-record-edit.js",
                    "EXT:backend/Resources/Public/JavaScript/element/contextual-record-edit-trigger.js",
                    "EXT:backend/Resources/Public/JavaScript/form-engine-review.js",
                    "EXT:backend/Resources/Public/JavaScript/form-engine.js",
                    "EXT:backend/Resources/Public/JavaScript/form-engine/container/flex-form-container-container.js",
                    "EXT:backend/Resources/Public/JavaScript/form-engine/container/inline-control-container.js",
                    "EXT:backend/Resources/Public/JavaScript/form-engine/element/select-tree-toolbar.js",
                    "EXT:backend/Resources/Public/JavaScript/form-engine/element/suggest/result-container.js",
                    "EXT:backend/Resources/Public/JavaScript/grid-editor.js",
                    "EXT:beuser/Resources/Private/Partials/BackendUser/PaginatedList.fluid.html",
                    "EXT:beuser/Resources/Private/Partials/BackendUserGroup/PaginatedList.fluid.html",
                    "EXT:beuser/Resources/Private/Partials/Filemount/PaginatedList.fluid.html",
                    "EXT:filelist/Classes/ContextMenu/ItemProviders/FileProvider.php",
                    "EXT:filelist/Classes/Controller/FileListController.php",
                    "EXT:filelist/Resources/Public/JavaScript/file-delete.js",
                    "EXT:reactions/Resources/Private/Templates/Management/Overview.fluid.html",
                    "EXT:redirects/Resources/Private/Templates/Management/Overview.fluid.html",
                    "EXT:redirects/Resources/Private/Templates/QrCode/Overview.fluid.html",
                    "EXT:redirects/Resources/Private/Templates/ShortUrl/Overview.fluid.html",
                    "EXT:webhooks/Resources/Private/Templates/Management/Overview.fluid.html"
                ],
                "warnings": []
            },
            {
                "resource": "EXT:backend/Resources/Private/Language/locallang_mfa.xlf",
                "location": "package",
                "conventionalName": true,
                "referenced": true,
                "references": [
                    "EXT:backend/Classes/Controller/MfaAjaxController.php",
                    "EXT:backend/Classes/Controller/MfaConfigurationController.php",
                    "EXT:backend/Classes/Controller/MfaSetupController.php",
                    "EXT:backend/Resources/Private/Partials/Mfa/SetupInstructions.fluid.html",
                    "EXT:backend/Resources/Private/Templates/Mfa/Auth.fluid.html",
                    "EXT:backend/Resources/Private/Templates/Mfa/Edit.fluid.html",
                    "EXT:backend/Resources/Private/Templates/Mfa/Overview.fluid.html",
                    "EXT:backend/Resources/Private/Templates/Mfa/Setup.fluid.html",
                    "EXT:backend/Resources/Private/Templates/Mfa/Standalone/Selection.fluid.html",
                    "EXT:backend/Resources/Private/Templates/Mfa/Standalone/Setup.fluid.html"
                ],
                "warnings": []
            }
        ],
        "answeredBy": "packages"
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    3 label(s) in <installation> match "save":
    - backend.locallang:labels.save
      "Save"
      EXT:backend/Resources/Private/Language/locallang.xlf
    - backend.locallang:labels.saveAndClose
      "Save and close"
      EXT:backend/Resources/Private/Language/locallang.xlf
    - core.locallang_core:labels.savedok
      "Save document"
      EXT:core/Resources/Private/Language/locallang_core.xlf

    Reference a label by the ref shown first. Package resources use a translation domain; project-site resources use the full LLL file reference.

    A match is reusable only when its resource is the one already used at the consuming code. A label from another module or package is not a shared vocabulary merely because its text matches; call again with resource once that usage context is known.

    Write a new trans-unit in English in the unprefixed source file, and put any other wording in the locale-prefixed file beside it — de.locallang.xlf for locallang.xlf — under the same unit id. A source file that is not English is a defect to correct in place rather than a convention to continue, and adding an en.-prefixed file is not that correction: typo3_hint_lookup with id=language-files has what it is.

    Resource warnings:
    - EXT:backend/Resources/Private/Language/locallang.xlf: No static reference to this resource was found; references assembled at runtime are outside this scan.
    - EXT:core/Resources/Private/Language/locallang_core.xlf: No static reference to this resource was found; references assembled at runtime are outside this scan.

Data:

.. code-block:: json

    {
        "query": "save",
        "resource": null,
        "matchCount": 3,
        "labels": [
            {
                "ref": "backend.locallang:labels.save",
                "domain": "backend.locallang",
                "key": "labels.save",
                "source": "Save",
                "resource": "EXT:backend/Resources/Private/Language/locallang.xlf"
            },
            {
                "ref": "backend.locallang:labels.saveAndClose",
                "domain": "backend.locallang",
                "key": "labels.saveAndClose",
                "source": "Save and close",
                "resource": "EXT:backend/Resources/Private/Language/locallang.xlf"
            },
            {
                "ref": "core.locallang_core:labels.savedok",
                "domain": "core.locallang_core",
                "key": "labels.savedok",
                "source": "Save document",
                "resource": "EXT:core/Resources/Private/Language/locallang_core.xlf"
            }
        ],
        "terms": [
            {
                "term": "save",
                "matchCount": 3
            }
        ],
        "resourceDiagnostics": [
            {
                "resource": "EXT:backend/Resources/Private/Language/locallang.xlf",
                "location": "package",
                "conventionalName": true,
                "referenced": false,
                "references": [],
                "warnings": [
                    "No static reference to this resource was found; references assembled at runtime are outside this scan."
                ]
            },
            {
                "resource": "EXT:core/Resources/Private/Language/locallang_core.xlf",
                "location": "package",
                "conventionalName": true,
                "referenced": false,
                "references": [],
                "warnings": [
                    "No static reference to this resource was found; references assembled at runtime are outside this scan."
                ]
            }
        ],
        "answeredBy": "installation"
    }

labels: miss
~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "quantumflux"
    }

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    No label in <installation> matches "quantumflux". This is an answer about your installation rather than about TYPO3 in general.

    A match is reusable only when its resource is the one already used at the consuming code. A label from another module or package is not a shared vocabulary merely because its text matches; call again with resource once that usage context is known.

    Write a new trans-unit in English in the unprefixed source file, and put any other wording in the locale-prefixed file beside it — de.locallang.xlf for locallang.xlf — under the same unit id. A source file that is not English is a defect to correct in place rather than a convention to continue, and adding an en.-prefixed file is not that correction: typo3_hint_lookup with id=language-files has what it is.

    Read from the XLF files of the installed packages and project site configuration: the console could not be asked (<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed — vendor/autoload.php is not there either, and composer install writes both). What that leaves out is the assembled runtime state — a label an installation replaces through LANG/resourceOverrides is shown here as its package ships it.

Data:

.. code-block:: json

    {
        "query": "quantumflux",
        "resource": null,
        "matchCount": 0,
        "labels": [],
        "terms": [
            {
                "term": "quantumflux",
                "matchCount": 0
            }
        ],
        "resourceDiagnostics": [],
        "answeredBy": "packages"
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    No label in <installation> matches "quantumflux". This is an answer about your installation rather than about TYPO3 in general.

    A match is reusable only when its resource is the one already used at the consuming code. A label from another module or package is not a shared vocabulary merely because its text matches; call again with resource once that usage context is known.

    Write a new trans-unit in English in the unprefixed source file, and put any other wording in the locale-prefixed file beside it — de.locallang.xlf for locallang.xlf — under the same unit id. A source file that is not English is a defect to correct in place rather than a convention to continue, and adding an en.-prefixed file is not that correction: typo3_hint_lookup with id=language-files has what it is.

Data:

.. code-block:: json

    {
        "query": "quantumflux",
        "resource": null,
        "matchCount": 0,
        "labels": [],
        "terms": [
            {
                "term": "quantumflux",
                "matchCount": 0
            }
        ],
        "resourceDiagnostics": [],
        "answeredBy": "installation"
    }
