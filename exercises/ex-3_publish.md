# Exercise 3 - Publish

The most obvious next step would be to publish data from JavaScript. But remember, you can't trust the client so in most cases you'll want to publish data via your server. That's what we'll do here.

## Docs

<http://pusher.com/docs/server_api_guide/server_publishing_events>

## Action

**Choose and get your [server library](http://pusher.com/docs/server_libraries).**

## Steps

We want to trigger/publish a `new_message` event on the `messages` channel. The event data should have a `text` property.

### New Message endpoint

Create a new endpoint to trigger your messages. If using PHP create a `new_message.php` file

### Pusher server library

Get the server library of your choice via: <http://pusher.com/docs/server_libraries> and reference it from your new endpoint. For PHP include the `Pusher.php` library in `new_message.php`

### Pusher app configuration

Create a config file and set your config variables from the Pusher dashboard. For PHP create a `config.php` file:
   
    <?php
      define('APP_ID', '');
      define('APP_KEY', '');
      define('APP_SECRET', '');
    ?>
   
Initialise the library as required. For PHP this is using:
   
    $pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);

### Triggering the event
         
Trigger/Publish a `new_message` event on the `messages` channel. The event data should have a `text` property. In PHP this looks as follows:
   
  	$pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);
		$pusher->trigger( 'messages', 'new_message', array('text' => 'hello world' ) );
   
Navigate to `new_message.php` and see the data appear in:

1. The Pusher debug console
2. The `window.console`
3. The UI

### Update the UI
     
Create an input field and button in the UI to send the data via the server to be validated to be published.

Create new markup:
  
    <label for="textarea" class="ui-hidden-accessible">Message:</label>
    <textarea name="user_message" id="user_message" placeholder="Message"></textarea>
    
    <a id="send_btn" href="index.html" data-role="button" data-theme="b">Send</a>     

Update the messages CSS:
    
    #messages {
      min-height: 100px;
      max-height: 200px;
      overflow: auto;
      margin-bottom: 20px;
      border-bottom: 2px solid #ccc;
    }

### Call the New Message endpoint with AJAX    
      
Create the JavaScript to handle the button click end send the data via AJAX:
  
    $( function() {
        $('#send_btn').click( handleClick );    
    } );
        
    function handleClick() {  
        var userMessageEl = $('#user_message');
        var message = $.trim( userMessageEl.val() );
        if( message ) {
        $.ajax( {
            url: 'new_message.php',
            type: 'post',
            data: {
            text: message
            },
            success: function() {
            userMessageEl.val('');
            }
        });
        }
        
        return false;
    }
    
Update `new_message.php` to get the `POST` `text` parameter that was posted. Put scaffolding in place to verify the data:
  
    $text = $_POST['text'];

    if( verify_message( $text ) ) {
      $pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);
      $pusher->trigger( 'messages', 'new_message', array('text' => $text) );
    }

    function verify_message() {
      return true;
    }

### That's it!

Since you've already subscribed to the channel and bound to the event in Exercise 2 you don't need to do anything else. It just works!