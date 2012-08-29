<?php
include 'Pusher.php';
include 'config.php';
include 'functions.php';

if( !user_logged_in() ) {
  header('HTTP/1.1 403 Forbidden');
  exit( 'Not authorized' );
}

$pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);

$socket_id = $_POST['socket_id'];
$channel_name = $_POST['channel_name'];

$auth = $pusher->socket_auth( $channel_name, $socket_id );
echo( $auth );
?>