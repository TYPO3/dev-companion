:navigation-title: Installation answers

Asking the installation
=======================

This page covers the answers the server reads from the installation the caller
works in. It gives the order in which the server asks for them, and what each
step can see and what it cannot see. It also says what an answer must state when
the files answered where the installation should have.

Three of the answers of this server come from outside it. The installation the
caller works in decides them, and no bundled snapshot can be right about them.

The rules are in `AGENTS.md <../../AGENTS.md>`_. What a change assumed is in
`decisions/discovery/ <../../decisions/discovery/readme.md>`_. This page is the
procedure.

.. diagram::
    :zoomable:
    :caption: Every path runs outside this process; the shortfall travels with the result instead of being hidden.

    units canvas=960 lines=4
      head "what the machine allows"
      key "one square = one entry the registry could return"
    title "A fallback answers less, and says so"
    description "One square per entry the registry could return. The console path and the booted runtime return all of them. The package-file fallback returns every declared entry and none of the ones registered at runtime, and the answer states that."

    row "A console command exists"
      sub "Typo3Cli::run()"
      note "answeredBy: installation"
      parts 88
    row "No command — boot the runtime"
      sub "Typo3Runtime::ask()"
      note "answeredBy: installation"
      parts 88
    row "Failsafe mode, or unreachable"
      sub "package files, read not executed"
      note "answeredBy: packages"
      missing "dynamic registrations, never read"
      parts 68 degraded=20

The order
---------

The server asks three sources, in this order:

1. **The console, where a command exists.** ``Typo3Cli::run()`` calls the
   ``bin/typo3`` of the installation. It goes through DDEV where the project
   runs there. The server uses the console for the registries that TYPO3 has a
   command for. That is ``language:domain:search`` everywhere, and
   ``fluid:namespaces`` on TYPO3 14 and up, where it exists.

2. **The container, where no command exists, or where the command answers less
   than the registry.** ``Typo3Runtime::ask()`` boots the installation in a
   subprocess and reads the registry itself. Only this source knows what a
   package registers at runtime. See :ref:`installation-container-cases`.

3. **The files, where the server can reach neither.** The server parses the
   registration files that the packages ship, and never includes them. This
   source is exact for everything the files declare and silent about everything
   else. An answer from this source says so.

Nothing in the chain runs in this process. Two Composer autoloaders under one
set of class names would stop the whole MCP session, not one answer. A platform
check that fails on a PHP this machine does not have would do the same. A
subprocess brings its own interpreter, and its failure is an exit code.

.. _installation-container-cases:

When the container answers
~~~~~~~~~~~~~~~~~~~~~~~~~~

The container answers in these cases:

* **What a package registers at runtime.** Only the container knows it.

* **The backend modules.** ``debug:backend:modules`` exports neither the
  navigation component a module resolves to nor its routes. The command is TYPO3
  v14 and up, and this server answers for two lines below that (``D-ANS-077``).

* **The effective configuration.** ``configuration:show`` arrived in TYPO3 14.2.
  A console answer would leave 12.4 and 13.4 with the message "command is not
  defined" from the console (``D-ANS-052``).

* **The Fluid namespaces.** ``fluid:namespaces`` arrived in TYPO3 14.2, and so
  did the files the server would fall back to. Below that version, the container
  reads ``SYS/fluid/namespaces``, where those versions keep the registry
  (``D-ANS-136``).

The probe reads two topics only where a caller asks for them, because each one
costs more than any other read needs. The whole of ``TYPO3_CONF_VARS`` is around
50 kB of JSON. The resolution of one ``type=flex`` column runs four events, a
file read and the TCA preparation behind it.

The server puts the arguments of the caller into the payload as one array, the
same way it puts in the autoloader. The server discards a read from before the
ask and takes a new one, so no caller has to ask for its topic first.

The probe
---------

`src/Installation/probe.php <../../src/Installation/probe.php>`_ goes in as
text, never as an include. ``Typo3Runtime`` removes its opening tag and writes
the declared autoloader path of the installation into it. Then it gives the text
to ``Typo3Cli::php()``, which runs it as:

.. code-block:: text

    <interpreter> -r 'eval(base64_decode("<payload>"));'


Three details matter, and a measurement found each of them:

* **The interpreter comes from the resolved console.** Without DDEV, it is the
  PHP that satisfied the platform requirement of the installation. Under DDEV it
  is ``ddev exec -- php``.

  Where ``TYPO3_DEV_COMPANION_CONSOLE`` names the console, the transport stays
  and only the binary changes: ``ddev exec .build/bin/typo3`` becomes
  ``ddev exec php``. Where the server can derive no interpreter, the answer
  gives that as its reason.

* **The payload is base64-encoded.** ``ddev exec`` joins its arguments and gives
  the line to ``bash``. So a payload goes through a shell that nothing here
  controls. The encoded payload has no character that shell could act on. Bash
  expands raw PHP with a ``$`` in it before PHP sees it.

* **The autoloader path is relative.** The two sides of DDEV share no absolute
  path. The subprocess starts in the installation root, and inside the container
  that same root is ``/var/www/html``.

The probe prints one JSON object on stdout and nothing else. First it discards
the output buffer of TYPO3. Otherwise an extension that prints text during boot
would put it in front of the payload.

The three states
----------------

===============  ================================================================  ===================================================================
State            What it means                                                     What the server does with it
===============  ================================================================  ===================================================================
``full``         The container came up with every extension in it                  It is the answer, and the server keeps it for the session
``failsafe``     TYPO3 booted without essential configuration: core packages only  The server never passes it on. The files answer, with the reason
``unreachable``  No console, no interpreter, or the boot failed                    Same — the files answer, with the reason
===============  ================================================================  ===================================================================


**Know the failsafe state.** ``Bootstrap::init()`` turns it on when
``checkIfEssentialConfigurationExists()`` fails. That is the usual condition of
an extension repository: ``composer install`` has run, and there is no
``settings.php`` and no database.

In that state every registry still answers. ``isLoaded()`` still says ``true``
for the extension under work. What comes back is a core-only subset that looks
complete. A measurement against ``georgringer/news`` gave 1259 icons, and not
one of them came from the extension.

The server keeps only a ``full`` read. A caller can read "the DDEV project is
stopped", start the project and ask again. That caller must get the better
answer in the same session. ``Typo3Cli::resolve()`` and ``Instance::describe()``
follow the same rule.

What an answer states
---------------------

Every answer says which source it came from, in one vocabulary. ``answeredBy``
is ``installation`` when the installation answered, and ``packages`` when its
files did.

Where it is ``packages``, the answer also states what that leaves out and why
the server read the files. The answer says so in the text, and in the data too.
A caller that reads only the matches would otherwise skip a line of its own. A
registry that looks complete makes a review report defects that nobody has.

The reason is one half. The other half is which files the fallback could not
read. A tool leaves out an empty section in two cases, and both look the same:

* The file does not exist.

* The file exists and builds its list while it runs.

Only the second case is a loss from the fallback. ``typo3_extension_describe``
lists those files in ``notReadStatically`` and names them in its text. Any other
code that parses a declaration file must make the same distinction for its
callers.

Checking it by hand
-------------------

The suite cannot boot TYPO3, because this repository has no core and never will.
So ``Typo3RuntimeTest`` tests everything around the boot:

* The payload reaches an interpreter and answers as data.

* The autoloader path is the declared one.

* Every state other than ``full`` arrives as a reason.

Check the boot itself by hand against an installation that is set up:

.. code-block:: bash

    php -r '
        require "vendor/autoload.php";
        TYPO3\DevCompanion\Installation\Instance::discoverFrom("/path/to/a/site");
        $answer = TYPO3\DevCompanion\Installation\Typo3Runtime::ask();
        printf("%s %s\n", $answer["state"], $answer["reason"]);
        print_r(array_map("count", $answer["topics"]));
    '


Use a site with an extension that registers at runtime, because only such a site
makes the check worth a run. If the identifiers of that extension are in the
topic, the container answered.
