<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Project;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * What the repository around the installation consists of.
 *
 * The knowledge base describes TYPO3; this describes the project, because a
 * recommendation is only worth as much as its fit: a check that is not declared
 * here does not exist here, whatever the core does with the same name.
 */
final class ProjectDescribe extends ReadOnlyTool
{
    /**
     * What the answer says where nothing has been installed below the root yet.
     *
     * Said rather than left to the three fields that go quiet: `typo3Version:
     * null` is what a root with no readable core answers and `extensions: []`
     * is what a project with no extensions of its own answers, so neither of
     * them tells a caller that the install has not happened (`D-ANS-085`). It
     * is also the state the installation workflow is entered in, which is why
     * the rest of the answer is composed for it rather than withheld.
     */
    private const NOTHING_INSTALLED = 'Nothing is installed below this root yet: no Composer metadata under the '
        . 'vendor directory it declares. So this is what the repository declares and not what is running — the '
        . 'TYPO3 version, the PHP floor the installed core requires, the PHP the install is bounded at and the '
        . 'extension list are read out of the installed tree and arrive once composer install has run. Everything '
        . 'else here is read from the files as they stand.';

    /**
     * How many drifting packages the text names before it says how many it left
     * out. A lock a rebase moved drifts by more packages than a reader can use,
     * and the data beside the text carries every one of them.
     */
    private const LOCK_PACKAGES_LISTED = 10;

    /**
     * What the block of guides opens with, above one line per page.
     *
     * A const because it is also where the answer stops being about this
     * repository: the corpus is the same in every answer and each entry says
     * for itself which kind of checkout it holds for, so a test reading the
     * part that is about the caller's own project splits the text here.
     */
    public const GUIDES_INTRO = 'Whole procedures this server carries, each one typo3_rule_lookup with that '
        . 'documentId — no resource list needed, and none of them is answered by a search over sections. Read the '
        . 'one whose sentence names the work you are about to do:';

    public static function name(): string
    {
        return 'typo3_project_describe';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Packages];
    }

    public static function description(): string
    {
        return 'Describe the repository this server was started in and the TYPO3 installation it has made. It answers: the TYPO3 and PHP constraints, with the floor the installed core requires and how the PHP numbers stand to each other; whether the install is what composer.lock names; the extensions that are its own rather than TYPO3\'s; the sites it configures and the sets each depends on; the commands composer.json and every package.json declare, each marked check, change or unknown, and whether their interpreter clears the PHP bound the install wrote; the environment it runs in, with its PHP, hooks and pull recipes; which Node its npm commands run on; its patched dependencies; and the guides this server carries. Read from files alone — no console, no database — so it answers on a fresh clone as well. Before composer install has run, installed says so and four fields wait for it: typo3Version, corePhpConstraint, installedPhpBound and extensions. Call it first, before booting the project and before recommending or running a check. A check this repository does not declare does not exist here, and the ones marked check are what a task told not to change files may run. What one of the extensions it lists registers — its tables, content elements, backend modules and icons — is typo3_extension_describe.';
    }

    public static function inputSchema(): array
    {
        return self::noArguments();
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'root' => Schema::nullableString('Absolute path of the repository this describes. Null when no project root was found to describe.'),
            'kind' => Schema::string('core-checkout or composer-project. What the root declares itself to be, not whether anything is installed in it — installed says that.'),
            'installed' => ['type' => 'boolean', 'description' => 'Whether the packages this repository declares are installed below it. False is a clone nobody has run composer install in, which is the state a boot or an installation task starts in. Everything else here is read from the repository\'s own files and answers either way. What false costs is the four fields that come out of the installed tree: typo3Version, corePhpConstraint and installedPhpBound are null and extensions is empty. None of the four tells you that on its own.'],
            'installedAgainstLock' => Schema::object([
                'state' => [
                    'type' => 'string',
                    'enum' => [Instance::LOCK_MATCHES, Instance::LOCK_DIFFERS, Instance::LOCK_NOT_INSTALLED, Instance::LOCK_ABSENT],
                    'description' => 'matches: every package composer.lock names is installed at that version, so a failure here is not a stale install. differs: packages says which of them are not, and the install is behind or ahead of the lock. not-installed: there is a lock and no Composer metadata below the vendor directory to hold it against, so the packages it names are not on disk. no-lock: this root has no composer.lock, so nothing states which versions it fixed.',
                ],
                'packages' => Schema::listOf(Schema::object([
                    'package' => Schema::string('The Composer package name, as both files spell it.'),
                    'locked' => Schema::nullableString('The version composer.lock names. Null where the lock names this package nowhere and it is installed anyway.'),
                    'installed' => Schema::nullableString('The version installed below the vendor directory. Null where the lock names it and nothing is installed under that name.'),
                ], ['package', 'locked', 'installed'])),
            ], ['state', 'packages'], 'Whether what is installed below the vendor directory is what composer.lock names, compared package by package and version by version. The one thing installed cannot say. A vendor directory a month older than the lock satisfies that boolean, and the suite run after it fails in classes your change never touched. The modification times are not read: a lock a rebase rewrote is newer than the install it describes, and nothing there is stale. Locked dev packages are compared only where the metadata says the install took them, so a --no-dev install is not reported as a drift. Where the two differ, the text names the command that reinstalls them. Empty packages where state is anything but differs.'),
            'typo3Version' => Schema::nullableString('The TYPO3 version installed here, read from the core package. Null where nothing is installed yet, which installed is what says.'),
            'phpConstraint' => Schema::nullableString('What composer.json requires of PHP. What the project declares, not what runs it — see environment.'),
            'coreConstraint' => Schema::nullableString('What it requires of typo3/cms-core.'),
            'corePhpConstraint' => Schema::nullableString('What the installed typo3/cms-core requires of PHP, out of that package\'s own composer.json — the lowest a package here may declare it supports. Neither of the other two PHP numbers: not what this project declares, and not what environment.php runs. Not derivable from the TYPO3 major either — v13.4 and v14.3 both require ^8.2, v12.4 requires ^8.1. Null where no core package was found to read.'),
            'installedPhpBound' => Schema::nullableString('The lowest PHP the packages installed below this root accept, read out of composer/platform_check.php below the vendor directory this project declares. The one number here that is not a declaration: composer install writes it over every package it installed, and the autoloader includes it. So an interpreter under it aborts there before any command\'s own tool starts, which the commands list, marking what each one does to the sources, says nothing about. No manifest field carries it, and a fixer required for development alone raises it above everything this project itself declares. Null means no bound: Composer leaves the file out where nothing requires a PHP version and deletes it where platform-check is off. Null is also what nothing installed yet answers, which installed is what says.'),
            'phpRelation' => [
                'type' => ['object', 'null'],
                'description' => 'How the four PHP numbers above stand to each other, which none of them says on its own. Derived from the constraints, the bound and the environment as the files spell them. Nothing was executed on any of these versions, so this is what the project claims and not evidence that any of it works. Null where phpConstraint names no floor: the project requires no PHP, or spells it in a way this will not claim to read. A constraint it cannot read costs this object rather than buying a wrong relation. installedPhpBound stands on its own either way.',
                'properties' => [
                    'floor' => Schema::string('The lowest PHP phpConstraint admits, as major.minor. What the project promises to run on, and the number its own commands are worth holding against.'),
                    'coreFloor' => Schema::nullableString('The same, read off corePhpConstraint. Null where no core package was found to read one from.'),
                    'againstCore' => ['type' => ['string', 'null'], 'enum' => [Project::BELOW, Project::SAME, Project::ABOVE, null], 'description' => 'Where floor sits against coreFloor. below: the project declares support for a PHP its own installed core refuses, so the promise cannot be kept. same: it declares what the core requires. above: it declares more than the core needs, which is a range the project narrowed itself and can widen without touching a dependency. Null where coreFloor is.'],
                    'inEnvironment' => ['type' => ['string', 'null'], 'enum' => [Project::BELOW, Project::SAME, Project::ABOVE, null], 'description' => 'Where the PHP environment.php states sits against floor. same: the declared floor is the version the commands are run on. above: the environment runs higher, so the floor is a version nothing configured here ever executes — a claim no check tests. below: the environment runs a PHP the project says it does not support. Null where there is no environment or it states no version. Only the floors are compared, so a version over what the constraint\'s own upper bound allows reads here like one inside it.'],
                    'bound' => Schema::nullableString('installedPhpBound as major.minor, which is the depth the environment states its own version at. Null where the install bounds nothing.'),
                    'environmentAgainstBound' => ['type' => ['string', 'null'], 'enum' => [Project::BELOW, Project::SAME, Project::ABOVE, null], 'description' => 'Where the PHP environment.php states sits against bound — the only one of these three that says whether a command runs at all rather than what it would run on. below: every command in the list below aborts in Composer\'s platform check before its own tool starts, whatever runs says about it. The check then has to be run somewhere else. same or above: nothing in that file stops them. Null where there is no bound to clear, or no environment stating the version that would clear it. Where this repository configures no environment, the shell you run them in is the interpreter and nothing here reads it.'],
                ],
                'required' => ['floor', 'coreFloor', 'againstCore', 'inEnvironment', 'bound', 'environmentAgainstBound'],
            ],
            'node' => [
                'type' => ['object', 'null'],
                'description' => 'Which Node the npm commands below run on, from the four files that state one. Those are engines.node in a package.json, an .nvmrc beside it, the actions/setup-node steps below .github/workflows/, and the nodejs_version a DDEV project states. The composer half of that command list has its interpreter in environment and the npm half had none. A version difference between the machine and CI is what a build breaks on. The first two are read wherever this repository keeps its manifest: at the root, or in Build/ where the frontend build sits one directory down. That is the layout the core has, and enginesIn and nvmrcIn name the file each came from. Null where this repository has no package.json anywhere and nothing states a Node, which is a repository with no npm surface to run.',
                'properties' => [
                    'engines' => Schema::nullableString('What package.json requires of Node in engines.node, as spelled. A range: it says which versions are admitted, never which one a command is executed on. Null where no manifest here states one, which is the ordinary case.'),
                    'enginesIn' => Schema::nullableString('The manifest that stated it, relative to the project root: package.json, or Build/package.json in a repository laid out the way the core is. Null where engines is.'),
                    'nvmrc' => Schema::nullableString('What the .nvmrc beside it says, as spelled. The closest thing here to what a developer actually runs, because a version manager reads that file and selects it. An alias like lts/iron is kept and not resolved — what it names is a list nvm downloads, not anything in this repository. Null where there is no such file.'),
                    'nvmrcIn' => Schema::nullableString('The .nvmrc that said it, relative to the project root — .nvmrc, or Build/.nvmrc beside the manifest there. Null where nvmrc is.'),
                    'environment' => Schema::nullableString('The Node the environment states, which for DDEV is nodejs_version. Null is not "none": a project that states none gets the default of the installed DDEV, which is not in these files and changes from one release to the next. Also null where the environment is not DDEV, or where there is no environment at all.'),
                    'ci' => Schema::listOf(Schema::object([
                        'workflow' => Schema::string('The workflow file, relative to the project root.'),
                        'from' => ['type' => 'string', 'enum' => ['node-version', 'node-version-file', 'none'], 'description' => 'Which input the step sets Node up by. none: it states neither, so the runner image\'s own Node is what runs — a version this repository does not decide.'],
                        'states' => Schema::string('The value as the workflow writes it, empty where from is none.'),
                        'version' => Schema::nullableString('The version that value names outright. Null where it does not: a ${{ }} expression, a matrix entry, a file to read it from, an lts alias, or a range that installs whatever is newest. Not resolved — the workflow is one file for you to read, and a resolved wrong number would carry this answer\'s authority.'),
                    ], ['workflow', 'from', 'states', 'version']), 'Every actions/setup-node step below .github/workflows/, one entry per distinct statement rather than per job — a matrix of five jobs setting up the same version is one fact. Empty means no workflow here sets Node up, so nothing states which Node CI runs these commands on.'),
                    'relation' => [
                        'type' => ['object', 'null'],
                        'description' => 'How those numbers stand to each other, in the same three words phpRelation uses. Null where neither .nvmrc nor engines.node names a version this will read. There is then nothing this repository declares to hold the others against, and the numbers above still stand.',
                        'properties' => [
                            'declared' => Schema::string('The Node this repository declares for itself, and what the other two are held against.'),
                            'declaredBy' => Schema::string('Which file that came from, relative to the project root — the nvmrcIn or the enginesIn above. The .nvmrc wins where both state one: the pin is what a version manager selects and therefore what a run is executed on. engines.node is a range, and only its lowest version could be compared.'),
                            'nvmrcAgainstEngines' => ['type' => ['string', 'null'], 'enum' => [Project::BELOW, Project::SAME, Project::ABOVE, null], 'description' => 'Where the pin sits against the lowest version engines.node admits. below: the pinned Node is one this package says it does not run on. Null where either is absent or spelled in a way this will not read.'],
                            'inEnvironment' => ['type' => ['string', 'null'], 'enum' => [Project::BELOW, Project::SAME, Project::ABOVE, null], 'description' => 'Where the Node the environment states sits against declared. Null where no environment states one.'],
                            'ci' => Schema::nullableString('The Node the workflows set up, where they all state the same one. Null where none states a version outright, or where they disagree — which of them applies is then the workflow\'s own condition, and ci above carries each statement.'),
                            'inCi' => ['type' => ['string', 'null'], 'enum' => [Project::BELOW, Project::SAME, Project::ABOVE, null], 'description' => 'Where that version sits against declared. Only the segments both spell are compared, so an .nvmrc naming a major and a workflow naming a patch level agree wherever the major does. The release difference inside one major is a thing no file here states.'],
                        ],
                        'required' => ['declared', 'declaredBy', 'nvmrcAgainstEngines', 'inEnvironment', 'ci', 'inCi'],
                    ],
                ],
                'required' => ['engines', 'enginesIn', 'nvmrc', 'nvmrcIn', 'environment', 'ci', 'relation'],
            ],
            'environment' => [
                'type' => ['object', 'null'],
                'description' => 'The environment this repository configures to run itself in, read from that environment\'s own files. Null means nothing here configures one that this server reads — .ddev/config.yaml and TYPO3_DEV_COMPANION_CONSOLE are what it reads — so the commands below run wherever the caller runs them.',
                'properties' => [
                    'via' => ['type' => 'string', 'enum' => ['ddev', 'override'], 'description' => 'ddev: the repository carries a .ddev/config.yaml. override: nothing in the files says so, and TYPO3_DEV_COMPANION_CONSOLE names a command that reaches this installation somewhere other than the caller\'s own shell.'],
                    'php' => Schema::nullableString('The PHP that environment runs, where its files state it. Null is not "none": a DDEV project that states no php_version gets the default of the installed DDEV. An environment named by TYPO3_DEV_COMPANION_CONSOLE states its version nowhere this server can read, and typo3_server_scope reports the version the console actually answers on.'),
                    'node' => Schema::nullableString('The Node that environment runs, where its files state one — nodejs_version in the .ddev configuration. Null where they state none, and then the installed DDEV\'s own default applies. The node object above is where it is held against what this repository declares.'),
                    'source' => Schema::string('Where this was read: the .ddev config file that states the version last, or TYPO3_DEV_COMPANION_CONSOLE.'),
                    'project' => Schema::nullableString('The DDEV project name, which is what every ddev command takes and what the containers are named after: ddev-<project>-web and ddev-<project>-db. Where no file states it, DDEV uses the directory name and so does this. Null where the environment is not DDEV.'),
                    'hostnames' => Schema::listOf(Schema::string(), 'The hostnames those files declare the site is served under: <project>.ddev.site, every additional_hostnames entry with the same top-level domain, and every additional_fqdns entry as written. What the configuration declares, not what is running. The ports the router binds and its address on the container network are not in these files, and `ddev describe -j` is what carries them. Empty where the environment is not DDEV.'),
                    'entered' => ['type' => 'boolean', 'description' => 'True when this server is already running inside that environment, so its shell is that environment and a declared command needs nothing in front of it.'],
                    'hooks' => Schema::listOf(Schema::object([
                        'stage' => Schema::string('The DDEV stage it fires at: post-start, post-import-db, pre-pull and the rest.'),
                        'command' => Schema::string('What that stage runs, as the file states it. A block of several lines is joined with ";", which is what the shell does with it.'),
                        'service' => Schema::nullableString('The container it runs in, "web" where the task names none. Null means it runs on the host instead, which is what an exec-host task is.'),
                    ], ['stage', 'command', 'service']), 'What this environment runs without being asked, from .ddev/config.yaml and every .ddev/config.*.yaml beside it. The commands list is what a caller may run; these fire on their own at the stage each names. An environment that installs dependencies on start and updates the schema on import says so here. Empty means those files declare no hooks. Unmarked, unlike the commands: runs says whether a caller may run something, and a hook is not the caller\'s to run.'),
                    'providers' => Schema::listOf(Schema::object([
                        'name' => Schema::string('What to pass: "ddev pull <name>".'),
                        'source' => Schema::string('The recipe file, relative to the project root.'),
                        'operations' => Schema::listOf(Schema::string(), 'pull, push, or both — which of the two the recipe declares commands for. A recipe with no push commands is one you cannot push upstream with.'),
                    ], ['name', 'source', 'operations']), 'The pull and push recipes below .ddev/providers/ that this repository wrote, which is where its database and files come from. DDEV writes its own recipes into every project and marks them #ddev-generated; those are left out, because they say what DDEV puts everywhere rather than what this project decided.'),
                ],
                'required' => ['via', 'php', 'node', 'source', 'project', 'hostnames', 'entered', 'hooks', 'providers'],
            ],
            'extensions' => Schema::listOf(Schema::object([
                'key' => Schema::string(),
                'path' => Schema::string('Relative to the project root.'),
                'origin' => ['type' => 'string', 'enum' => ['project', 'third-party', 'fixture'], 'description' => 'project: inside the repository, so what it is working on. third-party: installed as a dependency. fixture: shipped by the repository\'s test setup, below a Tests/ directory, so it exists to be loaded by a suite rather than developed.'],
                'deprecatedFiles' => Schema::listOf(Schema::deprecatedFile(), 'The files this extension ships that core has stopped reading, or is stopping, each with what shipping it costs. Read for an extension of origin project alone and empty for the others, whose files are their own maintainer\'s. Four predicates are checked, each off the extension\'s own tree: ext_tables.php, ext_emconf.php, ext_icon.svg/.png/.gif and the two ext_typoscript_*.txt. An empty list says none of the four holds, not that the extension is ready for the next major. Nothing else it ships was read for a deprecation, and typo3_changelog_lookup is what answers that. typo3_extension_describe is the same verdict beside everything else that extension registers.'),
            ], ['key', 'path', 'origin', 'deprecatedFiles']), 'Extensions that are not TYPO3 system extensions. Read from Composer\'s installed metadata, so where installed is false this is empty because nothing has been installed rather than because the repository has none.'),
            'sites' => Schema::listOf(Schema::object([
                'identifier' => Schema::string(),
                'base' => Schema::string(),
                'rootPageId' => ['type' => ['integer', 'null']],
                'sets' => Schema::listOf(Schema::string(), 'The site sets this site depends on, by their composer-style name.'),
                'languages' => Schema::listOf(Schema::string()),
            ], ['identifier', 'base', 'rootPageId', 'sets', 'languages'])),
            'commands' => Schema::listOf(Schema::object([
                'command' => Schema::string('As this repository declares it, run from the project root. Where environment is not null, it is run inside that environment rather than in the caller\'s shell. An npm script declared below the root carries the --prefix that points npm at the manifest declaring it. So two manifests with a build script are two commands you can tell apart.'),
                'source' => Schema::string('The manifest declaring it, relative to the project root: composer.json, package.json, or Build/package.json where the repository keeps its frontend build one directory down.'),
                'invocation' => Schema::string('The same command as it is run from where you stand, which is what to paste. Where this repository configures a DDEV project and this server is not already inside it, it is the declared command with DDEV in front. That is "ddev composer <name>" for a composer script and "ddev exec <command>" for the rest. It is the declared command unchanged everywhere else, including under TYPO3_DEV_COMPANION_CONSOLE, which reaches this installation\'s console rather than an arbitrary script.'),
                'declares' => Schema::string('The body the manifest declares for it, lines joined with &&.'),
                'runs' => ['type' => 'string', 'enum' => ['check', 'change', 'unknown'], 'description' => 'What running it does to the sources, read off the body rather than by running it. check: it reports and hands the code back as it was, so a task told not to change files can run it. It may still write a cache of its own. change: it rewrites something. unknown: the body does not say, which is what a test suite is, because it runs the project\'s own code.'],
            ], ['command', 'source', 'invocation', 'declares', 'runs']), 'What this repository declares. A check that is not here does not exist here.'),
            'uncheckedKinds' => Schema::listOf(Schema::string(), 'Kinds of file this project\'s own packages ship that no declared command names a checker for — "CSS", "PHP", "Sass", "TypeScript", "XLIFF". It says what is not covered and never what to add: which standards a repository holds itself to are its own. Read off the checkers named in the declared bodies, so a tool this server does not know contributes no coverage and a kind may be listed that something unrecognised does check. JavaScript is never listed, because a .js a package ships is as often build output or a vendored library as source.'),
            'patches' => Schema::listOf(Schema::object([
                'package' => Schema::string('The dependency being patched.'),
                'description' => Schema::string('What the patch is for, where composer.json says.'),
                'file' => Schema::string('The patch file, relative to the project root.'),
            ], ['package', 'description', 'file']), 'Patches from extra.patches. A patched package does not behave as its version says.'),
            'guides' => Schema::listOf(Schema::guideReference(), 'The whole procedures this server carries, named here because this is the call every task starts with. They are also served as typo3://guides resources, and a client that lists no resources renders none of them — four sessions in one week finished without learning they exist. Each is one typo3_rule_lookup call by documentId, which needs no resource list; a search over sections answers a question and never hands one of these over whole.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
        ], ['root', 'installed', 'installedAgainstLock', 'phpRelation', 'node', 'environment', 'extensions', 'sites', 'commands', 'uncheckedKinds', 'patches', 'guides', 'answeredBy'], []);
    }

    public static function answer(array $args): ToolResult
    {
        $project = Project::describe();
        if ($project === null) {
            return Unsupported::because(
                'no repository declaring TYPO3 was found to describe',
            );
        }

        $lines = [sprintf(
            '%s — %s, TYPO3 %s, PHP %s%s%s',
            $project['root'],
            $project['kind'],
            match (true) {
                !$project['installed'] => 'not installed here yet',
                $project['typo3Version'] === null => 'version unknown',
                default => $project['typo3Version'],
            },
            $project['phpConstraint'] ?? 'unconstrained',
            self::runtime($project['environment']),
            self::floor($project['corePhpConstraint']),
        )];

        if (!$project['installed']) {
            $lines[] = '';
            $lines[] = self::NOTHING_INSTALLED;
        } else {
            foreach (self::lock($project['installedAgainstLock'], $project['kind'], $project['environment']) as $line) {
                $lines[] = $line;
            }
        }

        $relation = self::relation($project['phpRelation'], $project['environment'], $project['installedPhpBound']);
        if ($relation !== '') {
            $lines[] = '';
            $lines[] = $relation;
        }

        $lines[] = '';
        $lines[] = match (true) {
            // The list is Composer's metadata rather than anything the manifest
            // declares, so an empty one here would read as a repository with no
            // extensions where it is a repository with nothing installed.
            !$project['installed'] => 'Extensions: not readable until the install has run — which extensions are '
                . 'here is what Composer wrote, not what composer.json requires.',
            $project['extensions'] === [] => 'Extensions: none beyond TYPO3\'s own.',
            default => 'Extensions that are not TYPO3\'s own:',
        };
        foreach ($project['extensions'] as $extension) {
            $lines[] = sprintf('- %s (%s) — %s', $extension['key'], $extension['origin'], $extension['path']);
        }
        $lines = array_merge($lines, self::deprecations($project['extensions']));

        $lines[] = '';
        $lines[] = $project['sites'] === []
            ? 'Sites: none configured below config/sites/.'
            : 'Sites, with the sets each one depends on:';
        foreach ($project['sites'] as $site) {
            $lines[] = sprintf(
                '- %s%s%s%s',
                $site['identifier'],
                $site['base'] === '' ? '' : ' at ' . $site['base'],
                $site['rootPageId'] === null ? '' : ', root page ' . $site['rootPageId'],
                $site['sets'] === [] ? ', no sets' : ', sets: ' . implode(', ', $site['sets']),
            );
        }

        $lines[] = '';
        $lines[] = $project['commands'] === []
            ? 'This repository declares no commands of its own in composer.json or package.json. What to run is '
                . 'then whatever its CI configuration does.' . self::suites($project['kind'], $project['commands'])
            : 'Commands this repository declares — these exist here, the core\'s testing suites do not.'
                . self::suites($project['kind'], $project['commands'])
                . ' What each one does to the sources is read off its body, never by running it: a check reports '
                . 'and leaves them as they are, a change rewrites something, and unknown is a body that does not '
                . 'say — a test suite runs the project\'s own code, and no declaration covers that. A task told '
                . 'not to change files can run the checks and nothing else. A check may still write a cache of '
                . 'its own; what it does not do is hand the code back different.';
        if ($project['commands'] !== []) {
            $lines[] = self::whereTheyRun($project['environment'], $project['installedPhpBound']);
        }
        // The one thing the list cannot show: what is missing from it. A
        // sitepackage with one stylesheet and no linter for it had to be told
        // so by its owner (`D-ANS-148`).
        if ($project['uncheckedKinds'] !== []) {
            $lines[] = 'These packages ship ' . implode(' and ', $project['uncheckedKinds'])
                . ' and no declared command names a checker for it. That is what is not covered rather than what '
                . 'this repository should add, and it is read off the checkers named in the bodies above — a tool '
                . 'this server does not know contributes no coverage.';
        }
        foreach ($project['commands'] as $command) {
            $lines[] = sprintf(
                '- %s (%s) — %s: %s',
                $command['command'],
                $command['source'],
                $command['runs'],
                $command['declares'],
            );
        }

        foreach (self::node($project['node'], $project['environment']) as $line) {
            $lines[] = $line;
        }

        foreach (self::lifecycle($project['environment']) as $line) {
            $lines[] = $line;
        }

        $site = self::site($project['environment']);
        if ($site !== '') {
            $lines[] = '';
            $lines[] = $site;
        }

        if ($project['patches'] !== []) {
            $lines[] = '';
            $lines[] = 'Patched dependencies — these packages do not behave as their version says, and the next '
                . 'composer update either reapplies the patch or fails on it:';
            foreach ($project['patches'] as $patch) {
                $lines[] = sprintf(
                    '- %s: %s (%s)',
                    $patch['package'],
                    $patch['description'] === '' ? 'no description given' : $patch['description'],
                    $patch['file'],
                );
            }
        }

        $guides = self::guides();
        foreach ($guides['lines'] as $line) {
            $lines[] = $line;
        }

        return ToolResult::create(
            implode("\n", $lines),
            $project + ['guides' => $guides['records'], 'answeredBy' => 'packages'],
        );
    }

    /**
     * The whole procedures this server carries, named where every task starts.
     *
     * They are served as `typo3://guides` resources and a client that lists no
     * resources renders none of them, while `typo3_server_scope` is the call an
     * agent skips precisely when the task looks legible without orientation.
     * This tool is the one the instructions open every task with, so the
     * inventory is here and the detail stays there (`D-ANS-061`). Last in the
     * answer rather than first, because what this tool is called for is the
     * installation.
     *
     * @return array{lines: array<int, string>, records: array<int, array{id: string, title: string, when: string}>}
     */
    private static function guides(): array
    {
        $records = [];
        $lines = ['', self::GUIDES_INTRO];
        foreach (Documents::documents() as $document) {
            $reference = Documents::reference($document);
            $records[] = $reference;
            $lines[] = sprintf(
                '- %s (%s) — %s. %s',
                $reference['id'],
                $reference['scope'],
                $reference['title'],
                $reference['when'],
            );
        }

        return ['lines' => $lines, 'records' => $records];
    }

    /**
     * Which files the repository's own extensions ship that core has stopped
     * reading, volunteered rather than left to a second call.
     *
     * `typo3_extension_describe` carries the same verdict for the extension a
     * caller names, and a session that never makes that call never sees it: the
     * reporting one held the tool's description, complete and in context, and
     * called nothing — `D-ANS-009`, `D-GUI-012`. So it arrives with the
     * orientation, for the extensions inside the repository, which are the ones
     * somebody here can fix.
     *
     * The closing sentence is said whether or not anything fired, which is what
     * `D-ANS-009`'s second **Wrong if** is about: an answer volunteering
     * deprecations is read as a compatibility verdict, and the absence of a
     * signal as a clean bill for the next major.
     *
     * @param array<int, array{key: string, origin: string, deprecatedFiles: array<int, array{file: string, changelog: string, predicate: string, cost: string}>}> $extensions
     * @return array<int, string>
     */
    private static function deprecations(array $extensions): array
    {
        $own = array_values(array_filter(
            $extensions,
            static fn(array $extension): bool => $extension['origin'] === Project::ORIGIN_PROJECT,
        ));
        if ($own === []) {
            return [];
        }

        $lines = ['', 'Files core has stopped reading, or is stopping, in the extensions this repository owns:'];
        $found = false;
        foreach ($own as $extension) {
            foreach ($extension['deprecatedFiles'] as $deprecated) {
                $found = true;
                $lines[] = sprintf(
                    '- %s/%s (%s) — %s %s',
                    $extension['key'],
                    $deprecated['file'],
                    $deprecated['changelog'],
                    $deprecated['predicate'],
                    $deprecated['cost'],
                );
            }
        }
        if (!$found) {
            $lines[] = '- None: not one of the four predicates holds in any of them.';
        }
        $lines[] = 'Four predicates are checked, each off the extension\'s own tree: ext_tables.php, '
            . 'ext_emconf.php, ext_icon.svg/.png/.gif, and ext_typoscript_setup.txt beside '
            . 'ext_typoscript_constants.txt. One of them missing above was looked at rather than skipped — the '
            . 'extension ships no such file, or what core reads first stands beside it. It is not an upgrade '
            . 'check: nothing else these extensions ship was read for a deprecation, and typo3_changelog_lookup '
            . 'is what answers that. typo3_extension_describe carries the same verdict beside everything one '
            . 'extension registers.';

        return $lines;
    }

    /**
     * Where the suites this list says are absent are run, and what the ones it
     * does list need before their first assertion.
     *
     * The core arm came first: the sentence named an absence and nothing that
     * has it, and a session read it and reached for a `Build/bin/phpunit` the
     * checkout does not contain — `D-ANS-031`. Everywhere else there is no
     * runTests.sh to point at and the suites are the repository's own, and the
     * answer said nothing about running them while a session spent eight round
     * trips working it out by hand — `D-ANS-092`.
     *
     * @param array<int, array{command: string, source: string, declares: string, runs: string}> $commands
     */
    private static function suites(string $kind, array $commands): string
    {
        if ($kind === Instance::KIND_CORE_CHECKOUT) {
            return ' The core\'s suites are run by Build/Scripts/runTests.sh, which no manifest here declares. '
                . 'typo3_test_run_guide names the ones a change needs, with the invocation.';
        }

        // PHPUnit is what runs a suite outside the core, so a body that names
        // it is the whole of what this recognises. A repository that runs its
        // tests some other way gets no pointer rather than a guessed one.
        foreach ($commands as $command) {
            if (stripos($command['declares'], 'phpunit') !== false) {
                return ' One of them runs PHPUnit, and its functional half stops before the first assertion where '
                    . 'nothing gave it database credentials — an error that reads like a broken suite rather than '
                    . 'like a missing setting. typo3_hint_lookup with id=project-extension-tests is what such a run '
                    . 'needs: the variables, an account allowed to create one database per test class, and the '
                    . 'interpreter it is run on.';
            }
        }

        return '';
    }

    /**
     * What the opening line says about the second PHP, beside the declared one.
     *
     * Beside rather than instead: the constraint is what the project accepts
     * and the environment is what it gets, and a review that holds the first
     * against the interpreter its own shell happens to have has compared two
     * machines. Empty where there is one machine, so the line an ordinary
     * project answers with does not change.
     *
     * @param array{via: string, php: ?string, node: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
     */
    private static function runtime(?array $environment): string
    {
        if ($environment === null) {
            return '';
        }
        if ($environment['via'] === Typo3Cli::VIA_OVERRIDE) {
            return sprintf(' declared, and run in the environment %s names, whose PHP nothing here states', Typo3Cli::CONSOLE_VARIABLE);
        }
        if ($environment['php'] === null) {
            return sprintf(
                ' declared, and run in DDEV, which %s states no php_version for — so the installed DDEV\'s own default'
                    . ' applies and typo3_server_scope is what reports the version its console answers on',
                $environment['source'],
            );
        }

        return sprintf(
            ' declared and %s in DDEV%s',
            $environment['php'],
            $environment['entered'] ? ', which this server is already inside' : '',
        );
    }

    /**
     * The third PHP number, on the same line as the other two.
     *
     * A task that has to state what PHP a package supports needs the floor the
     * core requires, and the two numbers already here are the wrong ones to take
     * it from: one is what the project accepts and may be absent, the other what
     * the container happens to run (`D-KNW-055`). Here rather than in a section
     * of its own, because it is the line the first call of a workflow is read
     * for, and stated even where it repeats the project's own — a number the
     * answer drops where the two agree cannot be told from one it never read.
     */
    private static function floor(?string $constraint): string
    {
        if ($constraint === null) {
            return '';
        }

        return sprintf(', and the installed core requires %s — the lowest a package here may declare', $constraint);
    }

    /**
     * Whether what is installed is what composer.lock names, which `installed`
     * never said.
     *
     * A vendor directory older than the lock satisfies that boolean, and the
     * suite run after it fails in classes the caller's own change never touched
     * — two sessions from two task shapes spent a full run each attributing
     * those failures (`D-ANS-102`). Said where the two agree as well, for the
     * reason `relation()` states its own numbers: a line the answer drops when
     * nothing is wrong cannot be told from one it never computed, and what a
     * review takes from this is exactly that attribution.
     *
     * Called only where something is installed. Where nothing is, the paragraph
     * above says so and this would say it a second time.
     *
     * @param array{state: string, packages: array<int, array{package: string, locked: ?string, installed: ?string}>} $lock
     * @param array{via: string, php: ?string, node: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
     * @return array<int, string>
     */
    private static function lock(array $lock, string $kind, ?array $environment): array
    {
        if ($lock['state'] === Instance::LOCK_ABSENT) {
            return ['', 'There is no composer.lock here, so nothing states which versions this project fixed and '
                . 'nothing says whether what is installed below the vendor directory is still them.'];
        }

        $command = self::install($kind, $environment);
        if ($lock['state'] === Instance::LOCK_NOT_INSTALLED) {
            return ['', sprintf(
                'There is a composer.lock here and no Composer metadata below the vendor directory it declares, so '
                    . 'the packages it names are not on disk at all. Run "%s" before any suite here. What a run '
                    . 'reports otherwise is the absent install rather than the code.',
                $command,
            )];
        }
        if ($lock['state'] === Instance::LOCK_MATCHES) {
            return ['', 'Every package composer.lock names is installed below the vendor directory at that version, '
                . 'so a failure here is the code and not a stale install.'];
        }

        $lines = ['', sprintf(
            'What is installed below the vendor directory is not what composer.lock names. A suite run here fails in '
                . 'classes your own change never touched, which is the evidence a review is least able to attribute '
                . '— run "%s" first. The versions were compared and the modification times were not. The %d that '
                . 'disagree:',
            $command,
            count($lock['packages']),
        )];
        foreach (array_slice($lock['packages'], 0, self::LOCK_PACKAGES_LISTED) as $package) {
            $lines[] = sprintf('- %s — %s', $package['package'], match (true) {
                $package['installed'] === null => sprintf('locked %s, not installed', (string) $package['locked']),
                $package['locked'] === null => sprintf('installed %s, locked nowhere', $package['installed']),
                default => sprintf('locked %s, installed %s', $package['locked'], $package['installed']),
            });
        }
        if (count($lock['packages']) > self::LOCK_PACKAGES_LISTED) {
            $lines[] = sprintf(
                '- and %d more, left out of this list and carried whole in the data beside it.',
                count($lock['packages']) - self::LOCK_PACKAGES_LISTED,
            );
        }

        return $lines;
    }

    /**
     * The command that makes the install what the lock names.
     *
     * Not one command everywhere: the core installs its dependencies through
     * the script its suites are run by, and a Composer project that configures
     * DDEV installs them in that project rather than in the caller's own shell.
     *
     * @param array{via: string, php: ?string, node: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
     */
    private static function install(string $kind, ?array $environment): string
    {
        if ($kind === Instance::KIND_CORE_CHECKOUT) {
            return 'CI=true ./Build/Scripts/runTests.sh -s composerInstall';
        }

        return $environment !== null && $environment['via'] === Typo3Cli::VIA_DDEV && !$environment['entered']
            ? 'ddev composer install'
            : 'composer install';
    }

    /**
     * How the three numbers on the line above stand to each other.
     *
     * The line states them and states which is which; what it never said is the
     * relation, and the relation is the defect (`D-ANS-082`). Stated even where
     * the three agree, for the reason `floor()` states the core's number where
     * it repeats the project's own: a line the answer drops when nothing is
     * wrong cannot be told from one it never computed.
     *
     * @param array{floor: string, coreFloor: ?string, againstCore: ?string, inEnvironment: ?string, bound: ?string, environmentAgainstBound: ?string}|null $relation
     * @param array{via: string, php: ?string, node: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
     */
    private static function relation(?array $relation, ?array $environment, ?string $bound): string
    {
        if ($relation === null) {
            return '';
        }

        $sentences = [sprintf('Those PHP numbers, as they stand to each other. This project promises %s.', $relation['floor'])];
        $sentences[] = match ($relation['againstCore']) {
            Project::BELOW => sprintf(
                'The installed core requires %s, so that promise cannot be kept here: the core refuses the version '
                    . 'this project says it supports.',
                (string) $relation['coreFloor'],
            ),
            Project::ABOVE => sprintf(
                'The installed core requires %s, so this project asks for more than its dependency needs — a range it '
                    . 'narrowed itself, and one it can widen without touching anything it does not own.',
                (string) $relation['coreFloor'],
            ),
            Project::SAME => sprintf('The installed core requires %s as well, so the two agree.', (string) $relation['coreFloor']),
            default => 'Nothing readable here says what the installed core requires, so there is no second floor to '
                . 'hold that against.',
        };
        $sentences[] = match ($relation['inEnvironment']) {
            Project::SAME => sprintf(
                'The environment runs %s, which is that floor, so it is the version the commands above are actually '
                    . 'executed on.',
                (string) $environment['php'],
            ),
            Project::ABOVE => sprintf(
                'The environment runs %s, above that floor, so nothing configured here ever executes the version this '
                    . 'project promises — it is a claim, and every check passes without testing it.',
                (string) $environment['php'],
            ),
            Project::BELOW => sprintf(
                'The environment runs %s, below that floor, so the commands above are executed on a PHP this project '
                    . 'says it does not support.',
                (string) $environment['php'],
            ),
            default => 'No environment here states a PHP, so there is nothing to say which of the versions in that '
                . 'range gets run.',
        };
        $sentences[] = match (true) {
            // The fourth number, and the only one nobody declared: what the
            // packages below the vendor directory came to require between them
            // (`D-ANS-086`).
            $bound === null => 'Nothing here bounds the interpreter — there is no composer/platform_check.php below '
                . 'the vendor directory to read one out of — so no PHP version stops a command below from starting.',
            $relation['environmentAgainstBound'] === Project::BELOW => sprintf(
                'The install itself is bounded at %s, over the %s the environment runs.',
                $bound,
                (string) $environment['php'],
            ),
            $relation['environmentAgainstBound'] === null => sprintf(
                'The install itself is bounded at %s, which is the one number here nobody declared, and nothing '
                    . 'states the interpreter that would have to clear it.',
                $bound,
            ),
            default => sprintf(
                'The install itself is bounded at %s, which the %s the environment runs clears.',
                $bound,
                (string) $environment['php'],
            ),
        };
        $sentences[] = 'All of it read from these files. Nothing was executed on any of these versions, and only the '
            . 'floors were compared — a version over what a constraint\'s own upper bound allows reads here like one '
            . 'inside it.';

        return implode(' ', $sentences);
    }

    /**
     * Where the commands just listed are run, which the list itself never said.
     *
     * `skills/base.md` sends every task to run the checks this list holds, and
     * a declared `composer test:unit` put on the caller's own shell in a
     * containerised project is a different interpreter from the one the project
     * is built for — which is the finding `feedback/2026-07-31-193611` reported
     * as a version mismatch that blocked nothing.
     *
     * @param array{via: string, php: ?string, node: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
     */
    private static function whereTheyRun(?array $environment, ?string $bound): string
    {
        if ($environment === null) {
            // Said rather than left out. An answer that names no environment
            // reads as "there is none" whether this looked or not, so it says
            // what it looked at and the reader can tell the two apart.
            return 'Nothing in this repository configures an environment of its own — .ddev/config.yaml and '
                . Typo3Cli::CONSOLE_VARIABLE . ' are what this reads — so these run wherever you run them.'
                . self::startable($bound, null, 'the shell you run them in');
        }
        if ($environment['via'] === Typo3Cli::VIA_OVERRIDE) {
            return sprintf(
                'They are run in the environment %s names for this installation, not in the shell you have, and '
                    . 'nothing readable here says which PHP that is — typo3_server_scope names the command it was given.',
                Typo3Cli::CONSOLE_VARIABLE,
            ) . self::startable($bound, null, 'that environment');
        }
        if ($environment['entered']) {
            return 'They are run in the DDEV project this repository configures, and this server is already inside '
                . 'it, so they are run as they are written here.'
                . self::startable($bound, $environment['php'], 'that project');
        }

        return sprintf(
            'They are run in the DDEV project this repository configures, not in the shell you have: "ddev composer '
                . '<name>" for a composer script, "ddev exec <command>" for the rest. Run one directly and it runs on '
                . 'whatever PHP this machine carries%s, which is not what the project is built for.',
            $environment['php'] === null ? '' : ' rather than on ' . $environment['php'],
        ) . self::startable($bound, $environment['php'], 'that project');
    }

    /**
     * Whether the commands about to be listed start on the interpreter that
     * would run them, which what each one does to the sources never said.
     *
     * `R-PRJ-007` marks a command a check a task told not to change files may
     * run, and a session offered one ran into `composer cgl:ci` aborting in the
     * platform check before the fixer started — then went looking for another
     * interpreter and gave the check to CI (`D-ANS-086`). Empty where nothing
     * bounds them: the number and its absence belong among the numbers above,
     * and here there is nothing to warn about.
     */
    private static function startable(?string $bound, ?string $interpreter, string $where): string
    {
        if ($bound === null) {
            return '';
        }

        return match (Project::againstBound($interpreter, $bound)) {
            Project::BELOW => sprintf(
                ' They do not start there: the install is bounded at PHP %s and %s runs %s, so each of them aborts '
                    . 'in composer/platform_check.php before its own tool does anything.',
                $bound,
                $where,
                (string) $interpreter,
            ),
            null => sprintf(
                ' What decides whether they start at all is the PHP %s has, which nothing here reads. The install is '
                    . 'bounded at %s, in the composer/platform_check.php below the vendor directory that every tool '
                    . 'installed there loads with the autoloader, and "php -v" there is what says whether it clears '
                    . 'the bound.',
                $where,
                $bound,
            ),
            default => sprintf(
                ' The install is bounded at PHP %s, which the %s %s runs clears, so nothing in that check stops them.',
                $bound,
                (string) $interpreter,
                $where,
            ),
        };
    }

    /**
     * Which Node the npm commands in that list run on, which nothing beside
     * them said.
     *
     * The PHP relation above is the same sentence for the other interpreter,
     * and the reported defect was this one: a build broke on the machine and CI
     * being different releases of one Node major (`D-SCO-013`). Empty where
     * this repository has no npm surface at all, which is the one case where a
     * silence about Node says something rather than hiding it.
     *
     * @param array{engines: ?string, enginesIn: ?string, nvmrc: ?string, nvmrcIn: ?string, environment: ?string, ci: array<int, array{workflow: string, from: string, states: string, version: ?string}>, relation: array{declared: string, declaredBy: string, nvmrcAgainstEngines: ?string, inEnvironment: ?string, ci: ?string, inCi: ?string}|null}|null $node
     * @param array{via: string, php: ?string, node: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
     * @return array<int, string>
     */
    private static function node(?array $node, ?array $environment): array
    {
        if ($node === null) {
            return [];
        }

        $relation = $node['relation'];
        $sentences = [$relation === null
            ? self::undeclaredNode($node)
            : sprintf(
                'The Node those npm commands run on. This repository declares %s, in %s.',
                $relation['declared'],
                $relation['declaredBy'],
            )];

        if ($relation !== null && $relation['declaredBy'] === $node['nvmrcIn'] && $node['engines'] !== null) {
            $manifest = (string) $node['enginesIn'];
            $sentences[] = match ($relation['nvmrcAgainstEngines']) {
                Project::SAME => sprintf('Its %s admits %s, which that pin is the lowest version of.', $manifest, $node['engines']),
                Project::ABOVE => sprintf('Its %s admits %s, and the pin sits above the lowest version of that.', $manifest, $node['engines']),
                Project::BELOW => sprintf(
                    'Its %s admits %s, which the pin is below — the version a machine here selects is one this '
                        . 'package says it does not run on.',
                    $manifest,
                    $node['engines'],
                ),
                default => sprintf(
                    'Its %s admits %s, in a spelling this will not read a lowest version out of, so the two are not '
                        . 'held against each other.',
                    $manifest,
                    $node['engines'],
                ),
            };
        }

        $sentences[] = match (true) {
            $node['ci'] === [] => 'No workflow below .github/workflows/ sets Node up, so nothing here says which one '
                . 'CI runs them on.',
            $relation === null || $relation['inCi'] === null => 'What its workflows set up is below, as each of them '
                . 'states it.',
            $relation['inCi'] === Project::SAME => sprintf(
                'Its workflows set up %s, which is that version as far as both of them spell it.',
                (string) $relation['ci'],
            ),
            default => sprintf(
                'Its workflows set up %s, %s it.',
                (string) $relation['ci'],
                $relation['inCi'] === Project::ABOVE ? 'above' : 'below',
            ),
        };

        if ($node['environment'] !== null) {
            $sentences[] = match ($relation['inEnvironment'] ?? null) {
                Project::SAME => sprintf('The environment runs %s, which is that version.', $node['environment']),
                Project::ABOVE => sprintf('The environment runs %s, above it.', $node['environment']),
                Project::BELOW => sprintf('The environment runs %s, below it.', $node['environment']),
                default => sprintf('The environment runs %s.', $node['environment']),
            };
        } elseif ($environment !== null && $environment['via'] === Typo3Cli::VIA_DDEV) {
            $sentences[] = 'That DDEV project states no nodejs_version, so the default of the installed DDEV applies '
                . 'and these files do not carry it.';
        }

        $sentences[] = 'All of it read from these files. Nothing was run to find it out, and the Node your own shell '
            . 'has is not among them.';

        $lines = ['', implode(' ', $sentences)];
        foreach ($node['ci'] as $step) {
            $lines[] = sprintf('- %s — %s', $step['workflow'], match (true) {
                $step['from'] === 'none' => 'sets Node up without stating a version, so the runner image\'s own',
                $step['version'] !== null => 'node-version: ' . $step['states'],
                $step['from'] === 'node-version-file' => sprintf(
                    'node-version-file: %s, so the version is whatever that file says',
                    $step['states'],
                ),
                default => sprintf(
                    'node-version: %s, which names no version outright — the workflow is what says which one that is',
                    $step['states'],
                ),
            });
        }

        return $lines;
    }

    /**
     * That no file here declares the Node, and what those files say instead.
     *
     * Two states wear one sentence otherwise: a repository that states nothing,
     * and one that states `lts/iron` or a range whose lowest version this will
     * not claim to read. The second is where the caller has something to open,
     * so each file is named where it sits.
     *
     * @param array{engines: ?string, enginesIn: ?string, nvmrc: ?string, nvmrcIn: ?string, environment: ?string, ci: array<int, array{workflow: string, from: string, states: string, version: ?string}>, relation: array{declared: string, declaredBy: string, nvmrcAgainstEngines: ?string, inEnvironment: ?string, ci: ?string, inCi: ?string}|null} $node
     */
    private static function undeclaredNode(array $node): string
    {
        $stated = [];
        if ($node['nvmrc'] !== null) {
            $stated[] = sprintf('its %s says %s', (string) $node['nvmrcIn'], $node['nvmrc']);
        }
        if ($node['engines'] !== null) {
            $stated[] = sprintf('its %s admits %s', (string) $node['enginesIn'], $node['engines']);
        }

        return $stated === []
            ? 'Nothing here declares which Node those npm commands run on: no package.json here states an '
                . 'engines.node and there is no .nvmrc beside one, so what runs them is whatever node is on the path.'
            : sprintf(
                'Nothing here declares which Node those npm commands run on in a spelling this will read as a '
                    . 'version: %s, and what either of those resolves to is decided outside this repository.',
                implode(', and ', $stated),
            );
    }

    /**
     * What the environment serves, which is not the same question as where the
     * commands run and is answered whether the repository declares any.
     *
     * The running half is named rather than guessed at: bound ports and a
     * container address are not in these files, and `R-DIS-006` is why nothing
     * here starts anything to find out.
     *
     * @param array{via: string, php: ?string, node: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
     */
    private static function site(?array $environment): string
    {
        if ($environment === null || $environment['via'] !== Typo3Cli::VIA_DDEV) {
            return '';
        }

        return sprintf(
            'That project is named %s, so its containers are ddev-%s-web and ddev-%s-db, and its files serve it at '
                . '%s. What they do not carry is what a browser needs beyond the name: the ports the router binds '
                . 'and its address on the container network come from "ddev describe -j" and "docker inspect".',
            (string) $environment['project'],
            (string) $environment['project'],
            (string) $environment['project'],
            implode(', ', $environment['hostnames']),
        );
    }

    /**
     * What the environment runs by itself, which the commands above never
     * covered: those are what a caller may run, these run without being asked.
     *
     * `R-PRJ-009`. Said even where there are none, because an answer that names
     * no hook reads as "there is none" whether this looked or not.
     *
     * @param array{via: string, php: ?string, node: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
     * @return array<int, string>
     */
    private static function lifecycle(?array $environment): array
    {
        if ($environment === null || $environment['via'] !== Typo3Cli::VIA_DDEV) {
            return [];
        }

        $lines = [''];
        $lines[] = $environment['hooks'] === []
            ? 'That DDEV project declares no hooks in .ddev/config.yaml or the config.*.yaml beside it, so the '
                . 'commands above are the whole of what runs here.'
            : 'What that DDEV project runs without being asked. The commands above are what you may run; these fire '
                . 'at the stage each one names, in the order .ddev/config.yaml and the config.*.yaml beside it '
                . 'state them:';
        foreach ($environment['hooks'] as $hook) {
            $lines[] = sprintf(
                '- %s, %s: %s',
                $hook['stage'],
                $hook['service'] === null ? 'on the host' : 'in the ' . $hook['service'] . ' container',
                $hook['command'],
            );
        }

        if ($environment['providers'] !== []) {
            $lines[] = '';
            $lines[] = 'Where its database and files come from — the recipes below .ddev/providers/ this repository '
                . 'wrote. DDEV puts its own into every project and marks them #ddev-generated; those are not this '
                . 'project\'s and are left out:';
            foreach ($environment['providers'] as $provider) {
                $lines[] = sprintf(
                    '- %s (%s)',
                    $provider['operations'] === []
                        ? $provider['name'] . ', which declares neither a pull nor a push command'
                        : implode(', ', array_map(
                            static fn(string $operation): string => 'ddev ' . $operation . ' ' . $provider['name'],
                            $provider['operations'],
                        )),
                    $provider['source'],
                );
            }
        }

        return $lines;
    }
}
