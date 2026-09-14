---
description: >-
  What the TYPO3 backend styleguide is, where it lives, how you install it where the core does not ship it, and what its examples state.
whenToUse: >-
  Before you write backend markup or borrow a core backend class or icon into a package. It names what the styleguide settles and what it leaves open, so you do not read a demo as a contract for the parts it happens to include.
hints:
  - css-styleguide-demos
  - backend-ui
---

# Using the Backend Styleguide

The styleguide is the core's own demonstration of its backend components. It is
also the boundary of what a package may use. A component it lists is public. A
component it does not list is one you do not use and do not suggest. Nothing
else in the core states that, so the list is the statement.

## Where the Styleguide Lives

**Since:** 13

It is a system extension, below `typo3/sysext/styleguide/`. Every installation
has it on disk, active or not. When you activate it, a **Styleguide** module
appears in the backend under the developer tools. The component demos are one
page per component.

## Installing the Styleguide Where the Core Does Not Ship It

**Until:** 12

It is a separate package, `typo3/cms-styleguide`, which a project requires as a
development dependency. An installation that never required it does not have it
at all. There you can read neither its module nor its templates.

## Reading It Without the Module

The templates are
`typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/`. The
list of components is `$allowedActions` in
`typo3/sysext/styleguide/Classes/Controller/ComponentsController.php`. Both are
committed. So a checkout of a branch answers what that branch demonstrates, and
you install and run nothing. That is how you settle a question about a major the
installation does not have.

## What an Example States, and What It Does Not

**An example is complete, and completeness is what it costs.** A table demo
carries a caption, a head, a foot and a control column. A card demo carries an
image, a header, a body and a footer. None of that says which parts make the
component and which the author added to show it off. No minimal form stands
beside the full one to subtract from.

So a demo answers what a full usage looks like and never what the component
requires. If you read it the other way, you produce markup that carries
everything. That markup works, and it is not what the component is.

## What a Template Writes Is Not What the Demo Shows

A demo renders through ViewHelpers and web components as often as it writes
markup. The avatar demo is `<be:avatar>` and spells no avatar class at all. The
status indicators stand in the styleguide's own `indicators-grid`, which is page
furniture rather than part of any component.

So the class names in a template are neither all of what the component uses nor
only that. A class you read out of one is a guess until something places it.

## What Places a Class

`typo3_component_lookup` answers where a class sits for each major: around the
component, on it, or inside it. It reads that from the core's compiled
stylesheet. That is the part you cannot subtract out of a demo, and it is what
makes a borrowed class safe. A class the answer does not place is one the
stylesheet says nothing about. That is not a licence to attach it anywhere.

A package that declares more than one major asks that question for each of
them. The installation supplies one major, and the styleguide in it demonstrates
that one.
