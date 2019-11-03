=== WP User Control ===
Contributors: wmsedgar
Donate link: http://palmspark.com/wordpress-user-control/
Tags: sidebar login widget, user login widget, user registration, custom login widget, login widget
Requires at least: 3.0.1
Tested up to: 3.6.1
Stable tag: 1.5.3
License: BSD New (3-Clause License)
License URI: http://directory.fsf.org/wiki/License:BSD_3Clause

WordPress sidebar login widget that allows a user to login, register, or reset their password, ALL without leaving their current location!

== Description ==

WP User Control adds a WordPress sidebar login widget that allows a user to login, register, reset lost passwords, etc. without leaving a specific location within your site.

<strong>Key Features:</strong>
<ul>
	<li>Honeypot to filter spambot registrations</li>
	<li>Supports WPMS or WP Standard</li>
	<li>Handles all errors gracefully and redirects to current page</li>
	<li>"Remember Me" checkbox</li>
	<li>No HTML or CSS knowledge needed to customize</li>
	<li>Cross-browser compatible</li>
	<li>Lightweight and easy to use</li>
</ul>
<strong>Customization Features</strong>
<ul>
	<li>Customize sender name and email address in WP admin emails</li>
	<li>Redirect to custom URL for login</li>
	<li>Customize tab & button labels (login, register, and reset)</li>
	<li>Toggle Avatar display</li>
	<li>Set default tab (login, register, or reset)</li>
	<li>Add custom link for logged in users after <em>Profile</em> or <em>Admin</em> link</li>
	<li>Internationalized and ready for language translation</li>
</ul>

= Plugin's Official Site =

WP User Control Website ([http://palmspark.com/wordpress-user-control](http://palmspark.com/wordpress-user-control))

= Plugin Support Forum =

WP User Control Web Forum ([http://palmspark.com/forum/wordpress-plugins/wordpress-user-control/](http://palmspark.com/forum/wordpress-plugins/wordpress-user-control/))

== Installation ==

Download the WP User Control archive, then extract the wp-user-control directory to your WordPress plugins directory (generally ../wordpress/wp-content/plugins/).  Then go to the <em>Plugins</em> page of your WordPress admin console and activate. Once activated, add the WP User Control widget to your sidebar, and configure as desired.

NOTE: The custom login URL setting will affect logins sitewide. Make ABSOLUTELY CERTAIN you are entering a valid URL before saving, or it could cause you problems getting into your site.

== Frequently Asked Questions ==

= Does WP User Control store any login credentials? =

No.

= Does the custom login URL setting affect all site logins? =

Yes, using this setting will affect all logins for the site. Make ABSOLUTELY CERTAIN you are entering a valid URL before saving, or it could cause you problems getting into your site.

= Does the custom sender name and email address affect all administrative emails from my site? =

Yes, this setting will take effect for all administrative emails sent from your site. Leave these fields blank to use the default WordPress settings.

== Screenshots ==

1. Login tab.
2. Registration tab.
3. Password reset tab.
4. Example logged in user.
5. Configuration options.
6. Login error.
7. Registration error.
8. Reset error.

== Changelog ==

= 1.5.3 =
* Re-added jQuery v1.9 fix which was overwritten in prior release.

= 1.5.2 =
* Tested on WordPress 3.6.x.
* Updated language translation file.
* Added honeypot to registration form to filter spam registrations for sites that display registration as the default tab.
* Updated CSS to allow easy use of widget with colored backgrounds and different font colors (such as with Twenty Thirteen theme).
* Added German language translation (Credits & Thanks to: Daniel ?).
* Added Afrikaans language translation (Credits & Thanks to: Pieter Goosen).
* Added Hungarian language translation (Credits & Thanks to: Bence Szabari).

= 1.5.1 =
* Added enhancements to deal with WP SSL settings for FORCE_SSL_ADMIN and FORCE_SSL_LOGIN.
* Added new Spanish language translation (Credits & Thanks to: Javier Galvez and Juan Saavedra).
* Fixed bug with loading of language file translations.
* Fixed minor issues with CSS styles that caused incompatibilities with some WP themes.

= 1.5 =
* Added WPMS support.
* Fixed bug with password resets for existing users.
* Added automatic detection of user registration disabled setting to trigger enabling or disabling of new user registration form.
* Added additional error traps for user registration (uppercase letters in user name, spaces, etc.).
* Revamped password reset function so user is assigned a temporary password instead of redirected to a standard WP page for reset. For security reasons, reset will now only accept email addresses, not user logins.
* Revamped login function so user is not redirected to standard WP login page for any errors.
* Added Italian language translation (Credits & Thanks to: Gregory Condello, Recos Srl - La Fotolito, Gabriele Piccione, and Orazio Foti)
* Added Dutch language translation (Credits & Thanks to: Jerry Latchmansing).

= 1.4 =
* Fixed bug with "invalid key" in links in password reset emails.
* Added ability to customize sender name and email address in WP admin emails (registration, reset, etc.).

= 1.3 =
* Fixed bug with jQuery not loading properly for external facing pages on some systems (required for dynamic tabs).
* Added Finnish language translation files.

= 1.2.1 =
* Fixed bug preventing options screen from displaying resulting from incorrect internal function call.

= 1.2 =
* Fixed bug preventing javascript from activating on some systems. Separated JS into separate file and loaded using WP enqueuing method to make sure jQuery is properly loaded.
* Added a custom link option to be included for logged in users after Profile or Admin links. Can be used for links such as: Add Post or Account (link to WPEC Account page).
* Widget now returns users to most recent active tab after login, register, or reset events that DO NOT result in a successful login.
* Widget event status messages now output on respective tab (login, register, or reset depending upon event).
* Added ability to handle user password reset submissions with invalid userid or invalid emails without redirecting to standard WP login page.
* Added ability to choose default tab (login, register, or reset).
* Internationalized plugin, it is now ready for language translation files (please send me any language files created for inclusion with the plugin download).

= 1.1.1 =
* Fixed registration bug for new user registration.

= 1.1 =
* Added capability to handle unsuccessful login attempts, and redirect to referring location with notification display.
* Added capability to set custom login URL for site that is also sent in new user registration emails.
* Added capability to handle new user registration errors for email or username exists, and redirect to referring location with notification display.

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.5 =
* Added WPMS support, automatic detection of registrations disabled setting, fixed password reset bug for existing users and added Italian and Dutch language translations.

= 1.4 =
* IMPORTANT: Fixed bug with "invalid key" in links in password reset emails, also added ability to customize sender name and address in WP admin emails.

= 1.3 =
* Minor bug fixes, added Finnish language translation file.

= 1.2.1 =
* IMPORTANT: Fixed bug preventing options screen from displaying resulting from incorrect internal function call.

= 1.2 = 
* Added a bunch of new features (custom links, choose default tab, internationalization, etc.) and fixed some minor bugs (javascript not loading for some users, handling of password reset for invalid userid or emails, etc.)

= 1.1.1 =
* IMPORTANT: Fixed registration bug for new user registration.

= 1.1 =
* Added capability to handle unsuccessful login attempts, and redirect to referring location with notification display.
* Added capability to set custom login URL for site that is also sent in new user registration emails.
* Added capability to handle new user registration errors for email or username exists, and redirect to referring location with notification display.

= 1.0 =
* Initial release
