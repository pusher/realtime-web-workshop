<?php
require_once('pusher_config.php');
require_once('Pusher.php');
require_once('functions.php');

$pusher = get_pusher();

$channel_name = try_get_param('channel_name');
$socket_id = try_get_param('socket_id');

$user_id = get_user_id();
$presence_data = get_user_info();

$auth = $pusher->presence_auth($channel_name, $socket_id, $user_id, $presence_data);

send_auth_response( $auth );
?>