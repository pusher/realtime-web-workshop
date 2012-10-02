/*!
Deck JS - deck.pusher
Copyright (c) 2012 Phil Leggetter
Dual licensed under the MIT license and GPL license.
https://github.com/imakewebthings/deck.js/blob/master/MIT-license.txt
https://github.com/imakewebthings/deck.js/blob/master/GPL-license.txt
*/

/*
This module lets you control your slides using Pusher, realtime events and WebSockets.
*/
(function($, deck, undefined) {
	var $d = $(document);
	
	/*
	Extends defaults/options.
	
	options.appKey
		Defines the Pusher application key to use. This is required to identify which Pusher
		application the events that control the slides uses. No default provided. If an `appKey`
		option is not provided an exception will be thrown.
		
	options.pusher.controlChannel
		The channel to subscribe to. Events will be triggered on this channel that
		control the slides.
		
	options.pusher.navEvent
		The event to be bound to. When an event is triggered the slides will change accordingly.
		
	options.pusher.scriptUrl
	  The location of the Pusher JavaScript library.
	  
	options.pusher.triggerOnChange
    Indicates if an attempt should be made to trigger an event (options.pusher.navEvent) on the
    options.pusher.controlChannel when the active slide changes.
    
  options.pusher.debug
    Defines if debug should be output to the browser console.
	*/
	$.extend(true, $[deck].defaults, {
	  pusher: {
	    appKey: null,
	    controlChannel: 'deckly-control',
		  navEvent: 'navigate',
		  scriptUrl: 'http://js.pusher.com/1.11/pusher.min.js',
		  triggerOnChange: false,
		  debug: false
		}
	});

	$d.bind('deck.init', function() {
	  var pusher, channel;
	  var ignoreNextChange = false; // change can be triggered following a Pusher event.
		var opts = $[deck]('getOptions'),
		slides = $[deck]('getSlides'),
		$current = $[deck]('getSlide');
		
		if(!opts.pusher.appKey) {
		  throw 'a Pusher App key (pusher.appKey) option must be specified in order to use the deck.pusher extension';
		}
		
		log('getting: ' + opts.pusher.scriptUrl);
		jQuery.getScript( opts.pusher.scriptUrl, function(data, textStatus){
		  log('loaded Pusher script');
		  Pusher.log = log;
		  
		  pusher = new Pusher(opts.pusher.appKey);
		  channel = pusher.subscribe(opts.pusher.controlChannel);
		  channel.bind(opts.pusher.navEvent, function(nav) {
		    ignoreNextChange = true;
		    $[deck]('go', nav.index);
		  });
		});
		
		if(opts.pusher.triggerOnChange) {  
		  $d.bind('deck.change', function(event, from, to) {
		    if(channel && !ignoreNextChange) {
		      channel.trigger(opts.pusher.navEvent, {index: to});
		    }
		    ignoreNextChange = false;
	    });
		}
		
		function log(msg) {
		  if(opts.pusher.debug && console && console.log) {
		    console.log(msg);
		  }
		}

	});
	
})(jQuery, 'deck');