( function( window, Pusher, $ ) {
  
  Pusher.log = function( msg ) {
    if( window.console && window.console.log ) {
      window.console.log( msg );
    }
  };
  
  Pusher.channel_auth_endpoint = 'auth.php';

  // Connect
  var pusher = new Pusher( CONFIG.PUSHER.APP_KEY );
  pusher.connection.bind('state_change', function( change ) {
    var el = $('.connection-status');
    el.removeClass( change.previous );
    el.addClass( change.current );
  });

  // Subscribe to messages channel
  var channel = pusher.subscribe( CONFIG.PUSHER.CHANNEL_NAME );

  // Receiving messages
  channel.bind( 'new_message', addMessage );

  function addMessage( data ) {
    var li = $('<li class="ui-li ui-li-static ui-body-c"></li>');
    li.text( data.text );
    li.hide();
    $('#messages').append(li);
    li.slideDown();
  }
  
  // Sending messages
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
  
  // Sending client events
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
                                        username: USER.NAME,
                                        typing: typing,
                                        enteredText: enteredText
                                      } );
  }
  
  // Receiving client events
  channel.bind( 'client-typing', handleTyping );
  function handleTyping( data ) {
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

  // Init - bind to UI events
  $( function() {
    $('#send_btn').click( handleClick );
    $('#user_message').keyup( userTyping );    
  } );
  
})( window, window['Pusher'], jQuery );