# Exercise 5: Client Events

Client events mean you bypass your own server and send events to other clients via Pusher. Doing this reduces latency between events being triggered and being received. However, you still want some form of authentication so you can only send client events on authenticated channels; private or [presence](http://pusher.com/docs/presence_channels).

Even though they are authenticated the user could still do unwanted things so:

1. Consider if you'd be better sending events via your server - validate, authenticate (again) and sanitize
2. Upon receipt of the message client application should do validation and sanitization.

To help enforce these Pusher has imposed some restrictions and conventions:

* Client events have a 'client-' prefix to ensure you know you're receiving a client event.
* Client events are not received by the client that triggered the event.
* Client events are rated limited to 10 per second.

## Docs

* <http://pusher.com/docs/client_events>

## Steps - Change to use client events

### Enable client events in your app dashboard

You need to turn on client events for your application in the settings page.

### Events need a client- prefix

Change the event that is being bound to `client-new_message`:

    channel.bind( 'client-new_message', addMessage );

### You could: Update messages to use client events

#### Trigger new message Client events
      
Update the `handleClick` function to trigger a client event on the channel:

    function handleClick() {  
        var userMessageEl = $('#user_message');
        var message = $.trim( userMessageEl.val() );
        if( message ) {
            channel.trigger( 'client-new_message', { 'text': message } );
        }
    }

#### Handle client events

Ensure that upon receipt you verify and sanitize the text.

Ensure you don't insert the data directly as HTML into the page. Remember, you can't trust the contents of client events.

    channel.bind( 'client-new_message', addMessage );

    function addMessage( data ) {

      if( allowMessage( data.text ) === true ) {
        var li = $('<li class="ui-li ui-li-static ui-body-c"></li>');
        li.text( data.text );
        li.hide();
        $('#messages').append(li);
        li.slideDown();
      }
    }

    function allowMessage( text ) {
      // perform some checks
      return true;
    }


### User X is Typing

Since we want messages to go via the server so we can store them in the DB for later access. We won't change the existing code to use client events. Instead let's use client events for events that add value, but aren't core to my application e.g. "Phil is typing" or other fun events. *You can of course use for more important events.*

#### Who is typing?

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

#### Send "Phil is typing"

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

#### Verifying it's working

Check JavaScript console and Pusher Debug Console. Are events being triggered?

#### Update the UI

Add UI element to display typing activity:

    <div id="activity"></div>

Update code to receive client events by bind to `client-typing` events. Remember to check the content of the events - they are client events!

Update UI to show who is typing - could be multiples (this doesn't handle that!):
  
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