# Exercise 1 - Connect

In order to use Pusher, or any other client-based realtime web technology, you first need to connect to the source of data.

In this exercise we connect to Pusher and display a connection indicator to the user.

## Docs

* <http://pusher.com/docs/client_api_guide/client_connect>

## Steps

If you don't mind signing up for a free Pusher Sandbox plan do so. It'll mean you get to see some of the tooling available in Pusher. Otherwise, just use the key below.

### Include the Pusher script tag

    <script src="http://js.pusher.com/1.12/pusher.min.js"></script>

### Create a new Pusher instance
       
    // If you have your own account change the key below.
    var pusher = new Pusher( 'dc51aaac1ba21c033848' );

### How do we know we're connected?

* Pusher debug console: <http://pusher.com/docs/debugging#pusher_debug_console> (I can demo if you don't have a Pusher account)
* `Pusher.log`: <http://pusher.com/docs/debugging#pusher_logging>
   
### Detecting connection in code:

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
 