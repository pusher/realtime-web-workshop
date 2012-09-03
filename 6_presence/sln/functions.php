<?php
	 function user_logged_in() {
	   // most insecure auth check EVER!
	   return strstr( $_SERVER['HTTP_REFERER'], 'auth=1' );
	 }
	 
	 function get_username() {
	   return get_value( 'user', uniqid('Guest_') );
	   
	 }

	 function get_twitter_id() {
	 		return get_value( 'twitter', null );
	 }

	 function get_value( $key, $default_value = null ) {
	   $value = $default_value;
	 		if( isset( $_SESSION[ $key ] ) ) {
	 			$value = $_SESSION[ $key ];
	 		}
	 		else if( isset( $_GET[ $key ] ) ) {
	 			$value = $_GET[ $key ];
	 			$_SESSION[ $key ] = $value;
	 		}
	 		return $value;
	 }
?>