var FormActionInit = {


    init: function(element) {

      //Pjax Form Delete
      $('body').on('click', element, function(e) {

      e.preventDefault();
      e.stopImmediatePropagation();

      var dataType = $(this).data('action');
      var dataJson = $(this).data('json');
      var dataID = $(this).data('id');
      var dataAction = $(this).attr('href');

      if(dataJson){
      dataJson['hash'] = dataID;
      dataJson['token'] = settings.token;
      }else{
      var dataJson = {hash: dataID, token: settings.token};
      }

      switch(dataType) {
          case 'passive':

              $.post(dataAction, dataJson, function(data) {
                        toastr.warning(data.description, data.message);
                        PjaxInit.reload(window.location.href);
                    }, 'JSON').done(function() {
                      
                    }).fail(function(xhr, status, response) {
                      var error = jQuery.parseJSON(xhr.responseText);
                      toastr.info(error.message.error, lang.get('txt.toastr-info-title'));
                      
                    }).always(function() {
                      
                    });

              break;

          case 'activate':

              $.post(dataAction, dataJson, function(data) {
                        toastr.success(data.description, data.message);
                        PjaxInit.reload(window.location.href);
                    }, 'JSON').done(function() {
                      
                    }).fail(function(xhr, status, response) {
                      var error = jQuery.parseJSON(xhr.responseText);
                      toastr.info(error.message.error, lang.get('txt.toastr-info-title'));
                      
                    }).always(function() {
                      
                    });

              break;

          case 'delete':

                swal({
                  title: lang.get('txt.swal-delete-title'),
                  text: lang.get('txt.swal-delete-text'),
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: lang.get('txt.swal-delete-confirm-button'),
                  cancelButtonText: lang.get('txt.swal-cancel-button'),
                }).then(function(result) {

                  if(result.value){
                      $.post(dataAction, dataJson, function(data) {
                            toastr.error(data.description, data.message);
                            PjaxInit.reload(window.location.href);
                      }, 'JSON').done(function() {
                      
                    }).fail(function(xhr, status, response) {
                      var error = jQuery.parseJSON(xhr.responseText);
                      toastr.info(error.message.error, lang.get('txt.toastr-info-title'));
                      
                    }).always(function() {
                      
                    });
                  }

                });

              break;
              
          default:
              console.log('');
      }

      });

    }


};