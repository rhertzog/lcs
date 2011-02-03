$(document).ready(function() {

    $(function(){
	$(".switch_seq").click(function(){
          var  no=$(this).attr('id');
          if ($(this).is(".clos")) {
            $(this).removeClass("clos");
            $(this).addClass("open");
            $(this).attr('title','- de d\351tails');
            $("#d" + no ).slideToggle("slow");
            }
            else if ($(this).is(".open")) {
            $(this).removeClass("open");
            $(this).addClass("clos");
             $(this).attr('title','+ de d\351tails');
            $("#d" + no ).slideToggle("slow");
            }
      });
    });
});