<?php
include '../config.php';
include '../../lib/pusher/Pusher.php';
include '../../lib/pusher/functions.php';

$to = $_POST['to'];
$from = $_POST['from'];
$count = $_POST['count'];
$toName = $_POST['toName'];
$fromName = $_POST['fromName'];

if( is_numeric($to) &&
    is_numeric($from) &&
    is_numeric($count) ) {
 $pusher = get_pusher();
 $pusher->trigger(DECKJS_CHANNEL_NAME,
                  'slide_change',
                  array(
                    'to' => $to,
                    'from' => $from,
                    'count' => $count,
                    'toName' => $toName,
                    'fromName' => $fromName
                  )
                 );
}
?>