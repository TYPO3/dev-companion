---
description: >-
  The PHPUnit configuration files a package writes into Build/, whole, and what each one needs after you write it out.
whenToUse: >-
  When a package has no test harness yet, or you have to repair its configuration. The hints below carry the conventions you write the tests themselves by.
hints:
  - project-extension-tests
  - unit-test-doubles
---

# Setting Up PHPUnit in a TYPO3 Extension

The files a package needs to run its own unit and functional tests, and what you
do with each afterwards. No `runTests.sh` exists outside the core repository.
What runs is `vendor/bin/phpunit` against a configuration this package owns.

This page is one step of a larger workflow. Where you set up the harness rather
than repair a configuration, `typo3-extension-testing` orders that work. This is
the file it writes. Where the suite exists and something about it is wrong, this
page answers on its own.

## Build/UnitTests.xml

**Since:** 13

```xml
<?xml version="1.0"?>
<!--
    Copied from typo3/testing-framework, adapted for one extension: the test
    suite points at this package's own tests, and the bootstrap is referenced
    where Composer installed it rather than copied along.
-->
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/11.2/phpunit.xsd"
         backupGlobals="true"
         bootstrap="../vendor/typo3/testing-framework/Resources/Core/Build/UnitTestsBootstrap.php"
         cacheDirectory=".phpunit.cache"
         cacheResult="false"
         colors="true"
         displayDetailsOnTestsThatTriggerDeprecations="true"
         displayDetailsOnTestsThatTriggerErrors="true"
         displayDetailsOnTestsThatTriggerNotices="true"
         displayDetailsOnTestsThatTriggerWarnings="true"
         failOnDeprecation="true"
         failOnNotice="true"
         failOnRisky="true"
         failOnWarning="true"
         requireCoverageMetadata="false"
>
    <testsuites>
        <testsuite name="Unit tests">
            <directory>../Tests/Unit/</directory>
        </testsuite>
    </testsuites>
    <php>
        <ini name="display_errors" value="1"/>
        <env name="TYPO3_CONTEXT" value="Testing"/>
    </php>
</phpunit>
```

## Build/UnitTests.xml

**Until:** 12

```xml
<?xml version="1.0"?>
<!--
    Copied from typo3/testing-framework, adapted for one extension: the test
    suite points at this package's own tests, and the bootstrap is referenced
    where Composer installed it rather than copied along.
-->
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.1/phpunit.xsd"
         backupGlobals="true"
         beStrictAboutTestsThatDoNotTestAnything="false"
         bootstrap="../vendor/typo3/testing-framework/Resources/Core/Build/UnitTestsBootstrap.php"
         cacheDirectory=".phpunit.cache"
         cacheResult="false"
         colors="true"
         displayDetailsOnTestsThatTriggerDeprecations="true"
         displayDetailsOnTestsThatTriggerErrors="true"
         displayDetailsOnTestsThatTriggerNotices="true"
         displayDetailsOnTestsThatTriggerWarnings="true"
         failOnDeprecation="true"
         failOnNotice="true"
         failOnRisky="true"
         failOnWarning="true"
         requireCoverageMetadata="false"
>
    <testsuites>
        <testsuite name="Unit tests">
            <directory>../Tests/Unit/</directory>
        </testsuite>
    </testsuites>
    <php>
        <ini name="display_errors" value="1"/>
        <env name="TYPO3_CONTEXT" value="Testing"/>
    </php>
</phpunit>
```

## Build/FunctionalTests.xml

**Since:** 13

```xml
<?xml version="1.0"?>
<!--
    Copied from typo3/testing-framework, adapted for one extension: the test
    suite points at this package's own tests, and the bootstrap is referenced
    where Composer installed it rather than copied along.
-->
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/11.2/phpunit.xsd"
         backupGlobals="true"
         bootstrap="../vendor/typo3/testing-framework/Resources/Core/Build/FunctionalTestsBootstrap.php"
         cacheDirectory=".phpunit.cache"
         cacheResult="false"
         colors="true"
         displayDetailsOnTestsThatTriggerDeprecations="true"
         displayDetailsOnTestsThatTriggerErrors="true"
         displayDetailsOnTestsThatTriggerNotices="true"
         displayDetailsOnTestsThatTriggerWarnings="true"
         failOnDeprecation="true"
         failOnNotice="true"
         failOnRisky="true"
         failOnWarning="true"
         requireCoverageMetadata="false"
>
    <testsuites>
        <testsuite name="Functional tests">
            <directory>../Tests/Functional/</directory>
        </testsuite>
    </testsuites>
    <php>
        <ini name="display_errors" value="1"/>
        <env name="TYPO3_CONTEXT" value="Testing"/>
    </php>
</phpunit>
```

## Build/FunctionalTests.xml

**Until:** 12

```xml
<?xml version="1.0"?>
<!--
    Copied from typo3/testing-framework, adapted for one extension: the test
    suite points at this package's own tests, and the bootstrap is referenced
    where Composer installed it rather than copied along.
-->
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.1/phpunit.xsd"
         backupGlobals="true"
         beStrictAboutTestsThatDoNotTestAnything="false"
         bootstrap="../vendor/typo3/testing-framework/Resources/Core/Build/FunctionalTestsBootstrap.php"
         cacheDirectory=".phpunit.cache"
         cacheResult="false"
         colors="true"
         displayDetailsOnTestsThatTriggerDeprecations="true"
         displayDetailsOnTestsThatTriggerErrors="true"
         displayDetailsOnTestsThatTriggerNotices="true"
         displayDetailsOnTestsThatTriggerWarnings="true"
         failOnDeprecation="true"
         failOnNotice="true"
         failOnRisky="true"
         failOnWarning="true"
         requireCoverageMetadata="false"
>
    <testsuites>
        <testsuite name="Functional tests">
            <directory>../Tests/Functional/</directory>
        </testsuite>
    </testsuites>
    <php>
        <ini name="display_errors" value="1"/>
        <env name="TYPO3_CONTEXT" value="Testing"/>
    </php>
</phpunit>
```

## What you change in the copied files

`typo3/testing-framework` ships both configurations under
`Resources/Core/Build/`. Their own headers say an extension should copy them
rather than point at them. The testsuite directory they carry resolves into the
core mono repository. A copy has to correct two attributes.

The testsuite directory becomes this package's own `Tests/Unit/` or
`Tests/Functional/`. It is relative to the configuration file. So the path above
holds for a configuration in `Build/`. It is wrong the moment you move the file
somewhere else.

The `bootstrap` attribute points into the installed package instead. The two
bootstraps hold nothing an extension configures — a `Testbase`, `ORIGINAL_ROOT`
and two directories below `typo3temp/var/`. So a copy of them is a file nobody
updates afterwards. The path above assumes `vendor/` beside `Build/`, which is
the ordinary layout of a package installed as the Composer root.

Which of the two variants above belongs to a package is not a choice. The
PHPUnit a `typo3/testing-framework` release admits decides it. A configuration
that names a schema that release does not ship is a validation error before a
test runs.

## Where the Configuration Sits in a Project

Both attributes are relative to the configuration file. So a package you develop
inside a project rather than as the Composer root corrects them again. Two
layouts, and the project decides which:

- The configuration at the project root, in `Build/`, beside `packages/` and
  `vendor/`. The bootstrap stays `../vendor/typo3/testing-framework/…`. The
  testsuite directory becomes `../packages/<extension key>/Tests/Unit/`. One
  configuration then covers every package the repository holds, by a directory
  each.
- The configuration inside the package, in `packages/<extension key>/Build/`.
  The testsuite directory is `../Tests/Unit/` again, and the bootstrap grows:
  `../../../vendor/typo3/testing-framework/…`, because `vendor/` is three
  directories up rather than one.

Neither is more correct. Take the one the repository already uses for its other
checks. What is not a variant is to leave the paths as written above, because
neither mistake is loud. PHPUnit reports a directory that is not there as
`Test directory "…" not found`. It reports a directory with no tests as
`No tests executed!`. It exits 0 on both. Measured on PHPUnit 12.5.34.

## Running the suites

```bash
vendor/bin/phpunit -c Build/UnitTests.xml
vendor/bin/phpunit -c Build/FunctionalTests.xml
```

Declare both as scripts in `composer.json`. Name them after what they do rather
than after the tool behind them. Then the entry point survives a replaced tool.
That is also what `typo3_project_describe` reads back. It is what a colleague or
an agent looks at to find out how to test this package.

## Database credentials for the functional suite

A functional run reads its connection from the environment and fails without it:
`typo3DatabaseDriver`, `typo3DatabaseHost`, `typo3DatabaseName`,
`typo3DatabaseUsername`, `typo3DatabasePassword`. The message it stops with does
not name the variables it lacks. It says that the credentials are neither in the
environment nor in a `LocalConfiguration` file.

They belong in the environment you start the suite in rather than in the
configuration file. Then a checkout runs with no edit and CI overrides them. A
`<php><env>` block in the copied XML is the other place they fit. It commits a
developer's own credentials to the repository.

The account needs the right to create databases. Every test class gets one of
its own: the configured name plus a hash of the class. So under a development
environment that means the administrative user rather than the one the site
itself runs as.

## What a run leaves behind

Nothing drops those per-class databases. The only drop is at the start of that
class's first test, and no class declares a `tearDownAfterClass()`. So a
finished suite leaves one database per test class that ran, and one
`typo3temp/var/tests/functional-<hash>` instance directory beside each.

The test classes bound the set, and it does not grow with every run. The hash
comes from the class name and not from the run. Nothing reads any of it again,
so a drop of all of them costs the next run nothing.

The `_ft<hash>` suffix is the whole of what tells a leftover from the live
database. It exists only under a server driver. Under `pdo_sqlite` the per-class
database is a file below `typo3temp/var/tests/functional-sqlite-dbs/`. Delete
that directory to clean up.
