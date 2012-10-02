<?php
include '../config.php';
include '../../lib/pusher/functions.php';
$stage = get_stage();
$user_data = get_user_data();
$name = $user_data['info']['name'];
$tech = $user_data['info']['tech']?$user_data['info']['tech']:'unknown';
?>
<!DOCTYPE html> 
<html>
  <head>
    <title><?php echo(PRESENTATION_TITLE); ?> - Interact</title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.css" />
    <script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
    <script src="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.js"></script>
    
    <link rel="stylesheet" href="styles.css" type="text/css" />
    <link rel="stylesheet" href="../../assets/css/alien.css" type="text/css" />
    <link rel="stylesheet" href="http://pusher.github.com/html5-realtime-push-notifications/src/lib/gritter/css/jquery.gritter.css" type="text/css" />    
    
    <link rel="stylesheet" href="../../assets/helpers/connection_state/connection_state.css" type="text/css">
    
    <link rel="stylesheet" href="http://pusher.github.com/pusher-realtime-chat-widget/src/pusher-chat-widget.css" type="text/css" />
    
    <script src="http://pusher.github.com/pusher-realtime-chat-widget/src/js/PusherChatWidget.js"></script>
    
    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body data-stage="<?php echo($stage); ?>" class="<?php echo($stage); ?>">
    
    <section data-role="page" data-theme="b">
      
      <header data-role="header" data-position="fixed">
        <h1><?php echo(PRESENTATION_TITLE); ?> &dash; <small>slide: <span class="slide">?</span> (<span class="stage"><?php echo($stage); ?></span>)</small></h1>
      </header>
      
      <section>
        <ul id="about" data-role="listview" data-inset="true">
          <li>Name: <span class="name"><?php echo($name); ?></span></li>
          <li>Favourite technology: <span class="tech"><?php echo($tech); ?></span></li>
        </ul>
      </section>

      <div class="page" data-role="content" id="title" data-theme="b">
        <h1><?php echo(PRESENTATION_TITLE); ?></h1>
      </div>

      <div class="page" data-role="content" id="who" data-theme="b">
        <ul>
          <li>Phil <a href="http://twitter.com/leggetter">@leggetter</a></li>
          <li><a href="http://www.leggetter.co.uk">www.leggetter.co.uk</a></li>
          <li>Developer Evangelist @ <a href="http://pusher.com">Pusher</a></li>
          <li><a href="http://twitter.com/pusher">@pusher</a></li>
        </ul>
      </div>
    
      <div class="page" data-role="content" id="intro" data-theme="b">
        <form action="send_intro.php" method="post">
          <p>
            My name is <input type="text" name="my_name" />
            and my favourite technology is <input type="text" name="favourite_technology" />
          </p>
          <input type="submit" value="Yep, that's me" />
        </form>
      </div>
      
      <div class="page" data-role="content" id="websockets" data-theme="b">
        <h2>WebSockets Rock!</h2>
        <img src="../../assets/images/html5logo.png" width="100%" alt="Html5logo" />
      </div>
      
      <div class="page" data-role="content" id="twitter_subscribe_example" data-theme="b">
        <h2>Twitter Subscribe Example</h2>
        <div>Subscribed to: <span id="twitter_subscribe_to">nothing</span></div>
        <ul id="tweets"></ul>
      </div>
      
      <div class="page" data-role="content" id="client_events" data-theme="b">
        <h2>Client Events</h2>
        <ul id="triggered_events"></ul>
      </div>
      
      <div class="page" data-role="content" id="realtime_data" data-theme="b">
        <h2>Realtime Data</h2>
      </div>
      
      <div class="page" data-role="content" id="realtime_notifications" data-theme="b">
        <h2>Notifications</h2>
        <p>You can view a simple example here:</p>
        <ul>
          <li><a target="_blank" href="http://html5-realtime-push-notifications.phpfogapp.com/examples/">Side-by-side view</a></li>
          <li><a target="_blank" href="http://html5-realtime-push-notifications.phpfogapp.com/examples/notify.html">Standalone</a></li>
        </ul>
      </div>
      
      <div class="page" data-role="content" id="activity_streams" data-theme="b">
        <h2>Activity Streams</h2>
        <p>You can view a simple example here:</p>
        <ul>
          <li><a target="_blank" href="http://html5-realtime-activity-streams.phpfogapp.com/">Side-by-side view</a></li>
          <li><a target="_blank" href="http://html5-realtime-activity-streams.phpfogapp.com/activity_stream.html">Standalone</a></li>
        </ul>
      </div>
      
      <div class="page" data-role="content" id="presence" data-theme="b">
        <h2>Who's online?</h2>
        <button id="goOnline">Hey, I'm online!</button>
        <ul id="whos_here"></ul>
      </div>
      
      <div class="page" data-role="content" id="chat" data-theme="b">
        <h2>Chat</h2>
      </div>
      
      <div class="page" data-role="content" id="collaboration_example" data-theme="b">
        <h2>Collaboration</h2>
        
        <a href="http://codr.cc/500511" target="_blank">Codr.cc</a>
      </div>
      
      <div class="page" data-role="content" id="ghosts" data-theme="b">
        <h2>Multiplayer Games</h2>
        
        <a href="https://ghosts.herokuapp.com/" target="_blank">Ghosts</a>
        <p>Move your mouse/touch to move your Ghost and tap/click to absorb energy from other Ghosts.</p>
      </div>
      
      <div class="page" data-role="content" id="realtime_socialtv" data-theme="b">
        <h2>2nd Screen Experiences</h2>
        
        Just like this.
      </div>
      
      <div class="page" data-role="content" id="future_ardunio" data-theme="b">
        <h2>Arduinos are very cool</h2>
        
        <p>Also check out <a href="http://readiymate.com" target="_blank">reaDIYmate</a> and <a href="http://ninjablocks.com">Ninja Blocks</a>.</p>
      </div>
      
      <div class="page" data-role="content" id="questions" data-theme="b">
        <h2>Questions?</h2>
        
        <form action="questions.php" method="post">
          <label for="name">Name:</label>
          <input type="text" name="name" readonly="readonly" value="<?php echo($name); ?>" />
          <label for="question">Question:</label>
          <textarea name="question"></textarea>
          <input type="submit" value="Ask away" />
        </form>
      </div>
      
      <footer id="activity" data-role="footer">
        <h2>Activity</h2>
        <div id="messages"></div>
      </footer>
    
    </section>
    
    <script src="http://js.pusher.com/1.11/pusher.min.js"></script>
    <?php require_once('../js_globals.php'); ?>
    <script src="app.js"></script>
    <script src="../../assets/helpers/connection_state/connection-state.js"></script>
    
    <script src="../../assets/js/soundmanager/soundmanager2-jsmin.js"></script>
    <script>
      soundManager.url = '../../assets/js/soundmanager/swf/';
    </script>

  	<script src="http://pusher.github.com/html5-realtime-push-notifications/src/lib/gritter/js/jquery.gritter.min.js"></script>
  </body>
</html>