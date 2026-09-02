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

**Read 2026-09-02:** the `diagnosis` intent still changes nothing and still
opens on what the installation wrote down rather than on what it rendered, by
`typo3_hint_lookup` with id=installation-exception-output. It was built on
2026-08-21, so the request is recognized as the shape it is instead of by the
subject it names. What is still owed is the run — `D-SKL-065`'s first **Wrong
if** is a session that has the route and hand-reads its way past both owners
anyway.

> One page on our site answers with an error instead of rendering, and the rest
> of it is fine. Work out what is causing it and where. Don't change anything
> yet — I want to know what is wrong before anybody touches it.

**What has to come out of it**

- The answer says which half the failure is in: something was thrown, or the
  installation answered that way on purpose and wrote nothing down.
- Where something was thrown, the message is read out of what the installation
  wrote rather than out of the page it rendered, and the answer says that some
  exceptions are never written at all.
- Where the page carries no message, what decided that is named rather than read
  as the installation having nothing to say.
- The cause is a file and a reason, with the evidence each was read from.
- Nothing is changed. No configuration is corrected, no code is edited, and the
  session stops at the finding with what the fix would be beside it.
- Where the cause belongs to a workflow that exists, that workflow is named —
  the installation and its site configuration, a removal on a declared major, a
  package-wide defect.

**How it fails**

- The request is read as the subject it names and the workflow that builds that
  subject is loaded: a content element renders wrong, and the session is in the
  order for adding one.
- The steps it works from are the ones a patch owes — keep the change focused,
  add the narrowest useful coverage, draft the commit message — against a
  request that asked for nothing to change.
- The rendered error page is fetched and parsed for a message the log holds
  whole.
- An empty log is read as nothing having been recorded, and the answer stops
  there instead of separating a response TYPO3 returns on purpose from an
  exception that is never written.
- The cause is asserted from the symptom, with no file opened and nothing said
  about which reading is missing.
- The fix is made and the change is reported as the finding.
