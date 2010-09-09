(function($) {
 var x,timeout1,timeout2;

 $.fn.notification = function(msg,isauto) {
  msg = $.trim(msg || this.text());
  if(!msg) { return; }

  var template='<div class="ui-notification"></div>';
  clearTimeout(timeout1);
  clearTimeout(timeout2);

  if(!x) {
   x = $(template).appendTo(document.body);
   if(isauto==1){
    $(x).bind("click", function(){
     $(this).animate({'opacity': 0},500,function(){$(this).hide()});
    });
   }
  }

  x.html(msg);
  x.animate({'opacity': '0'},0); // Bugfixing Internet Explorer
  x.show().animate({'opacity': '0.6'},1000);

  dummy = true; // Needed for getting a real timeout.

  timeout1 = setTimeout(function(){dummy=false}, 200 * Math.sqrt(msg.length));
  timeout2 = setTimeout(fadeOut, 5000);

  function fadeOut(){
   if(x.is(":visible") && !x.is(":animated") && !dummy) {
    x.animate({'opacity': 0},500,function(){$(this).hide()});
   }
  }
 };
})(jQuery);