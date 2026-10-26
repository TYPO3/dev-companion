---
date: 2026-10-26T12:11:12+00:00
category: idea
status: open
model: claude-opus-5-5[1m]
tool: typo3_project_describe
directory: /home/benji/site10-run
---

# project_describe shows a site base host DDEV does not serve, without a warning

## Observation

Task: create a sitepackage and check the frontend in a DDEV project.
project_describe returned the site base https://typo3-mcp-e-site-14-3.ddev.site/ and the DDEV hostname site10-run.ddev.site.
It did not say that these differ. A browser cannot reach the site under its base.
My first frontend check failed. I used curl in the web container with a Host header and https://127.0.0.1/.
TYPO3 answered 404 "No site configuration found".
Then I used http://127.0.0.1/ with the Host header and X-Forwarded-Proto: https. That gave HTTP 200.
I found this by trial. It cost three round trips.

## Query

typo3_project_describe (no arguments) in /home/benji/site10-run

## Suggestion

Mark a site whose base host is not among the DDEV hostnames. Say that a browser cannot reach it. Give the command that requests the site from inside the container: curl -H "Host: <base host>" -H "X-Forwarded-Proto: https" http://127.0.0.1/. Or put this in the browser-check guide and point to it from that field.
