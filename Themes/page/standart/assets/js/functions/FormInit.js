var FormInit = {


    init: function(element) {

//Form Post
$(document).on('submit', element, function(e) {

e.preventDefault();
e.stopImmediatePropagation();

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
    cache: false,
    success: function(data){

            $(formID).each(function(){

                    $(this).find('.information').removeClass('text-muted').text('');
                    $(this).find(':input').parents('div.form-group').attr('for', 'inputSuccess');
                    $(this).find(':input').parents('div.form-group').removeClass('has-error').addClass('has-success');
                    $(this).find(':input.form-control').removeClass('form-control-error').addClass('form-control-success');

            });

            if(form == undefined){
                        PjaxInit.reload(data.url);
                        toastr.success(data.description, data.message);
            }else if(form == 'default'){
                        PjaxInit.reload(data.url, true);
            }else if(form == 'login'){

                        toastr.success(data.description, data.message, {
                          "positionClass": "toast-bottom-center", 
                          "timeOut": 2000,
                          "closeButton": false,
                        });

                        setTimeout(function(){
                          window.location.href = data.url;
                        }, 2000);
            }else{
                        swal({
                          type: 'success',
                          title: data.message,
                          text: data.description,
                          showConfirmButton: false,
                          buttonsStyling: true,
                          timer: 2000,
                            onOpen: function () {
                            $('.modal').modal('hide');
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

                  $(this).find('.information').removeClass('text-muted').text('');
                  $(this).find(':input').parents('div.form-group').attr('for', 'inputSuccess');
                  $(this).find(':input').parents('div.form-group').removeClass('has-error').addClass('has-success');
                  $(this).find(':input.form-control').removeClass('form-control-error').addClass('form-control-success');


          });

          $.each(error.message, function (key, value) {
              if(value == true || value == false){
                var value = ''; 
              }

              var input = '[name^="' + key + '"]';

              $(formID).find(input).parents('div.form-group').find('.information').addClass('text-muted').text(value);
              $(formID).find(input).parents('div.form-group').attr('for', 'inputError');
              $(formID).find(input).parents('div.form-group').removeClass('has-success').addClass('has-error');
              $(formID).find(input).removeClass('form-control-success').addClass('form-control-error');                 

          });




          $header = $('header.navbar').height();
          $('html, body').animate({
              scrollTop: $(formID).find('div[for="inputError"]').first().offset().top-$header-100
          }, 500);

          $(formID).find('div[for="inputError"]').first().find(':input').first().focus();
          
        }
    }
});

return false;

});


    }


};