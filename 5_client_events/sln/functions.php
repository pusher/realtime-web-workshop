<?php
 function user_logged_in() {
   // most insecure auth check EVER!
   return strstr( $_SERVER['HTTP_REFERER'], 'auth=1' );
 }
 
 function get_username() {
   $username = uniqid('Guest_');
   if( isset( $_GET[ 'user' ] ) ) {
     $username = $_GET[ 'user' ];
   }
   return $username;
 }
?>