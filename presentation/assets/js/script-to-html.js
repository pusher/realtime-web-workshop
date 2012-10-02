(function( $ ) {
  $.scriptToHtml = function() {
    var codeExamples = $("*[data-code-target]");
    codeExamples.each(function(i, el) {
      el = $(el);
      var target = el.attr("data-code-target");
      el.removeAttr("data-code-target");
      if(el.get(0).tagName !== "SCRIPT") {
        var tmp = $("<div></div>");
        var clone = el.clone();
        tmp.append(clone);
        el = tmp;
      }
      var scriptHTML = el.html();
      $("*[data-code=" + target + "]").text(scriptHTML);
    });
  };
})( jQuery );