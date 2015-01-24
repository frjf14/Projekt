$(document).ready(function() {

    $("#flashMessage").click(function(){
      $('#flashMessage').slideUp();
    });

    $("#answer-button").click(function(){
      $('#answer-form').slideToggle();
    });

    $("#comment-button").click(function(){
      $('#comment-form').slideToggle();
    });

});
