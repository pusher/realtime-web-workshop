<?php
  require_once('../config.php');
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->  <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title><?php echo(PRESENTATION_TITLE); ?></title>
	
	<meta name="description" content="<?php echo(PRESENTATION_TITLE); ?>">
	<meta name="author" content="<?php echo(PRESENTER); ?>">
	<meta name="viewport" content="width=1024, user-scalable=no">
	
	<!-- Core and extension CSS files -->
	<link rel="stylesheet" href="../../lib/deck.js/core/deck.core.css">
	<link rel="stylesheet" href="../../lib/deck.js/extensions/goto/deck.goto.css">
	<link rel="stylesheet" href="../../lib/deck.js/extensions/menu/deck.menu.css">
	<link rel="stylesheet" href="../../lib/deck.js/extensions/navigation/deck.navigation.css">
	<link rel="stylesheet" href="../../lib/deck.js/extensions/status/deck.status.css">
	<link rel="stylesheet" href="../../lib/deck.js/extensions/hash/deck.hash.css">
	
	<link rel="stylesheet" id="transition-theme-link" href="../../lib/deck.js/themes/transition/horizontal-slide.css">
	<link rel="stylesheet" href="../../lib/snippet/jquery.snippet.min.css" type="text/css" />
	
	<link rel="stylesheet" href="http://pusher.github.com/pusher-realtime-chat-widget/src/pusher-chat-widget.css" type="text/css" />
	
	<!-- Custom CSS just for this page -->
	<link rel="stylesheet" href="../../assets/css/common.css">
	<link rel="stylesheet" href="styles.css">
	<link rel="stylesheet" href="../../assets/helpers/connection_state/connection_state.css" type="text/css" />
	
	
	<!-- Grab CDN jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.min.js"></script>
  <script>window.jQuery || document.write('<script src="../../lib/deck.js/jquery-1.7.min.js"><\/script>')</script>
	
	<script src="../../lib/deck.js/modernizr.custom.js"></script>
	
	<script src="http://js.pusher.com/1.12/pusher.min.js"></script>
  <script>window.Pusher || document.write('<script src="../../assets/js/pusher.min.js"><\/script>')</script>	
	
	<script src="http://pusher.github.com/pusher-realtime-chat-widget/src/js/PusherChatWidget.js"></script>
	<?php require_once('../js_globals.php'); ?>
	<script>
	  Pusher.log = function(msg) {
	    if(console && console.log) {
	      console.log(msg);
	    }
	  };
	  
	  Pusher.channel_auth_endpoint = '../../lib/pusher/auto_auth.php';
	  
	  function getPusher() {
	    if(Pusher.instances.length > 0) {
	      return Pusher.instances[0];
	    }
	    else {
	      return new Pusher(APP_KEY, {encrypted: true});
	    }
	  }
	  
	  $(function() {
	    setTimeout(initChat, 2000);
    });
    
    function initChat() {
      var pusher = getPusher();
      
      if( !window.PusherChatWidget ) {
        return;
      }
      
      var chatWidget = new PusherChatWidget(pusher, {
        chatEndPoint: '../interact/chat/',
        appendTo: '#chat',
        channelName: PRESENCE_CHANNEL_NAME
      });
    }
	</script>
	
	<link rel="stylesheet" href="http://pusher.github.com/html5-realtime-push-notifications/src/lib/gritter/css/jquery.gritter.css" type="text/css" />
	
	<script src="http://pusher.github.com/html5-realtime-push-notifications/src/lib/gritter/js/jquery.gritter.min.js"></script>
  <script src="http://pusher.github.com/html5-realtime-push-notifications/src/PusherNotifier.js"></script>
	<script>
  $(function() {
    var pusher = getPusher();
    pusher.subscribe(TWITTER_CMD_CHANNEL_NAME); // for use later
    
    var pres = pusher.subscribe(PRESENTATION_CHANNEL_NAME);

    if(!window.PusherNotifier) {
      return;
    }

    var notifier = new PusherNotifier(pres, {
      eventName: 'question',
      image: null,
      eventTextProperty: 'question',
      titleEventProperty: 'name',
      gritterOptions: {
        sticky: true
      }
    });
  });
  </script>
</head>

<body class="deck-container">
  
<img src="../../assets/images/3d-glasses-md.png" alt="3d Glasses Md" id="interact_icon" />

<section class="slide pusher markdown smaller interact" id="title">

## <?php echo(PRESENTATION_DATE); ?>

# <?php echo(PRESENTATION_TITLE); ?>


* [Phil Leggetter](http://www.leggetter.co.uk)
* [www.leggetter.co.uk](http://www.leggetter.co.uk)
* [@leggetter](http://twitter.com/leggetter)    
* Developer Evangelist at [Pusher](http://pusher.com)
</section>

<section class="slide markdown just-bullets smaller" id="overview">
## Overview

* History and current state of the Realtime Web
* Realtime Web Technology choices and Use Cases
* Connecting
* Publishing & Subscribing
* Security considerations
* Development best practices and tooling

</section>

<section class="slide markdown just-bullets smaller" id="prerequisites">
## Pre-requisites

* Laptop?
* JavaScript + back-end dev skills in PHP, Ruby, node.js, ASP.NET or Python?
* Web Server?
* Browser with dev tools?
* **Pair up?**
* Get the workshop files: [https://github.com/pusher/realtime-web-workshop](https://github.com/pusher/realtime-web-workshop)

</section>

<section class="slide markdown just-bullets smaller interact" id="intro">
  
<a href="http://www.youtube.com/watch?v=jDQbTOHvL6I" class="demo-video"></a>

## Introductions

* I've introduced myself, now it's your turn...
* <small>If you haven't already done so, please go to <a href="<?php echo(INTERACT_URL); ?>"><?php echo(INTERACT_URL); ?></a></small>

<ul id="introductions_notifications"></ul>

<script data-code-target="notification_example">
// connect
var pusher = getPusher();

// subscribe
var channel = pusher.subscribe(PRESENTATION_CHANNEL_NAME);

// bind
channel.bind('introduction', function(data) {
  var li = $('<li>' + 'Hello from <strong>' + data.name + '</strong>' +
             ' who\'s favourite technology is <strong>' + data.tech + '</strong>' +
             '</li>');
  $('#introductions_notifications').prepend(li);
});
</script>

</section>

<section class="slide markdown just-bullets" id="realtime">

## What is the Realtime Web?

<blockquote cite="http://en.wikipedia.org/wiki/Real-time_web">
  The real-time web is a set of technologies and practices that enable users to receive information as soon as it is published by its authors, rather than requiring that they or their software check a source periodically for updates.
</blockquote>
<small>Source: <a href="http://en.wikipedia.org/wiki/Real-time_web">Wikipedia: Realtime web</a></small>

</section>

<section class="slide markdown just-bullets" id="why_realtime_matters">
## Why the Realtime Web matters

* Opportunity
* Convenience
* Interaction
* Engagement
* **Improved richer user experiences**

<aside class="notes">
* Interactive &amp; Engaging experiences
* Powered by realtime events
* Opportunity &amp; Convenience
* Trend towards highly active users creating stuff that needs to be distributed to others immediately 
* It's all about user experience
</aside>

</section>

<section class="slide markdown just-bullets html5 smaller" id="history">
## Realtime Web Browser Technologies

* History
  * Java Applets
  * Polling
  
* **Comet**  
  * Long-Polling
  * Streaming
  
* Now
  * Server Sent Events (EventSource API)
  * HTML5 WebSockets

</section>

<section class="slide markdown" id="websockets">
## WebSockets

* A [protocol](http://tools.ietf.org/html/rfc6455) and an API
* Single bi-directional connection
* Supports cross domain communication

<pre class="js">
var ws =
  new WebSocket("ws://mywebsocketserver.com:80");
ws.onopen = function() {
  // connection established
};

ws.onmessage = function(ev) { // receive a message
  ws.send("Yep, got: " + ev.data); // send a message

  ws.close();
};

ws.onerror = function(ev) {}

ws.onclose = function(ev) {};
</code>
</pre>

</section>

<section class="slide markdown" id="websocket_support">
## WebSocket Support

<!-- <iframe src="http://caniuse.com/#feat=websockets" width="100%" height="400"></iframe> -->
<a href="http://caniuse.com/#feat=websockets">http://caniuse.com/#feat=websockets</a>

* Can use on IE6, IE7, IE8, IE9 and other older mainstream browsers with <a href="https://github.com/gimite/web-socket-js">web-socket-js Polyfil</a>
* Can use on Android with [FlashLite](http://en.wikipedia.org/wiki/Adobe_Flash_Lite) or using [Firefox for Android](https://market.android.com/details?id=org.mozilla.firefox&hl=en) and [Chrome for Android](https://market.android.com/details?id=com.android.chrome).
</section>

<section class="slide markdown" id="exercise_simplewebsocket">
# Exercise 0.5: Native WebSocket API  
</section>

<section class="slide markdown just-bullets" id="simplewebsocket_summary">
## Summary: Native WebSocket API

* In order to get data we need to connect to the source
* What about reconnections
* How do you identify the data you want to receive?
* What about routing of data?
* What about authenticating connections?
* **We need more that what the native raw tech offers - use a library**
</section>

<section class="slide markdown" id="pusher-library">
## Pusher JavaScript library

<pre class="js">
var pusher = new Pusher( 'app_key' );

pusher.connection.bind( 'connected', function() {
  // we are connected
});

pusher.connection.bind( 'state_change', function( state ) {
  var previousState = state.previous;
  var currentState = state.current;
});
</code>
</pre>

</section>

<section class="slide markdown" id="exercise_connecting">
# Exercise 1: Connecting  
</section>

<section class="slide markdown just-bullets" id="connecting_summary">
## Summary: Connecting

* Create a new `Pusher` instance and supply your `app_key`
* Bind to connection events on the `pusher.connection` object
</section>

<section class="slide markdown" id="what_about_the_data">
## What about the data?

* How do you identify the data you want to receive?
* What about routing of data?
</section>

<section class="slide just-bullets interact" id="pub_sub">

<h2>Publish &amp; Subscribe (PubSub)</h2>

<iframe height="100%" width="100%" id="pubsub_example" src="../../assets/examples/google_aliens/aliens.html" title="aliens - Google Search"></iframe>

</section>

<section class="slide markdown just-bullets" id="the_basics">
## The Basics

* Connect
* Subscribe
  * Channels
  * Events
    * Data
* Publish (trigger)
  * Channels
  * Events
    * Data
</section>

<section class="slide markdown" id="subscribe">
# Subscribe  
</section>

<section class="slide markdown just-bullets" id="why_subscribe">
## Why Subscribe?

* Notifications
* Activity Streams
* Realtime
  * sports stats
  * analytics
  * dashboards
* 2nd Screen applications
* Integrate with social media data feeds
</section>

<section class="slide markdown just-bullets nobg interact" id="realtime_notifications">
  
## Notifications

* Bring something of interest to the user's attention
* A user comes online
* Status changes <small>- notification of CRUD events</small>
* Long running process completion <small>- Stop polling!</small>

<button id="do_notification">Send Notification</button>
</section>


<section class="slide just-bullets image black" id="itv">
<h2 class="light">Notifications Examples:<br /><small>ITV</small></h2>

<img src="../../assets/images/itv_news.png" alt="CallRail" class="no-stretch" style="top: 190px" />
</section>

<section class="slide just-bullets image black" id="callcentres">
<h2 class="light">Notifications Examples:<br /><small>CallRail</small></h2>

<img src="../../assets/images/callrail.png" alt="CallRail" class="no-stretch" style="top: 190px" />
</section>

<section class="slide markdown just-bullets" id="activity_streams">
## Activity Streams

<figure class="side">
  <h3>Freedcamp</h3>
  <img src="../../assets/images/freedcamp.png" width="95%" align="left" alt="Freedcamp" />
</figure>

<figure class="side">
  <h3>Get Glue</h3>
  <img src="../../assets/images/get_glue.png" width="95%" align="right" alt="Get Glue" />
</figure>

* Walls
* Realtime comments
* Live blogging

</section>

<section class="slide just-bullets image black" id="gauges">
<h2 class="light">Analytics Example: Gaug.es</h2>

<a href="http://get.gaug.es"><img src="../../assets/images/gauges_home.png" alt="Gaug.es" /></a>
</section>

<section class="slide just-bullets image black" id="gauges_airtraffic">
<h2 class="light">Gaug.es: Air Traffic Live</h2>

<img src="../../assets/images/gauges_airtraffic_live.png" alt="Gaug.es" />
</section>

<section class="slide just-bullets image black" id="scoutapp">
<h2 class="light">Analytics Example: <a href="http://scoutapp.com">Scout</a></h2>

<img src="../../assets/images/scout-realtime-lanes.png" alt="ScoutApp" />
</section>

<section class="slide markdown just-bullets" id="realtime_socialtv">
## [Social TV](http://en.wikipedia.org/wiki/Social_television)
* 2nd screen experiences, 2-Screen, Sit forward TV

<img src="../../assets/images/sports_med.jpg" width="800" alt="Sports Med">
<!--img src="images/simpsons-socialtv.jpg" width="100%" alt="Simpsons Socialtv"-->

* GetGlue, OtherScreen
</section>

<section class="slide markdown" id="the_basics_code_client">
## Subscribe: Code Example

<pre class="js" data-code="notification_example">
</pre>

</section>

<section class="slide markdown interact" id="twitter_subscribe_example">
  
<a href="http://youtu.be/Rsht7SNYQZU" class="demo-video"></a>  
  
## Subscribe: Twitter Code Example

<button id="subscribeBtn">Subscribe</button>
<button id="unsubscribeBtn">Unsubscribe</button>
<input type="text" id="subscribeTo" value="nowplaying" />
<div id="last_tweet">
  <span class="text"></span>
  <img src="" />
</div>

<pre class="js" data-code="twitter_subscribe_example">

</pre>

<script data-code-target="twitter_subscribe_example">
$(function() {
  $('#subscribeBtn').live('click', subscribe);
});
function subscribe() {
  var pusher = getPusher();
  var channelName = 'tw-' + $('#subscribeTo').val();
  var channel = pusher.subscribe(channelName);
  channel.bind('tweet', function(tweet) {
    $('#last_tweet .text').text(tweet.text);
    $('#last_tweet img').attr('src', tweet.profile_image_url);
  });
}
</script>
<script>
$(function() {
  $('#unsubscribeBtn').live('click', unsubscribe);
  
  $('#subscribeBtn').live('click', sendSubscribeEvent);
  
  function sendSubscribeEvent() {
    var channelName = 'tw-' + $('#subscribeTo').val();
    var channel = pusher.channel(TWITTER_CMD_CHANNEL_NAME);
    channel.trigger('client-subscribe', {
      channel: channelName
    });
  }
});
function unsubscribe() {
  var channelName = 'tw-' + $('#subscribeTo').val();
  getPusher().unsubscribe(channelName);
  
  var channel = pusher.channel(TWITTER_CMD_CHANNEL_NAME);
  channel.trigger('client-unsubscribe', {
    channel: channelName
  });
}
</script>
</section>

<section class="slide markdown" id="subscribing_exercise">
# Exercise 2: Subscribing  
</section>

<section class="slide markdown just-bullets" id="subscribe_summar">
## Summary: Subscribing

* Use channels to filter and route data
  * product-1-channel, android-news-27-channel, project-fishcake-channel, bulls-v-lakers-channel
* Use events to help further filter e.g.
  * map to Created, Updated, Deleted, Liked, CommentReceived etc.
  * use with models in frameworks such as Backbone.js
  * map to UI changes
* Deciding how to structure your channels is a form of Information Architecture
</section>

<section class="slide markdown" id="publish">
# Publish/Trigger  
</section>

<section class="slide markdown just-bullets" id="why_publish">
## Why Publish?

* To send data to subscribers
* Bi-directional communication
* To create interactive experiences
  * Chat
  * Collaborative applications
  * Multiplayer games
  * Interactives 2nd screen apps
* Remember: Interactive experiences lead to engagement
</section>

<section class="slide markdown just-bullets" id="realtime_chat">  
## Chat

* Probably the No.1 use case

<img src="../../assets/images/gmail-chat-mock-up.png" width="33%" alt="Gmail Chat Mock Up" align="left" />
<img src="../../assets/images/live_help_olark.png" width="33%" alt="Live Help Olark" align="left" />
<img src="../../assets/images/FacebookChat03.gif" width="33%" alt="FacebookChat03" align="left" />
</section>

<section class="slide markdown just-bullets interact" id="chat">  
## Chat - Example

<div id="pusher_chat_widget"></div>
</section>

<section class="slide markdown just-bullets nobg" id="collaboration">  
## Collaboration

* Document editing
* Data Synchronisation
* Telecommuting

<img src="../../assets/images/canvas_dropr.png" alt="Canvas Dropr" width="50%" align="right" />

</section>

<section class="slide just-bullets image" id="mailchimp">
<h2 class="light">MailChimp - Collaboration</h2>

<img src="../../assets/images/mailchimp.png" alt="Mailchimp" />
</section>

<section class="slide markdown just-bullets" id="koding">
<h2 class="light"><a href="http://koding.com">Koding</a> - Collaboration</h2>

<img src="../../assets/images/koding.png" alt="Koding" />
</section>

<section class="slide markdown just-bullets" id="collaboration_example">
## Collaboration Example

<img src="../../assets/images/codr-cc.png" width="584" height="375" alt="Codr Cc">

* [Codr.cc](http://codr.cc/500511)
</section>

<section class="slide markdown just-bullets" id="realtime_games">
## Multiplayer Games

* Game actions
  * Player/object moves, etc
* Game state changes
</section>

<section class="slide just-bullets image" id="realtime_games_w2">
<h2 class="light">Word<sup>2</sup></h2>

<img src="../../assets/images/word2.png" alt="Word2" />
</section>

<section class="slide just-bullets image" id="realtime_games_w2_map">
<h2 class="light">Word<sup>2</sup> (Map)</h2>

<a href="http://wordsquared.com"><img src="../../assets/images/word2_map.png" alt="Word2 Map" /></a>
</section>

<section class="slide markdown just-bullets interact" id="ghosts">
  
<a href="http://youtu.be/rRwhbqNn7TY" class="demo-video"></a>

<iframe src="https://ghosts.herokuapp.com" style="height: 100%; width: 100%;"></iframe>

<small><a href="https://ghosts.herokuapp.com">https://ghosts.herokuapp.com</a></small>

</section>

<section class="slide markdown just-bullets nobg" id="publish_where">
## Where to publish/trigger events?

<img src="../../assets/images/trigger_events.png" align="right" width="390" height="390" alt="Trigger Events">

* Client or Server?
* Security considerations
* Authority
 * Data Validation
 * Data Sanitisation
</section>

<section class="slide markdown just-bullets nobg" id="publish_where">
## Pubishing/Trigger best practices

* How frequently should you publish your data?
* How much data should you send?

* Consider the application receiving the data
  * Data usage
  * Processing power
  * Battery usage
</section>

<section class="slide markdown" id="publish_server">
# Publish/Trigger from the server
</section>

<section class="slide markdown" id="server_trigger_php">
## Trigger Server (PHP)

<pre class="php">
&lt;?php
require('Pusher.php');
require('config.php');

$data = array('message' => 'hello world');
$pusher = new Pusher($config->key, $config->secret, $config->app_id);
$pusher->trigger('my-channel', 'my_event', data);
?&gt;
</pre>

</section>

<section class="slide markdown" id="server_trigger_rails">
## Trigger Server (Rails)

<pre class="ruby">
require 'pusher'

Pusher.app_id = 'APP_ID'
Pusher.key = 'API_KEY'
Pusher.secret = 'SECRET_KEY'

class HelloWorldController &lt; ApplicationController
  def trigger
    Pusher['my-channel'].trigger('my_event', {:message => 'hello world'})
  end
end
</pre>

</section>

<section class="slide markdown" id="server_trigger_dotnet">
## Trigger Server (.NET)

<pre class="csharp">
var appId = ConfigurationManager.AppSettings["app_id"];
var appKey = ConfigurationManager.AppSettings["app_key"];
var appSecret = ConfigurationManager.AppSettings["app_secret"];

var provider = new PusherProvider(appId, appKey, appSecret);
ObjectPusherRequest request =
  new ObjectPusherRequest('my-channel',
                          'my_event',
                          new {
                            message = "hello world"
                          });
provider.Trigger(request);                          
</pre>

</section>


<section class="slide markdown" id="server_trigger_node">
## Trigger Server (node.js)

<pre class="js">
var Pusher = require('node-pusher');

var pusher = new Pusher({
  appId: 'YOUR_PUSHER_APP_ID',
  key: 'YOUR_PUSHER_APP_KEY',
  secret: 'YOUR_PUSHER_SECRET_KEY'
});

pusher.trigger('my-channel',
               'my_event',
               {"message": "hello world"});                        
</pre>

</section>

<section class="slide markdown" id="publish_exercise">
# Exercise 3: Publishing / Triggering  
</section>

<section class="slide markdown just-bullets" id="publish_server_summary">
## Summary: Publishing / Server

* Secure - authenticate, sanitize, validate
* You server is the authority
* You can persist messages
* Consider:
  * How much data you are sending
  * What devices are receiving the data
</section>

<section class="slide markdown" id="private_channels_intro">
# User Authentication / Private Channels
</section>

<section class="slide markdown just-bullets" id="private_channels">
## Authentication

* Why do you want to control who can subscribe to your data?
* How do you restrict who can subscribe to your data?
  * Cryptic channel names
  * Session generated channel names
  * Auth mechanism
* Pusher uses an [authentication mechanism](http://pusher.com/docs/authenticating_users)
</section>

<section class="slide markdown" id="auth_exercise">
# Exercise 4: User Authentication / Private Channels
</section>

<section class="slide markdown just-bullets" id="auth_exercise">
## Summary: User/Subscription Authentication

* Ensure there is a mechanism for authenticating subscriptions
* There needs to be an authority
* Integrate with existing authentication mechanisms
</section>

<section class="slide markdown" id="client_events_title">
# Client Events
</section>

<section class="slide markdown just-bullets" id="client_events_intro">
## Client Events

* Sometimes you may want to publish/trigger events directly from your client
* Client applications need to know the source of the event
  * Sanitize/Verify in the client app
* Use cases
  * Added value meta data e.g. user X is typing
  * Mobile apps with limited server interaction
  * Where minimal latency matters e.g. gaming
</section>

<section class="slide markdown interact" id="client_events">
  
<a href="http://youtu.be/KT0XTRKMKto" class="demo-video"></a>
  
## Trigger Client: Code Example
<input type="text" id="name" value="Phil Leggetter" />
<input type="text" id="text" value="hello world" />
<button id="triggerEvent">Trigger</button>

<pre class="js" data-code="client_publish_example"></pre>

<script data-code-target="client_publish_example">
$(function() {
  $('#triggerEvent').live('click', publish);
});
var triggerPusher = getPusher();
var triggerChannel = triggerPusher.subscribe(TRIGGER_CHANNEL_NAME);
function publish() {
  if(triggerPusher.connection.state !== 'connected') {
    alert('not connected');
    return;
  }
  triggerChannel.trigger('client-new_trigger',
                  {
                    name: $('#name').val(),
                    text: $('#text').val()
                  });
}
</script>

* Authentication required
  * `private-chat`
* Event identified as client event
  * `client-new_message`
  
<aside class="notes">
  Rick
<!--  <script>document.location.href = 'http://www.youtube.com/watch?v=oHg5SJYRHA0';</script>  -->
<!-- Pusher.instances[0].channel(TRIGGER_CHANNEL_NAME).trigger('client-fun-stuff', {'do':'alien'}); -->
</aside>
</section>

<section class="slide markdown" id="client_events_ex">
# Exercise 5: Client Events
</section>

<section class="slide markdown just-bullets" id="client_events_intro">
## Summary: Client Events

* Remember: you can't trust a client
* Sanitize/Verify upon receipt of the data in your app
* Rate limit client events
  * Batch up events sent close together
</section>

<section class="slide markdown" id="presence_title">
# Presence
</section>

<section class="slide markdown just-bullets" id="presence_intro">
## Presence

* Who else is subscribed to a channel?
* List of existing subscribers
* Join/Leave events
  * `member_added`
  * `member_removed`
* Very handy for:
  * Chat rooms
  * Collaborative apps
  * Games
</section>

<section class="slide interact" id="presence">

<a href="http://youtu.be/sgz6CdxBspE" class="demo-video"></a>  
  
  <h2>Presence</h2>
  
  <div class="left one-quarter">
    <strong>Who's Online?</strong>
    <ul id="whos_here"></ul>
  </div>
  
  <div class="right three-quarters">
    
<pre class="js" data-code="presence_example">
</pre>
    
<script data-code-target="presence_example">
var pusher = getPusher();
var channel = pusher.subscribe(PRESENCE_CHANNEL_NAME);
channel.bind('pusher:subscription_succeeded', function(members) {
  members.each(addMember);
});
channel.bind('pusher:member_added', addMember);
channel.bind('pusher:member_removed', removeMember);

function addMember(member) {
  var li = $('<li data-user-id="' + member.id + '">' + member.info.name + '</li>');
  $('#whos_here').append(li);
}

function removeMember(member) {
  $('#whos_here').find('*[data-user-id=' + member.id + ']')
  .remove();
}
</script>
  </div>
</section>

<section class="slide markdown" id="webhooks_title">
# WebHooks
</section>

<section class="slide markdown" id="webhooks_intro">
## WebHooks

* What's happening in Pusher with your app?
* HTTP callbacks from Pusher to your web application
* Channel existence
  * `channel_occupied`
  * `channel_vacated`
* Presence events
  * `member_added`
  * `member_removed`
* More?
</section>

<section class="slide markdown" id="queries_title">
# Channel Queries
</section>

<section class="slide markdown just-bullets" id="queries_intro">
## Channel Queries

* Initial state when your app starts up
* Is a channel occupied?
* How many users are subscribed to a channel?
* What users are subscribed to a presence channel?
</section>

<section class="slide markdown just-bullets smaller" id="future">
## Future of Realtime Web technology

* **WebSockets**
  * Full native browser support
  * Not just web browsers
* UX & Performance considerations
* [TCP/UDP in the browser - Chrome Extensions](blog.alexmaccaw.com/chrome-tcp-udp)
* [SPDY](http://dev.chromium.org/spdy/spdy-whitepaper)
* **WebHooks**
  * We still live in a HTTP World 
  * Realtime server to server communication  
* The [Internet of Things](http://en.wikipedia.org/wiki/Internet_of_Things)
</section>

<section class="slide markdown just-bullets smaller" id="arduino_examples">
## reaDIYmate & Ninja Blocks

<iframe src="http://player.vimeo.com/video/38095211?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>

* [http://readiymate.com](http://readiymate.com)
* Also check out [http://ninjablocks.com/](http://ninjablocks.com/)
</section>

<section class="slide markdown just-bullets" id="future_ardunio">
## Arduino's taking over the World!

<!-- <iframe width="800" height="600" src="http://www.youtube.com/embed/YQIMGV5vtd4" frameborder="0" allowfullscreen></iframe> -->
<a href="http://www.youtube.com/embed/YQIMGV5vtd4">http://www.youtube.com/embed/YQIMGV5vtd4</a>
</section>

<section class="slide markdown just-bullets" id="summary">
## Summary

* The Realtime Web isn't just a marketing term
* Realtime Web awesome technology
  * Is in use today (in the wild)
  * Is fun to develop with
* Connect, Subscribe, Publish, Authenticate  
* Adds **real** value to apps:
    * through **Interaction** and **Engagement**
    * **Improves User Experience (UX)**
    * by **connecting audiences**
</section>

<section class="slide just-bullets markdown pusher" id="questions">
<h2>Questions?/Thanks</h2>

* We can run a longer version of this workshop. If you are interested please give me a shout.

* Slides available: [<?php echo(SLIDES_URL); ?>](<?php echo(SLIDES_URL); ?>)
  
* [Phil Leggetter](http://www.leggetter.co.uk), [@leggetter](http://twitter.com/leggetter)
* [Pusher](http://pusher.com) ([@pusher](http://twitter.com/pusher))
	  
</section>

<a href="#" class="deck-prev-link" title="Previous">&#8592;</a>
<a href="#" class="deck-next-link" title="Next">&#8594;</a>

<p class="deck-status">
	<span class="deck-status-current"></span>
	/
	<span class="deck-status-total"></span>
</p>

<form action="." method="get" class="goto-form">
	<label for="goto-slide">Go to slide:</label>
	<input type="text" name="slidenum" id="goto-slide" list="goto-datalist">
	<datalist id="goto-datalist"></datalist>
	<input type="submit" value="Go">
</form>

<a href="." title="Permalink to this slide" class="deck-permalink">#</a>

<!-- Deck Core and extensions -->
<script src="../../lib/deck.js/core/deck.core.js"></script>
<script src="../../lib/deck.js/extensions/hash/deck.hash.js"></script>
<script src="../../lib/deck.js/extensions/menu/deck.menu.js"></script>
<script src="../../lib/deck.js/extensions/goto/deck.goto.js"></script>
<script src="../../lib/deck.js/extensions/status/deck.status.js"></script>
<script src="../../lib/deck.js/extensions/navigation/deck.navigation.js"></script>

<script src="../../lib/deck.js/extensions/showdown/deck.showdown.js"></script>

<script src="../../lib/snippet/jquery.snippet.min.js"></script>

<!-- Specific to this page -->
<script src="http://leggetter.github.com/script-cdn/blog_helpers/js/script-to-html.js"></script>
  <script>window.jQuery.scriptToHtml || document.write('<script src="../../assets/js/script-to-html.js"><\/script>')</script>

<script src="../../assets/helpers/connection_state/connection-state.js"></script>

<script src="pres.js"></script>

</body>
</html>
