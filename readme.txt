=== HELLDAP ===
Contributors: qoelhex
Tags: ldap, ad, auth
Requires at least: 0.71  
Tested up to: 3.6.2
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Auth LDAP / AD, Simple as HELL !

== Description ==

Just LDAP auth, no other features. A minimalistic approach to auth when you need just auth and no other hassles with it.

== Installation ==

1. Upload the plugin to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Edit the php file and fill the gaps in the configurations

== Frequently Asked Questions ==

= Editing PHP file for configuration? Really? Why not some form in Admin and save in the database? =

Auth is a delicate matter, if fails, can lock your site
and force you to debug a live site to discover what happened
with your passwords and users. 

Considering that auth is configured only once in an installation, 
HTML forms and database querys are unnecessary overheads and hassles.

== Changelog ==

1.0.4
Put the proper required WP version in readme

1.0.3
A more complete README with FAQ and Changelog

1.0.2
A better README

1.0.1
Some minor spelling corrections

1.0
Initial Release

