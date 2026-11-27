# Settings the environment generates

Read this when the local environment generates settings into the installation.
The case is an installation on a database the environment does not provide, or
on no database at all.

## What goes wrong

Two files set the same settings: the file the environment generates, and the
installation's own. `typo3_hint_lookup` with `id=project-configuration-files`
says where the line between them runs.

The generator knows only the services it provides itself. So it merges its own
database settings over what the install wrote. The installation then cannot
connect.

## What to do

1. Take over the generated file, as the environment documents it. Do that as a
   step of the install, not as a repair afterwards.

2. Read the merged settings with `typo3_configuration_lookup`, not from the
   files. The merged result is what the installation runs on.
