.. _typo3_project_describe:

``typo3_project_describe``
==========================

*Describe the project*

Describe the repository this server started in and the TYPO3 installation it has
made. It answers the TYPO3 and PHP constraints, with the floor the installed
core requires and how the PHP numbers stand to each other. It says whether the
install is what composer.lock names. It lists the extensions that are its own
rather than TYPO3's, and the sites it configures with the sets each depends on.
It lists the commands composer.json and every package.json declare, each marked
check, change or unknown. It says whether their interpreter clears the PHP bound
the install wrote. It describes the environment it runs in, with its PHP, hooks
and pull recipes, and which Node its npm commands run on. It lists its patched
dependencies and the guides this server carries. Read from files alone — no
console, no database — so it answers on a fresh clone as well. Before composer
install has run, installed says so and four fields wait for it: typo3Version,
corePhpConstraint, installedPhpBound and extensions. Call it first, before you
boot the project and before you recommend or run a check. A check this
repository does not declare does not exist here. The ones marked check are what
a task told not to change files may run. What one of the extensions it lists
registers — its tables, content elements, backend modules and icons — is
typo3_extension_describe. Answers from: packages.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`packages <answer-sources-packages>`.

Takes
-----

Nothing.

Answers with
------------

.. code-block:: yaml

    # Absolute path of the repository this describes. Null when the discovery found
    # no project root to describe.
    root: string or null  # optional
    # core-checkout or composer-project. What the root declares itself to be, not
    # whether anything sits installed in it; installed says that.
    kind: string  # optional
    # Whether the packages this repository declares sit installed below it. False is
    # a clone nobody has run composer install in, which is the state a boot or an
    # installation task starts in. Everything else here comes from the repository's
    # own files and answers either way. What false costs is the four fields that
    # come out of the installed tree: typo3Version, corePhpConstraint and
    # installedPhpBound are null and extensions is empty. None of the four tells you
    # that on its own.
    installed: boolean  # optional
    # Whether what sits installed below the vendor directory is what composer.lock
    # names, package by package and version by version. The one thing installed
    # cannot say. A vendor directory a month older than the lock satisfies that
    # boolean, and the suite run after it fails in classes your change never
    # touched. The modification times are not read: a lock a rebase rewrote is newer
    # than the install it describes, and nothing there is stale. The comparison
    # takes locked dev packages only where the metadata says the install took them,
    # so a --no-dev install is no drift. Where the two differ, the text names the
    # command that reinstalls them. Empty packages where state is anything but
    # differs.
    installedAgainstLock:  # optional
      # One of: matches, differs, not-installed, no-lock. matches: every package
      # composer.lock names sits installed at that version, so a failure here is not
      # a stale install. differs: packages says which of them are not, and the
      # install is behind or ahead of the lock. not-installed: there is a lock and
      # no Composer metadata below the vendor directory to hold it against. So the
      # packages it names are not on disk. no-lock: this root has no composer.lock,
      # so nothing states which versions it fixed.
      state: string
      packages:
        - # The Composer package name, as both files spell it.
          package: string
          # The version composer.lock names. Null where the lock names this package
          # nowhere and it sits installed anyway.
          locked: string or null
          # The version installed below the vendor directory. Null where the lock
          # names it and nothing sits installed under that name.
          installed: string or null
    # The TYPO3 version installed here, read from the core package. Null where
    # nothing sits installed yet, which installed says.
    typo3Version: string or null  # optional
    # What composer.json requires of PHP. What the project declares, not what runs
    # it — see environment.
    phpConstraint: string or null  # optional
    # What it requires of typo3/cms-core.
    coreConstraint: string or null  # optional
    # What the installed typo3/cms-core requires of PHP, out of that package's own
    # composer.json — the lowest a package here may declare it supports. Neither
    # of the other two PHP numbers: not what this project declares, and not what
    # environment.php runs. Not derivable from the TYPO3 major either — v13.4 and
    # v14.3 both require ^8.2, v12.4 requires ^8.1. Null where the tool found no
    # core package to read.
    corePhpConstraint: string or null  # optional
    # The lowest PHP the packages installed below this root accept, read out of
    # composer/platform_check.php below the vendor directory this project declares.
    # The one number here that is not a declaration: composer install writes it over
    # every package it installed, and the autoloader includes it. So an interpreter
    # under it aborts there before any command's own tool starts. The commands list
    # marks what each one does to the sources and says nothing about that. No
    # manifest field carries it, and a fixer required for development alone raises
    # it above everything this project itself declares. Null means no bound:
    # Composer leaves the file out where nothing requires a PHP version and deletes
    # it where platform-check is off. Null is also what nothing installed yet
    # answers, which installed is what says.
    installedPhpBound: string or null  # optional
    # How the four PHP numbers above stand to each other, which none of them says on
    # its own. Derived from the constraints, the bound and the environment as the
    # files spell them. Nothing ran on any of these versions, so this is what the
    # project claims and not evidence that any of it works. Null where phpConstraint
    # names no floor: the project requires no PHP, or spells it in a way this will
    # not claim to read. A constraint it cannot read costs this object rather than
    # buys a wrong relation. installedPhpBound stands on its own either way.
    phpRelation:  # optional
      # The lowest PHP phpConstraint admits, as major.minor. What the project
      # promises to run on, and the number to hold its own commands against.
      floor: string
      # The same, read off corePhpConstraint. Null where the tool found no core
      # package to read one from.
      coreFloor: string or null
      # One of: below, same, above. Where floor sits against coreFloor. below: the
      # project declares support for a PHP its own installed core refuses, so the
      # promise cannot hold. same: it declares what the core requires. above: it
      # declares more than the core needs, which is a range the project narrowed
      # itself and can widen without a change to a dependency. Null where coreFloor
      # is.
      againstCore: string or null
      # One of: below, same, above. Where the PHP environment.php states sits
      # against floor. same: the declared floor is the version the commands run on.
      # above: the environment runs higher, so the floor is a version nothing
      # configured here ever executes — a claim no check tests. below: the
      # environment runs a PHP the project says it does not support. Null where
      # there is no environment or it states no version. The comparison takes the
      # floors alone, so a version over what the constraint's own upper bound allows
      # reads here like one inside it.
      inEnvironment: string or null
      # installedPhpBound as major.minor, which is the depth the environment states
      # its own version at. Null where the install bounds nothing.
      bound: string or null
      # One of: below, same, above. Where the PHP environment.php states sits
      # against bound. It is the only one of these three that says whether a command
      # runs at all rather than what it runs on. below: every command in the list
      # below aborts in Composer's platform check before its own tool starts,
      # whatever runs says about it. The check then has to run somewhere else. same
      # or above: nothing in that file stops them. Null where there is no bound to
      # clear, or no environment that states the version that would clear it. Where
      # this repository configures no environment, the shell you run them in is the
      # interpreter and nothing here reads it.
      environmentAgainstBound: string or null
    # Which Node the npm commands below run on, from the four files that state one.
    # Those are engines.node in a package.json, an .nvmrc beside it, the
    # actions/setup-node steps below .github/workflows/, and the nodejs_version a
    # DDEV project states. The composer half of that command list has its
    # interpreter in environment and the npm half had none. A version difference
    # between the machine and CI is what a build breaks on. The tool reads the first
    # two wherever this repository keeps its manifest. That is the root, or Build/
    # where the frontend build sits one directory down. That is the layout the core
    # has, and enginesIn and nvmrcIn name the file each came from. Null where this
    # repository has no package.json anywhere and nothing states a Node, which is a
    # repository with no npm surface to run.
    node:  # optional
      # What package.json requires of Node in engines.node, as spelled. A range: it
      # says which versions the package admits, never which one a command runs on.
      # Null where no manifest here states one, which is the ordinary case.
      engines: string or null
      # The manifest that stated it, relative to the project root: package.json, or
      # Build/package.json in a repository laid out the way the core is. Null where
      # engines is.
      enginesIn: string or null
      # What the .nvmrc beside it says, as spelled. The closest thing here to what a
      # developer actually runs, because a version manager reads that file and
      # selects it. An alias like lts/iron stays as it is, unresolved. What it names
      # is a list nvm downloads, not anything in this repository. Null where there
      # is no such file.
      nvmrc: string or null
      # The .nvmrc that said it, relative to the project root — .nvmrc, or
      # Build/.nvmrc beside the manifest there. Null where nvmrc is.
      nvmrcIn: string or null
      # The Node the environment states, which for DDEV is nodejs_version. Null is
      # not "none". A project that states none gets the default of the installed
      # DDEV. That default is not in these files and changes from one release to the
      # next. Also null where the environment is not DDEV, or where there is no
      # environment at all.
      environment: string or null
      # Every actions/setup-node step below .github/workflows/, one entry per
      # distinct statement rather than per job. A matrix of five jobs on the same
      # version is one fact. Empty means no workflow here sets Node up, so nothing
      # states which Node CI runs these commands on.
      ci:
        - # The workflow file, relative to the project root.
          workflow: string
          # One of: node-version, node-version-file, none. Which input the step sets
          # Node up by. none: it states neither, so the runner image's own Node is
          # what runs — a version this repository does not decide.
          from: string
          # The value as the workflow writes it, empty where from is none.
          states: string
          # The version that value names outright. Null where it does not. That is a
          # ${{ }} expression, a matrix entry, a file that names it, an lts alias,
          # or a range that takes the newest. Not resolved — the workflow is one
          # file for you to read, and a resolved wrong number would carry this
          # answer's authority.
          version: string or null
      # How those numbers stand to each other, in the same three words phpRelation
      # uses. Null where neither .nvmrc nor engines.node names a version this will
      # read. There is then nothing this repository declares to hold the others
      # against, and the numbers above still stand.
      relation:
        # The Node this repository declares for itself, and what the other two stand
        # against.
        declared: string
        # Which file that came from, relative to the project root — the nvmrcIn or
        # the enginesIn above. The .nvmrc wins where both state one: the pin is what
        # a version manager selects and therefore what a run executes on.
        # engines.node is a range, and only its lowest version could stand in a
        # comparison.
        declaredBy: string
        # One of: below, same, above. Where the pin sits against the lowest version
        # engines.node admits. below: the pinned Node is one this package says it
        # does not run on. Null where either is absent or spelled in a way this will
        # not read.
        nvmrcAgainstEngines: string or null
        # One of: below, same, above. Where the Node the environment states sits
        # against declared. Null where no environment states one.
        inEnvironment: string or null
        # The Node the workflows set up, where they all state the same one. Null
        # where none states a version outright, or where they disagree. Which of
        # them applies is then the workflow's own condition, and ci above carries
        # each statement.
        ci: string or null
        # One of: below, same, above. Where that version sits against declared. The
        # comparison takes the segments both spell, so an .nvmrc with a major and a
        # workflow with a patch level agree wherever the major does. The release
        # difference inside one major is a thing no file here states.
        inCi: string or null
    # The environment this repository configures to run itself in, read from that
    # environment's own files. Null means nothing here configures one that this
    # server reads, and it reads .ddev/config.yaml and TYPO3_DEV_COMPANION_CONSOLE.
    # So the commands below run wherever the caller runs them.
    environment:  # optional
      # One of: ddev, override. ddev: the repository carries a .ddev/config.yaml.
      # override: nothing in the files says so, and TYPO3_DEV_COMPANION_CONSOLE
      # names a command that reaches this installation somewhere other than the
      # caller's own shell.
      via: string
      # The PHP that environment runs, where its files state it. Null is not "none":
      # a DDEV project that states no php_version gets the default of the installed
      # DDEV. An environment named by TYPO3_DEV_COMPANION_CONSOLE states its version
      # nowhere this server can read, and typo3_server_scope reports the version the
      # console actually answers on.
      php: string or null
      # The Node that environment runs, where its files state one — nodejs_version
      # in the .ddev configuration. Null where they state none, and then the
      # installed DDEV's own default applies. The node object above holds it against
      # what this repository declares.
      node: string or null
      # Where this comes from: the .ddev config file that states the version last,
      # or TYPO3_DEV_COMPANION_CONSOLE.
      source: string
      # The DDEV project name, which is what every ddev command takes and what names
      # the containers: ddev-<project>-web and ddev-<project>-db. Where no file
      # states it, DDEV uses the directory name and so does this. Null where the
      # environment is not DDEV.
      project: string or null
      # The hostnames those files declare for the site: <project>.ddev.site, every
      # additional_hostnames entry with the same top-level domain, and every
      # additional_fqdns entry as written. What the configuration declares, not what
      # runs. The ports the router binds and its address on the container network
      # are not in these files, and `ddev describe -j` is what carries them. Empty
      # where the environment is not DDEV.
      hostnames: [string]
      # True when this server already runs inside that environment. Then its shell
      # is that environment and a declared command needs nothing in front of it.
      entered: boolean
      # What this environment runs unasked, from .ddev/config.yaml and every
      # .ddev/config.*.yaml beside it. The commands list is what a caller may run;
      # these fire on their own at the stage each names. An environment that
      # installs dependencies on start and updates the schema on import says so
      # here. Empty means those files declare no hooks. Unmarked, unlike the
      # commands: runs says whether a caller may run something, and a hook is not
      # the caller's to run.
      hooks:
        - # The DDEV stage it fires at: post-start, post-import-db, pre-pull and the
          # rest.
          stage: string
          # What that stage runs, as the file states it. A block of several lines
          # comes with ";" between them, which is what the shell does with it.
          command: string
          # The container it runs in, "web" where the task names none. Null means it
          # runs on the host instead, which is what an exec-host task is.
          service: string or null
      # The pull and push recipes below .ddev/providers/ that this repository wrote,
      # which is where its database and files come from. DDEV writes its own recipes
      # into every project and marks them #ddev-generated. The list leaves those
      # out, because they say what DDEV puts everywhere rather than what this
      # project decided.
      providers:
        - # What to pass: "ddev pull <name>".
          name: string
          # The recipe file, relative to the project root.
          source: string
          # pull, push, or both — which of the two the recipe declares commands
          # for. A recipe with no push commands is one you cannot push upstream
          # with.
          operations: [string]
    # Extensions that are not TYPO3 system extensions. Read from Composer's
    # installed metadata, so where installed is false this is empty because nothing
    # sits installed rather than because the repository has none.
    extensions:  # optional
      - key: string
        # Relative to the project root.
        path: string
        # One of: project, third-party, fixture. project: inside the repository, so
        # what it works on. third-party: installed as a dependency. fixture: shipped
        # by the repository's test setup, below a Tests/ directory, so it exists for
        # a suite to load rather than for development.
        origin: string
        # The files this extension ships that the core no longer reads, or stops to
        # read, each with what the shipped file costs. Read for an extension of
        # origin project alone and empty for the others, whose files are their own
        # maintainer's. The check reads four predicates, each off the extension's
        # own tree: ext_tables.php, ext_emconf.php, ext_icon.svg/.png/.gif and the
        # two ext_typoscript_*.txt. An empty list says none of the four holds, not
        # that the extension is ready for the next major. It reads nothing else it
        # ships for a deprecation, and typo3_changelog_lookup answers that.
        # typo3_extension_describe is the same verdict beside everything else that
        # extension registers.
        deprecatedFiles:
          - # The file, relative to the extension. Not always a registration file:
            # nothing reads ext_icon.* and ext_typoscript_*.txt now, so they are a
            # registration point nowhere and this check alone covers them.
            file: string
            # The changelog entry, for typo3_changelog_lookup, which has the
            # description and the migration whole.
            changelog: string
            # What the entry turns on, which is what holds here. That is the shipped
            # file and what stands beside it: what composer.json declares, or the
            # file the core reads before this one.
            predicate: string
            # What it raises, from which version, and what the removal does instead.
            cost: string
    sites:  # optional
      - identifier: string
        base: string
        rootPageId: integer or null
        # The site sets this site depends on, by their composer-style name.
        sets: [string]
        languages: [string]
    # What this repository declares. A check that is not here does not exist here.
    commands:  # optional
      - # As this repository declares it, run from the project root. Where
        # environment is not null, it runs inside that environment rather than in
        # the caller's shell. An npm script declared below the root carries the
        # --prefix that points npm at the manifest that declares it. So two
        # manifests with a build script are two commands you can tell apart.
        command: string
        # The manifest that declares it, relative to the project root:
        # composer.json, package.json, or Build/package.json where the repository
        # keeps its frontend build one directory down.
        source: string
        # The same command as you run it from where you stand, which is what to
        # paste. Where this repository configures a DDEV project and this server is
        # not already inside it, it is the declared command with DDEV in front. That
        # is "ddev composer <name>" for a composer script and "ddev exec <command>"
        # for the rest. It is the declared command unchanged everywhere else, under
        # TYPO3_DEV_COMPANION_CONSOLE too, which reaches this installation's console
        # rather than an arbitrary script.
        invocation: string
        # The body the manifest declares for it, lines joined with &&.
        declares: string
        # One of: check, change, unknown. What a run does to the sources, read off
        # the body rather than measured by a run. check: it reports and hands the
        # code back as it was, so a task told not to change files can run it. It may
        # still write a cache of its own. change: it rewrites something. unknown:
        # the body does not say, which is what a test suite is, because it runs the
        # project's own code.
        runs: string
        # The command this repository's own contract runs this one through instead,
        # where it has one — the reason it is here rather than the declaration. A
        # core checkout answers it on every npm script, because the core drives them
        # through Build/Scripts/runTests.sh. That script supplies the PHP version
        # and the database service a direct npm call does not, and the core's own
        # AGENTS.md forbids the direct route. Null everywhere else, which means the
        # declared command is the supported route.
        runThrough: string or null
    # Kinds of file this project's own packages ship that no declared command names
    # a checker for — "CSS", "PHP", "Sass", "TypeScript", "XLIFF". It says what is
    # not covered and never what to add: which standards a repository holds itself
    # to are its own. Read off the checkers named in the declared bodies. So a tool
    # this server does not know contributes no coverage, and the list may name a
    # kind that something unrecognised does check. JavaScript is never listed,
    # because a .js a package ships is as often build output or a vendored library
    # as source.
    uncheckedKinds: [string]  # optional
    # Patches from extra.patches. A patched package does not behave as its version
    # says.
    patches:  # optional
      - # The dependency being patched.
        package: string
        # What the patch is for, where composer.json says.
        description: string
        # The patch file, relative to the project root.
        file: string
    # The whole procedures this server carries, named here because this is the call
    # every task starts with. This server also serves them as typo3://guides
    # resources, and a client that lists no resources renders none of them. Four
    # sessions in one week finished without any knowledge that they exist. Each is
    # one typo3_rule_lookup call by documentId, which needs no resource list. A
    # search over sections answers a question and never hands one of these over
    # whole.
    guides:  # optional
      - # What typo3_rule_lookup takes as documentId to return the whole document.
        id: string
        title: string
        # What the caller has to do for this page to be the one to read.
        when: string
        # One of: core, project, extension, any. Which kind of work this page
        # serves. core: a patch to the TYPO3 core repository. project: the site
        # repository around an installation. extension: a package in it. any: all
        # three. It stands here because it decides whether to open the page at all.
        # A caller that reads it out of the id parses a path segment — D-ANS-150.
        scope: string
        # The tool that takes the id above and returns the page whole.
        tool: string
    # One of: packages. packages: the server read the files the installed packages
    # ship, because it could not ask the console. The answer misses the overrides
    # that apply at runtime.
    answeredBy: string  # optional
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
      # One of: installed, not-installed, undeclared. The state of the repository
      # the caller stands in, which the cause does not say. installed: packages sit
      # below the root the discovery found, so the caller lacks no install.
      # not-installed: the repository declares TYPO3 and has no packages below it
      # yet, so this call answers once composer install has run. undeclared: nothing
      # in the directories the discovery walked declares TYPO3, so an install here
      # answers nothing. Null where the discovery looked at nothing: a named root
      # the server could not use, or an entrypoint that handed no directory in.
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

The answer carries exactly one of these sets of fields:

- ``root``, ``installed``, ``installedAgainstLock``, ``phpRelation``, ``node``,
  ``environment``, ``extensions``, ``sites``, ``commands``, ``uncheckedKinds``,
  ``patches``, ``guides``, ``answeredBy``
- ``unsupported``

Answered
--------

Recorded on 2026-09-23 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on the one a client stands in. Neither
fills the whole surface. Answered against core-checkout, TYPO3 14.3.8-dev, the
14.3 core checkout below .checkouts/. Its console is out of reach:
<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3
exists. Its dependencies are not installed — vendor/autoload.php is not there
either, and composer install writes both. Answered against composer-project,
TYPO3 14.3.0, the installation this repository writes below .fixtures/. Its
console answers. The tools that declare ``answeredBy`` carry an answer from
each, under a heading that names which. Every other answer comes from the first
alone, because nothing in it would differ. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and ``bin/cli tools:check`` holds it.

project
~~~~~~~

Called with:

.. code-block:: json

    {}

From the 14.3 core checkout
"""""""""""""""""""""""""""

Text:

.. code-block:: text

    <installation> — core-checkout, TYPO3 14.3.8-dev, PHP ^8.2, and the installed core requires ^8.2 — the lowest a package here may declare

    There is a composer.lock here and no Composer metadata below the vendor directory it declares, so the packages it names are not on disk at all. Run "CI=true ./Build/Scripts/runTests.sh -s composerInstall" before any suite here. What a run reports otherwise is the absent install rather than the code.

    Those PHP numbers, as they stand to each other. This project promises 8.2. The installed core requires 8.2 as well, so the two agree. No environment here states a PHP, so there is nothing to say which of the versions in that range gets run. Nothing here bounds the interpreter — there is no composer/platform_check.php below the vendor directory to read one out of — so no PHP version stops a command below from starting. All of it read from these files. Nothing was executed on any of these versions, and only the floors were compared — a version over what a constraint's own upper bound allows reads here like one inside it.

    Extensions: none beyond TYPO3's own.

    Sites: none configured below config/sites/.

    Commands this repository declares — these exist here, the core's testing suites do not. The core's suites are run by Build/Scripts/runTests.sh, which no manifest here declares. typo3_test_run_guide names the ones a change needs, with the invocation. What each one does to the sources is read off its body, never by running it: a check reports and leaves them as they are, a change rewrites something, and unknown is a body that does not say — a test suite runs the project's own code, and no declaration covers that. A task told not to change files can run the checks and nothing else. A check may still write a cache of its own; what it does not do is hand the code back different.
    Nothing in this repository configures an environment of its own — .ddev/config.yaml and TYPO3_DEV_COMPANION_CONSOLE are what this reads — so these run wherever you run them.
    - composer gerrit:setup (composer.json) — unknown: @gerrit:setup:commitMessageHook:enable && @gerrit:setup:preCommitHook:enable
    - composer gerrit:setup:commitMessageHook:enable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::enableCommitMessageHook
    - composer gerrit:setup:preCommitHook:enable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::enablePreCommitHook
    - composer gerrit:setup:preCommitHook:disable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::disablePreCommitHook
    - npm --prefix Build run build (Build/package.json) — change: ./node_modules/.bin/grunt Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run build-css (Build/package.json) — change: ./node_modules/.bin/grunt css Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run build-js (Build/package.json) — change: ./node_modules/.bin/grunt scripts Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run build-flags (Build/package.json) — change: ./node_modules/.bin/grunt flags-build Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run build-fonts (Build/package.json) — change: ./node_modules/.bin/grunt fonts Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run update (Build/package.json) — change: ./node_modules/.bin/grunt update Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run lint (Build/package.json) — change: ./node_modules/.bin/grunt lint Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run test (Build/package.json) — unknown: wtr Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run playwright:install (Build/package.json) — unknown: playwright install Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run playwright:open (Build/package.json) — unknown: playwright test --ui Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run playwright:run (Build/package.json) — unknown: playwright test Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run playwright:codegen (Build/package.json) — unknown: playwright codegen --ignore-https-errors Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run watch:build (Build/package.json) — change: grunt watch Run it through Build/Scripts/runTests.sh -s <suite>.
    - npm --prefix Build run watch:test (Build/package.json) — unknown: wtr --watch Run it through Build/Scripts/runTests.sh -s <suite>.

    The Node those npm commands run on. This repository declares 24.14, in Build/.nvmrc. Its Build/package.json admits >=24.14.0 <25.0.0, which that pin is the lowest version of. No workflow below .github/workflows/ sets Node up, so nothing here says which one CI runs them on. All of it read from these files. Nothing was run to find it out, and the Node your own shell has is not among them.

    Whole procedures this server carries, each one typo3_rule_lookup with that documentId — no resource list needed, and none of them is answered by a search over sections. Read the one whose sentence names the work you are about to do:
    - any/assets/how-an-asset-reaches-a-page (any) — How a Package's Asset Reaches a Page. After a build wrote different files than it did before — renamed, split, hashed or moved — and before you change where a build writes. It names the route each output file takes and what proves the route still carries. A broken route raises nothing in PHP and shows as a page without its styles.
    - any/backend/using-the-styleguide (any) — Using the Backend Styleguide. Before you write backend markup or borrow a core backend class or icon into a package. It names what the styleguide settles and what it leaves open, so you do not read a demo as a contract for the parts it happens to include.
    - any/icons/drawing-a-content-icon (any) — Drawing a Content Icon, and a Set of Them. When an extension registers content elements or record types of its own and needs icons for them, and when a borrowed core identifier was refused. To register one, or to ask whether an identifier resolves, call typo3_icon_lookup.
    - any/security/reporting-a-vulnerability (any) — Reporting a TYPO3 Vulnerability. When a finding in the TYPO3 core or in an extension is a security defect, before you write anything about it where the public can read it.
    - any/testing/browser-check (any) — Looking at a Change in a Real Browser. When you have to see a defect rather than assert it — a position, a stacking order, something that appears only while the page scrolls — and when a screenshot or a browser session has to run against an installation that already has the content.
    - any/testing/proving-a-condition (any) — Proving a TypoScript Condition Verdict. When you have to show that a TypoScript condition matched in the frontend, or stopped matching — a repair judged before and after, or a template swap that may never have fired. What a condition gets at evaluation time and how an extension registers one are hints instead.
    - any/testing/proving-a-rendering-held (any) — Proving a Rendering Held Across a Change. When a change rewrites how pages render rather than what one page contains — a replaced rendering frame, a dropped dependency the site renders through, a moved template root, a rewritten layout — and you have to show that nothing else moved. To prove one unknown value a checkout does not produce, use the core rendering probe instead. To prove that one TypoScript condition matched, use the condition page.
    - any/writing/the-prose-a-patch-carries (any) — The Prose a Patch Carries. Before you write the comments and docblocks of a patch somebody else reviews, and again before you hand the patch over. It is about the wording rather than about whether the comment is owed at all, which is the codebase's own rule.
    - core/contribution/changelog (core) — The Changelog Entry a Core Patch Owes. When a core change adds, removes, deprecates or announces something an installation notices, and when a review asks for the entry.
    - core/contribution/commit-messages (core) — TYPO3 Core Commit Message Rules. When you write or amend the message of a patch to the core, which is the only repository these rules describe.
    - core/contribution/committed-build-output (core) — The Build Output the Core Commits. When a change touches Build/Sources/TypeScript or Build/Sources/Sass together with the generated file below Resources/Public/ that belongs to it. The question is whether the committed file carries the source change, how you produce it after an edit, or what you do with a backport that came back with conflict markers in it. The checkGruntClean suite answers the first of those and stages the whole tree on the way, so it is no way there from a checkout with work of your own.
    - core/contribution/gerrit-workflow (core) — TYPO3 Gerrit Workflow. When a change is ready to leave the checkout, when you have to read or try out a patch under review locally, or when you have to change a patch already under review — your own or another author's.
    - core/contribution/rebasing-a-stale-patch (core) — Rebasing a Stale Patch. Before you report that a patch rebased onto current code still holds — a change hundreds of commits behind its base, or one that rewrites, moves or deletes a class rather than edits it. typo3-core-patch-checkout says what a conflicting hunk means and where to stop; this page says what survives a rebase that raised no conflict at all.
    - core/contribution/reporting-an-issue (core) — Filing a TYPO3 Core Bug Report. When you write the title and the description of a core bug report, or fill in the new-issue form on forge.typo3.org — the report a patch's Resolves: trailer points at included.
    - core/contribution/rules (core) — TYPO3 Core Contribution Rules. Before you write or review a patch to the TYPO3 core, to know what makes it ready to merge.
    - core/contribution/sources (core) — TYPO3 Contribution Sources. When a question goes past what the bundled documents answer and you have to read the official guide.
    - core/testing/exercising-asset-publishing (core) — Exercising Asset Publishing in a Functional Test. When a functional test has to drive the system resource publisher rather than stub it — to prove that a publishing failure reaches a caller, or that it does not. It holds from TYPO3 14, where the SystemResource namespace arrived. How a package registers its public resources is a hint instead.
    - core/testing/proving-a-rendering (core) — Proving What a Rendering Change Renders. When a finding turns on what a rendering contains and nothing in the checkout produces it, so the value is the unknown rather than the expectation. A TypoScript change whose diff does not say what it renders is one case. A PHP change to the frontend request pipeline, an error handler or a page renderer caller is the same one. To assert a response whose expected value you already know, use the frontend request hint instead.
    - core/testing/scripts (core) — TYPO3 Core Script Help. When you run a suite inside a core checkout. typo3_test_run_guide says which suite a change needs, and it filters them by version.
    - core/testing/timing-a-code-path (core) — Timing a Code Path Between Two Revisions. When a finding turns on whether a change is slower or faster than the code it replaces, and nothing in the checkout measures it. A review that asks about performance is the case. To find out what a rendering contains rather than what it costs, use the rendering probe instead.
    - extension/compatibility/a-declared-major-that-is-not-installed (extension) — Settling an API Question on a Declared Major That Is Not Installed. When the code has to run on more than one declared major and one of them is installed — before you write against an API the installed copy happens to have. It hands you the invocation per symbol: one git call against the branch that is not installed, or that major's released package where no checkout is at hand. Nothing here bundles a per-version list of identifiers, because the branch is what carries the shape.
    - extension/compatibility/running-on-a-declared-major-that-is-not-installed (extension) — Running a Package on a Declared Major That Is Not Installed. When a change has to hold on more than one declared major and the installation supplies one of them — before you write the claim about the other one down. It says what CI already covers, where the second Composer root goes, what it costs the installation, and how you tell a cell that could have failed from one that could not.
    - extension/documentation/manual (extension) — Setting Up an Extension Manual. When an extension has no manual yet, or has one older than guides.xml. The hint below says what a manual is for and where it lives; this page says what goes in the directory.
    - extension/testing/phpunit (extension) — Setting Up PHPUnit in a TYPO3 Extension. When a package has no test harness yet, or you have to repair its configuration. The hints below carry the conventions you write the tests themselves by.
    - project/installation/booting-a-clone (project) — Booting a Clone Into a Running Installation. When you have to bring a repository that declares its own environment up locally and nothing is installed below it yet — a fresh clone, or one whose installation somebody tore down. For a package that declares no procedure, you create an installation instead, which starts a step earlier.
    - project/refactoring/renaming-an-installed-extension (project) — Renaming an Extension That Already Holds Content. When an extension key, a table name, a CType or a vendor prefix changes in a project whose installation already has records — the mirror of booting a clone, where the code moved out from under a database that stayed.
    - project/testing/playwright (project) — Setting Up Playwright in a TYPO3 Project. When a repository that serves a TYPO3 site has no browser suite yet, for what a visitor gets and for what an editor does. A rendering test through a functional test is neither. It runs no script and speaks no HTTP.

Data:

.. code-block:: json

    {
        "root": "<installation>",
        "kind": "core-checkout",
        "installed": true,
        "installedAgainstLock": {
            "state": "not-installed",
            "packages": []
        },
        "typo3Version": "14.3.8-dev",
        "phpConstraint": "^8.2",
        "coreConstraint": null,
        "corePhpConstraint": "^8.2",
        "installedPhpBound": null,
        "phpRelation": {
            "floor": "8.2",
            "coreFloor": "8.2",
            "againstCore": "same",
            "inEnvironment": null,
            "bound": null,
            "environmentAgainstBound": null
        },
        "node": {
            "engines": ">=24.14.0 <25.0.0",
            "enginesIn": "Build/package.json",
            "nvmrc": "v24.14",
            "nvmrcIn": "Build/.nvmrc",
            "environment": null,
            "ci": [],
            "relation": {
                "declared": "24.14",
                "declaredBy": "Build/.nvmrc",
                "nvmrcAgainstEngines": "same",
                "inEnvironment": null,
                "ci": null,
                "inCi": null
            }
        },
        "environment": null,
        "extensions": [],
        "sites": [],
        "commands": [
            {
                "command": "composer gerrit:setup",
                "source": "composer.json",
                "invocation": "composer gerrit:setup",
                "declares": "@gerrit:setup:commitMessageHook:enable && @gerrit:setup:preCommitHook:enable",
                "runs": "unknown",
                "runThrough": null
            },
            {
                "command": "composer gerrit:setup:commitMessageHook:enable",
                "source": "composer.json",
                "invocation": "composer gerrit:setup:commitMessageHook:enable",
                "declares": "TYPO3\\CMS\\Composer\\Scripts\\InstallerScripts::enableCommitMessageHook",
                "runs": "unknown",
                "runThrough": null
            },
            {
                "command": "composer gerrit:setup:preCommitHook:enable",
                "source": "composer.json",
                "invocation": "composer gerrit:setup:preCommitHook:enable",
                "declares": "TYPO3\\CMS\\Composer\\Scripts\\InstallerScripts::enablePreCommitHook",
                "runs": "unknown",
                "runThrough": null
            },
            {
                "command": "composer gerrit:setup:preCommitHook:disable",
                "source": "composer.json",
                "invocation": "composer gerrit:setup:preCommitHook:disable",
                "declares": "TYPO3\\CMS\\Composer\\Scripts\\InstallerScripts::disablePreCommitHook",
                "runs": "unknown",
                "runThrough": null
            },
            {
                "command": "npm --prefix Build run build",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run build",
                "declares": "./node_modules/.bin/grunt",
                "runs": "change",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run build-css",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run build-css",
                "declares": "./node_modules/.bin/grunt css",
                "runs": "change",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run build-js",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run build-js",
                "declares": "./node_modules/.bin/grunt scripts",
                "runs": "change",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run build-flags",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run build-flags",
                "declares": "./node_modules/.bin/grunt flags-build",
                "runs": "change",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run build-fonts",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run build-fonts",
                "declares": "./node_modules/.bin/grunt fonts",
                "runs": "change",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run update",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run update",
                "declares": "./node_modules/.bin/grunt update",
                "runs": "change",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run lint",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run lint",
                "declares": "./node_modules/.bin/grunt lint",
                "runs": "change",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run test",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run test",
                "declares": "wtr",
                "runs": "unknown",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run playwright:install",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run playwright:install",
                "declares": "playwright install",
                "runs": "unknown",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run playwright:open",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run playwright:open",
                "declares": "playwright test --ui",
                "runs": "unknown",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run playwright:run",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run playwright:run",
                "declares": "playwright test",
                "runs": "unknown",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run playwright:codegen",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run playwright:codegen",
                "declares": "playwright codegen --ignore-https-errors",
                "runs": "unknown",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run watch:build",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run watch:build",
                "declares": "grunt watch",
                "runs": "change",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            },
            {
                "command": "npm --prefix Build run watch:test",
                "source": "Build/package.json",
                "invocation": "npm --prefix Build run watch:test",
                "declares": "wtr --watch",
                "runs": "unknown",
                "runThrough": "Build/Scripts/runTests.sh -s <suite>"
            }
        ],
        "uncheckedKinds": [],
        "patches": [],
        "guides": [
            {
                "id": "any/assets/how-an-asset-reaches-a-page",
                "title": "How a Package's Asset Reaches a Page",
                "when": "After a build wrote different files than it did before — renamed, split, hashed or moved — and before you change where a build writes. It names the route each output file takes and what proves the route still carries. A broken route raises nothing in PHP and shows as a page without its styles.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/backend/using-the-styleguide",
                "title": "Using the Backend Styleguide",
                "when": "Before you write backend markup or borrow a core backend class or icon into a package. It names what the styleguide settles and what it leaves open, so you do not read a demo as a contract for the parts it happens to include.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/icons/drawing-a-content-icon",
                "title": "Drawing a Content Icon, and a Set of Them",
                "when": "When an extension registers content elements or record types of its own and needs icons for them, and when a borrowed core identifier was refused. To register one, or to ask whether an identifier resolves, call typo3_icon_lookup.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/security/reporting-a-vulnerability",
                "title": "Reporting a TYPO3 Vulnerability",
                "when": "When a finding in the TYPO3 core or in an extension is a security defect, before you write anything about it where the public can read it.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/testing/browser-check",
                "title": "Looking at a Change in a Real Browser",
                "when": "When you have to see a defect rather than assert it — a position, a stacking order, something that appears only while the page scrolls — and when a screenshot or a browser session has to run against an installation that already has the content.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/testing/proving-a-condition",
                "title": "Proving a TypoScript Condition Verdict",
                "when": "When you have to show that a TypoScript condition matched in the frontend, or stopped matching — a repair judged before and after, or a template swap that may never have fired. What a condition gets at evaluation time and how an extension registers one are hints instead.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/testing/proving-a-rendering-held",
                "title": "Proving a Rendering Held Across a Change",
                "when": "When a change rewrites how pages render rather than what one page contains — a replaced rendering frame, a dropped dependency the site renders through, a moved template root, a rewritten layout — and you have to show that nothing else moved. To prove one unknown value a checkout does not produce, use the core rendering probe instead. To prove that one TypoScript condition matched, use the condition page.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/writing/the-prose-a-patch-carries",
                "title": "The Prose a Patch Carries",
                "when": "Before you write the comments and docblocks of a patch somebody else reviews, and again before you hand the patch over. It is about the wording rather than about whether the comment is owed at all, which is the codebase's own rule.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/changelog",
                "title": "The Changelog Entry a Core Patch Owes",
                "when": "When a core change adds, removes, deprecates or announces something an installation notices, and when a review asks for the entry.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/commit-messages",
                "title": "TYPO3 Core Commit Message Rules",
                "when": "When you write or amend the message of a patch to the core, which is the only repository these rules describe.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/committed-build-output",
                "title": "The Build Output the Core Commits",
                "when": "When a change touches Build/Sources/TypeScript or Build/Sources/Sass together with the generated file below Resources/Public/ that belongs to it. The question is whether the committed file carries the source change, how you produce it after an edit, or what you do with a backport that came back with conflict markers in it. The checkGruntClean suite answers the first of those and stages the whole tree on the way, so it is no way there from a checkout with work of your own.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/gerrit-workflow",
                "title": "TYPO3 Gerrit Workflow",
                "when": "When a change is ready to leave the checkout, when you have to read or try out a patch under review locally, or when you have to change a patch already under review — your own or another author's.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/rebasing-a-stale-patch",
                "title": "Rebasing a Stale Patch",
                "when": "Before you report that a patch rebased onto current code still holds — a change hundreds of commits behind its base, or one that rewrites, moves or deletes a class rather than edits it. typo3-core-patch-checkout says what a conflicting hunk means and where to stop; this page says what survives a rebase that raised no conflict at all.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/reporting-an-issue",
                "title": "Filing a TYPO3 Core Bug Report",
                "when": "When you write the title and the description of a core bug report, or fill in the new-issue form on forge.typo3.org — the report a patch's Resolves: trailer points at included.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/rules",
                "title": "TYPO3 Core Contribution Rules",
                "when": "Before you write or review a patch to the TYPO3 core, to know what makes it ready to merge.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/sources",
                "title": "TYPO3 Contribution Sources",
                "when": "When a question goes past what the bundled documents answer and you have to read the official guide.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/testing/exercising-asset-publishing",
                "title": "Exercising Asset Publishing in a Functional Test",
                "when": "When a functional test has to drive the system resource publisher rather than stub it — to prove that a publishing failure reaches a caller, or that it does not. It holds from TYPO3 14, where the SystemResource namespace arrived. How a package registers its public resources is a hint instead.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/testing/proving-a-rendering",
                "title": "Proving What a Rendering Change Renders",
                "when": "When a finding turns on what a rendering contains and nothing in the checkout produces it, so the value is the unknown rather than the expectation. A TypoScript change whose diff does not say what it renders is one case. A PHP change to the frontend request pipeline, an error handler or a page renderer caller is the same one. To assert a response whose expected value you already know, use the frontend request hint instead.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/testing/scripts",
                "title": "TYPO3 Core Script Help",
                "when": "When you run a suite inside a core checkout. typo3_test_run_guide says which suite a change needs, and it filters them by version.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/testing/timing-a-code-path",
                "title": "Timing a Code Path Between Two Revisions",
                "when": "When a finding turns on whether a change is slower or faster than the code it replaces, and nothing in the checkout measures it. A review that asks about performance is the case. To find out what a rendering contains rather than what it costs, use the rendering probe instead.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "extension/compatibility/a-declared-major-that-is-not-installed",
                "title": "Settling an API Question on a Declared Major That Is Not Installed",
                "when": "When the code has to run on more than one declared major and one of them is installed — before you write against an API the installed copy happens to have. It hands you the invocation per symbol: one git call against the branch that is not installed, or that major's released package where no checkout is at hand. Nothing here bundles a per-version list of identifiers, because the branch is what carries the shape.",
                "scope": "extension",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "extension/compatibility/running-on-a-declared-major-that-is-not-installed",
                "title": "Running a Package on a Declared Major That Is Not Installed",
                "when": "When a change has to hold on more than one declared major and the installation supplies one of them — before you write the claim about the other one down. It says what CI already covers, where the second Composer root goes, what it costs the installation, and how you tell a cell that could have failed from one that could not.",
                "scope": "extension",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "extension/documentation/manual",
                "title": "Setting Up an Extension Manual",
                "when": "When an extension has no manual yet, or has one older than guides.xml. The hint below says what a manual is for and where it lives; this page says what goes in the directory.",
                "scope": "extension",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "extension/testing/phpunit",
                "title": "Setting Up PHPUnit in a TYPO3 Extension",
                "when": "When a package has no test harness yet, or you have to repair its configuration. The hints below carry the conventions you write the tests themselves by.",
                "scope": "extension",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "project/installation/booting-a-clone",
                "title": "Booting a Clone Into a Running Installation",
                "when": "When you have to bring a repository that declares its own environment up locally and nothing is installed below it yet — a fresh clone, or one whose installation somebody tore down. For a package that declares no procedure, you create an installation instead, which starts a step earlier.",
                "scope": "project",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "project/refactoring/renaming-an-installed-extension",
                "title": "Renaming an Extension That Already Holds Content",
                "when": "When an extension key, a table name, a CType or a vendor prefix changes in a project whose installation already has records — the mirror of booting a clone, where the code moved out from under a database that stayed.",
                "scope": "project",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "project/testing/playwright",
                "title": "Setting Up Playwright in a TYPO3 Project",
                "when": "When a repository that serves a TYPO3 site has no browser suite yet, for what a visitor gets and for what an editor does. A rendering test through a functional test is neither. It runs no script and speaks no HTTP.",
                "scope": "project",
                "tool": "typo3_rule_lookup"
            }
        ],
        "answeredBy": "packages"
    }

From the fixture installation
"""""""""""""""""""""""""""""

Text:

.. code-block:: text

    <installation> — composer-project, TYPO3 14.3.0, PHP ^8.2, and the installed core requires ^8.2 — the lowest a package here may declare

    There is no composer.lock here, so nothing states which versions this project fixed and nothing says whether what is installed below the vendor directory is still them.

    Those PHP numbers, as they stand to each other. This project promises 8.2. The installed core requires 8.2 as well, so the two agree. No environment here states a PHP, so there is nothing to say which of the versions in that range gets run. Nothing here bounds the interpreter — there is no composer/platform_check.php below the vendor directory to read one out of — so no PHP version stops a command below from starting. All of it read from these files. Nothing was executed on any of these versions, and only the floors were compared — a version over what a constraint's own upper bound allows reads here like one inside it.

    Extensions that are not TYPO3's own:
    - acme_events (project) — packages/acme_events

    Files core has stopped reading, or is stopping, in the extensions this repository owns:
    - acme_events/ext_tables.php (#109438) — The file is there and this package is not a system extension. Deprecated in v14.3. Loading it raises an E_USER_DEPRECATED, on an uncached request and while the compiled ext_tables cache entry is written; a request served from that cache raises nothing, so a functional suite with failOnDeprecation is usually what surfaces it. From v15.0 nothing reads the file, and a backend module, a route or a user setting registered there is lost without a report.
    Four predicates are checked, each off the extension's own tree: ext_tables.php, ext_emconf.php, ext_icon.svg/.png/.gif, and ext_typoscript_setup.txt beside ext_typoscript_constants.txt. One of them missing above was looked at rather than skipped — the extension ships no such file, or what core reads first stands beside it. It is not an upgrade check: nothing else these extensions ship was read for a deprecation, and typo3_changelog_lookup is what answers that. typo3_extension_describe carries the same verdict beside everything one extension registers.

    Sites, with the sets each one depends on:
    - fixture at https://fixture.example.org/, root page 1, sets: typo3/fluid-styled-content, acme/acme-events

    Commands this repository declares — these exist here, the core's testing suites do not. One of them runs PHPUnit, and its functional half stops before the first assertion where nothing gave it database credentials — an error that reads like a broken suite rather than like a missing setting. typo3_hint_lookup with id=project-extension-tests is what such a run needs: the variables, an account allowed to create one database per test class, and the interpreter it is run on. What each one does to the sources is read off its body, never by running it: a check reports and leaves them as they are, a change rewrites something, and unknown is a body that does not say — a test suite runs the project's own code, and no declaration covers that. A task told not to change files can run the checks and nothing else. A check may still write a cache of its own; what it does not do is hand the code back different.
    Nothing in this repository configures an environment of its own — .ddev/config.yaml and TYPO3_DEV_COMPANION_CONSOLE are what this reads — so these run wherever you run them.
    These packages ship XLIFF and no declared command names a checker for it. That is what is not covered rather than what this repository should add, and it is read off the checkers named in the bodies above — a tool this server does not know contributes no coverage.
    - composer cgl (composer.json) — change: php-cs-fixer fix
    - composer cgl:ci (composer.json) — check: php-cs-fixer fix --dry-run --diff
    - composer test (composer.json) — unknown: phpunit -c Build/phpunit.xml

    Whole procedures this server carries, each one typo3_rule_lookup with that documentId — no resource list needed, and none of them is answered by a search over sections. Read the one whose sentence names the work you are about to do:
    - any/assets/how-an-asset-reaches-a-page (any) — How a Package's Asset Reaches a Page. After a build wrote different files than it did before — renamed, split, hashed or moved — and before you change where a build writes. It names the route each output file takes and what proves the route still carries. A broken route raises nothing in PHP and shows as a page without its styles.
    - any/backend/using-the-styleguide (any) — Using the Backend Styleguide. Before you write backend markup or borrow a core backend class or icon into a package. It names what the styleguide settles and what it leaves open, so you do not read a demo as a contract for the parts it happens to include.
    - any/icons/drawing-a-content-icon (any) — Drawing a Content Icon, and a Set of Them. When an extension registers content elements or record types of its own and needs icons for them, and when a borrowed core identifier was refused. To register one, or to ask whether an identifier resolves, call typo3_icon_lookup.
    - any/security/reporting-a-vulnerability (any) — Reporting a TYPO3 Vulnerability. When a finding in the TYPO3 core or in an extension is a security defect, before you write anything about it where the public can read it.
    - any/testing/browser-check (any) — Looking at a Change in a Real Browser. When you have to see a defect rather than assert it — a position, a stacking order, something that appears only while the page scrolls — and when a screenshot or a browser session has to run against an installation that already has the content.
    - any/testing/proving-a-condition (any) — Proving a TypoScript Condition Verdict. When you have to show that a TypoScript condition matched in the frontend, or stopped matching — a repair judged before and after, or a template swap that may never have fired. What a condition gets at evaluation time and how an extension registers one are hints instead.
    - any/testing/proving-a-rendering-held (any) — Proving a Rendering Held Across a Change. When a change rewrites how pages render rather than what one page contains — a replaced rendering frame, a dropped dependency the site renders through, a moved template root, a rewritten layout — and you have to show that nothing else moved. To prove one unknown value a checkout does not produce, use the core rendering probe instead. To prove that one TypoScript condition matched, use the condition page.
    - any/writing/the-prose-a-patch-carries (any) — The Prose a Patch Carries. Before you write the comments and docblocks of a patch somebody else reviews, and again before you hand the patch over. It is about the wording rather than about whether the comment is owed at all, which is the codebase's own rule.
    - core/contribution/changelog (core) — The Changelog Entry a Core Patch Owes. When a core change adds, removes, deprecates or announces something an installation notices, and when a review asks for the entry.
    - core/contribution/commit-messages (core) — TYPO3 Core Commit Message Rules. When you write or amend the message of a patch to the core, which is the only repository these rules describe.
    - core/contribution/committed-build-output (core) — The Build Output the Core Commits. When a change touches Build/Sources/TypeScript or Build/Sources/Sass together with the generated file below Resources/Public/ that belongs to it. The question is whether the committed file carries the source change, how you produce it after an edit, or what you do with a backport that came back with conflict markers in it. The checkGruntClean suite answers the first of those and stages the whole tree on the way, so it is no way there from a checkout with work of your own.
    - core/contribution/gerrit-workflow (core) — TYPO3 Gerrit Workflow. When a change is ready to leave the checkout, when you have to read or try out a patch under review locally, or when you have to change a patch already under review — your own or another author's.
    - core/contribution/rebasing-a-stale-patch (core) — Rebasing a Stale Patch. Before you report that a patch rebased onto current code still holds — a change hundreds of commits behind its base, or one that rewrites, moves or deletes a class rather than edits it. typo3-core-patch-checkout says what a conflicting hunk means and where to stop; this page says what survives a rebase that raised no conflict at all.
    - core/contribution/reporting-an-issue (core) — Filing a TYPO3 Core Bug Report. When you write the title and the description of a core bug report, or fill in the new-issue form on forge.typo3.org — the report a patch's Resolves: trailer points at included.
    - core/contribution/rules (core) — TYPO3 Core Contribution Rules. Before you write or review a patch to the TYPO3 core, to know what makes it ready to merge.
    - core/contribution/sources (core) — TYPO3 Contribution Sources. When a question goes past what the bundled documents answer and you have to read the official guide.
    - core/testing/exercising-asset-publishing (core) — Exercising Asset Publishing in a Functional Test. When a functional test has to drive the system resource publisher rather than stub it — to prove that a publishing failure reaches a caller, or that it does not. It holds from TYPO3 14, where the SystemResource namespace arrived. How a package registers its public resources is a hint instead.
    - core/testing/proving-a-rendering (core) — Proving What a Rendering Change Renders. When a finding turns on what a rendering contains and nothing in the checkout produces it, so the value is the unknown rather than the expectation. A TypoScript change whose diff does not say what it renders is one case. A PHP change to the frontend request pipeline, an error handler or a page renderer caller is the same one. To assert a response whose expected value you already know, use the frontend request hint instead.
    - core/testing/scripts (core) — TYPO3 Core Script Help. When you run a suite inside a core checkout. typo3_test_run_guide says which suite a change needs, and it filters them by version.
    - core/testing/timing-a-code-path (core) — Timing a Code Path Between Two Revisions. When a finding turns on whether a change is slower or faster than the code it replaces, and nothing in the checkout measures it. A review that asks about performance is the case. To find out what a rendering contains rather than what it costs, use the rendering probe instead.
    - extension/compatibility/a-declared-major-that-is-not-installed (extension) — Settling an API Question on a Declared Major That Is Not Installed. When the code has to run on more than one declared major and one of them is installed — before you write against an API the installed copy happens to have. It hands you the invocation per symbol: one git call against the branch that is not installed, or that major's released package where no checkout is at hand. Nothing here bundles a per-version list of identifiers, because the branch is what carries the shape.
    - extension/compatibility/running-on-a-declared-major-that-is-not-installed (extension) — Running a Package on a Declared Major That Is Not Installed. When a change has to hold on more than one declared major and the installation supplies one of them — before you write the claim about the other one down. It says what CI already covers, where the second Composer root goes, what it costs the installation, and how you tell a cell that could have failed from one that could not.
    - extension/documentation/manual (extension) — Setting Up an Extension Manual. When an extension has no manual yet, or has one older than guides.xml. The hint below says what a manual is for and where it lives; this page says what goes in the directory.
    - extension/testing/phpunit (extension) — Setting Up PHPUnit in a TYPO3 Extension. When a package has no test harness yet, or you have to repair its configuration. The hints below carry the conventions you write the tests themselves by.
    - project/installation/booting-a-clone (project) — Booting a Clone Into a Running Installation. When you have to bring a repository that declares its own environment up locally and nothing is installed below it yet — a fresh clone, or one whose installation somebody tore down. For a package that declares no procedure, you create an installation instead, which starts a step earlier.
    - project/refactoring/renaming-an-installed-extension (project) — Renaming an Extension That Already Holds Content. When an extension key, a table name, a CType or a vendor prefix changes in a project whose installation already has records — the mirror of booting a clone, where the code moved out from under a database that stayed.
    - project/testing/playwright (project) — Setting Up Playwright in a TYPO3 Project. When a repository that serves a TYPO3 site has no browser suite yet, for what a visitor gets and for what an editor does. A rendering test through a functional test is neither. It runs no script and speaks no HTTP.

Data:

.. code-block:: json

    {
        "root": "<installation>",
        "kind": "composer-project",
        "installed": true,
        "installedAgainstLock": {
            "state": "no-lock",
            "packages": []
        },
        "typo3Version": "14.3.0",
        "phpConstraint": "^8.2",
        "coreConstraint": "^14.3",
        "corePhpConstraint": "^8.2",
        "installedPhpBound": null,
        "phpRelation": {
            "floor": "8.2",
            "coreFloor": "8.2",
            "againstCore": "same",
            "inEnvironment": null,
            "bound": null,
            "environmentAgainstBound": null
        },
        "node": null,
        "environment": null,
        "extensions": [
            {
                "key": "acme_events",
                "path": "packages/acme_events",
                "origin": "project",
                "deprecatedFiles": [
                    {
                        "file": "ext_tables.php",
                        "changelog": "#109438",
                        "predicate": "The file is there and this package is not a system extension.",
                        "cost": "Deprecated in v14.3. Loading it raises an E_USER_DEPRECATED, on an uncached request and while the compiled ext_tables cache entry is written; a request served from that cache raises nothing, so a functional suite with failOnDeprecation is usually what surfaces it. From v15.0 nothing reads the file, and a backend module, a route or a user setting registered there is lost without a report."
                    }
                ]
            }
        ],
        "sites": [
            {
                "identifier": "fixture",
                "base": "https://fixture.example.org/",
                "rootPageId": 1,
                "sets": [
                    "typo3/fluid-styled-content",
                    "acme/acme-events"
                ],
                "languages": [
                    "English"
                ]
            }
        ],
        "commands": [
            {
                "command": "composer cgl",
                "source": "composer.json",
                "invocation": "composer cgl",
                "declares": "php-cs-fixer fix",
                "runs": "change",
                "runThrough": null
            },
            {
                "command": "composer cgl:ci",
                "source": "composer.json",
                "invocation": "composer cgl:ci",
                "declares": "php-cs-fixer fix --dry-run --diff",
                "runs": "check",
                "runThrough": null
            },
            {
                "command": "composer test",
                "source": "composer.json",
                "invocation": "composer test",
                "declares": "phpunit -c Build/phpunit.xml",
                "runs": "unknown",
                "runThrough": null
            }
        ],
        "uncheckedKinds": [
            "XLIFF"
        ],
        "patches": [],
        "guides": [
            {
                "id": "any/assets/how-an-asset-reaches-a-page",
                "title": "How a Package's Asset Reaches a Page",
                "when": "After a build wrote different files than it did before — renamed, split, hashed or moved — and before you change where a build writes. It names the route each output file takes and what proves the route still carries. A broken route raises nothing in PHP and shows as a page without its styles.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/backend/using-the-styleguide",
                "title": "Using the Backend Styleguide",
                "when": "Before you write backend markup or borrow a core backend class or icon into a package. It names what the styleguide settles and what it leaves open, so you do not read a demo as a contract for the parts it happens to include.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/icons/drawing-a-content-icon",
                "title": "Drawing a Content Icon, and a Set of Them",
                "when": "When an extension registers content elements or record types of its own and needs icons for them, and when a borrowed core identifier was refused. To register one, or to ask whether an identifier resolves, call typo3_icon_lookup.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/security/reporting-a-vulnerability",
                "title": "Reporting a TYPO3 Vulnerability",
                "when": "When a finding in the TYPO3 core or in an extension is a security defect, before you write anything about it where the public can read it.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/testing/browser-check",
                "title": "Looking at a Change in a Real Browser",
                "when": "When you have to see a defect rather than assert it — a position, a stacking order, something that appears only while the page scrolls — and when a screenshot or a browser session has to run against an installation that already has the content.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/testing/proving-a-condition",
                "title": "Proving a TypoScript Condition Verdict",
                "when": "When you have to show that a TypoScript condition matched in the frontend, or stopped matching — a repair judged before and after, or a template swap that may never have fired. What a condition gets at evaluation time and how an extension registers one are hints instead.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/testing/proving-a-rendering-held",
                "title": "Proving a Rendering Held Across a Change",
                "when": "When a change rewrites how pages render rather than what one page contains — a replaced rendering frame, a dropped dependency the site renders through, a moved template root, a rewritten layout — and you have to show that nothing else moved. To prove one unknown value a checkout does not produce, use the core rendering probe instead. To prove that one TypoScript condition matched, use the condition page.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "any/writing/the-prose-a-patch-carries",
                "title": "The Prose a Patch Carries",
                "when": "Before you write the comments and docblocks of a patch somebody else reviews, and again before you hand the patch over. It is about the wording rather than about whether the comment is owed at all, which is the codebase's own rule.",
                "scope": "any",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/changelog",
                "title": "The Changelog Entry a Core Patch Owes",
                "when": "When a core change adds, removes, deprecates or announces something an installation notices, and when a review asks for the entry.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/commit-messages",
                "title": "TYPO3 Core Commit Message Rules",
                "when": "When you write or amend the message of a patch to the core, which is the only repository these rules describe.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/committed-build-output",
                "title": "The Build Output the Core Commits",
                "when": "When a change touches Build/Sources/TypeScript or Build/Sources/Sass together with the generated file below Resources/Public/ that belongs to it. The question is whether the committed file carries the source change, how you produce it after an edit, or what you do with a backport that came back with conflict markers in it. The checkGruntClean suite answers the first of those and stages the whole tree on the way, so it is no way there from a checkout with work of your own.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/gerrit-workflow",
                "title": "TYPO3 Gerrit Workflow",
                "when": "When a change is ready to leave the checkout, when you have to read or try out a patch under review locally, or when you have to change a patch already under review — your own or another author's.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/rebasing-a-stale-patch",
                "title": "Rebasing a Stale Patch",
                "when": "Before you report that a patch rebased onto current code still holds — a change hundreds of commits behind its base, or one that rewrites, moves or deletes a class rather than edits it. typo3-core-patch-checkout says what a conflicting hunk means and where to stop; this page says what survives a rebase that raised no conflict at all.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/reporting-an-issue",
                "title": "Filing a TYPO3 Core Bug Report",
                "when": "When you write the title and the description of a core bug report, or fill in the new-issue form on forge.typo3.org — the report a patch's Resolves: trailer points at included.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/rules",
                "title": "TYPO3 Core Contribution Rules",
                "when": "Before you write or review a patch to the TYPO3 core, to know what makes it ready to merge.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/contribution/sources",
                "title": "TYPO3 Contribution Sources",
                "when": "When a question goes past what the bundled documents answer and you have to read the official guide.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/testing/exercising-asset-publishing",
                "title": "Exercising Asset Publishing in a Functional Test",
                "when": "When a functional test has to drive the system resource publisher rather than stub it — to prove that a publishing failure reaches a caller, or that it does not. It holds from TYPO3 14, where the SystemResource namespace arrived. How a package registers its public resources is a hint instead.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/testing/proving-a-rendering",
                "title": "Proving What a Rendering Change Renders",
                "when": "When a finding turns on what a rendering contains and nothing in the checkout produces it, so the value is the unknown rather than the expectation. A TypoScript change whose diff does not say what it renders is one case. A PHP change to the frontend request pipeline, an error handler or a page renderer caller is the same one. To assert a response whose expected value you already know, use the frontend request hint instead.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/testing/scripts",
                "title": "TYPO3 Core Script Help",
                "when": "When you run a suite inside a core checkout. typo3_test_run_guide says which suite a change needs, and it filters them by version.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "core/testing/timing-a-code-path",
                "title": "Timing a Code Path Between Two Revisions",
                "when": "When a finding turns on whether a change is slower or faster than the code it replaces, and nothing in the checkout measures it. A review that asks about performance is the case. To find out what a rendering contains rather than what it costs, use the rendering probe instead.",
                "scope": "core",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "extension/compatibility/a-declared-major-that-is-not-installed",
                "title": "Settling an API Question on a Declared Major That Is Not Installed",
                "when": "When the code has to run on more than one declared major and one of them is installed — before you write against an API the installed copy happens to have. It hands you the invocation per symbol: one git call against the branch that is not installed, or that major's released package where no checkout is at hand. Nothing here bundles a per-version list of identifiers, because the branch is what carries the shape.",
                "scope": "extension",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "extension/compatibility/running-on-a-declared-major-that-is-not-installed",
                "title": "Running a Package on a Declared Major That Is Not Installed",
                "when": "When a change has to hold on more than one declared major and the installation supplies one of them — before you write the claim about the other one down. It says what CI already covers, where the second Composer root goes, what it costs the installation, and how you tell a cell that could have failed from one that could not.",
                "scope": "extension",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "extension/documentation/manual",
                "title": "Setting Up an Extension Manual",
                "when": "When an extension has no manual yet, or has one older than guides.xml. The hint below says what a manual is for and where it lives; this page says what goes in the directory.",
                "scope": "extension",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "extension/testing/phpunit",
                "title": "Setting Up PHPUnit in a TYPO3 Extension",
                "when": "When a package has no test harness yet, or you have to repair its configuration. The hints below carry the conventions you write the tests themselves by.",
                "scope": "extension",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "project/installation/booting-a-clone",
                "title": "Booting a Clone Into a Running Installation",
                "when": "When you have to bring a repository that declares its own environment up locally and nothing is installed below it yet — a fresh clone, or one whose installation somebody tore down. For a package that declares no procedure, you create an installation instead, which starts a step earlier.",
                "scope": "project",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "project/refactoring/renaming-an-installed-extension",
                "title": "Renaming an Extension That Already Holds Content",
                "when": "When an extension key, a table name, a CType or a vendor prefix changes in a project whose installation already has records — the mirror of booting a clone, where the code moved out from under a database that stayed.",
                "scope": "project",
                "tool": "typo3_rule_lookup"
            },
            {
                "id": "project/testing/playwright",
                "title": "Setting Up Playwright in a TYPO3 Project",
                "when": "When a repository that serves a TYPO3 site has no browser suite yet, for what a visitor gets and for what an editor does. A rendering test through a functional test is neither. It runs no script and speaks no HTTP.",
                "scope": "project",
                "tool": "typo3_rule_lookup"
            }
        ],
        "answeredBy": "packages"
    }
