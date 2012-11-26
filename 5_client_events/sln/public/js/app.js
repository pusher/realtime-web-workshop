( function( window, Pusher, $, config ) {
  
  Pusher.log = function( msg ) {
    if( window.console && window.console.log ) {
      window.console.log( msg );
    }
  };

  var pusher = new Pusher( config.pusher.appKey );
  pusher.connection.bind('state_change', function( change ) {
    var el = $('.connection-status');
    el.removeClass( change.previous );
    el.addClass( change.current );
  });

  var channel = pusher.subscribe( config.pusher.channelName );
  channel.bind( 'new_message', addMessage );

  function addMessage( data ) {
    var li = $('<li class="ui-li ui-li-static ui-body-c"></li>');
    li.text( data.text );
    li.hide();
    $('#messages').append(li);
    li.slideDown();
  }

  $( function() {
    $('#send_btn').click( handleClick );    
  } );
  
  function handleClick() {  
    var userMessageEl = $('#user_message');
    var message = $.trim( userMessageEl.val() );
    if( message ) {
      $.ajax( {
        url: 'new_message',
        type: 'post',
        data: {
          text: message
        },
        success: function() {
          userMessageEl.val('');
          sendTypingEvent( false, false );
        }
      });
    }

    return false;
  }

  $('#user_message').keydown( userTyping );
  
  var typingTimeout = null;
  function userTyping() {

    var el = $( this );

    if( !typingTimeout ) {
      var textEntered = ( $.trim( el.val() ).length > 0 );
      sendTypingEvent( true, textEntered );
    }
    else {
      window.clearTimeout( typingTimeout );
      typingTimeout = null;
    }

    typingTimeout = window.setTimeout( function() {
      var textEntered = ( $.trim( el.val() ).length > 0 );
      sendTypingEvent( false, textEntered );
      typingTimeout = null;
    }, 3000);
  }

  function sendTypingEvent( typing, enteredText ) {
    channel.trigger( 'client-typing', {
                                        username: USER.name,
                                        typing: typing,
                                        enteredText: enteredText
                                      } );
  }

  channel.bind( 'client-typing', handleTyping );
  function handleTyping( data ) {
    if( validUsername( data.username ) === false ) {
      return; // ignore
    }

    if( data.typing ) {
      $("#activity").text( data.username + ' is typing' );
    }
    else if( data.enteredText ) {
      $("#activity").text( data.username + ' has entered text' );
    }
    else {
      $("#activity").text( '' );
    }
  }

  function validUsername( username ) {
    return true; // update to actually validate
  }
  
})( window, window['Pusher'], jQuery, CONFIG );