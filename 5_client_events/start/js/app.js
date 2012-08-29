( function( window, Pusher, $) {
  
  Pusher.log = function( msg ) {
    if( window.console && window.console.log ) {
      window.console.log( msg );
    }
  };
  
  Pusher.channel_auth_endpoint = 'auth.php';

  var pusher = new Pusher( CONFIG.PUSHER.APP_KEY );
  pusher.connection.bind('state_change', function( change ) {
    var el = $('.connection-status');
    el.removeClass( change.previous );
    el.addClass( change.current );
  });

  var channel = pusher.subscribe( CONFIG.PUSHER.CHANNEL_NAME );
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
        }
      });
    }

    return false;
  }
  
})( window, window['Pusher'], jQuery );