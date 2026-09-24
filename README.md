# Zen Addons for SiteOrigin Page Builder

A focused collection of accessible, lightweight widget extensions built on the
[SiteOrigin Widgets Bundle](https://wordpress.org/plugins/so-widgets-bundle/) framework,
for use with [Page Builder by SiteOrigin](https://wordpress.org/plugins/siteorigin-panels/).

- **WordPress.org:** https://wordpress.org/plugins/zen-addons-for-siteorigin-page-builder/
- **Author:** [DopeThemes](https://www.dopethemes.com/)
- **License:** GPL-3.0-or-later
- **Requires:** WordPress 5.5+, PHP 7.4+, SiteOrigin Widgets Bundle

> The user-facing readme is `readme.txt` (WordPress.org format). This file is for developers
> and is excluded from the released plugin zip.

## Widgets

37 widgets in nine groups: layout and content, interactive, media, business and marketing,
conversion and engagement, people and social proof, content blocks, community (bbPress) and
integrations. The full, current list with a short description of each
widget is in the "Widgets included" section of `readme.txt`. All widgets appear under the **Zen Addons** tab in the
SiteOrigin Page Builder widget picker and under **Plugins → SiteOrigin Widgets**.

## Architecture

A flat, hand-written WordPress plugin, no build step required at runtime, no autoloader.

```
zen-addons-for-siteorigin-page-builder/
├── zen-addons-siteorigin.php   # main bootstrap (plugin header, constants, init)
├── uninstall.php               # clean removal of options/transients
├── readme.txt                  # WordPress.org readme
├── core/
│   ├── widgets.php             # registers the ZASO widget group + folder
│   ├── helpers.php
│   ├── shortcodes.php
│   ├── vendor/                 # bundled, runtime-required (e.g. DopeThemes dashboard)
│   └── basic/<slug>/           # one folder per widget: <slug>.php, tpl/, styles/, js/
├── assets/
│   └── vendor/lity/            # bundled lightbox library (no CDN)
└── lang/                       # translations (text domain: zen-addons-for-siteorigin-page-builder)
```

Widgets auto-register through SiteOrigin's `siteorigin_widgets_widget_folders` filter; the
Widgets Bundle discovers each widget from its file header.

## Development

```bash
# Lint coding standards (WordPress Coding Standards)
composer install
composer phpcs        # report
composer phpcbf       # auto-fix

# Syntax check a file
php -l path/to/file.php
```

Conventions: WordPress Coding Standards, tabs for indentation, Yoda conditions, PHPDoc on
all functions, an `ABSPATH` guard at the top of every PHP file, all output escaped, all
input sanitized, and AJAX actions gated by nonce + capability checks. Text domain is
`zen-addons-for-siteorigin-page-builder` (it must equal the plugin slug).

## Releases

Releases are published to the WordPress.org SVN repository. The release zip is built from a
`.distignore`-filtered set (no dev tooling, build configs, or docs):

```bash
# from the workspace root
scripts/build-plugin-release.sh zen-addons-for-siteorigin-page-builder
```

Validate the built zip with Plugin Check before publishing, then stage and commit to SVN.

## License

GPL-3.0-or-later. Copyright DopeThemes.
