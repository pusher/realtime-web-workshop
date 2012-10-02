<?php
require_once('pusher_config.php');
require_once('Pusher.php');
require_once('functions.php');

$pusher = get_pusher();

$channel_name = try_get_param('channel_name');
$socket_id = try_get_param('socket_id');

$auth = $pusher->socket_auth($channel_name, $socket_id);

send_auth_response($auth);
?>