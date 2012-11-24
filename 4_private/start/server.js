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

var viewData = {
    appKey: config.pusher.appKey,
    channelName: config.pusher.channelName
};

app.get( '/', function ( req, res ) {
  res.render( 'index', viewData );
} );

app.post( '/new_message', function( req, res ) {
    
    var text = req.body.text;
    if( verifyMessage( text ) === false ) {
        req.send( 401 );
        return;
    }
    
    var pusher = new Pusher( {
      appId: config.pusher.appId,
      key: config.pusher.appKey,
      secret: config.pusher.appSecret
    } );
    pusher.trigger( config.pusher.channelName, 'new_message', { text: text } );
    res.send( 200 );
    
} );

function verifyMessage( text ) {
    return true;
}

var port = process.env.PORT || 5000;
app.listen( port );
console.log( 'listening on port ' + port );