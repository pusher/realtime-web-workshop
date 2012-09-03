<?php
include 'config.php';
include 'Pusher.php';
include 'functions.php';

if( !user_logged_in() ) {
  header('HTTP/1.1 403 Forbidden');
  exit( 'Not authorized' );
}

$pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);

$socket_id = $_POST['socket_id'];
$channel_name = $_POST['channel_name'];

// user_id must be unique within the application
$user_id = get_username(); 
$user_info = array(
	'twitter_id' => get_twitter_id()
);

$auth = $pusher->presence_auth( $channel_name, $socket_id, $user_id, $user_info );
echo( $auth );
?>