# MageSteady CSP Backoffice

## Description

**MageSteady CSP Backoffice module for Magento 2** allows you to manage and edit the Content Security Policy (CSP) directly from the admin panel, instead of modifying XML files.

This is particularly useful for teams where non-developers manage tagging strategies through tools like Google Tag Manager or directly from the Design configuration in the Magento backoffice.

This module also allows you to collect and view CSP violation reports from the Magento admin panel.

You can also use this module to fix the "CSP header is too large" issue by cleaning up useless values from Magento core and other modules.

If you have multiple themes installed, you will also be able to have different rules for each store view.

[![Latest Stable Version](https://poser.pugx.org/magesteady/csp-backoffice/v/stable)](https://packagist.org/packages/magesteady/csp-backoffice)
[![Total Downloads](https://poser.pugx.org/magesteady/csp-backoffice/downloads)](https://packagist.org/packages/magesteady/csp-backoffice)

## Features

- Edit your Content Security Policy rules in the Magento 2 admin panel
- Collect and view CSP violation reports in the admin panel
- Supports multi-website instances (each rule can be scoped to a specific store view)
- Enable/disable CSP restrict mode from Stores > Configuration
- Override default csp_whitelist.xml entries (so you can remove the useless ones)
- Import/export rules from/to CSV
- Enable/disable the module to get back to the default Magento 2 behavior
- Manage view/edit permission via an ACL rule
- Manage your rules from a remote system using the API
- Violation reports history cleaned periodically to keep only the last X entries (configurable)
- Fully translated in French language (we are happy to merge your contributions if you want more languages supported)

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Compatibility](#compatibility)
- [Code Quality](#code-quality)
- [Known Issues](#known-issues)
- [Contributing](#contributing)
- [License](#license)
- [Disclaimer](#disclaimer)
- [Changelog](#changelog)

## Installation

1. Require the module via Composer: `composer require magesteady/csp-backoffice`
2. Enable/install the module: `bin/magento setup:upgrade`

## Usage

1. Navigate to the Magento admin panel.
2. Go to MageSteady > Content Security Policy > Rules.
3. Add, edit, or remove CSP policy rules as needed. You can also import from current XML rules.
4. Go to MageSteady > Content Security Policy > Configuration.
5. Enable rules management and/or violation reports.
6. Flush the cache.
7. *(Optional) Wait a few days and review your violation reports in MageSteady > Content Security Policy > Violation Reports.*
8. *(Optional) Once you're ok with your rules, you can enable CSP Restrict Mode in MageSteady > Content Security Policy > Configuration.*

## Compatibility

- Magento Open Source/Adobe Commerce: 2.4.x and above

## Code Quality

This module is built with respect for Magento 2’s coding guidelines, ensuring:
- Stable, maintainable codebase.
- Compatibility with future Magento updates.
- Clean implementation following Magento's architectural principles.

This module is also thoroughly optimized for performance and should not impact your stores' general speed.

We never use any private variable, and we try to keep public methods as short as possible to allow you to change this module's behavior easily by using plugins (preferably) and preferences if needed.

## Known Issues

### CSP header is too large
**Description:** Header size is limited to 8k by default in nginx and Apache. This can be an issue if you have too many rules in your Content Security Policy, as all the rules will be stacked one after the other in the same "Content-Security-Policy" header.

**Workaround:** Try removing some unnecessary rules in your configuration. If you can't get them to fit in the 8k default limitation, you should raise the maximum header size in your nginx/Apache configuration.

### Can't login to backoffice after activating Restrict mode
**Description:** If you have enabled adminhtml reCAPTCHA and you didn't allow Google reCAPTCHA's script to be loaded, you are banned from the backoffice and can't log in. 

**Workaround:** Run this command `bin/magento config:set magesteady_csp_backoffice/general/enable_restrict_mode_adminhtml 0`, flush the cache, then add the reCAPTCHA script to your rules and enable Restrict mode again.

### CSP violation reports are not collected properly
**Description:** I have enabled violation reports and they are not showing in the backoffice

**Workaround:** CSP violation reports are queued in your visitors' browser and they are sent when the browser is idle. Please wait a few moments before it appears in your backoffice. You must also be aware that CSP violations are only reported to publicly accessible websites with a valid SSL certificate.

## Contributing

Contributions, issues, and feature requests are welcome!

Feel free to open an issue or submit a pull request on GitHub at [https://github.com/MageSteady/csp-backoffice](https://github.com/MageSteady/csp-backoffice).

## License

This module is licensed under the GNU General Public License v3.0. Refer to the LICENSE file for details.

## Disclaimer

Use this module at your own risk.

While it provides convenience, improper configuration may lead to security vulnerabilities.

We advise you to read this documentation for a better understanding of CSP security concerns: https://cheatsheetseries.owasp.org/cheatsheets/Content_Security_Policy_Cheat_Sheet.html

**We strongly encourage you to forbid CSP rules edition by people that are unaware of the security consequences using the ACL role.**

Please always keep your store up to date to prevent any unwanted modification of this module's database table.

Always test changes thoroughly in a staging environment before applying them to production.

## Changelog

### v1.0.0

Release first version
