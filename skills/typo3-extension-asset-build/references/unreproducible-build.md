# A build that is not reproducible

Read this when the rebuild gives a diff your change does not explain. Read it
too when the build does not run at all.

A build whose output differs on every run makes every later diff unreadable.
After a change, two causes look the same. The output differs because of your
change, or it differs whatever the source says.

1. **Run the repository's own check.** A check that asserts a clean tree after a
   build answers this question. Step 2 of the skill found whether there is one.

2. **Build the unchanged checkout and compare.** That separates the two causes.

3. **If the tree is dirty on the unchanged checkout, report that as the
   finding.** It is about the toolchain, not about the package. Say which file
   differs and how.

4. **If the build does not run, compare the Node versions first.** Compare the
   Node that step 1 of the skill reported with the Node on this machine.
   Reinstall the dependencies only after that.
