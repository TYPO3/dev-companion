# Jambo County Council — static design

Four pages, one stylesheet, no build step and nothing loaded from the network.

```
index.html             the home page
services.html          a service overview, with an aside beside the text
permits.html           a service page, full width
imprint.html           a legal page, a title and text
assets/css/main.css    the one stylesheet
assets/img/            the emblem, the illustrations and the portrait
```

## The four page shapes

| Page            | Head                                 | Body                          |
| --------------- | ------------------------------------ | ----------------------------- |
| `index.html`    | feature head, image beside the words | card grid, then text sections |
| `services.html` | banner head over an image            | text, and an aside beside it  |
| `permits.html`  | banner head over an image            | text over the full width      |
| `imprint.html`  | title band, no image                 | text over the full width      |

## The blocks

Each block below appears on at least one page, and the stylesheet names it with
one class.

| Block        | Class                | Parts                                                                  |
| ------------ | -------------------- | ---------------------------------------------------------------------- |
| Feature head | `.hero--feature`     | eyebrow, title, one link, one image                                    |
| Banner head  | `.page-head`         | title, one line under it, one image                                    |
| Title band   | `.page-head--plain`  | title alone                                                            |
| Card grid    | `.card-grid` `.card` | title, text, link above the grid; image, title, text and link per card |
| Teaser       | `.teaser`            | title, one line under it, text, link                                   |
| Media teaser | `.teaser--media`     | the same, over an image                                                |
| Author box   | `.author`            | portrait, name, role, text                                             |
| Link column  | `.link-list`         | title, address, links                                                  |
| Social row   | `.social-row`        | title, one link per network                                            |
| Callout      | `.callout`           | title, text                                                            |
| Data table   | `.data-table`        | caption, head row, body rows                                           |

The aside stacks the author box and the teasers under the section navigation.
The footer carries four link columns and the social row.

## The design

| Token       | Value     | Used for                           |
| ----------- | --------- | ---------------------------------- |
| Brand green | `#06603f` | main navigation, card rule, emblem |
| Brand dark  | `#04452d` | head background, link colour       |
| Accent      | `#a8431a` | phase banner tag, callout rule     |
| Ink         | `#14261f` | body text, utility bar, footer     |
| Page        | `#f7f5f0` | page background                    |
| Surface     | `#ffffff` | cards, masthead                    |

The type is a system font stack, so no font is fetched. Every colour pair meets
WCAG AA contrast. The markup carries a skip link, landmark elements, a visible
focus outline and `aria-current` on the active navigation item.

The illustrations stand in for photography, which the council supplies later.
Each one is an SVG in the palette above.

## This council does not exist

Jambo County is fictional, and every page says so in the phase banner and in the
footer. Nothing here copies a real authority's identity.
