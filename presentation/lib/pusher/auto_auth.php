<?php
include 'functions.php';

$channel_name = try_get_param('channel_name');
if( $channel_name !== null ) {
  if( strpos($channel_name, 'private-') === 0 ) {
    require('private_auth.php');
  }
  else if( strpos($channel_name, 'presence-') === 0 ) {
    require('presence_auth.php');
  }
}
?>