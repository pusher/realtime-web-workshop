var express = require( 'express' );
var ejs = require( 'ejs' );
var fs = require( 'fs' );

var data = fs.readFileSync( __dirname + '/config.json' );
var config = JSON.parse( data );

var app = express();
app.use( express.static( __dirname + '/public' ) );

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

app.listen( process.env.PORT );