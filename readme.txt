=== KWS Security Checker ===
Contributors: jmorris-kissws
Tags: security, vulnerabilities, scanner, protection
Requires at least: 5.6
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Scan your WordPress site for vulnerable plugins and themes with security issues. Take action to quarantine, update, or uninstall risky components.

== Description ==

The KWS Security Checker helps protect your WordPress site by:

* Scanning all installed plugins and themes for known vulnerabilities
* Providing one-click actions to quarantine, update, or uninstall vulnerable items
* Supporting WordPress Multisite networks
* Offering bulk actions for multiple vulnerabilities
* Including French and Spanish translations
* Maintaining detailed security logs

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/kws-security-checker`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. For Multisite, network activate for site-wide protection
4. Access the Security Checker dashboard under the admin menu

== Frequently Asked Questions ==

= What does quarantining do? =
Quarantining deactivates the vulnerable plugin/theme while keeping it installed, allowing you to investigate or wait for an update.

= How often does it check for vulnerabilities? =
The plugin checks daily by default. You can adjust this in settings.

= Where does the vulnerability data come from? =
We aggregate data from WordPress.org, CVE databases, and our security research.

== Screenshots ==

1. Security Checker dashboard showing vulnerable items
2. Bulk action options for multiple vulnerabilities
3. Detailed view of a vulnerable plugin

== Changelog ==

= 1.0.0 =
* Initial release with core security scanning functionality
* Quarantine/Update/Uninstall actions
* Multisite support
* Internationalization (English, French, Spanish)

== Upgrade Notice ==

1.0.0 - Initial release of the plugin.

== Credits ==

Developed by James Morris at [K.I.S.S. Web Solutions](https://kissws.com)