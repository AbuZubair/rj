$(document).ready(function(){    
    
    if($('#status-page').text().trim() != 'false'){
      $('#myModal').modal('show')
    }

    $('.pasien-baru-btn').on('click',function(){
      $('#new-pasien').show()
      $('#registration').hide()
    });

    $('.back-btn').on('click',function(){
      $('#new-pasien').hide()
      $('#registration').show()
    });

    $('.dynamic-form').submit(function(e) {
        e.preventDefault();
        
        var post_url = $(this).attr("action");
        var request_method = $(this).attr("method");
        var form_data = $(this).serialize();
        var btn = $(this).find('button[type="submit"]')
        var $form = $(this);

        $.ajax({
          url : post_url,
          type: request_method,
          data : form_data,
          beforeSend: function() {
            btn.html('<div class="loader"></div>')
          },
          success: function(data) {            
            var jsonResponse = JSON.parse(data);
            if(jsonResponse.status){
              $form.find('.alert-primary').html(jsonResponse.message)
              $form.find('.alert-danger').hide() 
              $form.find('.alert-primary').show()
            }else{
              $form.find('.alert-danger').html(jsonResponse.message)
              $form.find('.alert-danger').show()              
              $form.find('.alert-primary').hide() 
            }
            btn.html('Submit')
            $form[0].reset();
          },
          error: function(xhr) { 
            var msg = xhr.responseJSON.message
            var err = xhr.responseJSON.errors
            if(err){
              var html = ''
              for (let key in err) {    
                err[key].forEach(element => {
                  html += '<p>'+capitalizeFirstLetter(element)+'</p>' 
                });                           
                $form.find('.alert-danger').html(html)                
              }
            }else{
              $form.find('.alert-danger').html(capitalizeFirstLetter(msg)) 
            }
            $form.find('.alert-danger').show() 
            btn.html('Submit')
          },
        })
    });
  
})

function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}
