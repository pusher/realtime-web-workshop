Pusher.log = function(msg) {
  if(window.console && window.console.log) {
    window.console.log(msg);
  }
};

Pusher.channel_auth_endpoint = '../../lib/pusher/presence_auth.php';

$(function() {
  
  $('#intro form').submit(introSubmitted);
  $('#questions form').submit(questionSubmitted);
  setupPusher();
  
  var pusher = getPusher();
  pusher.connection.bind('connected', function() {
    checkStage();
  });
});

function checkStage() {

  var defaultChange = function() {
    var val = $('body').attr('data-stage');
    if(val) {
      changeStage(val);
    }
  };
   
}

function questionSubmitted() {
  var form = $(this);
  var values = form.serialize(); 
  
  $.ajax({
    url: form.attr('action'),
    type: 'POST',
    data: values,
    dataType: 'json',
    success: function() {
      form.find('textarea').val('');
    }
  });
  
  return false;       
}

function introSubmitted() {
  var form = $(this);
  var values = form.serialize();
  
  $.ajax({
    url: form.attr('action'),
    type: 'POST',
    data: values,
    dataType: 'json',
    success: introSubmitSuccess
  });
  
  changeStage('wait');
  
  return false;
};

function introSubmitSuccess(data) {
  setUserInfo(data);
}

function setUserInfo(data) {
  var userInfo = $('#about');
  userInfo.find('.name').text(data.info.name);
  userInfo.find('.tech').text(data.info.tech);  
  
  $('#questions input[name="name"]').attr('readonly', 'readonly').val(data.info.name);      
}

var stageHandlers = {
  'presence': subscribeToPresence,
  'chat': initChat,
  'client_events': initClientEvents,
  'twitter_subscribe_example': initTwitter
};

var lastDisplayedStage = null;
function changeStage(stage, from) {
  var msg = 'going ' + (from?'from "' + from + '" ':'') + 'to "' + stage + '"';
  Pusher.log(msg);
  displayMessage(msg);
  
  var body = $('body');
  body.attr('data-stage', stage).addClass(stage);
  if(from) {
    body.removeClass(from);
  }
  body.addClass(stage);
  
  $('header .stage').text(stage);
    
  var handler = stageHandlers[stage];
  if(handler) {
    handler();
  }
  
  var toPage = $('#' + stage);
  if(toPage.size() === 1) {
    console.log('last: ' + lastDisplayedStage);
    $('#' + lastDisplayedStage + ', #' + from).slideUp();
    toPage.slideDown();
    lastDisplayedStage = stage;
  }
}

function getPusher() {
  if(Pusher.instances.length === 0) {
    return new Pusher(APP_KEY, {encrypted: true});
  }
  return Pusher.instances[0];
}

function getChannel( channelName ) {
  var pusher = getPusher();
  var channel = pusher.channel( channelName );
  if( !channel ) {
    channel = pusher.subscribe( channelName );
  }
  return channel;
}

function setupPusher() {
  var pusher = getPusher();
  var introsChannels = getChannel(PRESENTATION_CHANNEL_NAME);
  introsChannels.bind('introduction', function(data) {
    displayMessage('Hello from ' + data.name + ' who\'s favourite technology is ' + data.tech);
  });
  
  var deckjsChannel = getChannel(DECKJS_CHANNEL_NAME);
  deckjsChannel.bind('slide_change', function(data) {
    $('header .slide').text((parseInt(data.to, 10) + 1) + '/' + data.count);
    
    changeStage(data.toName, data.fromName);
  });

  // fun stuff  
  var clientChannel = getChannel(TRIGGER_CHANNEL_NAME);
  clientChannel.bind('client-fun-stuff', doFunStuff);

  var alien = $('<img class="alien" src="../../assets/images/scarey_alien.png" alt="Alien" />');
  $('body').append(alien);
}

function doFunStuff(data) {
  if(data.action === 'alien') {
    var alien = $('img.alien');
    alien.addClass('drop-in');
    setTimeout(function() {
      alien.removeClass('drop-in');
    }, 3000);
  }
  else if( data.action === 'notification' ){
    doNotification();
  }
};

var subscribeToPresenceInited = false;
function subscribeToPresence() {
  if(subscribeToPresenceInited) {
    return;
  }
  else
  {
    subscribeToPresenceInited = true;
  }
  
  $('#goOnline').live('click', goOnline);
};

function goOnline() {
  $('#goOnline').parent('.ui-btn').slideUp();
  
  var channel = getChannel(PRESENCE_CHANNEL_NAME);
  channel.bind('pusher:subscription_succeeded', function(members) {
    members.each(addMember);
  });
  channel.bind('pusher:member_added', addMember);
  channel.bind('pusher:member_removed', removeMember);

  function addMember(member) {
    var li = $('<li data-user-id="' + member.id + '">' +
      member.info.name + 
      '</li>');
    $('#whos_here').append(li);
  }

  function removeMember(member) {
    $('#whos_here')
    .find('*[data-user-id=' + member.id + ']')
    .remove();
  }
}

function displayMessage(msg) {
  var messages = $('#messages');
  messages.prepend('<div class="message">' + msg + '</div>');
  var children = messages.find('.message');
  if(children.size() > 5) {
    children.last().remove();
  }
}

function initChat() {
  if(PusherChatWidget.instances.length === 0){
    var pusher = getPusher();
  
    var chatWidget = new PusherChatWidget(pusher, {
      chatEndPoint: 'chat/',
      appendTo: '#chat',
      channelName: PRESENCE_CHANNEL_NAME
    });
    
    var name = $('#about .name').text();
    $('.pusher-chat-widget input[name="nickname"]')
    .attr('readonly', 'readonly')
    .val(name);
  }
}

function initClientEvents() {
  getChannel(TRIGGER_CHANNEL_NAME).bind('client-new_trigger', handleClientTrigger);
}

function handleClientTrigger(data) {
  $('#triggered_events').append(
    '<li>' +
      encodeHtml(data.name) + ' said &quot;' + data.text + '&quot;' +
    '</li>'
  );
}

function encodeHtml(value){
  return $('<div/>').text(value).html();
}

function doNotification() {
  var sound = soundManager.createSound({
   id: 'mySound',
   url: '../../assets/sounds/explode.mp3'
  });
  if( sound ) {
    sound.play();
  }
  
  var message = 'This is the captain, we have a little problem with our entry sequence, so we may experience some slight turbulence and then... explode.';
  
  $.gritter.add({
  	title: 'Notification!',
  	text: message
  });
}

var initTwitterDone = false;
function initTwitter() {
  if(initTwitterDone) {
    return;
  }
  else
  {
    initTwitterDone = true;
  }

  var pusher = getPusher();
  var channel = pusher.subscribe(TWITTER_CMD_CHANNEL_NAME);
  channel.bind('client-subscribe', handleTwitterSubscribe);
  channel.bind('client-unsubscribe', handleTwitterUnsubscribe);        
}

function handleTwitterSubscribe(data) {
  var pusher = getPusher();
  var channel = pusher.subscribe(data.channel);
  channel.bind('tweet', displayTweet);
  
  $('#twitter_subscribe_to').text(data.channel);
}

function handleTwitterUnsubscribe(data) {
  var pusher = getPusher();
  var channel = pusher.unsubscribe(data.channel);
  
  $('#twitter_subscribe_to').text('nothing');  
}

function displayTweet(tweet) {
  var li = $(
    '<li class="ui-li ui-li-static ui-body-c ui-li-has-count ui-li-has-icon">' +
    '<img src="' + tweet.profile_image_url + '" alt="France" class="ui-li-icon ui-li-thumb" />' + 
    encodeHtml(tweet.text) +
    '</li>'
  );
  var tweets = $('#tweets');
  tweets.prepend(li);
  
  var oldTweets = tweets.find('li');
  if(oldTweets.size() > 10) {
    for(var i = 9, l = oldTweets.size(); i < l; ++i) {
      $(oldTweets[i]).remove();
    }
  }
}