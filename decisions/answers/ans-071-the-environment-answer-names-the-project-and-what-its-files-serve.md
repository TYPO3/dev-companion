---
id: D-ANS-071
title: The environment answer names the project and what its files serve
date: 2026-08-10
status: open
---

# D-ANS-071 — The environment answer names the project and what its files serve

**`typo3_project_describe` carries the DDEV project name and the hostnames its
files declare, and says where the live half comes from.**

The answer reported that a DDEV environment exists and nothing that lets a
caller reach the site it describes.

## Evidence

- `feedback/2026-08-10-101723`. Four shell round trips for the project name, the
  primary URL and the router's address. One wrong attempt in between:
  `host.docker.internal` against the bound port, refused. The session read the
  omission correctly: live ports are not in `.ddev/config.yaml`.
- The name is. `ddev config --project-name` is "normally the same as the last
  part of directory name" and `--project-tld` defaults to `ddev.site`, both out
  of DDEV's own help. The config file's comments state that an
  `additional_hostnames` entry lives under the same top-level domain and an
  `additional_fqdns` entry as written.
- Read against the DDEV project that runs on this machine, the answer now names
  `typo3-cms` and `typo3-cms.ddev.site`, which is the `primary_url` that project
  reports.

## Decided

- Two fields, `project` and `hostnames`, required on every path — null and empty
  where the environment is the one `TYPO3_DEV_COMPANION_CONSOLE` names, which
  declares neither.
- The top-level domain is the project's own or the default, and a global DDEV
  configuration that sets another one is not read. This answer is the project's
  files (`R-PRJ-001`), and a machine file would make it depend on where it runs.
  A project that sets its own `project_tld` states it in the same files, and the
  answer comes from them.
- The answer says what it is not: bound ports and the router's address on the
  container network come from `ddev describe -j` and `docker inspect`. To reach
  for them here would mean a question to a live project, which is what
  `R-DIS-006` forbids. A stopped project has to read exactly like a live one.
- The sentence is its own line rather than part of "where the commands run". A
  repository that declares no commands has no such line at all, and what the
  environment serves is not a fact about commands.
- What to do with the hostname is not repeated here. Reaching the site from a
  container is
  [`D-KNW-069`](../knowledge/knw-069-a-browser-in-a-container-reaches-a-site-on-the-router.md)
  and the document it decided.

## Assumed

- That a project whose config states no `name` lives under its directory name.
  It is what DDEV's own help says the flag defaults to, and no project without
  the key exists here to watch it.

## Wrong if

- A caller acts on a hostname the live router does not answer on: a global
  `project_tld`, a project renamed since. The fields say they are what the
  configuration declares. A report that they misled somebody anyway means the
  mark does not carry it.

## Since then

The fields are there and marked. `typo3_project_describe`'s `environment` block
requires `via`, `project` and `hostnames`. The `hostnames` description says what
the **Wrong if** depends on: "What the configuration declares, not what is
running — the ports the router binds and its address on the container network
are not in these files, and `ddev describe -j` is what carries them."

Nobody reports that they misled them. Two sessions since have named a DDEV
hostname in a feedback and neither is that case. `2026-08-18-070515` used the
declared base to check its own assumption and reports that the site answers at
`https://blog.ddev.site/`. `2026-08-18-074200` is about two site configurations
that collide on one base. There this tool's answer is what the session calls the
single most useful thing it had. So nobody has tested the mark with an action
past it, which is what the bullet waits for.
