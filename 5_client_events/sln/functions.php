<?php
 function user_logged_in() {
   // most insecure auth check EVER!
   return strstr( $_SERVER['HTTP_REFERER'], 'auth=1' );
 }
 
 function get_username() {
   $username = $_GET['user'];
   if( !$username ) {
     $username = uniqid('Guest_');
   }
   return $username;
 }
 ?>