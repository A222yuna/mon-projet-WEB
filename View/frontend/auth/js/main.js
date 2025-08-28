$(function(){
  // Select
  $('html').click(function() {
      $('.select .dropdown').hide(); 
  });
  $('.select').click(function(event){
      event.stopPropagation();
  });
  $('.select .select-control').click(function(){
      $(this).parent().next().toggle().toggleClass('active');
  })
  $('.select .dropdown li').click(function(){
      $(this).parent().toggle();
      var text = $(this).attr('rel');
      $(this).parent().prev().find('div').text(text);
  })
})
