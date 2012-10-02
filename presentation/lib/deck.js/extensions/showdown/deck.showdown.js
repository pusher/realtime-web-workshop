/*!
Deck JS - deck.showdown
Copyright (c) 2012 Phil Leggetter
Dual licensed under the MIT license and GPL license.
https://github.com/imakewebthings/deck.js/blob/master/MIT-license.txt
https://github.com/imakewebthings/deck.js/blob/master/GPL-license.txt
*/

/*
This module lets you write your slide content in MarkDown.
*/
(function($, deck, undefined) {
	var $d = $(document);
	
	/*
	Extends defaults/options.

  options.showdown.slideSelector
    Defines the jQuery selector to use to find the slides which contain markdown to be converted to HTML.
    Defaults to 'section.slide.markdown'. If alls slides were written in markdown you could
    pass in an option value of 'article.slide'.

  options.showdown.debug
    Defines if debug should be output to the browser console.
	*/
	$.extend(true, $[deck].defaults, {
	  showdown: {
		  debug: false,
		  slideSelector: 'section.slide.markdown',
		  showdownSrc: "../lib/deck.js/extensions/showdown/showdown.js"
		}
	});

	$d.bind('deck.init', function() {
	  
	  var opts = $[deck]('getOptions');
	  
	  log('loaded deck.showdown');

    jQuery.getScript(opts.showdown.showdownSrc , checkLoaded);
  	
  	var scriptLoadCount = 0;
  	function checkLoaded() {
  	  ++scriptLoadCount;
  	  log('Loaded ' + scriptLoadCount + ' dependencie(s)');
  	  if(scriptLoadCount === 1) {
  	    allLoaded();
  	  }
  	}
  	
  	function allLoaded() {
      //var converter = new Markdown.Converter();
      var converter = new Showdown.converter();

      var markdownSlides = $(opts.showdown.slideSelector);
      log('found ' + markdownSlides.size() + ' slides to convert markdown to HTML');
      
      markdownSlides.each(function(i, el) {
        // TODO: deal with initial indentation based on first line.
        el.innerHTML = converter.makeHtml(el.innerHTML);
      });
  	}
		
		function log(msg) {
		  if(opts.showdown.debug && console && console.log) {
		    console.log(msg);
		  }
		}

	});
	
})(jQuery, 'deck');