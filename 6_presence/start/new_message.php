<?php
include 'Pusher.php';
include 'config.php';
include 'functions.php';

if( !user_logged_in() ) {
  header('HTTP/1.1 403 Forbidden');
  exit( 'Not authorized' );
}

$text = $_POST['text'];

if( verify_message( $text ) ) {
  $pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);
  $pusher->trigger( CHANNEL_NAME, 'new_message', array('text' => $text) );
}

function verify_message() {
  return true;
}
?>