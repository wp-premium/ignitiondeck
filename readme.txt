=== IgnitionDeck ===
Contributors: virtuousgiant
Donate link: http://IgnitionDeck.com
Tags: crowdfunding, crowd, funding, ecommerce, commerce, marketplace, order, transaction, widget, skin, fundraising
Requires at least: 3.2
Tested up to: 4.1.1
Stable tag: 1.1.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A free version of IgnitionDeck, the original crowdfunding plugin for WordPress.

== Description ==

A crowdfunding plugin for WordPress that features the ability to add an unlimited number of crowdfunding, pre-order, or fundraising projects to your WordPress website.

IgnitionDeck works with any of the thousands of themes available for Wordpress, offers an incredible amount of free and premium extensions, and is incredibly easy to customize.

This is the free version of IgnitionDeck and includes the following features:

* Create an unlimited number of crowdfunding projects
* Use the WordPress widgets to display the crowdfunding deck (full or mini) for the projects you wish to display
* Use the IgnitionDeck->Orders->Add Order feature to add orders manually

[Upgrade](http://ignitiondeck.com/id/ignitiondeck-pricing/?utm_source=wprepo&utm_medium=link&utm_campaign=freemium) and you'll have access to the complete set of project shortcodes and full commerce functionality.

###No Theme Required###

IgnitionDeck Works with any WordPress theme, which means you don’t have to buy a new theme to get started.

###Free Crowdfunding Theme###

Download our [free crowdfunding theme](http://ignitiondeck.com/id/wordpress-crowdfunding-theme/?utm_source=wprepo&utm_medium=link&utm_campaign=freemium) and you’ll have even more options, including gorgeous crowdfunding project pages, project grids, and project archives.

**[Demo](http://demo.ignitiondeck.com/?utm_source=wprepo&utm_medium=link&utm_campaign=freemium)**

###Support###

Premium support is available via our [product forums](http://forums.ignitiondeck.com/?utm_source=wprepo&utm_medium=link&utm_campaign=freemium)

###Developers###

Grab code snippets, child theming guides, and API information on our [developer resources](http://ignitiondeck.com/id/resources?utm_source=wprepo&utm_medium=link&utm_campaign=freemium) page.

== Installation ==

[IgnitionDeck Crowdfunding Documentation](http://docs.ignitiondeck.com)

== Frequently Asked Questions ==

[IgnitionDeck FAQ](http://ignitiondeck.com/id/ignitiondeck-pricing#bigfaq)

== Upgrade Notice ==

== Screenshots ==

== Changelog ==

= 1.1.8 =

* use maybe_userialize on dashboard settings when setting WP roles to solve offset error

= 1.1.7 =

* Security Fix: Remove nopriv option for theme/extension activation and require that user can manage_options
* Security Fix: Update our media button priveleges in order to ensure that users can only edit media when on a project they own, or when creating a new project

= 1.1.6 =

* New function for validating and returning linkable URLs: id_validate_url($url)
* Early prep for integration with iThemes Exchange
* Port idc order lightbox functionality to ID Social

= 1.1.5 =

* Fix header error caused by last update

= 1.1.4 =

* Fix bug preventing admins from viewing media
* Minor design updates on extensions and themes admin pages
* Add documentation button for extensions/themes on respective admin pages

= 1.1.3 =

* (IDE) Add new roles and capabilities for the purposes of editing media on the front-end submission form

= 1.1.2 =

* Fix issue preventing WC and EDD levels from linking to lightbox

= 1.1.1 =

* Add checks for SSL so that APIs can be used with or without SSL

= 1.1.0 =

* Port admin styling from IDC/IDCF into IDF
* New generic lightbox methods for cross-platform compatibility
* Optimize loading of IDF scripts
* Begin work on cross-platform social sharing template

= 1.0.9 =

* Extensions can now be activated via the extensions menu
* Disable buttons for installed extensions
* Update extension description CSS 

= 1.0.8 =

* Add js for lite version so that adjustHeights is not undefined

= 1.0.7 =

* Add js function to resize text dynamically
* Disable lightbox submission if no levels are available
* Minor styling updates to lightbox

= 1.0.6 =

* Fix IgnitionDeck Crowdfunding download and activation script so that plugin can be automatically downloaded and activated
* Don't hide project settings to prevent issue accessing project settings menu
* Add option to automatically update to latest version of IgnitionDeck Crowdfunding

= 1.0.5 =

* Load lightbox via admin_enqueue in the event IDCF is not licensed
* Create new localized admin variables for ajax URL and site URL for use with admin javascript
* Modify menu text

= 1.0.4 =

* Update registration page link

= 1.0.3 =

* Hide Legacy IgnitionDeck Crowdfunding menus when non-Legacy platforms are enabled
* Do not modify href attributes that do not exist, such as when levels are sold out or closed
* Add functionality and integrate with upcoming IgnitionDeck Commerce pledge what you want feature
* Only load functionality of IgnitionDeck Crowdfunding is enabled and registered
* Add data-href attribute to lightbox levels that can be accessed when necessary
* Remove lightbox support for Legacy PWYW functionality

= 1.0.2 =

* Fix bug causing "scalar value as array" error if platform not saved

= 1.0.1 =

* Update directory paths for new IgnitionDeck Crowdfunding structure and menu items
* Introduce textdomain for purpose of translation/s
* Update po/mo files

= 1.0 =

* Initial Release