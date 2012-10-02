<?php
include '../config.php';
include '../../lib/pusher/Pusher.php';
include '../../lib/pusher/functions.php';

$name = $_POST['name'];
$name = sanitize($name, 50);

$question = $_POST['question'];
$question = sanitize($question, 200);

$pusher = get_pusher();

$data = array('name' => $name, 'question' => $question);

$pusher->trigger(PRESENTATION_CHANNEL_NAME, 'question', $data);
?>