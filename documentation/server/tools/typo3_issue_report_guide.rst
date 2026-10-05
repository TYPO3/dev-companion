.. _typo3_issue_report_guide:

``typo3_issue_report_guide``
============================

*Draft a TYPO3 Core issue*

Draft a new issue for the TYPO3 Core project on forge.typo3.org, for a bug or a
task found on the way. Core only. The answer is the draft, the checks, and a
link that opens the tracker's new-issue form with every field filled in:
tracker, subject, description, TYPO3 Version, PHP Version, category, priority,
target version, complexity, Is Regression, tags and parent task. Submitting
stays yours: the form needs an account, and this tool files nothing. Pass the
description as Textile, or as its parts — problem, stepsToReproduce, cause and
suggestedFix — and the draft writes them in the order the core's own reports
use. The checks name what the tracker would render wrong, a Bug without its
TYPO3 Version, and a category or target version the project does not have. The
same call searches the tracker for issues that match searchFor, the subject by
default, and lists them as possible duplicates. Every draft carries the tag
dev-companion. typo3_forge_lookup reads issues that already exist, and settles a
negative this search leaves open. Answers from: knowledge, network.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`knowledge <answer-sources-knowledge>`,
:ref:`network <answer-sources-network>`.

Takes
-----

.. code-block:: yaml

    # The title. Name the subsystem and what it does wrong; a triage reads this line
    # instead of the report.
    subject: string
    # One of: Bug, Feature, Task, Story, Epic. Bug for something broken. The core
    # project offers these five and no other.
    tracker: string  # optional
    # The whole description as Textile. Left out, the draft composes it from
    # problem, stepsToReproduce, cause and suggestedFix.
    description: string  # optional
    # The symptom, in the words it showed in: an exception message, a wrong value, a
    # rendering that differs from the expected one. Textile.
    problem: string  # optional
    # One step per entry, from a state somebody else can reach. The draft writes
    # them as an ordered list.
    stepsToReproduce: [string]  # optional
    # The class and method the defect is in, and what it does wrong. Textile.
    cause: string  # optional
    # A diff. The draft wraps it in <pre><code class="diff"> so the tracker keeps
    # the removed lines.
    suggestedFix: string  # optional
    # The TYPO3 major you saw it on, such as 14. A full release number is cut to its
    # major. A Bug does not go without it.
    typo3Version: string  # optional
    # The PHP major and minor you saw it on, such as 8.4. Optional.
    phpVersion: string  # optional
    # The area, in the tracker's own spelling. typo3_forge_lookup with category in
    # your own words answers that spelling.
    category: string  # optional
    # One of: Must have, Should have, Could have, Won't have this time. Left out,
    # the form keeps its default, Should have, which nearly every report carries.
    priority: string  # optional
    # The form's Target version, by the name the form shows, such as
    # next-patchlevel. It is the schedule's call, so set it where you take the fix
    # on yourself.
    fixedVersion: string  # optional
    # Whether an earlier release did not have the defect.
    isRegression: boolean  # optional
    # One of: trivial, easy, medium, hard. A guess at the fix. Optional.
    complexity: string  # optional
    # Free tags. The draft adds dev-companion to every report.
    tags: [string]  # optional
    # The issue this one is a part of.
    parentIssue: integer  # optional
    # Two or three words of the symptom for the duplicate search. Every word has to
    # be in the same issue, so a whole subject rarely matches. Defaults to the
    # subject.
    searchFor: string  # optional

Answers with
------------

.. code-block:: yaml

    # The title as the form gets it.
    subject: string
    # Bug, Feature, Task, Story or Epic.
    tracker: string
    # The description as Textile, as the form gets it unless descriptionInLink is
    # false.
    description: string
    # Every field the link fills, in the order the form shows them. A field that is
    # not here stays as the form leaves it: Assignee and Sprint Focus are set by
    # whoever takes the issue on.
    form:
      - # The label the form shows.
        field: string
        # What the link fills in.
        value: string
    # The tracker's new-issue form with these fields filled in. An anonymous reader
    # passes the login first, and the form arrives filled after it.
    url: string
    # False where the description is too long for a link the tracker accepts. The
    # link then fills every other field, and the description is pasted by hand.
    descriptionInLink: boolean
    duplicateSearch:
      # The words the search took.
      query: string
      # One of: answered, empty, unavailable.
      status: string
      # How many issues matched, which may be more than the list carries.
      total: integer
    # Issues that carry every word of the search. Empty is not a negative: a wording
    # reaches only the issues worded that way.
    possibleDuplicates:
      - # The issue number; typo3_forge_lookup with issue reads it whole.
        issue: integer
        subject: string
        tracker: string
        status: string
        # Where a person reads it.
        url: string
    checks:
      - # One of: error, warning, info.
        level: string
        # Stable identifier of the check, for example missing-typo3-version.
        code: string
        message: string
    # What follows the draft, named as the call that answers it.
    nextTools:
      - tool: string
        # What to pass and why this call is the next one.
        when: string

Answered
--------

Recorded on 2026-10-05 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 15.0.0-dev, the main core checkout below .checkouts/. Its
console is out of reach: <installation> has no TYPO3 console — none of
bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed —
vendor/autoload.php is not there either, and composer install writes both.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

issue report: a bug drafted from its parts
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "subject": "Styleguide TCA generator hashes every demo password again",
        "problem": "styleguide:generate -c tca hashes the same demo password once per record.",
        "stepsToReproduce": [
            "Install EXT:styleguide",
            "Run @vendor/bin/typo3 styleguide:generate -c tca@"
        ],
        "typo3Version": "14",
        "phpVersion": "8.4",
        "category": "styleguide",
        "isRegression": false,
        "searchFor": "styleguide password"
    }

Text:

.. code-block:: text

    Forge issue draft for the TYPO3 Core project. Nothing was filed: open the link, read the form, submit.

    - Tracker: Bug
    - Subject: Styleguide TCA generator hashes every demo password again
    - TYPO3 Version: 14
    - PHP Version: 8.4
    - Is Regression: No
    - Tags: dev-companion

    Description, which the link fills in:
    ```textile
    h2. Problem

    styleguide:generate -c tca hashes the same demo password once per record.

    h2. Steps to reproduce

    # Install EXT:styleguide
    # Run @vendor/bin/typo3 styleguide:generate -c tca@
    ```

    The form, filled in: https://forge.typo3.org/projects/typo3cms-core/issues/new?issue%5Btracker_id%5D=1&issue%5Bsubject%5D=Styleguide%20TCA%20generator%20hashes%20every%20demo%20password%20again&issue%5Bcustom_field_values%5D%5B4%5D=14&issue%5Bcustom_field_values%5D%5B5%5D=8.4&issue%5Bcustom_field_values%5D%5B15%5D=0&issue%5Bcustom_field_values%5D%5B3%5D=dev-companion&issue%5Bdescription%5D=h2.%20Problem%0A%0Astyleguide%3Agenerate%20-c%20tca%20hashes%20the%20same%20demo%20password%20once%20per%20record.%0A%0Ah2.%20Steps%20to%20reproduce%0A%0A%23%20Install%20EXT%3Astyleguide%0A%23%20Run%20%40vendor%2Fbin%2Ftypo3%20styleguide%3Agenerate%20-c%20tca%40

    Checks:
    - ERROR: The core project has no category "styleguide", so it stays out of the form. typo3_forge_lookup with category in your own words answers the tracker's spelling.
    - WARNING: 7 issues match "styleguide password". Read them before you file this one.

    Possible duplicates:
    - #109716 · Bug · Resolved · Cannot DI PageRepository in blank projects because constructor calls init which demands DB and other framework context · https://forge.typo3.org/issues/109716
    - #108399 · Bug · Closed · Lowlevel Advanced Query module throws PHP deprecation warning in fresh install · https://forge.typo3.org/issues/108399
    - #106283 · Bug · Closed · DataHandler copy for not-nullable passwords fails with SQL error · https://forge.typo3.org/issues/106283
    - #104994 · Task · Needs Feedback · Set proper branch alias for supported release branches (incl. ELTS) · https://forge.typo3.org/issues/104994
    - #102685 · Feature · Under Review · Add PreviewRenderer for plugins · https://forge.typo3.org/issues/102685
    - #99335 · Bug · Closed · Read only password can be overwritten · https://forge.typo3.org/issues/99335
    - #97250 · Bug · Closed · Adjust password field acceptance test · https://forge.typo3.org/issues/97250

    typo3_forge_lookup — with issue and the number of each possible duplicate, and with backlog="newest" and createdSince where the search matched nothing.
    typo3_commit_message_guide — with workflow="core" and the number the tracker gives the issue, for the Resolves: line of the patch that fixes it.

Data:

.. code-block:: json

    {
        "subject": "Styleguide TCA generator hashes every demo password again",
        "tracker": "Bug",
        "description": "h2. Problem\n\nstyleguide:generate -c tca hashes the same demo password once per record.\n\nh2. Steps to reproduce\n\n# Install EXT:styleguide\n# Run @vendor/bin/typo3 styleguide:generate -c tca@",
        "form": [
            {
                "field": "Tracker",
                "value": "Bug"
            },
            {
                "field": "Subject",
                "value": "Styleguide TCA generator hashes every demo password again"
            },
            {
                "field": "TYPO3 Version",
                "value": "14"
            },
            {
                "field": "PHP Version",
                "value": "8.4"
            },
            {
                "field": "Is Regression",
                "value": "No"
            },
            {
                "field": "Tags",
                "value": "dev-companion"
            }
        ],
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues/new?issue%5Btracker_id%5D=1&issue%5Bsubject%5D=Styleguide%20TCA%20generator%20hashes%20every%20demo%20password%20again&issue%5Bcustom_field_values%5D%5B4%5D=14&issue%5Bcustom_field_values%5D%5B5%5D=8.4&issue%5Bcustom_field_values%5D%5B15%5D=0&issue%5Bcustom_field_values%5D%5B3%5D=dev-companion&issue%5Bdescription%5D=h2.%20Problem%0A%0Astyleguide%3Agenerate%20-c%20tca%20hashes%20the%20same%20demo%20password%20once%20per%20record.%0A%0Ah2.%20Steps%20to%20reproduce%0A%0A%23%20Install%20EXT%3Astyleguide%0A%23%20Run%20%40vendor%2Fbin%2Ftypo3%20styleguide%3Agenerate%20-c%20tca%40",
        "descriptionInLink": true,
        "duplicateSearch": {
            "query": "styleguide password",
            "status": "answered",
            "total": 7
        },
        "possibleDuplicates": [
            {
                "issue": 109716,
                "subject": "Cannot DI PageRepository in blank projects because constructor calls init which demands DB and other framework context",
                "tracker": "Bug",
                "status": "Resolved",
                "url": "https://forge.typo3.org/issues/109716"
            },
            {
                "issue": 108399,
                "subject": "Lowlevel Advanced Query module throws PHP deprecation warning in fresh install",
                "tracker": "Bug",
                "status": "Closed",
                "url": "https://forge.typo3.org/issues/108399"
            },
            {
                "issue": 106283,
                "subject": "DataHandler copy for not-nullable passwords fails with SQL error",
                "tracker": "Bug",
                "status": "Closed",
                "url": "https://forge.typo3.org/issues/106283"
            },
            {
                "issue": 104994,
                "subject": "Set proper branch alias for supported release branches (incl. ELTS)",
                "tracker": "Task",
                "status": "Needs Feedback",
                "url": "https://forge.typo3.org/issues/104994"
            },
            {
                "issue": 102685,
                "subject": "Add PreviewRenderer for plugins",
                "tracker": "Feature",
                "status": "Under Review",
                "url": "https://forge.typo3.org/issues/102685"
            },
            {
                "issue": 99335,
                "subject": "Read only password can be overwritten",
                "tracker": "Bug",
                "status": "Closed",
                "url": "https://forge.typo3.org/issues/99335"
            },
            {
                "issue": 97250,
                "subject": "Adjust password field acceptance test",
                "tracker": "Bug",
                "status": "Closed",
                "url": "https://forge.typo3.org/issues/97250"
            }
        ],
        "checks": [
            {
                "level": "error",
                "code": "unknown-category",
                "message": "The core project has no category \"styleguide\", so it stays out of the form. typo3_forge_lookup with category in your own words answers the tracker's spelling."
            },
            {
                "level": "warning",
                "code": "possible-duplicates",
                "message": "7 issues match \"styleguide password\". Read them before you file this one."
            }
        ],
        "nextTools": [
            {
                "tool": "typo3_forge_lookup",
                "when": "with issue and the number of each possible duplicate, and with backlog=\"newest\" and createdSince where the search matched nothing."
            },
            {
                "tool": "typo3_commit_message_guide",
                "when": "with workflow=\"core\" and the number the tracker gives the issue, for the Resolves: line of the patch that fixes it."
            }
        ]
    }

issue report: Markdown where the tracker reads Textile
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "subject": "Something renders wrong",
        "description": "## Problem\n\nThe `Foo::bar()` call fails.\n\n```php\nfoo();\n```"
    }

Text:

.. code-block:: text

    Forge issue draft for the TYPO3 Core project. Nothing was filed: open the link, read the form, submit.

    - Tracker: Bug
    - Subject: Something renders wrong
    - Tags: dev-companion

    Description, which the link fills in:
    ```textile
    ## Problem

    The `Foo::bar()` call fails.

    ```php
    foo();
    ```
    ```

    The form, filled in: https://forge.typo3.org/projects/typo3cms-core/issues/new?issue%5Btracker_id%5D=1&issue%5Bsubject%5D=Something%20renders%20wrong&issue%5Bcustom_field_values%5D%5B3%5D=dev-companion&issue%5Bdescription%5D=%23%23%20Problem%0A%0AThe%20%60Foo%3A%3Abar%28%29%60%20call%20fails.%0A%0A%60%60%60php%0Afoo%28%29%3B%0A%60%60%60

    Checks:
    - ERROR: Three backticks render as three backticks. A code block is <pre><code class="php"> … </code></pre>.
    - WARNING: A backtick renders as a backtick. Inline code is @Foo::bar()@.
    - WARNING: A line that starts with ## is a nested list item in Textile. A heading is h2. or h3.
    - ERROR: A Bug does not go without its TYPO3 Version: the major you saw it on. Pass typo3Version.
    - WARNING: 14 issues match "Something renders wrong". Read them before you file this one.

    Possible duplicates:
    - #110427 · Task · Under Review · Rework the database analyzer stack onto objects · https://forge.typo3.org/issues/110427
    - #109681 · Bug · Needs Feedback · SVG image processing only one path · https://forge.typo3.org/issues/109681
    - #104440 · Bug · Closed · stripPathSitePrefix breaks file path · https://forge.typo3.org/issues/104440
    - #101928 · Bug · Under Review · f:image(.uri) and other FAL/File ViewHelpers should never raise an Exception for missing files! · https://forge.typo3.org/issues/101928
    - #99372 · Bug · Closed · TYPO3 11.5.20 - after upgrade, error message "get_class_methods(): Argument #1 ($object_or_class) must be an object or a valid class name, string given" · https://forge.typo3.org/issues/99372
    - #96830 · Bug · Closed · Forms: Confirmation message Finisher overriding issues · https://forge.typo3.org/issues/96830
    - #96823 · Bug · Needs Feedback · <f:image src="{file-path-to-image}"> with config.absRefPrefix not working in TYPO3 11 · https://forge.typo3.org/issues/96823
    - #94202 · Bug · Closed · show details for BE user (non-admin) leads to exception: An argument "key" or "id" has to be provided · https://forge.typo3.org/issues/94202
    - #93079 · Bug · Closed · Site appearing twice in HMENU Navigation in Workspace Preview · https://forge.typo3.org/issues/93079
    - #86762 · Bug · Closed · Site-Configuration defined language fallbacks not working · https://forge.typo3.org/issues/86762

    typo3_forge_lookup — with issue and the number of each possible duplicate, and with backlog="newest" and createdSince where the search matched nothing.
    typo3_commit_message_guide — with workflow="core" and the number the tracker gives the issue, for the Resolves: line of the patch that fixes it.

Data:

.. code-block:: json

    {
        "subject": "Something renders wrong",
        "tracker": "Bug",
        "description": "## Problem\n\nThe `Foo::bar()` call fails.\n\n```php\nfoo();\n```",
        "form": [
            {
                "field": "Tracker",
                "value": "Bug"
            },
            {
                "field": "Subject",
                "value": "Something renders wrong"
            },
            {
                "field": "Tags",
                "value": "dev-companion"
            }
        ],
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues/new?issue%5Btracker_id%5D=1&issue%5Bsubject%5D=Something%20renders%20wrong&issue%5Bcustom_field_values%5D%5B3%5D=dev-companion&issue%5Bdescription%5D=%23%23%20Problem%0A%0AThe%20%60Foo%3A%3Abar%28%29%60%20call%20fails.%0A%0A%60%60%60php%0Afoo%28%29%3B%0A%60%60%60",
        "descriptionInLink": true,
        "duplicateSearch": {
            "query": "Something renders wrong",
            "status": "answered",
            "total": 14
        },
        "possibleDuplicates": [
            {
                "issue": 110427,
                "subject": "Rework the database analyzer stack onto objects",
                "tracker": "Task",
                "status": "Under Review",
                "url": "https://forge.typo3.org/issues/110427"
            },
            {
                "issue": 109681,
                "subject": "SVG image processing only one path",
                "tracker": "Bug",
                "status": "Needs Feedback",
                "url": "https://forge.typo3.org/issues/109681"
            },
            {
                "issue": 104440,
                "subject": "stripPathSitePrefix breaks file path",
                "tracker": "Bug",
                "status": "Closed",
                "url": "https://forge.typo3.org/issues/104440"
            },
            {
                "issue": 101928,
                "subject": "f:image(.uri) and other FAL/File ViewHelpers should never raise an Exception for missing files!",
                "tracker": "Bug",
                "status": "Under Review",
                "url": "https://forge.typo3.org/issues/101928"
            },
            {
                "issue": 99372,
                "subject": "TYPO3 11.5.20 - after upgrade, error message \"get_class_methods(): Argument #1 ($object_or_class) must be an object or a valid class name, string given\"",
                "tracker": "Bug",
                "status": "Closed",
                "url": "https://forge.typo3.org/issues/99372"
            },
            {
                "issue": 96830,
                "subject": "Forms: Confirmation message Finisher overriding issues",
                "tracker": "Bug",
                "status": "Closed",
                "url": "https://forge.typo3.org/issues/96830"
            },
            {
                "issue": 96823,
                "subject": "<f:image src=\"{file-path-to-image}\"> with config.absRefPrefix not working in TYPO3 11",
                "tracker": "Bug",
                "status": "Needs Feedback",
                "url": "https://forge.typo3.org/issues/96823"
            },
            {
                "issue": 94202,
                "subject": "show details for BE user (non-admin) leads to exception: An argument \"key\" or \"id\" has to be provided",
                "tracker": "Bug",
                "status": "Closed",
                "url": "https://forge.typo3.org/issues/94202"
            },
            {
                "issue": 93079,
                "subject": "Site appearing twice in HMENU Navigation in Workspace Preview",
                "tracker": "Bug",
                "status": "Closed",
                "url": "https://forge.typo3.org/issues/93079"
            },
            {
                "issue": 86762,
                "subject": "Site-Configuration defined language fallbacks not working",
                "tracker": "Bug",
                "status": "Closed",
                "url": "https://forge.typo3.org/issues/86762"
            }
        ],
        "checks": [
            {
                "level": "error",
                "code": "markdown-fence",
                "message": "Three backticks render as three backticks. A code block is <pre><code class=\"php\"> … </code></pre>."
            },
            {
                "level": "warning",
                "code": "markdown-code",
                "message": "A backtick renders as a backtick. Inline code is @Foo::bar()@."
            },
            {
                "level": "warning",
                "code": "markdown-heading",
                "message": "A line that starts with ## is a nested list item in Textile. A heading is h2. or h3."
            },
            {
                "level": "error",
                "code": "missing-typo3-version",
                "message": "A Bug does not go without its TYPO3 Version: the major you saw it on. Pass typo3Version."
            },
            {
                "level": "warning",
                "code": "possible-duplicates",
                "message": "14 issues match \"Something renders wrong\". Read them before you file this one."
            }
        ],
        "nextTools": [
            {
                "tool": "typo3_forge_lookup",
                "when": "with issue and the number of each possible duplicate, and with backlog=\"newest\" and createdSince where the search matched nothing."
            },
            {
                "tool": "typo3_commit_message_guide",
                "when": "with workflow=\"core\" and the number the tracker gives the issue, for the Resolves: line of the patch that fixes it."
            }
        ]
    }
