# Exercise 2 - Subscribe

Once you are connected to the source of data you need to indicate what you are subscribing to.

In this exercise we'll subscribe to a Pusher [channel](http://pusher.com/docs/channels) and demonstrate how we can test that the subscription has succeeded. We'll also update the UI when we recieve and update.

## Docs

<http://pusher.com/docs/client_api_guide/client_public_channels#subscribe>

## Steps

### Create markup for messages to appear

    <h2>Messages</h2>
    <ul id="messages" data-role="listview" class="ui-listview"></ul>

### Subscribe to `messages` channel

    var channel = pusher.subscribe('messages');

### Bind to the `new_message` event

    channel.bind( 'new_message', function( data ) {
        var li = $('<li class="ui-li ui-li-static ui-body-c"></li>');
        li.text( data.text );
        li.hide();
        $('#messages').prepend(li);
        li.slideDown();
    } );

### Test that the functionality works

   * Use the Event Creator. See <http://pusher.com/docs/debugging#event_creator>
   * Call the handler function directly
   
### Refactor the code so that in inline function is called `addMessage`:
     
    channel.bind( 'new_message', addMessage );
     
    function addMessage( data ) {
      var li = $('<li class="ui-li ui-li-static ui-body-c"></li>');
      li.text( data.text );
      li.hide();
      $('#messages').prepend(li);
      li.slideDown();
    }
     
### Test from the JavaScript console

Test by executing the following in your JavaScript console:

    addMessage( { text : "Hello from the console!" } );
   
Fake the event using `channel.emit` and executing in the JavaScript console:
   
    Pusher.instances[0].channel('messages').emit('new_message', {text: 'Hello via the console again'} );
         
### Just for fun (if time)

Create a new example that connects to Pusher and uses the `app_key` below:

    a71b8d8b7eef0ef6d98c
    
If you subscribe to any channel with a `tw-` and bind to the `tweet` event you'll start receiving tweets.

Use this to create your own little Twitter wall.

Consider how the UI should behave when you subscribe to a channel like `tw-hello`. Twitter hasn't nailed this yet. It's difficult to get the UX correct.