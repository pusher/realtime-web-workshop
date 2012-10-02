<?php
session_start();

function get_pusher() {
  return new Pusher(APP_KEY, APP_SECRET, APP_ID);
}

function get_user_id() {
  $data = get_user_data();
  
  return $data['id'];
}

function get_user_info() {
  $data = get_user_data();
  
  return $data['info'];
}

function save_user_info($info) {
  $unique_id = uniqid();
  
  $data = array(
    'id' => $unique_id,
    'info' => $info
  );
  
  save_user_data($data);
}

function get_user_data() {
  if( isset($_SESSION['user_data']) === false ){
    $unique_id = uniqid();
    $data = array(
      'id' => $unique_id,
      'info' => array('name' => 'Guest_' . $unique_id)
    );
    
    save_user_data($data);
  }
  
  return $_SESSION['user_data'];
}

function save_user_data($data) {
  $_SESSION['user_data'] = $data;
}

function set_stage($stage) {
  $_SESSION['stage'] = $stage;
}

function get_stage() {
  return ( $_SESSION['stage']? $_SESSION['stage'] : 'title' );
}

function clear_user_data() {
  session_unset();
}

function sanitize($value, $max_chars) {
  $value = substr($value, 0, $max_chars);
  $value = htmlentities($value);
  return $value;
}

function try_get_param($name) {
  $value = null;
  if( isset($_POST[$name]) === true ) {
    $value = $_POST[$name];
  }
  else if( isset($_GET[$name]) === true ) {
    $value = $_GET[$name];
  }
  return $value;
}

function send_auth_response($auth) {
  if( isset( $_GET['callback'] ) === true ) {
    $callback = str_replace('\\', '', $_GET['callback']);
    echo($callback . '(' . $auth . ');');
  }
  else {
    echo $auth;
  }
}
?>