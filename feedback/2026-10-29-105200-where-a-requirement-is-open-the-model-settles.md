---
date: 2026-10-29T10:52:00+00:00
category: missing-knowledge
status: open
model: claude-opus-5, claude-fable-5-1
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/lina/trials/jambo-1029, /home/lina/trials/jambo-fabian
---

# where a requirement is open the model settles it without asking

## Observation

Two runs of 2026-10-29 built a sitepackage from a static design, from identical
directories and one sentence. The design leaves several questions open, and
`knowledge/` answers none of them. Each run settled each question on its own,
and the two runs did not settle them alike.

| Question | `claude-opus-5` | `claude-fable-5-1` | In `knowledge/` |
| ------------------------ | ------------------------- | ------------------------- | --------------- |
| how an element is built   | TCA record types, no choice offered | TCA record types, no choice offered | nothing on content blocks |
| what Easy Read is         | a site language, `/easy-read/`, after a page first | a site language, `/easy-read/` | nothing |
| the language fallback     | `fallback`, `fallbacks: '0'` | `strict`, empty `fallbacks` | nothing on `fallbackType` |
| how a site is searched    | `indexed_search` and alternatives, names the crawler | `indexed_search` and alternatives, no crawler | rendering only |
| spam protection           | raised before go-live, unprompted | not raised | the honeypot, with its cache trap |
| how long stored data lives | its form stores nothing | rows kept without end | nothing |
| what holds a group of pages | standard pages, hidden from the menu | no container page | nothing |
| what a sent mail carries | the core layout, not raised | the core layout, not raised | nothing |

Neither run asked the user about any row of that table.

**Easy Read is the sharpest case.** The design puts `Easy Read` in the utility
bar beside `Swahili`. Read as markup it is a second language switch. Read as
public-sector practice it is an accessible version of the content, which needs
elements of its own. Both readings are defensible, and the design supports
both.

`claude-opus-5` built the page first, at `/utility/easy-read`, and then
replaced it: that page is gone from `Initialisation/data.xml`, and the site
now declares Easy Read as language 2 on `/easy-read/` with
`typo3Language: easyread`. `claude-fable-5-1` declared a language from the
start. So the two runs converged, without either asking, on the reading this
feedback argues against.

Neither run established what the term requires. Easy Read carries an image for
each sentence, larger type, one idea per sentence, and a reading by somebody
with the relevant disability before it publishes. That is not a translation of
the elements the design already has. A tile grid has no Easy Read translation,
because Easy Read needs an element of its own.

So both structures are wrong in the same way. The shape was settled before
anybody established what goes in it. The run that made it a language implies
that the existing elements translate, and they do not. The run that made it a
page gives no Easy Read version of a service page at all.

**Both runs answer a resident under TYPO3's branding, and the default sends
them there.** Each wrote an `EmailToReceiver` finisher. The template EXT:form
ships for that finisher, `Finishers/Email/Default.fluid.html`, declares
`<f:layout name="SystemEmail" />`, and the plain text template declares it as
well. So a public contact form renders through the layout written for system
mail to an administrator.

That layout takes its logo from `BE/loginLogo` and falls back to
`EXT:core/Resources/Public/Images/typo3_orange.svg` with `alt="TYPO3 Logo"`.
Its footer renders `typo3.information.copyrightNotice`. The maintainer read
the result of the `claude-fable-5-1` run:

> This email was sent by Jambo County Council from URL:
> https://jambo-fabian.ddev.site/ - Please contact your site administrator if
> you feel you received this email by accident.
>
> TYPO3 CMS. Copyright © 1998-2026 Kasper Skårhøj. [...] Obstructing the
> appearance of this notice is prohibited by law.

Two things are wrong there, and only one is the branding. The copy addresses
an administrator, and the reader is a resident who filled in a contact form.
The notice then tells that reader's site owner that the notice may not be
removed, which reads as a bar on sending mail of your own.

No bar exists, and the core says so by its own options. `EmailFinisher` takes
`templateRootPaths`, `layoutRootPaths`, `partialRootPaths` and `templateName`,
so a form supplies a layout of its own and never renders `SystemEmail`. What
the notice requires of the backend login screen is law rather than TYPO3, and
stays out of this corpus. That the finisher is built to take another layout is
this corpus's to say.

Neither run raised any of it. The corpus names `FluidEmail`, `SystemEmail`,
`BE/loginLogo` and `MAIL.templateRootPaths` nowhere.

**A container for pages is the decision whose consequence nobody sees.** The
`claude-opus-5` run put the footer links under a page `/footer`, the utility
links under `/utility`, and the legal pages under `/legal`. All three are
doktype 1 with `nav_hide = 1`. The core offers a folder, doktype 254, and a
spacer, doktype 199, for a group that is not itself a page.

`nav_hide` keeps a page out of the menu and out of nothing else.
`PagesXmlSitemapDataProvider` excludes the doktypes `3, 4, 6, 7, 199, 254` and
reads `nav_hide` nowhere. So `/footer`, `/utility`, and `/legal` are listed in
the XML sitemap of that site, and each one answers at its own address. The
duplicate footer leaves below them are shortcuts, doktype 4, so those stay out.
The doktype decides, and the flag the run set does not.

The `claude-fable-5-1` run built no container page. It wrote a folder for the
waste reports and hid the legal and the utility pages from the menu one by one,
which is what `nav_hide` is for.

The corpus states two facts beside this one and not this one. `datahandler`
says a folder is where a table of your own goes. `page-rendering` says the menu
processor excludes folders and backend user sections by default. Neither says
which doktype holds a group of pages.

**Stored form data is the row where an unprompted offer proved unreliable.**
The `claude-fable-5-1` run wrote a `SaveToDatabase` finisher into
`waste-report.form.yaml`. It stores a ward, a location, a reporter name, a
reporter email address, and a message in `tx_jambositepackage_waste_report`.
The run never raised how long those rows stay. The runs of 2026-10-28 offered a
command that deletes such rows after 90 days, and nobody asked them for it. So
the same offer arrives in one run and not in the next.

Core answers one half of that question and leaves the other half open.
`form:cleanup:uploads` removes the `form_<hash>` upload folders after a default
retention period of 336 hours, and `DeleteUploadsFinisher` removes the files of
one submission. Nothing in `typo3/cms-form` deletes a row that `SaveToDatabase`
wrote. A reader who finds the upload command concludes that the framework
handles retention.

Two properties of that installation hide the rows further. The finisher writes
them with `pid: 0`, where the list module shows them to an administrator alone.
The installation has no `typo3/cms-scheduler`, so a cleanup command runs from a
cron entry or not at all.

**What the runs did with the subjects this repository does cover.** The
Swahili locale is covered by `user-defined-locale`, and it landed:
`claude-fable-5-1` fetched it by id with `site-label-language` and registered
the locale, and `claude-opus-5` fetched the same pair. Fable
wrote the line in the sitepackage's `ext_localconf.php` rather than in
`config/system/additional.php` as the hint says, so whether that runs early
enough for the pack check is open. The honeypot is covered by
`form-framework`, which says it guards a first page only where render is
non-cacheable. On a cached page `FormRuntime` adds no honeypot validator at
all.

A reading of the transcripts breaks the pattern this paragraph first claimed.
Where `knowledge/` states something, a run reads it and then does as it likes:
Fable fetched `translation-domain` by id and wrote 29 labels in the long form.
That belongs to the feedback on a statement that does not reach the result.
What stays here is the other half. Where `knowledge/` says nothing, each run
answers from its own training, and the user cannot tell the two cases apart:
an answer this server stands behind and an answer the model supplied read the
same in the session.

The cost lands on the person who cannot check. A strict fallback leaves most of
a half-translated Swahili site empty rather than in English. A search box
renders in every language over content that exists in one. Both look finished.

## Query

User: "I'm a PHP developer and I have never used TYPO3. There is a static HTML
design in design/. I want to build this site in TYPO3." Two runs:
`claude-opus-5` in `/home/lina/trials/jambo-1029`, and `claude-fable-5-1` in
`/home/lina/trials/jambo-fabian`. The maintainer then asked each run for a
search, for Swahili and for Easy Read, and asked about spam protection.

## Suggestion

What Easy Read requires is not this server's knowledge. It is accessibility
practice rather than TYPO3, and a corpus that takes it on carries it badly. Two
things beside it are this server's, and both are missing.

Say that an accessible variant is a content model decision before it is a
language decision. A variant that needs its own elements is not a translation
of the elements beside it. That belongs next to `content-model-vs-markup`,
which is the same rule one level up.

Say what `fallbackType` decides. The three values are a choice the user makes,
and `strict` leaves an untranslated record absent rather than English. The two
runs picked opposite values for the same site, `fallback` and `strict`, and
neither asked. A site that launches with a partial translation is the case
that decides it.

Say what a sent mail carries and where that comes from. The template EXT:form
ships declares `<f:layout name="SystemEmail" />`, so a contact form answers
under the core's system mail layout until a site replaces it. `EmailFinisher`
takes `templateRootPaths`, `layoutRootPaths`, `partialRootPaths` and
`templateName` for that. `FluidEmail` reads the three `MAIL.*RootPaths` for
every other mail, and `BE/loginLogo` changes the logo in a mail and on the
backend login at once.

Say it where a reader meets the notice, because its wording reads as a bar on
replacing the layout. A site that writes to the public settles this before it
goes live.

Say which doktype holds a group of pages. A folder or a spacer is the
container, and a page hidden from the menu stays a page: it has an address, it
answers, and the XML sitemap lists it. That belongs beside the `page-rendering`
statement on what the menu processor excludes.

Say what deletes form data and what does not. `form:cleanup:uploads` and
`DeleteUploadsFinisher` cover the uploaded files. A row that `SaveToDatabase`
writes stays until the site deletes it. How long a site keeps personal data is
law rather than TYPO3, so this corpus says what core removes rather than what a
site should keep.

Two statements cover the rest. One weighs the core way of building an element
against content blocks. One says what `indexed_search` indexes without a
crawler, and names the alternatives. `friendsoftypo3/content-blocks` 2.4.10
requires `typo3/cms-core: ^14.3.7`, and `typo3/cms-indexed-search` is released
for 14.3 and is not part of `typo3/cms-base-distribution`.

The content blocks statement needs a decision before a hint, because it moves a
boundary. This server has answered for the core. A statement that weighs a
package outside it is a new kind of answer. `knw-012` records what happened
when a session blurred that line once. Whatever the decision settles, a
statement naming such a package says that the package is not core.

The general point is worth an entry of its own. This corpus covers the APIs of
TYPO3 well and the decisions a new site makes hardly at all. A newcomer cannot
make those decisions, and cannot see that one was made for them.
