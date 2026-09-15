# SITE-01 — A new site from scratch

**Environment:** `E-SITE`, freshly installed · **Contract:** `held`
**Held by:**
`HintsTest::aSitepackageIsAnsweredWithTheLayoutTheCoreItselfShips`,
`HintsTest::siteLocalSettingsSourcesAreAnsweredWithTheirPrecedence`,
`PackageSourcesTest::fluidNamespacesAreReadFromThePackagesThatDeclareThem`,
`ScopeTest::decidingOneSitesConfigurationIsDeclinedInTheOrientation`

> Fresh TYPO3 here. Set up the site: a site configuration with two languages, a
> site package with our templates, and the TypoScript so the frontend renders.
> The page tree structure I will do myself.

**What the agent needs from this server**

- What a site set is, what belongs in it, and how it relates to the site
  configuration.
- Where TSconfig ends and TypoScript begins.
- The Fluid conventions for the templates, and which namespaces are available
  undeclared in this installation.
- An honest boundary where the rest — the site configuration file itself, the
  language setup, the installation steps — is not covered.

**What has to come out of it**

- Site set and TSconfig conventions come over as conventions, without core paths
  or core checks attached.
- The answer names what the server does not cover as not covered, with the
  documentation as the route. It does not fill it in with something plausible.
  `typo3_server_scope` declines the decision about this site's languages, base
  and error handling outright. A site set and how its settings resolve against
  the site's own are the covered half and stay in the answer.
- Nothing suggests that a `typo3/sysext/` path is where this project's files go.

**How it fails**

- The core's own TypoScript defaults presented as how to configure a site.
- The scope boundary stated in one paragraph and then contradicted by the
  checklist below it.
