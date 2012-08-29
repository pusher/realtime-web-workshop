<?php
include 'config.php';
include_once 'functions.php';
?>

<!DOCTYPE html>
<html> 
	<head> 
	<title>My Page</title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js"></script>
	<script>
    var CONFIG = {
      PUSHER: {
        APP_KEY: "<?php echo( APP_KEY ); ?>",
        CHANNEL_NAME: "<?php echo( CHANNEL_NAME ); ?>"
      }
    };
	  
	  var USER = {
	    NAME: "<?php echo( get_username() ); ?>"
	  };
	</script>
	
	<link rel="stylesheet" href="css/styles.css" type="text/css" />
</head> 
<body> 

<div data-role="page">

	<div data-role="header">
		<h1>My Title</h1>
		<span class="connection-status"></span>
	</div><!-- /header -->

	<div data-role="content">
	  <h2>Messages</h2>
		<ul id="messages" data-role="listview" class="ui-listview"></ul>
		
    <label for="textarea" class="ui-hidden-accessible">Message:</label>
  	<textarea name="user_message" id="user_message" placeholder="Message"></textarea>
  	<div id="activity"></div>
  	
  	<a id="send_btn" href="index.html" data-role="button" data-theme="b">Send</a>

	</div><!-- /content -->

</div><!-- /page -->

<script src="http://js.pusher.com/1.12/pusher.min.js"></script>
<script src="js/app.js"></script>

</body>
</html>