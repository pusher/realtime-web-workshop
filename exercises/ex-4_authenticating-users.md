# Exercise 4 - Private Channels / Authenticating Users

When you want to restrict access to who can subscribe to a channel you use [private channels](http://pusher.com/docs/private_channels). They provide an easy way of giving your application the ability to decide who can and who can't subscribe to a channel by making it easy for you to hook into your existing authentication systems.

## Workshop Code refactoring

Some refactoring has taken place to clean up the structure of the app a bit.

**You don't need to do this. This is just for your information**

### Details

`index.html` renamed to `index.php`

`config.php` included in `index.php`

    <?php
      include 'config.php';
    ?>
    
`config.php` updated to define the channel name as it's used on the server when publishing messages and on the client when subscribing:

    define('CHANNEL_NAME', 'messages');
      
Update new_message.php to use `CHANNEL_NAME':

    $pusher->trigger( CHANNEL_NAME, 'new_message', array('text' => $text) );      
    
`APP_KEY` and `CHANNEL_NAME` made accessible as global JavaScript variable via `CONFIG.PUSHER` object:

    <script>
  	  var CONFIG = {
  	    PUSHER: {
  	      APP_KEY: "<?php echo( APP_KEY ); ?>",
  	      CHANNEL_NAME: "<?php echo( CHANNEL_NAME ); ?>"
  	    }
  	  };
  	</script>
  	
Update constructor to use `CONFIG`

    var pusher = new Pusher( CONFIG.PUSHER.APP_KEY );
      
Update the `pusher.subscribe` call to use the new `CHANNEL_NAME` variable:

    var channel = pusher.subscribe( CONFIG.PUSHER.CHANNEL_NAME );
        
Moved JavaScript into `js/app.js`

`style.css` moved into `css/style.css` 

## Docs

* <http://pusher.com/docs/client_api_guide/client_private_channels>
* <http://pusher.com/docs/authenticating_users>

## Steps

### Use a Private Channel
  
Change channel subscription to 'private-' prefix. If using PHP then this can can be made in `config.php`:

    define('CHANNEL_NAME', 'private-messages');

Run the application and have a look at the network tab in your browser development tools. 

* View auth call in the browser developer tools network tab.
* View JS Console logging which indicates authentication failure.

### Create an Authentication endpoint
  
Add auth endpoint. By default this will be `/pusher/auth` but you can set your own using `Pusher.channel_auth_endpoint`. If you are using PHP you can do the following and create an `auth.php` file located relative to your main application file:
  
    Pusher.channel_auth_endpoint = 'auth.php';
  
*Note:* You can bind to the `pusher:subscription_error` event if you wanted to detect subscription failures on the client.
  
Implement authentication with the help of the functionality supplied with the Pusher server library that you are using:

Get auth working without any actual authenticating the user against any application user database. In PHP this can be done as follows:
    
    <?php
      include 'Pusher.php';
      include 'config.php';
    
      $pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);
    
      $socket_id = $_POST['socket_id'];
      $channel_name = $_POST['channel_name'];
    
      $auth = $pusher->socket_auth( CHANNEL_NAME, $socket_id );
      echo( $auth );
    ?>

### Verify Authentication is working
        
You can check that the authentiction is working as expected in the following places:

* JavaScript console console: The output from `Pusher.log` will tell you.
* Pusher Debug Console: You'll see a successful subscription in the Pusher debug console.
* You could bind to `pusher:subscription_succeeded` if you like and check the subscription result in code.

### Optional (if time)
    
Change the code to allow/disallow the user:

Create a new functions.php
    
    <?php
      function user_logged_in() {
        // most insecure auth check EVER!
        return strstr( $_SERVER['HTTP_REFERER'], 'auth=1' );
      }
    ?>
         
Include this new file in the `auth.php` file.
    
Update `auth.php` to include `functions.php` and call:
    
    if( !user_logged_in() ) {
      header('HTTP/1.1 403 Forbidden');
      exit( 'Not authorized' );
    }
    
Update `new_message.php` (publish endpoint) to also do the auth check.