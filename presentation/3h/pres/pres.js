var _slideCount = 0;

$(function() {
	// Deck initialization
	$.deck('.slide', {
	  showdown: {
	    debug: true,
	    slideSelector: '.markdown',
	    showdownSrc: '../../lib/deck.js/extensions/showdown/showdown.js'
	  }
	});
	
  $.scriptToHtml();
  
  $("pre.js").each(function(i, el) {
    el = $(el);
    var text = el.text();
    text = text.replace("getPusher();", "new Pusher('" + APP_KEY + "', {encrypted: true});");
    text = text.replace("PRESENCE_CHANNEL_NAME", "'" + PRESENCE_CHANNEL_NAME + "'");
    text = text.replace("PRESENTATION_CHANNEL_NAME", "'" + PRESENTATION_CHANNEL_NAME + "'");
    text = text.replace("TRIGGER_CHANNEL_NAME", "'" + TRIGGER_CHANNEL_NAME + "'");        
    el.text(text);
  });
  
  $("pre.js").snippet("javascript", {style:"whitengrey", showNum: false});
  $("pre.csharp").snippet("csharp", {style:"whitengrey", showNum: false});  
  $("pre.php").snippet("php", {style:"whitengrey", showNum: false}); 
  $("pre.ruby").snippet("ruby", {style:"whitengrey", showNum: false});   
  
  $(document).bind('deck.change', deckChanged);
  
  _slideCount = $('section.slide').size();
  
  $('#do_notification').live('click', function() {
    sendNotification();
  });
  
  $('.demo-video')
  .attr('title', 'Click to view the video of the demo')
  .attr('target', '_blank');
});

function sendNotification() {
  var pusher = getPusher();
  var channel = pusher.channel(TRIGGER_CHANNEL_NAME);
  channel.trigger('client-fun-stuff', {'action': 'notification'});
}

function alienDropIn() {
  var pusher = getPusher();
  var channel = pusher.channel(TRIGGER_CHANNEL_NAME);
  channel.trigger('client-fun-stuff', {'action': 'alien'});
}

function deckChanged(event, from, to) {
  
  var slideTo = $.deck('getSlide', to);
  var slideToName = slideTo.attr('id');
  var slideFrom = $.deck('getSlide', from);
  var slideFromName = slideFrom.attr('id');
  
  var el = $('#interact_icon');
  if( slideTo.hasClass('interact') ) {
    el.fadeIn(500).delay(500)
      .fadeOut(500)
      .fadeIn(500).delay(500)
      .fadeOut(500)
      .fadeIn(500);
  }
  else {
    el.fadeOut(500)
  }
  
  if(document.location.search.indexOf(CONTROLLER_TOKEN) === -1) {
    return;
  }
  
  var data = {
    to: to,
    from: from,
    count: _slideCount,
    toName: slideToName,
    fromName: slideFromName
  };
  
  $.ajax({
    url: '../interact/slide_change.php',
    type: 'POST',
    data: data
  });
  
}