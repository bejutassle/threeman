var SortActionInit = {


    init: function(element) {

    var updateSortable = function (e) {
        $list = e.length ? e : $(e.target);
        $output = $list.data('output');
        $data = $list.nestable('serialize');
        $postURL = $(element).data('url');
        $postType = $(element).data('type');

        if($postType){
          var $datas = {
            list: $data,
            token: settings.token,
            type: $postType
          }
        }else{
          var $datas = {
            list: $data,
            token: settings.token
          }
        }

        $.post($postURL, $datas, function(data) {
                  toastr.success(data.description, data.message);
              }, 'JSON').done(function() {
                
              }).fail(function(xhr, status, response) {
                var error = jQuery.parseJSON(xhr.responseText);
                toastr.error(error.message.error, 'Error');
              });

    };


    $(element).nestable({
        group: 1,
        maxDepth: 5,
    }).on('change', updateSortable);


    }


};