var FormInit = {


    init: function(element) {

      //Form Post
      $('body').on('submit', element, function(e) {

      var form     = $(this).attr('form');
      var data     = new FormData($(this)[0]);
      var action   = $(this).attr('action');
      var method   = $(this).attr('method');
      var type     = $(this).attr('data-type');
      var formID = 'form#'+$(this).attr('id');

      //Disabled form submit button
      $(formID).find('input[type=submit]').prop("disabled", true);

      $.ajax({
          url: action,
          type: method,
          dataType: type,
          data: data,
          processData: false,
          contentType: false,
          async: true,
          cache: false,
          success: function(data){

          $(formID).each(function(){

                  $(this).find('.information').removeClass('help-block').text('');
                  $(this).find(':input').parents('.controls').attr('for', 'inputSuccess');
                  $(this).find(':input').parents('.controls').removeClass('has-error').addClass('has-success');

          });

            if(form == 'login'){

            toastr.success(data.description, data.message, {
              "positionClass": "toast-top-right", 
              "timeOut": 2000,
              "closeButton": false,
            });

            setTimeout(function(){
              window.location.href = data.url;
            }, 2000);
            }else if(form == 'refresh'){
            swal({
              type: 'success',
              title: data.message,
              text: data.description,
              buttonsStyling: true,
              timer: 1000,
                onOpen: function () {
                $('.modal:visible').modal('hide');
                swal.showLoading()
                setTimeout(function(){ 
                  swal.close();
                  PjaxInit.reload(window.location.href);
                }, 2000);
            }});
            }else if(form == 'modal'){
            swal({
              type: 'success',
              title: data.message,
              text: data.description,
              buttonsStyling: true,
              timer: 1000,
                onOpen: function () {
                $('.modal:visible').modal('hide');
                swal.showLoading()
                setTimeout(function(){ 
                  swal.close();
                }, 2000);
            }});
            }else{
              swal({
                type: 'success',
                title: data.message,
                text: data.description,
                buttonsStyling: true,
                timer: 2000,
                  onOpen: function () {
                  $('.modal:visible').modal('hide');
                  swal.showLoading()
                  setTimeout(function(){ 
                    swal.close();
                    PjaxInit.reload(data.url);
                  }, 2000);
              }});
            }


          },
          error: function(xhr, status, response){
          var error = jQuery.parseJSON(xhr.responseText);

          //Enabled form submit button
          $(formID).find('input[type=submit]').prop("disabled", false);

              if(error.message.token){
                swal({
                  title: error.message.token_title,
                  text: error.message.token_message,
                  type: 'question',
                  showCancelButton: false,
                  confirmButtonText: error.message.token_button
                }).then(function () {
                  PjaxInit.reload(data.url);
                });
                }else if(error.message.dialog){
                swal({
                  title: error.message.dialog_title,
                  text: error.message.dialog_message,
                  type: 'warning',
                  showCancelButton: false,
                  confirmButtonText: error.message.dialog_button
                }).then(function () {
                  PjaxInit.reload(data.url);
                });
                }else{

                $(formID).each(function(){

                        $(this).find('.information').removeClass('help-block').text('');
                        $(this).find(':input').parents('.controls').attr('for', 'inputSuccess');
                        $(this).find(':input').parents('.controls').removeClass('has-error').addClass('has-success');


                });

                $.each(error.message, function (key, value) {
                    if(value == true || value == false){
                      var value = ''; 
                    }

                    if(typeof value === 'object' && value !== null){

                    $.each(value, function(name, val) {

                    var input = '[name^="' + key + '['+name+']"]';

                    $(formID).find(input).parents('.controls').find('.information').addClass('help-block').text(val);
                    $(formID).find(input).parents('.controls').attr('for', 'inputError');
                    $(formID).find(input).parents('.controls').removeClass('has-success').addClass('has-error');      
                       
                    });

                    }else{


                    var input = '[name^="' + key + '"]';

                    $(formID).find(input).parents('.controls').find('.information').addClass('help-block').text(value);
                    $(formID).find(input).parents('.controls').attr('for', 'inputError');
                    $(formID).find(input).parents('.controls').removeClass('has-success').addClass('has-error');        

                    }          

                });



                if($(formID).find('div[for="inputError"]').length > 0){
                  $('html, body').animate({
                      scrollTop: $(formID).find('div[for="inputError"]').first().offset().top
                  }, 500);

                  $(formID).find('div[for="inputError"]').first().find(':input').first().focus();
                }
                
              }
          }
      });

      e.preventDefault();
      e.stopImmediatePropagation();
      return;

      });


    }


};