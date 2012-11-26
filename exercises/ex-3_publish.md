# Exercise 3 - Publish

The most obvious next step would be to publish data from JavaScript. But remember, you can't trust the client so in most cases you'll want to publish data via your server. That's what we'll do here.

## Docs

<http://pusher.com/docs/server_api_guide/server_publishing_events>

## Action

**Choose and get your [server library](http://pusher.com/docs/server_libraries).**

The rest of the workshop will assume you are working with node.js.

## Steps

We want to trigger/publish a `new_message` event on the `messages` channel. The event data should have a `text` property.

### A node web server

In order to write server functionality we're doing to need a web server. There are a number of node.js server solutions and web application frameworks for this in node.js, including using the core [HTTP package](http://nodejs.org/api/http.html), [connect](http://www.senchalabs.org/connect/) or [expressjs](http://expressjs.com/). We'll use **expressjs**.

*There is a little bit of work to be done here, but it's worth it to understand how expressjs works and so that we are doing things properly.*

#### Get expressjs

In `3_publish/start` create a `package.json` file defining expressjs as a dependency:

    {
      "name": "rtw-workshop-sample-app",
      "description": "Realtime Web Workshop sample app",
      "version": "0.0.1",
      "private": true,
      "dependencies": {
        "express": "3.x"
      }
    }
    
Open up a terminal. If you are using [Cloud9](http://c9.io) there is a "Open a Terminal" link at the bottom right of the browser window. Ensure you are in the `3_publish/start` directory and run `npm install`. expressjs will be downloaded into a `node_modules` folder.

### Create your server

Create a `server.js` file.

Add the following to the file:

    var express = require('express');
    var path = require('path');
    
    var app = express();
    
    app.get( '/', function( req, res ){
      res.sendfile( path.resolve(__dirname, 'index.html' ) );
    } );
    
    app.listen( process.env.PORT );
    
This does the following:

1. imports the `express` package and a `path` package
2. creates a server using `var app = express();`
3. defines that the `index.html` file should be served up from the root of the web server

### Restructing

#### Static files

It's standard in a number of of web servers that any static content (HTML documents, CSS, Images, JavaScript) should be served from a folder called `public` (or `static`) so let's create a `public` folder and:

* `git mv index.html public/`
* `mkdir public/css`
* `git mv styles.css public/css/`

And tell the server that's where the static files are served from. If we do this we can even remove the default root. The `server.js` code becomes:

    var express = require('express');
    
    var app = express();
    app.use( express.static( __dirname + '/public' ) );
    
    app.listen( process.env.PORT );
    
#### Views

Application frameworks tend to use *views* and *templates*. expressjs is no different and you have a number of templating engines to choose from. We'll use [EJS](https://npmjs.org/package/ejs). To do this we need to:

Update `package.json` to define that we have a dependency on ejs.

    {
      "name": "rtw-workshop-sample-app",
      "description": "Realtime Web Workshop sample app",
      "version": "0.0.1",
      "private": true,
      "dependencies": {
        "express": "3.x",
        "ejs": "0.8.3"
      }
    }
    
Run `npm updates` to pull down the `ejs` package into `node_modules`.
    
Create a `views` directory and move our `public/index.html` file in there to be used as a template.

* `mkdir views`
* `git mv public/index.html views/index.html`

Finally, we need to update `server.js`:

    var express = require( 'express' );
    var ejs = require( 'ejs' );
    
    var app = express();
    app.use( express.static( __dirname + '/public' ) );
    
    app.engine( '.html', ejs.__express );
    app.set( 'view engine', 'html' );
    app.set( 'views', __dirname + '/views' );
    
    app.get( '/', function ( req, res ) {
      res.render( 'index' );
    } );
    
    app.listen( process.env.PORT || 5000 );
    
This code requires the ejs package and tells expressjs to render .html files using ejs templating engine and that the templates are inthe `views` directory. It also defines that the root of the application should serve the `index` view, our `views/index.html` file.

#### Configuration

It's a good idea to keep configuation in one place rather than have the same config as Strings in client and server files. So, we'll create a `config.json` file and add our Pusher application credentials to it.

Create a `config.json` file passing in the Pusher app credentials and the channel name that we are subscribing to, and will be publishing on.

    {
      "pusher": {
        "appId": "YOUR_APP_ID",
        "appKey": "YOUR_APP_KEY",
        "appSecret": "YOUR_APP_SECRET",
        "channelName: : "messages"
      }
    }
    
Load the config file in `server.js`:

    var fs = require( 'fs' );
    
    var data = fs.readFileSync( __dirname + '/config.json' );
    var config = JSON.parse( data );

and pass the value of `pusher.appKey` through to the view:
    
    app.get( '/', function ( req, res ) {
      var viewData = {
        appKey: config.pusher.appKey,
        channelName: config.pusher.channelName
      };

      res.render( 'index', viewData );
    } );
    
Now we just need to update our `views/index.html` template to use the view data that we've passed in.

    var pusher = new Pusher( '<%= appKey %>' );
    
    var channel = pusher.subscribe( '<%= channelName %>' );
    
Now, run your app and make sure the client still connects to Pusher and the subscription is successful.

**Phew!** We've restructured our application to use expressjs so we can now **create our endpoint which is used to trigger events**.

### Triggering the event

We want to trigger/publish a `new_message` event on the `messages` channel and the event data should have a `text` property.

First we need to get the Pusher library that helps us do this. So, add [`node-pusher`](https://npmjs.org/package/node-pusher) as a dependency to `package.json`.

    {
      "name": "rtw-workshop-sample-app",
      "description": "Realtime Web Workshop sample app",
      "version": "0.0.1",
      "private": true,
      "dependencies": {
        "express": "3.x",
        "ejs": "0.8.3",
        "node-pusher": "0.0.2"
      }
    }
    
And run `npm update` to pull down the package.

Now we have the package let's create a `new_message` endpoint which triggers the message. In there we'll create a new `Pusher` object and trigger/publish the event.

    var Pusher = require( 'node-pusher' );

    var pusher = new Pusher( {
      appId: config.pusher.appId,
      key: config.pusher.appKey,
      secret: config.pusher.appSecret
    } );

    app.get( '/new_message', function( req, res ) {
        pusher.trigger( config.pusher.channelName, 'new_message', { text: 'hello' } );
        res.send( 200 );
    } );
   
Navigate to `/new_message` in your browser and see the data appear in:

1. The Pusher debug console
2. The `window.console`
3. The UI

### Update the UI
     
Create an input field and button in the UI to send the data via the server to be validated to be published.

Create new markup:
  
    <label for="textarea" class="ui-hidden-accessible">Message:</label>
    <textarea name="user_message" id="user_message" placeholder="Message"></textarea>
    
    <a id="send_btn" href="/new_message" data-role="button" data-theme="b">Send</a>     

Update the messages CSS in `public/styles.css`:
    
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
              url: '/new_message',
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
    
We next need to update the `/new_message` to receive the `POST` AJAX call with `text` parameter. To do this we need to tell expressjs to parse the request using the `bodyParser`:

    app.use( express.bodyParser() );

We should also put some scaffolding in place to verify and sanitise the data:

    var pusher = new Pusher( {
      appId: config.pusher.appId,
      key: config.pusher.appKey,
      secret: config.pusher.appSecret
    } );
  
    app.post( '/new_message', function( req, res ) {
        
      var text = req.body.text;
      if( verifyMessage( text ) === false ) {
        req.send( 400 );
        return;
      }
      
      pusher.trigger( config.pusher.channelName, 'new_message', { text: text } );
      res.send( 200 );
        
    } );
    
    function verifyMessage( text ) {
      return true;
    }

### That's it!

Since you've already subscribed to the channel and bound to the event in Exercise 2 you don't need to do anything else. It just works!