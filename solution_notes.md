http://www.leggetter.co.uk/pres/at-t_bootstrap/pres/

## Prep

* Download github code and make available via http://localhost/bootstrap-pusher
* Start Chrome as Demo User
  * Open http://www.leggetter.co.uk/pres/at-t_bootstrap/pres/ or
  * http://localhost/pusher-dev/git/pusher-presentations/deck-js/at-t_bootstrap/pres/

## Webex

* Share applications:
  * Chrome - enter Demo User profile
  * iOS simulator
  * Textmate
* Mute yourself
* Mute on entry
* Welcome note:

        Welcome to the AT&T webinar: Adding Realtime functionality to any web app in minutes.
        
        Pusher - Powering the Realtime web
        
        Regards,
        Phil @leggetter

## Intro

* Welcome to the AT&T Bootstrap week webinar on …
* A bit about me…
  * 11 years realtime web. 
  * Passionate about realtime web technologies
    * fun to develop with
    * great user benefits
    * business benefits

## Before we start

* Explain client & server (WebSockets and REST)
* Show Pusher client & server libraries
* We're going to use the CDN hosted JavaScript library
* Mobile focused - we'll use jQuery mobile
* Debugging/Tooling - but we'll cover that as we go
* Of course, we need to sign up for Pusher
* Access http://localhost/bootstrap-pusher

## Ex 1. Connect

1. Include the Pusher script tag
2. Create a new Pusher instance
3. How do we know we're connected?
   * Pusher debug console
   * `Pusher.log`
   * `pusher.connection.bind`
   
   ### Create element for connection status indicator
    
       <div class="connection-status"></div>
       
   ### Create `style.css` for CSS
   
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
   
   ### Include CSS file       
       
       <link rel="stylesheet" href="styles.css" type="text/css" />

   ### Bind to connection events

        var pusher = new Pusher('');
        pusher.connection.bind('state_change', function( change ) {
          var el = $('.connection-status');
          el.removeClass( change.previous );
          el.addClass( change.current );
        });
          

## Ex 2. Subscribe

1. Create markup for messages to appear

      <h2>Messages</h2>
    	<ul id="messages" data-role="listview" class="ui-listview"></ul>

2. Subscribe to `messages` channel

       var channel = pusher.subscribe('messages');

3. Bind to the `new_message` event

        channel.bind( 'new_message', function( data ) {
            var li = $('<li class="ui-li ui-li-static ui-body-c"></li>');
            li.text( data.text );
            li.hide();
            $('#messages').prepend(li);
            li.slideDown();
        } );

4. Test that the functionality works
   * Use the Event Creator
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
     
     Test by calling `addMessage( { text : "Hello from the console!" } );`
   
   * Fake the event using `channel.emit`
   
         Pusher.instances[0].channel('messages').emit('new_message', {text: 'Hello via the console again'} );  


## Ex 3. Publish

1. Trigger/Publish a `new_message` event on the `messages` channel. The event data should have a `text` property.

   * Create a `new_message.php` file
   * Get and include the `Pusher.php` library in `new_message.php`
   * Create a `config.php` file and set config variables from Pusher dashboard
   *       $pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);
   * Trigger/Publish a `new_message` event on the `messages` channel. The event data should have a `text` property
   * Navigate to `new_message.php` and see the data appear in:
     1. The Pusher debug console
     2. The `window.console`
     3. The UI
     
2. Create an input field and button in the UI to send the data via the server to be validated to be published.

  * Create new markup:
  
      <label for="textarea" class="ui-hidden-accessible">Message:</label>
    	<textarea name="user_message" id="user_message" placeholder="Message"></textarea>

    	<a id="send_btn" href="index.html" data-role="button" data-theme="b">Send</a>     

  * Update the messages CSS:
      
        #messages {
          min-height: 100px;
          max-height: 200px;
          overflow: auto;
          margin-bottom: 20px;
          border-bottom: 2px solid #ccc;
        }
      
  * Create the JavaScript to handle the button click end send the data via AJAX:
  
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
      
  * Update `new_message.php` to get the `POST` `text` parameter that was posted. Put scaffolding in place to verify the data:
  
        $text = $_POST['text'];

        if( verify_message( $text ) ) {
          $pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);
          $pusher->trigger( 'messages', 'new_message', array('text' => $text) );
        }

        function verify_message() {
          return true;
        }
        
## Ex 4. Private Channels

* Say we wan to restrict who can subscribe to data we publish to our application.
* We could do this by making our channel name generated and cryptic - guid/uuid. But, this means we have to store the channel name somewhere for reference and it also means that once it's been exposed we can't reuse it.
* So, we've created an authentication mechanism to give your application control over who can subscribe to channels.
* Private channels
* When the `pusher.subscribe` function is called the library makes an AJAX (or JSONP) call to an authentication endpoint within your application.
* The response from that application determines if the user is allowed to subscribe to the channel.

### Note: Some refactoring has taken place to clean up the structure of the app a bit:

* index.html renamed to index.php
* config.php included

    <?php
    include 'config.php';
    ?>
    
* config.php updated to define the channel name as it's used on the server when publishing messages and on the client when subscribing:

      define('CHANNEL_NAME', 'messages');
      
* Update new_message.php to use `CHANNEL_NAME':

      $pusher->trigger( CHANNEL_NAME, 'new_message', array('text' => $text) );      
    
* APP_KEY and CHANNEL_NAME_ made accessible as global JavaScript variable via `CONFIG.PUSHER` object:

    <script>
  	  var CONFIG = {
  	    PUSHER: {
  	      APP_KEY: "<?php echo( APP_KEY ); ?>",
  	      CHANNEL_NAME: "<?php echo( CHANNEL_NAME ); ?>"
  	    }
  	  };
  	</script>
  	
* Update constructor to use CONFIG

      var pusher = new Pusher( CONFIG.PUSHER.APP_KEY );
      
* Update the `pusher.subscribe` call to use the new `CHANNEL_NAME` variable:

      var channel = pusher.subscribe( CONFIG.PUSHER.CHANNEL_NAME );
        
* Moved JavaScript into `js/app.js`
* style.css moved into `css/style.css` 
  
1. * Change channel subscription to 'private-' prefix in config.php

         define('CHANNEL_NAME', 'private-messages');

   * Show auth call in network tab.
   * Show JS Console logging which indicates authentication failure.
  
2. Add auth endpoint of `auth.php`. Set `Pusher.channel_auth_endpoint`.
  
       Pusher.channel_auth_endpoint = 'auth.php';
  
  * Don't return a value, just create PHP.
  * Show logging in JavaScript console = Failure. You could bind to the `pusher:subscription_error` event if you wanted to.
  
3. Implement authentication using Pusher.php.
   * Get auth working without any actual checking:
    
          <?php
          include 'Pusher.php';
          include 'config.php';

          $pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);

          $socket_id = $_POST['socket_id'];
          $channel_name = $_POST['channel_name'];

          $auth = $pusher->socket_auth( CHANNEL_NAME, $socket_id );
          echo( $auth );
          ?>
        
    * Show auth was successful:
      * JS console
      * Pusher Debug Console
      * You could bind to `pusher:subscription_succeeded` if you like
    
4. Provide example of auth allow/disallow

   * Create a new functions.php
    
         <?php
         function user_logged_in() {
           // most insecure auth check EVER!
           return strstr( $_SERVER['HTTP_REFERER'], 'auth=1' );
         }
         ?>
         
    * include functions.php
    
    * Update auth.php to include functions.php and call:
    
          if( !user_logged_in() ) {
            header('HTTP/1.1 403 Forbidden');
            exit( 'Not authorized' );
          }
    
    * Update new_message.php to also do the auth check
    * Update the server to publish to the `private-` channel
    
  
## Ex 5. Client Events

* Client events mean you bypass your own server and send events to other clients via Pusher.
* Doing this reduces latency between events being triggered and being received.
* However, you still want some form of authentication so you can only send client events on authenticated channels: private (or presence, which we'll cover later).
* You need to turn on client events for your application.

* Client events have a 'client-' prefix to ensure you know you're receiving a client event.
* Client events are not received by the client that triggered the event.
* Client events are rated limited to 10 per second.

* Even though they are authenticated the user could still do unwanted things so:
  1. Consider if you'd be better sending events via your server - validate, authenticate (again) and sanitize
  2. Upon receipt of the message in your client application do validation and sanitization.
    
* Show client events being enabled in the app dashboard

* Since I want messages to go via the server so I can store them in the DB for later access I'm not going to change the existing code to use client events. Instead I'm going to use client events for events that add value, but aren't core to my application e.g. "Phil is typing" or other fun events. You can of course use for more important events.

* Add `get_username` function to functions.php

    function get_username() {
      $username = uniqid('Guest_');
      if( isset( $_GET[ 'user' ] ) ) {
        $username = $_GET[ 'user' ];
      }
      return $username;
    }
    
* Include functions.php in index.php

    include_once 'functions.php';
    
* Add user data variable so data is accessible through JavaScript:

    var USER = {
	    NAME: "<?php echo( get_username() ); ?>"
	  };

* Send "Phil is typing"
  * Detect mousedown events in the textarea and if state is not already 'client-typing' trigger a client event
  
        $('#user_message').mousedown( userTyping );
        
        function userTyping() {
          
        }  
  
  * setInterval to clear down if triggered
  * on further mouse down if state is already typing clear and restart timeout    
  
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
  
* Check JavaScript console and Pusher Debug Console

* Add UI element

      <div id="activity"></div>

* Update code to receive client events
  * bind to `client-typing` events
  * Check the content of the events - they are client events!
  * Update UI to show who is typing - could be multiples
  
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
      
* Update AJAX callback to sent clear user activity edge-case:

      success: function() {
        userMessageEl.val('');
        sendTypingEvent( false, false );
      }    
  
## Ex6. Presence

* Presence provide a way of determining who is subscribed to a channel. They also provide events whenever a user joins or leaves a channel.
* Presence are extensions of Private channels so require authentication.
* Part of the authentication process is to give your app the ability to provide additional information about the user e.g. username

* Use presence to see who's in the app.

* Update the app to use a `presence-` prefix in config.php

      define('CHANNEL_NAME', 'presence-messages');

  * Check JS console and Pusher Debug Console to see what's happening
    * Syntax error right now.

* Update `auth.php` to handle presence authentication.

      $user_id = get_username(); 
      $user_info = array(
        'twitter_id' => get_twitter_id()
      );

      $auth = $pusher->presence_auth( $channel_name, $socket_id, $user_id, $user_info );

* As above, we'll need a new `get_twitter_id()` function:

       function get_twitter_id() {
         $twitter_id = null;
         if( isset( $_GET[ 'twitter' ] ) ) {
           $twitter_id = $_GET[ 'twitter' ];
         }
         return $twitter_id;
       }

* run app with `user` and `twitter` in URL e.g. http://localhost/pusher-dev/git/realtime-web-workshop/6_presence/sln/?auth=1&user=Phil&twitter=leggetter
  * Information not available since it's only available on initial page load. We need to store in user session.

* Add session support

  * Update `config.php` - top line:

        session_start();

  * Update `functions.php` to use `$_SESSION` = refactor code:

         function get_username() {
           return get_value( 'user', uniqid('Guest_') );
           
         }

         function get_twitter_id() {
            return get_value( 'twitter', null );
         }

         function get_value( $key, $default_value = null ) {
           $value = $default_value;
            if( isset( $_SESSION[ $key ] ) ) {
              $value = $_SESSION[ $key ];
            }
            else if( isset( $_GET[ $key ] ) ) {
              $value = $_GET[ $key ];
              $_SESSION[ $key ] = $value;
            }
            return $value;
         }

  * Not saved in SESSION until accessed so also access `twitter` in `USER` JavaScript variable.

          var USER = {
            NAME: "<?php echo( get_username() ); ?>",
            TWITTER: "<?php echo( get_twitter_id() ); ?>"
          };

  * Show debug console. See `pusher:subscription_succeeded` event. We can `bind` to that event on the channel to receive an initial list of users subscribed to the channel.

* Create area of UI to show 'Online Users'

  * index.php - add col divs:

        <div class="col1">
          <h2>Messages</h2>
          <ul id="messages" data-role="listview" class="ui-listview"></ul>
            
          <label for="textarea" class="ui-hidden-accessible">Message:</label>
          <textarea name="user_message" id="user_message" placeholder="Message"></textarea>
          <div id="activity"></div>
            
          <a id="send_btn" href="index.html" data-role="button" data-theme="b">Send</a>
        </div>

        <div class="col2">
          <h3>Online</h3>

          <ul id="users">
            <li>
              <img src="http://twitter.com/api/users/profile_image/leggetter" alt="Phil" />
            </li>                     
          </ul>

        </div>

  * and styles.css updated:

        .col1 {
          float: left;
          width: 75%;
        }

        .col2 {
          float: left;
          width: 25%;
          text-align: center;
        }

        .col2 ul {
          padding: 0;
          list-style: none;
        }


* Bind to `pusher:subscription_succeeded` for initial list

  * app.js to bind and display in UI

        channel.bind( 'pusher:subscription_succeeded', getOnlineUsers );

        function getOnlineUsers( members ) {
          members.each( addMember );
        }

        function addMember( member ) {
          var img = $( '<img>' )
          img.attr( 'alt', member.id )
          if( member.info.twitter_id ) {
            img.attr( 'src', 'http://twitter.com/api/users/profile_image/' + member.info.twitter_id );
          }

          var li = $( '<li>' );
          li.attr( 'title', member.id )
          li.append( img );
          $( '#users' ).append( li );
        }

  * Update style.css:

        #users li {
          height: 48px;
          line-height: 48px;
        }

        #users li img {
          width: 48px;
          height: 48px;
        }

* Bind to `pusher:member_added`

      channel.bind( 'pusher:member_added', addMember );

* Bind to `pusher:member_removed`

      channel.bind( 'pusher:member_removed', removeMember );

      function removeMember( member ) {
        var li = $( '#users li[title=' + member.id + ']');
        li.slideUp( function() { li.remove() } );
      }

## Ex.7 WebHooks

* WebHooks are used to expose some of the Pusher internal working and state about your application.
* They offer a way of having an endpoint called whenever certain application events occur in Pusher
  * Channel existence: Channel occupied and vacated
  * Presence channel events: member_addeda and member_removed

* To use WebHooks you need to turn them on in your applications Pusher dashboard
  * Show turning on settings
  * To start off with use Requestb.in as the endpoint - we'll show this in use soon.

* Tooling
  * Developing with WebHooks can be quite tricky. Luckily there are a number of tools that can make this experience easier:
    * Pusher debug console:
      * Open up debug console in new Window: http://app.pusherapp.com/apps/25469/debug
      * Navigate to API access page (http://app.pusherapp.com/apps/25469/api_access)
      * Will see 'WebHook Sent' event
      * Also, close and then re-open app - will see events
    * Requestb.in - http://requestb.in/
      * View Requestb.in results - will see POST events
    * localtunnel
      * For windows version see: http://stackoverflow.com/questions/3271441/something-compareable-to-localtunnel-for-windows
      * We'll use this during development.

* Create webhook.php

TODO: complete WebHooks




