<?php
/**
 * Plugin Name: Hell DAP
 * Plugin URI: http://helldap.etrupe.net
 * Description: Auth LDAP / AD, Simple As Hell
 * Version: 1.0
 * Author: Fábio Coelho
 * Author URI: http://tocadocoelho.blogspot.com.br
 * License: GPL2

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
**/

add_filter ( 'authenticate', 'helldap_auth', 30, 3 );
function helldap_auth($user, $username, $password) {
	if ($username == '' || $password == '')
		return new WP_Error ( 'denied', __ ( "<strong>ERROR</strong>: Bad login/pass" ) );
	
	// Configurations ---- START
	// Where to start to search in the LDAP tree, ex: OU=CONTEINER,DC=ORGANIZATION
	$base_dn = "";
	// LDAP url, pretty much, add server and port if necessary, ex: ldap://ad.mydomain.com
	$ldap_url = "ldap://";
	// Well configured ldap servers, won't allow you to do an anonymous search, 
	// so you need an user with search permissions in the directory
	// in the form of a Distinguished Name CN=login_with_search_perms,OU=CONTEINER,DC=ORGANIZATION
	// Let null for anonymous search.
	$bind_dn = null;
	// And this user needs a Pass
	$bind_passwd = null;
	// What attribute carries the login
	$user_search_attr = "cn";
	// Class of the user
	$user_class = "Person";
	// Where to take user attributes for new users
	$user_attr_givenName = "givenName";
	$user_attr_lastName = "sn";
	// domain to use to compose email for new users
	$email_domain = "";
	// Configurations ---- END
	
	$ldap_con = @ldap_connect ( $ldap_url );
	@ldap_set_option ( $ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3 );
	$ldap_bind = @ldap_bind ( $ldap_con, $bind_dn, $bind_passwd );
	if (! $ldap_bind) {
		$user = new WP_Error ( 'denied', __ ( "<strong>ERROR</strong>: LDAP Connection Failed" ) );
	}
	$result = @ldap_search ( $ldap_con, $base_dn, "(&($user_search_attr=$username)(objectClass=$user_class))", 
			array('dn', $user_attr_givenName,$user_attr_lastName,$user_attr_title) );
	if (! $result) {
		$user = new WP_Error ( 'denied', __ ( "<strong>ERROR</strong>: There's no such user in LDAP" ) );
		return true;
	} else {
		$entradas = ldap_get_entries ( $ldap_con, $result );
		$dn = $entradas [0] ["dn"] [0];
		$fn = $entradas [0] [$user_attr_givenName] [0];
		$sn = $entradas [0] [$user_attr_lastName] [0];
		$ldap_bind = @ldap_bind ( $ldap_con, $ldap_user, $password );
		if (! $ldap_bind) {
			$user = new WP_Error ( 'denied', __ ( "<strong>ERROR</strong>: Bad login/pass" ) );
		} else {
			$user = get_user_by ( 'login', $username );
			if (! $user) {
				$userdata = array (
						'user_email' => $username . "@" . $email_domain,
						'user_login' => $username,
						'first_name' => $fn,
						'display_name' => $fn . " " . $sn,
						'last_name' => $sn 
				);
				$new_user_id = wp_insert_user ( $userdata );
				$user = new WP_User ( $new_user_id );
			}
		}
	}
	return $user;
}
?>