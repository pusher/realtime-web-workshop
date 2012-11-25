# Exercise 4 - Private Channels / Authenticating Users

When you want to restrict access to who can subscribe to a channel you use [private channels](http://pusher.com/docs/private_channels). They provide an easy way of giving your application the ability to decide who can and who can't subscribe to a channel by making it easy for you to hook into your existing authentication systems.

## Workshop Code refactoring

We've been trying to follow best practices throughout the workshop. But our JavaScript is still in our view and would be better in an external JavaScript file. So, before we get started with this exercise let's move as much JavaScript out of `views/index.html` and put it into `public/js/app.js`.

Remember that the `appKey` and `channelName` are passed to the view so we'll need to expose that to the the code in `js/app.js`. We'll have to do that using a global variable. So, expose the config and include the new `js/app.js` file in `views/index.html`:

    <script src="http://js.pusher.com/1.12/pusher.min.js"></script>
    <script>
      var CONFIG = {
        pusher: {
          appKey: '<%= appKey %>',
          channelName: '<%= channelName %>'
        }
      };
    </script>
    <script src="js/app.js"></script>

In `js/app.js` we'll wrap the existing code in a closure and pass in the dependencies.

    ( function( window, Pusher, $, config ) {
      // existing code
    })( window, window['Pusher'], jQuery, CONFIG ); 

This includes the `window` object, the `Pusher` reference, `jQuery` and the `CONFIG` we've just created.  We'll use the `config` when creating our `Pusher` instance and subscribing the channel:

    var pusher = new Pusher( config.pusher.appKey );

    var channel = pusher.subscribe( config.pusher.channelName );

Now that we've finished with this refactoring we can update the application to use a private channel and authenticate the user.    

## Docs

* <http://pusher.com/docs/client_api_guide/client_private_channels>
* <http://pusher.com/docs/authenticating_users>

## Steps

### Use a Private Channel
  
Change channel subscription to 'private-' prefix. Since we have this defined in our config we can change it there:

    {
        "pusher": {
            "channelName": "private-messages"
        }
    }

Run the application and have a look at the network tab in your browser development tools. 

* View auth call in the browser developer tools network tab.
* View JS Console logging which indicates authentication failure.

### Create an Authentication endpoint
  
Add auth endpoint. By default this will be `/pusher/auth` but you can set your own using `Pusher.channel_auth_endpoint`.

    app.post( '/pusher/auth', function( req, res ) {

    } );
  
*Note:* You can bind to the `pusher:subscription_error` event if you wanted to detect subscription failures on the client.

### Authenticate the request

Implement authentication with the help of the functionality supplied with the Pusher server library for the moment we won't actually authenticate the user in any way:

  app.post( '/pusher/auth', function( req, res ) {
    var socketId = req.body.socket_id;
    var channelName = req.body.channel_name;

    var auth = pusher.auth( socketId, channelName );

    res.send( auth );
  } );

### Verify Authentication is working
        
You can check that the authentiction is working as expected in the following places:

* JavaScript console console: The output from `Pusher.log` will tell you.
* Pusher Debug Console: You'll see a successful subscription in the Pusher debug console.
* You could bind to `pusher:subscription_succeeded` if you like and check the subscription result in code.

### Optional (if time)
    
Change the code to allow/disallow the user.

Create a new function to do user authentication:
    
    function userLoggedIn( req ) {
      if( req.headers['referer'].indexOf( 'auth=1' ) !== -1 ) {
        return true;
      }
      return false;
    }
    
Authenticate the call to `/pusher/auth`:

  app.post( '/pusher/auth', function( req, res ) {

    if( userLoggedIn( req ) === false ) {
      res.send( 401 );
      return;
    }

    var socketId = req.body.socket_id;
    var channelName = req.body.channel_name;

    var auth = pusher.auth( socketId, channelName );

    res.send( auth );
  } );