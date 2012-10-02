<?php
include '../config.php';
include '../../lib/pusher/Pusher.php';
include '../../lib/pusher/functions.php';

$name = $_POST['my_name'];
$name = sanitize($name, 50);

$tech = $_POST['favourite_technology'];
$tech = sanitize($tech, 50);

$pusher = get_pusher();

$data = array('name' => $name, 'tech' => $tech);

save_user_info($data);
set_stage('wait');

$pusher->trigger(PRESENTATION_CHANNEL_NAME, 'introduction', $data);

$user_data = get_user_data();

header('Content-Type: application/javascript');
echo( json_encode( $user_data ) );
?>