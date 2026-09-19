# SKILL-15 — Say why the page is broken before anybody touches it

**Environment:** `E-SITE`, one page of the site answering with an error while
the rest of it renders · **Contract:** `held`
**Held by:** `HintsTest::aFailingInstallationIsSaidWhatItWritesDownAndWhatItOnlyShows`
holds that both halves of a failing installation are stated and reached from the
symptom's own words,
`SkillTest::anInstallationIsBuiltInDependencyOrder`
that the workflow which owns a running installation names that lookup before it
proves anything, and
`HintsTest::aRequestForACauseIsAnsweredWithWhatFindingOneNeeds` that a request
asking for a cause and no change is answered with what finding one needs rather
than with the workflow that writes a patch. That the session then reaches the
cause is **not guarded**, because nothing short of a run on `E-SITE` reads it.

**Read 2026-09-02, again 2026-09-19:** the `diagnosis` intent still changes nothing. It still opens on what the installation wrote down rather than on what it rendered, by `typo3_hint_lookup` with id=installation-exception-output. It exists since 2026-08-21, so the guide recognizes the request as the shape it is instead of by the subject it names. What remains owed is the run. `D-SKL-065`'s first **Wrong if** is a session that has the route and hand-reads its way past both owners anyway.

> One page on our site answers with an error instead of rendering, and the rest
> of it is fine. Work out what is causing it and where. Don't change anything
> yet — I want to know what is wrong before anybody touches it.

**What has to come out of it**

- The answer says which half the failure is in: something threw, or the
  installation answered that way on purpose and wrote nothing down.
- Where something threw, the session reads the message out of what the
  installation wrote rather than out of the page it rendered. The answer says
  that some exceptions never land in the log at all.
- Where the page carries no message, the answer names what decided that rather
  than reads it as an installation with nothing to say.
- The cause is a file and a reason, with the evidence each came from.
- Nothing is changed. The session corrects no configuration and edits no code.
  It stops at the finding with what the fix would be beside it.
- Where the cause belongs to a workflow that exists, the answer names that
  workflow. The installation and its site configuration, a removal on a declared
  major, a package-wide defect.

**How it fails**

- The guide reads the request as the subject it names and the session loads the
  workflow that builds that subject. A content element renders wrong, and the
  session is in the order that adds one.
- The steps it works from are the ones a patch owes, keep the change focused,
  add the narrowest useful coverage, draft the commit message. The request asked
  for nothing to change.
- The session fetches the rendered error page and parses it for a message the
  log holds whole.
- The session reads an empty log as no record at all. The answer stops there
  instead of separates a response TYPO3 returns on purpose from an exception
  that never lands in the log.
- The session asserts the cause from the symptom, with no file opened and
  nothing said about which read it lacks.
- The session makes the fix and reports the change as the finding.
