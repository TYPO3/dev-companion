---
date: 2026-10-26T12:15:21+00:00
category: tool-gap
status: open
model: claude-opus-5-5[1m]
tool: bincli, environmentstatus, environmentcreate, typo3_project_describe
---

# A case run leaves the person without the frontend URL and the backend login

## Observation

Task: run the contract case SITE-10 in E-SITE 14.3 and hand the result to the maintainer to look at.
The run went into a copy of .environments/e-site-14.3 outside this repository, under its own DDEV name. Inside the repository the session would load this repository's CLAUDE.md and AGENTS.md.
The copy kept the site base of the original, so its frontend answered 404 "No site configuration found" while DDEV served the new hostname. The run itself only reached the page from inside the container, with a Host header.
The maintainer also did not know the backend login. It stands as Environments::ADMIN_PASSWORD in src/Upkeep/Environments.php, and environment:status prints neither the URL nor where the login is.
So the person who has to judge the run could open neither the frontend nor the backend without a second round of questions.

## Query

bin/cli environment:status; the SITE-10 prompt in a copy of e-site-14.3 at ~/site10-run

## Suggestion

environment:status prints, per running E-SITE, the frontend URL, the backend URL and the user, with where the password is defined.
A procedure for a case run outside the repository states that the copy needs its site base set to the new DDEV hostname, or makes the copy itself (environment:create with a target directory outside the checkout).
typo3_project_describe can warn where a site base host is not one DDEV serves for this project, which a session in the run filed separately.
