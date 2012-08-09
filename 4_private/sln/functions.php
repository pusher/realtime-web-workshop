<?php
 function user_logged_in() {
   // most insecure auth check EVER!
   return strstr( $_SERVER['HTTP_REFERER'], 'auth=1' );
 }
 ?>