<?php
include 'Pusher.php';
include 'config.php';
include 'functions.php';

$text = $_POST['text'];

if( verify_message( $text ) ) {
  $pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);
  $pusher->trigger( 'messages', 'new_message', array('text' => $text) );
}

function verify_message() {
  return true;
}
?>