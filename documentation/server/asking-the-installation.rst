Asking the installation
=======================

Three of this server's answers are not its own. They belong to the installation
the caller stands in, and no bundled snapshot could be right about them. This
page is the order the server looks those answers up in, and what each step can
and cannot see. It says what an answer has to say when it came from the wrong
one.

The rules stay in `AGENTS.md <../../AGENTS.md>`_; what a change assumed is in
`decisions/discovery/ <../../decisions/discovery/readme.md>`_. This is the
procedure.

.. image:: ../images/installation-fallback.svg
    :zoomable:
        :alt: One square per entry the registry could return. The console path and
          the booted runtime return all of them. The package-file fallback
          returns every declared entry and none of the ones registered at
          runtime, and the answer states that.

The order
---------

1. **The console, where a command exists.** ``Typo3Cli::run()`` invokes the
   installation's own ``bin/typo3``, through DDEV where the project runs there,
   for the registries TYPO3 exposes a command for. That is
   ``language:domain:search`` everywhere, and ``fluid:namespaces`` on TYPO3 14
   and up, where it exists.
2. **The container, where none does, or where the command answers less than the
   registry.** ``Typo3Runtime::ask()`` boots the installation in a subprocess
   and reads the registry itself. This is the only source that knows what a
   package registers dynamically. The backend modules are the second case.
   ``debug:backend:modules`` exports neither the navigation component a module
   resolves to nor its routes. It is TYPO3 v14 and up while this server answers
   for two lines below that (``D-ANS-077``). The effective configuration is the
   third. ``configuration:show`` arrived in TYPO3 14.2, so a console answer
   would leave 12.4 and 13.4 with the console's own "command is not defined"
   (``D-ANS-052``). The Fluid namespaces are the fourth. ``fluid:namespaces``
   arrived in TYPO3 14.2 and the files it would fall back to arrived with it. So
   below that the container reads ``SYS/fluid/namespaces``, which is where those
   versions keep the registry (``D-ANS-136``).

   The probe reads two topics only where a caller asked for them, because each
   costs what no other read wants. The whole of ``TYPO3_CONF_VARS`` is around 50
   kB of JSON. The resolution of one ``type=flex`` column runs four events, a
   file read and the TCA preparation behind it. What the caller asked them with
   goes into the payload the way the autoloader does, as one array. A read taken
   before goes, so no caller has to ask its topic first.
3. **The files, where the server can reach neither.** The registration files the
   packages ship, parsed and never included. Exact for everything declared,
   silent about everything else — and an answer that came from here says so.

Nothing in the chain runs in this process. Two Composer autoloaders under one
set of class names would take the whole MCP session down instead of one answer.
So would a platform check that fails on a PHP this machine does not have. A
subprocess brings its own interpreter and fails as an exit code.

The probe
---------

`src/Installation/probe.php <../../src/Installation/probe.php>`_ goes in as
text, never as an include. ``Typo3Runtime`` strips its opening tag, writes the
installation's declared autoloader path into it, and hands it to
``Typo3Cli::php()``, which delivers it as:

.. code-block:: text

    <interpreter> -r 'eval(base64_decode("<payload>"));'


Three details are load-bearing, and each of them cost a measurement:

* **The interpreter comes from the resolved console.** Directly it is the PHP
  that satisfied the installation's platform requirement. Under DDEV it is
  ``ddev exec -- php``. Behind a stated ``TYPO3_DEV_COMPANION_CONSOLE`` the
  transport stays and only the binary changes: ``ddev exec .build/bin/typo3``
  becomes ``ddev exec php``. Where the server can derive no interpreter, that is
  the reason the answer carries.
* **The payload is base64-encoded.** ``ddev exec`` joins its arguments and hands
  the line to ``bash``, so a payload passes through a shell nobody controls from
  here. Encoded, it carries no character that shell could act on. Bash expands
  raw PHP with a ``$`` in it before PHP ever sees it.
* **The autoloader path is relative.** The two sides of DDEV share no absolute
  path: the subprocess starts in the installation root, and inside the container
  that same root is ``/var/www/html``.

The probe prints one JSON object on stdout and nothing else. It discards TYPO3's
own output buffer first, because an extension that echoes during boot would
otherwise sit in front of the payload.

The three states
----------------

===============  ================================================================  ===================================================================
State            What it means                                                     What is done with it
===============  ================================================================  ===================================================================
``full``         The container came up with every extension in it                  It is the answer, and it is remembered for the session
``failsafe``     TYPO3 booted without essential configuration: core packages only  Never handed on. The files answer, and the reason travels with them
``unreachable``  No console, no interpreter, or the boot failed                    Same — the files answer, with the reason
===============  ================================================================  ===================================================================


**Failsafe is the state worth knowing.** ``Bootstrap::init()`` turns it on when
``checkIfEssentialConfigurationExists()`` fails, which is the ordinary condition
of an extension repository. ``composer install`` has run, there is no
``settings.php``, and there is no database. Every registry still answers,
``isLoaded()`` still says ``true`` for the extension under work, and what comes
back is a core-only subset that looks complete. Measured against
``georgringer/news``: 1259 icons, not one of them the extension's own.

The server remembers only a ``full`` read. A caller that reads "the DDEV project
is stopped", starts it, and asks again must get the better answer in the same
session. That is the same rule ``Typo3Cli::resolve()`` and
``Instance::describe()`` follow.

What an answer owes
-------------------

Every answer says which source it came from, in one vocabulary: ``answeredBy``
is ``installation`` when the installation itself answered and ``packages`` when
its files did. Where it is ``packages``, the answer also states what that leaves
out and why the read went that way. It says so in the text and not only in the
data. A caller that reads the matches would skip a line of its own. A registry
that reads as complete is what makes a review report defects nobody has.

The reason is half of it. The other half is which files it cost. A section a
tool leaves out because it is empty says the same nothing in two cases. The file
does not exist, or it exists and builds its list while it runs. Only the second
is a casualty of the degradation. ``typo3_extension_describe`` carries those in
``notReadStatically`` and names them in its text; anything else that parses a
declaration file owes its callers the same distinction.

Checking it by hand
-------------------

The suite cannot boot TYPO3, since this repository has no core and never will.
So ``Typo3RuntimeTest`` holds everything around the boot. That the payload
reaches an interpreter and answers as data, and that the autoloader path is the
declared one. That every state that is not ``full`` arrives as a reason. A check
of the boot itself runs by hand against a set-up installation:

.. code-block:: bash

    php -r '
        require "vendor/autoload.php";
        TYPO3\DevCompanion\Installation\Instance::discoverFrom("/path/to/a/site");
        $answer = TYPO3\DevCompanion\Installation\Typo3Runtime::ask();
        printf("%s %s\n", $answer["state"], $answer["reason"]);
        print_r(array_map("count", $answer["topics"]));
    '


A site with an extension that registers dynamically is what makes the check
worth running: if its identifiers are in the topic, the container answered.
