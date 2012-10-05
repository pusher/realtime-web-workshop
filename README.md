# Realtime Web Workshop - By Pusher

In the following exercises we'll gradually build a chat-style application that could also be used as a status update, activity stream, style application.

## Exercise 0 - Development Environments

### Requirements

#### Dev skills

It's expected that you have basic JavaScript and a backend technology knowlege (e.g. PHP, Ruby, node.js, C# .NET, Python).

#### Dev environment

1. A text editor
2. A web server - PHP, Ruby, Python, .NET, node.js *Note: Workshop uses PHP so if you choose to use your own backend tech there may be a bit of extra work required*
3. A web browser with developer tools
4. Exercise files from: https://github.com/pusher/realtime-web-workshop

   Via git using: `git clone git@github.com:pusher/realtime-web-workshop.git`

   Or zip file by downloading: <https://github.com/pusher/realtime-web-workshop/zipball/master>
   
   This repo contains the files required for each exercise. It contains a `start` folder with files that let you start from a point where the previous exercise has been completed. It also contains a `sln` folder with a proposed solution (then used as the `start` for the next exercise).
   
   After exercise 1 you may not want to use these files and simply continue to develop your existing application.

## Exercise 0.5 - WebSockets

The purpose of this exercise is to let you have a play with an interface that is as close to the native `WebSocket` interface as possible.

1. Go to: http://jsbin.com/iyavup/1/edit
2. Within the `init` function you can see a `SimpleWebSocket` object has been created.
3. Within the `ws.onopen` handler function use the `log` function to display when the connection has been established.
4. Within the `ws.onmessage` function use the `log` function to output the contents of the event data
5. *The fun part* when the `send_btn` is clicked use the `ws.send` function to send data to anybody else that is connected. The `sendSomething` function is already set up to handle the button click event and get the text from the `textarea`.

## Exercise 1 - Connect

In order to use Pusher, or any other client-based realtime web technology, you first need to connect to the source of data.

In this exercise we connect to Pusher and display a connection indicator to the user.

### Docs

* <http://pusher.com/docs/client_api_guide/client_connect>

### Steps

If you don't mind signing up for a free Pusher Sandbox plan do so. It'll mean you get to see some of the tooling available in Pusher. Otherwise, just use the key below.

Include the Pusher script tag

    <script src="http://js.pusher.com/1.12/pusher.min.js"></script>

Create a new Pusher instance
       
    // If you have your own account change the key below.
    var pusher = new Pusher( '43549c6d4c7248a2f848' );

How do we know we're connected?

* Pusher debug console: <http://pusher.com/docs/debugging#pusher_debug_console> (I can demo if you don't have a Pusher account)
* `Pusher.log`
   
Detecting connection in code:

Create element for connection status indicator

    <div class="connection-status"></div>
    
Create `styles.css` for CSS

    .connection-status {
      position: fixed;
      display: block;
      right: 0;
      top: 0;
      background-color: red;
      width: 40px;
      height: 40px;
    
      -webkit-border-radius: 20px;
      -moz-border-radius: 20px;
      border-radius: 20px;
    }
    
    .connection-status.connecting {
      background-color: orange;
    }
    
    .connection-status.connected {
      background-color: green;
    }

Include CSS file       
    
    <link rel="stylesheet" href="styles.css" type="text/css" />

Bind to connection events

    pusher.connection.bind('state_change', function( change ) {
      var el = $('.connection-status');
      el.removeClass( change.previous );
      el.addClass( change.current );
    });
        
## Exercise 2 - Subscribe

Once you are connected to the source of data you need to indicate what you are subscribing to.

In this exercise we'll subscribe to a Pusher [channel](http://pusher.com/docs/channels) and demonstrate how we can test that this subscription has succeeded. We'll also update the UI when we recieve and update.

### Docs

<http://pusher.com/docs/client_api_guide/client_public_channels#subscribe>

### Steps

Create markup for messages to appear

    <h2>Messages</h2>
    <ul id="messages" data-role="listview" class="ui-listview"></ul>

Subscribe to `messages` channel

    var channel = pusher.subscribe('messages');

Bind to the `new_message` event

    channel.bind( 'new_message', function( data ) {
        var li = $('<li class="ui-li ui-li-static ui-body-c"></li>');
        li.text( data.text );
        li.hide();
        $('#messages').prepend(li);
        li.slideDown();
    } );

Test that the functionality works

   * Use the Event Creator. See <http://pusher.com/docs/debugging#event_creator>
   * Call the handler function directly
   
Refactor the code so that in inline function is called `addMessage`:
     
    channel.bind( 'new_message', addMessage );
     
    function addMessage( data ) {
      var li = $('<li class="ui-li ui-li-static ui-body-c"></li>');
      li.text( data.text );
      li.hide();
      $('#messages').prepend(li);
      li.slideDown();
    }
     
Test by calling:

    addMessage( { text : "Hello from the console!" } );
   
Fake the event using `channel.emit`
   
    Pusher.instances[0].channel('messages').emit('new_message', {text: 'Hello via the console again'} );
         
### Just for fun (if time)

Create a new example that connects to Pusher and uses the `app_key` below:

    a71b8d8b7eef0ef6d98c
    
If you subscribe to any channel with a `tw-` and bind to the `tweet` event you'll start receiving tweets.

Use this to create your own little Twitter wall.

Consider how the UI should behave when you subscribe to a channel like `tw-hello`. Twitter hasn't nailed this yet. It's difficult to get the UX correct.

## Exercise 3 - Publish

The most obvious next step would be to publish data from JavaScript. But remember, you can't trust the client so in most cases you'll want to publish data via your server. That's what we'll do here.

### Docs

<http://pusher.com/docs/server_api_guide/server_publishing_events>

### Action

**Choose and get your [server library](http://pusher.com/docs/server_libraries).**

### Steps

Trigger/Publish a `new_message` event on the `messages` channel. The event data should have a `text` property.

Create a new endpoint to trigger your messages. If using PHP create a `new_message.php` file

Get the server library of your choice via: <http://pusher.com/docs/server_libraries> and reference it from your new endpoint. For PHP include the `Pusher.php` library in `new_message.php`

Create a config file and set your config variables from the Pusher dashboard. For PHP create a `config.php` file:
   
    <?php
      define('APP_ID', '');
      define('APP_KEY', '');
      define('APP_SECRET', '');
    ?>
   
Initialise the library as required. For PHP this is using:
   
    $pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);
         
Trigger/Publish a `new_message` event on the `messages` channel. The event data should have a `text` property. In PHP this looks as follows:
   
    $pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);
$pusher->trigger( 'messages', 'new_message', array('text' => 'hello world' ) );
   
Navigate to `new_message.php` and see the data appear in:

1. The Pusher debug console
2. The `window.console`
3. The UI
     
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

## Exercise 4 - Private Channels / Authenticating Users

When you want to restrict access to who can subscribe to a channel you use [private channels](http://pusher.com/docs/private_channels). They provide an easy way of giving your application the ability to decide who can and who can't subscribe to a channel by making it easy for you to hook into your existing authentication systems.

### Workshop Code refactoring

Some refactoring has taken place to clean up the structure of the app a bit.

*You don't need to do this. This is just for your information*

#### Details

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

### Docs

* <http://pusher.com/docs/client_api_guide/client_private_channels>
* <http://pusher.com/docs/authenticating_users>

### Steps
  
Change channel subscription to 'private-' prefix. If using PHP then this can can be made in `config.php`:

    define('CHANNEL_NAME', 'private-messages');

Run the application and have a look at the network tab in your browser development tools. 

* View auth call in the browser developer tools network tab.
* View JS Console logging which indicates authentication failure.
  
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
        
Show auth was successful:

* JS console
* Pusher Debug Console
* You could bind to `pusher:subscription_succeeded` if you like

#### Optional (if time)
    
Change the code to allow/disallow the user:

Create a new functions.php
    
    <?php
      function user_logged_in() {
        // most insecure auth check EVER!
        return strstr( $_SERVER['HTTP_REFERER'], 'auth=1' );
      }
    ?>
         
include functions.php
    
Update `auth.php` to include `functions.php` and call:
    
    if( !user_logged_in() ) {
      header('HTTP/1.1 403 Forbidden');
      exit( 'Not authorized' );
    }
    
Update `new_message.php` (publish endpoint) to also do the auth check

Update the server to publish to the `private-` channel
    
## Exercise 5: Client Events

Client events mean you bypass your own server and send events to other clients via Pusher. Doing this reduces latency between events being triggered and being received. However, you still want some form of authentication so you can only send client events on authenticated channels; private or [presence](http://pusher.com/docs/presence_channels).

You need to turn on client events for your application in the settings page.

* Client events have a 'client-' prefix to ensure you know you're receiving a client event.
* Client events are not received by the client that triggered the event.
* Client events are rated limited to 10 per second.

* Even though they are authenticated the user could still do unwanted things so:
  1. Consider if you'd be better sending events via your server - validate, authenticate (again) and sanitize
  2. Upon receipt of the message client application should do validation and sanitization.

### Docs

* <http://pusher.com/docs/client_events>

### Steps - Change to use client events

Enable client events in your app dashboard

Change the event that is being bound to `client-new_message`:

    channel.bind( 'client-new_message', addMessage );
      
Update the `handleClick` function to trigger a client event:

    function handleClick() {  
        var userMessageEl = $('#user_message');
        var message = $.trim( userMessageEl.val() );
        if( message ) {
            channel.trigger( 'client-new_message', { 'text': message } );
        }
    }

Ensure that upon receipt you verify and sanitize the text.

Ensure you don't insert the data directly as HTML into the page. Remember, you can't trust the contents of client events.


### Steps - User X is Typing

Enable client events in your app dashboard

Since we want messages to go via the server so we can store them in the DB for later access. We won't change the existing code to use client events. Instead let's use client events for events that add value, but aren't core to my application e.g. "Phil is typing" or other fun events. *You can of course use for more important events.*

Add `get_username` function to functions.php

        function get_username() {
          $username = uniqid('Guest_');
          if( isset( $_GET[ 'user' ] ) ) {
            $username = $_GET[ 'user' ];
          }
          return $username;
        }
    
Include functions.php in index.php

    include_once 'functions.php';
    
Add user data variable so data is accessible through JavaScript:

    var USER = {
        NAME: "<?php echo( get_username() ); ?>"
    };

Send "Phil is typing"

Detect mousedown events in the textarea and if state is not already 'client-typing' trigger a client event
  
    $('#user_message').mousedown( userTyping );
    
    function userTyping() {
    
    }  
  
`setInterval` to clear down if triggered and on further mouse down if state is already typing clear and restart timeout    
  
    var typingTimeout = null;
    function userTyping() {

        var el = $( this );

        if( !typingTimeout ) {
          var textEntered = ( $.trim( el.val() ).length > 0 );
          sendTypingEvent( true, textEntered );
        }
        else {
          window.clearTimeout( typingTimeout );
          typingTimeout = null;
        }

        typingTimeout = window.setTimeout( function() {
          var textEntered = ( $.trim( el.val() ).length > 0 );
          sendTypingEvent( false, textEntered );
          typingTimeout = null;
        }, 3000);
    }

    function sendTypingEvent( typing, enteredText ) {
      channel.trigger( 'client-typing', {
                                          username: USER.NAME,
                                          typing: typing,
                                          enteredText: enteredText
                                        } );
    }
  
Check JavaScript console and Pusher Debug Console. Are events being triggered?

Add UI element to display typing activity:

    <div id="activity"></div>

Update code to receive client events:

bind to `client-typing` events

Check the content of the events - they are client events!

Update UI to show who is typing - could be multiples
  
    channel.bind( 'client-typing', handleTyping );
    function handleTyping( data ) {
      if( data.typing ) {
        $("#activity").text( data.username + ' is typing' );
      }
      else if( data.enteredText ) {
        $("#activity").text( data.username + ' has entered text' );
      }
      else {
        $("#activity").text( '' );
      }
    }
      
Update AJAX callback to send clear user activity edge-case:

    success: function() {
      userMessageEl.val('');
      sendTypingEvent( false, false );
    }         