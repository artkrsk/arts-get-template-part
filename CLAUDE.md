# arts/get-template-part

A single-purpose Composer library that registers one global function,
`arts_get_template_part()`, to load a PHP template partial with WordPress
child/parent-theme override support. It exists so every Arts WordPress plugin
shares one template-loading convention instead of reimplementing it.

The entire library is `src/php/arts-get-template-part.php`, pulled in eagerly
through Composer's `autoload.files` (runs when `vendor/autoload.php` loads, not
lazily like PSR-4).

## Commands

- `composer check` — phpcs then phpstan (the full gate).
- `composer phpcs` / `composer phpstan` — run one gate alone.
- `composer fix` — phpcbf auto-fixes coding-standard violations.
- `composer normalize` — normalize composer.json.

phpstan runs at `level: max` with the WordPress stubs extension. phpcs is
`WordPress-Core` with short-array syntax and Yoda conditions intentionally
allowed (see phpcs.xml for the excluded sniffs).

## Public API

```php
arts_get_template_part(
    string $slug,                 // e.g. 'Media/Media' → Media/Media.php (slugs may be nested)
    array  $args = [],            // data for the template, read as $args['key']
    string $default_path = '',    // absolute path to the plugin's own templates dir
    string $theme_subdir = 'arts' // override folder name inside the theme
): void
```

Search order, first match wins:

1. Child theme — `{stylesheet}/{theme_subdir}/{slug}.php`
2. Parent theme — `{template}/{theme_subdir}/{slug}.php`
3. Plugin default — `{default_path}/{slug}.php`

Steps 1–2 are a single `locate_template()` call (which returns the path — it
does not load); step 3 is the manual fallback, tried only when `$default_path`
is set.

## Gotchas

- **The `function_exists()` guard is load-bearing.** This file is eagerly
  `files`-autoloaded and more than one plugin can ship a copy of it; without the
  guard the second declaration of the global fatals with "cannot redeclare".
- **`$args` is NOT extracted.** The match is pulled in with a plain `include`
  inside the function scope, so templates read `$args['key']` — there is no
  `extract()`, unlike `wc_get_template()`.
- **Silent no-op when nothing matches** — no output, error, or warning.

## Requirements

PHP >= 8.0. Runtime needs only WordPress (`locate_template`, `trailingslashit`,
`ABSPATH`); everything in `require-dev` is tooling.
