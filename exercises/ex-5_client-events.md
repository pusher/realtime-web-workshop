# Exercise 5: Client Events

Client events mean you bypass your own server and send events to other clients via Pusher. Doing this reduces latency between an event being triggered and being received. However, you still want some form of authentication so you can only send client events on authenticated channels; private or [presence](http://pusher.com/docs/presence_channels).

Even though they are authenticated the user could still do unwanted things so:

1. Consider if you'd be better sending events via your server - validate, authenticate (again) and sanitize
2. Upon receipt of the message client application should do validation and sanitization.

To help enforce these Pusher has imposed some restrictions and conventions:

* Client events can only be sent on authenticated channels.
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
        var data = { 'text': message };
        channel.trigger( 'client-new_message', data );

        addMessage( data );

        userMessageEl.val('');
      }

      return false;
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

We may want messages to go via the server so we can store them in the DB for later access. If that's the case we can leave the code as it is and instead use client events for events that add value, but aren't core to my application e.g. "Phil is typing" or other fun events. *You can of course use for more important events.*

#### Who is typing?

In order to know who's typing we need to add the idea of at least a username to the application. With a real application you would use an account system of some sort. But for now we'll just add a *very simple* way of setting a user name.

Add `getUsername` function to `server.js` which fetches a `user` parameter value from the query string:

    function getUsername( req ) {
      var username = req.query.user || 'unknown';
      return username;
    }

*Again, you really wouldn't do this in a real app.*
    
Get the user name using the new function and pass it as data to the view:

    app.get( '/', function ( req, res ) {
      var viewData = {
        appKey: config.pusher.appKey,
        channelName: config.pusher.channelName,
        username: getUsername( req )
      };
      
      res.render( 'index', viewData );
    } );

    
Add user data variable so data is accessible through JavaScript:

    var USER = {
      name: '<%= username %>',
    };

#### Send "Phil is typing"

Detect keydown events in the textarea and if state is not already 'client-typing' trigger a client event
  
    $('#user_message').keydown( userTyping );
    
    function userTyping() {
    
    }  
  
We don't want to send an event every time a user hits a key. We only want to sent an event when:

* The user initially enters some text
* If they stop typing we want to send the state - have they entered text or not

To ensure we don't send too many events we'll use a `setTimeout` to stop additional events being sent:    
  
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
                                          username: USER.name,
                                          typing: typing,
                                          enteredText: enteredText
                                        } );
    }

#### Verifying it's working

Check JavaScript console and Pusher Debug Console. Are events being triggered?

#### Update the UI

Add UI element to display typing activity (you may want to add a bit of styling):

    <div id="activity"></div>

Update code to receive client events by bind to `client-typing` events. Remember to check the content of the events - they are client events!

Update UI to show who is typing - could be multiples (this doesn't handle that!):
  
    channel.bind( 'client-typing', handleTyping );
    function handleTyping( data ) {
      if( validUsername( data.username ) === false ) {
        return; // ignore
      }

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

    function validUsername( username ) {
      return true; // update to actually validate
    }
      
Update AJAX callback to send clear user activity edge-case:

    success: function() {
      userMessageEl.val('');
      sendTypingEvent( false, false );
    }

***We now know how to use client events and understand what to consider when using them.***