var FormEditableInit = {


    init: function(element) {

   	$el = $(element).find('td');

	$.each($el, function(index, el) {
	   $dirtype = $(this).data('type');

	   if($dirtype){
	    $(this).editable('click', function(e) {
	    	$value = e.value;
	    	$type = $(e.target).data('type');
	    	$url = $(e.target).parents().data('url');
	    	$hash = $(e.target).parents().data('hash');
	    	$data = $(e.target).parents().data('json');

			if($data){
			$data['hash'] = $hash;
			$data['type'] = $type;
			$data['value'] = $value;
			$data['token'] = settings.token;
			}else{
			$data = {hash: $hash, type: $type, value: $value, token: settings.token};
			}

	    	   if($value != e.old_value){

		   //Form Post
	        	   $.post($url, $data, function(data) {
	                  toastr.success(data.description, data.message);
	                  PjaxInit.reload(window.location.href);
	              }, 'JSON').done(function() {
	                
	              }).fail(function(xhr, status, response) {
	                var error = jQuery.parseJSON(xhr.responseText);
	                toastr.error(error.message.error, lang.get('txt.toastr-error-title'));
	              });

	          }

	    });
	 }
	 
	});


    }


};