---
date: 2026-09-09T18:07:25+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_configuration_lookup
directory: /home/benji/projects/typo3-cms
---

# Functional test instances publish assets by symlink, not by the Testing context default

## Observation

Task: prove, with the real resource publisher rather than a test double, that a publishing failure raised inside PackageSetup::setup() never reaches the caller (Gerrit change 95482, TYPO3 15.0-dev / backported to 14.3).

I needed a genuine PackageAssetsPublishingFailedException from DefaultSystemResourcePublisher inside a functional test. I reasoned it out from the code and got it wrong twice:

1. PublishingConfiguration resolves 'auto' to 'link' when Environment::getContext()->isDevelopment(), else 'mirror'. Functional tests run TYPO3_CONTEXT=Testing, which is not Development, so I concluded MirrorPublisher was active and provoked its failure path — realpath(source) === realpath(target) — by pre-creating a symlink at the publishing target. Result: no message at all, not even an error.
2. A throwaway debug test printed the truth: the functional test instance sets $GLOBALS['TYPO3_CONF_VARS']['SYS']['SystemResources']['filesystemPublishingType'] = 'link' explicitly, so 'auto' never applies. The container holds JunctionPublisher, MirrorPublisher and SymlinkPublisher, and only SymlinkPublisher returns canPublish() = true. My pre-created symlink was therefore read by SymlinkPublisher::isSymlinked() as an already-published state — a success, silently.
3. A plain regular file at the publishing target finally produced the real ERROR: 'Could not publish public resources for extension "typo3/app" by using the "symlink" strategy.'

Three rounds and two throwaway functional tests to learn one configuration value.

A second fact from the same debug run, equally undocumented and equally load-bearing: ResourcePublishingContext marks a resource isSourcePublic when packagePath+relativePath starts with Environment::getPublicPath(). In a functional test instance projectPath == publicPath, so all three default app resources (_assets, uploads, typo3temp/assets) are skipped and publishing for the VirtualAppPackage is a complete no-op — unless the package path is pointed outside the instance. Anyone trying to test app-package publishing will otherwise conclude, wrongly, that the code does nothing.

## Query

How do I make DefaultSystemResourcePublisher::publishResources() actually publish, and actually fail, inside a TYPO3 functional test? Which FileSystemPublisher is active there, and why is publishing a no-op for the VirtualAppPackage?

## Suggestion

A page answering "how do I exercise asset publishing in a functional test", carrying three facts: (1) the test instance pins SYS/SystemResources/filesystemPublishingType to 'link', so SymlinkPublisher is the active strategy regardless of what 'auto' would resolve to in the Testing context; (2) to provoke a failure, put a regular file at the publishing target — a pre-existing symlink pointing at the source is read as success, and the mirror strategy's own failure path is unreachable there; (3) publishing is a no-op for any package whose path lies under the public path, which in a functional instance is everything, because projectPath == publicPath.

More generally: "which TYPO3_CONF_VARS the functional test instance pins, and to what" would be a useful page in its own right. I could only find this one by dumping $GLOBALS from inside a test.
