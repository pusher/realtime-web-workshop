$(function() {
  
  function ConnectionMonitor(connection) {
    this._connection = connection;
    
    this._el = this._checkEl();
    
    this._connection.bind('state_change', scopeTo(this.connectionStateChanged, this) );
  };
  
  ConnectionMonitor.prototype._checkEl = function() {
    var el = $('.pusher-connection-state');
    if(el.size() === 0) {
      el = $('<div class="pusher-connection-state"></div>');
      $(document.body).prepend(el);
    }
    return el;
  }
  
  ConnectionMonitor.prototype.connectionStateChanged = function(state) {
    this._el.removeClass(state.previous);
    this._el.addClass(state.current);
  };
  
  function scopeTo(func, object) {
    return function() {
      func.apply(object, arguments);
    };
  }
  
  if(Pusher.instances.length) {
    new ConnectionMonitor(Pusher.instances[0].connection);
  }

});