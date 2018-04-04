# Custom HTML Error Pages by ![Flagrow logo](https://avatars0.githubusercontent.com/u/16413865?v=3&s=20) [Flagrow](https://discuss.flarum.org/d/1832-flagrow-extension-developer-group), a project of [Gravure](https://gravure.io/)

[![MIT license](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/flagrow/html-errors/blob/master/LICENSE.md) [![Latest Stable Version](https://img.shields.io/packagist/v/flagrow/html-errors.svg)](https://packagist.org/packages/flagrow/html-errors) [![Total Downloads](https://img.shields.io/packagist/dt/flagrow/html-errors.svg)](https://packagist.org/packages/flagrow/html-errors) [![Donate](https://img.shields.io/badge/patreon-support-yellow.svg)](https://www.patreon.com/flagrow) [![Join our Discord server](https://discordapp.com/api/guilds/240489109041315840/embed.png)](https://flagrow.io/join-discord)

This extension allows you to customize the Flarum error pages.
By default these pages have very boring HTML content with no styling and no link in beta7.
Now you can change them to something that better reflects your website!

## Installation

Use [Bazaar](https://discuss.flarum.org/d/5151-flagrow-bazaar-the-extension-marketplace) or install manually:

```bash
composer require flagrow/html-errors
```

## Updating

```bash
composer update flagrow/html-errors
php flarum migrate
php flarum cache:clear
```

## Configuration

Open the extension options to configure the custom HTML.
Leaving a field empty will show the default Flarum error page.

The custom error pages are only applied when browsing the forum front-end.
Any error response under /api or /admin is unaffected.

You can handle additional error codes by entering the values manually in the `settings` table of the database.

## Support our work

We prefer to keep our work available to everyone.
In order to do so we rely on voluntary contributions on [Patreon](https://www.patreon.com/flagrow).

## Security

If you discover a security vulnerability within Custom HTML Error Pages, please send an email to the Gravure team at security@gravure.io. All security vulnerabilities will be promptly addressed.

Please include as many details as possible. You can use `php flarum info` to get the PHP, Flarum and extension versions installed.

## Links

- [Flarum Discuss post](https://discuss.flarum.org/d/10784-custom-html-error-pages)
- [Source code on GitHub](https://github.com/flagrow/html-errors)
- [Report an issue](https://github.com/flagrow/html-errors/issues)
- [Download via Packagist](https://packagist.org/packages/flagrow/html-errors)

An extension by [Flagrow](https://flagrow.io/), a project of [Gravure](https://gravure.io/).
