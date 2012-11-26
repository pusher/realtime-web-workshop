var express = require( 'express' );
var ejs = require( 'ejs' );
var fs = require( 'fs' );
var Pusher = require( 'node-pusher' );

var data = fs.readFileSync( __dirname + '/config.json' );
var config = JSON.parse( data );

var app = express();
app.use( express.static( __dirname + '/public' ) );
app.use( express.bodyParser() );

app.engine( '.html', ejs.__express );
app.set( 'view engine', 'html' );
app.set( 'views', __dirname + '/views' );

var pusher = new Pusher( {
  appId: config.pusher.appId,
  key: config.pusher.appKey,
  secret: config.pusher.appSecret
} );

function getUsername( req ) {
  var username = req.query.user || 'unknown';
  return username;
}

app.get( '/', function ( req, res ) {
  var viewData = {
    appKey: config.pusher.appKey,
    channelName: config.pusher.channelName,
    username: getUsername( req )
  };
  
  res.render( 'index', viewData );
} );

app.post( '/new_message', function( req, res ) {
    
  if( userLoggedIn( req ) === false ) {
    res.send( 401 );
    return;
  }
    
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

function userLoggedIn( req ) {
  if( req.headers['referer'].indexOf( 'auth=1' ) !== -1 ) {
    return true;
  }
  return false;
}

var port = process.env.PORT || 5000;
app.listen( port );
console.log( 'listening on port ' + port );